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

    /**
     * Get the school ID to filter statistics by.
     * Returns null for owners/super_admins viewing all schools,
     * or specific school_id for admins.
     */
    protected function getSchoolId(): ?int
    {
        $user = auth()->user();

        // Owners and super admins can see all schools (no filtering)
        if ($user->canAccessCrossSchool()) {
            // Check if they have selected a specific school context
            return getSchoolId();
        }

        // Admins and other school-bound users: restrict to their school
        return $user->school_id;
    }

    #[Computed]
    public function systemStats()
    {
        $startDate = $this->getPeriodStartDate();
        $schoolId = $this->getSchoolId();

        // Build base query for school-scoped users
        $userQuery = User::query();
        if ($schoolId) {
            $userQuery->where('school_id', $schoolId);
        }

        return [
            'total_users' => $userQuery->count(),
            'active_users' => (clone $userQuery)->where('last_seen_at', '>=', now()->subDays(7))->count(),
            'verified_users' => (clone $userQuery)->whereNotNull('email_verified_at')->count(),
            'pending_verification' => (clone $userQuery)->whereNull('email_verified_at')->count(),
            'new_users_period' => (clone $userQuery)->where('created_at', '>=', $startDate)->count(),
            'active_today' => (clone $userQuery)->where('last_seen_at', '>=', now()->startOfDay())->count(),
        ];
    }

    #[Computed]
    public function userBreakdown()
    {
        $schoolId = $this->getSchoolId();

        // Build base queries with school scoping
        $studentQuery = Student::query();
        $teacherQuery = Teacher::query();
        $userQuery = User::query();

        if ($schoolId) {
            $studentQuery->where('school_id', $schoolId);
            $teacherQuery->where('school_id', $schoolId);
            $userQuery->where('school_id', $schoolId);
        }

        return [
            'students' => $studentQuery->count(),
            'teachers' => $teacherQuery->count(),
            'librarians' => (clone $userQuery)->where('role', 'librarian')->count(),
            'authors' => (clone $userQuery)->where('role', 'author')->count(),
            'parents' => (clone $userQuery)->where('role', 'parent')->count(),
            'administrators' => (clone $userQuery)->whereIn('role', ['admin', 'owner'])->count(),
            'moderators' => (clone $userQuery)->where('role', 'moderator')->count(),
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
        $schoolId = $this->getSchoolId();

        // Build base queries with school scoping
        $bookQuery = Book::query();
        $borrowingQuery = BookBorrowing::query();

        if ($schoolId) {
            $bookQuery->where('school_id', $schoolId);
            // $borrowingQuery->where('school_id', $schoolId);
        }

        return [
            'total_books' => $bookQuery->count(),
            'published_books' => (clone $bookQuery)->where('status', 'published')->count(),
            'pending_approval' => (clone $bookQuery)->where('status', 'pending')->orWhereNull('status')->count(),
            'active_borrowings' => $borrowingQuery->where('status', 'active')->count(),
            'overdue_books' => (clone $borrowingQuery)->where('status', 'active')
                ->where('due_date', '<', now())->count(),
            'new_borrowings' => (clone $borrowingQuery)->where('created_at', '>=', $startDate)->count(),
            'returned_books' => (clone $borrowingQuery)->where('status', 'returned')
                ->where('updated_at', '>=', $startDate)->count(),
        ];
    }

    #[Computed]
    public function academicStats()
    {
        $startDate = $this->getPeriodStartDate();
        $schoolId = $this->getSchoolId();

        // Build base queries with school scoping
        $subjectQuery = AcademicSubject::query();
        $groupQuery = AcademicGroup::query();
        $levelQuery = AcademicLevel::query();
        $subscriptionQuery = BookSubscription::query();
        $assessmentQuery = Assessment::query();

        if ($schoolId) {
            $subjectQuery->where('school_id', $schoolId);
            $groupQuery->where('school_id', $schoolId);
            $levelQuery->where('school_id', $schoolId);
            // $subscriptionQuery->where('school_id', $schoolId);
            // $assessmentQuery->where('school_id', $schoolId);
        }

        return [
            'total_subjects' => $subjectQuery->count(),
            'total_groups' => $groupQuery->count(),
            'total_levels' => $levelQuery->count(),
            'active_subscriptions' => $subscriptionQuery->where('status', 'paid')
                ->where('end_date', '>', now())->count(),
            'recent_assessments' => (clone $assessmentQuery)->where('created_at', '>=', $startDate)->count(),
            'average_performance' => (clone $assessmentQuery)->where('created_at', '>=', $startDate)->avg('score') ?? 0,
            'total_assessments' => $assessmentQuery->count(),
        ];
    }

    #[Computed]
    public function paymentStats()
    {
        $startDate = $this->getPeriodStartDate();
        $schoolId = $this->getSchoolId();

        // School payments are always school-scoped
        $paymentQuery = SchoolPayment::query();
        if ($schoolId) {
            $paymentQuery->where('school_id', $schoolId);
        }

        $totalRevenue = $paymentQuery->where('status', 'succeeded')
            ->where('created_at', '>=', $startDate)
            ->sum('amount');

        $pendingPayments = (clone $paymentQuery)->where('status', 'pending')
            ->sum('amount');

        return [
            'total_revenue_period' => $totalRevenue,
            'pending_amount' => $pendingPayments,
            'successful_payments' => (clone $paymentQuery)->where('status', 'succeeded')
                ->where('created_at', '>=', $startDate)->count(),
            'pending_payments' => (clone $paymentQuery)->where('status', 'pending')->count(),
            'failed_payments' => (clone $paymentQuery)->where('status', 'failed')
                ->where('created_at', '>=', $startDate)->count(),
            'total_transactions' => (clone $paymentQuery)->where('created_at', '>=', $startDate)->count(),
        ];
    }

    #[Computed]
    public function messageStats()
    {
        $startDate = $this->getPeriodStartDate();
        $schoolId = $this->getSchoolId();

        // Messages are sent by users, scope by sender's school if needed
        $messageQuery = Message::query();
        if ($schoolId) {
            // Messages sent by users in this school
            $messageQuery->whereHas('sender', function ($q) use ($schoolId) {
                $q->where('school_id', $schoolId);
            });
        }

        return [
            'total_messages' => $messageQuery->count(),
            'messages_period' => (clone $messageQuery)->where('created_at', '>=', $startDate)->count(),
            'unread_messages' => (clone $messageQuery)->whereDoesntHave('readReceipts')->count(),
            'chat_groups' => ChatGroup::count(),
            'active_chat_groups' => ChatGroup::active()->count(),
            'chat_messages_period' => ChatMessage::where('created_at', '>=', $startDate)->count(),
        ];
    }

    #[Computed]
    public function loginStats()
    {
        $startDate = $this->getPeriodStartDate();
        $schoolId = $this->getSchoolId();

        // User logins should be scoped to school for admins
        $loginQuery = UserLogin::query();
        if ($schoolId) {
            $loginQuery->whereHas('user', function ($q) use ($schoolId) {
                $q->where('school_id', $schoolId);
            });
        }

        return [
            'total_logins_period' => $loginQuery->where('login_at', '>=', $startDate)->count(),
            'active_sessions' => (clone $loginQuery)->activeSessions()->count(),
            'logins_today' => (clone $loginQuery)->today()->count(),
            'unique_users_period' => (clone $loginQuery)->where('login_at', '>=', $startDate)
                ->distinct('user_id')->count('user_id'),
            'avg_session_duration' => round((clone $loginQuery)->where('login_at', '>=', $startDate)
                ->whereNotNull('duration_minutes')
                ->avg('duration_minutes') ?? 0, 1),
        ];
    }

    #[Computed]
    public function forumStats()
    {
        $startDate = $this->getPeriodStartDate();
        $schoolId = $this->getSchoolId();

        // Forum topics and posts should be scoped by user's school
        $topicQuery = ForumTopic::query();
        $postQuery = ForumPost::query();

        if ($schoolId) {
            $topicQuery->whereHas('user', function ($q) use ($schoolId) {
                $q->where('school_id', $schoolId);
            });
            $postQuery->whereHas('user', function ($q) use ($schoolId) {
                $q->where('school_id', $schoolId);
            });
        }

        return [
            'total_topics' => $topicQuery->count(),
            'new_topics_period' => (clone $topicQuery)->where('created_at', '>=', $startDate)->count(),
            'total_posts' => $postQuery->count(),
            'new_posts_period' => (clone $postQuery)->where('created_at', '>=', $startDate)->count(),
            'active_discussions' => (clone $topicQuery)->where('last_activity_at', '>=', $startDate)->count(),
        ];
    }

    #[Computed]
    public function activityStats()
    {
        $startDate = $this->getPeriodStartDate();
        $schoolId = $this->getSchoolId();

        // Activities should be scoped to school for admins
        $activityQuery = Activity::query();
        if ($schoolId) {
            $activityQuery->where(function ($q) use ($schoolId) {
                // Scope by causer (user who performed action)
                $q->whereHasMorph('causer', [User::class], function ($q) use ($schoolId) {
                    $q->where('school_id', $schoolId);
                })
                    // Or scope by subject (the model being acted upon)
                    ->orWhereHasMorph('subject', '*', function ($q) use ($schoolId) {
                        $q->where('school_id', $schoolId);
                    });
            });
        }

        return [
            'total_activities' => $activityQuery->where('created_at', '>=', $startDate)->count(),
            'activities_today' => (clone $activityQuery)->whereDate('created_at', today())->count(),
            'recent_activities' => (clone $activityQuery)->with('causer')
                ->orderBy('created_at', 'desc')
                ->take(10)
                ->get(),
        ];
    }

    #[Computed]
    public function loginChartData(): array
    {
        $days = $this->selectedPeriod === 'today' ? 24 : ($this->selectedPeriod === 'week' ? 7 : ($this->selectedPeriod === 'month' ? 30 : 12));
        $schoolId = $this->getSchoolId();
        $labels = [];
        $data = [];

        // Build base query with school scoping
        $loginQuery = UserLogin::query();
        if ($schoolId) {
            $loginQuery->whereHas('user', function ($q) use ($schoolId) {
                $q->where('school_id', $schoolId);
            });
        }

        if ($this->selectedPeriod === 'today') {
            // Hourly data for today
            for ($i = 0; $i < 24; $i++) {
                $hour = Carbon::today()->addHours($i);
                $labels[] = $hour->format('H:00');
                $data[] = (clone $loginQuery)->whereBetween('login_at', [$hour, $hour->copy()->addHour()])->count();
            }
        } elseif ($this->selectedPeriod === 'year') {
            // Monthly data for the year
            for ($i = 11; $i >= 0; $i--) {
                $month = Carbon::now()->subMonths($i);
                $labels[] = $month->format('M');
                $data[] = (clone $loginQuery)->whereYear('login_at', $month->year)
                    ->whereMonth('login_at', $month->month)
                    ->count();
            }
        } else {
            // Daily data for week/month
            for ($i = $days - 1; $i >= 0; $i--) {
                $date = Carbon::now()->subDays($i);
                $labels[] = $date->format('M d');
                $data[] = (clone $loginQuery)->whereDate('login_at', $date)->count();
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
        $schoolId = $this->getSchoolId();
        $labels = [];
        $data = [];

        // Build base query with school scoping
        $paymentQuery = SchoolPayment::where('status', 'succeeded');
        if ($schoolId) {
            $paymentQuery->where('school_id', $schoolId);
        }

        if ($this->selectedPeriod === 'today') {
            // Hourly data for today
            for ($i = 0; $i < 24; $i++) {
                $hour = Carbon::today()->addHours($i);
                $labels[] = $hour->format('H:00');
                $data[] = (float) (clone $paymentQuery)->whereBetween('created_at', [$hour, $hour->copy()->addHour()])
                    ->sum('amount');
            }
        } elseif ($this->selectedPeriod === 'year') {
            // Monthly data for the year
            for ($i = 11; $i >= 0; $i--) {
                $month = Carbon::now()->subMonths($i);
                $labels[] = $month->format('M');
                $data[] = (float) (clone $paymentQuery)->whereYear('created_at', $month->year)
                    ->whereMonth('created_at', $month->month)
                    ->sum('amount');
            }
        } else {
            // Daily data for week/month
            for ($i = $days - 1; $i >= 0; $i--) {
                $date = Carbon::now()->subDays($i);
                $labels[] = $date->format('M d');
                $data[] = (float) (clone $paymentQuery)->whereDate('created_at', $date)
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
        $schoolId = $this->getSchoolId();

        // Build queries with school scoping
        $userQuery = User::query();
        $borrowingQuery = BookBorrowing::query();
        $bookQuery = Book::query();
        $paymentQuery = SchoolPayment::query();
        $loginQuery = UserLogin::query();
        $messageQuery = Message::query();

        if ($schoolId) {
            $userQuery->where('school_id', $schoolId);
            // $borrowingQuery->where('school_id', $schoolId);
            $bookQuery->where('school_id', $schoolId);
            $paymentQuery->where('school_id', $schoolId);
            $loginQuery->whereHas('user', function ($q) use ($schoolId) {
                $q->where('school_id', $schoolId);
            });
            $messageQuery->whereHas('sender', function ($q) use ($schoolId) {
                $q->where('school_id', $schoolId);
            });
        }

        return [
            'new_users' => $userQuery->where('created_at', '>=', now()->subDays(7))
                ->orderBy('created_at', 'desc')->take(5)->get(),
            'recent_borrowings' => $borrowingQuery->with(['student.user', 'book'])
                ->where('created_at', '>=', now()->subDays(3))
                ->orderBy('created_at', 'desc')->take(5)->get(),
            'pending_approvals' => $bookQuery->with(['author.user'])
                ->where('status', 'pending')
                ->orWhereNull('status')
                ->orderBy('created_at', 'desc')->take(5)->get(),
            'recent_payments' => $paymentQuery->with(['student.user'])
                ->orderBy('created_at', 'desc')->take(5)->get(),
            'recent_logins' => $loginQuery->with('user')
                ->orderBy('login_at', 'desc')->take(5)->get(),
            'recent_messages' => $messageQuery->with('sender')
                ->orderBy('created_at', 'desc')->take(5)->get(),
        ];
    }

    #[Computed]
    public function systemAlerts()
    {
        $schoolId = $this->getSchoolId();
        $alerts = [];

        // Check for overdue books (school-scoped)
        $overdueQuery = BookBorrowing::where('status', 'active')
            ->where('due_date', '<', now());
        if ($schoolId) {
            // $overdueQuery->where('school_id', $schoolId);
        }
        $overdueCount = $overdueQuery->count();

        if ($overdueCount > 0) {
            $alerts[] = [
                'type' => 'warning',
                'message' => "{$overdueCount} books are overdue",
                'action' => 'View Overdue Books',
                'route' => 'admin.book-management',
            ];
        }

        // Check for pending book approvals (school-scoped)
        $pendingBooksQuery = Book::where('status', 'pending')->orWhereNull('status');
        if ($schoolId) {
            $pendingBooksQuery->where('school_id', $schoolId);
        }
        $pendingBooks = $pendingBooksQuery->count();

        if ($pendingBooks > 0) {
            $alerts[] = [
                'type' => 'info',
                'message' => "{$pendingBooks} books pending approval",
                'action' => 'Review Books',
                'route' => 'admin.book-approvals',
            ];
        }

        // Check for unverified users (school-scoped)
        $unverifiedQuery = User::whereNull('email_verified_at');
        if ($schoolId) {
            $unverifiedQuery->where('school_id', $schoolId);
        }
        $unverifiedUsers = $unverifiedQuery->count();

        if ($unverifiedUsers > 10) {
            $alerts[] = [
                'type' => 'warning',
                'message' => "{$unverifiedUsers} users pending email verification",
                'action' => 'Manage Users',
                'route' => 'users.index',
            ];
        }

        // Check for pending payments (school-scoped)
        $pendingPaymentsQuery = SchoolPayment::where('status', 'pending');
        if ($schoolId) {
            $pendingPaymentsQuery->where('school_id', $schoolId);
        }
        $pendingPayments = $pendingPaymentsQuery->count();

        if ($pendingPayments > 0) {
            $alerts[] = [
                'type' => 'info',
                'message' => "{$pendingPayments} payments pending processing",
                'action' => 'View Payments',
                'route' => 'admin.payments.index',
            ];
        }

        // Check for active sessions (school-scoped)
        $activeSessionsQuery = UserLogin::activeSessions();
        if ($schoolId) {
            $activeSessionsQuery->whereHas('user', function ($q) use ($schoolId) {
                $q->where('school_id', $schoolId);
            });
        }
        $activeSessions = $activeSessionsQuery->count();

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
        $schoolId = $this->getSchoolId();

        return [
            'user_growth' => $this->getUserGrowthTrend($startDate),
            'borrowing_trend' => $this->getBorrowingTrend($startDate),
            'popular_categories' => $this->getPopularBookCategories(),
            'active_teams' => Team::when($schoolId, fn($q) => $q->where('school_id', $schoolId))->whereHas('members')->count(),
            'payment_trend' => $this->getPaymentTrend($startDate),
            'login_trend' => $this->getLoginTrend($startDate),
        ];
    }

    #[Computed]
    public function quickActionItems()
    {
        $schoolId = $this->getSchoolId();

        // Build queries with school scoping
        $bookQuery = Book::query();
        $borrowingQuery = BookBorrowing::query();
        $paymentQuery = SchoolPayment::query();
        $loginQuery = UserLogin::query();

        if ($schoolId) {
            $bookQuery->where('school_id', $schoolId);
            // $borrowingQuery->where('school_id', $schoolId);
            $paymentQuery->where('school_id', $schoolId);
            $loginQuery->whereHas('user', function ($q) use ($schoolId) {
                $q->where('school_id', $schoolId);
            });
        }


        $actions =  [
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
                'title' => 'View Payments',
                'description' => 'Monitor and manage school payments',
                'icon' => 'currency',
                'route' => 'admin.payments.index',
                'color' => 'emerald',
                'badge' => $paymentQuery->where('status', 'pending')->count(),
            ],
            [
                'title' => 'Message Center',
                'description' => 'Send and manage communications',
                'icon' => 'mail',
                'route' => 'admin.messages.index',
                'color' => 'cyan',
            ],
            [
                'title' => 'Login Activity',
                'description' => 'Monitor user login sessions',
                'icon' => 'shield-check',
                'route' => 'admin.logins',
                'color' => 'amber',
                'badge' => $loginQuery->activeSessions()->count(),
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
                'title' => 'Academic Settings',
                'description' => 'Configure academic groups and levels',
                'icon' => 'cog',
                'route' => 'school-settings.index',
                'color' => 'gray',
            ],
        ];

        if (auth()->user()->role->value == 'owner') {
            $actions[] =
                [
                    'title' => 'Subject Management',
                    'description' => 'Configure academic subjects',
                    'icon' => 'book-open',
                    'route' => 'admin.subject-management',
                    'color' => 'rose',
                ];
            $actions[] =
                [
                    'title' => 'Approve Books',
                    'description' => 'Review and approve pending book submissions',
                    'icon' => 'check-circle',
                    'route' => 'admin.book-approvals',
                    'color' => 'green',
                    'badge' => $bookQuery->where('status', 'pending')->orWhereNull('status')->count(),
                ];

            $actions[] =
                [
                    'title' => 'Manage Overdue',
                    'description' => 'Handle overdue book returns',
                    'icon' => 'exclamation-triangle',
                    'route' => 'admin.book-management',
                    'color' => 'red',
                    'badge' => $borrowingQuery->where('status', 'active')
                        ->where('due_date', '<', now())->count(),
                ];
        }

        return $actions;
    }

    #[Computed]
    public function managementSummary()
    {
        $schoolId = $this->getSchoolId();

        // Build queries with school scoping
        $studentQuery = Student::query();
        $teacherQuery = Teacher::query();
        $userQuery = User::query();

        if ($schoolId) {
            $studentQuery->where('school_id', $schoolId);
            $teacherQuery->where('school_id', $schoolId);
            $userQuery->where('school_id', $schoolId);
        }

        return [
            'students' => [
                'total' => $studentQuery->count(),
                'active' => $studentQuery->whereHas('user', fn($q) => $q->where('last_seen_at', '>=', now()->subDays(7)))->count(),
                'new_this_period' => (clone $studentQuery)->where('created_at', '>=', $this->getPeriodStartDate())->count(),
                'route' => 'admin.student-management',
                'icon' => 'academic-cap',
                'color' => 'blue',
            ],
            'teachers' => [
                'total' => $teacherQuery->count(),
                'active' => $teacherQuery->whereHas('user', fn($q) => $q->where('last_seen_at', '>=', now()->subDays(7)))->count(),
                'new_this_period' => (clone $teacherQuery)->where('created_at', '>=', $this->getPeriodStartDate())->count(),
                'route' => 'admin.teacher-management',
                'icon' => 'briefcase',
                'color' => 'indigo',
            ],
            'librarians' => [
                'total' => (clone $userQuery)->where('role', 'librarian')->count(),
                'active' => (clone $userQuery)->where('role', 'librarian')->where('last_seen_at', '>=', now()->subDays(7))->count(),
                'route' => 'admin.librarian-management',
                'icon' => 'library',
                'color' => 'amber',
            ],
            'parents' => [
                'total' => (clone $userQuery)->where('role', 'parent')->count(),
                'active' => (clone $userQuery)->where('role', 'parent')->where('last_seen_at', '>=', now()->subDays(7))->count(),
                'route' => 'admin.parent-management',
                'icon' => 'users',
                'color' => 'green',
            ],
            'authors' => [
                'total' => (clone $userQuery)->where('role', 'author')->count(),
                'active' => (clone $userQuery)->where('role', 'author')->where('last_seen_at', '>=', now()->subDays(7))->count(),
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
        $schoolId = $this->getSchoolId();

        return User::select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('COUNT(*) as count')
        )
            ->when($schoolId, fn($q) => $q->where('school_id', $schoolId))
            ->where('created_at', '>=', $startDate)
            ->groupBy('date')
            ->orderBy('date')
            ->get();
    }

    private function getBorrowingTrend($startDate)
    {
        $schoolId = $this->getSchoolId();

        return BookBorrowing::select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('COUNT(*) as count')
        )
            ->when($schoolId, fn($q) => $q->where('school_id', $schoolId))
            ->where('created_at', '>=', $startDate)
            ->groupBy('date')
            ->orderBy('date')
            ->get();
    }

    private function getPaymentTrend($startDate)
    {
        $schoolId = $this->getSchoolId();

        return SchoolPayment::select(
            DB::raw('DATE(created_at) as date'),
            DB::raw('SUM(amount) as total'),
            DB::raw('COUNT(*) as count')
        )
            ->where('status', 'succeeded')
            ->when($schoolId, fn($q) => $q->where('school_id', $schoolId))
            ->where('created_at', '>=', $startDate)
            ->groupBy('date')
            ->orderBy('date')
            ->get();
    }

    private function getLoginTrend($startDate)
    {
        $schoolId = $this->getSchoolId();

        return UserLogin::select(
            DB::raw('DATE(login_at) as date'),
            DB::raw('COUNT(*) as count'),
            DB::raw('COUNT(DISTINCT user_id) as unique_users')
        )
            ->when($schoolId, function ($q) use ($schoolId) {
                $q->whereHas('user', function ($subQ) use ($schoolId) {
                    $subQ->where('school_id', $schoolId);
                });
            })
            ->where('login_at', '>=', $startDate)
            ->groupBy('date')
            ->orderBy('date')
            ->get();
    }

    private function getPopularBookCategories()
    {
        $schoolId = $this->getSchoolId();

        return BookCategory::select('book_categories.id', 'book_categories.name')
            ->selectRaw('COUNT(book_borrowings.id) as borrowings_count')
            ->join('book_category', 'book_categories.id', '=', 'book_category.category_id')
            ->join('books', 'book_category.book_id', '=', 'books.id')
            ->join('book_borrowings', 'books.id', '=', 'book_borrowings.book_id')
            ->when($schoolId, fn($q) => $q->where('book_borrowings.school_id', $schoolId))
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
