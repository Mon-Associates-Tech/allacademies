<?php

namespace App\Policies;

use App\Models\BookBorrowing;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class BookBorrowingPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {
        return $user->hasRole('administrator') ||
               $user->hasRole('librarian') ||
               $user->hasRole('teacher') ||
               $user->hasRole('student');
    }

    public function view(User $user, BookBorrowing $bookBorrowing)
    {
        return $user->hasRole('administrator') ||
               $user->hasRole('librarian') ||
               $user->hasRole('teacher') ||
               ($user->hasRole('student') && $user->student->id === $bookBorrowing->student_id);
    }

    public function create(User $user)
    {
        return $user->hasRole('librarian') ||
               $user->hasRole('student');
    }

    public function update(User $user, BookBorrowing $bookBorrowing)
    {
        return $user->hasRole('administrator') ||
               $user->hasRole('librarian');
    }

    public function delete(User $user, BookBorrowing $bookBorrowing)
    {
        return $user->hasRole('administrator') ||
               $user->hasRole('librarian');
    }

    public function return(User $user, BookBorrowing $bookBorrowing)
    {
        return $user->hasRole('librarian') ||
               ($user->hasRole('student') && $user->student->id === $bookBorrowing->student_id);
    }
}
