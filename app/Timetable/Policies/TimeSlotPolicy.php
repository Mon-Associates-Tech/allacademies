<?php
// app/Timetable/Policies/TimeSlotPolicy.php

namespace App\Timetable\Policies;

use App\Models\User;
use App\Timetable\Models\TimeSlot;

class TimeSlotPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, TimeSlot $slot): bool
    {
        return $user->canAccessCrossSchool() || $user->school_id === $slot->school_id;
    }

    public function create(User $user): bool
    {
        return $user->isOwner() || $user->isSchoolAdmin();
    }

    public function update(User $user, TimeSlot $slot): bool
    {
        if ($user->isOwner()) {
            return true;
        }

        return $user->isSchoolAdmin() && $user->school_id === $slot->school_id;
    }

    public function delete(User $user, TimeSlot $slot): bool
    {
        return $this->update($user, $slot);
    }
}
