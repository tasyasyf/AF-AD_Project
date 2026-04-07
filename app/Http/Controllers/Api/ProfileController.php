<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    /**
     * GET /api/profile
     * Get own profile (AF/AD).
     */
    public function show(Request $request): JsonResponse
    {
        $profile = $request->user()->profile;

        if (!$profile) {
            return response()->json(['message' => 'Profile not registered yet.'], 404);
        }

        return response()->json([
            'id'                  => $profile->id,
            'full_name'           => $profile->full_name,
            'ic_number'           => $profile->ic_number,
            'phone'               => $profile->phone,
            'address'             => $profile->address,
            'contact_email'       => $profile->contact_email,
            'qualification'       => $profile->qualification,
            'qualification_level' => $profile->qualification_level,
            'specialisation'      => $profile->specialisation,
            'bank_name'           => $profile->bank_name,
            'bank_account_number' => $profile->bank_account_number,
            'bank_account_holder' => $profile->bank_account_holder,
            'status'              => $profile->status,
            'rejection_reason'    => $profile->rejection_reason,
            'verified_at'         => $profile->verified_at?->toDateTimeString(),
            'certificates'        => $profile->certificates->map(fn($c) => [
                'id'                  => $c->id,
                'title'               => $c->title,
                'issuing_institution' => $c->issuing_institution,
                'year_obtained'       => $c->year_obtained,
                'is_verified'         => $c->is_verified,
            ]),
        ]);
    }

    /**
     * POST /api/profile
     * Register profile (AF/AD).
     */
    public function store(Request $request): JsonResponse
    {
        if ($request->user()->profile) {
            return response()->json(['message' => 'Profile already exists.'], 409);
        }

        $data = $request->validate([
            'full_name'            => ['required', 'string', 'max:150'],
            'ic_number'            => ['required', 'string', 'max:20', 'unique:profiles,ic_number'],
            'phone'                => ['required', 'string', 'max:20'],
            'address'              => ['required', 'string'],
            'contact_email'        => ['required', 'email', 'max:150'],
            'qualification'        => ['required', 'string', 'max:255'],
            'qualification_level'  => ['required', 'in:diploma,degree,masters,phd,professional'],
            'specialisation'       => ['nullable', 'string', 'max:255'],
            'bank_name'            => ['required', 'string', 'max:100'],
            'bank_account_number'  => ['required', 'string', 'max:30'],
            'bank_account_holder'  => ['required', 'string', 'max:150'],
        ]);

        $profile = $request->user()->profile()->create($data);

        return response()->json([
            'message' => 'Profile registered successfully. Awaiting verification.',
            'profile' => ['id' => $profile->id, 'status' => $profile->status],
        ], 201);
    }
}
