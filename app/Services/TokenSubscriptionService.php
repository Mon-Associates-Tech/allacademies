<?php

namespace App\Services;

use App\Models\Chat\OpenAiTokenPackage;
use App\Models\Chat\UserTokenSubscription;
use App\Models\User;
use Illuminate\Support\Facades\DB;

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

            // If topping up and current subscription is active and usable
            if ($isTopUp && $currentSubscription &&
                $currentSubscription->status === 'active' &&
                !$currentSubscription->isExpired() &&
                $currentSubscription->tokens_remaining > 0) {

                // Simply add tokens to existing subscription
                $currentSubscription->tokens_purchased += $newPackage->token_limit;
                $currentSubscription->tokens_remaining += $newPackage->token_limit;
                $currentSubscription->save();

                // Create a pending "purchase" record for payment tracking
                $purchaseRecord = UserTokenSubscription::create([
                    'user_id' => $user->id,
                    'package_id' => $newPackage->id,
                    'tokens_purchased' => $newPackage->token_limit,
                    'tokens_used' => 0,
                    'tokens_remaining' => 0, // Will be merged
                    'status' => $newPackage->isFree() ? 'active' : 'pending',
                    'purchased_at' => $newPackage->isFree() ? now() : null,
                    'action_type' => 'purchase',
                    'replaced_by_id' => $currentSubscription->id, // Links to main subscription
                ]);

                if ($newPackage->isFree()) {
                    $purchaseRecord->activated_at = now();
                    $purchaseRecord->save();
                }

                return $purchaseRecord;
            }

            // Otherwise, create new subscription and replace old one
            return $this->replaceSubscription($user, $currentSubscription, $newPackage);
        });
    }

    /**
     * Replace existing subscription with a new one
     */
    protected function replaceSubscription(User $user, ?UserTokenSubscription $currentSubscription, OpenAiTokenPackage $newPackage): UserTokenSubscription
    {
        // Determine action type
        $actionType = $this->determineActionType($currentSubscription, $newPackage);

        // Create new subscription
        $newSubscription = UserTokenSubscription::create([
            'user_id' => $user->id,
            'package_id' => $newPackage->id,
            'tokens_purchased' => $newPackage->token_limit,
            'tokens_used' => 0,
            'tokens_remaining' => $newPackage->token_limit,
            'status' => $newPackage->isFree() ? 'active' : 'pending',
            'purchased_at' => $newPackage->isFree() ? now() : null,
            'activated_at' => $newPackage->isFree() ? now() : null,
            'expires_at' => $newPackage->isFree() ? now()->addWeek() : null,
            'action_type' => $actionType,
        ]);

        // If there's a current subscription, deactivate it
        if ($currentSubscription) {
            $currentSubscription->deactivate('replaced');
            $currentSubscription->replaced_by_id = $newSubscription->id;
            $currentSubscription->save();
        }

        // If free package, activate immediately
        if ($newPackage->isFree()) {
            $newSubscription->activate();
        }

        return $newSubscription;
    }

    /**
     * Activate a pending subscription after payment
     */
    public function activateSubscription(UserTokenSubscription $subscription): void
    {
        DB::transaction(function () use ($subscription) {
            // Check if this is a top-up (has replaced_by_id pointing to active subscription)
            if ($subscription->replaced_by_id) {
                $mainSubscription = UserTokenSubscription::find($subscription->replaced_by_id);

                // If the linked subscription is active, this was a top-up
                if ($mainSubscription && $mainSubscription->status === 'active') {
                    // Tokens already added in changeSubscription, just mark as activated
                    $subscription->status = 'replaced'; // Mark as merged/replaced
                    $subscription->purchased_at = now();
                    $subscription->activated_at = now();
                    $subscription->save();

                    return;
                }
            }

            // Regular subscription activation (not a top-up)
            // Deactivate any currently active subscription
            $currentActive = $subscription->user->activeTokenSubscription;
            if ($currentActive && $currentActive->id !== $subscription->id) {
                $currentActive->deactivate('replaced');
                $currentActive->replaced_by_id = $subscription->id;
                $currentActive->save();
            }

            // Activate the new subscription
            $subscription->activate();
        });
    }

    /**
     * Determine if the action is upgrade, downgrade, or purchase
     */
    protected function determineActionType(?UserTokenSubscription $current, OpenAiTokenPackage $new): string
    {
        if (!$current) {
            return 'purchase';
        }

        if ($current->action_type === 'trial') {
            return 'purchase';
        }

        $currentPrice = $current->package->price;
        $newPrice = $new->price;

        if ($newPrice > $currentPrice) {
            return 'upgrade';
        } elseif ($newPrice < $currentPrice) {
            return 'downgrade';
        }

        return 'purchase';
    }

    /**
     * Check and expire subscriptions
     */
    public function checkExpiredSubscriptions(): int
    {
        $expiredCount = 0;

        $expiredSubscriptions = UserTokenSubscription::where('status', 'active')
            ->whereNotNull('expires_at')
            ->where('expires_at', '<=', now())
            ->get();

        foreach ($expiredSubscriptions as $subscription) {
            $subscription->deactivate('expired');
            $expiredCount++;
        }

        return $expiredCount;
    }

    /**
     * Get subscription stats for user
     */
    public function getUserSubscriptionStats(User $user): array
    {
        $current = $user->activeTokenSubscription;
        $history = $user->subscriptionHistory()->count();

        // Total spent on paid packages
        $totalSpent = $user->tokenSubscriptions()
            ->join('openai_token_packages', 'user_token_subscriptions.package_id', '=', 'openai_token_packages.id')
            ->whereNotNull('user_token_subscriptions.purchased_at')
            ->where('openai_token_packages.is_free', false)
            ->sum('openai_token_packages.price');

        // Total tokens purchased (all packages including top-ups)
        $totalTokensPurchased = $user->tokenSubscriptions()
            ->whereNotNull('activated_at')
            ->sum('tokens_purchased');

        // Total tokens actually used
        $totalTokensUsed = $user->tokenUsageLogs()->sum('total_tokens');

        // Count of paid subscriptions
        $paidSubscriptionsCount = $user->tokenSubscriptions()
            ->join('openai_token_packages', 'user_token_subscriptions.package_id', '=', 'openai_token_packages.id')
            ->whereNotNull('user_token_subscriptions.purchased_at')
            ->where('openai_token_packages.is_free', false)
            ->count();

        return [
            'current_subscription' => $current,
            'total_subscriptions' => $history + ($current ? 1 : 0),
            'paid_subscriptions_count' => $paidSubscriptionsCount,
            'total_spent' => round($totalSpent ?? 0, 2),
            'total_tokens_purchased' => $totalTokensPurchased,
            'total_tokens_used' => $totalTokensUsed,
            'has_active' => (bool) $current,
            'needs_upgrade' => $user->needsTokenUpgrade(),
        ];
    }
}
