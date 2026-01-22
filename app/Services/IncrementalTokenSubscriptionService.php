<?php

namespace App\Services;

use App\Models\Chat\PricingTier;
use App\Models\Chat\UserTokenSubscription;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Extension to TokenSubscriptionService that integrates with IncrementalPricingService
 *
 * This service handles creating subscriptions with incremental pricing models
 * where the cost changes after an initial period.
 */
class IncrementalTokenSubscriptionService
{
    protected IncrementalPricingService $pricingService;

    protected SubscriptionCycleService $cycleService;

    public function __construct(
        IncrementalPricingService $pricingService,
        SubscriptionCycleService $cycleService
    ) {
        $this->pricingService = $pricingService;
        $this->cycleService = $cycleService;
    }

    /**
     * Create a new incremental pricing subscription
     *
     * This creates both a UserTokenSubscription and initializes subscription cycles
     * for monthly token resets.
     */
    public function createIncrementalSubscription(
        User $user,
        PricingTier $pricingTier
    ): UserTokenSubscription {
        return DB::transaction(function () use ($user, $pricingTier) {
            $now = now();

            Log::info('Creating incremental pricing subscription', [
                'user_id' => $user->id,
                'pricing_tier' => $pricingTier->name,
                'initial_price' => $pricingTier->initial_price,
                'subsequent_price' => $pricingTier->subsequent_price,
            ]);

            // Create the main token subscription record
            $subscription = UserTokenSubscription::create([
                'user_id' => $user->id,
                'package_id' => $pricingTier->pricing_tier_id,
                'tokens_purchased' => $pricingTier->monthly_token_limit,
                'tokens_used' => 0,
                'tokens_remaining' => $pricingTier->monthly_token_limit,
                'status' => 'active',
                'purchased_at' => $now,
                'activated_at' => $now,
                'expires_at' => null, // No expiration for incremental subscriptions
                'action_type' => 'purchase',
            ]);

            // Initialize the first subscription cycle
            $this->cycleService->initializeSubscriptionCycles($user, $pricingTier, $now);

            Log::info('Incremental pricing subscription created', [
                'subscription_id' => $subscription->id,
                'user_id' => $user->id,
            ]);

            return $subscription;
        });
    }

    /**
     * Upgrade subscription from one pricing tier to another
     *
     * This handles tier upgrades while preserving the subscription start date
     * to maintain consistent pricing periods.
     */
    public function upgradeSubscription(
        User $user,
        PricingTier $newPricingTier,
        UserTokenSubscription $currentSubscription
    ): UserTokenSubscription {
        return DB::transaction(function () use ($user, $newPricingTier, $currentSubscription) {
            Log::info('Upgrading subscription to new pricing tier', [
                'user_id' => $user->id,
                'from_tier' => $currentSubscription->package->pricingTier?->name ?? 'Unknown',
                'to_tier' => $newPricingTier->name,
            ]);

            // Get current active cycle
            $currentCycle = $this->cycleService->getCurrentActiveCycle($user);

            // Deactivate current subscription
            $currentSubscription->deactivate(\App\Support\TokenSubscriptionStatus::REPLACED);
            $currentSubscription->save();

            // Create new subscription with new tier
            $newSubscription = UserTokenSubscription::create([
                'user_id' => $user->id,
                'package_id' => $newPricingTier->pricing_tier_id,
                'tokens_purchased' => $newPricingTier->monthly_token_limit,
                'tokens_used' => 0,
                'tokens_remaining' => $newPricingTier->monthly_token_limit,
                'status' => 'active',
                'purchased_at' => now(),
                'activated_at' => now(),
                'action_type' => 'upgrade',
                'replaced_by_id' => $currentSubscription->id,
            ]);

            // If there's an active cycle, update it to use new tier
            if ($currentCycle) {
                $currentCycle->update([
                    'pricing_tier_id' => $newPricingTier->id,
                    'tokens_allocated' => $newPricingTier->monthly_token_limit,
                    'tokens_remaining' => $newPricingTier->monthly_token_limit,
                    'current_price' => $newPricingTier->initial_price, // Use initial price for upgrade
                ]);

                Log::info('Current cycle updated for tier upgrade', [
                    'cycle_id' => $currentCycle->id,
                    'new_tier' => $newPricingTier->name,
                ]);
            }

            Log::info('Subscription upgraded', [
                'old_subscription_id' => $currentSubscription->id,
                'new_subscription_id' => $newSubscription->id,
            ]);

            return $newSubscription;
        });
    }

