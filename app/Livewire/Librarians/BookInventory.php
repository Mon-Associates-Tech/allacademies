<?php

namespace App\Livewire\Librarians;

use App\Models\Book;
use App\Models\BookCategory;
use App\Models\Book as BookCopy;
use App\Models\Publisher;
use Livewire\Component;
use Livewire\WithPagination;

class BookInventory extends Component
{
    use WithPagination;

    public $search = '';
    public $statusFilter = 'all';
    public $categoryFilter = 'all';
    public $publisherFilter = 'all';
    public $sortBy = 'title';
    public $sortDirection = 'asc';
    public $perPage = 15;

    // Modal properties
    public $showModal = false;
    public $selectedBook = null;
    public $newCopyQuantity = 1;
    public $selectedCondition = 'new';
    public $selectedLocation = '';
    public $notes = '';

    protected $queryString = [
        'search' => ['except' => ''],
        'statusFilter' => ['except' => 'all'],
        'categoryFilter' => ['except' => 'all'],
        'publisherFilter' => ['except' => 'all'],
        'sortBy' => ['except' => 'title'],
        'sortDirection' => ['except' => 'asc'],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatusFilter()
    {
        $this->resetPage();
    }

    public function updatingCategoryFilter()
    {
        $this->resetPage();
    }

    public function updatingPublisherFilter()
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
    }

    public function resetFilters()
    {
        $this->search = '';
        $this->statusFilter = 'all';
        $this->categoryFilter = 'all';
        $this->publisherFilter = 'all';
        $this->sortBy = 'title';
        $this->sortDirection = 'asc';
        $this->resetPage();
    }

    public function openAddCopiesModal($bookId)
    {
        $this->selectedBook = Book::find($bookId);
        $this->showModal = true;
        $this->resetModalFields();
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->selectedBook = null;
        $this->resetModalFields();
    }

    public function addCopies()
    {
        $this->validate([
            'newCopyQuantity' => 'required|integer|min:1|max:100',
            'selectedCondition' => 'required|in:new,good,fair,poor',
            'selectedLocation' => 'required|string|max:255',
            'notes' => 'nullable|string|max:500',
        ]);

        for ($i = 0; $i < $this->newCopyQuantity; $i++) {
            BookCopy::create([
                'book_id' => $this->selectedBook->id,
                'barcode' => $this->generateBarcode(),
                'condition' => $this->selectedCondition,
                'location' => $this->selectedLocation,
                'status' => 'available',
                'notes' => $this->notes,
                'added_by' => auth()->id(),
            ]);
        }

        session()->flash('success', "Successfully added {$this->newCopyQuantity} copies of '{$this->selectedBook->title}'");
        $this->closeModal();
    }

    public function updateCopyStatus($copyId, $status)
    {
        $copy = BookCopy::find($copyId);

        if ($copy && in_array($status, ['available', 'damaged', 'lost', 'repair'])) {
            $copy->update([
                'status' => $status,
                'updated_by' => auth()->id(),
            ]);

            session()->flash('success', 'Copy status updated successfully');
        }
    }

    public function removeCopy($copyId)
    {
        $copy = BookCopy::find($copyId);

        if ($copy && $copy->status === 'available') {
            $copy->delete();
            session()->flash('success', 'Copy removed successfully');
        } else {
            session()->flash('error', 'Cannot remove copy. It may be borrowed or not available.');
        }
    }

    private function resetModalFields()
    {
        $this->newCopyQuantity = 1;
        $this->selectedCondition = 'new';
        $this->selectedLocation = '';
        $this->notes = '';
    }

    private function generateBarcode()
    {
        return 'BC' . str_pad(rand(1, 999999), 6, '0', STR_PAD_LEFT);
    }

    public function render()
    {
        $query = Book::with(['copies', 'bookCategory', 'publisher'])
            ->withCount(['copies as total_copies', 'copies as available_copies' => function ($query) {
                $query->where('status', 'available');
            }]);

        // Apply search filter
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('title', 'like', '%' . $this->search . '%')
                  ->orWhere('author', 'like', '%' . $this->search . '%')
                  ->orWhere('isbn', 'like', '%' . $this->search . '%');
            });
        }

        // Apply status filter
        if ($this->statusFilter !== 'all') {
            switch ($this->statusFilter) {
                case 'available':
                    $query->whereHas('copies', function ($q) {
                        $q->where('status', 'available');
                    });
                    break;
                case 'out_of_stock':
                    $query->whereDoesntHave('copies', function ($q) {
                        $q->where('status', 'available');
                    });
                    break;
                case 'low_stock':
                    $query->having('available_copies', '<=', 2)
                          ->having('available_copies', '>', 0);
                    break;
            }
        }

        // Apply category filter
        if ($this->categoryFilter !== 'all') {
            $query->where('category_id', $this->categoryFilter);
        }

        // Apply publisher filter
        if ($this->publisherFilter !== 'all') {
            $query->where('publisher_id', $this->publisherFilter);
        }

        // Apply sorting
        $query->orderBy($this->sortBy, $this->sortDirection);

        $books = $query->paginate($this->perPage);

        return view('livewire.librarians.book-inventory', [
            'books' => $books,
            'categories' => BookCategory::all(),
            'publishers' => collect() // Publisher::all(),
        ]);
    }
}
