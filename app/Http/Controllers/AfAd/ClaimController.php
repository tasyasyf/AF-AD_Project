<?php

namespace App\Http\Controllers\AfAd;

use App\Http\Controllers\Controller;
use App\Models\Claim;
use App\Models\ClaimAudit;
use App\Models\ClaimDocument;
use App\Models\Submission;
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
        $uploadedSubmissions = $this->uploadedSubmissions($profile);
        return view('afad.claims.index', compact('claims', 'uploadedSubmissions'));
    }

    public function create(): View|RedirectResponse
    {
        $profile = auth()->user()->profile;
        if (!$profile || $profile->status !== 'verified') {
            return redirect()->route('afad.dashboard')
                ->with('error', 'Your profile must be verified before submitting claims.');
        }
        $appointments = $profile->appointments()->active()->get();
        $submissionChecklist = $this->submissionChecklistDefaults($profile);
        $submissionTotals = $this->submissionAmountDefaults($profile);
        $uploadedSubmissions = $this->uploadedSubmissions($profile);
        $videoRecordingRows = $this->videoRecordingClaimRows($profile);
        $hasRecordingSubmission = $profile->submissions()
            ->where('submission_type', Submission::TYPE_VIDEO_RECORDING)
            ->exists();
        return view('afad.claims.create', compact('profile', 'appointments', 'submissionChecklist', 'submissionTotals', 'uploadedSubmissions', 'videoRecordingRows', 'hasRecordingSubmission'));
    }

    public function store(Request $request): RedirectResponse
    {
        $profile = auth()->user()->profile;
        abort_if(!$profile || $profile->status !== 'verified', 403);

        $data = $request->validate([
            'appointment_id' => ['required', 'exists:appointments,id'],
            'claim_items' => ['required', 'array', 'min:1'],
            'claim_items.*.claim_type' => ['required', 'in:teaching,marking,module_development,consultation'],
            'claim_items.*.total_hours' => ['nullable', 'numeric', 'min:0.5'],
            'claim_items.*.rate' => ['required', 'numeric', 'min:0'],
            'partner_name' => ['nullable', 'string', 'max:150'],
            'school' => ['nullable', 'string', 'max:150'],
            'learning_centre' => ['nullable', 'string', 'max:150'],
            'programme' => ['nullable', 'string', 'max:50'],
            'semester_text' => ['nullable', 'string', 'max:100'],
            'semester_intake' => ['nullable', 'array'],
            'semester_intake.*' => ['string', 'in:jan,may,sept'],
            'action' => ['nullable', 'in:draft,submit'],
            'has_mark_entry_forms' => ['nullable', 'boolean'],
            'has_graded_scripts' => ['nullable', 'boolean'],
            'has_qa' => ['nullable', 'boolean'],
        ]);

        $appointment = $profile->appointments()->findOrFail($data['appointment_id']);
        if ($error = $this->teachingHoursError($data['claim_items'])) {
            return back()->withErrors($error)->withInput();
        }
        $claimItems = $this->normalizeClaimItems($data['claim_items']);
        $submissionTotals = $this->submissionAmountDefaults($profile);
        $firstItem = $claimItems[0];

        $data['profile_id']   = $profile->id;
        $data['period_from']  = $appointment->start_date;
        $data['period_to']    = $appointment->end_date;
        $data['claim_type'] = $firstItem['claim_type'];
        $data['claim_items'] = $claimItems;
        $data['claim_form_data'] = $this->claimFormData($data);
        $data['total_hours'] = round(collect($claimItems)->sum('total_hours'), 2);
        $data['rate_per_hour'] = $firstItem['rate'];
        $data['total_amount'] = round(collect($claimItems)->sum('amount') + ($submissionTotals['amount'] ?? 0), 2);
        $data = array_merge($data, $this->submissionChecklistData($profile));

        $claim = Claim::create($data);

        // Create default document checklist based on claim type
        $this->createDocumentChecklist($claim);

        ClaimAudit::record($claim, 'created', null, 'draft');

        if (($data['action'] ?? 'draft') === 'submit') {
            $claim->update([
                'status' => 'submitted',
                'submitted_at' => now(),
            ]);

            ClaimAudit::record($claim, 'submitted', 'draft', 'submitted');

            return redirect()->route('afad.claims.index')
                ->with('success', 'Claim submitted successfully.');
        }

        return redirect()->route('afad.claims.show', $claim)
            ->with('success', 'Claim created as draft. Please upload required documents before submitting.');
    }

    public function show(Claim $claim): View
    {
        abort_if($claim->profile->user_id !== auth()->id(), 403);
        $claim->load(['appointment', 'documents', 'audits.performer', 'pcEndorser']);
        $uploadedSubmissions = $this->uploadedSubmissions($claim->profile);
        $videoRecordingRows = $this->videoRecordingClaimRows($claim->profile);
        $hasRecordingSubmission = $claim->profile->submissions()
            ->where('submission_type', Submission::TYPE_VIDEO_RECORDING)
            ->exists();
        return view('afad.claims.show', compact('claim', 'uploadedSubmissions', 'videoRecordingRows', 'hasRecordingSubmission'));
    }

    public function edit(Claim $claim): View|RedirectResponse
    {
        abort_if($claim->profile->user_id !== auth()->id(), 403);
        abort_if(!in_array($claim->status, ['draft', 'returned']), 403, 'This claim cannot be edited.');

        $profile = auth()->user()->profile;
        $appointments = $profile->appointments()->active()->get();
        $submissionChecklist = $this->submissionChecklistDefaults($profile);
        $submissionTotals = $this->submissionAmountDefaults($profile);
        $uploadedSubmissions = $this->uploadedSubmissions($profile);
        $videoRecordingRows = $this->videoRecordingClaimRows($profile);
        $hasRecordingSubmission = $profile->submissions()
            ->where('submission_type', Submission::TYPE_VIDEO_RECORDING)
            ->exists();
        return view('afad.claims.edit', compact('claim', 'appointments', 'submissionChecklist', 'submissionTotals', 'uploadedSubmissions', 'videoRecordingRows', 'hasRecordingSubmission'));
    }

    public function update(Request $request, Claim $claim): RedirectResponse
    {
        abort_if($claim->profile->user_id !== auth()->id(), 403);
        abort_if(!in_array($claim->status, ['draft', 'returned']), 403);

        $data = $request->validate([
            'claim_items' => ['required', 'array', 'min:1'],
            'claim_items.*.claim_type' => ['required', 'in:teaching,marking,module_development,consultation'],
            'claim_items.*.total_hours' => ['nullable', 'numeric', 'min:0.5'],
            'claim_items.*.rate' => ['required', 'numeric', 'min:0'],
            'partner_name' => ['nullable', 'string', 'max:150'],
            'school' => ['nullable', 'string', 'max:150'],
            'learning_centre' => ['nullable', 'string', 'max:150'],
            'programme' => ['nullable', 'string', 'max:50'],
            'semester_text' => ['nullable', 'string', 'max:100'],
            'semester_intake' => ['nullable', 'array'],
            'semester_intake.*' => ['string', 'in:jan,may,sept'],
            'has_mark_entry_forms' => ['nullable', 'boolean'],
            'has_graded_scripts' => ['nullable', 'boolean'],
            'has_qa' => ['nullable', 'boolean'],
        ]);

        if ($error = $this->teachingHoursError($data['claim_items'])) {
            return back()->withErrors($error)->withInput();
        }
        $claimItems = $this->normalizeClaimItems($data['claim_items']);
        $submissionTotals = $this->submissionAmountDefaults($claim->profile);
        $firstItem = $claimItems[0];

        $data['period_from'] = $claim->appointment->start_date;
        $data['period_to'] = $claim->appointment->end_date;
        $data['claim_type'] = $firstItem['claim_type'];
        $data['claim_items'] = $claimItems;
        $data['claim_form_data'] = $this->claimFormData($data);
        $data['total_hours'] = round(collect($claimItems)->sum('total_hours'), 2);
        $data['rate_per_hour'] = $firstItem['rate'];
        $data['total_amount'] = round(collect($claimItems)->sum('amount') + ($submissionTotals['amount'] ?? 0), 2);
        $data = array_merge($data, $this->submissionChecklistData($claim->profile));

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
        $documents = [];
        foreach (collect($claim->displayClaimItems())->pluck('claim_type')->unique()->values() as $claimType) {
            $documents = array_merge($documents, $this->documentsForClaimType($claimType));
        }

        foreach (collect($documents)->unique('document_type')->values() as $index => $doc) {
            $doc['sort_order'] = $index + 1;
            ClaimDocument::create(array_merge($doc, ['claim_id' => $claim->id]));
        }
    }

    private function documentsForClaimType(string $claimType): array
    {
        return match ($claimType) {
            'teaching' => [
                ['document_type' => 'attendance_sheet', 'label' => 'Attendance Sheet', 'is_required' => true],
                ['document_type' => 'lesson_plan', 'label' => 'Lesson Plan', 'is_required' => true],
                ['document_type' => 'student_list', 'label' => 'Student List', 'is_required' => false],
            ],
            'marking' => [
                ['document_type' => 'marking_scheme', 'label' => 'Marking Scheme', 'is_required' => true],
                ['document_type' => 'assignment_sample', 'label' => 'Assignment Sample', 'is_required' => true],
                ['document_type' => 'attendance_sheet', 'label' => 'Attendance Sheet', 'is_required' => false],
            ],
            'module_development' => [
                ['document_type' => 'lesson_plan', 'label' => 'Module Draft / Outline', 'is_required' => true],
                ['document_type' => 'other', 'label' => 'Approval Letter', 'is_required' => true],
            ],
            'consultation' => [
                ['document_type' => 'attendance_sheet', 'label' => 'Consultation Record', 'is_required' => true],
                ['document_type' => 'other', 'label' => 'Supporting Document', 'is_required' => false],
            ],
            default => [],
        };
    }

    private function normalizeClaimItems(array $items): array
    {
        return collect($items)
            ->map(function (array $item) {
                $type = $item['claim_type'];
                $hours = $type === 'teaching' ? round((float) ($item['total_hours'] ?? 0), 2) : null;
                $rate = round((float) $item['rate'], 2);

                return [
                    'claim_type' => $type,
                    'total_hours' => $hours,
                    'rate' => $rate,
                    'amount' => $type === 'teaching' ? round($hours * $rate, 2) : $rate,
                ];
            })
            ->values()
            ->all();
    }

    private function claimFormData(array $data): array
    {
        return [
            'partner_name' => $data['partner_name'] ?? null,
            'school' => $data['school'] ?? null,
            'learning_centre' => $data['learning_centre'] ?? null,
            'programme' => $data['programme'] ?? null,
            'semester_text' => $data['semester_text'] ?? null,
            'semester_intake' => $data['semester_intake'] ?? [],
        ];
    }

    private function teachingHoursError(array $items): ?array
    {
        foreach ($items as $index => $item) {
            if (($item['claim_type'] ?? null) === 'teaching' && empty($item['total_hours'])) {
                return ["claim_items.{$index}.total_hours" => 'Total hours is required for teaching claims.'];
            }
        }

        return null;
    }

    private function submissionChecklistData($profile): array
    {
        return $this->submissionChecklistDefaults($profile);
    }

    private function submissionChecklistDefaults($profile): array
    {
        $submittedTypes = $profile->submissions()
            ->whereIn('submission_type', array_keys(Submission::CLAIM_CHECKLIST_MAP))
            ->pluck('submission_type')
            ->all();

        $defaults = array_fill_keys(array_values(Submission::CLAIM_CHECKLIST_MAP), false);
        foreach ($submittedTypes as $type) {
            $defaults[Submission::CLAIM_CHECKLIST_MAP[$type]] = true;
        }

        return $defaults;
    }

    private function submissionAmountDefaults($profile): array
    {
        $submissions = $profile->submissions()
            ->whereNotNull('total_amount')
            ->get(['claim_hours', 'rate_per_hour', 'total_amount']);

        $hours = round((float) $submissions->sum('claim_hours'), 2);
        $amount = round((float) $submissions->sum('total_amount'), 2);
        $rate = $hours > 0 ? round($amount / $hours, 2) : 0;

        return compact('hours', 'rate', 'amount');
    }

    private function uploadedSubmissions($profile)
    {
        return $profile->submissions()
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
    }

    private function videoRecordingClaimRows($profile): array
    {
        return $profile->submissions()
            ->where('submission_type', Submission::TYPE_VIDEO_RECORDING)
            ->whereNotNull('rate_per_hour')
            ->orderBy('course')
            ->orderBy('tutorial_number')
            ->orderBy('submission_date')
            ->get()
            ->groupBy(fn(Submission $submission) => implode('|', [
                $submission->course ?? '',
                $submission->course_name ?? '',
                $submission->rate_per_hour ?? '',
            ]))
            ->map(function ($submissions) {
                $first = $submissions->first();
                $tutorials = [];

                foreach ($submissions->take(4) as $submission) {
                    $tutorials[] = [
                        'date' => ($submission->submission_date ?? $submission->created_at)->format('d/m/Y'),
                        'hours' => $submission->claim_hours ? number_format((float) $submission->claim_hours, 2) : '',
                    ];
                }

                $totalHours = round((float) $submissions->sum('claim_hours'), 2);
                $amount = round((float) $submissions->sum('total_amount'), 2);

                return [
                    'course' => $first->course,
                    'course_name' => $first->course_name,
                    'tutorials' => $tutorials,
                    'total_hours' => $totalHours,
                    'rate' => (float) $first->rate_per_hour,
                    'amount' => $amount,
                ];
            })
            ->values()
            ->all();
    }
}
