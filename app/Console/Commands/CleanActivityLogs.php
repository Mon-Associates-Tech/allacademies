<?php

namespace App\Console\Commands;

use App\Models\UserActivity;
use Illuminate\Console\Command;

class CleanActivityLogs extends Command
{
    protected $signature = 'activity-log:clean';

    protected $description = 'Clean up old activity logs based on retention days configured';

    public function handle(): int
    {
        if (! config('activity_log.enabled')) {
            $this->info('Activity logging is disabled.');

            return self::SUCCESS;
        }

        $retentionDays = config('activity_log.retention_days', 365);

        if ($retentionDays === 0) {
            $this->info('Activity log retention is set to indefinite. No cleanup performed.');

            return self::SUCCESS;
        }

        $cutoffDate = now()->subDays($retentionDays);

        $deleted = UserActivity::where('created_at', '<', $cutoffDate)->delete();

        $this->info("Deleted {$deleted} activity logs older than {$retentionDays} days.");

        return self::SUCCESS;
    }
}
