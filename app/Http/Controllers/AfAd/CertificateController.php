<?php

namespace App\Http\Controllers\AfAd;

use App\Http\Controllers\Controller;
use App\Models\Certificate;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class CertificateController extends Controller
{
    public function index(): View|RedirectResponse
    {
        $profile = auth()->user()->profile;
        if (!$profile) {
            return redirect()->route('afad.profile.create')
                ->with('info', 'Complete your profile before managing certificates.');
        }
        $certificates = $profile->certificates()->latest()->get();
        return view('afad.certificates.index', compact('certificates', 'profile'));
    }

    public function create(): View|RedirectResponse
    {
        $profile = auth()->user()->profile;
        if (!$profile) {
            return redirect()->route('afad.profile.create');
        }
        return view('afad.certificates.create', compact('profile'));
    }

    public function store(Request $request): RedirectResponse
    {
        $profile = auth()->user()->profile;
        abort_if(!$profile, 403);

        $data = $request->validate([
            'title'               => ['required', 'string', 'max:255'],
            'issuing_institution' => ['required', 'string', 'max:255'],
            'year_obtained'       => ['required', 'integer', 'min:1950', 'max:' . date('Y')],
            'certificate_file'    => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
        ]);

        $certData = [
            'profile_id'          => $profile->id,
            'title'               => $data['title'],
            'issuing_institution' => $data['issuing_institution'],
            'year_obtained'       => $data['year_obtained'],
        ];

        if ($request->hasFile('certificate_file')) {
            $file = $request->file('certificate_file');
            $path = $file->store('certificates', 'local');
            $certData['file_path']          = $path;
            $certData['file_original_name'] = $file->getClientOriginalName();
            $certData['file_mime']          = $file->getMimeType();
            $certData['file_size']          = $file->getSize();
        }

        Certificate::create($certData);

        return redirect()->route('afad.certificates.index')
            ->with('success', 'Certificate added successfully.');
    }

    public function destroy(Certificate $certificate): RedirectResponse
    {
        abort_if($certificate->profile->user_id !== auth()->id(), 403);

        if ($certificate->file_path) {
            Storage::disk('local')->delete($certificate->file_path);
        }
        $certificate->delete();

        return redirect()->route('afad.certificates.index')
            ->with('success', 'Certificate removed.');
    }
}
