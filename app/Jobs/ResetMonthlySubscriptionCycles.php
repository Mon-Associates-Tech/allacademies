<?php

namespace App\Jobs;

use App\Models\Chat\SubscriptionCycle;
use App\Models\User;
use App\Services\SubscriptionCycleService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ResetMonthlySubscriptionCycles implements ShouldQueue
{
    use Dispatchable;
    use InteractsWithQueue;
    use Queueable;
    use SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct()
    {
    }

    /**
     * Execute the job.
     */
    public function handle(SubscriptionCycleService $cycleService): void
    {
        Log::info('Starting monthly subscription cycle reset job');

        try {
            // Find all subscription cycles that have expired (end_date has passed)
            // and mark them as expired, creating new cycles for next month
            $expiredCycles = SubscriptionCycle::where('status', 'active')
                ->where('cycle_end_date', '<', now())
                ->get();

            $processedCount = 0;

            foreach ($expiredCycles as $cycle) {
                try {
                    $user = $cycle->user;

                    // Only reset if the user still has an active subscription
                    if ($user && $user->activeTokenSubscription) {
                        $pricingTier = $cycle->pricingTier;
                        $cycleService->resetMonthlyTokens($user, $pricingTier);
                        $processedCount++;

                        Log::info('Reset monthly tokens for user', [
                            'user_id' => $user->id,
                            'old_cycle_id' => $cycle->id,
                            'pricing_tier' => $pricingTier->name,
                        ]);
                    }
                } catch (\Exception $e) {
                    Log::error('Failed to reset cycle for user', [
                        'cycle_id' => $cycle->id,
                        'user_id' => $cycle->user_id,
                        'error' => $e->getMessage(),
                    ]);
                }
            }

            Log::info('Monthly subscription cycle reset job completed', [
                'total_processed' => $processedCount,
                'expired_cycles_found' => $expiredCycles->count(),
            ]);
        } catch (\Exception $e) {
            Log::error('Monthly subscription cycle reset job failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            throw $e;
        }
    }
}
