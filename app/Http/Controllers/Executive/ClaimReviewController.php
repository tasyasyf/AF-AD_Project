<?php

namespace App\Http\Controllers\Executive;

use App\Http\Controllers\Controller;
use App\Models\Claim;
use App\Models\ClaimAudit;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ClaimReviewController extends Controller
{
    public function index(Request $request): View
    {
        $query = Claim::with(['profile.user', 'appointment'])->latest('submitted_at');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('claim_reference', 'like', "%{$request->search}%")
                  ->orWhereHas('profile', fn($pq) => $pq->where('full_name', 'like', "%{$request->search}%"));
            });
        }

        $claims = $query->paginate(15)->withQueryString();
        return view('executive.claims.index', compact('claims'));
    }

    public function show(Claim $claim): View
    {
        $claim->load(['profile.user', 'appointment', 'documents', 'audits.performer', 'reviewer']);
        $uploadedSubmissions = $claim->submissions()
            ->latest()
            ->get([
                'id',
                'submission_type',
                'title',
                'course',
                'course_name',
                'tutorial_number',
                'submission_date',
                'claim_hours',
                'rate_per_hour',
                'total_amount',
                'status',
                'created_at',
            ]);

        return view('executive.claims.show', compact('claim', 'uploadedSubmissions'));
    }

    public function approve(Request $request, Claim $claim): RedirectResponse
    {
        abort_if(!in_array($claim->status, ['submitted', 'under_review']), 403, 'Claim cannot be approved in its current state.');

        $request->validate([
            'remarks' => ['nullable', 'string'],
        ]);

        $oldStatus = $claim->status;
        $claim->update([
            'status'            => 'approved',
            'reviewed_by'       => auth()->id(),
            'reviewed_at'       => now(),
            'executive_remarks' => $request->remarks,
        ]);

        ClaimAudit::record($claim, 'approved', $oldStatus, 'approved', $request->remarks);

        return redirect()->route('executive.claims.show', $claim)
            ->with('success', "Claim {$claim->claim_reference} has been approved and is ready for Program Coordinator endorsement.");
    }

    public function return(Request $request, Claim $claim): RedirectResponse
    {
        abort_if(!in_array($claim->status, ['submitted', 'under_review']), 403);

        $request->validate([
            'remarks' => ['required', 'string', 'min:10'],
        ]);

        $oldStatus = $claim->status;
        $claim->update([
            'status'            => 'returned',
            'reviewed_by'       => auth()->id(),
            'reviewed_at'       => now(),
            'executive_remarks' => $request->remarks,
        ]);

        ClaimAudit::record($claim, 'returned_for_revision', $oldStatus, 'returned', $request->remarks);

        return redirect()->route('executive.claims.show', $claim)
            ->with('success', "Claim {$claim->claim_reference} has been returned for revision.");
    }

    public function reject(Request $request, Claim $claim): RedirectResponse
    {
        abort_if(!in_array($claim->status, ['submitted', 'under_review']), 403);

        $request->validate([
            'remarks' => ['required', 'string', 'min:10'],
        ]);

        $oldStatus = $claim->status;
        $claim->update([
            'status'            => 'rejected',
            'reviewed_by'       => auth()->id(),
            'reviewed_at'       => now(),
            'executive_remarks' => $request->remarks,
        ]);

        ClaimAudit::record($claim, 'rejected', $oldStatus, 'rejected', $request->remarks);

        return redirect()->route('executive.claims.show', $claim)
            ->with('success', "Claim {$claim->claim_reference} has been rejected.");
    }
}
