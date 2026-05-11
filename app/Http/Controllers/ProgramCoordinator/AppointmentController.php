<?php

namespace App\Http\Controllers\ProgramCoordinator;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Profile;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AppointmentController extends Controller
{
    public function index(Request $request): View
    {
        $query = Appointment::with('profile.user')->latest('start_date');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($appointment) use ($search) {
                $appointment->where('course_code', 'like', "%{$search}%")
                    ->orWhere('course_name', 'like', "%{$search}%")
                    ->orWhereHas('profile', fn ($profile) => $profile->where('full_name', 'like', "%{$search}%"));
            });
        }

        if ($request->filled('semester')) {
            $query->where('semester', $request->semester);
        }

        if ($request->filled('course')) {
            $query->where('course_code', $request->course);
        }

        $appointments = $query->paginate(15)->withQueryString();
        $semesters = Appointment::distinct()->orderBy('semester')->pluck('semester');
        $courses = Appointment::distinct()->orderBy('course_code')->pluck('course_code');

        return view('program-coordinator.appointments.index', compact('appointments', 'semesters', 'courses'));
    }

    public function create(Request $request): View
    {
        $profiles = Profile::verified()->with('user')->orderBy('full_name')->get();
        $selectedProfile = $request->integer('profile_id');

        return view('program-coordinator.appointments.create', compact('profiles', 'selectedProfile'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'profile_id'       => ['required', 'exists:profiles,id'],
            'course_code'      => ['required', 'string', 'in:CRM300,CSC400,CIT400'],
            'course_name'      => ['required', 'string', 'in:Industrial Training,Customer Relationship Management,Software Construction'],
            'role_type'        => ['required', 'in:af,ad,af_internal,ad_internal'],
            'semester'         => ['required', 'string', 'max:20'],
            'academic_session' => ['required', 'string', 'max:20'],
            'start_date'       => ['required', 'date'],
            'end_date'         => ['required', 'date', 'after_or_equal:start_date'],
            'venue'            => ['nullable', 'string', 'max:255'],
            'student_count'    => ['nullable', 'integer', 'min:1'],
            'notes'            => ['nullable', 'string'],
        ]);

        $profile = Profile::findOrFail($data['profile_id']);
        abort_if($profile->status !== 'verified', 422, 'Profile must be verified before appointment.');

        $data['appointed_by'] = auth()->id();
        Appointment::create($data);

        return redirect()->route('pc.appointments.index')
            ->with('success', 'Appointment and AF/AD role assignment created successfully.');
    }
}
