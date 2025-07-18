<?php

namespace App\Livewire\Librarians;

use App\Models\BookBorrow;
use App\Models\BookBorrowing;
use App\Models\BookCopy;
use App\Models\Student;
use Carbon\Carbon;
use Livewire\Component;
use Livewire\WithPagination;

class BookReturns extends Component
{
    use WithPagination;

    public $search = '';
    public $statusFilter = 'all';
    public $dueDateFilter = 'all';
    public $sortBy = 'expected_return_date';
    public $sortDirection = 'asc';
    public $perPage = 15;

    // Return modal properties
    public $showReturnModal = false;
    public $selectedBorrow = null;
    public $returnCondition = 'good';
    public $returnNotes = '';
    public $lateFee = 0;

    // Quick return properties
    public $quickReturnBarcode = '';
    public $quickReturnMode = false;

    protected $queryString = [
        'search' => ['except' => ''],
        'statusFilter' => ['except' => 'all'],
        'dueDateFilter' => ['except' => 'all'],
        'sortBy' => ['except' => 'expected_return_date'],
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

    public function updatingDueDateFilter()
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
        $this->dueDateFilter = 'all';
        $this->sortBy = 'expected_return_date';
        $this->sortDirection = 'asc';
        $this->resetPage();
    }

    public function openReturnModal($borrowId)
    {
        $this->selectedBorrow = BookBorrow::with(['bookCopy.book', 'student'])
            ->find($borrowId);

        if ($this->selectedBorrow) {
            $this->calculateLateFee();
            $this->showReturnModal = true;
        }
    }

    public function closeReturnModal()
    {
        $this->showReturnModal = false;
        $this->selectedBorrow = null;
        $this->resetReturnFields();
    }

    public function processReturn()
    {
        $this->validate([
            'returnCondition' => 'required|in:new,good,fair,poor,damaged',
            'returnNotes' => 'nullable|string|max:500',
            'lateFee' => 'nullable|numeric|min:0',
        ]);

        if (!$this->selectedBorrow) {
            session()->flash('error', 'Invalid borrow record.');
            return;
        }

        // Update borrow record
        $this->selectedBorrow->update([
            'returned_at' => now(),
            'return_condition' => $this->returnCondition,
            'return_notes' => $this->returnNotes,
            'late_fee' => $this->lateFee,
            'returned_by' => auth()->id(),
        ]);

        // Update book copy status and condition
        $this->selectedBorrow->bookCopy->update([
            'status' => $this->returnCondition === 'damaged' ? 'damaged' : 'available',
            'condition' => $this->returnCondition,
        ]);

        // Log activity
        activity()
            ->performedOn($this->selectedBorrow)
            ->causedBy(auth()->user())
            ->withProperties([
                'action' => 'book_returned',
                'book_title' => $this->selectedBorrow->bookCopy->book->title,
                'student' => $this->selectedBorrow->student->name,
                'late_fee' => $this->lateFee,
                'condition' => $this->returnCondition,
            ])
            ->log('Book returned');

        session()->flash('success', 'Book returned successfully!');
        $this->closeReturnModal();
    }

    public function quickReturn()
    {
        if (!$this->quickReturnBarcode) {
            session()->flash('error', 'Please enter a barcode.');
            return;
        }

        $bookCopy = BookCopy::where('barcode', $this->quickReturnBarcode)->first();

        if (!$bookCopy) {
            session()->flash('error', 'Book copy not found.');
            return;
        }

        $activeBorrow = BookBorrow::where('book_copy_id', $bookCopy->id)
            ->whereNull('returned_at')
            ->first();

        if (!$activeBorrow) {
            session()->flash('error', 'No active borrow record found for this book.');
            return;
        }

        $this->openReturnModal($activeBorrow->id);
        $this->quickReturnBarcode = '';
    }

    public function toggleQuickReturnMode()
    {
        $this->quickReturnMode = !$this->quickReturnMode;
        $this->quickReturnBarcode = '';
    }

    public function renewBook($borrowId)
    {
        $borrow = BookBorrow::find($borrowId);

        if (!$borrow || $borrow->returned_at) {
            session()->flash('error', 'Invalid borrow record.');
            return;
        }

        // Check if book can be renewed (not overdue by more than 7 days)
        if ($borrow->expected_return_date < now()->subDays(7)) {
            session()->flash('error', 'Book is too overdue to be renewed.');
            return;
        }

        // Check if student has any overdue books
        $hasOverdueBooks = BookBorrow::where('student_id', $borrow->student_id)
            ->whereNull('returned_at')
            ->where('expected_return_date', '<', now())
            ->exists();

        if ($hasOverdueBooks) {
            session()->flash('error', 'Student has overdue books. Cannot renew.');
            return;
        }

        // Renew the book (add 14 days)
        $borrow->update([
            'expected_return_date' => $borrow->expected_return_date->addDays(14),
            'renewed_at' => now(),
            'renewed_by' => auth()->id(),
        ]);

        session()->flash('success', 'Book renewed successfully!');
    }

    private function calculateLateFee()
    {
        if (!$this->selectedBorrow) {
            return;
        }

        $daysOverdue = max(0, now()->diffInDays($this->selectedBorrow->expected_return_date, false));
        $this->lateFee = $daysOverdue * 0.50; // $0.50 per day
    }

    private function resetReturnFields()
    {
        $this->returnCondition = 'good';
        $this->returnNotes = '';
        $this->lateFee = 0;
    }

    public function render()
    {
        $query =  BookBorrowing::with(['bookCopy.book', 'student'])
            ->whereNull('return_date');

        // Apply search filter
        if ($this->search) {
            $query->where(function ($q) {
                $q->whereHas('bookCopy.book', function ($bookQuery) {
                    $bookQuery->where('title', 'like', '%' . $this->search . '%')
                             ->orWhere('author', 'like', '%' . $this->search . '%');
                })
                ->orWhereHas('student', function ($studentQuery) {
                    $studentQuery->where('name', 'like', '%' . $this->search . '%')
                               ->orWhere('email', 'like', '%' . $this->search . '%');
                })
                ->orWhereHas('bookCopy', function ($copyQuery) {
                    $copyQuery->where('barcode', 'like', '%' . $this->search . '%');
                });
            });
        }

        // Apply status filter
        if ($this->statusFilter !== 'all') {
            switch ($this->statusFilter) {
                case 'overdue':
                    $query->where('expected_return_date', '<', now());
                    break;
                case 'due_soon':
                    $query->whereBetween('expected_return_date', [now(), now()->addDays(3)]);
                    break;
                case 'on_time':
                    $query->where('expected_return_date', '>=', now());
                    break;
            }
        }

        // Apply due date filter
        if ($this->dueDateFilter !== 'all') {
            switch ($this->dueDateFilter) {
                case 'today':
                    $query->whereDate('expected_return_date', today());
                    break;
                case 'tomorrow':
                    $query->whereDate('expected_return_date', today()->addDay());
                    break;
                case 'this_week':
                    $query->whereBetween('expected_return_date', [now(), now()->endOfWeek()]);
                    break;
            }
        }

        // Apply sorting
        $query->orderBy($this->sortBy, $this->sortDirection);

        $borrows = $query->paginate($this->perPage);

        return view('livewire.librarians.book-returns', [
            'borrows' => $borrows,
        ]);
    }
}
