<?php

namespace App\Livewire\Librarians;

use App\Models\Book;
use App\Models\BookBorrowing;
use App\Models\Student;
use Livewire\Component;
use Livewire\Attributes\Computed;
use Carbon\Carbon;

class LibraryDashboard extends Component
{
    public $selectedPeriod = 'week';

    #[Computed]
    public function totalBooks()
    {
        return Book::count();
    }

    #[Computed]
    public function availableBooks()
    {
        return Book::where('status', 'published')
            ->whereDoesntHave('borrowings', function($query) {
                $query->where('status', 'active');
            })
            ->count();
    }

    #[Computed]
    public function borrowedBooks()
    {
        return BookBorrowing::where('status', 'active')->count();
    }

    #[Computed]
    public function overdueBooks()
    {
        return BookBorrowing::where('status', 'active')
            ->where('due_date', '<', now())
            ->count();
    }

    #[Computed]
    public function recentBorrowings()
    {
        return BookBorrowing::with(['student.user', 'book'])
            ->where('status', 'active')
            ->latest()
            ->take(5)
            ->get();
    }

    #[Computed]
    public function topBorrowedBooks()
    {
        return Book::withCount(['borrowings' => function($query) {
            $query->where('created_at', '>=', $this->getPeriodStartDate());
        }])
        ->orderBy('borrowings_count', 'desc')
        ->take(5)
        ->get();
    }

    #[Computed]
    public function libraryStats()
    {
        $startDate = $this->getPeriodStartDate();

        return [
            'new_borrowings' => BookBorrowing::where('created_at', '>=', $startDate)->count(),
            'returned_books' => BookBorrowing::where('return_date', '>=', $startDate)->count(),
            'active_students' => Student::whereHas('borrowedBooks', function($query) use ($startDate) {
                $query->where('created_at', '>=', $startDate);
            })->count(),
            'overdue_rate' => $this->calculateOverdueRate(),
        ];
    }

    private function getPeriodStartDate()
    {
        return match($this->selectedPeriod) {
            'today' => Carbon::today(),
            'week' => Carbon::now()->startOfWeek(),
            'month' => Carbon::now()->startOfMonth(),
            'year' => Carbon::now()->startOfYear(),
            default => Carbon::now()->startOfWeek(),
        };
    }

    private function calculateOverdueRate()
    {
        $totalActive = BookBorrowing::where('status', 'active')->count();
        $overdue = BookBorrowing::where('status', 'active')
            ->where('due_date', '<', now())
            ->count();

        return $totalActive > 0 ? round(($overdue / $totalActive) * 100, 1) : 0;
    }

    public function render()
    {
        return view('livewire.librarians.library-dashboard');
    }
}
