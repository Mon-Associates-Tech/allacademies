<?php

namespace App\Console\Commands;

use App\Models\Chat\SubscriptionCycle;
use Illuminate\Console\Command;

class ActivateCurrentCycles extends Command
{
    protected $signature = 'cycles:activate {--user_id= : Activate cycles for a specific user}';

    protected $description = 'Activate subscription cycles that are within their period and not expired';

    public function handle()
    {
        $userId = $this->option('user_id');

        $query = SubscriptionCycle::where('status', 'inactive')
            ->where('cycle_start_date', '<=', now())
            ->where('cycle_end_date', '>=', now());

        if ($userId) {
            $query->where('user_id', $userId);
        }

        $cycles = $query->get();

        if ($cycles->isEmpty()) {
            $this->info('No inactive cycles found within current period.');
            return 0;
        }

        $activatedCount = 0;
        foreach ($cycles as $cycle) {
            $cycle->status = 'active';
            $cycle->save();
            $activatedCount++;

            $this->info("Activated cycle #{$cycle->id} for user #{$cycle->user_id} (Period: {$cycle->cycle_start_date->format('Y-m-d')} to {$cycle->cycle_end_date->format('Y-m-d')})");
        }

        $this->info("Successfully activated {$activatedCount} cycle(s).");
        return 0;
    }
}
