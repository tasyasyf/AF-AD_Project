<?php

namespace App\Http\Controllers\AfAd;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class AppointmentController extends Controller
{
    public function index(): View|RedirectResponse
    {
        $profile = auth()->user()->profile;
        if (!$profile) {
            return redirect()->route('afad.profile.create')
                ->with('info', 'Complete your profile to view appointments.');
        }
        $appointments = $profile->appointments()->latest('start_date')->paginate(10);
        return view('afad.appointments.index', compact('appointments'));
    }

    public function show(Appointment $appointment): View
    {
        abort_if($appointment->profile->user_id !== auth()->id(), 403);
        $appointment->load('claims');
        return view('afad.appointments.show', compact('appointment'));
    }
}
