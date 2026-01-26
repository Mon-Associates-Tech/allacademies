<?php

namespace App\Livewire\Administrators;

use App\Models\AcademicSubject;
use App\Models\Assessment;
use App\Models\Book;
use App\Models\BookBorrowing;
use App\Models\BookCategory;
use App\Models\BookSubscription;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\Team;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Component;

class Overview extends Component
{
    public $selectedPeriod = 'week';

    public $showQuickActions = true;

    public function mount()
    {
        $this->selectedPeriod = 'week';
    }

    #[Computed]
    public function systemStats()
    {
        return [
            'total_users' => User::count(),
            'active_users' => User::where('last_seen_at', '>=', now()->subDays(7))->count(),
            'verified_users' => User::whereNotNull('email_verified_at')->count(),
            'pending_verification' => User::whereNull('email_verified_at')->count(),
        ];
    }

    #[Computed]
    public function userBreakdown()
    {
        return [
            'students' => Student::count(),
            'teachers' => Teacher::count(),
            'librarians' => User::where('role', 'librarian')->count(),
            'authors' => User::where('role', 'author')->count(),
            'parents' => User::where('role', 'parent')->count(),
            'administrators' => User::where('role', 'administrator')->count(),
        ];
    }

    #[Computed]
    public function libraryStats()
    {
        $startDate = $this->getPeriodStartDate();

        return [
            'total_books' => Book::count(),
            'published_books' => Book::where('status', 'published')->count(),
            'pending_approval' => Book::where('status', 'pending')->orWhereNull('status')->count(),
            'active_borrowings' => BookBorrowing::where('status', 'active')->count(),
            'overdue_books' => BookBorrowing::where('status', 'active')
                ->where('due_date', '<', now())->count(),
            'new_borrowings' => BookBorrowing::where('created_at', '>=', $startDate)->count(),
        ];
    }

    #[Computed]
    public function academicStats()
    {
        $startDate = $this->getPeriodStartDate();

        return [
            'total_subjects' => AcademicSubject::count(),
            'active_subscriptions' => BookSubscription::where('status', 'paid')
                ->where('end_date', '>', now())->count(),
            'recent_assessments' => Assessment::where('created_at', '>=', $startDate)->count(),
            'average_performance' => Assessment::where('created_at', '>=', $startDate)->avg('score') ?? 0,
        ];
    }

    #[Computed]
    public function systemHealth()
    {
        $overdueBooks = $this->libraryStats['overdue_books'];
        $pendingApprovals = $this->libraryStats['pending_approval'];
        $unverifiedUsers = $this->systemStats['pending_verification'];

        $issueCount = 0;
        $issues = [];

        if ($overdueBooks > 0) {
            $issueCount++;
            $issues[] = 'overdue_books';
        }

        if ($pendingApprovals > 5) {
            $issueCount++;
            $issues[] = 'pending_approvals';
        }

        if ($unverifiedUsers > 10) {
            $issueCount++;
            $issues[] = 'unverified_users';
        }

        return [
            'status' => $issueCount === 0 ? 'excellent' : ($issueCount <= 1 ? 'good' : ($issueCount <= 2 ? 'fair' : 'poor')),
            'issues_count' => $issueCount,
            'issues' => $issues,
            'score' => max(0, 100 - ($issueCount * 20)),
        ];
    }

    #[Computed]
    public function recentActivity()
    {
        return [
            'new_users' => User::where('created_at', '>=', now()->subDays(7))
                ->orderBy('created_at', 'desc')->take(5)->get(),
            'recent_borrowings' => BookBorrowing::with(['student.user', 'book'])
                ->where('created_at', '>=', now()->subDays(3))
                ->orderBy('created_at', 'desc')->take(5)->get(),
            'pending_approvals' => Book::with(['author.user'])
                ->where('status', 'pending')
                ->orWhereNull('status')
                ->orderBy('created_at', 'desc')->take(5)->get(),
        ];
    }

