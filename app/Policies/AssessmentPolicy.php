<?php

namespace App\Policies;

use App\Models\Assessment;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class AssessmentPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {
        return $user->hasRole('administrator') ||
               $user->hasRole('teacher') ||
               $user->hasRole('student');
    }

    public function view(User $user, Assessment $assessment)
    {
        return $user->hasRole('administrator') ||
               $user->hasRole('teacher') ||
               ($user->hasRole('student') && $user->student->id === $assessment->student_id);
    }

    public function create(User $user)
    {
        return $user->hasRole('student');
    }

    public function update(User $user, Assessment $assessment)
    {
        return $user->hasRole('administrator') ||
               ($user->hasRole('student') && $user->student->id === $assessment->student_id);
    }

    public function delete(User $user, Assessment $assessment)
    {
        return $user->hasRole('administrator') ||
               ($user->hasRole('student') && $user->student->id === $assessment->student_id);
    }
}
