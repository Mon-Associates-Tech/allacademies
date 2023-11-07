<?php

namespace App\Console\Commands;

use App\Enums\SubscriptionStatus;
use App\Models\Subscription;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Notification;
use App\Notifications\SubscriptionExpiredNotification;
use Illuminate\Support\Carbon;

class SubscriptionExpiredCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'subscription:expired';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send notification to users whose subscription has expired';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->output->info('Sending expired subscription notifications');

        Subscription::query()
            ->with('subscriber')
            ->where('status', SubscriptionStatus::PAID)
            ->whereDate('expires_at', Carbon::yesterday())
            ->each(function (Subscription $subscription) {
                Notification::send($subscription->subscriber, new SubscriptionExpiredNotification($subscription));
            });

        return Command::SUCCESS;
    }
}
