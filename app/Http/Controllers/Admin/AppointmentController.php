<?php

namespace App\Http\Controllers\Admin;

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

    public function create(): View
    {
        $profiles = Profile::verified()->with('user')->orderBy('full_name')->get();
        return view('admin.appointments.create', compact('profiles'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'profile_id'       => ['required', 'exists:profiles,id'],
            'course_code'      => ['required', 'string', 'in:CRM300,CSC400,CIT400'],
            'course_name'      => ['required', 'string', 'in:Industrial Training,Customer Relationship Management,Software Construction'],
            'role_type'        => ['required', 'in:af,ad,af_internal,ad_internal'],
            'semester'         => ['required', 'string', 'in:January,May,September'],
            'academic_session' => ['required', 'string', 'max:20'],
            'start_date'       => ['required', 'date'],
            'end_date'         => ['required', 'date', 'after_or_equal:start_date'],
            'venue'            => ['nullable', 'string', 'max:255'],
            'student_count'    => ['nullable', 'integer', 'min:0'],
            'notes'            => ['nullable', 'string'],
        ]);

        $profile = Profile::findOrFail($data['profile_id']);
        abort_if($profile->status !== 'verified', 422, 'Profile must be verified before appointment.');

        $data['appointed_by'] = auth()->id();
        $data['is_active'] = true;
        $appointment = Appointment::create($data);

        return redirect()->route('admin.appointments.show', $appointment)
            ->with('success', 'Appointment created successfully.');
    }

    public function edit(Appointment $appointment): View
    {
        return view('admin.appointments.edit', compact('appointment'));
    }

    public function update(Request $request, Appointment $appointment): RedirectResponse
    {
        $data = $request->validate([
            'course_code'      => ['required', 'string', 'in:CRM300,CSC400,CIT400'],
            'course_name'      => ['required', 'string', 'in:Industrial Training,Customer Relationship Management,Software Construction'],
            'role_type'        => ['required', 'in:af,ad,af_internal,ad_internal'],
            'semester'         => ['required', 'string', 'max:20'],
            'academic_session' => ['required', 'string', 'max:20'],
            'start_date'       => ['required', 'date'],
            'end_date'         => ['required', 'date', 'after_or_equal:start_date'],
            'venue'            => ['nullable', 'string', 'max:255'],
            'student_count'    => ['nullable', 'integer', 'min:0'],
            'is_active'        => ['nullable', 'boolean'],
            'notes'            => ['nullable', 'string'],
        ]);

        $data['is_active'] = $request->boolean('is_active');
        $appointment->update($data);

        return redirect()->route('admin.appointments.show', $appointment)
            ->with('success', 'Appointment updated successfully.');
    }

    public function destroy(Appointment $appointment): RedirectResponse
    {
        $appointment->delete();

        return redirect()->route('admin.appointments.index')
            ->with('success', 'Appointment deleted successfully.');
    }
}
