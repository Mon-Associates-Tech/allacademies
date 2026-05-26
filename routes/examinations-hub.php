<?php

use App\ExaminationHub\Controllers\DashboardController;
use App\ExaminationHub\Controllers\ExamCreationController;
use App\ExaminationHub\Controllers\ExamTakingController;
use App\ExaminationHub\Controllers\GradingSystemController;
use App\ExaminationHub\Controllers\HeartbeatController;
use App\ExaminationHub\Controllers\LiveMonitoringController;
use App\ExaminationHub\Controllers\ParticipantController;
use App\ExaminationHub\Controllers\ParticipantResultsController;
use App\ExaminationHub\Controllers\PerformanceReportController;
use App\ExaminationHub\Controllers\ProctoringController;
use App\ExaminationHub\Controllers\ParticipantPerformanceReportController;
use App\ExaminationHub\Controllers\StudentPerformanceController;
use App\ExaminationHub\Controllers\SubmissionController;
use App\ExaminationHub\Controllers\ExamSettingsController;
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
    Route::post('/exams/{exam}/participant-mode', [ExamSettingsController::class, 'updateParticipantMode'])->name('exams.participant-mode');
    Route::post('/exams/{exam}/toggle-results', [DashboardController::class, 'toggleResults'])->name('exams.toggle-results');
    Route::post('/exams/{exam}/violation-settings', [DashboardController::class, 'updateViolationSettings'])->name('exams.violation-settings');

    Route::get('/exams/{exam}/submissions', [SubmissionController::class, 'index'])->name('submissions.index');
    Route::get('/exams/{exam}/submissions/export', [SubmissionController::class, 'export'])->name('submissions.export');
    Route::get('/exams/{exam}/submissions/{submission}', [SubmissionController::class, 'show'])->name('submissions.show');
    Route::get('/exams/{exam}/submissions/export-excel', [SubmissionController::class, 'exportExcel'])->name('submissions.export-excel');
    
    // Add route for grading submissions
    Route::get('/exams/{exam}/submissions/{submission}/grade', [SubmissionController::class, 'grade'])->name('submissions.grade');

    Route::get('/performance', [ParticipantPerformanceReportController::class, 'index'])->name('performance.index');
    Route::get('/performance/{participantType}/{participantId}', [ParticipantPerformanceReportController::class, 'show'])->name('performance.show');
    Route::get('/performance/{participantType}/{participantId}/export', [ParticipantPerformanceReportController::class, 'export'])->name('performance.export');
    Route::get('/performance/{participantType}/{participantId}/export-excel', [ParticipantPerformanceReportController::class, 'exportExcel'])->name('performance.export-excel');
    Route::get('/performance/export-all-excel', [ParticipantPerformanceReportController::class, 'exportAllExcel'])->name('performance.export-all-excel');
    Route::get('/performance/export-all-pdf', [ParticipantPerformanceReportController::class, 'exportAllPdf'])->name('performance.export-all-pdf');

    Route::post('/exams/{exam}/participants/configured', [ParticipantController::class, 'storeConfigured'])->name('participants.configured.store');
    Route::post('/exams/{exam}/participants/configured/import', [ParticipantController::class, 'importConfigured'])->name('participants.configured.import');
    Route::patch('/exams/{exam}/participants/configured/{participant}/toggle', [ParticipantController::class, 'toggleConfigured'])->name('participants.configured.toggle');
    Route::delete('/exams/{exam}/participants/configured/{participant}', [ParticipantController::class, 'destroyConfigured'])->name('participants.configured.destroy');

    Route::get('/reports', [PerformanceReportController::class, 'index'])->name('reports.index');
    Route::post('/reports/generate', [PerformanceReportController::class, 'generate'])->name('reports.generate');

    // ── Proctoring (admin review) ─────────────────────────────────────────────
    Route::get('/exams/{exam}/proctoring', [ProctoringController::class, 'index'])->name('proctoring.index');
    Route::get('/exams/{exam}/submissions/{submission}/proctoring', [ProctoringController::class, 'show'])->name('proctoring.show');

    // ── Live Monitoring (Admin) ───────────────────────────────────────────────
    Route::prefix('exams/{exam}/live-monitoring')->name('live-monitoring.')->group(function () {
        Route::get('/', [LiveMonitoringController::class, 'index'])->name('index');
        Route::get('/participant/{submission}', [LiveMonitoringController::class, 'show'])->name('show');

        // API endpoints
        Route::get('/api/participants', [LiveMonitoringController::class, 'apiParticipants'])->name('api.participants');
        Route::get('/api/participant/{submission}', [LiveMonitoringController::class, 'apiParticipant'])->name('api.participant');

        // Admin actions
        Route::post('/warn/{submission}', [LiveMonitoringController::class, 'warn'])->name('warn');
        Route::post('/message/{submission}', [LiveMonitoringController::class, 'message'])->name('message');
        Route::post('/terminate/{submission}', [LiveMonitoringController::class, 'terminate'])->name('terminate');
        Route::post('/force-submit/{submission}', [LiveMonitoringController::class, 'forceSubmit'])->name('force-submit');
        Route::post('/extend-time/{submission}', [LiveMonitoringController::class, 'extendTime'])->name('extend-time');
        Route::post('/clear-warning/{submission}', [LiveMonitoringController::class, 'clearWarning'])->name('clear-warning');
    });

    Route::middleware('can:viewAny,App\ExaminationHub\Models\GradeScale')->group(function () {
        Route::get('/grading-system', [GradingSystemController::class, 'index'])->name('grading-system.index');
        Route::post('/grading-system', [GradingSystemController::class, 'store'])->name('grading-system.store');
        Route::put('/grading-system/{gradeScale}', [GradingSystemController::class, 'update'])->name('grading-system.update');
        Route::delete('/grading-system/{gradeScale}', [GradingSystemController::class, 'destroy'])->name('grading-system.destroy');
        Route::post('/grading-system/initialize', [GradingSystemController::class, 'initializeDefault'])->name('grading-system.initialize');
    });

    // Remove the duplicate proctoring event route - keeping the one with throttling in the exam taking section
});

