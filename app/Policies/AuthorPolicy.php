<?php

namespace App\Policies;

use App\Models\Author;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class AuthorPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {
        return true; // Anyone can view the list of authors
    }

    public function view(User $user, Author $author)
    {
        return true; // Anyone can view author details
    }

    public function create(User $user)
    {
        return $user->hasRole('administrator');
    }

    public function update(User $user, Author $author)
    {
        return $user->hasRole('administrator') ||
               $user->id === $author->user_id;
    }

    public function delete(User $user, Author $author)
    {
        return $user->hasRole('administrator');
    }
}
