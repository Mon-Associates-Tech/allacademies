<?php

namespace App\Policies;

use App\Models\Student;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class StudentPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {
        return $user->hasRole('administrator') ||
               $user->hasRole('teacher') ||
               $user->hasRole('librarian');
    }

    public function view(User $user, Student $student)
    {
        return $user->hasRole('administrator') ||
               $user->hasRole('teacher') ||
               $user->hasRole('librarian') ||
               $user->id === $student->user_id;
    }

    public function create(User $user)
    {
        return $user->hasRole('administrator');
    }

    public function update(User $user, Student $student)
    {
        return $user->hasRole('administrator') ||
               $user->id === $student->user_id;
    }

    public function delete(User $user, Student $student)
    {
        return $user->hasRole('administrator');
    }

    public function assignToGroup(User $user, Student $student)
    {
        return $user->hasRole('administrator') ||
               ($user->hasRole('teacher') && $user->teacher->studentGroups->contains('id', $student->student_group_id));
    }
}
