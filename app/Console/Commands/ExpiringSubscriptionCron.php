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
    protected $description = 'Expiring subscriptions reminder';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $now = Carbon::now();
        Log::info("cron job running at " . $now);

        $notify = Subscription::query()
            ->with('subscriber')
            ->whereRaw("TIMESTAMPDIFF(DAY, expires_at, ?) = 7", [$now])
            ->where('status', SubscriptionStatus::PAID)
            ->get();

        if (count($notify)) {
            foreach ($notify as $notification) {
                $user = $notification->subscriber;
                $message =  "Subscriptions with reference no. " . $notification->reference . " will expire in 7 days. Kindy renew this subscription to keep using All Academies.";
                Notification::send($user, new ExpiringSubscription($message));
            }
        }

        // return Command::SUCCESS;
    }
}
