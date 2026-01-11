<?php

namespace App\Services;

use App\Models\Chat\OpenAiTokenPackage;
use App\Models\Chat\UserTokenSubscription;
use App\Models\User;
use App\Support\TokenSubscriptionStatus;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TokenSubscriptionService
{
    /**
     * Add tokens to user's account (top-up or replace)
     */
    public function changeSubscription(User $user, OpenAiTokenPackage $newPackage, bool $isTopUp = true): UserTokenSubscription
    {
        return DB::transaction(function () use ($user, $newPackage, $isTopUp) {
            // Get current active subscription
            $currentSubscription = $user->activeTokenSubscription;

            Log::info('changeSubscription called', [
                'user_id' => $user->id,
                'new_package_id' => $newPackage->id,
                'new_package_tokens' => $newPackage->token_limit,
                'is_top_up' => $isTopUp,
                'current_subscription_id' => $currentSubscription?->id,
                'current_status' => $currentSubscription?->status,
                'current_tokens_remaining' => $currentSubscription?->tokens_remaining,
                'current_action_type' => $currentSubscription?->action_type,
            ]);

            // If topping up and current subscription is active and usable
            // FIX: Compare enum with enum, not string
            if ($isTopUp && $currentSubscription &&
                $currentSubscription->status === TokenSubscriptionStatus::ACTIVE &&
                !$currentSubscription->isExpired() &&
                $currentSubscription->tokens_remaining > 0 &&
                $currentSubscription->action_type !== 'trial') {

                Log::info('Creating top-up purchase record');

                // Create a pending "purchase" record for payment tracking ONLY
                $purchaseRecord = UserTokenSubscription::create([
                    'user_id' => $user->id,
                    'package_id' => $newPackage->id,
                    'tokens_purchased' => $newPackage->token_limit,
                    'tokens_used' => 0,
                    'tokens_remaining' => $newPackage->token_limit,
                    'status' => TokenSubscriptionStatus::PENDING,
                    'purchased_at' => null,
                    'action_type' => 'purchase',
                    'replaced_by_id' => $currentSubscription->id,
                ]);

                Log::info('Top-up purchase record created', [
                    'purchase_record_id' => $purchaseRecord->id,
                    'linked_to_subscription_id' => $currentSubscription->id,
                ]);

                return $purchaseRecord;
            }

            Log::info('Creating new subscription (not a top-up)');

            // Otherwise, create new subscription and replace old one
            return $this->replaceSubscription($user, $currentSubscription, $newPackage, true);
        });
    }

    /**
     * Replace an existing subscription with a new one
     */
    protected function replaceSubscription(
        User                   $user,
        ?UserTokenSubscription $currentSubscription,
        OpenAiTokenPackage     $newPackage,
        bool                   $preserveTokens = true
    ): UserTokenSubscription
    {
        // Free packages should NEVER go through this method
        // They should be created directly via User::createFreeTrialSubscription()
        if ($newPackage->isFree()) {
            throw new \Exception('Free trial packages should not go through payment flow. Use User::createFreeTrialSubscription() instead.');
        }

        $actionType = $this->determineActionType($currentSubscription, $newPackage);
        $newTokens = $newPackage->token_limit;
        $carryOverTokens = 0;

        // FIX: Compare enum with enum
        if ($preserveTokens && $currentSubscription &&
            $currentSubscription->status === TokenSubscriptionStatus::ACTIVE &&
            !$currentSubscription->isExpired() &&
            $currentSubscription->tokens_remaining > 0) {

            $carryOverTokens = $currentSubscription->tokens_remaining;
        }

        $totalTokens = $newTokens + $carryOverTokens;

        Log::info('replaceSubscription', [
            'new_tokens' => $newTokens,
            'carry_over_tokens' => $carryOverTokens,
            'total_tokens' => $totalTokens,
            'action_type' => $actionType,
        ]);

        // Create new subscription with PENDING status (will be activated after payment)
        $newSubscription = UserTokenSubscription::create([
            'user_id' => $user->id,
            'package_id' => $newPackage->id,
            'tokens_purchased' => $totalTokens,
            'tokens_used' => 0,
            'tokens_remaining' => $totalTokens,
            'status' => TokenSubscriptionStatus::PENDING, // Always PENDING for paid packages
            'purchased_at' => null, // Will be set after payment
            'activated_at' => null, // Will be set after payment
            'expires_at' => null, // Will be set after payment
            'action_type' => $actionType,
        ]);

        Log::info('Created new subscription', [
            'new_sub_id' => $newSubscription->id,
        ]);

        // Deactivate old subscription
        if ($currentSubscription) {
            $currentSubscription->deactivate(TokenSubscriptionStatus::REPLACED);
            $currentSubscription->replaced_by_id = $newSubscription->id;
            $currentSubscription->save();

            Log::info('Deactivated and linked old subscription', [
                'old_sub_id' => $currentSubscription->id,
                'linked_to' => $newSubscription->id,
            ]);
        }

        return $newSubscription;
    }

    protected function determineActionType(?UserTokenSubscription $current, OpenAiTokenPackage $new): string
    {
        if (!$current || $current->action_type === 'trial') {
            return 'purchase';
        }

        $currentPrice = $current->package ? $current->package->price : $current->pricingTier?->initial_price ?? 0;
        $newPrice = $new->price;

        if ($newPrice > $currentPrice) return 'upgrade';
        if ($newPrice < $currentPrice) return 'downgrade';

        return 'purchase';
    }

    /**
     * Activate a pending subscription after payment
     */
    public function activateSubscription(UserTokenSubscription $subscription): void
    {
        DB::transaction(function () use ($subscription) {
            // Check if this is a topup for a subscription cycle (new system)
            if ($subscription->action_type === 'topup') {
                $this->activateTopup($subscription);

                return;
            }

            Log::info('activateSubscription called', [
                'subscription_id' => $subscription->id,
                'status' => $subscription->status,
                'replaced_by_id' => $subscription->replaced_by_id,
                'tokens_remaining' => $subscription->tokens_remaining,
            ]);

            // Check if this is a top-up (has replaced_by_id pointing to active subscription)
            if ($subscription->replaced_by_id) {
                $mainSubscription = UserTokenSubscription::find($subscription->replaced_by_id);

                Log::info('Found replaced_by_id, checking main subscription', [
                    'main_subscription_id' => $mainSubscription?->id,
                    'main_status' => $mainSubscription?->status,
                ]);

                // FIX: Compare enum with enum
                if ($mainSubscription && $mainSubscription->status === TokenSubscriptionStatus::ACTIVE) {

                    Log::info('Top-up activation START', [
                        'main_sub_id' => $mainSubscription->id,
                        'purchase_id' => $subscription->id,
                        'main_tokens_purchased_before' => $mainSubscription->tokens_purchased,
                        'main_tokens_remaining_before' => $mainSubscription->tokens_remaining,
                        'main_tokens_used' => $mainSubscription->tokens_used,
                        'tokens_to_add_purchased' => $subscription->tokens_purchased,
                        'tokens_to_add_remaining' => $subscription->tokens_remaining,
                    ]);

                    // Add tokens to the main subscription
                    $mainSubscription->tokens_purchased = $mainSubscription->tokens_purchased + $subscription->tokens_purchased;
                    $mainSubscription->tokens_remaining = $mainSubscription->tokens_remaining + $subscription->tokens_remaining;
                    $saved = $mainSubscription->save();

                    Log::info('Top-up activation END', [
                        'save_result' => $saved,
                        'main_tokens_purchased_after' => $mainSubscription->tokens_purchased,
                        'main_tokens_remaining_after' => $mainSubscription->tokens_remaining,
                    ]);

                    // Verify the save worked
                    $mainSubscription->refresh();
                    Log::info('After refresh', [
                        'main_tokens_purchased' => $mainSubscription->tokens_purchased,
                        'main_tokens_remaining' => $mainSubscription->tokens_remaining,
                    ]);

                    // Mark purchase record as processed
                    $subscription->status = TokenSubscriptionStatus::REPLACED;
                    $subscription->purchased_at = now();
                    $subscription->activated_at = now();
                    $subscription->save();

                    return;
                }
            }

            Log::info('Regular subscription activation (not a top-up)');

            // Regular subscription activation (not a top-up)
            $currentActive = $subscription->user->activeTokenSubscription;

            Log::info('Current active subscription', [
                'current_active_id' => $currentActive?->id,
                'current_active_status' => $currentActive?->status,
            ]);

            if ($currentActive && $currentActive->id !== $subscription->id) {
                // Carry over remaining tokens if needed
                // Get the token limit from either package or pricing tier
                $tokenLimit = $subscription->package
                    ? $subscription->package->token_limit
                    : $subscription->pricingTier?->monthly_token_limit ?? $subscription->tokens_purchased;

                if ($currentActive->tokens_remaining > 0 &&
                    $subscription->tokens_purchased == $tokenLimit) {

                    $carryOverTokens = $currentActive->tokens_remaining;

                    Log::info('Carrying over tokens', [
                        'carry_over_tokens' => $carryOverTokens,
                        'new_sub_tokens_before' => $subscription->tokens_remaining,
                    ]);

                    $subscription->tokens_purchased += $carryOverTokens;
                    $subscription->tokens_remaining += $carryOverTokens;

                    Log::info('After carry over', [
                        'new_sub_tokens_after' => $subscription->tokens_remaining,
                    ]);
                }

                // Deactivate old subscription
                $currentActive->deactivate(TokenSubscriptionStatus::REPLACED);
                $currentActive->replaced_by_id = $subscription->id;
                $currentActive->save();

                Log::info('Deactivated old subscription', [
                    'old_sub_id' => $currentActive->id,
                ]);
            }

            // Activate the new subscription
            $subscription->activate();

            Log::info('Activated new subscription', [
                'new_sub_id' => $subscription->id,
                'tokens_remaining' => $subscription->tokens_remaining,
            ]);
        });
    }

    /**
     * Activate a topup subscription - adds tokens to current cycle
     */
    protected function activateTopup(UserTokenSubscription $topupSubscription): void
    {
        $user = $topupSubscription->user;
        $topupInfo = session('topup_info');

        if (!$topupInfo) {
            Log::error('Topup session info missing', [
                'subscription_id' => $topupSubscription->id,
            ]);

            throw new \Exception('Topup session information missing.');
        }

        $cycleId = $topupInfo['cycle_id'];
        $amount = $topupInfo['amount'];
        $pricingTierId = $topupInfo['pricing_tier_id'];

        $cycle = $user->subscriptionCycles()
            ->where('id', $cycleId)
            ->first();

        if (!$cycle) {
            Log::error('Topup cycle not found', [
                'cycle_id' => $cycleId,
                'user_id' => $user->id,
            ]);

            throw new \Exception('Subscription cycle not found for topup.');
        }

        // Get the pricing tier to calculate tokens from amount
        $pricingTier = $cycle->pricingTier;

        if (!$pricingTier) {
            Log::error('Pricing tier not found for topup', [
                'cycle_id' => $cycleId,
                'pricing_tier_id' => $pricingTierId,
            ]);

            throw new \Exception('Pricing tier not found for topup.');
        }

        // Calculate tokens based on the pricing tier's rate
        // Example: 7000 tokens for $10 = 700 tokens per $1
        // So $23 topup = 23 * 700 = 16,100 tokens
        $topupTokens = $pricingTier->calculateTokensFromAmount((float)$amount);

        if ($topupTokens <= 0) {
            Log::warning('Topup calculated to zero tokens', [
                'amount' => $amount,
                'pricing_tier_id' => $pricingTier->id,
                'initial_price' => $pricingTier->initial_price,
                'monthly_token_limit' => $pricingTier->monthly_token_limit,
            ]);
        }

        // Add topup tokens to the current cycle
        $cycle->addTopupTokens($topupTokens);

        // Mark topup subscription as completed
        $topupSubscription->status = TokenSubscriptionStatus::ACTIVE;
        $topupSubscription->tokens_purchased = $topupTokens;
        $topupSubscription->tokens_remaining = $topupTokens;
        $topupSubscription->purchased_at = now();
        $topupSubscription->activated_at = now();
        $topupSubscription->save();

        Log::info('Topup activated successfully', [
            'subscription_id' => $topupSubscription->id,
            'cycle_id' => $cycle->id,
            'amount' => $amount,
            'topup_tokens' => $topupTokens,
            'user_id' => $user->id,
            'pricing_tier_id' => $pricingTier->id,
            'tokens_per_currency' => $pricingTier->monthly_token_limit / (float)$pricingTier->initial_price,
        ]);

        // Clear topup session
        session()->forget('topup_info');
    }

    public function checkExpiredSubscriptions(): int
    {
        $expiredCount = 0;

        $expiredSubscriptions = UserTokenSubscription::where('status', TokenSubscriptionStatus::ACTIVE)
            ->whereNotNull('expires_at')
            ->where('expires_at', '<=', now())
            ->get();

        foreach ($expiredSubscriptions as $subscription) {
            $subscription->deactivate(TokenSubscriptionStatus::EXPIRED);
            $expiredCount++;
        }

        return $expiredCount;
    }

    public function getUserSubscriptionStats(User $user): array
    {
        $currentCycle = $user->getCurrentActiveCycle();
        $allCycles = $user->subscriptionCycles;

        $totalSpent = $user->subscriptionCycles()
//            ->where('status', '!=', 'inactive')
            ->sum('current_price');

        $totalTokensPurchased = $user->subscriptionCycles()
//            ->where('status', '!=', 'inactive')
            ->sum('tokens_allocated');

        $totalTokensUsed = $user->tokenUsageLogs()->sum('total_tokens');

        $paidCyclesCount = $user->subscriptionCycles()
//            ->where('status', '!=', 'inactive')
            ->count();

        return [
            'current_subscription' => $currentCycle,
            'total_subscriptions' => $allCycles->count(),
            'paid_subscriptions_count' => $paidCyclesCount,
            'total_spent' => round($totalSpent ?? 0, 2),
            'total_tokens_purchased' => $totalTokensPurchased,
            'total_tokens_used' => $totalTokensUsed,
            'has_active' => (bool)$currentCycle,
            'needs_upgrade' => $user->needsTokenUpgrade(),
        ];
    }
}
