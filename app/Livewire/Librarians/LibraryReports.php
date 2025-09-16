<?php

namespace App\Livewire\Librarians;

use App\Models\Book;
use App\Models\Book as BookCopy;
use App\Models\BookBorrowing as BookBorrow;
use App\Models\Student;
use Carbon\Carbon;
use Livewire\Component;

class LibraryReports extends Component
{
    public $reportType = 'overview';
    public $dateRange = '30';
    public $startDate = null;
    public $endDate = null;
    public $selectedBook = null;
    public $selectedStudent = null;

    // Report data
    public $reportData = [];
    public $chartData = [];

    public function mount()
    {
        $this->startDate = now()->subDays(30)->format('Y-m-d');
        $this->endDate = now()->format('Y-m-d');
        $this->generateReport();
    }

    public function updatedReportType()
    {
        $this->generateReport();
    }

    public function updatedDateRange()
    {
        switch ($this->dateRange) {
            case '7':
                $this->startDate = now()->subDays(7)->format('Y-m-d');
                break;
            case '30':
                $this->startDate = now()->subDays(30)->format('Y-m-d');
                break;
            case '90':
                $this->startDate = now()->subDays(90)->format('Y-m-d');
                break;
            case '365':
                $this->startDate = now()->subDays(365)->format('Y-m-d');
                break;
        }
        $this->endDate = now()->format('Y-m-d');
        $this->generateReport();
    }

    public function updatedStartDate()
    {
        $this->generateReport();
    }

    public function updatedEndDate()
    {
        $this->generateReport();
    }

    public function generateReport()
    {
        $startDate = Carbon::parse($this->startDate);
        $endDate = Carbon::parse($this->endDate);

        switch ($this->reportType) {
            case 'overview':
                $this->generateOverviewReport($startDate, $endDate);
                break;
            case 'borrowing':
                $this->generateBorrowingReport($startDate, $endDate);
                break;
            case 'returns':
                $this->generateReturnsReport($startDate, $endDate);
                break;
            case 'overdue':
                $this->generateOverdueReport($startDate, $endDate);
                break;
            case 'popular_books':
                $this->generatePopularBooksReport($startDate, $endDate);
                break;
            case 'student_activity':
                $this->generateStudentActivityReport($startDate, $endDate);
                break;
            case 'inventory':
                $this->generateInventoryReport();
                break;
        }
    }

    private function generateOverviewReport($startDate, $endDate)
    {
        $this->reportData = [
            'total_books' => Book::count(),
            'total_copies' => BookCopy::count(),
            'available_copies' => BookCopy::where('status', 'available')->count(),
            'borrowed_copies' => BookCopy::where('status', 'borrowed')->count(),
            'total_students' => Student::count(),
            'active_borrowers' => BookBorrow::whereBetween('borrow_date', [$startDate, $endDate])
                ->distinct('user_id')->count(),
            'total_borrows' => BookBorrow::whereBetween('borrow_date', [$startDate, $endDate])->count(),
            'total_returns' => BookBorrow::whereBetween('return_date', [$startDate, $endDate])->count(),
            'overdue_books' => BookBorrow::whereNull('return_date')
                ->where('due_date', '<', now())->count(),
            'late_fees_collected' => BookBorrow::whereBetween('return_date', [$startDate, $endDate])
                ->sum('late_fee'),
        ];

        // Daily activity chart data
        $dailyData = [];
        for ($date = $startDate->copy(); $date->lte($endDate); $date->addDay()) {
            $borrowCount = BookBorrow::whereDate('borrow_date', $date)->count();
            $returnCount = BookBorrow::whereDate('return_date', $date)->count();

            $dailyData[] = [
                'date' => $date->format('Y-m-d'),
                'borrows' => $borrowCount,
                'returns' => $returnCount,
            ];
        }

        $this->chartData = [
            'daily_activity' => $dailyData,
        ];
    }

    private function generateBorrowingReport($startDate, $endDate)
    {
        $borrows = BookBorrow::with(['bookCopy.book', 'student'])
            ->whereBetween('borrowed_at', [$startDate, $endDate])
            ->get();

        $this->reportData = [
            'total_borrows' => $borrows->count(),
            'unique_students' => $borrows->unique('student_id')->count(),
            'unique_books' => $borrows->unique('bookCopy.book_id')->count(),
            'average_per_day' => round($borrows->count() / max(1, $startDate->diffInDays($endDate)), 2),
            'peak_day' => $borrows->groupBy(function ($borrow) {
                return $borrow->borrowed_at->format('Y-m-d');
            })->sortByDesc(function ($group) {
                return $group->count();
            })->keys()->first(),
            'borrowing_trends' => $borrows->groupBy(function ($borrow) {
                return $borrow->borrowed_at->format('Y-m-d');
            })->map(function ($group) {
                return $group->count();
            })->toArray(),
        ];

        // Most active students
        $this->reportData['most_active_students'] = $borrows->groupBy('student_id')
            ->map(function ($group) {
                return [
                    'student' => $group->first()->student,
                    'count' => $group->count(),
                ];
            })->sortByDesc('count')->take(10)->values()->toArray();
    }

