<?php

use App\Http\Controllers\Api\AppointmentController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\ClaimController;
use App\Http\Controllers\Api\ExecutiveController;
use App\Http\Controllers\Api\ProfileController;
use Illuminate\Support\Facades\Route;

// ─── Public ──────────────────────────────────────────────────────────────────
Route::post('/login', [AuthController::class, 'login']);

// ─── Authenticated (any role) ─────────────────────────────────────────────────
Route::middleware('auth:sanctum')->group(function () {

    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me',      [AuthController::class, 'me']);

    // ── AF/AD routes ──────────────────────────────────────────────────────────
    Route::middleware('role:afad')->prefix('afad')->group(function () {

        // Profile
        Route::get('/profile',  [ProfileController::class, 'show']);
        Route::post('/profile', [ProfileController::class, 'store']);

        // Appointments (read-only)
        Route::get('/appointments',     [AppointmentController::class, 'index']);
        Route::get('/appointments/{id}',[AppointmentController::class, 'show']);

        // Claims
        Route::get('/claims',           [ClaimController::class, 'index']);
        Route::post('/claims',          [ClaimController::class, 'store']);
        Route::get('/claims/{id}',      [ClaimController::class, 'show']);
        Route::post('/claims/{id}/submit', [ClaimController::class, 'submit']);
    });

    // ── Executive routes ──────────────────────────────────────────────────────
    Route::middleware('role:executive')->prefix('executive')->group(function () {

        // Profiles
        Route::get('/profiles',                       [ExecutiveController::class, 'profiles']);
        Route::post('/profiles/{id}/verify',          [ExecutiveController::class, 'verifyProfile']);
        Route::post('/profiles/{id}/reject',          [ExecutiveController::class, 'rejectProfile']);

        // Claims
        Route::get('/claims',                         [ExecutiveController::class, 'claims']);
        Route::post('/claims/{id}/approve',           [ExecutiveController::class, 'approveClaim']);
        Route::post('/claims/{id}/return',            [ExecutiveController::class, 'returnClaim']);
        Route::post('/claims/{id}/reject',            [ExecutiveController::class, 'rejectClaim']);
    });
});
