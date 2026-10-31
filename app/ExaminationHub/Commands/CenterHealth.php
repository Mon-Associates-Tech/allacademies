<?php

namespace App\ExaminationHub\Commands;

use App\ExaminationHub\Models\Center;
use App\ExaminationHub\Models\GeneralExamSubmission;
use Illuminate\Console\Command;

/**
 * CenterHealth — reports center health status on central.
 *
 * Shows last seen, last pull, last push, backlog, app version, and parity alerts
 * for all centers. Alerts for stale centers based on configurable threshold.
 *
 * Usage:
 *   php artisan center:health
 *   php artisan center:health --stale=120  # Alert centers not seen in 2 hours
 */
class CenterHealth extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'center:health {--stale=60 : Minutes before a center is considered stale}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Report center health status on central server';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $role = config('center.role');

        if ($role !== 'central') {
            $this->info('Skipping: APP_ROLE is not central.');
            return self::SUCCESS;
        }

        $staleMinutes = (int) $this->option('stale');
        $centers = Center::active()->orderBy('name')->get();

        if ($centers->isEmpty()) {
            $this->info('No active centers found.');
            return self::SUCCESS;
        }

        $this->table(
            ['Center', 'Last Seen', 'Last Pull', 'Last Push', 'Backlog', 'App Version', 'Parity Failures', 'Status'],
            $centers->map(fn ($center) => [
                $center->name,
                $center->last_seen_at?->diffForHumans() ?? 'Never',
                $center->last_pull_at?->diffForHumans() ?? 'Never',
                $center->last_push_at?->diffForHumans() ?? 'Never',
                $this->getBacklog($center->id),
                $center->app_version ?? 'Unknown',
                $this->getParityFailures($center->id),
                $center->last_seen_at && $center->last_seen_at->lt(now()->subMinutes($staleMinutes))
                    ? 'STALE'
                    : 'OK',
            ])->toArray()
        );

        // Alert for stale centers
        $staleCenters = Center::active()->stale($staleMinutes)->get();
        if ($staleCenters->isNotEmpty()) {
            $this->warn("\nStale centers (not seen in {$staleMinutes} minutes):");
            foreach ($staleCenters as $center) {
                $this->line("  - {$center->name} (last seen: {$center->last_seen_at?->diffForHumans()})");
            }
        }

        return self::SUCCESS;
    }

    /**
     * Get the backlog size for a center.
     */
    protected function getBacklog(int $centerId): int
    {
        return GeneralExamSubmission::where('center_id', $centerId)
            ->whereRaw('synced_version < sync_version')
            ->count();
    }

    /**
     * Get the parity failure count for a center.
     */
    protected function getParityFailures(int $centerId): int
    {
        return GeneralExamSubmission::where('center_id', $centerId)
            ->where('parity_ok', false)
            ->count();
    }
}
