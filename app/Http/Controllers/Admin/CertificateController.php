<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Certificate;
use App\Models\Profile;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class CertificateController extends Controller
{
    public function index(Request $request): View
    {
        $query = Certificate::with('profile.user')->latest();

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('title', 'like', "%{$request->search}%")
                    ->orWhereHas('profile', fn($pq) => $pq->where('full_name', 'like', "%{$request->search}%"));
            });
        }

        $certificates = $query->paginate(15)->withQueryString();
        return view('admin.certificates.index', compact('certificates'));
    }

    public function create(): View
    {
        $profiles = Profile::with('user')->orderBy('full_name')->get();
        return view('admin.certificates.create', compact('profiles'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $this->validatedData($request);
        $data = $this->attachFileData($request, $data);
        $data['is_verified'] = $request->boolean('is_verified');
        $data['verified_by'] = $data['is_verified'] ? auth()->id() : null;
        $data['verified_at'] = $data['is_verified'] ? now() : null;

        Certificate::create($data);

        return redirect()->route('admin.certificates.index')
            ->with('success', 'Certificate created successfully.');
    }

    public function edit(Certificate $certificate): View
    {
        $profiles = Profile::with('user')->orderBy('full_name')->get();
        return view('admin.certificates.edit', compact('certificate', 'profiles'));
    }

    public function update(Request $request, Certificate $certificate): RedirectResponse
    {
        $data = $this->validatedData($request);
        $data['is_verified'] = $request->boolean('is_verified');
        $data['verified_by'] = $data['is_verified'] ? auth()->id() : null;
        $data['verified_at'] = $data['is_verified'] ? now() : null;

        if ($request->hasFile('certificate_file')) {
            Storage::disk('local')->delete($certificate->file_path);
            $data = $this->attachFileData($request, $data);
        }

        $certificate->update($data);

        return redirect()->route('admin.certificates.index')
            ->with('success', 'Certificate updated successfully.');
    }

    public function destroy(Certificate $certificate): RedirectResponse
    {
        Storage::disk('local')->delete($certificate->file_path);
        $certificate->delete();

        return redirect()->route('admin.certificates.index')
            ->with('success', 'Certificate deleted successfully.');
    }

    private function validatedData(Request $request): array
    {
        return $request->validate([
            'profile_id' => ['required', 'exists:profiles,id'],
            'title' => ['required', 'string', 'max:255'],
            'issuing_institution' => ['required', 'string', 'max:255'],
            'year_obtained' => ['required', 'integer', 'min:1950', 'max:' . date('Y')],
            'certificate_file' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
            'is_verified' => ['nullable', 'boolean'],
        ]);
    }

    private function attachFileData(Request $request, array $data): array
    {
        if (!$request->hasFile('certificate_file')) {
            return $data;
        }

        $file = $request->file('certificate_file');
        $data['file_path'] = $file->store('certificates', 'local');
        $data['file_original_name'] = $file->getClientOriginalName();
        $data['file_mime'] = $file->getMimeType();
        $data['file_size'] = $file->getSize();

        return $data;
    }
}
