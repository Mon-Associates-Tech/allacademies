<?php

namespace App\Policies;

use App\Models\Lesson;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class LessonPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {
        return true; // Anyone can view lessons
    }

    public function view(User $user, Lesson $lesson)
    {
        return true; // Anyone can view lesson details
    }

    public function create(User $user)
    {
        return $user->hasRole('teacher');
    }

    public function update(User $user, Lesson $lesson)
    {
        return $user->hasRole('administrator') ||
               ($user->hasRole('teacher') && $lesson->teacher_id === $user->teacher->id);
    }

    public function delete(User $user, Lesson $lesson)
    {
        return $user->hasRole('administrator') ||
               ($user->hasRole('teacher') && $lesson->teacher_id === $user->teacher->id);
    }

    public function assign(User $user, Lesson $lesson)
    {
        return $user->hasRole('teacher') && $lesson->teacher_id === $user->teacher->id;
    }
}
