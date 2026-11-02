<?php

namespace App\ExaminationHub\Jobs;

use App\ExaminationHub\Services\PullAgent;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

/**
 * SyncPullJob — pulls exam packages from central in the background.
 *
 * This job runs PullAgent asynchronously so the web request returns
 * immediately instead of timing out with large exam packages.
 */
class SyncPullJob implements ShouldQueue
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
        Log::info('SyncPullJob: Starting background pull sync');

        $agent = app(PullAgent::class);
        $result = $agent->pull();

        // Skip if another pull is already running
        if (isset($result['already_running']) && $result['already_running']) {
            Log::info('SyncPullJob: Skipped — a pull run is already in progress');
            return;
        }

        if (!$result['success']) {
            $errors = $result['errors'] ?? ['Unknown error'];
            Log::error('SyncPullJob: Pull failed: ' . implode(', ', $errors));
            return;
        }

        $pulled = $result['pulled'] ?? 0;
        Log::info("SyncPullJob: Completed — {$pulled} exam(s) pulled");
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error('SyncPullJob failed: ' . $exception->getMessage(), [
            'trace' => $exception->getTraceAsString(),
        ]);
    }
}
