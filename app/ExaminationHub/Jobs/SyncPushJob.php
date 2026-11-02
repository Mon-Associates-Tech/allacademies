<?php

namespace App\ExaminationHub\Jobs;

use App\ExaminationHub\Models\SyncRun;
use App\ExaminationHub\Services\PushAgent;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

/**
 * SyncPushJob — pushes results from a center to central in the background.
 *
 * This job runs PushAgent asynchronously so the web request returns
 * immediately instead of timing out with many pending submissions.
 */
class SyncPushJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of times the job may be attempted.
     */
    public $tries = 3;

    /**
     * The maximum time in seconds the job can run.
     */
    public $timeout = 3600; // 1 hour max

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::info('SyncPushJob: Starting background push sync');

        $agent = app(PushAgent::class);
        $result = $agent->push();

        // Skip if another push is already running
        if (isset($result['submissions']['already_running']) && $result['submissions']['already_running']) {
            Log::info('SyncPushJob: Skipped — a push run is already in progress');
            return;
        }

        if (isset($result['submissions']['error'])) {
            Log::error('SyncPushJob: Push failed: ' . $result['submissions']['error']);
            return;
        }

        $ingested = $result['submissions']['ingested'] ?? 0;
        $proctoringIngested = $result['proctoring']['ingested'] ?? 0;
        Log::info("SyncPushJob: Completed — {$ingested} submissions, {$proctoringIngested} proctoring logs pushed");
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('SyncPushJob failed: ' . $exception->getMessage(), [
            'trace' => $exception->getTraceAsString(),
        ]);
    }
}
