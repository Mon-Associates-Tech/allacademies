<?php

namespace App\Services;

use App\Models\Chat\PricingTier;
use App\Models\Chat\SubscriptionCycle;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SubscriptionCycleService
{
    protected IncrementalPricingService $pricingService;

    public function __construct(IncrementalPricingService $pricingService)
    {
        $this->pricingService = $pricingService;
    }

    /**
     * Create a new subscription cycle for a user
     * Each cycle is 30 days from the subscription start date (anniversary date)
     * Price stored is the cumulative total cost up to this cycle
     *
     * @param  float|null  $customPrice  Optional custom cumulative price for the cycle
     * @param  string|null  $groupId  Optional UUID to group cycles from the same purchase
     * @param  bool  $isTopup  Whether this is a topup cycle (default: false)
     */
    public function createCycle(User $user, PricingTier $pricingTier, Carbon $startDate, int $cycleNumber, ?float $customPrice = null, ?string $groupId = null, bool $isTopup = false): SubscriptionCycle
    {
        return DB::transaction(function () use ($user, $pricingTier, $startDate, $cycleNumber, $customPrice, $groupId, $isTopup) {
            // Cycle ends 30 days after start (anniversary date model)
            $endDate = $startDate->copy()->addDays(30);

            // Use cumulative price (total cost up to this cycle)
            $price = $customPrice ?? $pricingTier->getCumulativePriceUpToCycle($cycleNumber);
            $tokenLimit = $this->pricingService->getMonthlyTokenLimit($pricingTier);

            $cycle = SubscriptionCycle::create([
                'user_id' => $user->id,
                'pricing_tier_id' => $pricingTier->id,
                'subscription_group_id' => $groupId,
                'cycle_number' => $cycleNumber,
                'cycle_start_date' => $startDate,
                'cycle_end_date' => $endDate,
                'tokens_allocated' => $tokenLimit,
                'tokens_used' => 0,
                'current_price' => $price,
                'status' => 'active',
                'is_topup' => $isTopup,
            ]);

            Log::info('Subscription cycle created', [
                'user_id' => $user->id,
                'cycle_id' => $cycle->id,
                'cycle_number' => $cycleNumber,
                'pricing_tier' => $pricingTier->name,
                'token_limit' => $tokenLimit,
                'cumulative_price' => $price,
                'cycle_start' => $startDate->toDateString(),
                'cycle_end' => $endDate->toDateString(),
                'group_id' => $groupId,
                'is_topup' => $isTopup,
            ]);

            return $cycle;
        });
    }

    /**
     * Create initial cycle(s) for a new subscription
     * Each cycle is 30 days from subscription start date (anniversary model)
     * Price is cumulative from the subscription start
     */
    public function initializeSubscriptionCycles(User $user, PricingTier $pricingTier, Carbon $subscriptionStartDate): SubscriptionCycle
    {
        return DB::transaction(function () use ($user, $pricingTier, $subscriptionStartDate) {
            // Calculate cycle dates using anniversary model (30 days from start date)
            $cycleStartDate = $subscriptionStartDate->copy()->startOfDay();
            $cycleEndDate = $subscriptionStartDate->copy()->addDays(30);

            // Cycle 1 uses initial_price (cumulative is just initial_price for cycle 1)
            $price = $pricingTier->getCumulativePriceUpToCycle(1);
            $tokenLimit = $this->pricingService->getMonthlyTokenLimit($pricingTier);

            // Create the first cycle
            $cycle = SubscriptionCycle::create([
                'user_id' => $user->id,
                'pricing_tier_id' => $pricingTier->id,
                'cycle_number' => 1,
                'cycle_start_date' => $cycleStartDate,
                'cycle_end_date' => $cycleEndDate,
                'tokens_allocated' => $tokenLimit,
                'tokens_used' => 0,
                'current_price' => $price,
                'status' => 'active',
            ]);

            Log::info('Initial subscription cycles created', [
                'user_id' => $user->id,
                'cycle_id' => $cycle->id,
                'pricing_tier' => $pricingTier->name,
                'subscription_start_date' => $subscriptionStartDate,
                'cumulative_price' => $price,
                'cycle_start' => $cycleStartDate->toDateString(),
                'cycle_end' => $cycleEndDate->toDateString(),
            ]);

            return $cycle;
        });
    }

    /**
     * Get the current active cycle for a user
     */
    public function getCurrentActiveCycle(User $user): ?SubscriptionCycle
    {
        return $user->subscriptionCycles()
            ->where('status', 'active')
            ->where('cycle_start_date', '<=', now())
            ->where('cycle_end_date', '>=', now())
            ->first();
    }

    /**
     * Get the next upcoming cycle for a user
     */
    public function getNextUpcomingCycle(User $user): ?SubscriptionCycle
    {
        return $user->subscriptionCycles()
            ->where('cycle_start_date', '>', now())
            ->orderBy('cycle_start_date')
            ->first();
    }

    /**
     * Reset monthly cycle for a user (called when cycle expires)
     * Marks current cycle as expired and creates a new one
     * Uses anniversary date model (30 days from previous start date)
     * Price is cumulative total cost up to the new cycle
     */
    public function resetMonthlyTokens(User $user, PricingTier $pricingTier): SubscriptionCycle
    {
        return DB::transaction(function () use ($user, $pricingTier) {
            $currentCycle = $user->getCurrentActiveCycle();

            if ($currentCycle) {
                // Mark current cycle as expired
                $currentCycle->update(['status' => 'expired']);

                Log::info('Previous subscription cycle marked as expired', [
                    'user_id' => $user->id,
                    'cycle_id' => $currentCycle->id,
                    'cycle_number' => $currentCycle->cycle_number,
                ]);
            }

            // Create new cycle starting from anniversary of previous cycle end
            $nextCycleNumber = $currentCycle ? $currentCycle->cycle_number + 1 : 1;
            $previousEndDate = $currentCycle ? $currentCycle->cycle_end_date : now();
            $newStartDate = $previousEndDate->copy()->startOfDay();
            $newEndDate = $newStartDate->copy()->addDays(30);

            // Use cumulative price (total cost up to this cycle number)
            $price = $pricingTier->getCumulativePriceUpToCycle($nextCycleNumber);
            $tokenLimit = $this->pricingService->getMonthlyTokenLimit($pricingTier);

            $newCycle = SubscriptionCycle::create([
                'user_id' => $user->id,
                'pricing_tier_id' => $pricingTier->id,
                'cycle_number' => $nextCycleNumber,
                'cycle_start_date' => $newStartDate,
                'cycle_end_date' => $newEndDate,
                'tokens_allocated' => $tokenLimit,
                'tokens_used' => 0,
                'current_price' => $price,
                'status' => 'active',
            ]);

            Log::info('New subscription cycle created after expiration', [
                'user_id' => $user->id,
                'cycle_id' => $newCycle->id,
                'cycle_number' => $nextCycleNumber,
                'token_limit' => $tokenLimit,
                'cumulative_price' => $price,
                'cycle_start' => $newStartDate->toDateString(),
                'cycle_end' => $newEndDate->toDateString(),
            ]);

            return $newCycle;
        });
    }

    /**
     * Get all subscription cycles for a user within a date range
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getUserCyclesInRange(User $user, Carbon $startDate, Carbon $endDate)
    {
        return $user->subscriptionCycles()
            ->whereBetween('cycle_start_date', [$startDate, $endDate])
            ->orderBy('cycle_number')
            ->get();
    }

    /**
     * Get statistics for a user's current cycle
     */
    public function getCurrentCycleStats(User $user): ?array
    {
        $cycle = $this->getCurrentActiveCycle($user);

        if (! $cycle) {
            return null;
        }

        return [
            'cycle_id' => $cycle->id,
            'cycle_number' => $cycle->cycle_number,
            'tokens_allocated' => $cycle->tokens_allocated,
            'tokens_used' => $cycle->tokens_used,
            'tokens_remaining' => $cycle->getTokensRemainingAttribute(),
            'usage_percentage' => $cycle->usage_percentage,
            'remaining_percentage' => 100 - $cycle->usage_percentage,
            'current_price' => $cycle->current_price,
            'cycle_start_date' => $cycle->cycle_start_date,
            'cycle_end_date' => $cycle->cycle_end_date,
            'days_remaining' => now()->diffInDays($cycle->cycle_end_date, false),
            'is_nearing_depletion' => $cycle->isNearingDepletion(),
        ];
    }

    /**
     * Check if a user has enough tokens in current cycle
     */
    public function hasAvailableTokens(User $user, int $requiredTokens = 1): bool
    {
        $cycle = $this->getCurrentActiveCycle($user);

        return $cycle ? $cycle->hasTokens($requiredTokens) : false;
    }

    /**
     * Deduct tokens from user's current cycle
     */
    public function deductTokens(User $user, int $tokens): bool
    {
        $cycle = $this->getCurrentActiveCycle($user);

        if (! $cycle) {
            Log::warning('No active subscription cycle found', [
                'user_id' => $user->id,
                'tokens_requested' => $tokens,
            ]);

            return false;
        }

        $success = $cycle->deductTokens($tokens);

        if ($success) {
            Log::info('Tokens deducted from cycle', [
                'user_id' => $user->id,
                'cycle_id' => $cycle->id,
                'tokens_deducted' => $tokens,
                'tokens_remaining' => $cycle->tokens_remaining,
            ]);
        } else {
            Log::warning('Insufficient tokens in cycle', [
                'user_id' => $user->id,
                'cycle_id' => $cycle->id,
                'tokens_available' => $cycle->getTokensRemainingAttribute(),
                'tokens_requested' => $tokens,
            ]);
        }

        return $success;
    }
}
