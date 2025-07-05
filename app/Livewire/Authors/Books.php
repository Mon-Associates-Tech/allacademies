<?php

namespace App\Livewire\Authors;

use App\Models\Book;
use App\Models\BookCategory;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;

class Books extends Component
{
    use WithPagination;

    public $search = '';
    public $statusFilter = '';
    public $categoryFilter = '';
    public $sortBy = 'created_at';
    public $sortDirection = 'desc';
    public $viewType = 'grid'; // New: grid or list view

    public $showDeleteModal = false;
    public $bookToDelete = null;

    protected $queryString = [
        'search' => ['except' => ''],
        'statusFilter' => ['except' => ''],
        'categoryFilter' => ['except' => ''],
        'sortBy' => ['except' => 'created_at'],
        'sortDirection' => ['except' => 'desc'],
        'viewType' => ['except' => 'grid'],
    ];

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedStatusFilter()
    {
        $this->resetPage();
    }

    public function updatedCategoryFilter()
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

    public function toggleSortDirection()
    {
        $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        $this->resetPage();
    }

    public function resetFilters()
    {
        $this->search = '';
        $this->statusFilter = '';
        $this->categoryFilter = '';
        $this->sortBy = 'created_at';
        $this->sortDirection = 'desc';
        $this->resetPage();

        session()->flash('success', 'Filters reset successfully!');
    }

    public function confirmDelete($bookId)
    {
        $this->bookToDelete = Book::findOrFail($bookId);
        $this->showDeleteModal = true;
    }

    public function deleteBook()
    {
        if ($this->bookToDelete) {
            $this->bookToDelete->delete();
            session()->flash('success', 'Book deleted successfully!');
        }

        $this->showDeleteModal = false;
        $this->bookToDelete = null;
    }

    public function cancelDelete()
    {
        $this->showDeleteModal = false;
        $this->bookToDelete = null;
    }

    public function toggleBookStatus($bookId)
    {
        $book = Book::findOrFail($bookId);
        $book->status = $book->status === 'published' ? 'draft' : 'published';
        $book->save();

        session()->flash('success', 'Book status updated successfully!');
    }

    public function duplicateBook($bookId)
    {
        $book = Book::findOrFail($bookId);
        $newBook = $book->replicate();
        $newBook->title = $book->title . ' (Copy)';
        $newBook->status = 'draft';
        $newBook->save();

        session()->flash('success', 'Book duplicated successfully!');
    }

    public function getBookPerformanceData()
    {
        $author = Auth::user()->author;
        if (!$author) return [];

        return $author->books()
            ->with(['bookCategory'])
            ->withCount(['subscriptions', 'borrowings'])
            ->get()
            ->map(function($book) {
                return [
                    'id' => $book->id,
                    'title' => $book->title,
                    'performance_score' => $this->calculatePerformanceScore($book),
                    'trend' => $this->calculateTrend($book),
                ];
            });
    }

    private function calculatePerformanceScore($book)
    {
        // Simple performance calculation based on subscriptions and borrowings
        $subscriptions = $book->subscriptions_count ?? 0;
        $borrowings = $book->borrowings_count ?? 0;

        return ($subscriptions * 2) + $borrowings;
    }

    private function calculateTrend($book)
    {
        // This would need historical data - for now, return a mock trend
        return rand(-10, 25); // Simulated percentage change
    }

    public function render()
    {
        $author = Auth::user()->author;
        $books = null;
        $categories = BookCategory::all();
        $performanceData = $this->getBookPerformanceData();

        if ($author) {
            $query = $author->books()
                ->with(['bookCategory'])
                ->withCount(['subscriptions', 'borrowings']);

            if ($this->search) {
                $query->where(function($q) {
                    $q->where('title', 'like', '%' . $this->search . '%')
                      ->orWhere('publisher', 'like', '%' . $this->search . '%')
                      ->orWhere('additional_info', 'like', '%' . $this->search . '%');
                });
            }

            if ($this->statusFilter) {
                $query->where('status', $this->statusFilter);
            }

            if ($this->categoryFilter) {
                $query->where('book_category_id', $this->categoryFilter);
            }

            $books = $query->orderBy($this->sortBy, $this->sortDirection)
                          ->paginate(12);
        }

        return view('livewire.authors.books', [
            'books' => $books,
            'categories' => $categories,
            'performanceData' => $performanceData,
        ]);
    }
}
