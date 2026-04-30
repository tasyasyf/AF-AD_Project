<?php

namespace App\Http\Controllers\Executive;

use App\Http\Controllers\Controller;
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
        return view('executive.submissions.index', compact('submissions'));
    }

    public function show(Submission $submission): View
    {
        $submission->load('profile.user', 'reviewer');
        return view('executive.submissions.show', compact('submission'));
    }

    public function download(Submission $submission): StreamedResponse
    {
        abort_if(!Storage::disk('local')->exists($submission->file_path), 404);
        return Storage::disk('local')->download($submission->file_path, $submission->file_original_name);
    }

    public function review(Request $request, Submission $submission): RedirectResponse
    {
        $data = $request->validate([
            'executive_remarks' => ['nullable', 'string'],
        ]);

        $submission->update([
            'status'            => 'reviewed',
            'reviewed_by'       => auth()->id(),
            'reviewed_at'       => now(),
            'executive_remarks' => $data['executive_remarks'] ?? null,
        ]);

        return redirect()->route('executive.submissions.show', $submission)
            ->with('success', 'Submission marked as reviewed.');
    }
}
