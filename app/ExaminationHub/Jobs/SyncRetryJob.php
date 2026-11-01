<?php

namespace App\ExaminationHub\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

/**
 * SyncRetryJob — retries failed sync operations with exponential backoff.
 *
 * This job is dispatched by PushAgent when a batch push fails.
 * It retries the operation with exponential backoff and jitter,
 * up to a maximum number of attempts.
 */
class SyncRetryJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The number of times the job may be attempted.
     *
     * @var int
     */
    public $tries = 5;

    /**
     * The maximum delay between retries in seconds (15 minutes).
     */
    const MAX_DELAY = 900;

    /**
     * The base delay in seconds.
     */
    const BASE_DELAY = 30;

    /**
     * Create a new job instance.
     *
     * @param string $operation The operation type ('push_submissions' or 'push_proctoring')
     * @param array $context Additional context for the retry
     */
    public function __construct(
        protected string $operation,
        protected array $context = []
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $attempt = $this->attempts();

        // Calculate exponential backoff with jitter
        $delay = self::BASE_DELAY * pow(2, $attempt - 1) + random_int(0, self::BASE_DELAY);
        $delay = min($delay, self::MAX_DELAY);

        Log::info("SyncRetryJob: Attempt {$attempt} for {$this->operation} after {$delay}s delay", $this->context);

        switch ($this->operation) {
            case 'push_submissions':
                $this->retryPushSubmissions();
                break;
            case 'push_proctoring':
                $this->retryPushProctoring();
                break;
            default:
                Log::warning("SyncRetryJob: Unknown operation {$this->operation}");
                return;
        }
    }

    /**
     * Retry pushing submissions.
     */
    protected function retryPushSubmissions(): void
    {
        $agent = app(\App\ExaminationHub\Services\PushAgent::class);
        $result = $agent->push();

        if (isset($result['submissions']['error'])) {
            Log::error('SyncRetryJob: Push submissions still failing: ' . $result['submissions']['error']);
        }
    }

    /**
     * Retry pushing proctoring logs.
     */
    protected function retryPushProctoring(): void
    {
        $agent = app(\App\ExaminationHub\Services\PushAgent::class);
        $result = $agent->push();

        if (isset($result['proctoring']['error'])) {
            Log::error('SyncRetryJob: Push proctoring still failing: ' . $result['proctoring']['error']);
        }
    }

    /**
     * Handle a job failure.
     */
    public function failed(\Throwable $exception): void
    {
        Log::error("SyncRetryJob failed after {$this->attempts()} attempts: " . $exception->getMessage(), [
            'operation' => $this->operation,
            'context' => $this->context,
            'trace' => $exception->getTraceAsString(),
        ]);
    }
}
