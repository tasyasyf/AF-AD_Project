<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Claim;
use Illuminate\Http\Request;
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
}
