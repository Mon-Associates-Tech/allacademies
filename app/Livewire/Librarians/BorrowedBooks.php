<?php

namespace App\Livewire\Librarians;

use App\Models\BookBorrowing;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Livewire\WithPagination;

class BorrowedBooks extends Component
{
    use WithPagination;

    public $search = '';

    public $dueDateFilter = '';

    public $overdueOnly = false;

    public $showReturnModal = false;

    public $selectedBorrowing = null;

    public $returnCondition = 'good';

    public $returnNotes = '';

    protected $queryString = [
        'search' => ['except' => ''],
        'dueDateFilter' => ['except' => ''],
        'overdueOnly' => ['except' => false],
    ];

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedDueDateFilter()
    {
        $this->resetPage();
    }

    public function updatedOverdueOnly()
    {
        $this->resetPage();
    }

    #[Computed]
    public function borrowedBooks()
    {
        $query = BookBorrowing::with(['student.user', 'book.bookCategory', 'book.author'])
            ->where('status', 'active');

        if ($this->search) {
            $query->where(function ($q) {
                $q->whereHas('student.user', function ($user) {
                    $user->where('name', 'like', '%'.$this->search.'%');
                })
                    ->orWhereHas('book', function ($book) {
                        $book->where('title', 'like', '%'.$this->search.'%');
                    });
            });
        }

        if ($this->dueDateFilter) {
            $query->whereDate('due_date', $this->dueDateFilter);
        }

        if ($this->overdueOnly) {
            $query->where('due_date', '<', now());
        }

        return $query->latest()->paginate(10);
    }

    public function extendDueDate($borrowingId, $days = 7)
    {
        $borrowing = BookBorrowing::findOrFail($borrowingId);
        $borrowing->update([
            'due_date' => $borrowing->due_date->addDays($days),
            'extended_by' => auth()->id(),
            'extended_at' => now(),
        ]);

        session()->flash('success', 'Due date extended successfully!');
    }

    public function showReturnModal($borrowingId)
    {
        $this->selectedBorrowing = BookBorrowing::findOrFail($borrowingId);
        $this->showReturnModal = true;
    }

    public function processReturn()
    {
        $this->selectedBorrowing->update([
            'status' => 'returned',
            'return_date' => now(),
            'return_condition' => $this->returnCondition,
            'return_notes' => $this->returnNotes,
            'returned_to' => auth()->id(),
        ]);

        $this->showReturnModal = false;
        $this->selectedBorrowing = null;
        $this->returnCondition = 'good';
        $this->returnNotes = '';

        session()->flash('success', 'Book returned successfully!');
    }

    public function sendReminder($borrowingId)
    {
        $borrowing = BookBorrowing::findOrFail($borrowingId);

        // Send reminder notification to student
        $borrowing->student->user->notify(new \App\Notifications\BookReturnReminder($borrowing));

        session()->flash('success', 'Reminder sent successfully!');
    }

    public function render()
    {
        return view('livewire.librarians.borrowed-books');
    }
}
