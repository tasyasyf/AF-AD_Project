<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ClassSession;
use App\Models\Profile;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ClassController extends Controller
{
    public function index(Request $request): View
    {
        $query = ClassSession::with('profile.user')->latest();

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('course_code', 'like', "%{$request->search}%")
                    ->orWhere('course_name', 'like', "%{$request->search}%")
                    ->orWhereHas('profile', fn($pq) => $pq->where('full_name', 'like', "%{$request->search}%"));
            });
        }

        $classes = $query->paginate(15)->withQueryString();
        return view('admin.classes.index', compact('classes'));
    }

    public function create(): View
    {
        $profiles = Profile::verified()->with('user')->orderBy('full_name')->get();
        return view('admin.classes.create', compact('profiles'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validatedData($request);
        ClassSession::create($data);

        return redirect()->route('admin.classes.index')
            ->with('success', 'Class created successfully.');
    }

    public function show(ClassSession $class): View
    {
        $class->load('profile.user');
        return view('admin.classes.show', compact('class'));
    }

    public function edit(ClassSession $class): View
    {
        $profiles = Profile::verified()->with('user')->orderBy('full_name')->get();
        return view('admin.classes.edit', compact('class', 'profiles'));
    }

    public function update(Request $request, ClassSession $class): RedirectResponse
    {
        $class->update($this->validatedData($request));

        return redirect()->route('admin.classes.show', $class)
            ->with('success', 'Class updated successfully.');
    }

    public function destroy(ClassSession $class): RedirectResponse
    {
        $class->delete();

        return redirect()->route('admin.classes.index')
            ->with('success', 'Class deleted successfully.');
    }

    private function validatedData(Request $request): array
    {
        return $request->validate([
            'profile_id' => ['required', 'exists:profiles,id'],
            'course_code' => ['required', 'string', 'in:CRM300,CSC400,CIT400'],
            'course_name' => ['required', 'string', 'in:Industrial Training,Customer Relationship Management,Software Construction'],
            'section' => ['nullable', 'string', 'max:50'],
            'day' => ['required', 'string', 'in:Monday,Tuesday,Wednesday,Thursday,Friday,Saturday,Sunday'],
            'start_time' => ['required', 'date_format:H:i'],
            'end_time' => ['required', 'date_format:H:i', 'after:start_time'],
            'venue' => ['nullable', 'string', 'max:255'],
            'semester' => ['required', 'string', 'in:January,May,September'],
            'academic_session' => ['required', 'string', 'max:20'],
            'student_count' => ['nullable', 'integer', 'min:0'],
            'notes' => ['nullable', 'string'],
        ]);
    }
}