// ── Exam Taking Routes ────────────────────────────────────────────────────────
Route::prefix('examinations')->name('examination-hub.take.')->group(function () {
    Route::get('/join', [ExamTakingController::class, 'join'])->name('join');
    Route::post('/authenticate', [ExamTakingController::class, 'authenticate'])->name('authenticate');

    Route::middleware([EnsureExamSession::class])->group(function () {
        Route::get('/{exam}/start', [ExamTakingController::class, 'start'])->name('start');
        Route::get('/{exam}/section/{sectionIndex}', [ExamTakingController::class, 'section'])->name('section');
        Route::post('/{exam}/save-response', [ExamTakingController::class, 'saveResponse'])
            ->middleware('throttle:60,1')
            ->name('save-response');
        Route::post('/{exam}/submit', [ExamTakingController::class, 'submit'])->name('submit');

        // Proctoring event ingestion
        Route::post('/{exam}/proctor/event', [ProctoringController::class, 'storeEvent'])
            ->middleware('throttle:30,1')
            ->name('proctor.event');

        // Heartbeat endpoints
        Route::post('/{exam}/heartbeat', [HeartbeatController::class, 'beat'])
            ->middleware('throttle:10,1')
            ->name('heartbeat');
        Route::post('/{exam}/heartbeat/init', [HeartbeatController::class, 'initialize'])
            ->middleware('throttle:5,1')
            ->name('heartbeat.init');
        Route::post('/{exam}/heartbeat/acknowledge-warning', [HeartbeatController::class, 'acknowledgeWarning'])
            ->middleware('throttle:10,1')
            ->name('heartbeat.acknowledge-warning');
    });

    Route::get('/{exam}/completed', [ExamTakingController::class, 'completed'])->name('completed');
});

// ── Public Join Routes ────────────────────────────────────────────────────────
Route::get('/examinations/participants/join', [ParticipantController::class, 'joinEntry'])->name('examination-hub.join.entry');
Route::post('/examinations/participants/join', [ParticipantController::class, 'joinLookup'])->name('examination-hub.join.lookup');
Route::get('/examinations/participants/join/{code}', [ParticipantController::class, 'joinForm'])->name('examination-hub.join');
Route::post('/examinations/participants/join/{code}', [ParticipantController::class, 'attemptJoin'])->name('examination-hub.join.attempt');

// ── Results Routes ────────────────────────────────────────────────────────────
Route::prefix('examinations/results')->name('examination-hub.results.')->group(function () {
    Route::get('/', [ParticipantResultsController::class, 'index'])->name('index');
    Route::get('/{submission}', [ParticipantResultsController::class, 'show'])->name('show');
    Route::get('/{submission}/certificate', [ParticipantResultsController::class, 'certificate'])->name('certificate');
    Route::get('/{submission}/certificate/download', [ParticipantResultsController::class, 'certificatePdf'])->name('certificate.download');
});