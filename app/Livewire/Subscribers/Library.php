<?php

namespace App\Livewire\Subscribers;

use App\Models\Book;
use App\Models\BookCategory;
use App\Models\BookSubscription;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class Library extends Component
{
    use WithPagination;

    public $search = '';
    public $category = '';
    public $status = 'all'; // 'all', 'subscribed', 'free'
    public $sortBy = 'title';

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedCategory()
    {
        $this->resetPage();
    }

    public function updatedStatus()
    {
        $this->resetPage();
    }

    public function render()
    {
        $user = Auth::user();
        $subscribedBookIds = [];

        if ($user->student) {
            $subscribedBookIds = BookSubscription::where('user_id', $user->id)
                ->where('status', 'active')
                ->pluck('book_id')
                ->toArray();
        }

        $query = Book::with(['author.user', 'bookCategory'])
            ->when($this->search, function ($q) {
                return $q->where('title', 'like', '%' . $this->search . '%')
                    ->orWhereHas('author.user', function ($subQ) {
                        $subQ->where('name', 'like', '%' . $this->search . '%');
                    });
            })
            ->when($this->category, function ($q) {
                return $q->where('book_category_id', $this->category);
            })
            ->when($this->status === 'subscribed', function ($q) use ($subscribedBookIds) {
                return $q->whereIn('id', $subscribedBookIds);
            })
            ->when($this->status === 'free', function ($q) {
                return $q->where('is_free', true);
            })
            ->orderBy($this->sortBy);

        return view('livewire.subscribers.library', [
            'books' => $query->paginate(12),
            'categories' => BookCategory::all(),
            'subscribedBookIds' => $subscribedBookIds,
        ]);
    }
}
