<?php

namespace App\Policies;

use App\Models\Administrator;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class AdministratorPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {
        return $user->hasRole('administrator');
    }

    public function view(User $user, Administrator $administrator)
    {
        return $user->hasRole('administrator');
    }

    public function create(User $user)
    {
        return $user->hasRole('administrator');
    }

    public function update(User $user, Administrator $administrator)
    {
        return $user->hasRole('administrator');
    }

    public function delete(User $user, Administrator $administrator)
    {
        return $user->hasRole('administrator');
    }
}
