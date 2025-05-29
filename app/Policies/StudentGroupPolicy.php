<?php

namespace App\Policies;

use App\Models\StudentGroup;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class StudentGroupPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {
        return true; // Anyone can view the list of student groups
    }

    public function view(User $user, StudentGroup $studentGroup)
    {
        return true; // Anyone can view student group details
    }

    public function create(User $user)
    {
        return $user->hasRole('administrator') ||
               $user->hasRole('teacher');
    }

    public function update(User $user, StudentGroup $studentGroup)
    {
        return $user->hasRole('administrator') ||
               ($user->hasRole('teacher') && $studentGroup->teacher_id === $user->teacher->id);
    }

    public function delete(User $user, StudentGroup $studentGroup)
    {
        return $user->hasRole('administrator') ||
               ($user->hasRole('teacher') && $studentGroup->teacher_id === $user->teacher->id);
    }

    public function addStudent(User $user, StudentGroup $studentGroup)
    {
        return $user->hasRole('administrator') ||
               ($user->hasRole('teacher') && $studentGroup->teacher_id === $user->teacher->id);
    }
}
