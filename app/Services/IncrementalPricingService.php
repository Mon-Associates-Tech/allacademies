<?php

namespace App\Services;

use App\Models\Chat\PricingTier;
use App\Models\Chat\SubscriptionCycle;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class IncrementalPricingService
{
    /**
     * Get the current price for a user's subscription
     * Determines whether user is in initial or subsequent pricing period
     *
     * @param User $user
     * @param PricingTier $pricingTier
     * @param Carbon $subscriptionStartDate
     * @return float
     */
    public function getCurrentPrice(User $user, PricingTier $pricingTier, Carbon $subscriptionStartDate): float
    {
        if ($pricingTier->isInInitialPeriod($subscriptionStartDate)) {
            Log::info('User in initial pricing period', [
                'user_id' => $user->id,
                'pricing_tier' => $pricingTier->name,
                'price' => $pricingTier->initial_price,
            ]);
            return (float) $pricingTier->initial_price;
        }

        Log::info('User in subsequent pricing period', [
            'user_id' => $user->id,
            'pricing_tier' => $pricingTier->name,
            'price' => $pricingTier->subsequent_price,
        ]);
        return (float) $pricingTier->subsequent_price;
    }

    /**
     * Get the monthly token limit for a pricing tier
     *
     * @param PricingTier $pricingTier
     * @return int
     */
    public function getMonthlyTokenLimit(PricingTier $pricingTier): int
    {
        return $pricingTier->monthly_token_limit;
    }

    /**
     * Calculate the number of months elapsed since subscription start
     *
     * @param Carbon $subscriptionStartDate
     * @return int
     */
    public function getMonthsElapsed(Carbon $subscriptionStartDate): int
    {
        return now()->diffInMonths($subscriptionStartDate);
    }

    /**
     * Determine if a user is in the initial pricing period
     *
     * @param Carbon $subscriptionStartDate
     * @param int $initialPeriodMonths
     * @return bool
     */
    public function isInInitialPeriod(Carbon $subscriptionStartDate, int $initialPeriodMonths = 6): bool
    {
        return $this->getMonthsElapsed($subscriptionStartDate) < $initialPeriodMonths;
    }

    /**
     * Get the number of the current cycle (starting from 1)
     *
     * @param Carbon $subscriptionStartDate
     * @return int
     */
    public function getCurrentCycleNumber(Carbon $subscriptionStartDate): int
    {
        return $this->getMonthsElapsed($subscriptionStartDate) + 1;
    }

    /**
     * Calculate price for a specific month/cycle
     *
     * @param PricingTier $pricingTier
     * @param int $cycleNumber
     * @return float
     */
    public function getPriceForCycle(PricingTier $pricingTier, int $cycleNumber): float
    {
        if ($cycleNumber <= $pricingTier->initial_period_months) {
            return (float) $pricingTier->initial_price;
        }

        return (float) $pricingTier->subsequent_price;
    }

    /**
     * Get total cost for a subscription period
     * Sums up all cycles from subscription start to now
     *
     * @param User $user
     * @param PricingTier $pricingTier
     * @param Carbon $subscriptionStartDate
     * @return float
     */
    public function calculateTotalCost(User $user, PricingTier $pricingTier, Carbon $subscriptionStartDate): float
    {
        $totalCost = 0.0;
        $cyclesCompleted = $this->getCurrentCycleNumber($subscriptionStartDate) - 1;

        for ($cycle = 1; $cycle <= $cyclesCompleted; $cycle++) {
            $totalCost += $this->getPriceForCycle($pricingTier, $cycle);
        }

        Log::info('Total cost calculated', [
            'user_id' => $user->id,
            'pricing_tier' => $pricingTier->name,
            'cycles_completed' => $cyclesCompleted,
            'total_cost' => $totalCost,
        ]);

        return round($totalCost, 2);
    }

    /**
     * Get summary of pricing for display
     *
     * @param PricingTier $pricingTier
     * @param Carbon $subscriptionStartDate
     * @return array
     */
    public function getPricingSummary(PricingTier $pricingTier, Carbon $subscriptionStartDate): array
    {
        $monthsElapsed = $this->getMonthsElapsed($subscriptionStartDate);
        $currentCycle = $this->getCurrentCycleNumber($subscriptionStartDate);
        $isInitialPeriod = $this->isInInitialPeriod($subscriptionStartDate, $pricingTier->initial_period_months);
        $monthsUntilChangePrice = $isInitialPeriod 
            ? $pricingTier->initial_period_months - $monthsElapsed 
            : 0;

        return [
            'pricing_tier' => $pricingTier->name,
            'current_cycle' => $currentCycle,
            'months_elapsed' => $monthsElapsed,
            'is_initial_period' => $isInitialPeriod,
            'current_price' => $this->getPriceForCycle($pricingTier, $currentCycle),
            'initial_price' => $pricingTier->initial_price,
            'subsequent_price' => $pricingTier->subsequent_price,
            'initial_period_months' => $pricingTier->initial_period_months,
            'months_until_price_change' => $monthsUntilChangePrice,
            'monthly_token_limit' => $pricingTier->monthly_token_limit,
        ];
    }
}
