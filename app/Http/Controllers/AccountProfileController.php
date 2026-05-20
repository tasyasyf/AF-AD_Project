<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class AccountProfileController extends Controller
{
    public function show(): View
    {
        abort_if(auth()->user()->isAdmin(), 404);

        return view('account.profile', ['user' => auth()->user()]);
    }

    public function update(Request $request): RedirectResponse
    {
        $user = $request->user();
        abort_if($user->isAdmin(), 404);

        $data = $request->validate([
            'profile_photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:10240'],
            'remove_profile_photo' => ['nullable', 'boolean'],
        ]);

        if ($request->hasFile('profile_photo')) {
            if ($user->profile_photo_path) {
                Storage::disk('public')->delete($user->profile_photo_path);
            }

            $file = $request->file('profile_photo');
            $user->update([
                'profile_photo_path' => $file->store('profile-photos', 'public'),
                'profile_photo_original_name' => $file->getClientOriginalName(),
            ]);
        } elseif ($request->boolean('remove_profile_photo') && $user->profile_photo_path) {
            Storage::disk('public')->delete($user->profile_photo_path);
            $user->update([
                'profile_photo_path' => null,
                'profile_photo_original_name' => null,
            ]);
        }

        return back()->with('success', 'Profile photo updated successfully.');
    }
}
