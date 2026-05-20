<?php

namespace App\Http\Controllers\ProgramCoordinator;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\ClaimDocument;
use App\Models\Submission;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DocumentChecklistController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $request->validate([
            'course' => ['nullable', 'string', 'max:20'],
            'semester' => ['nullable', 'string', 'max:20'],
        ]);

        $query = Appointment::with([
            'profile.user',
            'profile.submissions',
            'claims.documents',
        ])->latest('start_date');

        if ($request->filled('course')) {
            $query->where('course_code', $filters['course']);
        }

        if ($request->filled('semester')) {
            $query->where('semester', $filters['semester']);
        }

        $appointments = $query->paginate(15)->withQueryString();

        $courses = Appointment::query()
            ->whereNotNull('course_code')
            ->distinct()
            ->orderBy('course_code')
            ->pluck('course_code');

        $semesters = Appointment::query()
            ->whereNotNull('semester')
            ->distinct()
            ->orderBy('semester')
            ->pluck('semester');

        return view('program-coordinator.document-checklist.index', compact(
            'appointments',
            'courses',
            'semesters',
            'filters'
        ));
    }

    public function confirmQbAs(Request $request, Submission $submission): RedirectResponse
    {
        abort_if(!$submission->isQuestionBankAnswerSheet(), 404);

        $data = $request->validate([
            'pc_qbas_set_count' => ['required', 'integer', 'min:2', 'max:20'],
            'pc_qbas_remarks' => ['nullable', 'string', 'max:1000'],
        ]);

        $submission->update([
            'pc_qbas_status' => 'confirmed',
            'pc_qbas_set_count' => $data['pc_qbas_set_count'],
            'pc_qbas_checked_by' => auth()->id(),
            'pc_qbas_checked_at' => now(),
            'pc_qbas_remarks' => $data['pc_qbas_remarks'] ?? null,
        ]);

        return redirect()->back()->with('success', 'QB-AS quantity confirmed by Program Coordinator.');
    }

    public function rejectQbAs(Request $request, Submission $submission): RedirectResponse
    {
        abort_if(!$submission->isQuestionBankAnswerSheet(), 404);

        $data = $request->validate([
            'pc_qbas_set_count' => ['nullable', 'integer', 'min:0', 'max:20'],
            'pc_qbas_remarks' => ['required', 'string', 'max:1000'],
        ]);

        $submission->update([
            'pc_qbas_status' => 'rejected',
            'pc_qbas_set_count' => $data['pc_qbas_set_count'] ?? null,
            'pc_qbas_checked_by' => auth()->id(),
            'pc_qbas_checked_at' => now(),
            'pc_qbas_remarks' => $data['pc_qbas_remarks'],
        ]);

        return redirect()->back()->with('success', 'QB-AS quantity rejected and visible to AF/AD.');
    }

    public function viewSubmission(Submission $submission): StreamedResponse
    {
        abort_if($submission->hasVideoLink(), 404);
        abort_if(!Storage::disk('local')->exists($submission->file_path), 404);

        return Storage::disk('local')->response($submission->file_path, $submission->file_original_name);
    }

    public function viewClaimDocument(ClaimDocument $document): StreamedResponse
    {
        abort_if(!$document->is_uploaded || !$document->file_path, 404);
        abort_if(!Storage::disk('local')->exists($document->file_path), 404);

        return Storage::disk('local')->response($document->file_path, $document->file_original_name);
    }
}
