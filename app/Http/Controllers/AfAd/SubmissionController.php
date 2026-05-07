<?php

namespace App\Http\Controllers\AfAd;

use App\Http\Controllers\Controller;
use App\Models\Submission;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SubmissionController extends Controller
{
    public function index(): View|RedirectResponse
    {
        $profile = auth()->user()->profile;
        if (!$profile) {
            return redirect()->route('afad.profile.create')
                ->with('info', 'Complete your profile before submitting recordings.');
        }
        $submissions = $profile->submissions()->latest()->paginate(10);
        return view('afad.submissions.index', compact('submissions'));
    }

    public function create(): View|RedirectResponse
    {
        $profile = auth()->user()->profile;
        if (!$profile) {
            return redirect()->route('afad.profile.create');
        }
        $submissionTypes = Submission::TYPES;
        return view('afad.submissions.create', compact('submissionTypes'));
    }

    public function store(Request $request): RedirectResponse
    {
        $profile = auth()->user()->profile;
        abort_if(!$profile, 403);

        $data = $request->validate([
            'submission_type' => ['required', 'string', 'in:' . implode(',', array_keys(Submission::TYPES))],
            'submission_date' => ['required', 'date', 'before_or_equal:today'],
            'title'       => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'tutorial_number' => ['nullable', 'required_if:submission_type,' . Submission::TYPE_VIDEO_RECORDING, 'integer', 'between:1,5'],
            'video_duration_seconds' => ['nullable', 'required_if:submission_type,' . Submission::TYPE_VIDEO_RECORDING, 'numeric', 'min:1', 'max:86400'],
            'claim_hours' => ['nullable', 'numeric', 'min:0.01', 'max:9999'],
            'rate_per_hour' => ['nullable', 'required_if:submission_type,' . Submission::TYPE_VIDEO_RECORDING, 'numeric', 'min:0', 'max:999999'],
            'semester_intake' => ['nullable', 'required_if:submission_type,' . Submission::TYPE_QUESTION_BANK_ANSWER_SHEET, 'string', 'in:January,May,September'],
            'course' => ['required', 'string', 'in:CRM300,CSC400,CIT400'],
            'course_name' => ['required', 'string', 'in:Industrial Training,Customer Relationship Management,Software Construction'],
            'programme' => ['required', 'string', 'in:BBA,BICT,BDCM'],
            'file'        => ['required', 'file'],
        ]);

        $isVideoRecording = $data['submission_type'] === Submission::TYPE_VIDEO_RECORDING;
        $isQuestionBankAnswerSheet = $data['submission_type'] === Submission::TYPE_QUESTION_BANK_ANSWER_SHEET;

        $request->validate([
            'file' => $isVideoRecording
                ? ['mimetypes:video/mp4,video/quicktime,video/webm,video/x-matroska,video/x-msvideo,video/x-ms-wmv', 'max:512000']
                : ['mimes:pdf', 'max:5120'],
        ], [
            'file.mimetypes' => 'Video recording submissions must be uploaded as a video file.',
            'file.mimes' => 'This submission type must be uploaded as a PDF file.',
            'file.max' => $isVideoRecording
                ? 'Video recording submissions must not exceed 500MB.'
                : 'PDF submissions must not exceed 5MB.',
        ]);

        $videoDurationMinutes = $isVideoRecording
            ? round(((float) $data['video_duration_seconds']) / 60, 2)
            : null;
        $claimHours = $isVideoRecording
            ? round(((float) $data['video_duration_seconds']) / 3600, 2)
            : null;
        $submissionRate = $isVideoRecording ? (float) $data['rate_per_hour'] : null;
        $submissionTotalAmount = $isVideoRecording ? round($claimHours * $submissionRate, 2) : null;

        $request->validate([
            'video_duration_seconds' => $isVideoRecording && $videoDurationMinutes <= 0
                ? ['prohibited']
                : ['nullable'],
        ], [
            'video_duration_seconds.prohibited' => 'The video duration could not be calculated. Please choose the video again.',
        ]);

        if ($isVideoRecording && $videoDurationMinutes <= 0) {
            return back()
                ->withErrors(['file' => 'The video duration could not be calculated. Please choose the video again.'])
                ->withInput();
        }

        if (!$isVideoRecording) {
            $data['tutorial_number'] = null;
        }

        if (!$isQuestionBankAnswerSheet) {
            $data['semester_intake'] = null;
        }

        $file = $request->file('file');
        $path = $file->store('submissions', 'local');

        Submission::create([
            'profile_id'         => $profile->id,
            'submission_type'    => $data['submission_type'],
            'submission_date'    => $data['submission_date'],
            'title'              => $data['title'],
            'description'        => $data['description'] ?? null,
            'file_path'          => $path,
            'file_original_name' => $file->getClientOriginalName(),
            'file_mime'          => $file->getMimeType(),
            'file_size'          => $file->getSize(),
            'video_duration_minutes' => $videoDurationMinutes,
            'tutorial_number'    => $data['tutorial_number'] ?? null,
            'claim_hours'        => $claimHours,
            'rate_per_hour'      => $submissionRate,
            'total_amount'       => $submissionTotalAmount,
            'semester_intake'    => $data['semester_intake'] ?? null,
            'course'             => $data['course'] ?? null,
            'course_name'        => $data['course_name'] ?? null,
            'programme'          => $data['programme'] ?? null,
        ]);

        return redirect()->route('afad.submissions.index')
            ->with('success', 'Submission uploaded successfully.');
    }

    public function show(Submission $submission): View
    {
        abort_if($submission->profile->user_id !== auth()->id(), 403);
        $submission->load('reviewer');
        return view('afad.submissions.show', compact('submission'));
    }

    public function download(Submission $submission): StreamedResponse
    {
        abort_if($submission->profile->user_id !== auth()->id(), 403);
        abort_if(!Storage::disk('local')->exists($submission->file_path), 404);

        return Storage::disk('local')->download($submission->file_path, $submission->file_original_name);
    }

    public function destroy(Submission $submission): RedirectResponse
    {
        abort_if($submission->profile->user_id !== auth()->id(), 403);
        abort_if($submission->status === 'reviewed', 403, 'Reviewed submissions cannot be deleted.');

        if ($submission->file_path) {
            Storage::disk('local')->delete($submission->file_path);
        }
        $submission->delete();

        return redirect()->route('afad.submissions.index')
            ->with('success', 'Submission removed successfully.');
    }
}
