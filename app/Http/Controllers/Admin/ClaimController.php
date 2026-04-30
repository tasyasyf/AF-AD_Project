<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Claim;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ClaimController extends Controller
{
    public function index(Request $request): View
    {
        $query = Claim::with(['profile.user', 'appointment'])->latest('submitted_at');

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
        $claim->load(['profile.user', 'appointment', 'documents', 'audits.performer', 'reviewer']);
        return view('admin.claims.show', compact('claim'));
    }

    public function edit(Claim $claim): View
    {
        return view('admin.claims.edit', compact('claim'));
    }

    public function update(Request $request, Claim $claim): RedirectResponse
    {
        $data = $request->validate([
            'claim_type'         => ['required', 'in:teaching,marking,module_development,consultation'],
            'period_from'        => ['required', 'date'],
            'period_to'          => ['required', 'date', 'after_or_equal:period_from'],
            'total_hours'        => ['required', 'numeric', 'min:0'],
            'rate_per_hour'      => ['required', 'numeric', 'min:0'],
            'status'             => ['required', 'in:draft,submitted,under_review,approved,returned,rejected'],
            'executive_remarks'  => ['nullable', 'string'],
        ]);

        $data['total_amount'] = $data['total_hours'] * $data['rate_per_hour'];

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
