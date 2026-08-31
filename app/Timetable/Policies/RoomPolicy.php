<?php
// app/Timetable/Policies/RoomPolicy.php

namespace App\Timetable\Policies;

use App\Models\User;
use App\Timetable\Models\Room;

class RoomPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Room $room): bool
    {
        return $user->canAccessCrossSchool() || $user->school_id === $room->school_id;
    }

    public function create(User $user): bool
    {
        return $user->isOwner() || $user->isSchoolAdmin();
    }

    public function update(User $user, Room $room): bool
    {
        if ($user->isOwner()) {
            return true;
        }

        return $user->isSchoolAdmin() && $user->school_id === $room->school_id;
    }

    public function delete(User $user, Room $room): bool
    {
        return $this->update($user, $room);
    }
}
