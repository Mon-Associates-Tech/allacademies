<?php

namespace App\Livewire\Librarians;

use App\Models\Book;
use App\Models\BookCategory;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Computed;

class LibraryBooks extends Component
{
    use WithPagination;

    public $search = '';
    public $categoryFilter = '';
    public $statusFilter = '';
    public $formatFilter = '';
    public $availabilityFilter = '';
    public $sortBy = 'title';
    public $sortDirection = 'asc';

    protected $queryString = [
        'search' => ['except' => ''],
        'categoryFilter' => ['except' => ''],
        'statusFilter' => ['except' => ''],
        'formatFilter' => ['except' => ''],
        'availabilityFilter' => ['except' => ''],
        'sortBy' => ['except' => 'title'],
        'sortDirection' => ['except' => 'asc'],
    ];

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedCategoryFilter()
    {
        $this->resetPage();
    }

    public function updatedStatusFilter()
    {
        $this->resetPage();
    }

    public function updatedFormatFilter()
    {
        $this->resetPage();
    }

    public function updatedAvailabilityFilter()
    {
        $this->resetPage();
    }

    public function sortBy($field)
    {
        if ($this->sortBy === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $field;
            $this->sortDirection = 'asc';
        }
        $this->resetPage();
    }

    public function resetFilters()
    {
        $this->reset(['search', 'categoryFilter', 'statusFilter', 'formatFilter', 'availabilityFilter']);
        $this->resetPage();
    }

    private function getBooksQuery()
    {
        $query = Book::with(['bookCategory', 'author.user'])
            ->withCount([
                'borrowings as active_borrowings_count' => function($query) {
                    $query->where('status', 'active');
                },
                'borrowings as total_borrowings_count'
            ]);

        if ($this->search) {
            $query->where(function($q) {
                $q->where('title', 'like', '%' . $this->search . '%')
                    ->orWhere('description', 'like', '%' . $this->search . '%')
                    ->orWhereHas('author.user', function($user) {
                        $user->where('name', 'like', '%' . $this->search . '%');
                    });
            });
        }

        if ($this->categoryFilter) {
            $query->where('book_category_id', $this->categoryFilter);
        }

        if ($this->statusFilter) {
            $query->where('status', $this->statusFilter);
        }

        if ($this->formatFilter) {
            $query->where('format', $this->formatFilter);
        }

        if ($this->availabilityFilter) {
            if ($this->availabilityFilter === 'available') {
                $query->whereDoesntHave('borrowings', function($q) {
                    $q->where('status', 'active');
                });
            } elseif ($this->availabilityFilter === 'borrowed') {
                $query->whereHas('borrowings', function($q) {
                    $q->where('status', 'active');
                });
            }
        }

        return $query->orderBy($this->sortBy, $this->sortDirection);
    }

    #[Computed]
    public function categories()
    {
        return BookCategory::orderBy('name')->get();
    }

    #[Computed]
    public function totalBooks()
    {
        return $this->getBooksQuery()->count();
    }

    #[Computed]
    public function availableBooks()
    {
        return $this->getBooksQuery()
            ->whereDoesntHave('borrowings', function($q) {
                $q->where('status', 'active');
            })
            ->count();
    }

    #[Computed]
    public function borrowedBooks()
    {
        return $this->getBooksQuery()
            ->whereHas('borrowings', function($q) {
                $q->where('status', 'active');
            })
            ->count();
    }

    public function toggleBookStatus($bookId)
    {
        $book = Book::findOrFail($bookId);
        $book->update([
            'status' => $book->status === 'published' ? 'draft' : 'published'
        ]);

        session()->flash('success', 'Book status updated successfully!');
    }

    public function render()
    {
        $books = $this->getBooksQuery()->paginate(12);

        return view('livewire.librarians.library-books', [
            'books' => $books,
        ]);
    }
}
