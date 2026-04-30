<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Profile;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function index(Request $request): View
    {
        $query = Profile::with('user')->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('full_name', 'like', "%{$request->search}%")
                  ->orWhere('ic_number', 'like', "%{$request->search}%");
            });
        }

        $profiles = $query->paginate(15)->withQueryString();
        return view('admin.profiles.index', compact('profiles'));
    }

    public function show(Profile $profile): View
    {
        $profile->load(['user', 'certificates', 'verifier', 'appointments', 'claims']);
        return view('admin.profiles.show', compact('profile'));
    }

    public function edit(Profile $profile): View
    {
        return view('admin.profiles.edit', compact('profile'));
    }

    public function update(Request $request, Profile $profile): RedirectResponse
    {
        $data = $request->validate([
            'full_name'            => ['required', 'string', 'max:150'],
            'ic_number'            => ['required', 'string', 'max:20', "unique:profiles,ic_number,{$profile->id}"],
            'phone'                => ['required', 'string', 'max:20'],
            'address'              => ['required', 'string'],
            'contact_email'        => ['required', 'email', 'max:150'],
            'qualification'        => ['required', 'string', 'max:255'],
            'qualification_level'  => ['required', 'in:diploma,degree,masters,phd,professional'],
            'specialisation'       => ['nullable', 'string', 'max:255'],
            'bank_name'            => ['required', 'string', 'max:100'],
            'bank_account_number'  => ['required', 'string', 'max:30'],
            'bank_account_holder'  => ['required', 'string', 'max:150'],
            'status'               => ['required', 'in:pending,verified,rejected'],
        ]);

        $profile->update($data);

        return redirect()->route('admin.profiles.show', $profile)
            ->with('success', 'Profile updated successfully.');
    }

    public function destroy(Profile $profile): RedirectResponse
    {
        if ($profile->resume_path) {
            Storage::disk('local')->delete($profile->resume_path);
        }
        foreach ($profile->certificates as $cert) {
            if ($cert->file_path) {
                Storage::disk('local')->delete($cert->file_path);
            }
        }
        $profile->delete();

        return redirect()->route('admin.profiles.index')
            ->with('success', 'Profile deleted successfully.');
    }
}
