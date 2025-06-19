<?php

namespace App\Policies;

use App\Models\Book;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class BookPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {
        return true; // Anyone can view the list of books
    }

    public function view(User $user, Book $book)
    {
        return true; // Anyone can view a book's details
    }

    public function create(User $user)
    {
        return $user->hasRole('administrator') ||
               $user->hasRole('librarian') ||
               $user->hasRole('author');
    }

    public function update(User $user, Book $book)
    {
        return $user->hasRole('administrator') ||
               $user->hasRole('librarian') ||
               ($user->hasRole('author') && $book->author_id === $user->author->id);
    }

    public function delete(User $user, Book $book)
    {
        return $user->hasRole('administrator') ||
               $user->hasRole('librarian') ||
               ($user->hasRole('author') && $book->author_id === $user->author->id);
    }

    public function borrow(User $user, Book $book)
    {
        return $user->hasRole('student') && $book->has_hardcopy;
    }

    public function subscribe(User $user, Book $book)
    {
        return $user->hasRole('student') && $book->has_softcopy;
    }

    public function groupSubscribe(User $user, Book $book)
    {
        return ($user->hasRole('teacher') ||
                $user->hasRole('librarian') ||
                $user->hasRole('administrator')) &&
               $book->has_softcopy;
    }

    public function approve(User $user, Book $book)
    {
        return $user->hasRole('librarian');
    }
}
