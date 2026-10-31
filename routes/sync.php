<?php

use App\ExaminationHub\Controllers\Sync\HeartbeatController;
use App\ExaminationHub\Controllers\Sync\ManifestController;
use App\ExaminationHub\Controllers\Sync\PackageController;
use App\ExaminationHub\Controllers\Sync\ProctoringIngestController;
use App\ExaminationHub\Controllers\Sync\StateController;
use App\ExaminationHub\Controllers\Sync\SubmissionIngestController;
use App\ExaminationHub\Middleware\SyncAuth;
use App\ExaminationHub\Middleware\SyncSignature;
use App\ExaminationHub\Middleware\SyncVersion;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Sync API Routes
|--------------------------------------------------------------------------
|
| Dedicated /api/sync/v1/* namespace for multi-center sync operations.
| All routes use token auth, HMAC signature verification, and version
| handshake middleware.
|
*/

Route::prefix('sync/v1')->middleware([SyncAuth::class, SyncSignature::class, SyncVersion::class])->group(function () {

    // ─── Pull endpoints (center → central) ──────────────────────────────────

    // Manifest: list of all published exams with revision info
    Route::get('/manifest', [ManifestController::class, 'index']);

    // Download a specific exam package revision
    Route::get('/exams/{uuid}/revisions/{revision}', [PackageController::class, 'show']);

    // Small state block for an exam (results_released, status)
    Route::get('/exams/{uuid}/state', [StateController::class, 'show']);

    // ─── Push endpoints (center → central) ──────────────────────────────────

    // Ingest a batch of submissions with answers
    Route::post('/ingest/submissions', [SubmissionIngestController::class, 'store']);

    // Ingest a batch of proctoring logs
    Route::post('/ingest/proctoring', [ProctoringIngestController::class, 'store']);

    // ─── Heartbeat ──────────────────────────────────────────────────────────

    // Center reports app version, last pull, backlog
    Route::post('/heartbeat', [HeartbeatController::class, 'store']);
});
