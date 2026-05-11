<?php

namespace App\Http\Controllers\ProgramCoordinator;

use App\Http\Controllers\Controller;
use App\Models\Profile;
use Illuminate\View\View;

class NominationController extends Controller
{
    public function index(): View
    {
        $profiles = Profile::verified()
            ->with(['appointments' => fn ($appointments) => $appointments->active()->latest('start_date')])
            ->latest('verified_at')
            ->paginate(12);

        return view('program-coordinator.nomination.index', compact('profiles'));
    }
}
