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
            'total_executives'   => User::where('role', 'executive')->count(),
            'total_pc'           => User::where('role', 'pc')->count(),
            'total_admins'       => User::where('role', 'admin')->count(),
            'total_profiles'     => Profile::count(),
            'verified_profiles'  => Profile::verified()->count(),
            'pending_profiles'   => Profile::pending()->count(),
            'total_appointments' => Appointment::count(),
            'total_claims'       => Claim::count(),
            'claims_approved'    => Claim::approved()->count(),
            'claims_pending'     => Claim::whereIn('status', ['submitted', 'under_review'])->count(),
            'claims_pending_pc'  => Claim::where('status', 'approved')->whereNull('pc_endorsed_at')->count(),
            'claims_pc_endorsed' => Claim::whereNotNull('pc_endorsed_at')->count(),
            'total_claim_amount' => Claim::approved()->sum('total_amount'),
        ];

        return view('admin.dashboard', compact('stats'));
    }
}
