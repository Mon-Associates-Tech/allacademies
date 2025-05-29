<?php

namespace App\Policies;

use App\Models\Teacher;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class TeacherPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {
        return true; // Anyone can view the list of teachers
    }

    public function view(User $user, Teacher $teacher)
    {
        return true; // Anyone can view teacher details
    }

    public function create(User $user)
    {
        return $user->hasRole('administrator');
    }

    public function update(User $user, Teacher $teacher)
    {
        return $user->hasRole('administrator') ||
               $user->id === $teacher->user_id;
    }

    public function delete(User $user, Teacher $teacher)
    {
        return $user->hasRole('administrator');
    }

    public function assignStudentGroup(User $user, Teacher $teacher)
    {
        return $user->hasRole('administrator') ||
               $user->id === $teacher->user_id;
    }
}
