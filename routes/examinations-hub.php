<?php

use App\ExaminationHub\Controllers\DashboardController;
use App\ExaminationHub\Controllers\ExamCreationController;
use App\ExaminationHub\Controllers\ExamTakingController;
use App\ExaminationHub\Controllers\GradingSystemController;
use App\ExaminationHub\Controllers\ParticipantController;
use App\ExaminationHub\Controllers\ParticipantResultsController;
use App\ExaminationHub\Controllers\PerformanceReportController;
use App\ExaminationHub\Controllers\StudentPerformanceController;
use App\ExaminationHub\Controllers\SubmissionController;
use App\ExaminationHub\Controllers\ProctoringController;
use App\Http\Middleware\EnsureExamSession;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])->prefix('examinations')->name('examination-hub.')->group(function () {
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
    Route::post('/exams/{exam}/proctoring-settings', [DashboardController::class, 'updateProctoringSettings'])->name('exams.proctoring-settings');
    Route::post('/exams/{exam}/toggle-results', [DashboardController::class, 'toggleResults'])->name('exams.toggle-results');

    Route::get('/exams/{exam}/submissions', [SubmissionController::class, 'index'])->name('submissions.index');
    Route::get('/exams/{exam}/submissions/export', [SubmissionController::class, 'export'])->name('submissions.export');
    Route::get('/exams/{exam}/submissions/{submission}', [SubmissionController::class, 'show'])->name('submissions.show');
    Route::get('/exams/{exam}/submissions/export-excel', [SubmissionController::class, 'exportExcel'])->name('submissions.export-excel');


    Route::get('/performance', [StudentPerformanceController::class, 'index'])->name('performance.index');
    Route::get('/performance/{participantType}/{participantId}', [StudentPerformanceController::class, 'show'])->name('performance.show');
    Route::get('/performance/{participantType}/{participantId}/export', [StudentPerformanceController::class, 'export'])->name('performance.export');

    Route::post('/exams/{exam}/participants/configured', [ParticipantController::class, 'storeConfigured'])->name('participants.configured.store');
    Route::post('/exams/{exam}/participants/configured/import', [ParticipantController::class, 'importConfigured'])->name('participants.configured.import');

    Route::get('/reports', [PerformanceReportController::class, 'index'])->name('reports.index');
    Route::post('/reports/generate', [PerformanceReportController::class, 'generate'])->name('reports.generate');

    // ── Proctoring (admin review) ─────────────────────────────────────────────
    Route::get('/exams/{exam}/proctoring', [ProctoringController::class, 'index'])->name('proctoring.index');
    Route::get('/exams/{exam}/submissions/{submission}/proctoring', [ProctoringController::class, 'show'])->name('proctoring.show');


    Route::middleware('can:viewAny,App\ExaminationHub\Models\GradeScale')->group(function () {
        Route::get('/grading-system', [GradingSystemController::class, 'index'])->name('grading-system.index');
        Route::post('/grading-system', [GradingSystemController::class, 'store'])->name('grading-system.store');
        Route::put('/grading-system/{gradeScale}', [GradingSystemController::class, 'update'])->name('grading-system.update');
        Route::delete('/grading-system/{gradeScale}', [GradingSystemController::class, 'destroy'])->name('grading-system.destroy');
        Route::post('/grading-system/initialize', [GradingSystemController::class, 'initializeDefault'])->name('grading-system.initialize');
    });

    // Proctoring event ingestion (JSON, called by exam-proctor.js)
    Route::post('/{exam}/proctor/event', [ProctoringController::class, 'storeEvent'])->name('proctor.event');
});

Route::prefix('examinations-hub/take')->name('examination-hub.take.')->group(function () {
    Route::get('/join', [ExamTakingController::class, 'join'])->name('join');
    Route::post('/authenticate', [ExamTakingController::class, 'authenticate'])->name('authenticate');

    Route::middleware([EnsureExamSession::class, 'proctor.exam:exam'])->group(function () {
        Route::get('/{exam}/start', [ExamTakingController::class, 'start'])->name('start');
        Route::get('/{exam}/section/{sectionIndex}', [ExamTakingController::class, 'section'])->name('section');
        Route::post('/{exam}/save-response', [ExamTakingController::class, 'saveResponse'])->name('save-response');
        Route::post('/{exam}/submit', [ExamTakingController::class, 'submit'])->name('submit');

        // Proctoring event ingestion (JSON, called by exam-proctor.js)
        Route::post('/{exam}/proctor/event', [ProctoringController::class, 'storeEvent'])->name('proctor.event');
    });

    Route::get('/{exam}/completed', [ExamTakingController::class, 'completed'])->name('completed');
});

Route::get('/examinations-hub/join', [ParticipantController::class, 'joinEntry'])->name('examination-hub.join.entry');
Route::post('/examinations-hub/join', [ParticipantController::class, 'joinLookup'])->name('examination-hub.join.lookup');
Route::get('/examinations-hub/join/{code}', [ParticipantController::class, 'joinForm'])->name('examination-hub.join');
Route::post('/examinations-hub/join/{code}', [ParticipantController::class, 'attemptJoin'])->name('examination-hub.join.attempt');

Route::prefix('examinations-hub/results')->name('examination-hub.results.')->group(function () {
    Route::get('/', [ParticipantResultsController::class, 'index'])->name('index');
    Route::get('/{submission}', [ParticipantResultsController::class, 'show'])->name('show');
});
