<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Claim;
use App\Models\ClaimAudit;
use App\Models\Profile;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ExecutiveController extends Controller
{
    /**
     * GET /api/executive/profiles
     * List all profiles. Supports ?status= filter.
     */
    public function profiles(Request $request): JsonResponse
    {
        $query = Profile::with('user')->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $profiles = $query->get()->map(fn($p) => [
            'id'                  => $p->id,
            'full_name'           => $p->full_name,
            'ic_number'           => $p->ic_number,
            'contact_email'       => $p->contact_email,
            'qualification'       => $p->qualification,
            'qualification_level' => $p->qualification_level,
            'status'              => $p->status,
            'user_email'          => $p->user->email,
            'registered_at'       => $p->created_at->toDateTimeString(),
        ]);

        return response()->json(['data' => $profiles]);
    }

    /**
     * POST /api/executive/profiles/{id}/verify
     * Verify an AF/AD profile.
     */
    public function verifyProfile(Request $request, int $id): JsonResponse
    {
        $profile = Profile::find($id);

        if (!$profile) {
            return response()->json(['message' => 'Profile not found.'], 404);
        }

        if ($profile->status === 'verified') {
            return response()->json(['message' => 'Profile is already verified.'], 409);
        }

        $profile->update([
            'status'      => 'verified',
            'verified_by' => $request->user()->id,
            'verified_at' => now(),
            'rejection_reason' => null,
        ]);

        $profile->certificates()->update([
            'is_verified' => true,
            'verified_by' => $request->user()->id,
            'verified_at' => now(),
        ]);

        return response()->json(['message' => "Profile for {$profile->full_name} verified successfully."]);
    }

    /**
     * POST /api/executive/profiles/{id}/reject
     * Reject an AF/AD profile.
     */
    public function rejectProfile(Request $request, int $id): JsonResponse
    {
        $profile = Profile::find($id);

        if (!$profile) {
            return response()->json(['message' => 'Profile not found.'], 404);
        }

        $request->validate([
            'rejection_reason' => ['required', 'string', 'min:10'],
        ]);

        $profile->update([
            'status'           => 'rejected',
            'rejection_reason' => $request->rejection_reason,
            'verified_by'      => $request->user()->id,
            'verified_at'      => now(),
        ]);

        return response()->json(['message' => "Profile for {$profile->full_name} rejected."]);
    }

    /**
     * GET /api/executive/claims
     * List claims for review. Supports ?status= filter.
     */
    public function claims(Request $request): JsonResponse
    {
        $query = Claim::with(['profile', 'appointment'])->latest('submitted_at');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $claims = $query->get()->map(fn($c) => [
            'id'              => $c->id,
            'claim_reference' => $c->claim_reference,
            'afad_name'       => $c->profile->full_name,
            'course_code'     => $c->appointment->course_code,
            'claim_type'      => $c->claim_type,
            'total_amount'    => $c->total_amount,
            'status'          => $c->status,
            'submitted_at'    => $c->submitted_at?->toDateTimeString(),
        ]);

        return response()->json(['data' => $claims]);
    }

    /**
     * POST /api/executive/claims/{id}/approve
     * Approve a claim.
     */
    public function approveClaim(Request $request, int $id): JsonResponse
    {
        $claim = Claim::find($id);

        if (!$claim) {
            return response()->json(['message' => 'Claim not found.'], 404);
        }

        if (!in_array($claim->status, ['submitted', 'under_review'])) {
            return response()->json(['message' => "Cannot approve claim in '{$claim->status}' status."], 422);
        }

        $request->validate(['remarks' => ['nullable', 'string']]);

        $oldStatus = $claim->status;
        $claim->update([
            'status'            => 'approved',
            'reviewed_by'       => $request->user()->id,
            'reviewed_at'       => now(),
            'executive_remarks' => $request->remarks,
        ]);

        ClaimAudit::record($claim, 'approved', $oldStatus, 'approved', $request->remarks);

        return response()->json(['message' => "Claim {$claim->claim_reference} approved."]);
    }

    /**
     * POST /api/executive/claims/{id}/return
     * Return a claim for revision.
     */
    public function returnClaim(Request $request, int $id): JsonResponse
    {
        $claim = Claim::find($id);

        if (!$claim) {
            return response()->json(['message' => 'Claim not found.'], 404);
        }

        if (!in_array($claim->status, ['submitted', 'under_review'])) {
            return response()->json(['message' => "Cannot return claim in '{$claim->status}' status."], 422);
        }

        $request->validate(['remarks' => ['required', 'string', 'min:10']]);

        $oldStatus = $claim->status;
        $claim->update([
            'status'            => 'returned',
            'reviewed_by'       => $request->user()->id,
            'reviewed_at'       => now(),
            'executive_remarks' => $request->remarks,
        ]);

        ClaimAudit::record($claim, 'returned_for_revision', $oldStatus, 'returned', $request->remarks);

        return response()->json(['message' => "Claim {$claim->claim_reference} returned for revision."]);
    }

    /**
     * POST /api/executive/claims/{id}/reject
     * Reject a claim.
     */
    public function rejectClaim(Request $request, int $id): JsonResponse
    {
        $claim = Claim::find($id);

        if (!$claim) {
            return response()->json(['message' => 'Claim not found.'], 404);
        }

        if (!in_array($claim->status, ['submitted', 'under_review'])) {
            return response()->json(['message' => "Cannot reject claim in '{$claim->status}' status."], 422);
        }

        $request->validate(['remarks' => ['required', 'string', 'min:10']]);

        $oldStatus = $claim->status;
        $claim->update([
            'status'            => 'rejected',
            'reviewed_by'       => $request->user()->id,
            'reviewed_at'       => now(),
            'executive_remarks' => $request->remarks,
        ]);

        ClaimAudit::record($claim, 'rejected', $oldStatus, 'rejected', $request->remarks);

        return response()->json(['message' => "Claim {$claim->claim_reference} rejected."]);
    }
}
