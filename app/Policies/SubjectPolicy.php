<?php

namespace App\Policies;

use App\Models\Subject;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class SubjectPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {
        return true; // Anyone can view subjects
    }

    public function view(User $user, Subject $subject)
    {
        return true; // Anyone can view subject details
    }

    public function create(User $user)
    {
        return $user->hasRole('administrator') ||
               $user->hasRole('teacher');
    }

    public function update(User $user, Subject $subject)
    {
        return $user->hasRole('administrator') ||
               $user->hasRole('teacher');
    }

    public function delete(User $user, Subject $subject)
    {
        return $user->hasRole('administrator');
    }
}
