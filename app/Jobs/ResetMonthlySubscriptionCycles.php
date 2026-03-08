<?php

namespace App\Jobs;

use App\Models\Chat\SubscriptionCycle;
use App\Models\User;
use App\Services\SubscriptionCycleService;
use App\Services\SubscriptionTopupService;
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
    public function __construct() {}

    /**
     * Execute the job.
     * Processes expiring subscription cycles:
     * 1. Expires cycles that have reached their end date
     * 2. Activates the next cycle if available
     * 3. Carries over unused topup tokens to the next cycle
     */
    public function handle(SubscriptionCycleService $cycleService, SubscriptionTopupService $topupService): void
    {
        Log::info('Starting subscription cycle expiration and renewal job');

        try {
            // Find all subscription cycles that have expired (end_date has passed)
            $expiredCycles = SubscriptionCycle::where('status', 'active')
                ->where('cycle_end_date', '<', now())
                ->with(['user', 'pricingTier'])
                ->get();

            $processedCount = 0;
            $renewedCount = 0;
            $topupCarriedOverCount = 0;

            foreach ($expiredCycles as $cycle) {
                try {
                    $user = $cycle->user;

                    // Only process if the user still has an active token subscription
                    if (! $user || ! $user->activeTokenSubscription) {
                        Log::info('Skipping cycle expiration - user has no active subscription', [
                            'cycle_id' => $cycle->id,
                            'user_id' => $cycle->user_id,
                        ]);

                        continue;
                    }

                    // Carryover unused topup tokens to next cycle
                    $carryoverAmount = $topupService->carryoverTopupTokens($cycle);
                    if ($carryoverAmount > 0) {
                        $topupCarriedOverCount++;
                    }

                    // Expire current cycle and activate next one
                    $nextCycle = $cycle->expireAndActivateNext();

                    if ($nextCycle) {
                        $renewedCount++;
                        Log::info('Subscription cycle expired and renewed', [
                            'user_id' => $user->id,
                            'old_cycle_id' => $cycle->id,
                            'old_cycle_number' => $cycle->cycle_number,
                            'new_cycle_id' => $nextCycle->id,
                            'new_cycle_number' => $nextCycle->cycle_number,
                            'pricing_tier' => $cycle->pricingTier->name,
                            'topup_carryover' => $carryoverAmount,
                        ]);
                    } else {
                        Log::info('Subscription cycle expired - no renewal cycle available', [
                            'user_id' => $user->id,
                            'cycle_id' => $cycle->id,
                            'cycle_number' => $cycle->cycle_number,
                        ]);
                    }

                    $processedCount++;
                } catch (\Exception $e) {
                    Log::error('Failed to process cycle expiration', [
                        'cycle_id' => $cycle->id,
                        'user_id' => $cycle->user_id,
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString(),
                    ]);
                }
            }

            Log::info('Subscription cycle expiration and renewal job completed', [
                'total_expired_cycles' => $expiredCycles->count(),
                'cycles_processed' => $processedCount,
                'cycles_renewed' => $renewedCount,
                'topup_carryovers' => $topupCarriedOverCount,
            ]);
        } catch (\Exception $e) {
            Log::error('Subscription cycle job failed fatally', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            throw $e;
        }
    }
}
