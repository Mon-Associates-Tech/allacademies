<?php

namespace App\Livewire\UserBooks;

use App\Models\UserBook;
use App\Models\UserBookShare;
use Livewire\Component;

class SharedBooks extends Component
{
    public $activeTab = 'pending'; // Define the property with default value
    public $sharedByMe;
    public function acceptShare(UserBookShare $share): void
    {
        if ($share->shared_to_user_id !== auth()->id()) {
            abort(403);
        }

        $share->accept();
        session()->flash('message', 'Book share accepted!');
    }

    public function declineShare(UserBookShare $share): void
    {
        if ($share->shared_to_user_id !== auth()->id()) {
            abort(403);
        }

        $share->decline();

        // Decrement the shares count on the user book
        $share->userBook->decrement('shares_count');

        session()->flash('message', 'Book share declined!');
    }

    public function setActiveTab($tab): void
    {
        $this->activeTab = $tab;
    }

    public function render()
    {
        $pendingShares = UserBookShare::with(['userBook', 'sharedBy'])
            ->where('shared_to_user_id', auth()->id())
            ->where('status', 'pending')
            ->get();

        $acceptedShares = UserBookShare::with(['userBook', 'sharedBy'])
            ->where('shared_to_user_id', auth()->id())
            ->where('status', 'accepted')
            ->get();


        // Add a user's own uploaded books
        $myBooks = UserBook::where('user_id', auth()->id())->get();
        $this->sharedByMe = UserBookShare::where('shared_by_user_id', auth()->id())
            ->with(['userBook', 'sharedWith'])
            ->get();

        return view('livewire.user-books.shared-books', compact('pendingShares', 'acceptedShares', 'myBooks'));
    }
}
