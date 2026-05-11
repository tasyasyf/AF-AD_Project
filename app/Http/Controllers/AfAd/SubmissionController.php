<?php

namespace App\Http\Controllers\AfAd;

use App\Http\Controllers\Controller;
use App\Models\Submission;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\JsonResponse;
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
            'video_url' => ['nullable', 'url', 'max:500'],
            'video_duration_seconds' => ['nullable', 'numeric', 'min:0', 'max:86400'],
            'claim_hours' => ['nullable', 'numeric', 'min:0', 'max:9999'],
            'rate_per_hour' => ['required', 'numeric', 'min:0', 'max:999999'],
            'semester_intake' => ['nullable', 'required_if:submission_type,' . Submission::TYPE_QUESTION_BANK_ANSWER_SHEET, 'string', 'in:January,May,September'],
            'course' => ['required', 'string', 'in:CRM300,CSC400,CIT400'],
            'course_name' => ['required', 'string', 'in:Industrial Training,Customer Relationship Management,Software Construction'],
            'programme' => ['required', 'string', 'in:BBA,BICT,BDCM'],
            'file'        => ['nullable', 'file'],
        ]);

        $isVideoRecording = $data['submission_type'] === Submission::TYPE_VIDEO_RECORDING;
        $isQuestionBankAnswerSheet = $data['submission_type'] === Submission::TYPE_QUESTION_BANK_ANSWER_SHEET;

        if ($isVideoRecording) {
            $request->validate([
                'video_url' => ['required', 'url', 'max:500'],
            ], [
                'video_url.required' => 'Please paste the video recording link.',
                'video_url.url' => 'Please enter a valid video recording link.',
            ]);
        } else {
            $request->validate([
                'file' => ['required', 'mimes:pdf', 'max:5120'],
            ], [
                'file.required' => 'Please upload the required PDF file.',
                'file.mimes' => 'This submission type must be uploaded as a PDF file.',
                'file.max' => 'PDF submissions must not exceed 5MB.',
            ]);
        }

        $videoDurationSeconds = (float) ($data['video_duration_seconds'] ?? 0);
        $videoDurationMinutes = $isVideoRecording && $videoDurationSeconds > 0
            ? round($videoDurationSeconds / 60, 2)
            : null;
        $claimHours = $isVideoRecording && $videoDurationSeconds > 0
            ? round($videoDurationSeconds / 3600, 2)
            : null;
        $submissionRate = (float) $data['rate_per_hour'];
        $submissionTotalAmount = $isVideoRecording
            ? ($claimHours !== null ? round($claimHours * $submissionRate, 2) : null)
            : round($submissionRate, 2);

        if (!$isVideoRecording) {
            $data['tutorial_number'] = null;
        }

        if (!$isQuestionBankAnswerSheet) {
            $data['semester_intake'] = null;
        }

        $file = $request->file('file');
        $path = $isVideoRecording ? $data['video_url'] : $file->store('submissions', 'local');

        $submission = Submission::create([
            'profile_id'         => $profile->id,
            'submission_type'    => $data['submission_type'],
            'submission_date'    => $data['submission_date'],
            'title'              => $data['title'],
            'description'        => $data['description'] ?? null,
            'file_path'          => $path,
            'file_original_name' => $isVideoRecording ? 'Video recording link' : $file->getClientOriginalName(),
            'file_mime'          => $isVideoRecording ? 'text/uri-list' : $file->getMimeType(),
            'file_size'          => $isVideoRecording ? 0 : $file->getSize(),
            'video_url'          => $isVideoRecording ? $data['video_url'] : null,
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

        return redirect()->route($isVideoRecording ? 'afad.submissions.show' : 'afad.submissions.index', $isVideoRecording ? $submission : [])
            ->with('success', 'Submission uploaded successfully.');
    }

    public function updateVideoDuration(Request $request, Submission $submission): JsonResponse
    {
        abort_if($submission->profile->user_id !== auth()->id(), 403);
        abort_if(!$submission->isVideoRecording(), 404);

        $data = $request->validate([
            'video_duration_seconds' => ['required', 'numeric', 'min:1', 'max:86400'],
        ]);

        $durationSeconds = (float) $data['video_duration_seconds'];
        $durationMinutes = round($durationSeconds / 60, 2);
        $claimHours = round($durationSeconds / 3600, 2);
        $totalAmount = $submission->rate_per_hour !== null
            ? round($claimHours * (float) $submission->rate_per_hour, 2)
            : null;

        $submission->update([
            'video_duration_minutes' => $durationMinutes,
            'claim_hours' => $claimHours,
            'total_amount' => $totalAmount,
        ]);

        return response()->json([
            'video_duration_minutes' => $durationMinutes,
            'claim_hours' => $claimHours,
            'total_amount' => $totalAmount,
            'formatted_duration' => number_format($durationMinutes, 2) . ' minutes',
            'formatted_claim_hours' => number_format($claimHours, 2),
            'formatted_total_amount' => $totalAmount !== null ? 'RM ' . number_format($totalAmount, 2) : '—',
        ]);
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
        abort_if($submission->hasVideoLink(), 404);
        abort_if(!Storage::disk('local')->exists($submission->file_path), 404);

        return Storage::disk('local')->download($submission->file_path, $submission->file_original_name);
    }

    public function destroy(Submission $submission): RedirectResponse
    {
        abort_if($submission->profile->user_id !== auth()->id(), 403);
        abort_if($submission->status === 'reviewed', 403, 'Reviewed submissions cannot be deleted.');

        if (!$submission->isVideoRecording() && $submission->file_path) {
            Storage::disk('local')->delete($submission->file_path);
        }
        $submission->delete();

        return redirect()->route('afad.submissions.index')
            ->with('success', 'Submission removed successfully.');
    }
}
