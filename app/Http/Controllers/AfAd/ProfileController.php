<?php

namespace App\Http\Controllers\AfAd;

use App\Http\Controllers\Controller;
use App\Models\Profile;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function show(): View|RedirectResponse
    {
        $profile = auth()->user()->profile;
        if (!$profile) {
            return redirect()->route('afad.profile.create')
                ->with('info', 'Please complete your profile registration first.');
        }
        return view('afad.profile.show', compact('profile'));
    }

    public function create(): View|RedirectResponse
    {
        if (auth()->user()->profile) {
            return redirect()->route('afad.profile.show');
        }
        return view('afad.profile.create');
    }

    public function store(Request $request): RedirectResponse
    {
        if (auth()->user()->profile) {
            return redirect()->route('afad.profile.show');
        }

        $data = $request->validate([
            'full_name'            => ['required', 'string', 'max:150'],
            'ic_number'            => ['required', 'string', 'max:20', 'unique:profiles,ic_number'],
            'phone'                => ['required', 'string', 'max:20'],
            'address'              => ['required', 'string'],
            'contact_email'        => ['required', 'email', 'max:150'],
            'qualification'        => ['required', 'string', 'max:255'],
            'qualification_level'  => ['required', 'in:diploma,degree,masters,phd,professional'],
            'specialisation'       => ['nullable', 'string', 'max:255'],
            'bank_name'            => ['required', 'string', 'max:100'],
            'bank_account_number'  => ['required', 'string', 'max:30'],
            'bank_account_holder'  => ['required', 'string', 'max:150'],
        ]);

        $data['user_id'] = auth()->id();
        Profile::create($data);

        return redirect()->route('afad.profile.show')
            ->with('success', 'Profile registered successfully. Awaiting verification.');
    }

    public function edit(): View|RedirectResponse
    {
        $profile = auth()->user()->profile;
        if (!$profile) {
            return redirect()->route('afad.profile.create');
        }
        if (!in_array($profile->status, ['pending', 'rejected'])) {
            return redirect()->route('afad.profile.show')
                ->with('error', 'Verified profiles cannot be edited. Contact the School Executive.');
        }
        return view('afad.profile.edit', compact('profile'));
    }

    public function update(Request $request): RedirectResponse
    {
        $profile = auth()->user()->profile;
        abort_if(!$profile, 404);
        abort_if(!in_array($profile->status, ['pending', 'rejected']), 403, 'Profile cannot be edited in its current state.');

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
        ]);

        $profile->update($data + ['status' => 'pending', 'rejection_reason' => null]);

        return redirect()->route('afad.profile.show')
            ->with('success', 'Profile updated. Awaiting re-verification.');
    }
}
