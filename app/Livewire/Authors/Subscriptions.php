<?php

namespace App\Livewire\Authors;

use App\Livewire\AppComponent;
use App\Models\Author;
use App\Models\BookSubscription;
use Illuminate\Contracts\View\View;

class Subscriptions extends AppComponent
{
    public Author $author;
    public $search = '';
    public $statusFilter = 'all';
    public $perPage = 10;

    public function mount(?Author $author)
    {
        //if (!$author) {
            $this->author = auth()->user()->author;
        //} else {
          //  $this->author = $author;
       // }
    }

    public function render(): View
    {
        $subscriptions = $this->getSubscriptions();

        return view('livewire.authors.subscriptions', [
            'subscriptions' => $subscriptions,
            'totalSubscribers' => $this->getTotalSubscribers(),
            'activeSubscriptions' => $this->getActiveSubscriptions(),
            'hasSubscriptions' => $subscriptions->total() > 0
        ]);
    }

    private function getSubscriptions()
    {
        $query = BookSubscription::with(['user', 'book'])
            ->whereHas('book', function ($bookQuery) {
                $bookQuery->where('author_id', $this->author->id);
            });

        if ($this->search) {
            $query->whereHas('user', function ($userQuery) {
                $userQuery->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('email', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->statusFilter !== 'all') {
            $query->where('status', $this->statusFilter);
        }

        return $query->latest()->paginate($this->perPage);
    }

    private function getTotalSubscribers()
    {
        return BookSubscription::whereHas('book', function ($bookQuery) {
            $bookQuery->where('author_id', $this->author->id);
        })->distinct('user_id')->count();
    }

    private function getActiveSubscriptions()
    {
        return BookSubscription::whereHas('book', function ($bookQuery) {
            $bookQuery->where('author_id', $this->author->id);
        })->where('status', 'active')
            ->where('end_date', '>', now())
            ->count();
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedStatusFilter()
    {
        $this->resetPage();
    }

    public function resetFilters()
    {
        $this->search = '';
        $this->statusFilter = 'all';
        $this->resetPage();
    }
}
