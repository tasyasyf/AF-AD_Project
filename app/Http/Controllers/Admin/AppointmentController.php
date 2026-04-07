<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AppointmentController extends Controller
{
    public function index(Request $request): View
    {
        $query = Appointment::with('profile.user')->latest('start_date');

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('course_code', 'like', "%{$request->search}%")
                  ->orWhere('course_name', 'like', "%{$request->search}%");
            });
        }
        if ($request->filled('role_type')) {
            $query->where('role_type', $request->role_type);
        }

        $appointments = $query->paginate(15)->withQueryString();
        return view('admin.appointments.index', compact('appointments'));
    }

    public function show(Appointment $appointment): View
    {
        $appointment->load(['profile.user', 'appointedBy', 'claims']);
        return view('admin.appointments.show', compact('appointment'));
    }
}
