<?php

namespace App\ExaminationHub\Commands;

use App\ExaminationHub\Services\PushAgent;
use Illuminate\Console\Command;

/**
 * SyncPush — pushes pending results from a center to central.
 *
 * This command is run by the scheduler every minute. It checks whether
 * the configured push interval has elapsed before actually pushing.
 * It can also be triggered manually after grading or on demand.
 *
 * Usage:
 *   php artisan sync:push          # Run push if interval elapsed
 *   php artisan sync:push --force  # Run push immediately
 */
class SyncPush extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:push {--force : Force push immediately}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Push pending submissions and proctoring logs to central server';

    /**
     * Execute the console command.
     */
    public function handle(PushAgent $pushAgent): int
    {
        $role = config('center.role');

        // Only centers push data
        if ($role !== 'center') {
            $this->info('Skipping push: APP_ROLE is not center.');
            return self::SUCCESS;
        }

        // Check if push interval has elapsed (unless --force)
        if (!$this->option('force')) {
            $lastPush = \App\ExaminationHub\Models\SyncRun::where('direction', 'push')
                ->orderBy('started_at', 'desc')
                ->first();

            $pushInterval = config('center.push_interval_minutes', 5);

            if ($lastPush && $lastPush->started_at->gt(now()->subMinutes($pushInterval))) {
                $remaining = $lastPush->started_at->diffInMinutes(now(), false);
                $this->info("Skipping push: last push was {$remaining} minutes ago (interval: {$pushInterval} minutes).");
                return self::SUCCESS;
            }
        }

        $this->info('Starting push to central...');
        $startTime = now();

        $result = $pushAgent->push();

        $duration = $startTime->diffInSeconds(now());

        if (isset($result['submissions']['error'])) {
            $this->error('Push failed: ' . $result['submissions']['error']);
            $this->info('To fix this, add the missing values to your .env file:');
            $this->info('  CENTRAL_URL=http://<central-server-url>');
            $this->info('  CENTER_SYNC_TOKEN=<token-from-central>');
            $this->info('  CENTER_ID=<center-database-id>');
            $this->info('Then run: php artisan config:clear');
            return self::FAILURE;
        }

        $submissions = $result['submissions'] ?? [];
        $proctoring = $result['proctoring'] ?? [];

        $this->info("Push completed in {$duration}s:");
        $subIngested = $submissions['ingested'] ?? 0;
        $subSkipped = $submissions['skipped'] ?? 0;
        $subErrors = $submissions['errors'] ?? 0;
        $this->info("  Submissions: {$subIngested} ingested, {$subSkipped} skipped, {$subErrors} errors");
        $proIngested = $proctoring['ingested'] ?? 0;
        $proSkipped = $proctoring['skipped'] ?? 0;
        $proErrors = $proctoring['errors'] ?? 0;
        $this->info("  Proctoring:  {$proIngested} ingested, {$proSkipped} skipped, {$proErrors} errors");

        return self::SUCCESS;
    }
}
