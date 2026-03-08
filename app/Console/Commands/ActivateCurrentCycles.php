<?php

namespace App\Console\Commands;

use App\Models\Chat\SubscriptionCycle;
use Illuminate\Console\Command;

class ActivateCurrentCycles extends Command
{
    protected $signature = 'cycles:activate {--user_id= : Activate cycles for a specific user}';

    protected $description = 'Manage subscription cycle lifecycle: expire old cycles, activate current cycles (one per user)';

    public function handle()
    {
        $userId = $this->option('user_id');
        $now = now();

        // Build base query
        $baseQuery = SubscriptionCycle::query();
        if ($userId) {
            $baseQuery->where('user_id', $userId);
        }

        // 1. Mark expired cycles
        $expiredCount = (clone $baseQuery)
            ->where('cycle_end_date', '<', $now)
            ->whereIn('status', ['active', 'inactive'])
            ->update(['status' => 'expired']);

        // 2. Deactivate active cycles that are no longer current
        $deactivatedCount = (clone $baseQuery)
            ->where('status', 'active')
            ->where(function($query) use ($now) {
                $query->where('cycle_start_date', '>', $now)
                      ->orWhere('cycle_end_date', '<', $now);
            })
            ->update(['status' => 'inactive']);

        // 3. Find and activate the current cycle (only one per user)
        $usersToProcess = $userId ? [$userId] : (clone $baseQuery)->distinct()->pluck('user_id');
        $activatedCount = 0;

        foreach ($usersToProcess as $currentUserId) {
            $currentCycle = SubscriptionCycle::where('user_id', $currentUserId)
                ->where('status', 'inactive')
                ->where('cycle_start_date', '<=', $now)
                ->where('cycle_end_date', '>=', $now)
                ->orderBy('cycle_start_date')
                ->first();

            if ($currentCycle) {
                $currentCycle->status = 'active';
                $currentCycle->save();
                $activatedCount++;

                $this->info("Activated cycle #{$currentCycle->id} for user #{$currentCycle->user_id} (Period: {$currentCycle->cycle_start_date->format('Y-m-d')} to {$currentCycle->cycle_end_date->format('Y-m-d')})");
            }
        }

        $this->info("Expired: {$expiredCount}, Deactivated: {$deactivatedCount}, Activated: {$activatedCount} cycle(s).");
        return 0;
    }
}
