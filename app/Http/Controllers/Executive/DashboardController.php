<?php

namespace App\Http\Controllers\Executive;

use App\Http\Controllers\Controller;
use App\Models\Claim;
use App\Models\Profile;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $stats = [
            'pending_profiles'  => Profile::pending()->count(),
            'verified_profiles' => Profile::verified()->count(),
            'rejected_profiles' => Profile::rejected()->count(),
            'claims_submitted'  => Claim::submitted()->count(),
            'claims_under_review' => Claim::underReview()->count(),
            'claims_approved'   => Claim::approved()->count(),
            'claims_returned'   => Claim::returned()->count(),
        ];

        $pending_profiles = Profile::pending()->with('user')->latest()->take(5)->get();
        $pending_claims   = Claim::whereIn('status', ['submitted', 'under_review', 'approved', 'returned', 'rejected'])
            ->with(['profile.user', 'appointment'])
            ->latest()
            ->take(5)
            ->get();

        return view('executive.dashboard', compact('stats', 'pending_profiles', 'pending_claims'));
    }
}
