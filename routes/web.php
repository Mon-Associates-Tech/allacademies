<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AcademicLevelController;
use App\Http\Controllers\AcademicSubjectController;
use App\Http\Controllers\AcademicTopicController;
use App\Http\Controllers\MultipleChoiceQuestionController;
use App\Http\Controllers\SignInController;
use App\Http\Controllers\EssayQuestionController;
use App\Http\Controllers\ExaminationController;
use App\Http\Controllers\TrueOrFalseQuestionController;
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
    // TODO: examination, quizzes
});
