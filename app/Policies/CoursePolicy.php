<?php

namespace App\Policies;

use App\Models\Lms\Course;
use App\Models\User;

class CoursePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Course $course): bool
    {
        // Published courses can be viewed by anyone
        if ($course->isPublished()) {
            // Check audience restrictions
            if ($course->audience === 'school_only') {
                return $user->school_id === $course->school_id;
            }

            return true;
        }

        // Draft/unpublished courses can only be viewed by creator or admins
        return $this->canManage($user, $course);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasAnyRole(['owner', 'admin', 'author', 'teacher']);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Course $course): bool
    {
        return $this->canManage($user, $course);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Course $course): bool
    {
        return $this->canManage($user, $course);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Course $course): bool
    {
        return $user->hasAnyRole(['owner', 'admin']);
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Course $course): bool
    {
        return $user->hasAnyRole(['owner', 'admin']);
    }

    /**
     * Determine whether the user can publish the course.
     */
    public function publish(User $user, Course $course): bool
    {
        return $this->canManage($user, $course);
    }

    /**
     * Determine whether the user can enroll in the course.
     */
    public function enroll(User $user, Course $course): bool
    {
        // Cannot enroll in unpublished courses
        if (! $course->isPublished()) {
            return false;
        }

        // Check audience restrictions
        if ($course->audience === 'school_only') {
            return $user->school_id === $course->school_id;
        }

        return true;
    }

    /**
     * Determine whether the user can set the course audience.
     */
    public function setAudience(User $user, Course $course, string $audience): bool
    {
        // Teachers can only set school_only for their school's courses
        if ($user->hasRole('teacher')) {
            if ($audience === 'school_only') {
                return $course->school_id === $user->school_id;
            }

            return true;
        }

        return $user->hasAnyRole(['owner', 'admin', 'author']);
    }

    /**
     * Check if user can manage (edit/delete) the course.
     */
    protected function canManage(User $user, Course $course): bool
    {
        // Owners and admins can manage any course
        if ($user->hasAnyRole(['owner', 'admin'])) {
            return true;
        }

        // Authors can manage any course they created
        if ($user->hasRole('author') && $course->created_by === $user->id) {
            return true;
        }

        // Teachers can only manage courses they created
        if ($user->hasRole('teacher') && $course->created_by === $user->id) {
            return true;
        }

        return false;
    }
}
