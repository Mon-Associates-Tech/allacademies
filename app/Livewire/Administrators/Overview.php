<?php

namespace App\Livewire\Administrators;

use App\Models\AcademicGroup;
use App\Models\AcademicLevel;
use App\Models\AcademicSubject;
use App\Models\Assessment;
use App\Models\Book;
use App\Models\BookBorrowing;
use App\Models\BookCategory;
use App\Models\BookSubscription;
use App\Models\ChatGroup;
use App\Models\ChatMessage;
use App\Models\Forum\ForumPost;
use App\Models\Forum\ForumTopic;
use App\Models\Message;
use App\Models\SchoolPayment;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\Team;
use App\Models\User;
use App\Models\UserLogin;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Component;
use Spatie\Activitylog\Models\Activity;

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
        $startDate = $this->getPeriodStartDate();

        return [
            'total_users' => User::count(),
            'active_users' => User::where('last_seen_at', '>=', now()->subDays(7))->count(),
            'verified_users' => User::whereNotNull('email_verified_at')->count(),
            'pending_verification' => User::whereNull('email_verified_at')->count(),
            'new_users_period' => User::where('created_at', '>=', $startDate)->count(),
            'active_today' => User::where('last_seen_at', '>=', now()->startOfDay())->count(),
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
            'administrators' => User::whereIn('role', ['admin', 'owner'])->count(),
            'moderators' => User::where('role', 'moderator')->count(),
        ];
    }

    #[Computed]
    public function userDistributionChartData(): array
    {
        $breakdown = $this->userBreakdown;

        return [
            'labels' => ['Students', 'Teachers', 'Librarians', 'Authors', 'Parents', 'Administrators', 'Moderators'],
            'data' => [
                $breakdown['students'],
                $breakdown['teachers'],
                $breakdown['librarians'],
                $breakdown['authors'],
                $breakdown['parents'],
                $breakdown['administrators'],
                $breakdown['moderators'],
            ],
            'colors' => [
                '#6366F1', // Indigo - Students
                '#F59E0B', // Amber - Teachers
                '#10B981', // Emerald - Librarians
                '#8B5CF6', // Purple - Authors
                '#EC4899', // Pink - Parents
                '#3B82F6', // Blue - Administrators
                '#EF4444', // Red - Moderators
            ],
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
            'returned_books' => BookBorrowing::where('status', 'returned')
                ->where('updated_at', '>=', $startDate)->count(),
        ];
    }

    #[Computed]
    public function academicStats()
    {
        $startDate = $this->getPeriodStartDate();

        return [
            'total_subjects' => AcademicSubject::count(),
            'total_groups' => AcademicGroup::count(),
            'total_levels' => AcademicLevel::count(),
            'active_subscriptions' => BookSubscription::where('status', 'paid')
                ->where('end_date', '>', now())->count(),
            'recent_assessments' => Assessment::where('created_at', '>=', $startDate)->count(),
            'average_performance' => Assessment::where('created_at', '>=', $startDate)->avg('score') ?? 0,
            'total_assessments' => Assessment::count(),
        ];
    }

    #[Computed]
    public function paymentStats()
    {
        $startDate = $this->getPeriodStartDate();

        $totalRevenue = SchoolPayment::where('status', 'succeeded')
            ->where('created_at', '>=', $startDate)
            ->sum('amount');

        $pendingPayments = SchoolPayment::where('status', 'pending')
            ->sum('amount');

        return [
            'total_revenue_period' => $totalRevenue,
            'pending_amount' => $pendingPayments,
            'successful_payments' => SchoolPayment::where('status', 'succeeded')
                ->where('created_at', '>=', $startDate)->count(),
            'pending_payments' => SchoolPayment::where('status', 'pending')->count(),
            'failed_payments' => SchoolPayment::where('status', 'failed')
                ->where('created_at', '>=', $startDate)->count(),
            'total_transactions' => SchoolPayment::where('created_at', '>=', $startDate)->count(),
        ];
    }

    #[Computed]
    public function messageStats()
    {
        $startDate = $this->getPeriodStartDate();

        return [
            'total_messages' => Message::count(),
            'messages_period' => Message::where('created_at', '>=', $startDate)->count(),
            'unread_messages' => Message::whereDoesntHave('readReceipts')->count(),
            'chat_groups' => ChatGroup::count(),
            'active_chat_groups' => ChatGroup::active()->count(),
            'chat_messages_period' => ChatMessage::where('created_at', '>=', $startDate)->count(),
        ];
    }

    #[Computed]
    public function loginStats()
    {
        $startDate = $this->getPeriodStartDate();

        return [
            'total_logins_period' => UserLogin::where('login_at', '>=', $startDate)->count(),
            'active_sessions' => UserLogin::activeSessions()->count(),
            'logins_today' => UserLogin::today()->count(),
            'unique_users_period' => UserLogin::where('login_at', '>=', $startDate)
                ->distinct('user_id')->count('user_id'),
            'avg_session_duration' => round(UserLogin::where('login_at', '>=', $startDate)
                ->whereNotNull('duration_minutes')
                ->avg('duration_minutes') ?? 0, 1),
        ];
    }

    #[Computed]
    public function forumStats()
    {
        $startDate = $this->getPeriodStartDate();

        return [
            'total_topics' => ForumTopic::count(),
            'new_topics_period' => ForumTopic::where('created_at', '>=', $startDate)->count(),
            'total_posts' => ForumPost::count(),
            'new_posts_period' => ForumPost::where('created_at', '>=', $startDate)->count(),
            'active_discussions' => ForumTopic::where('last_activity_at', '>=', $startDate)->count(),
        ];
    }

    #[Computed]
    public function activityStats()
    {
        $startDate = $this->getPeriodStartDate();

        return [
            'total_activities' => Activity::where('created_at', '>=', $startDate)->count(),
            'activities_today' => Activity::whereDate('created_at', today())->count(),
            'recent_activities' => Activity::with('causer')
                ->orderBy('created_at', 'desc')
                ->take(10)
                ->get(),
        ];
    }

    #[Computed]
    public function loginChartData(): array
    {
        $days = $this->selectedPeriod === 'today' ? 24 : ($this->selectedPeriod === 'week' ? 7 : ($this->selectedPeriod === 'month' ? 30 : 12));
        $labels = [];
        $data = [];

        if ($this->selectedPeriod === 'today') {
            // Hourly data for today
            for ($i = 0; $i < 24; $i++) {
                $hour = Carbon::today()->addHours($i);
                $labels[] = $hour->format('H:00');
                $data[] = UserLogin::whereBetween('login_at', [$hour, $hour->copy()->addHour()])->count();
            }
        } elseif ($this->selectedPeriod === 'year') {
            // Monthly data for the year
            for ($i = 11; $i >= 0; $i--) {
                $month = Carbon::now()->subMonths($i);
                $labels[] = $month->format('M');
                $data[] = UserLogin::whereYear('login_at', $month->year)
                    ->whereMonth('login_at', $month->month)
                    ->count();
            }
        } else {
            // Daily data for week/month
            for ($i = $days - 1; $i >= 0; $i--) {
                $date = Carbon::now()->subDays($i);
                $labels[] = $date->format('M d');
                $data[] = UserLogin::whereDate('login_at', $date)->count();
            }
        }

        return [
            'labels' => $labels,
            'data' => $data,
        ];
    }

    #[Computed]
    public function revenueChartData(): array
    {
        $days = $this->selectedPeriod === 'today' ? 24 : ($this->selectedPeriod === 'week' ? 7 : ($this->selectedPeriod === 'month' ? 30 : 12));
        $labels = [];
        $data = [];

        if ($this->selectedPeriod === 'today') {
            // Hourly data for today
            for ($i = 0; $i < 24; $i++) {
                $hour = Carbon::today()->addHours($i);
                $labels[] = $hour->format('H:00');
                $data[] = (float) SchoolPayment::where('status', 'succeeded')
                    ->whereBetween('created_at', [$hour, $hour->copy()->addHour()])
                    ->sum('amount');
            }
        } elseif ($this->selectedPeriod === 'year') {
            // Monthly data for the year
            for ($i = 11; $i >= 0; $i--) {
                $month = Carbon::now()->subMonths($i);
                $labels[] = $month->format('M');
                $data[] = (float) SchoolPayment::where('status', 'succeeded')
                    ->whereYear('created_at', $month->year)
                    ->whereMonth('created_at', $month->month)
                    ->sum('amount');
            }
        } else {
            // Daily data for week/month
            for ($i = $days - 1; $i >= 0; $i--) {
                $date = Carbon::now()->subDays($i);
                $labels[] = $date->format('M d');
                $data[] = (float) SchoolPayment::where('status', 'succeeded')
                    ->whereDate('created_at', $date)
                    ->sum('amount');
            }
        }

        return [
            'labels' => $labels,
            'data' => $data,
        ];
    }

    #[Computed]
    public function systemHealth()
    {
        $overdueBooks = $this->libraryStats['overdue_books'];
        $pendingApprovals = $this->libraryStats['pending_approval'];
        $unverifiedUsers = $this->systemStats['pending_verification'];
        $pendingPayments = $this->paymentStats['pending_payments'];

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

        if ($pendingPayments > 10) {
            $issueCount++;
            $issues[] = 'pending_payments';
        }

        return [
            'status' => $issueCount === 0 ? 'excellent' : ($issueCount <= 1 ? 'good' : ($issueCount <= 2 ? 'fair' : 'poor')),
            'issues_count' => $issueCount,
            'issues' => $issues,
            'score' => max(0, 100 - ($issueCount * 15)),
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
            'recent_payments' => SchoolPayment::with(['student.user'])
                ->orderBy('created_at', 'desc')->take(5)->get(),
            'recent_logins' => UserLogin::with('user')
                ->orderBy('login_at', 'desc')->take(5)->get(),
            'recent_messages' => Message::with('sender')
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

        // Check for pending payments
        $pendingPayments = SchoolPayment::where('status', 'pending')->count();
        if ($pendingPayments > 0) {
            $alerts[] = [
                'type' => 'info',
                'message' => "{$pendingPayments} payments pending processing",
                'action' => 'View Payments',
                'route' => 'admin.payments.index',
            ];
        }

        // Check for active sessions
        $activeSessions = UserLogin::activeSessions()->count();
        if ($activeSessions > 0) {
            $alerts[] = [
                'type' => 'success',
                'message' => "{$activeSessions} users currently online",
                'action' => 'View Activity',
                'route' => 'admin.logins',
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
            'payment_trend' => $this->getPaymentTrend($startDate),
            'login_trend' => $this->getLoginTrend($startDate),
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
                'title' => 'Add New Teacher',
                'description' => 'Register a new teacher in the system',
                'icon' => 'academic-cap',
                'route' => 'admin.teacher-management',
                'color' => 'indigo',
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
                'title' => 'View Payments',
                'description' => 'Monitor and manage school payments',
                'icon' => 'currency',
                'route' => 'admin.payments.index',
                'color' => 'emerald',
                'badge' => SchoolPayment::where('status', 'pending')->count(),
            ],
            [
                'title' => 'Message Center',
                'description' => 'Send and manage communications',
                'icon' => 'mail',
                'route' => 'admin.messages.index',
                'color' => 'cyan',
            ],
            [
                'title' => 'User Impersonation',
                'description' => 'Login as another user for support',
                'icon' => 'user-secret',
                'route' => 'admin.users.impersonate',
                'color' => 'purple',
            ],
            [
                'title' => 'Login Activity',
                'description' => 'Monitor user login sessions',
                'icon' => 'shield-check',
                'route' => 'admin.logins',
                'color' => 'amber',
                'badge' => UserLogin::activeSessions()->count(),
            ],
            [
                'title' => 'Activity Trail',
                'description' => 'View system activity logs',
                'icon' => 'clipboard-list',
                'route' => 'admin.activity-trail.index',
                'color' => 'slate',
            ],
            [
                'title' => 'Student Groups',
                'description' => 'Manage student group assignments',
                'icon' => 'user-group',
                'route' => 'admin.student-groups',
                'color' => 'teal',
            ],
            [
                'title' => 'Subject Management',
                'description' => 'Configure academic subjects',
                'icon' => 'book-open',
                'route' => 'admin.subject-management',
                'color' => 'rose',
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

    #[Computed]
    public function managementSummary()
    {
        return [
            'students' => [
                'total' => Student::count(),
                'active' => Student::whereHas('user', fn ($q) => $q->where('last_seen_at', '>=', now()->subDays(7)))->count(),
                'new_this_period' => Student::where('created_at', '>=', $this->getPeriodStartDate())->count(),
                'route' => 'admin.student-management',
                'icon' => 'academic-cap',
                'color' => 'blue',
            ],
            'teachers' => [
                'total' => Teacher::count(),
                'active' => Teacher::whereHas('user', fn ($q) => $q->where('last_seen_at', '>=', now()->subDays(7)))->count(),
                'new_this_period' => Teacher::where('created_at', '>=', $this->getPeriodStartDate())->count(),
                'route' => 'admin.teacher-management',
                'icon' => 'briefcase',
                'color' => 'indigo',
            ],
            'librarians' => [
                'total' => User::where('role', 'librarian')->count(),
                'active' => User::where('role', 'librarian')->where('last_seen_at', '>=', now()->subDays(7))->count(),
                'route' => 'admin.librarian-management',
                'icon' => 'library',
                'color' => 'amber',
            ],
            'parents' => [
                'total' => User::where('role', 'parent')->count(),
                'active' => User::where('role', 'parent')->where('last_seen_at', '>=', now()->subDays(7))->count(),
                'route' => 'admin.parent-management',
                'icon' => 'users',
                'color' => 'green',
            ],
            'authors' => [
                'total' => User::where('role', 'author')->count(),
                'active' => User::where('role', 'author')->where('last_seen_at', '>=', now()->subDays(7))->count(),
                'route' => 'admin.author-management',
                'icon' => 'pencil',
                'color' => 'purple',
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

    private function getPaymentTrend($startDate)
    {
        return SchoolPayment::select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('SUM(amount) as total'),
            DB::raw('COUNT(*) as count')
        )
            ->where('status', 'succeeded')
            ->where('created_at', '>=', $startDate)
            ->groupBy('date')
            ->orderBy('date')
            ->get();
    }

    private function getLoginTrend($startDate)
    {
        return UserLogin::select(
            DB::raw('DATE(login_at) as date'),
            DB::raw('COUNT(*) as count'),
            DB::raw('COUNT(DISTINCT user_id) as unique_users')
        )
            ->where('login_at', '>=', $startDate)
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