    #[Computed]
    public function systemAlerts()
    {
        $alerts = [];

        // Check for overdue books
        $overdueCount = BookBorrowing::where('status', 'active')
            ->where('due_date', '<', now())->count();
        if ($overdueCount > 0) {
            $alerts[] = [
                'type' => 'warning',
                'message' => "{$overdueCount} books are overdue",
                'action' => 'View Overdue Books',
                'route' => 'admin.book-management',
            ];
        }

        // Check for pending book approvals
        $pendingBooks = Book::where('status', 'pending')->orWhereNull('status')->count();
        if ($pendingBooks > 0) {
            $alerts[] = [
                'type' => 'info',
                'message' => "{$pendingBooks} books pending approval",
                'action' => 'Review Books',
                'route' => 'admin.book-approvals',
            ];
        }

        // Check for unverified users
        $unverifiedUsers = User::whereNull('email_verified_at')->count();
        if ($unverifiedUsers > 10) {
            $alerts[] = [
                'type' => 'warning',
                'message' => "{$unverifiedUsers} users pending email verification",
                'action' => 'Manage Users',
                'route' => 'users.index',
            ];
        }

        return $alerts;
    }

    #[Computed]
    public function performanceMetrics()
    {
        $startDate = $this->getPeriodStartDate();

        return [
            'user_growth' => $this->getUserGrowthTrend($startDate),
            'borrowing_trend' => $this->getBorrowingTrend($startDate),
            'popular_categories' => $this->getPopularBookCategories(),
            'active_teams' => Team::whereHas('members')->count(),
        ];
    }

    #[Computed]
    public function quickActionItems()
    {
        return [
            [
                'title' => 'Add New Student',
                'description' => 'Register a new student in the system',
                'icon' => 'user-plus',
                'route' => 'admin.student-management',
                'color' => 'blue',
            ],
            [
                'title' => 'Approve Books',
                'description' => 'Review and approve pending book submissions',
                'icon' => 'check-circle',
                'route' => 'admin.book-approvals',
                'color' => 'green',
                'badge' => Book::where('status', 'pending')->orWhereNull('status')->count(),
            ],
            [
                'title' => 'Manage Overdue',
                'description' => 'Handle overdue book returns',
                'icon' => 'exclamation-triangle',
                'route' => 'admin.book-management',
                'color' => 'red',
                'badge' => BookBorrowing::where('status', 'active')
                    ->where('due_date', '<', now())->count(),
            ],
            [
                'title' => 'User Impersonation',
                'description' => 'Login as another user for support',
                'icon' => 'user-secret',
                'route' => 'admin.users.impersonate',
                'color' => 'purple',
            ],
            [
                'title' => 'System Reports',
                'description' => 'Generate comprehensive system reports',
                'icon' => 'chart-bar',
                'route' => 'dashboard', // Replace with actual reports route
                'color' => 'indigo',
            ],
            [
                'title' => 'Academic Settings',
                'description' => 'Configure academic groups and levels',
                'icon' => 'cog',
                'route' => 'school-settings.index',
                'color' => 'gray',
            ],
        ];
    }

    private function getPeriodStartDate()
    {
        return match ($this->selectedPeriod) {
            'today' => Carbon::today(),
            'week' => Carbon::now()->startOfWeek(),
            'month' => Carbon::now()->startOfMonth(),
            'year' => Carbon::now()->startOfYear(),
            default => Carbon::now()->startOfWeek(),
        };
    }

    private function getUserGrowthTrend($startDate)
    {
        return User::select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('COUNT(*) as count')
        )
            ->where('created_at', '>=', $startDate)
            ->groupBy('date')
            ->orderBy('date')
            ->get();
    }

    private function getBorrowingTrend($startDate)
    {
        return BookBorrowing::select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('COUNT(*) as count')
        )
            ->where('created_at', '>=', $startDate)
            ->groupBy('date')
            ->orderBy('date')
            ->get();
    }

    private function getPopularBookCategories()
    {

        return BookCategory::select('book_categories.id', 'book_categories.name')
            ->selectRaw('COUNT(book_borrowings.id) as borrowings_count')
            ->join('book_category', 'book_categories.id', '=', 'book_category.category_id')
            ->join('books', 'book_category.book_id', '=', 'books.id')
            ->join('book_borrowings', 'books.id', '=', 'book_borrowings.book_id')
            ->where('book_borrowings.created_at', '>=', $this->getPeriodStartDate())
            ->groupBy('book_categories.id', 'book_categories.name')
            ->orderBy('borrowings_count', 'desc')
            ->take(5)
            ->get();
    }

    public function render()
    {
        return view('livewire.administrators.overview');
    }
}
