<?php
// app/Policies/TimetableEntryPolicy.php

namespace App\Timetable\Policies;

use App\Models\User;
use App\Timetable\Models\TimetableEntry;

class TimetableEntryPolicy
{
    /**
     * All authenticated roles can view — scoped to their own school
     * (cross-school for owner) at the query level, not here.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, TimetableEntry $entry): bool
    {
        return $user->canAccessCrossSchool() || $user->school_id === $entry->school_id;
    }

    public function create(User $user): bool
    {
        return $user->isSchoolAdmin() || $user->isOwner() || $user->isTeacher();
    }

    public function update(User $user, TimetableEntry $entry): bool
    {
        if ($user->isOwner()) {
            return true;
        }

        if ($user->isSchoolAdmin()) {
            return $user->school_id === $entry->school_id;
        }

        if ($user->isTeacher()) {
            // Teachers may only modify their own entries.
            return $user->teacher?->id === $entry->teacher_id;
        }

        return false;
    }

    public function delete(User $user, TimetableEntry $entry): bool
    {
        return $this->update($user, $entry);
    }
}
