<?php

namespace App\Http\Controllers;

use App\Models\Appointment;
use App\Models\Certificate;
use App\Models\Claim;
use App\Models\Profile;
use App\Models\Submission;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Read-only "Additional Access" viewer pages. Every action here is a GET that
 * renders aggregate data with no write capability. Access is gated per-page by
 * the `permitted:<key>` middleware in routes/web.php.
 */
class AccessController extends Controller
{
    public function profiles(Request $request): View
    {
        $search = $request->string('search')->toString();

        $profiles = Profile::with('user')
            ->when($search, fn (Builder $q) => $q->where(function (Builder $inner) use ($search) {
                $inner->where('full_name', 'like', "%{$search}%")
                    ->orWhere('ic_number', 'like', "%{$search}%")
                    ->orWhere('contact_email', 'like', "%{$search}%");
            }))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('access.profiles', compact('profiles', 'search'));
    }

    public function appointments(Request $request): View
    {
        $search = $request->string('search')->toString();

        $appointments = Appointment::with('profile.user')
            ->when($search, fn (Builder $q) => $q->where(function (Builder $inner) use ($search) {
                $inner->where('course_code', 'like', "%{$search}%")
                    ->orWhere('course_name', 'like', "%{$search}%")
                    ->orWhereHas('profile', fn (Builder $p) => $p->where('full_name', 'like', "%{$search}%"));
            }))
            ->latest('start_date')
            ->paginate(15)
            ->withQueryString();

        return view('access.appointments', compact('appointments', 'search'));
    }

    public function claims(Request $request): View
    {
        $search = $request->string('search')->toString();
        $status = $request->string('status')->toString();

        $claims = Claim::with(['profile.user', 'appointment', 'audits.performer'])
            ->where('status', '!=', 'draft')
            ->when($status, fn (Builder $q) => $q->where('status', $status))
            ->when($search, fn (Builder $q) => $q->where(function (Builder $inner) use ($search) {
                $inner->where('claim_reference', 'like', "%{$search}%")
                    ->orWhereHas('profile', fn (Builder $p) => $p->where('full_name', 'like', "%{$search}%"));
            }))
            ->latest('submitted_at')
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('access.claims', compact('claims', 'search', 'status'));
    }

    public function submissions(Request $request): View
    {
        $search = $request->string('search')->toString();

        $submissions = Submission::with('profile.user')
            ->when($search, fn (Builder $q) => $q->where(function (Builder $inner) use ($search) {
                $inner->where('title', 'like', "%{$search}%")
                    ->orWhere('course', 'like', "%{$search}%")
                    ->orWhereHas('profile', fn (Builder $p) => $p->where('full_name', 'like', "%{$search}%"));
            }))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('access.submissions', compact('submissions', 'search'));
    }

    public function certificates(Request $request): View
    {
        $search = $request->string('search')->toString();

        $certificates = Certificate::with('profile.user')
            ->when($search, fn (Builder $q) => $q->where(function (Builder $inner) use ($search) {
                $inner->where('title', 'like', "%{$search}%")
                    ->orWhere('issuing_institution', 'like', "%{$search}%")
                    ->orWhereHas('profile', fn (Builder $p) => $p->where('full_name', 'like', "%{$search}%"));
            }))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('access.certificates', compact('certificates', 'search'));
    }

    public function reports(): View
    {
        $summary = [
            'verified_profiles' => Profile::verified()->count(),
            'total_appointments' => Appointment::count(),
            'active_appointments' => Appointment::active()->count(),
            'submitted_claims' => Claim::whereIn('status', ['submitted', 'under_review'])->count(),
            'approved_claims' => Claim::where('status', 'approved')->count(),
            'pc_endorsed_claims' => Claim::whereNotNull('pc_endorsed_at')->count(),
            'declined_claims' => Claim::whereIn('status', ['returned', 'rejected'])->count(),
            'approved_claim_amount' => (float) Claim::where('status', 'approved')->sum('total_amount'),
        ];

        $claimStatuses = Claim::where('status', '!=', 'draft')
            ->select('status', DB::raw('COUNT(*) as total'), DB::raw('COALESCE(SUM(total_amount), 0) as amount'))
            ->groupBy('status')
            ->orderBy('status')
            ->get();

        $recentClaims = Claim::with(['profile', 'appointment'])
            ->where('status', '!=', 'draft')
            ->latest('submitted_at')
            ->latest()
            ->limit(10)
            ->get();

        return view('access.reports', compact('summary', 'claimStatuses', 'recentClaims'));
    }

    public function reportsExport(): StreamedResponse
    {
        $filename = 'claims-report-' . now()->format('Ymd-His') . '.csv';

        return response()->streamDownload(function () {
            $handle = fopen('php://output', 'w');
            fputcsv($handle, ['Reference', 'AF/AD', 'Course', 'Type', 'Period From', 'Period To', 'Amount', 'Status']);

            Claim::with(['profile', 'appointment'])
                ->where('status', '!=', 'draft')
                ->latest('submitted_at')
                ->chunk(200, function ($rows) use ($handle) {
                    foreach ($rows as $claim) {
                        fputcsv($handle, [
                            $claim->claim_reference,
                            $claim->profile?->full_name,
                            $claim->appointment?->course_code,
                            ucfirst(str_replace('_', ' ', $claim->claim_type)),
                            $claim->period_from?->format('Y-m-d'),
                            $claim->period_to?->format('Y-m-d'),
                            number_format((float) $claim->total_amount, 2),
                            $claim->status,
                        ]);
                    }
                });

            fclose($handle);
        }, $filename, ['Content-Type' => 'text/csv']);
    }
}
