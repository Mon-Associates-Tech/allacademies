<?php

namespace App\Console\Commands;

use App\Services\TokenSubscriptionService;
use Illuminate\Console\Command;

class CheckExpiredTokenSubscriptions extends Command
{
    protected $signature = 'tokens:check-expired';

    protected $description = 'Check and expire token subscriptions that have passed their expiry date';

    protected $subscriptionService;

    public function __construct(TokenSubscriptionService $subscriptionService)
    {
        parent::__construct();
        $this->subscriptionService = $subscriptionService;
    }

    public function handle()
    {
        $this->info('Checking for expired token subscriptions...');

        $expiredCount = $this->subscriptionService->checkExpiredSubscriptions();

        $this->info("Expired {$expiredCount} subscription(s).");

        return Command::SUCCESS;
    }
}
