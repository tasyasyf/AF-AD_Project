<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Profile;
use App\Models\Submission;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SubmissionController extends Controller
{
    public function index(Request $request): View
    {
        $query = Submission::with('profile.user')->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', "%{$request->search}%")
                    ->orWhereHas('profile', fn($pq) => $pq->where('full_name', 'like', "%{$request->search}%"));
            });
        }

        $submissions = $query->paginate(15)->withQueryString();
        return view('admin.submissions.index', compact('submissions'));
    }

    public function create(): View
    {
        $profiles = Profile::with('user')->orderBy('full_name')->get();
        $submissionTypes = Submission::TYPES;
        return view('admin.submissions.create', compact('profiles', 'submissionTypes'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validatedData($request);
        $isVideo = $data['submission_type'] === Submission::TYPE_VIDEO_RECORDING;
        $isQuestionBank = $data['submission_type'] === Submission::TYPE_QUESTION_BANK_ANSWER_SHEET;
        $isMarkEntry = $data['submission_type'] === Submission::TYPE_MARK_ENTRY_FORMS;

        $this->validateUpload($request, $isVideo);

        $file = $request->file('file');
        $data['file_path'] = $file->store('submissions', 'local');
        $data['file_original_name'] = $file->getClientOriginalName();
        $data['file_mime'] = $file->getMimeType();
        $data['file_size'] = $file->getSize();
        $data['video_duration_minutes'] = $isVideo ? round(((float) $data['video_duration_seconds']) / 60, 2) : null;
        $data['tutorial_number'] = $isVideo ? $data['tutorial_number'] : null;
        $data['semester_intake'] = $isQuestionBank ? $data['semester_intake'] : null;
        $data['course'] = ($isQuestionBank || $isMarkEntry) ? $data['course'] : null;
        $data['course_name'] = $isMarkEntry ? $data['course_name'] : null;
        $data['programme'] = ($isQuestionBank || $isMarkEntry) ? $data['programme'] : null;
        $data['reviewed_by'] = $data['status'] === 'reviewed' ? auth()->id() : null;
        $data['reviewed_at'] = $data['status'] === 'reviewed' ? now() : null;

        unset($data['video_duration_seconds'], $data['file']);
        $submission = Submission::create($data);

        return redirect()->route('admin.submissions.show', $submission)
            ->with('success', 'Submission created successfully.');
    }

    public function show(Submission $submission): View
    {
        $submission->load('profile.user', 'reviewer');
        return view('admin.submissions.show', compact('submission'));
    }

    public function edit(Submission $submission): View
    {
        $profiles = Profile::with('user')->orderBy('full_name')->get();
        $submissionTypes = Submission::TYPES;
        return view('admin.submissions.edit', compact('submission', 'profiles', 'submissionTypes'));
    }

    public function update(Request $request, Submission $submission): RedirectResponse
    {
        $data = $this->validatedData($request, false);
        $isVideo = $data['submission_type'] === Submission::TYPE_VIDEO_RECORDING;
        $isQuestionBank = $data['submission_type'] === Submission::TYPE_QUESTION_BANK_ANSWER_SHEET;
        $isMarkEntry = $data['submission_type'] === Submission::TYPE_MARK_ENTRY_FORMS;

        if ($request->hasFile('file')) {
            $this->validateUpload($request, $isVideo);
            if (!$submission->hasVideoLink()) {
                Storage::disk('local')->delete($submission->file_path);
            }
            $file = $request->file('file');
            $data['file_path'] = $file->store('submissions', 'local');
            $data['file_original_name'] = $file->getClientOriginalName();
            $data['file_mime'] = $file->getMimeType();
            $data['file_size'] = $file->getSize();
        }

        $data['video_duration_minutes'] = $isVideo ? round(((float) $data['video_duration_seconds']) / 60, 2) : null;
        $data['tutorial_number'] = $isVideo ? $data['tutorial_number'] : null;
        $data['semester_intake'] = $isQuestionBank ? $data['semester_intake'] : null;
        $data['course'] = ($isQuestionBank || $isMarkEntry) ? $data['course'] : null;
        $data['course_name'] = $isMarkEntry ? $data['course_name'] : null;
        $data['programme'] = ($isQuestionBank || $isMarkEntry) ? $data['programme'] : null;
        $data['reviewed_by'] = $data['status'] === 'reviewed' ? ($submission->reviewed_by ?: auth()->id()) : null;
        $data['reviewed_at'] = $data['status'] === 'reviewed' ? ($submission->reviewed_at ?: now()) : null;

        unset($data['video_duration_seconds'], $data['file']);
        $submission->update($data);

        return redirect()->route('admin.submissions.show', $submission)
            ->with('success', 'Submission updated successfully.');
    }

    public function download(Submission $submission): StreamedResponse
    {
        abort_if($submission->hasVideoLink(), 404);
        abort_if(!Storage::disk('local')->exists($submission->file_path), 404);
        return Storage::disk('local')->download($submission->file_path, $submission->file_original_name);
    }

    public function destroy(Submission $submission): RedirectResponse
    {
        if (!$submission->hasVideoLink()) {
            Storage::disk('local')->delete($submission->file_path);
        }
        $submission->delete();

        return redirect()->route('admin.submissions.index')
            ->with('success', 'Submission deleted successfully.');
    }

    private function validatedData(Request $request, bool $fileRequired = true): array
    {
        return $request->validate([
            'profile_id' => ['required', 'exists:profiles,id'],
            'submission_type' => ['required', 'string', 'in:' . implode(',', array_keys(Submission::TYPES))],
            'submission_date' => ['required', 'date', 'before_or_equal:today'],
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'tutorial_number' => ['nullable', 'required_if:submission_type,' . Submission::TYPE_VIDEO_RECORDING, 'integer', 'between:1,5'],
            'video_duration_seconds' => ['nullable', 'required_if:submission_type,' . Submission::TYPE_VIDEO_RECORDING, 'numeric', 'min:0', 'max:86400'],
            'semester_intake' => ['nullable', 'required_if:submission_type,' . Submission::TYPE_QUESTION_BANK_ANSWER_SHEET, 'string', 'in:January,May,September'],
            'course' => ['nullable', 'required_if:submission_type,' . Submission::TYPE_QUESTION_BANK_ANSWER_SHEET, 'required_if:submission_type,' . Submission::TYPE_MARK_ENTRY_FORMS, 'string', 'in:CRM300,CSC400,CIT400'],
            'course_name' => ['nullable', 'required_if:submission_type,' . Submission::TYPE_MARK_ENTRY_FORMS, 'string', 'in:Industrial Training,Customer Relationship Management,Software Construction'],
            'programme' => ['nullable', 'required_if:submission_type,' . Submission::TYPE_QUESTION_BANK_ANSWER_SHEET, 'required_if:submission_type,' . Submission::TYPE_MARK_ENTRY_FORMS, 'string', 'in:BBA,BICT,BDCM'],
            'status' => ['required', 'in:pending,reviewed'],
            'executive_remarks' => ['nullable', 'string'],
            'file' => [$fileRequired ? 'required' : 'nullable', 'file'],
        ]);
    }

    private function validateUpload(Request $request, bool $isVideo): void
    {
        $request->validate([
            'file' => $isVideo
                ? ['mimetypes:video/mp4,video/quicktime,video/webm,video/x-matroska,video/x-msvideo,video/x-ms-wmv', 'max:512000']
                : ['mimes:pdf', 'max:5120'],
        ]);
    }
}
