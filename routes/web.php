<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AcademicLevelController;
use App\Http\Controllers\AcademicSubjectController;
use App\Http\Controllers\AcademicTopicController;
use App\Http\Controllers\SignInController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::redirect('/', 'dashboard');

Route::middleware('guest')->group(function () {
    Route::get('sign-in', [SignInController::class, 'index'])->name('sign-in');
    Route::post('sign-in', [SignInController::class, 'store']);
});

Route::middleware('auth')->group(function () {
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::resource('academic-levels', AcademicLevelController::class);
    Route::resource('academic-subjects', AcademicSubjectController::class);
    Route::resource('academic-topics', AcademicTopicController::class);
});
