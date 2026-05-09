<?php

use App\Http\Controllers\Examinations\DashboardController;
use App\Http\Controllers\Examinations\ExamCreationController;
use App\Http\Controllers\Examinations\ExamTakingController;
use App\Http\Controllers\Examinations\ParticipantController;
use App\Http\Controllers\Examinations\StudentPerformanceController;
use App\Http\Controllers\Examinations\SubmissionController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])->prefix('examinations-hub')->name('examinations-hub.')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/manage', [DashboardController::class, 'manage'])->name('manage');
    Route::get('/create', [ExamCreationController::class, 'create'])->name('create');
    Route::post('/create/preview', [ExamCreationController::class, 'preview'])->name('create.preview');
    Route::post('/create/store', [ExamCreationController::class, 'store'])->name('create.store');
    Route::get('/subscriptions', [DashboardController::class, 'subscriptions'])->name('subscriptions');
    Route::get('/admin', [DashboardController::class, 'admin'])->name('admin');
    Route::get('/exams/{exam}', [DashboardController::class, 'show'])->name('exams.show');
    Route::get('/exams/{exam}/edit', [ExamCreationController::class, 'edit'])->name('exams.edit');
    Route::post('/exams/{exam}/send-invitations', [DashboardController::class, 'sendInvitations'])->name('exams.send-invitations');
    Route::post('/exams/{exam}/send-reminder', [DashboardController::class, 'sendReminder'])->name('exams.send-reminder');
    Route::post('/exams/{exam}/reminder-settings', [DashboardController::class, 'updateReminderSettings'])->name('exams.reminder-settings');

    Route::get('/exams/{exam}/submissions', [SubmissionController::class, 'index'])->name('submissions.index');
    Route::get('/exams/{exam}/submissions/export', [SubmissionController::class, 'export'])->name('submissions.export');
    Route::get('/exams/{exam}/submissions/{submission}', [SubmissionController::class, 'show'])->name('submissions.show');

    Route::get('/performance', [StudentPerformanceController::class, 'index'])->name('performance.index');
    Route::get('/performance/{participantType}/{participantId}', [StudentPerformanceController::class, 'show'])->name('performance.show');
    Route::get('/performance/{participantType}/{participantId}/export', [StudentPerformanceController::class, 'export'])->name('performance.export');

    Route::post('/exams/{exam}/participants/configured', [ParticipantController::class, 'storeConfigured'])->name('participants.configured.store');
    Route::post('/exams/{exam}/participants/configured/import', [ParticipantController::class, 'importConfigured'])->name('participants.configured.import');

    Route::get('/reports', [\App\Http\Controllers\Examinations\PerformanceReportController::class, 'index'])->name('reports.index');
    Route::post('/reports/generate', [\App\Http\Controllers\Examinations\PerformanceReportController::class, 'generate'])->name('reports.generate');

    Route::middleware('can:viewAny,App\Models\GradeScale')->group(function () {
        Route::get('/grading-system', [\App\Http\Controllers\Examinations\GradingSystemController::class, 'index'])->name('grading-system.index');
        Route::post('/grading-system', [\App\Http\Controllers\Examinations\GradingSystemController::class, 'store'])->name('grading-system.store');
        Route::put('/grading-system/{gradeScale}', [\App\Http\Controllers\Examinations\GradingSystemController::class, 'update'])->name('grading-system.update');
        Route::delete('/grading-system/{gradeScale}', [\App\Http\Controllers\Examinations\GradingSystemController::class, 'destroy'])->name('grading-system.destroy');
        Route::post('/grading-system/initialize', [\App\Http\Controllers\Examinations\GradingSystemController::class, 'initializeDefault'])->name('grading-system.initialize');
    });
});

Route::prefix('examinations-hub/take')->name('examinations-hub.take.')->group(function () {
    Route::get('/join', [ExamTakingController::class, 'join'])->name('join');
    Route::post('/authenticate', [ExamTakingController::class, 'authenticate'])->name('authenticate');
    
    Route::middleware(\App\Http\Middleware\EnsureExamSession::class)->group(function () {
        Route::get('/{exam}/start', [ExamTakingController::class, 'start'])->name('start');
        Route::get('/{exam}/section/{sectionIndex}', [ExamTakingController::class, 'section'])->name('section');
        Route::post('/{exam}/save-response', [ExamTakingController::class, 'saveResponse'])->name('save-response');
        Route::post('/{exam}/submit', [ExamTakingController::class, 'submit'])->name('submit');
    });
    
    Route::get('/{exam}/completed', [ExamTakingController::class, 'completed'])->name('completed');
});

Route::get('/examinations-hub/join', [ParticipantController::class, 'joinEntry'])->name('examinations-hub.join.entry');
Route::post('/examinations-hub/join', [ParticipantController::class, 'joinLookup'])->name('examinations-hub.join.lookup');
Route::get('/examinations-hub/join/{code}', [ParticipantController::class, 'joinForm'])->name('examinations-hub.join');
Route::post('/examinations-hub/join/{code}', [ParticipantController::class, 'attemptJoin'])->name('examinations-hub.join.attempt');

Route::prefix('examinations-hub/results')->name('examinations-hub.results.')->group(function () {
    Route::get('/', [\App\Http\Controllers\Examinations\ParticipantResultsController::class, 'index'])->name('index');
    Route::get('/{submission}', [\App\Http\Controllers\Examinations\ParticipantResultsController::class, 'show'])->name('show');
});
