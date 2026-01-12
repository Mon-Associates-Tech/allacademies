<?php

namespace App\Console\Commands;

use App\Models\Chat\SubscriptionCycle;
use Illuminate\Console\Command;

class ExpireSubscriptionCycles extends Command
{
    protected $signature = 'subscriptions:expire-cycles';

    protected $description = 'Expire subscription cycles that have passed their end date';

    public function handle(): int
    {
        $expiredCycles = SubscriptionCycle::where('status', 'active')
            ->where('cycle_end_date', '<', now())
            ->get();

        $count = $expiredCycles->count();

        if ($count === 0) {
            $this->info('No expired cycles found.');

            return self::SUCCESS;
        }

        foreach ($expiredCycles as $cycle) {
            $cycle->expireAndActivateNext();
            $this->info("Expired cycle #{$cycle->id} for user #{$cycle->user_id}");
        }

        $this->info("Expired {$count} subscription cycle(s).");

        return self::SUCCESS;
    }
}
