<?php

namespace App\Policies;

use App\Models\Chat\UserTokenSubscription;
use App\Models\User;

class UserTokenSubscriptionPolicy
{
    /**
     * Determine if the user can view the subscription.
     */
    public function view(User $user, UserTokenSubscription $subscription): bool
    {
        // User can view their own subscription
        return $user->id === $subscription->user_id || $user->isSuperAdmin();
    }

    /**
     * Determine if the user can view any subscriptions.
     */
    public function viewAny(User $user): bool
    {
        return true; // Any authenticated user can view their own subscriptions list
    }

    /**
     * Determine if the user can create subscriptions.
     */
    public function create(User $user): bool
    {
        return true; // Any authenticated user can create subscriptions
    }

    /**
     * Determine if the user can update the subscription.
     */
    public function update(User $user, UserTokenSubscription $subscription): bool
    {
        // Only allow updates for pending subscriptions by owner or admin
        return ($user->id === $subscription->user_id && $subscription->status === 'pending')
            || $user->isSuperAdmin();
    }

    /**
     * Determine if the user can delete the subscription.
     */
    public function delete(User $user, UserTokenSubscription $subscription): bool
    {
        // Only super admins can delete subscriptions
        return $user->isSuperAdmin();
    }
}
