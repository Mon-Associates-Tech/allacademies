<?php

namespace App\ExaminationHub\Commands;

use App\ExaminationHub\Services\PullAgent;
use Illuminate\Console\Command;

/**
 * sync:pull — Pull exam packages from central.
 *
 * This command is run by the scheduler every minute. It checks whether
 * the configured pull interval has elapsed before actually pulling.
 */
class SyncPull extends Command
{
    protected $signature = 'sync:pull {--force : Bypass the pull interval check}';
    protected $description = 'Pull exam packages from central server';

    public function __construct(
        protected PullAgent $pullAgent
    ) {
        parent::__construct();
    }

    public function handle(): int
    {
        $role = config('center.role', 'central');

        if ($role !== 'center') {
            $this->info('Skipping pull: this instance is not a center.');

            return Command::SUCCESS;
        }

        // Check if pull interval has elapsed (skip if --force)
        if (!$this->option('force')) {
            $lastPull = \App\ExaminationHub\Models\SyncRun::where('direction', 'pull')
                ->orderByDesc('started_at')
                ->value('started_at');

            $intervalMinutes = config('center.pull_interval_minutes', 5);

            if ($lastPull) {
                $lastPullTime = \Illuminate\Support\Carbon::parse($lastPull);
                if ($lastPullTime->gt(now()->subMinutes($intervalMinutes))) {
                    $wait = $lastPullTime->addMinutes($intervalMinutes)->diffInSeconds(now(), false);
                    $this->info("Skipping pull: next pull in {$wait}s (interval: {$intervalMinutes}min).");

                    return Command::SUCCESS;
                }
            }
        }

        $centralUrl = config('center.central_url');
        $this->info("Pulling exam packages from {$centralUrl}...");
        $this->newLine();

        $result = $this->pullAgent->pull();

        // Display detailed results
        if (!empty($result['details'])) {
            foreach ($result['details'] as $detail) {
                $status = $detail['status'] ?? 'unknown';
                $message = $detail['message'] ?? '';
                $examUuid = $detail['exam_uuid'] ?? '';
                $revision = $detail['revision'] ?? '';

                if ($status === 'success') {
                    $this->line("<info>✓</info> Exam {$examUuid} (rev {$revision}): {$message}");
                } elseif ($status === 'warning') {
                    $this->line("<comment>⚠</comment> {$message}");
                } else {
                    $this->line("<error>✗</error> Exam {$examUuid} (rev {$revision}): {$message}");
                }
            }

            $this->newLine();
        }

        if ($result['success']) {
            $this->info("Pull complete: {$result['pulled']} exam(s) pulled successfully.");
        } else {
            $this->error("Pull completed with errors:");
            foreach ($result['errors'] as $error) {
                $this->error("  - {$error}");
            }
        }

        return $result['success'] ? Command::SUCCESS : Command::FAILURE;
    }
}
