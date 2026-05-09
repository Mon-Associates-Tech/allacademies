<?php

namespace App\Policies;

use App\Models\GradeScale;
use App\Models\User;

class GradeScalePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isSuperAdmin() || $user->hasRole('owner') || $user->hasRole('admin');
    }

    public function view(User $user, GradeScale $gradeScale): bool
    {
        return $user->canAccessSchool($gradeScale->school_id);
    }

    public function create(User $user): bool
    {
        return $user->isSuperAdmin() || $user->hasRole('owner') || $user->hasRole('admin');
    }

    public function update(User $user, GradeScale $gradeScale): bool
    {
        return $user->canAccessSchool($gradeScale->school_id) && 
               ($user->isSuperAdmin() || $user->hasRole('owner') || $user->hasRole('admin'));
    }

    public function delete(User $user, GradeScale $gradeScale): bool
    {
        return $user->canAccessSchool($gradeScale->school_id) && 
               ($user->isSuperAdmin() || $user->hasRole('owner') || $user->hasRole('admin'));
    }
}
