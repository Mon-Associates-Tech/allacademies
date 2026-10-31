<?php

use App\Http\Controllers\Center\CenterManagementController;
use App\Http\Controllers\Center\ExamPublishController;
use App\Http\Controllers\Center\CenterDashboardController;
use App\Http\Controllers\Center\CenterSyncController;
use App\Http\Controllers\Center\BankExportImportController;
use App\Http\Controllers\Center\GradingCorrectionsController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Multi-Center Sync Admin Routes
|--------------------------------------------------------------------------
|
| These routes provide the admin UI for managing examination centers,
| publishing exams, monitoring sync status, exporting/importing the
| question bank, and applying grading corrections.
|
| Only available on central instances (APP_ROLE=central).
|
*/

Route::middleware(['auth', 'verified'])->prefix('admin/center-sync')->name('center-sync.')->group(function () {

    // ── Multi-Center Dashboard ──────────────────────────────────────────────
    Route::get('/dashboard', [CenterDashboardController::class, 'index'])->name('dashboard');

    // ── Center Management ───────────────────────────────────────────────────
    // Static routes MUST come before {center} parameter routes to avoid conflicts
    Route::get('/centers', [CenterManagementController::class, 'index'])->name('centers.index');
    Route::get('/centers/create', [CenterManagementController::class, 'create'])->name('centers.create');
    Route::post('/centers', [CenterManagementController::class, 'store'])->name('centers.store');
    Route::get('/centers/{center}', [CenterDashboardController::class, 'show'])->name('centers.show');
    Route::get('/centers/{center}/edit', [CenterManagementController::class, 'edit'])->name('centers.edit');
    Route::patch('/centers/{center}', [CenterManagementController::class, 'update'])->name('centers.update');
    Route::patch('/centers/{center}/toggle-active', [CenterManagementController::class, 'toggleActive'])->name('centers.toggle-active');
    Route::post('/centers/{center}/regenerate-token', [CenterManagementController::class, 'regenerateToken'])->name('centers.regenerate-token');

    // ── Exam Publish / Revise ───────────────────────────────────────────────
    Route::get('/exams', [ExamPublishController::class, 'index'])->name('exams.index');
    Route::get('/exams/{exam}', [ExamPublishController::class, 'show'])->name('exams.show');
    Route::post('/exams/{exam}/publish', [ExamPublishController::class, 'publish'])->name('exams.publish');
    Route::get('/exams/{exam}/revise', [ExamPublishController::class, 'reviseForm'])->name('exams.revise-form');
    Route::post('/exams/{exam}/revise', [ExamPublishController::class, 'revise'])->name('exams.revise');
    Route::post('/exams/{exam}/release-results', [ExamPublishController::class, 'releaseResults'])->name('exams.release-results');

    // ── Sync Status (Center staff view) ─────────────────────────────────────
    Route::get('/sync', [CenterSyncController::class, 'index'])->name('sync.index');
    Route::post('/sync/push', [CenterSyncController::class, 'pushNow'])->name('sync.push');
    Route::post('/sync/pull', [CenterSyncController::class, 'pullNow'])->name('sync.pull');

    // ── Bank Export / Import ────────────────────────────────────────────────
    Route::get('/bank', [BankExportImportController::class, 'index'])->name('bank.index');
    Route::post('/bank/export', [BankExportImportController::class, 'export'])->name('bank.export');
    Route::post('/bank/import', [BankExportImportController::class, 'import'])->name('bank.import');

    // ── Grading Corrections ─────────────────────────────────────────────────
    Route::get('/exams/{exam}/corrections', [GradingCorrectionsController::class, 'show'])->name('corrections.show');
    Route::patch('/exams/{exam}/corrections', [GradingCorrectionsController::class, 'update'])->name('corrections.update');
    Route::post('/exams/{exam}/corrections/question/{question}/exclude', [GradingCorrectionsController::class, 'excludeQuestion'])->name('corrections.exclude-question');
    Route::post('/exams/{exam}/corrections/question/{question}/restore', [GradingCorrectionsController::class, 'restoreQuestion'])->name('corrections.restore-question');
});
