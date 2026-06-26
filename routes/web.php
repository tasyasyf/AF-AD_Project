<?php

use App\Http\Controllers\Admin;
use App\Http\Controllers\AccountProfileController;
use App\Http\Controllers\AfAd;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Executive;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ProfilePhotoController;
use App\Http\Controllers\ProgramCoordinator;
use Illuminate\Support\Facades\Route;

// Root redirect
Route::get('/', fn () => redirect()->route('login'));

// Auth routes jahkdsidskjwennkdjskndkslmxlfskorejs]]fdjkfdk kdiwejrjksdsmsd
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.attempt');
Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register'])->name('register.store');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');
Route::get('/profile-photo/{user}', [ProfilePhotoController::class, 'show'])->name('profile-photo.show')->middleware('auth');

// Notifications (all authenticated roles)
Route::middleware('auth')->group(function () {
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/notifications/{id}/read', [NotificationController::class, 'read'])->name('notifications.read');
    Route::post('/notifications/read-all', [NotificationController::class, 'readAll'])->name('notifications.read-all');
});

// AF/AD routes
Route::prefix('afad')->name('afad.')->middleware(['auth', 'role:afad'])->group(function () {
    Route::get('/dashboard', [AfAd\DashboardController::class, 'index'])->name('dashboard');

    // Profile
    Route::get('/profile', [AfAd\ProfileController::class, 'show'])->name('profile.show');
    Route::get('/profile/create', [AfAd\ProfileController::class, 'create'])->name('profile.create');
    Route::post('/profile', [AfAd\ProfileController::class, 'store'])->name('profile.store');
    Route::get('/profile/edit', [AfAd\ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [AfAd\ProfileController::class, 'update'])->name('profile.update');

    Route::middleware('afad.profile.complete')->group(function () {
        // Certificates
        Route::get('/certificates', [AfAd\CertificateController::class, 'index'])->name('certificates.index');
        Route::get('/certificates/create', [AfAd\CertificateController::class, 'create'])->name('certificates.create');
        Route::post('/certificates', [AfAd\CertificateController::class, 'store'])->name('certificates.store');
        Route::delete('/certificates/{certificate}', [AfAd\CertificateController::class, 'destroy'])->name('certificates.destroy');

        // Appointments (read-only)
        Route::get('/appointments', [AfAd\AppointmentController::class, 'index'])->name('appointments.index');
        Route::get('/appointments/{appointment}', [AfAd\AppointmentController::class, 'show'])->name('appointments.show');

        Route::middleware('afad.profile.verified')->group(function () {
            // Classes
            Route::get('/classes', [AfAd\ClassController::class, 'index'])->name('classes.index');
            Route::get('/classes/create', [AfAd\ClassController::class, 'create'])->name('classes.create');
            Route::post('/classes', [AfAd\ClassController::class, 'store'])->name('classes.store');
            Route::get('/classes/{class}', [AfAd\ClassController::class, 'show'])->name('classes.show');
            Route::get('/classes/{class}/edit', [AfAd\ClassController::class, 'edit'])->name('classes.edit');
            Route::put('/classes/{class}', [AfAd\ClassController::class, 'update'])->name('classes.update');
            Route::delete('/classes/{class}', [AfAd\ClassController::class, 'destroy'])->name('classes.destroy');

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

            // Submissions (video recording link uploads)
            Route::get('/submissions', [AfAd\SubmissionController::class, 'index'])->name('submissions.index');
            Route::get('/submissions/create', [AfAd\SubmissionController::class, 'create'])->name('submissions.create');
            Route::post('/submissions', [AfAd\SubmissionController::class, 'store'])->name('submissions.store');
            Route::get('/submissions/{submission}', [AfAd\SubmissionController::class, 'show'])->name('submissions.show');
            Route::post('/submissions/{submission}/duration', [AfAd\SubmissionController::class, 'updateVideoDuration'])->name('submissions.duration');
            Route::get('/submissions/{submission}/download', [AfAd\SubmissionController::class, 'download'])->name('submissions.download');
            Route::delete('/submissions/{submission}', [AfAd\SubmissionController::class, 'destroy'])->name('submissions.destroy');
        });
    });
});

// Executive routes
Route::prefix('executive')->name('executive.')->middleware(['auth', 'role:executive'])->group(function () {
    Route::get('/dashboard', [Executive\DashboardController::class, 'index'])->name('dashboard');
    Route::get('/profile', [AccountProfileController::class, 'show'])->name('profile.show');
    Route::put('/profile', [AccountProfileController::class, 'update'])->name('profile.update');

    // Profile verification
    Route::get('/profiles', [Executive\ProfileVerificationController::class, 'index'])->name('profiles.index');
    Route::get('/profiles/{profile}', [Executive\ProfileVerificationController::class, 'show'])->name('profiles.show');
    Route::get('/profiles/{profile}/resume/view', [Executive\ProfileVerificationController::class, 'viewResume'])->name('profiles.resume.view');
    Route::get('/profiles/{profile}/certificates/{certificate}/view', [Executive\ProfileVerificationController::class, 'viewCertificate'])->name('profiles.certificates.view');
    Route::post('/profiles/{profile}/documents/verify', [Executive\ProfileVerificationController::class, 'verifyDocuments'])->name('profiles.documents.verify');
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

    // Submissions (review video recording uploads)
    Route::get('/submissions', [Executive\SubmissionController::class, 'index'])->name('submissions.index');
    Route::get('/submissions/{submission}', [Executive\SubmissionController::class, 'show'])->name('submissions.show');
    Route::get('/submissions/{submission}/download', [Executive\SubmissionController::class, 'download'])->name('submissions.download');
    Route::post('/submissions/{submission}/review', [Executive\SubmissionController::class, 'review'])->name('submissions.review');
});

// Program Coordinator routes
Route::prefix('pc')->name('pc.')->middleware(['auth', 'role:pc'])->group(function () {
    Route::get('/dashboard', [ProgramCoordinator\DashboardController::class, 'index'])->name('dashboard');
    Route::get('/profile', [AccountProfileController::class, 'show'])->name('profile.show');
    Route::put('/profile', [AccountProfileController::class, 'update'])->name('profile.update');

    Route::get('/afad', [ProgramCoordinator\AfAdController::class, 'index'])->name('afad.index');
    Route::get('/afad/{profile}', [ProgramCoordinator\AfAdController::class, 'show'])->name('afad.show');

    Route::get('/appointments', [ProgramCoordinator\AppointmentController::class, 'index'])->name('appointments.index');
    Route::get('/appointments/create', [ProgramCoordinator\AppointmentController::class, 'create'])->name('appointments.create');
    Route::post('/appointments', [ProgramCoordinator\AppointmentController::class, 'store'])->name('appointments.store');
    Route::get('/nomination', [ProgramCoordinator\NominationController::class, 'index'])->name('nomination.index');
    Route::get('/document-checklist', [ProgramCoordinator\DocumentChecklistController::class, 'index'])->name('document-checklist.index');
    Route::get('/document-checklist/submissions/{submission}/view', [ProgramCoordinator\DocumentChecklistController::class, 'viewSubmission'])->name('document-checklist.submissions.view');
    Route::get('/document-checklist/claim-documents/{document}/view', [ProgramCoordinator\DocumentChecklistController::class, 'viewClaimDocument'])->name('document-checklist.claim-documents.view');
    Route::post('/document-checklist/qbas/{submission}/confirm', [ProgramCoordinator\DocumentChecklistController::class, 'confirmQbAs'])->name('document-checklist.qbas.confirm');
    Route::post('/document-checklist/qbas/{submission}/reject', [ProgramCoordinator\DocumentChecklistController::class, 'rejectQbAs'])->name('document-checklist.qbas.reject');
    Route::get('/claims', [ProgramCoordinator\ClaimReviewController::class, 'index'])->name('claims.index');
    Route::get('/claims/{claim}', [ProgramCoordinator\ClaimReviewController::class, 'show'])->name('claims.show');
    Route::post('/claims/{claim}/endorse', [ProgramCoordinator\ClaimReviewController::class, 'endorse'])->name('claims.endorse');
    Route::post('/claims/{claim}/return', [ProgramCoordinator\ClaimReviewController::class, 'return'])->name('claims.return');
    Route::get('/reports', [ProgramCoordinator\ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/export', [ProgramCoordinator\ReportController::class, 'export'])->name('reports.export');
});

// Admin routes
Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/dashboard', [Admin\DashboardController::class, 'index'])->name('dashboard');

    // Users / roles
    Route::get('/users', [Admin\UserController::class, 'index'])->name('users.index');
    Route::get('/users/create', [Admin\UserController::class, 'create'])->name('users.create');
    Route::post('/users', [Admin\UserController::class, 'store'])->name('users.store');
    Route::get('/users/{user}/edit', [Admin\UserController::class, 'edit'])->name('users.edit');
    Route::put('/users/{user}', [Admin\UserController::class, 'update'])->name('users.update');
    Route::delete('/users/{user}', [Admin\UserController::class, 'destroy'])->name('users.destroy');

    // Profiles
    Route::get('/profiles', [Admin\ProfileController::class, 'index'])->name('profiles.index');
    Route::get('/profiles/create', [Admin\ProfileController::class, 'create'])->name('profiles.create');
    Route::post('/profiles', [Admin\ProfileController::class, 'store'])->name('profiles.store');
    Route::get('/profiles/{profile}', [Admin\ProfileController::class, 'show'])->name('profiles.show');
    Route::get('/profiles/{profile}/edit', [Admin\ProfileController::class, 'edit'])->name('profiles.edit');
    Route::put('/profiles/{profile}', [Admin\ProfileController::class, 'update'])->name('profiles.update');
    Route::delete('/profiles/{profile}', [Admin\ProfileController::class, 'destroy'])->name('profiles.destroy');

    // Appointments
    Route::get('/appointments', [Admin\AppointmentController::class, 'index'])->name('appointments.index');
    Route::get('/appointments/create', [Admin\AppointmentController::class, 'create'])->name('appointments.create');
    Route::post('/appointments', [Admin\AppointmentController::class, 'store'])->name('appointments.store');
    Route::get('/appointments/{appointment}', [Admin\AppointmentController::class, 'show'])->name('appointments.show');
    Route::get('/appointments/{appointment}/edit', [Admin\AppointmentController::class, 'edit'])->name('appointments.edit');
    Route::put('/appointments/{appointment}', [Admin\AppointmentController::class, 'update'])->name('appointments.update');
    Route::delete('/appointments/{appointment}', [Admin\AppointmentController::class, 'destroy'])->name('appointments.destroy');

    // Classes
    Route::get('/classes', [Admin\ClassController::class, 'index'])->name('classes.index');
    Route::get('/classes/create', [Admin\ClassController::class, 'create'])->name('classes.create');
    Route::post('/classes', [Admin\ClassController::class, 'store'])->name('classes.store');
    Route::get('/classes/{class}', [Admin\ClassController::class, 'show'])->name('classes.show');
    Route::get('/classes/{class}/edit', [Admin\ClassController::class, 'edit'])->name('classes.edit');
    Route::put('/classes/{class}', [Admin\ClassController::class, 'update'])->name('classes.update');
    Route::delete('/classes/{class}', [Admin\ClassController::class, 'destroy'])->name('classes.destroy');

    // Certificates
    Route::get('/certificates', [Admin\CertificateController::class, 'index'])->name('certificates.index');
    Route::get('/certificates/create', [Admin\CertificateController::class, 'create'])->name('certificates.create');
    Route::post('/certificates', [Admin\CertificateController::class, 'store'])->name('certificates.store');
    Route::get('/certificates/{certificate}/edit', [Admin\CertificateController::class, 'edit'])->name('certificates.edit');
    Route::put('/certificates/{certificate}', [Admin\CertificateController::class, 'update'])->name('certificates.update');
    Route::delete('/certificates/{certificate}', [Admin\CertificateController::class, 'destroy'])->name('certificates.destroy');

    // Submissions
    Route::get('/submissions', [Admin\SubmissionController::class, 'index'])->name('submissions.index');
    Route::get('/submissions/create', [Admin\SubmissionController::class, 'create'])->name('submissions.create');
    Route::post('/submissions', [Admin\SubmissionController::class, 'store'])->name('submissions.store');
    Route::get('/submissions/{submission}', [Admin\SubmissionController::class, 'show'])->name('submissions.show');
    Route::get('/submissions/{submission}/edit', [Admin\SubmissionController::class, 'edit'])->name('submissions.edit');
    Route::put('/submissions/{submission}', [Admin\SubmissionController::class, 'update'])->name('submissions.update');
    Route::get('/submissions/{submission}/download', [Admin\SubmissionController::class, 'download'])->name('submissions.download');
    Route::delete('/submissions/{submission}', [Admin\SubmissionController::class, 'destroy'])->name('submissions.destroy');

    // Claims
    Route::get('/claims', [Admin\ClaimController::class, 'index'])->name('claims.index');
    Route::get('/claims/create', [Admin\ClaimController::class, 'create'])->name('claims.create');
    Route::post('/claims', [Admin\ClaimController::class, 'store'])->name('claims.store');
    Route::get('/claims/{claim}', [Admin\ClaimController::class, 'show'])->name('claims.show');
    Route::get('/claims/{claim}/edit', [Admin\ClaimController::class, 'edit'])->name('claims.edit');
    Route::put('/claims/{claim}', [Admin\ClaimController::class, 'update'])->name('claims.update');
    Route::delete('/claims/{claim}', [Admin\ClaimController::class, 'destroy'])->name('claims.destroy');
});
