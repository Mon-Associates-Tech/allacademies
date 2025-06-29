<?php

namespace App\Livewire\Administrators;

use Livewire\Component;
use App\Models\User;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\Book;
use App\Models\Assessment;
use App\Models\BookBorrowing;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Cache;

class Overview extends Component
{
    public $timeRange = 'today';
    public $refreshInterval = 30; // seconds

    public $metrics = [
        'users' => [],
        'activity' => [],
        'system' => [],
        'performance' => []
    ];

    public $systemHealth = [];
    public $recentActivities = [];
    public $alerts = [];

    protected $listeners = ['refreshData' => 'loadData'];

    public function mount()
    {
        $this->loadData();
    }

    public function updatedTimeRange()
    {
        $this->loadData();
    }

    public function loadData()
    {
        $this->loadUserMetrics();
        $this->loadActivityMetrics();
        $this->loadSystemHealth();
        $this->loadRecentActivities();
        $this->loadAlerts();
    }

    private function loadUserMetrics()
    {
        $dateRange = $this->getDateRange();

        $this->metrics['users'] = [
            'total_users' => User::count(),
            'new_users' => User::whereBetween('created_at', $dateRange)->count(),
            'active_users' => User::where('is_online', true)->count(),
            'students' => Student::count(),
            'teachers' => Teacher::count(),
            'verified_users' => User::whereNotNull('email_verified_at')->count(),
        ];
    }

    private function loadActivityMetrics()
    {
        $dateRange = $this->getDateRange();

        $this->metrics['activity'] = [
            'assessments_completed' => Assessment::whereBetween('created_at', $dateRange)
                ->where('status', 'completed')
                ->count(),
            'books_borrowed' => BookBorrowing::whereBetween('created_at', $dateRange)->count(),
            'average_score' => Assessment::whereBetween('created_at', $dateRange)
                ->where('status', 'completed')
                ->avg('score') ?? 0,
            'login_sessions' => $this->getLoginSessions($dateRange),
        ];
    }

    private function loadSystemHealth()
    {
        $this->systemHealth = [
            'database' => $this->checkDatabaseHealth(),
            'cache' => $this->checkCacheHealth(),
            'storage' => $this->checkStorageHealth(),
            'queue' => $this->checkQueueHealth(),
        ];
    }

    private function loadRecentActivities()
    {
        $this->recentActivities = [
            'recent_users' => User::with(['student', 'teacher'])
                ->latest()
                ->limit(5)
                ->get()
                ->map(function ($user) {
                    return [
                        'type' => 'user_registration',
                        'message' => "New {$user->role} registered: {$user->name}",
                        'time' => $user->created_at,
                        'user' => $user->name,
                        'role' => $user->role
                    ];
                }),
            'recent_assessments' => Assessment::with(['student.user', 'subject'])
                ->where('status', 'completed')
                ->latest()
                ->limit(5)
                ->get()
                ->map(function ($assessment) {
                    return [
                        'type' => 'assessment_completed',
                        'message' => "{$assessment->student->user->name} completed {$assessment->subject->name}",
                        'time' => $assessment->created_at,
                        'score' => $assessment->score,
                        'max_score' => $assessment->max_score
                    ];
                })
        ];
    }

    private function loadAlerts()
    {
        $this->alerts = [
            'overdue_books' => BookBorrowing::where('due_date', '<', now())
                ->where('return_date', null)
                ->count(),
            'pending_approvals' => BookBorrowing::where('status', 'pending')->count(),
            'inactive_users' => User::where('last_seen_at', '<', now()->subDays(30))
                ->whereNotNull('last_seen_at')
                ->count(),
            'system_errors' => $this->getSystemErrorCount(),
        ];
    }

    private function getDateRange()
    {
        return match ($this->timeRange) {
            'today' => [now()->startOfDay(), now()->endOfDay()],
            'week' => [now()->startOfWeek(), now()->endOfWeek()],
            'month' => [now()->startOfMonth(), now()->endOfMonth()],
            'year' => [now()->startOfYear(), now()->endOfYear()],
            default => [now()->startOfDay(), now()->endOfDay()]
        };
    }

    private function checkDatabaseHealth()
    {
        try {
            DB::connection()->getPdo();
            $responseTime = $this->measureDatabaseResponseTime();
            return [
                'status' => $responseTime < 100 ? 'healthy' : 'warning',
                'response_time' => $responseTime,
                'message' => "Response time: {$responseTime}ms"
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'message' => 'Database connection failed'
            ];
        }
    }

    private function checkCacheHealth()
    {
        try {
            Cache::put('health_check', 'ok', 60);
            $cached = Cache::get('health_check');
            return [
                'status' => $cached === 'ok' ? 'healthy' : 'warning',
                'message' => $cached === 'ok' ? 'Cache working' : 'Cache issues detected'
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'message' => 'Cache unavailable'
            ];
        }
    }

    private function checkStorageHealth()
    {
        $freeSpace = disk_free_space(storage_path());
        $totalSpace = disk_total_space(storage_path());
        $usagePercent = (($totalSpace - $freeSpace) / $totalSpace) * 100;

        return [
            'status' => $usagePercent < 80 ? 'healthy' : ($usagePercent < 90 ? 'warning' : 'error'),
            'usage_percent' => round($usagePercent, 1),
            'message' => "Storage usage: " . round($usagePercent, 1) . "%"
        ];
    }

    private function checkQueueHealth()
    {
        // This would depend on your queue implementation
        return [
            'status' => 'healthy',
            'message' => 'Queue operational'
        ];
    }

    private function measureDatabaseResponseTime()
    {
        $start = microtime(true);
        DB::table('users')->limit(1)->get();
        return round((microtime(true) - $start) * 1000, 2);
    }

    private function getLoginSessions($dateRange)
    {
        // Implement based on your login tracking system
        return User::whereBetween('last_seen_at', $dateRange)->count();
    }

    private function getSystemErrorCount()
    {
        // Implement based on your error logging system
        return 0;
    }

    public function render()
    {
        return view('livewire.administrators.overview');
    }
}
