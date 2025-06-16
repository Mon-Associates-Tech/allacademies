<?php

namespace App\Policies;

use App\Models\Role;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class RolePolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user): User|bool
    {
        return $user->hasRole('admin');
    }

    public function view(User $user, Role $role): User|bool
    {
        return $user->hasRole('admin');
    }

    public function create(User $user): User|bool
    {
        return $user->hasRole('admin');
    }

    public function update(User $user, Role $role): User|bool
    {
        return $user->hasRole('admin');
    }

    public function delete(User $user, Role $role): User|bool
    {
        return $user->hasRole('admin');
    }
}
