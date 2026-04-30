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
        return view('afad.submissions.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $profile = auth()->user()->profile;
        abort_if(!$profile, 403);

        $data = $request->validate([
            'title'       => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'file'        => ['required', 'file', 'mimes:pdf,xls,xlsx,csv', 'max:5120'],
        ]);

        $file = $request->file('file');
        $path = $file->store('submissions', 'local');

        Submission::create([
            'profile_id'         => $profile->id,
            'title'              => $data['title'],
            'description'        => $data['description'] ?? null,
            'file_path'          => $path,
            'file_original_name' => $file->getClientOriginalName(),
            'file_mime'          => $file->getMimeType(),
            'file_size'          => $file->getSize(),
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
