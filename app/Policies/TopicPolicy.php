<?php

namespace App\Policies;

use App\Models\AcademicTopic;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class TopicPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {
        return true; // Anyone can view topics
    }

    public function view(User $user, AcademicTopic $topic)
    {
        return true; // Anyone can view topic details
    }

    public function create(User $user)
    {
        return $user->hasRole('administrator') ||
               $user->hasRole('teacher');
    }

    public function update(User $user, AcademicTopic $topic)
    {
        return $user->hasRole('administrator') ||
               $user->hasRole('teacher');
    }

    public function delete(User $user, AcademicTopic $topic)
    {
        return $user->hasRole('administrator');
    }
}
