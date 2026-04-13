<?php

namespace App\Http\Controllers\AfAd;

use App\Http\Controllers\Controller;
use App\Models\Certificate;
use App\Models\Profile;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
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
        $profile->load('certificates');
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
            'date_of_birth'        => ['required', 'date', 'before:today'],
            'gender'               => ['required', 'in:male,female'],
            'qualification'        => ['required', 'string', 'max:255'],
            'qualification_level'  => ['required', 'in:diploma,degree,masters,phd,professional'],
            'specialisation'       => ['nullable', 'string', 'max:255'],
            'area_of_expertise'    => ['nullable', 'string', 'max:255'],
            'bank_name'            => ['required', 'string', 'max:100'],
            'bank_account_number'  => ['required', 'string', 'max:30'],
            'bank_account_holder'  => ['required', 'string', 'max:150'],
            'resume'               => ['nullable', 'file', 'mimes:pdf', 'max:5120'],
            'certificates'         => ['nullable', 'array'],
            'certificates.*.title'               => ['required_with:certificates', 'string', 'max:255'],
            'certificates.*.issuing_institution' => ['required_with:certificates', 'string', 'max:255'],
            'certificates.*.year_obtained'       => ['required_with:certificates', 'integer', 'min:1950', 'max:' . date('Y')],
            'certificates.*.file'                => ['required_with:certificates', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
        ]);

        $data['user_id'] = auth()->id();

        // Handle resume upload
        if ($request->hasFile('resume')) {
            $file = $request->file('resume');
            $data['resume_path']          = $file->store('resumes', 'local');
            $data['resume_original_name'] = $file->getClientOriginalName();
            $data['resume_size']          = $file->getSize();
        }

        $profile = Profile::create(collect($data)->except(['certificates', 'resume'])->toArray());

        // Store uploaded certificates
        if ($request->has('certificates')) {
            foreach ($request->input('certificates') as $index => $certInput) {
                $certData = [
                    'profile_id'          => $profile->id,
                    'title'               => $certInput['title'],
                    'issuing_institution' => $certInput['issuing_institution'],
                    'year_obtained'       => $certInput['year_obtained'],
                ];

                $file = $request->file("certificates.{$index}.file");
                if ($file) {
                    $path = $file->store('certificates', 'local');
                    $certData['file_path']          = $path;
                    $certData['file_original_name'] = $file->getClientOriginalName();
                    $certData['file_mime']          = $file->getMimeType();
                    $certData['file_size']          = $file->getSize();
                }

                Certificate::create($certData);
            }
        }

        return redirect()->route('afad.profile.show')
            ->with('success', 'Profile registered successfully. Awaiting verification.');
    }

    public function edit(): View|RedirectResponse
    {
        $profile = auth()->user()->profile;
        if (!$profile) {
            return redirect()->route('afad.profile.create');
        }
        $profile->load('certificates');
        return view('afad.profile.edit', compact('profile'));
    }

    public function update(Request $request): RedirectResponse
    {
        $profile = auth()->user()->profile;
        abort_if(!$profile, 404);

        $data = $request->validate([
            'full_name'            => ['required', 'string', 'max:150'],
            'ic_number'            => ['required', 'string', 'max:20', "unique:profiles,ic_number,{$profile->id}"],
            'phone'                => ['required', 'string', 'max:20'],
            'address'              => ['required', 'string'],
            'contact_email'        => ['required', 'email', 'max:150'],
            'date_of_birth'        => ['required', 'date', 'before:today'],
            'gender'               => ['required', 'in:male,female'],
            'qualification'        => ['required', 'string', 'max:255'],
            'qualification_level'  => ['required', 'in:diploma,degree,masters,phd,professional'],
            'specialisation'       => ['nullable', 'string', 'max:255'],
            'area_of_expertise'    => ['nullable', 'string', 'max:255'],
            'bank_name'            => ['required', 'string', 'max:100'],
            'bank_account_number'  => ['required', 'string', 'max:30'],
            'bank_account_holder'  => ['required', 'string', 'max:150'],
            'resume'               => ['nullable', 'file', 'mimes:pdf', 'max:5120'],
            'remove_resume'        => ['nullable', 'boolean'],
            'new_certificates'     => ['nullable', 'array'],
            'new_certificates.*.title'               => ['required_with:new_certificates', 'string', 'max:255'],
            'new_certificates.*.issuing_institution' => ['required_with:new_certificates', 'string', 'max:255'],
            'new_certificates.*.year_obtained'       => ['required_with:new_certificates', 'integer', 'min:1950', 'max:' . date('Y')],
            'new_certificates.*.file'                => ['required_with:new_certificates', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
        ]);

        // Handle resume upload or removal
        if ($request->hasFile('resume')) {
            if ($profile->resume_path) {
                Storage::disk('local')->delete($profile->resume_path);
            }
            $file = $request->file('resume');
            $data['resume_path']          = $file->store('resumes', 'local');
            $data['resume_original_name'] = $file->getClientOriginalName();
            $data['resume_size']          = $file->getSize();
        } elseif ($request->boolean('remove_resume') && $profile->resume_path) {
            Storage::disk('local')->delete($profile->resume_path);
            $data['resume_path']          = null;
            $data['resume_original_name'] = null;
            $data['resume_size']          = null;
        }

        $profile->update(collect($data)->except(['new_certificates', 'resume', 'remove_resume'])->toArray() + ['status' => 'pending', 'rejection_reason' => null]);

        // Store new certificates
        if ($request->has('new_certificates')) {
            foreach ($request->input('new_certificates') as $index => $certInput) {
                $certData = [
                    'profile_id'          => $profile->id,
                    'title'               => $certInput['title'],
                    'issuing_institution' => $certInput['issuing_institution'],
                    'year_obtained'       => $certInput['year_obtained'],
                ];

                $file = $request->file("new_certificates.{$index}.file");
                if ($file) {
                    $path = $file->store('certificates', 'local');
                    $certData['file_path']          = $path;
                    $certData['file_original_name'] = $file->getClientOriginalName();
                    $certData['file_mime']          = $file->getMimeType();
                    $certData['file_size']          = $file->getSize();
                }

                Certificate::create($certData);
            }
        }

        return redirect()->route('afad.profile.show')
            ->with('success', 'Profile updated. Awaiting re-verification.');
    }
}
