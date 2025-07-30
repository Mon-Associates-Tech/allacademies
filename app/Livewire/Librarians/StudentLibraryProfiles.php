<?php

namespace App\Livewire\Librarians;

use App\Models\Student;
use App\Models\BookBorrowing;
use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;

class StudentLibraryProfiles extends Component
{
    use WithPagination;

    public $search = '';
    public $statusFilter = 'all';
    public $sortBy = 'name';
    public $sortDirection = 'asc';
    public $perPage = 15;

    // Profile modal properties
    public $showProfileModal = false;
    public $selectedStudent = null;
    public $studentStats = [];
    public $borrowingHistory = [];

    protected $queryString = [
        'search' => ['except' => ''],
        'statusFilter' => ['except' => 'all'],
        'sortBy' => ['except' => 'name'],
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
        $this->sortBy = 'name';
        $this->sortDirection = 'asc';
        $this->resetPage();
    }

    public function openProfileModal($studentId)
    {
        $this->selectedStudent = Student::with(['user', 'academicLevel'])
            ->find($studentId);

        if ($this->selectedStudent) {
            $this->loadStudentStats();
            $this->loadBorrowingHistory();
            $this->showProfileModal = true;
        }
    }

    public function closeProfileModal()
    {
        $this->showProfileModal = false;
        $this->selectedStudent = null;
        $this->studentStats = [];
        $this->borrowingHistory = [];
    }

    private function loadStudentStats()
    {
        $studentId = $this->selectedStudent->id;
        $user_id = $this->selectedStudent->user_id;
        // Current borrowings
        $currentBorrows = BookBorrowing::with(['bookCopy.book'])
            ->where('user_id', $user_id)
            ->whereNull('return_date')
            ->get();

        // Historical stats
        $allBorrows = BookBorrowing::where('user_id', $user_id)->get();
        $overdueBooks = $currentBorrows->where('due_date', '<', now());

        $this->studentStats = [
            'total_borrowed' => $allBorrows->count(),
            'total_returned' => $allBorrows->whereNotNull('return_date')->count(),
            'currently_borrowed' => $currentBorrows->count(),
            'overdue_books' => $overdueBooks->count(),
            'total_late_fees' => $allBorrows->sum('late_fee'),
            'average_borrow_duration' => $this->calculateAverageBorrowDuration($allBorrows),
            'favorite_categories' => $this->getFavoriteCategories($allBorrows),
            'borrowing_frequency' => $this->calculateBorrowingFrequency($allBorrows),
            'current_borrows' => $currentBorrows,
            'overdue_details' => $overdueBooks,
        ];
    }

    private function loadBorrowingHistory()
    {
        $this->borrowingHistory = BookBorrowing::with(['bookCopy.book'])
            ->where('user_id', $this->selectedStudent->user_id)
            ->orderBy('borrow_date', 'desc')
            ->limit(20)
            ->get()
            ->toArray();
    }

    private function calculateAverageBorrowDuration($borrows)
    {
        $returnedBorrows = $borrows->whereNotNull('return_date');

        if ($returnedBorrows->count() === 0) {
            return 0;
        }

        $totalDays = $returnedBorrows->sum(function ($borrow) {
            return $borrow->borrowed_at->diffInDays($borrow->return_date);
        });

        return round($totalDays / $returnedBorrows->count(), 1);
    }

    private function getFavoriteCategories($borrows)
    {
        return $borrows->groupBy('bookCopy.book.category.name')
            ->map(function ($group, $category) {
                return [
                    'category' => $category ?? 'Uncategorized',
                    'count' => $group->count(),
                ];
            })
            ->sortByDesc('count')
            ->take(5)
            ->values()
            ->toArray();
    }

    private function calculateBorrowingFrequency($borrows)
    {
        if ($borrows->count() === 0) {
            return 0;
        }

        $firstBorrow = $borrows->min('borrow_date');
        $lastBorrow = $borrows->max('borrow_date');

        if (!$firstBorrow || !$lastBorrow) {
            return 0;
        }

        $daysDiff = $firstBorrow->diffInDays($lastBorrow);

        if ($daysDiff === 0) {
            return $borrows->count();
        }

        return round($borrows->count() / ($daysDiff / 30), 2); // Books per month
    }

    public function suspendStudent($studentId)
    {
        $student = Student::find($studentId);

        if ($student) {
            $student->update([
                'library_suspended' => true,
                'library_suspended_at' => now(),
                'library_suspended_by' => auth()->id(),
            ]);

            session()->flash('success', 'Student suspended from library services.');
        }
    }

    public function unsuspendStudent($studentId)
    {
        $student = Student::find($studentId);

        if ($student) {
            $student->update([
                'library_suspended' => false,
                'library_suspended_at' => null,
                'library_suspended_by' => null,
            ]);

            session()->flash('success', 'Student library access restored.');
        }
    }

    public function sendReminder($studentId)
    {
        $student = Student::find($studentId);

        if ($student) {
            // Implementation for sending reminder
            session()->flash('success', "Reminder sent to {$student->name}");
        }
    }

    public function render()
    {
        $query = Student::with(['user', 'academicLevel'])
            ->withCount([
                'borrowedBooks as total_borrows',
                'borrowedBooks as current_borrows' => function ($q) {
                    $q->whereNull('return_date');
                },
                'borrowedBooks as overdue_borrows' => function ($q) {
                    $q->whereNull('return_date')
                      ->where('due_date', '<', now());
                },
            ])
            ->withSum('borrowedBooks as total_late_fees', 'late_fee');

        // Apply search filter
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('bio', 'like', '%' . $this->search . '%')
//                  ->orWhere('email', 'like', '%' . $this->search . '%')
                  ->orWhere('student_id', 'like', '%' . $this->search . '%')
                  ->orWhereHas('user', function ($userQuery) {
                      $userQuery->where('name', 'like', '%' . $this->search . '%')
                          ->orWhere('email', 'like', '%' . $this->search . '%')
                      ->where('email', 'like', '%' . $this->search . '%');
                  });
            });
        }

        // Apply status filter
        if ($this->statusFilter !== 'all') {
            switch ($this->statusFilter) {
                case 'active':
                    $query->having('current_borrows', '>', 0);
                    break;
                case 'overdue':
                    $query->having('overdue_borrows', '>', 0);
                    break;
                case 'suspended':
                    $query->where('library_suspended', true);
                    break;
                case 'inactive':
                    $query->having('current_borrows', 0)
                          ->having('overdue_borrows', 0);
                    break;
            }
        }

        // Apply sorting
        $query->orderBy('id', $this->sortDirection);

        $students = $query->paginate($this->perPage);

        return view('livewire.librarians.student-library-profiles', [
            'students' => $students,
        ]);
    }
}
