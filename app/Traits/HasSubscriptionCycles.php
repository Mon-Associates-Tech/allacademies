<?php

namespace App\Traits;

use App\Models\Chat\SubscriptionCycle;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * Trait HasSubscriptionCycles
 *
 * Handles subscription cycle relationships and methods for the User model.
 * This trait manages all subscription cycle operations keeping the User model lean.
 */
trait HasSubscriptionCycles
{
    /**
     * Get the currently active subscription cycle
     */
    public function activeSubscriptionCycle(): HasOne
    {
        return $this->hasOne(SubscriptionCycle::class, 'user_id')
            ->where('status', 'active')
            ->where('cycle_end_date', '>=', now())
            ->latest('cycle_number');
    }

    /**
     * Get the next upcoming subscription cycle
     */
    public function nextSubscriptionCycle(): HasOne
    {
        return $this->hasOne(SubscriptionCycle::class, 'user_id')
            ->where('status', 'inactive')
            ->where('cycle_start_date', '>', now())
            ->oldest('cycle_start_date');
    }

    /**
     * Get the next upcoming cycle (actual instance, not relation)
     */
    public function getNextUpcomingCycle(): ?SubscriptionCycle
    {
        return $this->subscriptionCycles()
            ->where('status', 'inactive')
            ->where('cycle_start_date', '>', now())
            ->oldest('cycle_start_date')
            ->first();
    }

    /**
     * Get all subscription cycles for this user
     */
    public function subscriptionCycles(): HasMany
    {
        return $this->hasMany(SubscriptionCycle::class, 'user_id');
    }

    /**
     * Check if user has an active subscription cycle
     */
    public function hasActiveSubscriptionCycle(): bool
    {
        return $this->getCurrentActiveCycle() !== null;
    }

    /**
     * Get the current active cycle (actual instance, not relation)
     */
    public function getCurrentActiveCycle(): ?SubscriptionCycle
    {
        return $this->subscriptionCycles()
            ->where('status', 'active')
            ->where('cycle_end_date', '>=', now())
            ->latest('cycle_number')
            ->first();
    }

    /**
     * Check if user has available tokens in current cycle
     */
    public function hasAvailableTokens(int $requiredTokens = 1): bool
    {
        $cycle = $this->getCurrentActiveCycle();

        return $cycle && $cycle->hasTokens($requiredTokens);
    }

    /**
     * Get all cycles in a given date range
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getCyclesInRange(\Carbon\Carbon $startDate, \Carbon\Carbon $endDate)
    {
        return $this->subscriptionCycles()
            ->whereBetween('cycle_start_date', [$startDate, $endDate])
            ->orderBy('cycle_number')
            ->get();
    }

    /**
     * Get all expired cycles
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getExpiredCycles()
    {
        return $this->subscriptionCycles()
            ->where('status', 'expired')
            ->orderBy('cycle_number', 'desc')
            ->get();
    }

    /**
     * Get total remaining tokens across all active cycles
     */
    public function getTotalRemainingTokens(): int
    {
        return $this->getTotalAllocatedTokens() - $this->getTotalUsedTokens();
    }

    /**
     * Get total tokens allocated across all active cycles
     */
    public function getTotalAllocatedTokens(): int
    {
        return $this->subscriptionCycles()
            ->where('status', 'active')
            ->sum('tokens_allocated');
    }

    /**
     * Get total tokens used across all active cycles
     */
    public function getTotalUsedTokens(): int
    {
        return $this->subscriptionCycles()
            ->where('status', 'active')
            ->sum('tokens_used');
    }
}
