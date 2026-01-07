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
     * Process a topup for a user - add topup tokens to current active cycle
     * This is called when a user purchases additional tokens
     */
    public function processTopup(User $user, int $topupTokens): ?SubscriptionCycle
    {
        if ($topupTokens <= 0) {
            Log::warning('Invalid topup amount', [
                'user_id' => $user->id,
                'tokens' => $topupTokens,
            ]);

            return null;
        }

        return DB::transaction(function () use ($user, $topupTokens) {
            $currentCycle = $user->getCurrentActiveCycle();

            if (! $currentCycle) {
                Log::warning('No active subscription cycle found for topup', [
                    'user_id' => $user->id,
                    'topup_tokens' => $topupTokens,
                ]);

                return null;
            }

            // Add topup tokens to current cycle (not replacing, just adding)
            $success = $currentCycle->addTopupTokens($topupTokens);

            if ($success) {
                Log::info('Topup processed and added to current cycle', [
                    'user_id' => $user->id,
                    'cycle_id' => $currentCycle->id,
                    'cycle_number' => $currentCycle->cycle_number,
                    'topup_tokens' => $topupTokens,
                    'total_tokens_allocated' => $currentCycle->tokens_allocated,
                    'topup_tokens_allocated' => $currentCycle->topup_tokens_allocated,
                ]);
            }

            return $currentCycle;
        });
    }

    /**
     * Get topup amount for a cycle (total topup tokens in this cycle)
     */
    public function getTopupAmount(SubscriptionCycle $cycle): int
    {
        return $cycle->topup_tokens_allocated;
    }

    /**
     * Get unused topup tokens in a cycle
     */
    public function getUnusedTopupAmount(SubscriptionCycle $cycle): int
    {
        return $cycle->calculateUnusedTopupTokens();
    }

    /**
     * Calculate topup carryover to next cycle
     * Only unused topup tokens carry over, not base allocation
     */
    public function calculateTopupCarryover(SubscriptionCycle $currentCycle): int
    {
        return $currentCycle->calculateUnusedTopupTokens();
    }

    /**
     * Carryover unused topup tokens from current cycle to next cycle
     *
     * @return int Tokens carried over
     */
    public function carryoverTopupTokens(SubscriptionCycle $currentCycle): int
    {
        return $currentCycle->carryoverUnusedTopupTokens();
    }

    /**
     * Get topup history for a user
     */
    public function getTopupHistory(User $user): \Illuminate\Support\Collection
    {
        return $user->subscriptionCycles()
            ->where('topup_tokens_allocated', '>', 0)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function (SubscriptionCycle $cycle) {
                return [
                    'cycle_id' => $cycle->id,
                    'cycle_number' => $cycle->cycle_number,
                    'topup_amount' => $this->getTopupAmount($cycle),
                    'topup_used' => min($this->getTopupAmount($cycle), max(0, $cycle->tokens_used - $cycle->getBaseTokensAllocated())),
                    'topup_remaining' => $this->getUnusedTopupAmount($cycle),
                    'cycle_start_date' => $cycle->cycle_start_date,
                    'cycle_end_date' => $cycle->cycle_end_date,
                    'status' => $cycle->status,
                ];
            });
    }

    /**
     * Get current topup tokens for a user (in active cycle)
     */
    public function getCurrentTopupTokens(User $user): int
    {
        $cycle = $user->getCurrentActiveCycle();

        return $cycle ? $cycle->topup_tokens_allocated : 0;
    }

    /**
     * Get remaining topup tokens for a user (in active cycle)
     */
    public function getRemainingTopupTokens(User $user): int
    {
        $cycle = $user->getCurrentActiveCycle();

        return $cycle ? $cycle->getTopupTokensRemaining() : 0;
    }
}
