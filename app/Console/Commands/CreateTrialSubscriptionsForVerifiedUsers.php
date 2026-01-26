<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class CreateTrialSubscriptionsForVerifiedUsers extends Command
{
    protected $signature = 'subscriptions:create-trials-for-verified';

    protected $description = 'Create free trial token subscriptions for all verified users without subscriptions';

    public function handle(): int
    {
        $this->info('Finding verified users without token subscriptions...');

        $users = User::whereNotNull('email_verified_at')
            ->doesntHave('tokenSubscriptions')
            ->get();

        $count = $users->count();
        $this->info("Found {$count} users");

        if ($count === 0) {
            $this->info('No users need trial subscriptions.');

            return self::SUCCESS;
        }

        $bar = $this->output->createProgressBar($count);
        $bar->start();

        $created = 0;
        $failed = 0;

        foreach ($users as $user) {
            try {
                $user->createFreeTrialSubscription();
                $created++;
            } catch (\Exception $e) {
                $this->newLine();
                $this->error("Failed for user {$user->id}: {$e->getMessage()}");
                $failed++;
            }
            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        $this->info("✓ Created {$created} trial subscriptions");
        if ($failed > 0) {
            $this->warn("✗ Failed to create {$failed} subscriptions");
        }

        return self::SUCCESS;
    }
}
