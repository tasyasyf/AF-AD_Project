<?php

namespace App\Http\Controllers\AfAd;

use App\Http\Controllers\Controller;
use App\Models\ClassSession;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ClassController extends Controller
{
    public function index(): View|RedirectResponse
    {
        $profile = auth()->user()->profile;
        if (!$profile) {
            return redirect()->route('afad.profile.create')
                ->with('info', 'Complete your profile before managing classes.');
        }
        $classes = $profile->classSessions()->latest()->paginate(10);
        return view('afad.classes.index', compact('classes'));
    }

    public function create(): View|RedirectResponse
    {
        $profile = auth()->user()->profile;
        if (!$profile) {
            return redirect()->route('afad.profile.create');
        }
        return view('afad.classes.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $profile = auth()->user()->profile;
        abort_if(!$profile, 403);

        $data = $request->validate([
            'course_code'      => ['required', 'string', 'max:20'],
            'course_name'      => ['required', 'string', 'max:255'],
            'section'          => ['nullable', 'string', 'max:50'],
            'day'              => ['required', 'string', 'in:Monday,Tuesday,Wednesday,Thursday,Friday,Saturday,Sunday'],
            'start_time'       => ['required', 'date_format:H:i'],
            'end_time'         => ['required', 'date_format:H:i', 'after:start_time'],
            'venue'            => ['nullable', 'string', 'max:255'],
            'semester'         => ['required', 'string', 'max:20'],
            'academic_session' => ['required', 'string', 'max:20'],
            'student_count'    => ['nullable', 'integer', 'min:0'],
            'notes'            => ['nullable', 'string'],
        ]);

        $data['profile_id'] = $profile->id;
        ClassSession::create($data);

        return redirect()->route('afad.classes.index')
            ->with('success', 'Class added successfully.');
    }

    public function show(ClassSession $class): View
    {
        abort_if($class->profile->user_id !== auth()->id(), 403);
        return view('afad.classes.show', compact('class'));
    }

    public function edit(ClassSession $class): View
    {
        abort_if($class->profile->user_id !== auth()->id(), 403);
        return view('afad.classes.edit', compact('class'));
    }

    public function update(Request $request, ClassSession $class): RedirectResponse
    {
        abort_if($class->profile->user_id !== auth()->id(), 403);

        $data = $request->validate([
            'course_code'      => ['required', 'string', 'max:20'],
            'course_name'      => ['required', 'string', 'max:255'],
            'section'          => ['nullable', 'string', 'max:50'],
            'day'              => ['required', 'string', 'in:Monday,Tuesday,Wednesday,Thursday,Friday,Saturday,Sunday'],
            'start_time'       => ['required', 'date_format:H:i'],
            'end_time'         => ['required', 'date_format:H:i', 'after:start_time'],
            'venue'            => ['nullable', 'string', 'max:255'],
            'semester'         => ['required', 'string', 'max:20'],
            'academic_session' => ['required', 'string', 'max:20'],
            'student_count'    => ['nullable', 'integer', 'min:0'],
            'notes'            => ['nullable', 'string'],
        ]);

        $class->update($data);

        return redirect()->route('afad.classes.show', $class)
            ->with('success', 'Class updated successfully.');
    }

    public function destroy(ClassSession $class): RedirectResponse
    {
        abort_if($class->profile->user_id !== auth()->id(), 403);
        $class->delete();

        return redirect()->route('afad.classes.index')
            ->with('success', 'Class removed successfully.');
    }
}
