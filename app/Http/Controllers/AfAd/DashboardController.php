<?php

namespace App\Http\Controllers\AfAd;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $user = auth()->user();
        $profile = $user->profile()->with('verifier')->first();

        $stats = [
            'profile_status'    => $profile?->status ?? 'not_registered',
            'total_appointments' => $profile ? $profile->appointments()->count() : 0,
            'pending_claims'    => $profile ? $profile->claims()->whereIn('status', ['draft', 'submitted', 'under_review'])->count() : 0,
            'approved_claims'   => $profile ? $profile->claims()->where('status', 'approved')->count() : 0,
        ];

        $recent_claims = $profile
            ? $profile->claims()->with('appointment')->latest()->take(5)->get()
            : collect();

        $submission_documents = $profile
            ? $profile->submissions()->latest()->take(12)->get()
            : collect();

        return view('afad.dashboard', compact('profile', 'stats', 'recent_claims', 'submission_documents'));
    }
}
