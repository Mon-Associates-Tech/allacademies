<?php

namespace App\Policies;

use App\Models\LessonNote;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class LessonNotePolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {
        return true; // Anyone can view lesson notes
    }

    public function view(User $user, LessonNote $lessonNote)
    {
        return true; // Anyone can view lesson note details
    }

    public function create(User $user)
    {
        return $user->hasRole('teacher');
    }

    public function update(User $user, LessonNote $lessonNote)
    {
        return $user->hasRole('administrator') ||
               ($user->hasRole('teacher') && $lessonNote->teacher_id === $user->teacher->id);
    }

    public function delete(User $user, LessonNote $lessonNote)
    {
        return $user->hasRole('administrator') ||
               ($user->hasRole('teacher') && $lessonNote->teacher_id === $user->teacher->id);
    }
}
