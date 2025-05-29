<?php

namespace App\Policies;

use App\Models\Role;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class RolePolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {
        return $user->hasRole('administrator');
    }

    public function view(User $user, Role $role)
    {
        return $user->hasRole('administrator');
    }

    public function create(User $user)
    {
        return $user->hasRole('administrator');
    }

    public function update(User $user, Role $role)
    {
        return $user->hasRole('administrator');
    }

    public function delete(User $user, Role $role)
    {
        return $user->hasRole('administrator');
    }
}
