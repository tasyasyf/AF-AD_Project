<?php

use App\Http\Controllers\Admin;
use App\Http\Controllers\AfAd;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Executive;
use Illuminate\Support\Facades\Route;

// Root redirect
Route::get('/', fn () => redirect()->route('login'));

// Auth routes
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.attempt');
Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register'])->name('register.store');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

// AF/AD routes
Route::prefix('afad')->name('afad.')->middleware(['auth', 'role:afad'])->group(function () {
    Route::get('/dashboard', [AfAd\DashboardController::class, 'index'])->name('dashboard');

    // Profile
    Route::get('/profile', [AfAd\ProfileController::class, 'show'])->name('profile.show');
    Route::get('/profile/create', [AfAd\ProfileController::class, 'create'])->name('profile.create');
    Route::post('/profile', [AfAd\ProfileController::class, 'store'])->name('profile.store');
    Route::get('/profile/edit', [AfAd\ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [AfAd\ProfileController::class, 'update'])->name('profile.update');

    // Certificates
    Route::get('/certificates', [AfAd\CertificateController::class, 'index'])->name('certificates.index');
    Route::get('/certificates/create', [AfAd\CertificateController::class, 'create'])->name('certificates.create');
    Route::post('/certificates', [AfAd\CertificateController::class, 'store'])->name('certificates.store');
    Route::delete('/certificates/{certificate}', [AfAd\CertificateController::class, 'destroy'])->name('certificates.destroy');

    // Classes
    Route::get('/classes', [AfAd\ClassController::class, 'index'])->name('classes.index');
    Route::get('/classes/create', [AfAd\ClassController::class, 'create'])->name('classes.create');
    Route::post('/classes', [AfAd\ClassController::class, 'store'])->name('classes.store');
    Route::get('/classes/{class}', [AfAd\ClassController::class, 'show'])->name('classes.show');
    Route::get('/classes/{class}/edit', [AfAd\ClassController::class, 'edit'])->name('classes.edit');
    Route::put('/classes/{class}', [AfAd\ClassController::class, 'update'])->name('classes.update');
    Route::delete('/classes/{class}', [AfAd\ClassController::class, 'destroy'])->name('classes.destroy');

    // Appointments (read-only)
    Route::get('/appointments', [AfAd\AppointmentController::class, 'index'])->name('appointments.index');
    Route::get('/appointments/{appointment}', [AfAd\AppointmentController::class, 'show'])->name('appointments.show');

    // Claims
    Route::get('/claims', [AfAd\ClaimController::class, 'index'])->name('claims.index');
    Route::get('/claims/create', [AfAd\ClaimController::class, 'create'])->name('claims.create');
    Route::post('/claims', [AfAd\ClaimController::class, 'store'])->name('claims.store');
    Route::get('/claims/{claim}', [AfAd\ClaimController::class, 'show'])->name('claims.show');
    Route::get('/claims/{claim}/edit', [AfAd\ClaimController::class, 'edit'])->name('claims.edit');
    Route::put('/claims/{claim}', [AfAd\ClaimController::class, 'update'])->name('claims.update');
    Route::post('/claims/{claim}/submit', [AfAd\ClaimController::class, 'submit'])->name('claims.submit');
    Route::delete('/claims/{claim}', [AfAd\ClaimController::class, 'destroy'])->name('claims.destroy');

    // Claim documents
    Route::post('/claims/{claim}/documents/{document}/upload', [AfAd\ClaimDocumentController::class, 'upload'])->name('claims.documents.upload');
    Route::delete('/claims/{claim}/documents/{document}', [AfAd\ClaimDocumentController::class, 'remove'])->name('claims.documents.remove');
});

// Executive routes
Route::prefix('executive')->name('executive.')->middleware(['auth', 'role:executive'])->group(function () {
    Route::get('/dashboard', [Executive\DashboardController::class, 'index'])->name('dashboard');

    // Profile verification
    Route::get('/profiles', [Executive\ProfileVerificationController::class, 'index'])->name('profiles.index');
    Route::get('/profiles/{profile}', [Executive\ProfileVerificationController::class, 'show'])->name('profiles.show');
    Route::post('/profiles/{profile}/verify', [Executive\ProfileVerificationController::class, 'verify'])->name('profiles.verify');
    Route::post('/profiles/{profile}/reject', [Executive\ProfileVerificationController::class, 'reject'])->name('profiles.reject');

    // Appointments
    Route::get('/appointments', [Executive\AppointmentController::class, 'index'])->name('appointments.index');
    Route::get('/appointments/create', [Executive\AppointmentController::class, 'create'])->name('appointments.create');
    Route::post('/appointments', [Executive\AppointmentController::class, 'store'])->name('appointments.store');
    Route::get('/appointments/{appointment}', [Executive\AppointmentController::class, 'show'])->name('appointments.show');
    Route::get('/appointments/{appointment}/edit', [Executive\AppointmentController::class, 'edit'])->name('appointments.edit');
    Route::put('/appointments/{appointment}', [Executive\AppointmentController::class, 'update'])->name('appointments.update');

    // Claim review
    Route::get('/claims', [Executive\ClaimReviewController::class, 'index'])->name('claims.index');
    Route::get('/claims/{claim}', [Executive\ClaimReviewController::class, 'show'])->name('claims.show');
    Route::post('/claims/{claim}/approve', [Executive\ClaimReviewController::class, 'approve'])->name('claims.approve');
    Route::post('/claims/{claim}/return', [Executive\ClaimReviewController::class, 'return'])->name('claims.return');
    Route::post('/claims/{claim}/reject', [Executive\ClaimReviewController::class, 'reject'])->name('claims.reject');
});

// Admin routes
Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/dashboard', [Admin\DashboardController::class, 'index'])->name('dashboard');
    Route::get('/profiles', [Admin\ProfileController::class, 'index'])->name('profiles.index');
    Route::get('/profiles/{profile}', [Admin\ProfileController::class, 'show'])->name('profiles.show');
    Route::get('/appointments', [Admin\AppointmentController::class, 'index'])->name('appointments.index');
    Route::get('/appointments/{appointment}', [Admin\AppointmentController::class, 'show'])->name('appointments.show');
    Route::get('/claims', [Admin\ClaimController::class, 'index'])->name('claims.index');
    Route::get('/claims/{claim}', [Admin\ClaimController::class, 'show'])->name('claims.show');
});
