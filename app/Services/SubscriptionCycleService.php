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
     *
     * @param User $user
     * @param PricingTier $pricingTier
     * @param Carbon $startDate
     * @param int $cycleNumber
     * @return SubscriptionCycle
     */
    public function createCycle(User $user, PricingTier $pricingTier, Carbon $startDate, int $cycleNumber): SubscriptionCycle
    {
        return DB::transaction(function () use ($user, $pricingTier, $startDate, $cycleNumber) {
            $endDate = $startDate->copy()->addMonth();
            $price = $this->pricingService->getPriceForCycle($pricingTier, $cycleNumber);
            $tokenLimit = $this->pricingService->getMonthlyTokenLimit($pricingTier);

            $cycle = SubscriptionCycle::create([
                'user_id' => $user->id,
                'pricing_tier_id' => $pricingTier->id,
                'cycle_number' => $cycleNumber,
                'cycle_start_date' => $startDate,
                'cycle_end_date' => $endDate,
                'tokens_allocated' => $tokenLimit,
                'tokens_used' => 0,
                'current_price' => $price,
                'status' => 'active',
            ]);

            Log::info('Subscription cycle created', [
                'user_id' => $user->id,
                'cycle_id' => $cycle->id,
                'cycle_number' => $cycleNumber,
                'pricing_tier' => $pricingTier->name,
                'token_limit' => $tokenLimit,
                'price' => $price,
            ]);

            return $cycle;
        });
    }

    /**
     * Create initial cycle(s) for a new subscription
     * If subscription starts mid-month, create prorated first cycle
     *
     * @param User $user
     * @param PricingTier $pricingTier
     * @param Carbon $subscriptionStartDate
     * @return SubscriptionCycle
     */
    public function initializeSubscriptionCycles(User $user, PricingTier $pricingTier, Carbon $subscriptionStartDate): SubscriptionCycle
    {
        return DB::transaction(function () use ($user, $pricingTier, $subscriptionStartDate) {
            // Calculate when the next month starts (for first cycle end date)
            $cycleStartDate = $subscriptionStartDate->copy()->startOfDay();
            $cycleEndDate = $subscriptionStartDate->copy()->endOfMonth();

            $price = $this->pricingService->getPriceForCycle($pricingTier, 1);
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
            ]);

            return $cycle;
        });
    }

    /**
     * Get the current active cycle for a user
     *
     * @param User $user
     * @return SubscriptionCycle|null
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
     *
     * @param User $user
     * @return SubscriptionCycle|null
     */
    public function getNextUpcomingCycle(User $user): ?SubscriptionCycle
    {
        return $user->subscriptionCycles()
            ->where('cycle_start_date', '>', now())
            ->orderBy('cycle_start_date')
            ->first();
    }

    /**
     * Reset monthly cycle for a user
     * Marks current cycle as expired and creates a new one
     *
     * @param User $user
     * @param PricingTier $pricingTier
     * @return SubscriptionCycle
     */
    public function resetMonthlyTokens(User $user, PricingTier $pricingTier): SubscriptionCycle
    {
        return DB::transaction(function () use ($user, $pricingTier) {
            $currentCycle = $this->getCurrentActiveCycle($user);

            if ($currentCycle) {
                // Mark current cycle as expired
                $currentCycle->update(['status' => 'expired']);

                Log::info('Previous subscription cycle marked as expired', [
                    'user_id' => $user->id,
                    'cycle_id' => $currentCycle->id,
                    'cycle_number' => $currentCycle->cycle_number,
                ]);
            }

            // Create new cycle
            $nextCycleNumber = $currentCycle ? $currentCycle->cycle_number + 1 : 1;
            $newStartDate = now()->startOfDay();
            $newEndDate = now()->endOfMonth();

            $price = $this->pricingService->getPriceForCycle($pricingTier, $nextCycleNumber);
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

            Log::info('New subscription cycle created', [
                'user_id' => $user->id,
                'cycle_id' => $newCycle->id,
                'cycle_number' => $nextCycleNumber,
                'token_limit' => $tokenLimit,
                'price' => $price,
            ]);

            return $newCycle;
        });
    }

    /**
     * Get all subscription cycles for a user within a date range
     *
     * @param User $user
     * @param Carbon $startDate
     * @param Carbon $endDate
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
     *
     * @param User $user
     * @return array|null
     */
    public function getCurrentCycleStats(User $user): ?array
    {
        $cycle = $this->getCurrentActiveCycle($user);

        if (!$cycle) {
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
     *
     * @param User $user
     * @param int $requiredTokens
     * @return bool
     */
    public function hasAvailableTokens(User $user, int $requiredTokens = 1): bool
    {
        $cycle = $this->getCurrentActiveCycle($user);
        return $cycle ? $cycle->hasTokens($requiredTokens) : false;
    }

    /**
     * Deduct tokens from user's current cycle
     *
     * @param User $user
     * @param int $tokens
     * @return bool
     */
    public function deductTokens(User $user, int $tokens): bool
    {
        $cycle = $this->getCurrentActiveCycle($user);

        if (!$cycle) {
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
