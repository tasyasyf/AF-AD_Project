<?php

namespace App\Http\Controllers\ProgramCoordinator;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Profile;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AfAdController extends Controller
{
    public function index(Request $request): View
    {
        $query = Profile::verified()
            ->with(['user', 'appointments' => fn ($appointments) => $appointments->active()->latest('start_date')]);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($profile) use ($search) {
                $profile->where('full_name', 'like', "%{$search}%")
                    ->orWhere('ic_number', 'like', "%{$search}%")
                    ->orWhere('contact_email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('semester')) {
            $query->whereHas('appointments', fn ($appointments) => $appointments->where('semester', $request->semester));
        }

        if ($request->filled('course')) {
            $query->whereHas('appointments', function ($appointments) use ($request) {
                $appointments->where('course_code', $request->course)
                    ->orWhere('course_name', $request->course);
            });
        }

        $profiles = $query->latest('verified_at')->paginate(12)->withQueryString();

        $semesters = Appointment::query()
            ->whereNotNull('semester')
            ->distinct()
            ->orderBy('semester')
            ->pluck('semester');

        $courses = Appointment::query()
            ->select('course_code', 'course_name')
            ->distinct()
            ->orderBy('course_code')
            ->get();

        return view('program-coordinator.afad.index', compact('profiles', 'semesters', 'courses'));
    }

    public function show(Profile $profile): View
    {
        abort_if($profile->status !== 'verified', 404);

        $profile->load([
            'user',
            'certificates',
            'appointments' => fn ($appointments) => $appointments->latest('start_date'),
            'claims' => fn ($claims) => $claims->latest()->limit(5),
        ]);

        return view('program-coordinator.afad.show', compact('profile'));
    }
}
