<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Profile;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProfileController extends Controller
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
        return view('admin.profiles.index', compact('profiles'));
    }

    public function show(Profile $profile): View
    {
        $profile->load(['user', 'certificates', 'verifier', 'appointments', 'claims']);
        return view('admin.profiles.show', compact('profile'));
    }
}
