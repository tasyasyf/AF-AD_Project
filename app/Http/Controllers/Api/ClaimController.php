<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Claim;
use App\Models\ClaimAudit;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ClaimController extends Controller
{
    /**
     * GET /api/claims
     * List own claims (AF/AD). Supports ?status= filter.
     */
    public function index(Request $request): JsonResponse
    {
        $profile = $request->user()->profile;

        if (!$profile) {
            return response()->json(['message' => 'Profile not found.'], 404);
        }

        $query = $profile->claims()->with('appointment')->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $claims = $query->get()->map(fn($c) => $this->formatClaim($c));

        return response()->json(['data' => $claims]);
    }

    /**
     * GET /api/claims/{id}
     * Show single claim with documents and audit trail.
     */
    public function show(Request $request, int $id): JsonResponse
    {
        $profile = $request->user()->profile;
        $claim   = $profile?->claims()->with(['appointment', 'documents', 'audits.performer'])->find($id);

        if (!$claim) {
            return response()->json(['message' => 'Claim not found.'], 404);
        }

        return response()->json([
            ...$this->formatClaim($claim),
            'documents' => $claim->documents->map(fn($d) => [
                'id'                 => $d->id,
                'label'              => $d->label,
                'document_type'      => $d->document_type,
                'is_required'        => $d->is_required,
                'is_uploaded'        => $d->is_uploaded,
                'file_original_name' => $d->file_original_name,
                'uploaded_at'        => $d->uploaded_at?->toDateTimeString(),
            ]),
            'audit_trail' => $claim->audits->map(fn($a) => [
                'action'      => $a->action,
                'performed_by'=> $a->performer?->name,
                'from_status' => $a->from_status,
                'to_status'   => $a->to_status,
                'remarks'     => $a->remarks,
                'timestamp'   => $a->created_at->toDateTimeString(),
            ]),
        ]);
    }

    /**
     * POST /api/claims
     * Create a new draft claim (AF/AD).
     */
    public function store(Request $request): JsonResponse
    {
        $profile = $request->user()->profile;

        if (!$profile || $profile->status !== 'verified') {
            return response()->json(['message' => 'Your profile must be verified to submit claims.'], 403);
        }

        $data = $request->validate([
            'appointment_id' => ['required', 'exists:appointments,id'],
            'claim_type'     => ['required', 'in:teaching,marking,module_development,consultation'],
            'period_from'    => ['required', 'date'],
            'period_to'      => ['required', 'date', 'after_or_equal:period_from'],
            'total_hours'    => ['required', 'numeric', 'min:0.5'],
            'rate_per_hour'  => ['required', 'numeric', 'min:0'],
        ]);

        // Ensure appointment belongs to this profile
        $appointment = $profile->appointments()->find($data['appointment_id']);
        if (!$appointment) {
            return response()->json(['message' => 'Appointment not found or not assigned to you.'], 404);
        }

        $data['profile_id']   = $profile->id;
        $data['total_amount'] = round($data['total_hours'] * $data['rate_per_hour'], 2);

        $claim = Claim::create($data);
        ClaimAudit::record($claim, 'created', null, 'draft');

        return response()->json([
            'message' => 'Claim created as draft.',
            'claim'   => $this->formatClaim($claim),
        ], 201);
    }

    /**
     * POST /api/claims/{id}/submit
     * Submit a draft or returned claim.
     */
    public function submit(Request $request, int $id): JsonResponse
    {
        $profile = $request->user()->profile;
        $claim   = $profile?->claims()->find($id);

        if (!$claim) {
            return response()->json(['message' => 'Claim not found.'], 404);
        }

        if (!in_array($claim->status, ['draft', 'returned'])) {
            return response()->json(['message' => "Claim cannot be submitted in '{$claim->status}' status."], 422);
        }

        $missingDocs = $claim->documents()->where('is_required', true)->where('is_uploaded', false)->count();
        if ($missingDocs > 0) {
            return response()->json([
                'message' => "Please upload all required documents before submitting. Missing: {$missingDocs}.",
            ], 422);
        }

        $claim->update(['status' => 'submitted', 'submitted_at' => now()]);
        ClaimAudit::record($claim, 'submitted', 'draft', 'submitted');

        return response()->json([
            'message' => 'Claim submitted for review.',
            'claim'   => $this->formatClaim($claim->fresh()),
        ]);
    }

    private function formatClaim(Claim $claim): array
    {
        return [
            'id'              => $claim->id,
            'claim_reference' => $claim->claim_reference,
            'claim_type'      => $claim->claim_type,
            'appointment'     => $claim->appointment ? [
                'id'          => $claim->appointment->id,
                'course_code' => $claim->appointment->course_code,
                'course_name' => $claim->appointment->course_name,
                'role_type'   => $claim->appointment->role_type,
            ] : null,
            'period_from'     => $claim->period_from->toDateString(),
            'period_to'       => $claim->period_to->toDateString(),
            'total_hours'     => $claim->total_hours,
            'rate_per_hour'   => $claim->rate_per_hour,
            'total_amount'    => $claim->total_amount,
            'status'          => $claim->status,
            'submitted_at'    => $claim->submitted_at?->toDateTimeString(),
            'executive_remarks' => $claim->executive_remarks,
            'created_at'      => $claim->created_at->toDateTimeString(),
        ];
    }
}
