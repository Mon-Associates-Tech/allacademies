<?php

namespace App\Livewire\Librarians;

use App\Models\BookBorrowing as BookBorrow;
use App\Notifications\OverdueBookReminder;
use Livewire\Component;
use Livewire\WithPagination;

class OverdueBooks extends Component
{
    use WithPagination;

    public $search = '';

    public $overdueFilter = 'all';

    public $sortBy = 'due_date';

    public $sortDirection = 'asc';

    public $perPage = 15;

    // Reminder modal properties
    public $showReminderModal = false;

    public $selectedBorrows = [];

    public $reminderMessage = '';

    public $reminderType = 'email';

    protected $queryString = [
        'search' => ['except' => ''],
        'overdueFilter' => ['except' => 'all'],
        'sortBy' => ['except' => 'due_date'],
        'sortDirection' => ['except' => 'asc'],
    ];

    public function mount()
    {
        $this->reminderMessage = "Dear Student,\n\nThis is a friendly reminder that you have overdue books that need to be returned to the library.\n\nPlease return the books as soon as possible to avoid additional late fees.\n\nThank you,\nLibrary Team";
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingOverdueFilter()
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
        $this->overdueFilter = 'all';
        $this->sortBy = 'expected_return_date';
        $this->sortDirection = 'asc';
        $this->resetPage();
    }

    public function openReminderModal($borrowIds = [])
    {
        $this->selectedBorrows = $borrowIds;
        $this->showReminderModal = true;
    }

    public function closeReminderModal()
    {
        $this->showReminderModal = false;
        $this->selectedBorrows = [];
        $this->reminderType = 'email';
    }

    public function sendReminder($borrowId)
    {
        $borrow = BookBorrow::with(['student', 'bookCopy.book'])->find($borrowId);

        if (! $borrow) {
            session()->flash('error', 'Borrow record not found.');

            return;
        }

        try {
            $borrow->student->notify(new OverdueBookReminder($borrow));

            // Update last reminder sent
            $borrow->update([
                'last_reminder_sent' => now(),
                'reminder_count' => $borrow->reminder_count + 1,
            ]);

            session()->flash('success', "Reminder sent to {$borrow->student->name}");
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to send reminder.');
        }
    }

    public function sendBulkReminders()
    {
        if (empty($this->selectedBorrows)) {
            session()->flash('error', 'Please select books to send reminders for.');

            return;
        }

        $sentCount = 0;
        $failedCount = 0;

        foreach ($this->selectedBorrows as $borrowId) {
            $borrow = BookBorrow::with(['student', 'bookCopy.book'])->find($borrowId);

            if (! $borrow) {
                $failedCount++;

                continue;
            }

            try {
                $borrow->student->notify(new OverdueBookReminder($borrow, $this->reminderMessage));

                $borrow->update([
                    'last_reminder_sent' => now(),
                    'reminder_count' => $borrow->reminder_count + 1,
                ]);

                $sentCount++;
            } catch (\Exception $e) {
                $failedCount++;
            }
        }

        if ($sentCount > 0) {
            session()->flash('success', "Reminders sent to {$sentCount} students.");
        }

        if ($failedCount > 0) {
            session()->flash('error', "Failed to send {$failedCount} reminders.");
        }

        $this->closeReminderModal();
    }

    public function markAsLost($borrowId)
    {
        $borrow = BookBorrow::with(['bookCopy'])->find($borrowId);

        if (! $borrow) {
            session()->flash('error', 'Borrow record not found.');

            return;
        }

        $borrow->update([
            'status' => 'lost',
            'marked_lost_at' => now(),
            'marked_lost_by' => auth()->id(),
        ]);

        $borrow->bookCopy->update([
            'status' => 'lost',
        ]);

        session()->flash('success', 'Book marked as lost.');
    }

    public function calculateLateFee($expectedReturnDate)
    {
        $daysOverdue = max(0, now()->diffInDays($expectedReturnDate, false));

        return $daysOverdue * 0.50; // $0.50 per day
    }

    public function getDaysOverdue($expectedReturnDate)
    {
        return max(0, now()->diffInDays($expectedReturnDate, false));
    }

    public function getOverdueStatus($expectedReturnDate)
    {
        $daysOverdue = $this->getDaysOverdue($expectedReturnDate);

        if ($daysOverdue <= 7) {
            return 'recent';
        } elseif ($daysOverdue <= 30) {
            return 'moderate';
        } else {
            return 'severe';
        }
    }

    public function render()
    {
        $query = BookBorrow::with(['bookCopy.book', 'student'])
            ->whereNull('return_date')
            ->where('due_date', '<', now());

        // Apply search filter
        if ($this->search) {
            $query->where(function ($q) {
                $q->whereHas('bookCopy.book', function ($bookQuery) {
                    $bookQuery->where('title', 'like', '%'.$this->search.'%')
                        ->orWhere('author', 'like', '%'.$this->search.'%');
                })
                    ->orWhereHas('student', function ($studentQuery) {
                        $studentQuery->where('name', 'like', '%'.$this->search.'%')
                            ->orWhere('email', 'like', '%'.$this->search.'%');
                    })
                    ->orWhereHas('bookCopy', function ($copyQuery) {
                        $copyQuery->where('barcode', 'like', '%'.$this->search.'%');
                    });
            });
        }

        // Apply overdue filter
        if ($this->overdueFilter !== 'all') {
            switch ($this->overdueFilter) {
                case '1-7':
                    $query->whereBetween('due_date', [now()->subDays(7), now()]);
                    break;
                case '8-30':
                    $query->whereBetween('due_date', [now()->subDays(30), now()->subDays(8)]);
                    break;
                case '30+':
                    $query->where('due_date', '<', now()->subDays(30));
                    break;
            }
        }

        // Apply sorting
        $query->orderBy($this->sortBy, $this->sortDirection);

        $overdueBooks = $query->paginate($this->perPage);

        // Statistics
        $stats = [
            'total_overdue' => BookBorrow::whereNull('return_date')
                ->where('due_date', '<', now())
                ->count(),
            'recent_overdue' => BookBorrow::whereNull('return_date')
                ->whereBetween('due_date', [now()->subDays(7), now()])
                ->count(),
            'moderate_overdue' => BookBorrow::whereNull('return_date')
                ->whereBetween('due_date', [now()->subDays(30), now()->subDays(8)])
                ->count(),
            'severe_overdue' => BookBorrow::whereNull('return_date')
                ->where('due_date', '<', now()->subDays(30))
                ->count(),
        ];

        return view('livewire.librarians.overdue-books', [
            'overdueBooks' => $overdueBooks,
            'stats' => $stats,
        ]);
    }
}
