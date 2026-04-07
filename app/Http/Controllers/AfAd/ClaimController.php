<?php

namespace App\Http\Controllers\AfAd;

use App\Http\Controllers\Controller;
use App\Models\Claim;
use App\Models\ClaimAudit;
use App\Models\ClaimDocument;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ClaimController extends Controller
{
    public function index(): View|RedirectResponse
    {
        $profile = auth()->user()->profile;
        if (!$profile) {
            return redirect()->route('afad.profile.create');
        }
        $claims = $profile->claims()->with('appointment')->latest()->paginate(10);
        return view('afad.claims.index', compact('claims'));
    }

    public function create(): View|RedirectResponse
    {
        $profile = auth()->user()->profile;
        if (!$profile || $profile->status !== 'verified') {
            return redirect()->route('afad.dashboard')
                ->with('error', 'Your profile must be verified before submitting claims.');
        }
        $appointments = $profile->appointments()->active()->get();
        return view('afad.claims.create', compact('appointments'));
    }

    public function store(Request $request): RedirectResponse
    {
        $profile = auth()->user()->profile;
        abort_if(!$profile || $profile->status !== 'verified', 403);

        $data = $request->validate([
            'appointment_id' => ['required', 'exists:appointments,id'],
            'claim_type'     => ['required', 'in:teaching,marking,module_development,consultation'],
            'period_from'    => ['required', 'date'],
            'period_to'      => ['required', 'date', 'after_or_equal:period_from'],
            'total_hours'    => ['required', 'numeric', 'min:0.5'],
            'rate_per_hour'  => ['required', 'numeric', 'min:0'],
        ]);

        $appointment = $profile->appointments()->findOrFail($data['appointment_id']);

        $data['profile_id']   = $profile->id;
        $data['total_amount'] = round($data['total_hours'] * $data['rate_per_hour'], 2);

        $claim = Claim::create($data);

        // Create default document checklist based on claim type
        $this->createDocumentChecklist($claim);

        ClaimAudit::record($claim, 'created', null, 'draft');

        return redirect()->route('afad.claims.show', $claim)
            ->with('success', 'Claim created as draft. Please upload required documents before submitting.');
    }

    public function show(Claim $claim): View
    {
        abort_if($claim->profile->user_id !== auth()->id(), 403);
        $claim->load(['appointment', 'documents', 'audits.performer']);
        return view('afad.claims.show', compact('claim'));
    }

    public function edit(Claim $claim): View|RedirectResponse
    {
        abort_if($claim->profile->user_id !== auth()->id(), 403);
        abort_if(!in_array($claim->status, ['draft', 'returned']), 403, 'This claim cannot be edited.');

        $profile = auth()->user()->profile;
        $appointments = $profile->appointments()->active()->get();
        return view('afad.claims.edit', compact('claim', 'appointments'));
    }

    public function update(Request $request, Claim $claim): RedirectResponse
    {
        abort_if($claim->profile->user_id !== auth()->id(), 403);
        abort_if(!in_array($claim->status, ['draft', 'returned']), 403);

        $data = $request->validate([
            'claim_type'    => ['required', 'in:teaching,marking,module_development,consultation'],
            'period_from'   => ['required', 'date'],
            'period_to'     => ['required', 'date', 'after_or_equal:period_from'],
            'total_hours'   => ['required', 'numeric', 'min:0.5'],
            'rate_per_hour' => ['required', 'numeric', 'min:0'],
        ]);

        $data['total_amount'] = round($data['total_hours'] * $data['rate_per_hour'], 2);

        $claim->update($data);
        ClaimAudit::record($claim, 'edited', $claim->status, $claim->status);

        return redirect()->route('afad.claims.show', $claim)
            ->with('success', 'Claim updated successfully.');
    }

    public function submit(Claim $claim): RedirectResponse
    {
        abort_if($claim->profile->user_id !== auth()->id(), 403);
        abort_if(!in_array($claim->status, ['draft', 'returned']), 403);

        $requiredDocs = $claim->documents()->where('is_required', true)->where('is_uploaded', false)->count();
        if ($requiredDocs > 0) {
            return back()->with('error', "Please upload all {$requiredDocs} required document(s) before submitting.");
        }

        $claim->update([
            'status'       => 'submitted',
            'submitted_at' => now(),
        ]);

        ClaimAudit::record($claim, 'submitted', 'draft', 'submitted');

        return redirect()->route('afad.claims.show', $claim)
            ->with('success', 'Claim submitted for review.');
    }

    public function destroy(Claim $claim): RedirectResponse
    {
        abort_if($claim->profile->user_id !== auth()->id(), 403);
        abort_if($claim->status !== 'draft', 403, 'Only draft claims can be deleted.');

        $claim->delete();
        return redirect()->route('afad.claims.index')->with('success', 'Draft claim deleted.');
    }

    private function createDocumentChecklist(Claim $claim): void
    {
        $documents = match ($claim->claim_type) {
            'teaching' => [
                ['document_type' => 'attendance_sheet', 'label' => 'Attendance Sheet', 'is_required' => true,  'sort_order' => 1],
                ['document_type' => 'lesson_plan',      'label' => 'Lesson Plan',      'is_required' => true,  'sort_order' => 2],
                ['document_type' => 'student_list',     'label' => 'Student List',      'is_required' => false, 'sort_order' => 3],
            ],
            'marking' => [
                ['document_type' => 'marking_scheme',     'label' => 'Marking Scheme',     'is_required' => true,  'sort_order' => 1],
                ['document_type' => 'assignment_sample',  'label' => 'Assignment Sample',  'is_required' => true,  'sort_order' => 2],
                ['document_type' => 'attendance_sheet',   'label' => 'Attendance Sheet',   'is_required' => false, 'sort_order' => 3],
            ],
            'module_development' => [
                ['document_type' => 'lesson_plan',        'label' => 'Module Draft / Outline', 'is_required' => true,  'sort_order' => 1],
                ['document_type' => 'other',              'label' => 'Approval Letter',         'is_required' => true,  'sort_order' => 2],
            ],
            'consultation' => [
                ['document_type' => 'attendance_sheet',   'label' => 'Consultation Record', 'is_required' => true,  'sort_order' => 1],
                ['document_type' => 'other',              'label' => 'Supporting Document',  'is_required' => false, 'sort_order' => 2],
            ],
            default => [],
        };

        foreach ($documents as $doc) {
            ClaimDocument::create(array_merge($doc, ['claim_id' => $claim->id]));
        }
    }
}
