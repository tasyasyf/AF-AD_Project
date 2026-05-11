<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Appointment;
use App\Models\Claim;
use App\Models\ClaimAudit;
use App\Models\Profile;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ClaimController extends Controller
{
    public function index(Request $request): View
    {
        $query = Claim::with(['profile.user', 'appointment', 'pcEndorser'])->latest('submitted_at');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('claim_reference', 'like', "%{$request->search}%")
                  ->orWhereHas('profile', fn($pq) => $pq->where('full_name', 'like', "%{$request->search}%"));
            });
        }

        $claims = $query->paginate(15)->withQueryString();
        return view('admin.claims.index', compact('claims'));
    }

    public function show(Claim $claim): View
    {
        $claim->load(['profile.user', 'appointment', 'documents', 'audits.performer', 'reviewer', 'pcEndorser']);
        return view('admin.claims.show', compact('claim'));
    }

    public function edit(Claim $claim): View
    {
        return view('admin.claims.edit', compact('claim'));
    }

    public function create(): View
    {
        $appointments = Appointment::with('profile')
            ->active()
            ->latest('start_date')
            ->get();

        return view('admin.claims.create', compact('appointments'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'appointment_id' => ['required', 'exists:appointments,id'],
            'claim_type'     => ['required', 'in:teaching,marking,module_development,consultation'],
            'total_hours'    => ['required', 'numeric', 'min:0'],
            'rate_per_hour'  => ['required', 'numeric', 'min:0'],
            'status'         => ['required', 'in:draft,submitted,under_review,approved,returned,rejected'],
            'executive_remarks' => ['nullable', 'string'],
            'has_mark_entry_forms' => ['nullable', 'boolean'],
            'has_graded_scripts' => ['nullable', 'boolean'],
            'has_qa' => ['nullable', 'boolean'],
        ]);

        $appointment = Appointment::with('profile')->findOrFail($data['appointment_id']);

        $claim = Claim::create([
            'appointment_id' => $appointment->id,
            'profile_id' => $appointment->profile_id,
            'claim_type' => $data['claim_type'],
            'period_from' => $appointment->start_date,
            'period_to' => $appointment->end_date,
            'total_hours' => $data['total_hours'],
            'rate_per_hour' => $data['rate_per_hour'],
            'total_amount' => round($data['total_hours'] * $data['rate_per_hour'], 2),
            'status' => $data['status'],
            'submitted_at' => in_array($data['status'], ['submitted', 'under_review', 'approved', 'returned', 'rejected'], true) ? now() : null,
            'reviewed_by' => in_array($data['status'], ['approved', 'returned', 'rejected'], true) ? auth()->id() : null,
            'reviewed_at' => in_array($data['status'], ['approved', 'returned', 'rejected'], true) ? now() : null,
            'executive_remarks' => $data['executive_remarks'] ?? null,
            'has_mark_entry_forms' => $request->boolean('has_mark_entry_forms'),
            'has_graded_scripts' => $request->boolean('has_graded_scripts'),
            'has_qa' => $request->boolean('has_qa'),
        ]);

        ClaimAudit::record($claim, 'created_by_admin', null, $claim->status, 'Created by admin data entry.');

        return redirect()->route('admin.claims.show', $claim)
            ->with('success', 'Claim created successfully.');
    }

    public function update(Request $request, Claim $claim): RedirectResponse
    {
        $data = $request->validate([
            'claim_type'         => ['required', 'in:teaching,marking,module_development,consultation'],
            'total_hours'        => ['required', 'numeric', 'min:0'],
            'rate_per_hour'      => ['required', 'numeric', 'min:0'],
            'status'             => ['required', 'in:draft,submitted,under_review,approved,returned,rejected'],
            'executive_remarks'  => ['nullable', 'string'],
            'has_mark_entry_forms' => ['nullable', 'boolean'],
            'has_graded_scripts' => ['nullable', 'boolean'],
            'has_qa' => ['nullable', 'boolean'],
        ]);

        $data['period_from'] = $claim->appointment->start_date;
        $data['period_to'] = $claim->appointment->end_date;
        $data['total_amount'] = $data['total_hours'] * $data['rate_per_hour'];
        $data['has_mark_entry_forms'] = $request->boolean('has_mark_entry_forms');
        $data['has_graded_scripts'] = $request->boolean('has_graded_scripts');
        $data['has_qa'] = $request->boolean('has_qa');

        $claim->update($data);

        return redirect()->route('admin.claims.show', $claim)
            ->with('success', 'Claim updated successfully.');
    }

    public function destroy(Claim $claim): RedirectResponse
    {
        foreach ($claim->documents as $doc) {
            if ($doc->file_path) {
                Storage::disk('local')->delete($doc->file_path);
            }
        }
        $claim->delete();

        return redirect()->route('admin.claims.index')
            ->with('success', 'Claim deleted successfully.');
    }
}
