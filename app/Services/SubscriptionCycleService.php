<?php

namespace App\Services;

use App\Models\Chat\PricingTier;
use App\Models\Chat\SubscriptionCycle;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class SubscriptionCycleService
{
    protected IncrementalPricingService $pricingService;

    public function __construct(IncrementalPricingService $pricingService)
    {
        $this->pricingService = $pricingService;
    }

    /**
     * Create subscription cycles for a multi-month purchase
     * Handles merging with existing cycles if they overlap
     */
    public function createSubscriptionCycles(User $user, PricingTier $pricingTier, int $months, bool $isPending = false): array
    {
        return DB::transaction(function () use ($user, $pricingTier, $months, $isPending) {
            $groupId = Str::uuid()->toString();
            $startDate = now()->startOfDay();
            $cycles = [];

            // Get existing future cycles
            $existingCycles = $user->subscriptionCycles()
                ->where('status', '!=', 'expired')
                ->orderBy('cycle_start_date')
                ->get();

            for ($i = 1; $i <= $months; $i++) {
                // Use 30-day periods to match existing cycle calculation
                $cycleStart = $startDate->copy()->addDays(($i - 1) * 30);
                $cycleEnd = $cycleStart->copy()->addDays(30)->subSecond();

                // Check if there's an overlapping existing cycle
                $overlappingCycle = $existingCycles->first(function ($cycle) use ($cycleStart, $cycleEnd) {
                    return $cycle->cycle_start_date->between($cycleStart, $cycleEnd) ||
                           $cycle->cycle_end_date->between($cycleStart, $cycleEnd) ||
                           ($cycleStart->between($cycle->cycle_start_date, $cycle->cycle_end_date));
                });

                if ($overlappingCycle) {
                    // Merge: combine tokens and use new tier's model
                    $cycles[] = $this->mergeCycles($overlappingCycle, $pricingTier, $groupId, $i);
                } else {
                    // Create new cycle
                    $cycles[] = $this->createNewCycle($user, $pricingTier, $groupId, $i, $cycleStart, $cycleEnd, $isPending);
                }
            }

            return $cycles;
        });
    }

    /**
     * Merge an existing cycle with a new subscription
     */
    protected function mergeCycles(SubscriptionCycle $existingCycle, PricingTier $newTier, string $newGroupId, int $newCycleNumber): SubscriptionCycle
    {
        $oldTier = $existingCycle->pricingTier;
        $oldGroupId = $existingCycle->subscription_group_id;

        // Get the ORIGINAL base tokens from the old tier (not the current allocated amount)
        $oldBaseTokens = $existingCycle->is_merged
            ? $oldTier->monthly_token_limit
            : $existingCycle->getBaseTokensAllocated();

        $oldTopupTokens = $existingCycle->topup_tokens_allocated;
        $newBaseTokens = $newTier->monthly_token_limit;

        $combinedTokens = $oldBaseTokens + $newBaseTokens + $oldTopupTokens;

        // Calculate combined price: add the NEW cycle's increment to existing price
        $newCycleIncrement = $newTier->getMonthlyPriceIncrement($newCycleNumber);
        $combinedPrice = $existingCycle->current_price + $newCycleIncrement;

        // Update existing cycle with merged data
        $existingCycle->update([
            'pricing_tier_id' => $newTier->id,
            'tokens_allocated' => $combinedTokens,
            'topup_tokens_allocated' => $oldTopupTokens,
            'current_price' => $combinedPrice,
            'merged_with_group_id' => $newGroupId,
            'is_merged' => true,
        ]);

        Log::info('Merged subscription cycles', [
            'existing_cycle_id' => $existingCycle->id,
            'old_group_id' => $oldGroupId,
            'new_group_id' => $newGroupId,
            'old_tokens' => $oldBaseTokens,
            'new_tokens' => $newBaseTokens,
            'combined_tokens' => $combinedTokens,
            'old_price' => $existingCycle->current_price - $newCycleIncrement,
            'new_cycle_increment' => $newCycleIncrement,
            'combined_price' => $combinedPrice,
            'old_tier' => $oldTier->name,
            'new_tier' => $newTier->name,
        ]);

        return $existingCycle;
    }

    /**
     * Create a new subscription cycle
     */
    protected function createNewCycle(User $user, PricingTier $pricingTier, string $groupId, int $cycleNumber, $startDate, $endDate, bool $isPending = false): SubscriptionCycle
    {
        // Use PricingTier's method to get correct cumulative price
        $cumulativePrice = $pricingTier->getCumulativePriceUpToCycle($cycleNumber);

        // If pending payment, always create as inactive with 0 tokens
        $status = 'inactive';
        $tokensAllocated = 0;

        if (! $isPending) {
            // Only check if should be active when not pending payment
            $isCurrentCycle = now()->between($startDate, $endDate);
            $status = $isCurrentCycle ? 'active' : 'inactive';
            $tokensAllocated = $pricingTier->monthly_token_limit;
        }

        return SubscriptionCycle::create([
            'user_id' => $user->id,
            'pricing_tier_id' => $pricingTier->id,
            'subscription_group_id' => $groupId,
            'cycle_number' => $cycleNumber,
            'cycle_start_date' => $startDate,
            'cycle_end_date' => $endDate,
            'tokens_allocated' => $tokensAllocated,
            'topup_tokens_allocated' => 0,
            'tokens_used' => 0,
            'current_price' => $cumulativePrice,
            'status' => $status,
            'is_topup' => false,
            'is_merged' => false,
        ]);
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

            // Determine if this cycle should be active (is within current date range)
            $isCurrentCycle = now()->between($startDate, $endDate);
            $status = $isCurrentCycle ? 'active' : 'inactive';

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
                'status' => $status,
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

            // Determine if this cycle should be active (is within current date range)
            $isCurrentCycle = now()->between($cycleStartDate, $cycleEndDate);
            $status = $isCurrentCycle ? 'active' : 'inactive';

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
                'status' => $status,
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
            ->latest('cycle_number')
            ->first();
    }

    /**
     * Get the next upcoming cycle for a user
     */
    public function getNextUpcomingCycle(User $user): ?SubscriptionCycle
    {
        return $user->subscriptionCycles()
            ->where('status', 'inactive')
            ->where('cycle_start_date', '>', now())
            ->oldest('cycle_start_date')
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

            // Determine if this cycle should be active (is within current date range)
            $isCurrentCycle = now()->between($newStartDate, $newEndDate);
            $status = $isCurrentCycle ? 'active' : 'inactive';

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
                'status' => $status,
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

    /**
     * Activate pending cycles after payment confirmation
     */
    public function activatePendingCycles(string $groupId, PricingTier $pricingTier): int
    {
        $cycles = SubscriptionCycle::where('subscription_group_id', $groupId)
            ->orWhere('merged_with_group_id', $groupId)
            ->get();

        $activatedCount = 0;
        foreach ($cycles as $cycle) {
            $tokensAdded = false;

            // New cycles from this subscription (not merged)
            if ($cycle->subscription_group_id === $groupId && $cycle->tokens_allocated == 0) {
                $cycle->tokens_allocated = $pricingTier->monthly_token_limit;
                $tokensAdded = true;
            }

            // Merged cycles - add new subscription's tokens
            if ($cycle->merged_with_group_id === $groupId && $cycle->is_merged) {
                $cycle->tokens_allocated += $pricingTier->monthly_token_limit;
                $tokensAdded = true;
            }

            $isCurrentCycle = now()->between($cycle->cycle_start_date, $cycle->cycle_end_date);
            if ($isCurrentCycle && $cycle->status !== 'active') {
                $cycle->status = 'active';
                $activatedCount++;
            }

            if ($tokensAdded || $cycle->isDirty('status')) {
                $cycle->save();
            }
        }

        if ($activatedCount > 0) {
            Log::info('Pending cycles activated after payment', [
                'group_id' => $groupId,
                'count' => $activatedCount,
                'pricing_tier_id' => $pricingTier->id,
                'tokens_per_cycle' => $pricingTier->monthly_token_limit,
            ]);
        }

        return $activatedCount;
    }

    /**
     * Activate cycles that have reached their start date
     * This is typically called by a scheduled job to activate upcoming cycles
     * Returns the number of cycles activated
     */
    public function activateCyclesDueForActivation(): int
    {
        $activatedCount = SubscriptionCycle::where('status', 'inactive')
            ->where('cycle_start_date', '<=', now())
            ->where('cycle_end_date', '>=', now())
            ->update(['status' => 'active']);

        if ($activatedCount > 0) {
            Log::info('Subscription cycles activated', [
                'count' => $activatedCount,
                'timestamp' => now()->toDateTimeString(),
            ]);
        }

        return $activatedCount;
    }

    /**
     * Deactivate cycles that have passed their end date
     * This is typically called by a scheduled job to expire cycles
     * Returns the number of cycles deactivated
     */
    public function deactivateCyclesPastEnd(): int
    {
        $deactivatedCount = SubscriptionCycle::where('status', 'active')
            ->where('cycle_end_date', '<', now())
            ->update(['status' => 'expired']);

        if ($deactivatedCount > 0) {
            Log::info('Subscription cycles expired', [
                'count' => $deactivatedCount,
                'timestamp' => now()->toDateTimeString(),
            ]);
        }

        return $deactivatedCount;
    }
}
