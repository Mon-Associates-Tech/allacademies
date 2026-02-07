<?php

use App\Http\Controllers\Lms\CertificateController;
use App\Http\Controllers\Lms\CourseController;
use App\Http\Controllers\Lms\EnrollmentController;
use App\Livewire\Admin\CertificateTemplateSettings;
use App\Livewire\Admin\IdCardTemplateSettings;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| LMS Routes
|--------------------------------------------------------------------------
|
| Routes for the Learning Management System including courses, enrollments,
| certificates, and admin settings for ID cards and certificate templates.
|
*/

Route::middleware(['auth', 'verified'])->group(function () {

    // =========================================================================
    // PUBLIC COURSE ROUTES (for enrolled users and course browsing)
    // =========================================================================
    Route::prefix('courses')->name('lms.courses.')->group(function () {
        // Course listing and browsing
        Route::get('/', [CourseController::class, 'index'])->name('index');
        Route::get('/browse', [CourseController::class, 'browse'])->name('browse');
        Route::get('/{course:slug}', [CourseController::class, 'show'])->name('show');

        // Course enrollment
        Route::post('/{course}/enroll', [EnrollmentController::class, 'enroll'])->name('enroll');
        Route::delete('/{course}/unenroll', [EnrollmentController::class, 'unenroll'])->name('unenroll');

        // Course player (for enrolled users)
        Route::get('/{course:slug}/learn', [CourseController::class, 'learn'])->name('learn');

        // Certificate
        Route::get('/{course}/certificate', [CertificateController::class, 'showCourseCertificate'])->name('certificate');
        Route::get('/{course}/certificate/download', [CertificateController::class, 'downloadCourseCertificate'])->name('certificate.download');
    });

    // =========================================================================
    // MY LEARNING ROUTES (for students)
    // =========================================================================
    Route::prefix('my-learning')->name('my-learning.')->group(function () {
        Route::get('/', [EnrollmentController::class, 'myLearning'])->name('index');
        Route::get('/in-progress', [EnrollmentController::class, 'inProgress'])->name('in-progress');
        Route::get('/completed', [EnrollmentController::class, 'completed'])->name('completed');
        Route::get('/certificates', [CertificateController::class, 'myCertificates'])->name('certificates');
    });

    // =========================================================================
    // COURSE MANAGEMENT ROUTES (for instructors/admins)
    // =========================================================================
    Route::middleware(['can:create,App\Models\Lms\Course'])->prefix('course-management')->name('course-management.')->group(function () {
        // Course listing for management (index route first)
        Route::get('/', [CourseController::class, 'manage'])->name('index');

        // Course builder - static routes before wildcard routes
        Route::get('/create', [CourseController::class, 'create'])->name('create');

        // Wildcard routes - use where constraint to prevent matching "create"
        Route::get('/{course}/edit', [CourseController::class, 'edit'])->name('edit')->where('course', '[0-9]+');
        Route::get('/{course}/analytics', [CourseController::class, 'analytics'])->name('analytics')->where('course', '[0-9]+');
        Route::get('/{course}/enrollments', [EnrollmentController::class, 'courseEnrollments'])->name('enrollments')->where('course', '[0-9]+');
        Route::post('/{course}/publish', [CourseController::class, 'publish'])->name('publish')->where('course', '[0-9]+');
        Route::post('/{course}/unpublish', [CourseController::class, 'unpublish'])->name('unpublish')->where('course', '[0-9]+');
        Route::delete('/{course}', [CourseController::class, 'destroy'])->name('destroy')->where('course', '[0-9]+');
    });

    // =========================================================================
    // ADMIN SETTINGS ROUTES
    // =========================================================================
    Route::middleware([])->prefix('admin/settings')->name('admin.settings.')->group(function () {
        // ID Card Template Settings
        Route::get('/id-card-templates', IdCardTemplateSettings::class)->name('id-card-templates');

        // Certificate Template Settings
        Route::get('/certificate-templates', CertificateTemplateSettings::class)->name('certificate-templates');
    });

    // =========================================================================
    // CERTIFICATE VERIFICATION (public route)
    // =========================================================================
    Route::get('/certificates/verify/{code}', [CertificateController::class, 'verify'])
        ->name('certificates.verify')
        ->withoutMiddleware(['auth', 'verified']);
});
