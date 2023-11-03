<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Subscription;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Enums\SubscriptionStatus;
use Illuminate\Support\Facades\Notification;
use App\Notifications\ExpiringSubscription;

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
        $now = Carbon::now();
        Log::info("cron job running at " . $now);

        $subscriptions = Subscription::query()
            ->with('subscriber')
            ->where('expires_at', $now->copy()->addDays(7))
            ->where('status', SubscriptionStatus::PAID)
            ->get();

        $subscriptions->each(function ($subscription) {
            $user = $subscription->subscriber;
            $message =  "Your subscription with reference no. " . $subscription->reference . " will expire in 7 days. Kindy renew this subscription to continue using All Academies.";
            Notification::send($user, new ExpiringSubscription($message));
        });
    }
}