    /**
     * Downgrade subscription from one pricing tier to another
     *
     * Downgrades take effect in the next billing cycle.
     */
    public function downgradeSubscription(
        User $user,
        PricingTier $newPricingTier,
        UserTokenSubscription $currentSubscription
    ): UserTokenSubscription {
        return DB::transaction(function () use ($user, $newPricingTier, $currentSubscription) {
            Log::info('Downgrading subscription to new pricing tier', [
                'user_id' => $user->id,
                'from_tier' => $currentSubscription->package->pricingTier?->name ?? 'Unknown',
                'to_tier' => $newPricingTier->name,
            ]);

            // For downgrades, we typically want to apply immediately but mark for review
            $newSubscription = UserTokenSubscription::create([
                'user_id' => $user->id,
                'package_id' => $newPricingTier->pricing_tier_id,
                'tokens_purchased' => $newPricingTier->monthly_token_limit,
                'tokens_used' => 0,
                'tokens_remaining' => $newPricingTier->monthly_token_limit,
                'status' => 'active',
                'purchased_at' => now(),
                'activated_at' => now(),
                'action_type' => 'downgrade',
                'replaced_by_id' => $currentSubscription->id,
            ]);

            // Deactivate old subscription
            $currentSubscription->deactivate(\App\Support\TokenSubscriptionStatus::REPLACED);
            $currentSubscription->save();

            Log::info('Subscription downgraded', [
                'old_subscription_id' => $currentSubscription->id,
                'new_subscription_id' => $newSubscription->id,
            ]);

            return $newSubscription;
        });
    }

    /**
     * Get subscription pricing information for display
     */
    public function getSubscriptionInfo(User $user, UserTokenSubscription $subscription): array
    {
        $pricingTier = $subscription->package?->pricingTier;

        if (! $pricingTier) {
            return [];
        }

        $currentCycle = $this->cycleService->getCurrentActiveCycle($user);
        $startDate = $subscription->activated_at ?? $subscription->purchased_at ?? now();

        return [
            'subscription_id' => $subscription->id,
            'pricing_tier' => $pricingTier->name,
            'subscription_start_date' => $startDate,
            'current_cycle' => $currentCycle ? $currentCycle->cycle_number : null,
            'pricing_summary' => $this->pricingService->getPricingSummary($pricingTier, $startDate),
            'current_cycle_stats' => $this->cycleService->getCurrentCycleStats($user),
            'total_cost_so_far' => $this->pricingService->calculateTotalCost($user, $pricingTier, $startDate),
        ];
    }

    /**
     * Check if user has tokens available in current cycle
     */
    public function hasAvailableTokens(User $user, int $requiredTokens = 1): bool
    {
        return $this->cycleService->hasAvailableTokens($user, $requiredTokens);
    }

    /**
     * Deduct tokens from user's current cycle
     */
    public function deductTokens(User $user, int $tokens): bool
    {
        return $this->cycleService->deductTokens($user, $tokens);
    }

    /**
     * Get current pricing for a tier based on subscription date
     */
    public function getCurrentMonthlyPrice(PricingTier $pricingTier, User $user, ?Carbon $subscriptionStartDate = null): float
    {
        $startDate = $subscriptionStartDate ?? now();

        return $this->pricingService->getCurrentPrice($user, $pricingTier, $startDate);
    }
}
