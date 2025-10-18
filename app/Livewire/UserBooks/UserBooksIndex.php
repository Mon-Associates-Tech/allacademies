<?php

namespace App\Livewire\UserBooks;

use App\Models\UserBook;
use Livewire\Component;

class UserBooksIndex extends Component
{
    public $userBooks = [];
    public $maxShares = 10; // Same as in form

    public function mount(): void
    {
        $this->loadUserBooks();
    }

    public function loadUserBooks(): void
    {
        $this->userBooks = UserBook::withCount('shares')
            ->where('user_id', auth()->id())
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function deleteBook(UserBook $userBook): void
    {
        if ($userBook->user_id !== auth()->id()) {
            abort(403);
        }

        $userBook->delete();
        $this->loadUserBooks();
        session()->flash('message', 'Book deleted successfully!');
    }

    public function render()
    {
        return view('livewire.user-books.user-books-index');
    }
}

