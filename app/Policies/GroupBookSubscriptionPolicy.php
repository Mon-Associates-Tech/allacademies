<?php

namespace App\Policies;

use App\Models\GroupBookSubscription;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class GroupBookSubscriptionPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {
        return $user->hasRole('administrator') ||
               $user->hasRole('librarian') ||
               $user->hasRole('teacher');
    }

    public function view(User $user, GroupBookSubscription $groupBookSubscription)
    {
        return $user->hasRole('administrator') ||
               $user->hasRole('librarian') ||
               ($user->hasRole('teacher') && $groupBookSubscription->subscribedBy()->is($user->teacher));
    }

    public function create(User $user)
    {
        return $user->hasRole('administrator') ||
               $user->hasRole('librarian') ||
               $user->hasRole('teacher');
    }

    public function update(User $user, GroupBookSubscription $groupBookSubscription)
    {
        return $user->hasRole('administrator') ||
               $user->hasRole('librarian') ||
               ($user->hasRole('teacher') && $groupBookSubscription->subscribedBy()->is($user->teacher));
    }

    public function delete(User $user, GroupBookSubscription $groupBookSubscription)
    {
        return $user->hasRole('administrator') ||
               $user->hasRole('librarian') ||
               ($user->hasRole('teacher') && $groupBookSubscription->subscribedBy()->is($user->teacher));
    }
}
