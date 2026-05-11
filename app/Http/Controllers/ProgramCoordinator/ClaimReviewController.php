<?php

namespace App\Http\Controllers\ProgramCoordinator;

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
        $query = Claim::with(['profile.user', 'appointment', 'pcEndorser'])
            ->whereIn('status', ['approved', 'returned', 'rejected'])
            ->latest('reviewed_at')
            ->latest('submitted_at');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($claim) use ($search) {
                $claim->where('claim_reference', 'like', "%{$search}%")
                    ->orWhereHas('profile', fn ($profile) => $profile->where('full_name', 'like', "%{$search}%"));
            });
        }

        $claims = $query->paginate(15)->withQueryString();

        return view('program-coordinator.claims.index', compact('claims'));
    }

    public function show(Claim $claim): View
    {
        abort_if(!in_array($claim->status, ['approved', 'returned', 'rejected']), 404);

        $claim->load(['profile.user', 'appointment', 'documents', 'audits.performer', 'reviewer', 'pcEndorser']);

        return view('program-coordinator.claims.show', compact('claim'));
    }

    public function endorse(Request $request, Claim $claim): RedirectResponse
    {
        abort_if($claim->status !== 'approved', 403, 'Only approved claims can be endorsed by Program Coordinator.');
        abort_if($claim->pc_endorsed_at !== null, 403, 'This claim has already been endorsed.');

        $request->validate([
            'remarks' => ['nullable', 'string', 'max:2000'],
        ]);

        $claim->update([
            'pc_endorsed_by' => auth()->id(),
            'pc_endorsed_at' => now(),
            'pc_remarks' => $request->remarks,
        ]);

        ClaimAudit::record(
            $claim,
            'approved',
            'approved',
            'approved',
            $request->remarks ?: 'Program Coordinator endorsed the approved claim for manual Finance submission.',
            ['pc_endorsement' => true, 'finance_submission' => 'manual']
        );

        return redirect()->route('pc.claims.show', $claim)
            ->with('success', 'Claim endorsed. Print and send the endorsed claim form to Finance manually.');
    }

    public function return(Request $request, Claim $claim): RedirectResponse
    {
        abort_if($claim->status !== 'approved', 403, 'Only approved claims pending PC endorsement can be returned.');
        abort_if($claim->pc_endorsed_at !== null, 403, 'Endorsed claims cannot be returned from this screen.');

        $request->validate([
            'remarks' => ['required', 'string', 'min:10', 'max:2000'],
        ]);

        $oldStatus = $claim->status;
        $claim->update([
            'status' => 'returned',
            'pc_endorsed_by' => null,
            'pc_endorsed_at' => null,
            'pc_remarks' => $request->remarks,
        ]);

        ClaimAudit::record(
            $claim,
            'returned_for_revision',
            $oldStatus,
            'returned',
            'Program Coordinator returned claim: ' . $request->remarks,
            ['pc_review' => true]
        );

        return redirect()->route('pc.claims.show', $claim)
            ->with('success', "Claim {$claim->claim_reference} has been returned to AF/AD for revision.");
    }
}
