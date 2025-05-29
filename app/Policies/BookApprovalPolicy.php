<?php

namespace App\Policies;

use App\Models\BookApproval;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class BookApprovalPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {
        return $user->hasRole('administrator') ||
               $user->hasRole('librarian') ||
               $user->hasRole('author');
    }

    public function view(User $user, BookApproval $bookApproval)
    {
        return $user->hasRole('administrator') ||
               $user->hasRole('librarian') ||
               ($user->hasRole('author') && $user->author->books->contains('id', $bookApproval->book_id));
    }

    public function create(User $user)
    {
        return $user->hasRole('librarian');
    }

    public function update(User $user, BookApproval $bookApproval)
    {
        return $user->hasRole('librarian') && $bookApproval->librarian_id === $user->librarian->id;
    }

    public function delete(User $user, BookApproval $bookApproval)
    {
        return $user->hasRole('administrator') ||
               ($user->hasRole('librarian') && $bookApproval->librarian_id === $user->librarian->id);
    }
}
