<?php

namespace App\Policies;

use App\Models\BookSubscription;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class BookSubscriptionPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {
        return $user->hasRole('administrator') ||
               $user->hasRole('librarian') ||
               $user->hasRole('teacher') ||
               $user->hasRole('student');
    }

    public function view(User $user, BookSubscription $bookSubscription)
    {
        return $user->hasRole('administrator') ||
               $user->hasRole('librarian') ||
               $user->hasRole('teacher') ||
               ($user->hasRole('student') && $user->student->id === $bookSubscription->student_id);
    }

    public function create(User $user)
    {
        return $user->hasRole('student');
    }

    public function update(User $user, BookSubscription $bookSubscription)
    {
        return $user->hasRole('administrator') ||
               $user->hasRole('librarian') ||
               ($user->hasRole('student') && $user->student->id === $bookSubscription->student_id);
    }

    public function delete(User $user, BookSubscription $bookSubscription)
    {
        return $user->hasRole('administrator') ||
               $user->hasRole('librarian') ||
               ($user->hasRole('student') && $user->student->id === $bookSubscription->student_id);
    }
}
