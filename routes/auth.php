<?php

use App\Http\Controllers\EmailVerificationController;
use App\Http\Controllers\PasswordController;
use App\Http\Controllers\SignInController;
use App\Http\Controllers\SignOutController;
use App\Http\Controllers\SignUpController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
|
| Routes for user authentication including login, registration,
| password management, email verification, and two-factor authentication.
|
*/

// Guest Authentication Routes (only accessible when not logged in)
Route::middleware('guest')->group(function () {
    Route::get('login', [SignInController::class, 'create'])->name('login');
    Route::post('login', [SignInController::class, 'store']);
    Route::get('register', [SignUpController::class, 'create'])->name('register');
    Route::post('register', [SignUpController::class, 'store']);

    // Password Reset Routes
    Route::prefix('password')->name('password.')->group(function () {
        Route::get('forgot', [PasswordController::class, 'forgotForm'])->name('request');
        Route::post('forgot', [PasswordController::class, 'forgot'])->name('email');
        Route::get('reset/{token}', [PasswordController::class, 'resetForm'])->name('reset');
        Route::post('reset', [PasswordController::class, 'reset'])->name('update');
    });
});

// Email Verification Routes
Route::get('verify/email/notice', [EmailVerificationController::class, 'notice'])->name('verification.notice');
Route::post('verify/email/send', [EmailVerificationController::class, 'send'])->middleware('throttle:6,1')->name('verification.send');
Route::get('verify/email/{id}/{hash}', [EmailVerificationController::class, 'verify'])->middleware('signed')->name('verification.verify');

// Two-Factor Authentication Routes
Route::get('2fa/verify', [SignInController::class, 'show2faForm'])->name('2fa.verify');
Route::post('2fa/verify', [SignInController::class, 'verify2fa']);
Route::post('/2fa/resend', [SignInController::class, 'resend2fa'])->name('2fa.resend');

// Authenticated Routes
Route::middleware(['auth'])->group(function () {
    // Sign Out
    Route::post('sign-out', [SignOutController::class, 'store'])->name('logout');

    // Password Change Routes
    Route::middleware('verified')->group(function () {
        Route::get('password/change', [PasswordController::class, 'changeForm'])->name('password.change');
        Route::post('password/change', [PasswordController::class, 'change']);
    });
});
