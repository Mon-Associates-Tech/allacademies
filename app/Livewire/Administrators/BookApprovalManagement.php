<?php

namespace App\Livewire\Administrators;

use App\Models\Book;
use App\Models\BookApproval;
use App\Models\BookCategory;
use App\Models\Librarian;
use Livewire\Component;
use Livewire\WithPagination;

class BookApprovalManagement extends Component
{
    use WithPagination;

    public $searchTerm = '';
    public $sortField = 'created_at';
    public $sortDirection = 'desc';
    public $filterStatus = '';
    public $filterCategory = '';
    public $dateRange = '';
    public $selectedBookId;
    public $selectedBookTitle;
    public $approvalStatus;
    public $approvalComments;
    public $showApprovalModal = false;

    // Add properties for counts
    public $pendingCount = 0;
    public $approvedCount = 0;
    public $rejectedCount = 0;

    public function mount()
    {
        $this->updateCounts();
    }

    public function updated($propertyName)
    {
        // Update counts when filters change
        if (in_array($propertyName, ['filterStatus', 'filterCategory', 'searchTerm', 'dateRange'])) {
            $this->updateCounts();
            $this->resetPage();
        }
    }

    public function updateCounts()
    {
        $baseQuery = Book::query()
            ->when($this->searchTerm, function($query) {
                $query->where(function($subQuery) {
                    $subQuery->where('title', 'like', '%'.$this->searchTerm.'%')
                        ->orWhereHas('author.user', function($q) {
                            $q->where('name', 'like', '%'.$this->searchTerm.'%');
                        })
                        ->orWhereHas('bookCategory', function($q) {
                            $q->where('name', 'like', '%'.$this->searchTerm.'%');
                        });
                });
            })
            ->when($this->filterCategory, function($query) {
                $query->where('book_category_id', $this->filterCategory);
            })
            ->when($this->dateRange, function($query) {
                switch($this->dateRange) {
                    case 'today':
                        $query->whereDate('created_at', today());
                        break;
                    case 'week':
                        $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
                        break;
                    case 'month':
                        $query->whereMonth('created_at', now()->month)
                              ->whereYear('created_at', now()->year);
                        break;
                    case 'quarter':
                        $query->whereBetween('created_at', [now()->startOfQuarter(), now()->endOfQuarter()]);
                        break;
                }
            });

        // Count pending books (no approvals or only pending approvals)
        $this->pendingCount = (clone $baseQuery)
            ->whereDoesntHave('approvals', function($q) {
                $q->whereIn('status', ['approved', 'rejected']);
            })
            ->count();

        // Count approved books
        $this->approvedCount = (clone $baseQuery)
            ->whereHas('approvals', function($q) {
                $q->where('status', 'approved');
            })
            ->count();

        // Count rejected books
        $this->rejectedCount = (clone $baseQuery)
            ->whereHas('approvals', function($q) {
                $q->where('status', 'rejected');
            })
            ->count();
    }

    public function resetFilters()
    {
        $this->searchTerm = '';
        $this->filterStatus = '';
        $this->filterCategory = '';
        $this->dateRange = '';
        $this->updateCounts();
        $this->resetPage();
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function openApprovalModal($bookId, $bookTitle)
    {
        $this->selectedBookId = $bookId;
        $this->selectedBookTitle = $bookTitle;

        // Get the latest approval for this book
        $book = Book::with(['approvals' => function($query) {
            $query->latest();
        }])->find($bookId);

        $latestApproval = $book->approvals->first();

        if ($latestApproval) {
            // Pre-populate with existing approval data
            $this->approvalStatus = $latestApproval->status;
            $this->approvalComments = $latestApproval->comments;
        } else {
            // Reset for new approval
            $this->approvalStatus = '';
            $this->approvalComments = '';
        }

        $this->showApprovalModal = true;
    }

    public function closeApprovalModal()
    {
        $this->showApprovalModal = false;
        $this->approvalStatus = '';
        $this->approvalComments = '';
    }

    public function submitApproval()
    {
        $this->validate([
            'approvalStatus' => 'required|in:approved,rejected',
            'approvalComments' => 'nullable|string|max:1000',
        ]);

        $book = Book::findOrFail($this->selectedBookId);

        // Find a librarian (in a real app, you'd use the authenticated librarian)
        $librarian = Librarian::first();

        if (!$librarian) {
            session()->flash('error', 'No librarian found. Please contact administrator.');
            return;
        }

        // Create new approval record
        BookApproval::create([
            'librarian_id' => $librarian->id,
            'book_id' => $this->selectedBookId,
            'approval_date' => now(),
            'status' => $this->approvalStatus,
            'comments' => $this->approvalComments,
        ]);

        $this->closeApprovalModal();
        $this->updateCounts(); // Update counts after approval

        $statusText = $this->approvalStatus === 'approved' ? 'approved' : 'rejected';
        session()->flash('message', "Book '{$this->selectedBookTitle}' has been {$statusText}.");
    }

    public function render()
    {
        $query = Book::query()
            ->when($this->searchTerm, function($query) {
                $query->where(function($subQuery) {
                    $subQuery->where('title', 'like', '%'.$this->searchTerm.'%')
                        ->orWhereHas('author.user', function($q) {
                            $q->where('name', 'like', '%'.$this->searchTerm.'%');
                        })
                        ->orWhereHas('bookCategory', function($q) {
                            $q->where('name', 'like', '%'.$this->searchTerm.'%');
                        });
                });
            })
            ->when($this->filterCategory, function($query) {
                $query->where('book_category_id', $this->filterCategory);
            })
            ->when($this->dateRange, function($query) {
                switch($this->dateRange) {
                    case 'today':
                        $query->whereDate('created_at', today());
                        break;
                    case 'week':
                        $query->whereBetween('created_at', [now()->startOfWeek(), now()->endOfWeek()]);
                        break;
                    case 'month':
                        $query->whereMonth('created_at', now()->month)
                              ->whereYear('created_at', now()->year);
                        break;
                    case 'quarter':
                        $query->whereBetween('created_at', [now()->startOfQuarter(), now()->endOfQuarter()]);
                        break;
                }
            })
            ->with(['author.user', 'bookCategory', 'approvals' => function($query) {
                $query->latest();
            }]);

        // Filter by approval status
        if ($this->filterStatus === 'approved') {
            $query->whereHas('approvals', function($q) {
                $q->where('status', 'approved');
            });
        } elseif ($this->filterStatus === 'rejected') {
            $query->whereHas('approvals', function($q) {
                $q->where('status', 'rejected');
            });
        } elseif ($this->filterStatus === 'pending') {
            $query->whereDoesntHave('approvals', function($q) {
                $q->whereIn('status', ['approved', 'rejected']);
            });
        }

        $books = $query->orderBy($this->sortField, $this->sortDirection)
            ->paginate(10);

        // Get categories for filter dropdown
        $categories = BookCategory::orderBy('name')->get();

        return view('livewire.administrators.book-approval-management', [
            'books' => $books,
            'categories' => $categories
        ]);
    }
}
