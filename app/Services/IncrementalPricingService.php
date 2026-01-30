<?php

namespace App\Services;

use App\Models\Chat\PricingTier;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class IncrementalPricingService
{
    /**
     * Get the current price for a user's subscription
     * Determines whether user is in initial or subsequent pricing period
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
     */
    public function getMonthlyTokenLimit(PricingTier $pricingTier): int
    {
        return $pricingTier->monthly_token_limit;
    }

    /**
     * Calculate the number of months elapsed since subscription start
     */
    public function getMonthsElapsed(Carbon $subscriptionStartDate): int
    {
        return now()->diffInMonths($subscriptionStartDate);
    }

    /**
     * Determine if a user is in the initial pricing period
     */
    public function isInInitialPeriod(Carbon $subscriptionStartDate, int $initialPeriodMonths = 6): bool
    {
        return $this->getMonthsElapsed($subscriptionStartDate) < $initialPeriodMonths;
    }

    /**
     * Get the number of the current cycle (starting from 1)
     */
    public function getCurrentCycleNumber(Carbon $subscriptionStartDate): int
    {
        return $this->getMonthsElapsed($subscriptionStartDate) + 1;
    }

    /**
     * Calculate price for a specific cycle (monthly increment only)
     * Month 1-6: uses initial_price
     * Month 7+: uses subsequent_price
     */
    public function getPriceForCycle(PricingTier $pricingTier, int $cycleNumber): float
    {
        return $pricingTier->getMonthlyPriceIncrement($cycleNumber);
    }

    /**
     * Get cumulative total price up to and including a specific cycle
     * Example: Basic $10 initial, $5 subsequent
     * Cycle 1: $10
     * Cycle 2: $20
     * Cycle 6: $60
     * Cycle 7: $65 (60 + 5)
     * Cycle 8: $70 (65 + 5)
     */
    public function getCumulativePriceForCycle(PricingTier $pricingTier, int $cycleNumber): float
    {
        return $pricingTier->getCumulativePriceUpToCycle($cycleNumber);
    }

    /**
     * Get total cost for a subscription period
     * Sums up all monthly increments from subscription start to now
     */
    public function calculateTotalCost(User $user, PricingTier $pricingTier, Carbon $subscriptionStartDate): float
    {
        $totalCost = 0.0;
        $cyclesCompleted = $this->getCurrentCycleNumber($subscriptionStartDate) - 1;

        for ($cycle = 1; $cycle <= $cyclesCompleted; $cycle++) {
            $totalCost += $pricingTier->getMonthlyPriceIncrement($cycle);
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
     * Get cumulative cost up to a specific cycle
     * Returns the total amount paid including all cycles up to cycleNumber
     */
    public function getCumulativeCost(PricingTier $pricingTier, int $cycleNumber): float
    {
        return $pricingTier->getCumulativePriceUpToCycle($cycleNumber);
    }

    /**
     * Get summary of pricing for display
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
