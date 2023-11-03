<?php

namespace App\Console\Commands;

use App\Models\Subscription;
use Illuminate\Console\Command;
use App\Enums\SubscriptionStatus;
use Illuminate\Support\Facades\Log;
use App\Notifications\ExpiringSubscription;
use Illuminate\Support\Facades\Notification;

class ExpiringSubscriptionCron extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'expiringsubscription:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sends email reminder to subscribers of expiring subscription.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $today = now()->toDateString();
        Log::info("cron job running at " . $today);

        $subscriptions = Subscription::query()
            ->with('subscriber')
            ->whereDate('expires_at', $today)
            ->where('status', SubscriptionStatus::PAID)
            ->get();

        $subscriptions->each(function ($subscription) {
            $user = $subscription->subscriber;
            $reference =  $subscription->reference;
            Notification::send($user, new ExpiringSubscription($reference));
        });
    }
}
