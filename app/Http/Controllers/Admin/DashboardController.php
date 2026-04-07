<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Claim;
use App\Models\Profile;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $stats = [
            'total_afad'         => User::where('role', 'afad')->count(),
            'total_profiles'     => Profile::count(),
            'verified_profiles'  => Profile::verified()->count(),
            'pending_profiles'   => Profile::pending()->count(),
            'total_appointments' => Appointment::count(),
            'total_claims'       => Claim::count(),
            'claims_approved'    => Claim::approved()->count(),
            'claims_pending'     => Claim::whereIn('status', ['submitted', 'under_review'])->count(),
            'total_claim_amount' => Claim::approved()->sum('total_amount'),
        ];

        return view('admin.dashboard', compact('stats'));
    }
}
