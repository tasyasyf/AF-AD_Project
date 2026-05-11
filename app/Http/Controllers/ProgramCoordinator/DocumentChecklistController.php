<?php

namespace App\Http\Controllers\ProgramCoordinator;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Submission;
use Illuminate\Http\Request;
use Illuminate\View\View;

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

        $qbAsTypes = [
            Submission::TYPE_QA,
            Submission::TYPE_QUESTION_BANK_ANSWER_SHEET,
        ];

        return view('program-coordinator.document-checklist.index', compact(
            'appointments',
            'courses',
            'semesters',
            'filters',
            'qbAsTypes'
        ));
    }
}
