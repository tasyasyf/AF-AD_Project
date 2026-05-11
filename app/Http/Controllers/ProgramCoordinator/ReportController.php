<?php

namespace App\Http\Controllers\ProgramCoordinator;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Claim;
use App\Models\Profile;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function index(Request $request): View
    {
        $filters = $this->filters($request);
        $claimQuery = $this->claimReportQuery($filters);
        $appointmentQuery = $this->appointmentReportQuery($filters);

        $summary = [
            'verified_profiles' => Profile::verified()->count(),
            'total_appointments' => (clone $appointmentQuery)->count(),
            'active_appointments' => (clone $appointmentQuery)->active()->count(),
            'submitted_claims' => (clone $claimQuery)->whereIn('status', ['submitted', 'under_review'])->count(),
            'approved_claims' => (clone $claimQuery)->where('status', 'approved')->count(),
            'pc_endorsed_claims' => (clone $claimQuery)->whereNotNull('pc_endorsed_at')->count(),
            'pending_pc_endorsements' => (clone $claimQuery)->where('status', 'approved')->whereNull('pc_endorsed_at')->count(),
            'declined_claims' => (clone $claimQuery)->whereIn('status', ['returned', 'rejected'])->count(),
            'total_claim_amount' => (clone $claimQuery)->where('status', '!=', 'draft')->sum('total_amount'),
            'approved_claim_amount' => (clone $claimQuery)->where('status', 'approved')->sum('total_amount'),
        ];

        $claimStatuses = (clone $claimQuery)
            ->select('status', DB::raw('COUNT(*) as total'), DB::raw('COALESCE(SUM(total_amount), 0) as amount'))
            ->groupBy('status')
            ->orderBy('status')
            ->get();

        $claimTypes = (clone $claimQuery)
            ->select('claim_type', DB::raw('COUNT(*) as total'), DB::raw('COALESCE(SUM(total_hours), 0) as hours'), DB::raw('COALESCE(SUM(total_amount), 0) as amount'))
            ->groupBy('claim_type')
            ->orderByDesc('amount')
            ->get();

        $appointmentCourses = (clone $appointmentQuery)
            ->select('course_code', 'course_name', DB::raw('COUNT(*) as total'), DB::raw('SUM(CASE WHEN is_active = 1 THEN 1 ELSE 0 END) as active_total'))
            ->groupBy('course_code', 'course_name')
            ->orderByDesc('total')
            ->limit(8)
            ->get();

        $recentClaims = (clone $claimQuery)
            ->with(['profile', 'appointment'])
            ->latest('submitted_at')
            ->latest()
            ->limit(10)
            ->get();

        $semesters = Appointment::query()
            ->whereNotNull('semester')
            ->distinct()
            ->orderBy('semester')
            ->pluck('semester');

        $courses = Appointment::query()
            ->select('course_code', 'course_name')
            ->distinct()
            ->orderBy('course_code')
            ->get();

        return view('program-coordinator.reports.index', compact(
            'summary',
            'claimStatuses',
            'claimTypes',
            'appointmentCourses',
            'recentClaims',
            'semesters',
            'courses',
            'filters'
        ));
    }

    public function export(Request $request): StreamedResponse
    {
        $filters = $this->filters($request);
        $claims = $this->claimReportQuery($filters)
            ->with(['profile', 'appointment'])
            ->latest('submitted_at')
            ->latest();

        $filename = 'pc-report-' . now()->format('Ymd-His') . '.csv';

        return response()->streamDownload(function () use ($claims) {
            $handle = fopen('php://output', 'w');

            fputcsv($handle, [
                'Reference',
                'AF/AD',
                'Course Code',
                'Course Name',
                'Semester',
                'Claim Type',
                'Period From',
                'Period To',
                'Hours',
                'Rate Per Hour',
                'Total Amount',
                'Status',
                'Submitted At',
            ]);

            $claims->chunk(200, function ($rows) use ($handle) {
                foreach ($rows as $claim) {
                    fputcsv($handle, [
                        $claim->claim_reference,
                        $claim->profile?->full_name,
                        $claim->appointment?->course_code,
                        $claim->appointment?->course_name,
                        $claim->appointment?->semester,
                        ucfirst(str_replace('_', ' ', $claim->claim_type)),
                        $claim->period_from?->format('Y-m-d'),
                        $claim->period_to?->format('Y-m-d'),
                        $claim->total_hours,
                        $claim->rate_per_hour,
                        $claim->total_amount,
                        ucfirst(str_replace('_', ' ', $claim->status)),
                        $claim->submitted_at?->format('Y-m-d H:i:s'),
                    ]);
                }
            });

            fclose($handle);
        }, $filename, ['Content-Type' => 'text/csv']);
    }

    private function filters(Request $request): array
    {
        return $request->validate([
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date', 'after_or_equal:from'],
            'semester' => ['nullable', 'string', 'max:20'],
            'course' => ['nullable', 'string', 'max:20'],
            'status' => ['nullable', 'in:draft,submitted,under_review,approved,returned,rejected'],
            'search' => ['nullable', 'string', 'max:100'],
        ]);
    }

    private function claimReportQuery(array $filters): Builder
    {
        return Claim::query()
            ->when($filters['from'] ?? null, fn (Builder $query, string $from) => $query->whereDate('period_from', '>=', $from))
            ->when($filters['to'] ?? null, fn (Builder $query, string $to) => $query->whereDate('period_to', '<=', $to))
            ->when($filters['status'] ?? null, fn (Builder $query, string $status) => $query->where('status', $status))
            ->when($filters['semester'] ?? null, function (Builder $query, string $semester) {
                $query->whereHas('appointment', fn (Builder $appointment) => $appointment->where('semester', $semester));
            })
            ->when($filters['course'] ?? null, function (Builder $query, string $course) {
                $query->whereHas('appointment', fn (Builder $appointment) => $appointment->where('course_code', $course));
            })
            ->when($filters['search'] ?? null, function (Builder $query, string $search) {
                $query->where(function (Builder $claim) use ($search) {
                    $claim->where('claim_reference', 'like', "%{$search}%")
                        ->orWhereHas('profile', fn (Builder $profile) => $profile->where('full_name', 'like', "%{$search}%"))
                        ->orWhereHas('appointment', fn (Builder $appointment) => $appointment
                            ->where('course_code', 'like', "%{$search}%")
                            ->orWhere('course_name', 'like', "%{$search}%"));
                });
            });
    }

    private function appointmentReportQuery(array $filters): Builder
    {
        return Appointment::query()
            ->when($filters['from'] ?? null, fn (Builder $query, string $from) => $query->whereDate('end_date', '>=', $from))
            ->when($filters['to'] ?? null, fn (Builder $query, string $to) => $query->whereDate('start_date', '<=', $to))
            ->when($filters['semester'] ?? null, fn (Builder $query, string $semester) => $query->where('semester', $semester))
            ->when($filters['course'] ?? null, fn (Builder $query, string $course) => $query->where('course_code', $course))
            ->when($filters['search'] ?? null, function (Builder $query, string $search) {
                $query->where(function (Builder $appointment) use ($search) {
                    $appointment->where('course_code', 'like', "%{$search}%")
                        ->orWhere('course_name', 'like', "%{$search}%")
                        ->orWhereHas('profile', fn (Builder $profile) => $profile->where('full_name', 'like', "%{$search}%"));
                });
            });
    }
}
