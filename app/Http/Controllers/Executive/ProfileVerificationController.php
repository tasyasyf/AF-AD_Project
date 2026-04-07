<?php

namespace App\Http\Controllers\Executive;

use App\Http\Controllers\Controller;
use App\Models\Profile;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProfileVerificationController extends Controller
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
        return view('executive.profiles.index', compact('profiles'));
    }

    public function show(Profile $profile): View
    {
        $profile->load(['user', 'certificates', 'verifier']);
        return view('executive.profiles.show', compact('profile'));
    }

    public function verify(Profile $profile): RedirectResponse
    {
        abort_if($profile->status === 'verified', 403, 'Profile is already verified.');

        $profile->update([
            'status'      => 'verified',
            'verified_by' => auth()->id(),
            'verified_at' => now(),
            'rejection_reason' => null,
        ]);

        // Also mark all certificates as verified
        $profile->certificates()->update([
            'is_verified' => true,
            'verified_by' => auth()->id(),
            'verified_at' => now(),
        ]);

        return redirect()->route('executive.profiles.show', $profile)
            ->with('success', "Profile for {$profile->full_name} has been verified.");
    }

    public function reject(Request $request, Profile $profile): RedirectResponse
    {
        $request->validate([
            'rejection_reason' => ['required', 'string', 'min:10'],
        ]);

        $profile->update([
            'status'           => 'rejected',
            'rejection_reason' => $request->rejection_reason,
            'verified_by'      => auth()->id(),
            'verified_at'      => now(),
        ]);

        return redirect()->route('executive.profiles.show', $profile)
            ->with('success', "Profile for {$profile->full_name} has been rejected.");
    }
}
