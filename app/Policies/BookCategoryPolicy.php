<?php

namespace App\Policies;

use App\Models\BookCategory;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class BookCategoryPolicy
{
    use HandlesAuthorization;

    public function viewAny(User $user)
    {
        return true; // Anyone can view book categories
    }

    public function view(User $user, BookCategory $bookCategory)
    {
        return true; // Anyone can view book category details
    }

    public function create(User $user)
    {
        return $user->hasRole('administrator') ||
               $user->hasRole('librarian');
    }

    public function update(User $user, BookCategory $bookCategory)
    {
        return $user->hasRole('administrator') ||
               $user->hasRole('librarian');
    }

    public function delete(User $user, BookCategory $bookCategory)
    {
        return $user->hasRole('administrator') ||
               $user->hasRole('librarian');
    }
}
