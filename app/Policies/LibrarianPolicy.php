<?php

namespace App\Policies;

use App\Models\Librarian;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class LibrarianPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {
        return true; // Anyone can view the list of librarians
    }

    public function view(User $user, Librarian $librarian)
    {
        return true; // Anyone can view librarian details
    }

    public function create(User $user)
    {
        return $user->hasRole('administrator');
    }

    public function update(User $user, Librarian $librarian)
    {
        return $user->hasRole('administrator') ||
               $user->id === $librarian->user_id;
    }

    public function delete(User $user, Librarian $librarian)
    {
        return $user->hasRole('administrator');
    }

    public function approveBooks(User $user)
    {
        return $user->hasRole('librarian');
    }

    public function lendBooks(User $user)
    {
        return $user->hasRole('librarian');
    }
}
