<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * Login and return a Sanctum token.
     *
     * POST /api/login
     * Body: { "email": "...", "password": "...", "device_name": "MyApp" }
     */
    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'email'       => ['required', 'email'],
            'password'    => ['required'],
            'device_name' => ['required', 'string'],
        ]);

        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json([
                'message' => 'Invalid credentials.',
            ], 401);
        }

        $user = Auth::user();

        if (!$user->is_active) {
            Auth::logout();
            return response()->json([
                'message' => 'Account is deactivated.',
            ], 403);
        }

        // Abilities (scopes) based on role
        $abilities = match ($user->role) {
            'afad'      => ['profile:manage', 'claims:manage', 'appointments:read'],
            'executive' => ['profiles:verify', 'appointments:manage', 'claims:review'],
            'admin'     => ['*'],
            default     => [],
        };

        $token = $user->createToken($request->device_name, $abilities)->plainTextToken;

        return response()->json([
            'token'      => $token,
            'token_type' => 'Bearer',
            'user' => [
                'id'   => $user->id,
                'name' => $user->name,
                'email'=> $user->email,
                'role' => $user->role,
            ],
        ]);
    }

    /**
     * Logout (revoke current token).
     *
     * POST /api/logout
     */
    public function logout(Request $request): JsonResponse
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logged out successfully.']);
    }

    /**
     * Get current authenticated user info.
     *
     * GET /api/me
     */
    public function me(Request $request): JsonResponse
    {
        $user    = $request->user();
        $profile = $user->profile;

        return response()->json([
            'id'      => $user->id,
            'name'    => $user->name,
            'email'   => $user->email,
            'role'    => $user->role,
            'profile' => $profile ? [
                'id'                 => $profile->id,
                'full_name'          => $profile->full_name,
                'status'             => $profile->status,
                'qualification'      => $profile->qualification,
                'qualification_level'=> $profile->qualification_level,
            ] : null,
        ]);
    }
}