    private function generateReturnsReport($startDate, $endDate)
    {
        $returns = BookBorrow::with(['bookCopy.book', 'student'])
            ->whereBetween('returned_at', [$startDate, $endDate])
            ->get();

        $this->reportData = [
            'total_returns' => $returns->count(),
            'on_time_returns' => $returns->where('returned_at', '<=', 'expected_return_date')->count(),
            'late_returns' => $returns->where('returned_at', '>', 'expected_return_date')->count(),
            'average_days_borrowed' => round($returns->avg(function ($return) {
                return $return->borrowed_at->diffInDays($return->returned_at);
            }), 2),
            'total_late_fees' => $returns->sum('late_fee'),
            'damaged_returns' => $returns->where('return_condition', 'damaged')->count(),
            'condition_breakdown' => $returns->groupBy('return_condition')
                ->map(function ($group) {
                    return $group->count();
                })->toArray(),
        ];
    }

    private function generateOverdueReport($startDate, $endDate)
    {
        $overdueBooks = BookBorrow::with(['bookCopy.book', 'student'])
            ->whereNull('returned_at')
            ->where('expected_return_date', '<', now())
            ->get();

        $this->reportData = [
            'total_overdue' => $overdueBooks->count(),
            'recent_overdue' => $overdueBooks->where('expected_return_date', '>=', now()->subDays(7))->count(),
            'moderate_overdue' => $overdueBooks->where('expected_return_date', '>=', now()->subDays(30))
                ->where('expected_return_date', '<', now()->subDays(7))->count(),
            'severe_overdue' => $overdueBooks->where('expected_return_date', '<', now()->subDays(30))->count(),
            'total_potential_fees' => $overdueBooks->sum(function ($borrow) {
                return max(0, now()->diffInDays($borrow->expected_return_date)) * 0.50;
            }),
            'most_overdue_students' => $overdueBooks->groupBy('student_id')
                ->map(function ($group) {
                    return [
                        'student' => $group->first()->student,
                        'count' => $group->count(),
                        'total_days_overdue' => $group->sum(function ($borrow) {
                            return max(0, now()->diffInDays($borrow->expected_return_date));
                        }),
                    ];
                })->sortByDesc('total_days_overdue')->take(10)->values()->toArray(),
        ];
    }

    private function generatePopularBooksReport($startDate, $endDate)
    {
        $popularBooks = BookBorrow::with(['bookCopy.book'])
            ->whereBetween('borrowed_at', [$startDate, $endDate])
            ->get()
            ->groupBy('bookCopy.book_id')
            ->map(function ($group) {
                return [
                    'book' => $group->first()->bookCopy->book,
                    'borrow_count' => $group->count(),
                    'unique_borrowers' => $group->unique('student_id')->count(),
                ];
            })
            ->sortByDesc('borrow_count')
            ->take(20)
            ->values()
            ->toArray();

        $this->reportData = [
            'popular_books' => $popularBooks,
            'total_unique_books_borrowed' => BookBorrow::whereBetween('borrowed_at', [$startDate, $endDate])
                ->distinct('bookCopy.book_id')->count(),
        ];
    }

    private function generateStudentActivityReport($startDate, $endDate)
    {
        $studentActivity = Student::with(['bookBorrows' => function ($query) use ($startDate, $endDate) {
            $query->whereBetween('borrowed_at', [$startDate, $endDate]);
        }])
        ->get()
        ->map(function ($student) {
            return [
                'student' => $student,
                'borrow_count' => $student->bookBorrows->count(),
                'books_returned' => $student->bookBorrows->whereNotNull('returned_at')->count(),
                'overdue_books' => $student->bookBorrows->whereNull('returned_at')
                    ->where('expected_return_date', '<', now())->count(),
                'late_fees' => $student->bookBorrows->sum('late_fee'),
            ];
        })
        ->sortByDesc('borrow_count')
        ->take(50)
        ->values()
        ->toArray();

        $this->reportData = [
            'student_activity' => $studentActivity,
            'active_students' => collect($studentActivity)->where('borrow_count', '>', 0)->count(),
        ];
    }

    private function generateInventoryReport()
    {
        $books = Book::with(['copies', 'category'])
            ->withCount([
                'copies as total_copies',
                'copies as available_copies' => function ($query) {
                    $query->where('status', 'available');
                },
                'copies as borrowed_copies' => function ($query) {
                    $query->where('status', 'borrowed');
                },
                'copies as damaged_copies' => function ($query) {
                    $query->where('status', 'damaged');
                },
                'copies as lost_copies' => function ($query) {
                    $query->where('status', 'lost');
                },
            ])
            ->get();

        $this->reportData = [
            'total_books' => $books->count(),
            'total_copies' => $books->sum('total_copies'),
            'available_copies' => $books->sum('available_copies'),
            'borrowed_copies' => $books->sum('borrowed_copies'),
            'damaged_copies' => $books->sum('damaged_copies'),
            'lost_copies' => $books->sum('lost_copies'),
            'out_of_stock_books' => $books->where('available_copies', 0)->count(),
            'low_stock_books' => $books->where('available_copies', '>', 0)
                ->where('available_copies', '<=', 2)->count(),
            'books_by_category' => $books->groupBy('category.name')
                ->map(function ($group) {
                    return [
                        'category' => $group->first()->category->name ?? 'Uncategorized',
                        'book_count' => $group->count(),
                        'total_copies' => $group->sum('total_copies'),
                        'available_copies' => $group->sum('available_copies'),
                    ];
                })->values()->toArray(),
            'inventory_details' => $books->toArray(),
        ];
    }

    public function exportReport()
    {
        // This would generate a CSV or PDF export
        // Implementation depends on your export library choice
        session()->flash('success', 'Report export functionality would be implemented here.');
    }

    public function render()
    {
        return view('livewire.librarians.library-reports');
    }
}
