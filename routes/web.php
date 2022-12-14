<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TeamController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\SignInController;
use App\Http\Controllers\SignUpController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\SignOutController;
use App\Http\Controllers\SettingsController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ExaminationController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\AcademicGroupController;
use App\Http\Controllers\AcademicLevelController;
use App\Http\Controllers\AcademicTopicController;
use App\Http\Controllers\EssayQuestionController;
use App\Http\Controllers\AcademicSubjectController;
use App\Http\Controllers\TrueOrFalseQuestionController;
use App\Http\Controllers\MultipleChoiceQuestionController;

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
    Route::get('sign-in', [SignInController::class, 'create'])->name('sign-in');
    Route::post('sign-in', [SignInController::class, 'store']);
    Route::get('sign-up', [SignUpController::class, 'create'])->name('sign-up');
    Route::post('sign-up', [SignUpController::class, 'store']);
});

Route::middleware('auth')->group(function () {
    Route::post('sign-out', [SignOutController::class, 'store'])->name('sign-out');
    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::resource('academic-groups', AcademicGroupController::class);
    Route::resource('academic-groups.academic-levels', AcademicLevelController::class)->only(['create', 'store']);
    Route::resource('academic-levels', AcademicLevelController::class)->except(['create', 'store']);
    Route::resource('academic-levels.academic-subjects', AcademicSubjectController::class)->only(['create', 'store']);
    Route::resource('academic-subjects', AcademicSubjectController::class)->except(['create', 'store']);
    Route::resource('academic-subjects.academic-topics', AcademicTopicController::class)->only(['create', 'store']);
    Route::resource('academic-topics', AcademicTopicController::class)->except(['create', 'store']);
    Route::resource('academic-topics.multiple-choice-questions', MultipleChoiceQuestionController::class)->only(['create', 'store']);
    Route::resource('multiple-choice-questions', MultipleChoiceQuestionController::class)->except(['create', 'store']);
    Route::resource('academic-topics.essay-questions', EssayQuestionController::class)->only(['create', 'store']);
    Route::resource('essay-questions', EssayQuestionController::class)->except(['create', 'store']);
    Route::resource('academic-topics.true-or-false-questions', TrueOrFalseQuestionController::class)->only(['create', 'store']);
    Route::resource('true-or-false-questions', TrueOrFalseQuestionController::class)->except(['create', 'store']);
    Route::resource('academic-subjects.examinations', ExaminationController::class)->only(['create', 'store']);
    Route::resource('examinations', ExaminationController::class)->except(['create', 'store']);
    Route::resource('subscriptions', SubscriptionController::class);
    Route::resource('payments', PaymentController::class);
    Route::post('teams/{team}/activate', [TeamController::class, 'activate'])->name('teams.activate');
    Route::resource('teams', TeamController::class);
    Route::resource('teams.members', MemberController::class)->only(['create', 'store', 'destroy']);
    Route::get('settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::match(['GET', 'POST'], 'settings/role', [SettingsController::class, 'role'])->name('settings.role');
    // TODO: examination, quizzes
});
