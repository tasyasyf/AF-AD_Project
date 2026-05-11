<?php

namespace App\Http\Controllers\ProgramCoordinator;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Claim;
use App\Models\Profile;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $stats = [
            'pending_pc_reviews' => Claim::where('status', 'approved')->whereNull('pc_endorsed_at')->count(),
            'active_afad' => Profile::verified()->count(),
            'active_permissions' => Appointment::active()->count(),
            'monthly_endorsed' => Claim::whereNotNull('pc_endorsed_at')
                ->whereMonth('pc_endorsed_at', now()->month)
                ->whereYear('pc_endorsed_at', now()->year)
                ->count(),
            'monthly_declined' => Claim::whereIn('status', ['returned', 'rejected'])
                ->whereMonth('reviewed_at', now()->month)
                ->whereYear('reviewed_at', now()->year)
                ->count(),
        ];

        $pendingClaims = Claim::with(['profile', 'appointment'])
            ->where('status', 'approved')
            ->whereNull('pc_endorsed_at')
            ->latest('reviewed_at')
            ->limit(5)
            ->get();

        $verifiedProfiles = Profile::verified()
            ->with(['user', 'appointments' => fn ($query) => $query->active()->latest('start_date')])
            ->latest('verified_at')
            ->limit(5)
            ->get();

        return view('program-coordinator.dashboard', compact('stats', 'pendingClaims', 'verifiedProfiles'));
    }
}
