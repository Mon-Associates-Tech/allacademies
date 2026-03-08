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
        return $user->hasAnyRole(['admin', 'teacher', 'superadmin', 'owner']);
    }

    public function view(User $user, Student $student)
    {
        if ($user->canAccessCrossSchool()) {
            return true;
        }

        return $user->school_id === $student->school_id;
    }

    public function create(User $user)
    {
        return $user->hasAnyRole(['admin', 'superadmin', 'owner']);
    }

    public function update(User $user, Student $student)
    {
        if (! $this->view($user, $student)) {
            return false;
        }

        return $user->hasAnyRole(['admin', 'superadmin', 'owner']);
    }

    public function delete(User $user, Student $student)
    {
        if (! $this->view($user, $student)) {
            return false;
        }

        return $user->hasAnyRole(['admin', 'superadmin', 'owner']);
    }

    public function assignToGroup(User $user, Student $student)
    {
        return $user->hasRole('admin') ||
               ($user->hasRole('teacher') && $user->teacher->studentGroups->contains('id', $student->student_group_id));
    }
}
