<?php

namespace App\Services;

use App\Models\Chat\SubscriptionCycle;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Class SubscriptionTopupService
 *
 * Handles topup token purchases and their allocation across subscription cycles.
 * Topup tokens follow special rules:
 * - Added to the current active cycle
 * - If not exhausted during the cycle, carry over to the next cycle
 * - Only topup tokens carry over, not regular monthly allocations
 */
class SubscriptionTopupService
{
    /**
     * Process a topup for a user
     */
    public function processTopup(User $user, int $tokens): ?SubscriptionCycle
    {
        if ($tokens <= 0) {
            Log::warning('Invalid topup amount', [
                'user_id' => $user->id,
                'tokens' => $tokens,
            ]);

            return null;
        }

        return DB::transaction(function () use ($user, $tokens) {
            $currentCycle = $user->getCurrentActiveCycle();

            if (! $currentCycle) {
                Log::warning('No active subscription cycle found for topup', [
                    'user_id' => $user->id,
                    'tokens' => $tokens,
                ]);

                return null;
            }

            // Add topup tokens to current cycle
            $currentCycle->addTopupTokens($tokens);

            Log::info('Topup processed for user', [
                'user_id' => $user->id,
                'cycle_id' => $currentCycle->id,
                'cycle_number' => $currentCycle->cycle_number,
                'topup_tokens' => $tokens,
                'total_allocated' => $currentCycle->tokens_allocated,
            ]);

            return $currentCycle;
        });
    }

    /**
     * Get topup amount for a cycle
     * Returns the portion of tokens allocated that came from topups
     */
    public function getTopupAmount(SubscriptionCycle $cycle): int
    {
        $baseAllocation = $cycle->pricingTier->monthly_token_limit;

        return max(0, $cycle->tokens_allocated - $baseAllocation);
    }

    /**
     * Calculate topup carryover to next cycle
     * Only unused topup tokens carry over
     */
    public function calculateTopupCarryover(SubscriptionCycle $currentCycle): int
    {
        $topupAmount = $this->getTopupAmount($currentCycle);
        $topupUsed = min($topupAmount, $currentCycle->tokens_used);
        $topupRemaining = max(0, $topupAmount - $topupUsed);

        return $topupRemaining;
    }

    /**
     * Carryover unused topup tokens from current cycle to next cycle
     *
     * @return int Tokens carried over
     */
    public function carryoverTopupTokens(SubscriptionCycle $currentCycle): int
    {
        $carryoverAmount = $this->calculateTopupCarryover($currentCycle);

        if ($carryoverAmount <= 0) {
            return 0;
        }

        $nextCycle = $currentCycle->user->getNextUpcomingCycle();

        if (! $nextCycle) {
            Log::info('No next cycle to carryover topup tokens', [
                'user_id' => $currentCycle->user_id,
                'cycle_id' => $currentCycle->id,
                'carryover_amount' => $carryoverAmount,
            ]);

            return 0;
        }

        // Add topup tokens to next cycle (not as regular allocation)
        $nextCycle->addTopupTokens($carryoverAmount, false);

        Log::info('Topup tokens carried over to next cycle', [
            'user_id' => $currentCycle->user_id,
            'current_cycle_id' => $currentCycle->id,
            'next_cycle_id' => $nextCycle->id,
            'carryover_amount' => $carryoverAmount,
        ]);

        return $carryoverAmount;
    }

    /**
     * Get topup history for a user
     */
    public function getTopupHistory(User $user): \Illuminate\Support\Collection
    {
        return $user->subscriptionCycles()
            ->where('status', 'expired')
            ->get()
            ->map(function (SubscriptionCycle $cycle) {
                return [
                    'cycle_id' => $cycle->id,
                    'cycle_number' => $cycle->cycle_number,
                    'topup_amount' => $this->getTopupAmount($cycle),
                    'topup_used' => min($this->getTopupAmount($cycle), $cycle->tokens_used),
                    'topup_remaining' => $this->calculateTopupCarryover($cycle),
                    'cycle_start_date' => $cycle->cycle_start_date,
                    'cycle_end_date' => $cycle->cycle_end_date,
                ];
            });
    }
}
