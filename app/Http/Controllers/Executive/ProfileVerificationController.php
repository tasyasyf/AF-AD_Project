<?php

namespace App\Http\Controllers\Executive;

use App\Http\Controllers\Controller;
use App\Models\Certificate;
use App\Models\Profile;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

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
        $profile->load(['user', 'certificates', 'verifier', 'documentsVerifier']);
        return view('executive.profiles.show', compact('profile'));
    }

    public function verify(Profile $profile): RedirectResponse
    {
        abort_if($profile->status === 'verified', 403, 'Profile is already verified.');
        abort_if(!$profile->documents_verified_at, 422, 'Document completeness must be confirmed before verifying this profile.');

        $profile->update([
            'status'      => 'verified',
            'verified_by' => auth()->id(),
            'verified_at' => now(),
            'rejection_reason' => null,
            'rejection_sections' => null,
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

    public function verifyDocuments(Profile $profile): RedirectResponse
    {
        abort_if($profile->status === 'verified', 403, 'Profile is already verified.');

        $hasResume = $profile->resume_path && Storage::disk('local')->exists($profile->resume_path);
        $hasCertificate = $profile->certificates()
            ->whereNotNull('file_path')
            ->exists();

        if (!$hasResume || !$hasCertificate) {
            return back()->with('error', 'Resume / CV and at least one certificate must be uploaded before confirming document completeness.');
        }

        $profile->update([
            'documents_verified_by' => auth()->id(),
            'documents_verified_at' => now(),
        ]);

        return back()->with('success', 'Document completeness confirmed. You may now verify the profile.');
    }

    public function viewResume(Profile $profile): StreamedResponse
    {
        abort_if(!$profile->resume_path, 404);
        abort_if(!Storage::disk('local')->exists($profile->resume_path), 404);

        return Storage::disk('local')->response($profile->resume_path, $profile->resume_original_name);
    }

    public function viewCertificate(Profile $profile, Certificate $certificate): StreamedResponse
    {
        abort_if($certificate->profile_id !== $profile->id, 404);
        abort_if(!$certificate->file_path, 404);
        abort_if(!Storage::disk('local')->exists($certificate->file_path), 404);

        return Storage::disk('local')->response($certificate->file_path, $certificate->file_original_name);
    }

    public function reject(Request $request, Profile $profile): RedirectResponse
    {
        $request->validate([
            'rejection_sections' => ['required', 'array', 'min:1'],
            'rejection_sections.*' => ['string', 'in:personal,qualification,bank,resume,certificates,other'],
            'rejection_reason' => ['required', 'string', 'min:10'],
        ]);

        $profile->update([
            'status'           => 'rejected',
            'rejection_reason' => $request->rejection_reason,
            'rejection_sections' => $request->rejection_sections,
            'verified_by'      => auth()->id(),
            'verified_at'      => now(),
            'documents_verified_by' => null,
            'documents_verified_at' => null,
        ]);

        $profile->certificates()->update([
            'is_verified' => false,
            'verified_by' => null,
            'verified_at' => null,
        ]);

        return redirect()->route('executive.profiles.show', $profile)
            ->with('success', "Profile for {$profile->full_name} has been rejected.");
    }
}
