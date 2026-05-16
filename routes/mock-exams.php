<?php

use App\MockExam\Controllers\MockExamController;
use App\MockExam\Controllers\MockExamGradeScaleController;
use App\MockExam\Controllers\MockExamParticipantController;
use App\MockExam\Controllers\MockExamPdfController;
use App\MockExam\Controllers\MockExamResultController;
use App\MockExam\Controllers\MockExamSubjectExamController;
use App\MockExam\Controllers\MockExamTakingController;
use Illuminate\Support\Facades\Route;

// ─── Instructor routes (authenticated + role enforced in controller) ───────────

Route::middleware(['web', 'auth'])
    ->prefix('mock-exams')
    ->name('mock-exams.')
    ->group(function () {

        // Mock exam CRUD
        Route::get('/',              [MockExamController::class, 'index'])->name('index');
        Route::get('/create',        [MockExamController::class, 'create'])->name('create');
        Route::post('/',             [MockExamController::class, 'store'])->name('store');
        Route::get('/{mockExam}',    [MockExamController::class, 'show'])->name('show');
        Route::get('/{mockExam}/edit',    [MockExamController::class, 'edit'])->name('edit');
        Route::put('/{mockExam}',         [MockExamController::class, 'update'])->name('update');
        Route::delete('/{mockExam}',      [MockExamController::class, 'destroy'])->name('destroy');

        // Subject exams
        Route::prefix('/{mockExam}/subject-exams')->name('subject-exams.')->group(function () {
            Route::get('/create',              [MockExamSubjectExamController::class, 'create'])->name('create');
            Route::post('/',                   [MockExamSubjectExamController::class, 'store'])->name('store');
            Route::get('/{subjectExam}/edit',  [MockExamSubjectExamController::class, 'edit'])->name('edit');
            Route::put('/{subjectExam}',       [MockExamSubjectExamController::class, 'update'])->name('update');
            Route::delete('/{subjectExam}',    [MockExamSubjectExamController::class, 'destroy'])->name('destroy');
        });

        // Configured participants
        Route::prefix('/{mockExam}/participants')->name('participants.')->group(function () {
            Route::post('/',          [MockExamParticipantController::class, 'store'])->name('store');
            Route::post('/import',    [MockExamParticipantController::class, 'import'])->name('import');
            Route::delete('/{participant}', [MockExamParticipantController::class, 'destroy'])->name('destroy');
        });

        // Results / grading
        Route::prefix('/{mockExam}/results')->name('results.')->group(function () {
            Route::get('/',                    [MockExamResultController::class, 'index'])->name('index');
            Route::post('/release',            [MockExamResultController::class, 'release'])->name('release');
            Route::get('/{submission}',        [MockExamResultController::class, 'show'])->name('show');
            Route::post('/{submission}/grade',    [MockExamResultController::class, 'grade'])->name('grade');
            Route::post('/{submission}/finalize', [MockExamResultController::class, 'finalize'])->name('finalize');
        });

        // PDF downloads
        Route::get('/{mockExam}/pdf',            [MockExamPdfController::class, 'examPdf'])->name('pdf.exam');
        Route::get('/{mockExam}/pdf/answer-key', [MockExamPdfController::class, 'answerKeyPdf'])->name('pdf.answer-key');

        // Grade scales (global per instructor)
        Route::prefix('/grade-scales')->name('grade-scales.')->group(function () {
            Route::get('/',                        [MockExamGradeScaleController::class, 'index'])->name('index');
            Route::post('/',                       [MockExamGradeScaleController::class, 'store'])->name('store');
            Route::put('/{gradeScale}',            [MockExamGradeScaleController::class, 'update'])->name('update');
            Route::delete('/{gradeScale}',         [MockExamGradeScaleController::class, 'destroy'])->name('destroy');
            Route::post('/initialize',             [MockExamGradeScaleController::class, 'initialize'])->name('initialize');
        });
    });

// ─── Participant-facing routes (no auth required) ──────────────────────────────

Route::middleware('web')
    ->prefix('take/mock')
    ->name('mock-exams.take.')
    ->group(function () {
        Route::get('/join',                                 [MockExamTakingController::class, 'join'])->name('join');
        Route::post('/authenticate',                        [MockExamTakingController::class, 'authenticate'])->name('authenticate');
        Route::get('/{mockExam}/start',                     [MockExamTakingController::class, 'start'])->name('start');
        Route::get('/{mockExam}/section/{sectionIndex}',    [MockExamTakingController::class, 'section'])->name('section');
        Route::post('/{mockExam}/response',                 [MockExamTakingController::class, 'saveResponse'])->name('response');
        Route::post('/{mockExam}/submit',                   [MockExamTakingController::class, 'submit'])->name('submit');
        Route::get('/{mockExam}/completed',                 [MockExamTakingController::class, 'completed'])->name('completed');
    });
