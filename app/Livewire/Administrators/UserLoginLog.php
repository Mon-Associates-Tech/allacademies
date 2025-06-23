<?php

namespace App\Livewire\Administrators;

use App\Models\UserLogin;
use App\Services\UserLoginService;
use Livewire\Component;
use Livewire\WithPagination;
use Carbon\Carbon;

class UserLoginLog extends Component
{
    use WithPagination;

    public $searchTerm = '';
    public $userId = null;
    public $dateFrom = '';
    public $dateTo = '';
    public $statusFilter = 'all'; // all, active, completed
    public $logoutTypeFilter = 'all';
    public $sortBy = 'login_at';
    public $sortDirection = 'desc';
    public $viewMode = 'sessions'; // sessions, activities
    public $showFilters = false;

    protected $queryString = [
        'searchTerm' => ['except' => ''],
        'statusFilter' => ['except' => 'all'],
        'viewMode' => ['except' => 'sessions'],
        'page' => ['except' => 1]
    ];

    public function mount()
    {
        $this->dateFrom = Carbon::now()->subDays(7)->format('Y-m-d');
        $this->dateTo = Carbon::now()->format('Y-m-d');
    }

    public function updatingSearchTerm()
    {
        $this->resetPage();
    }

    public function toggleFilters()
    {
        $this->showFilters = !$this->showFilters;
    }

    public function toggleViewMode()
    {
        $this->viewMode = $this->viewMode === 'sessions' ? 'activities' : 'sessions';
        $this->resetPage();
    }

    public function sortBy($field)
    {
        if ($this->sortBy === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $field;
            $this->sortDirection = 'desc';
        }
        $this->resetPage();
    }

    public function forceLogout($sessionId)
    {
        $success = app(UserLoginService::class)->forceLogoutSpecificSession($sessionId);

        if ($success) {
            $this->dispatch('notify', [
                'type' => 'success',
                'message' => 'User session terminated successfully.'
            ]);
        } else {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Failed to terminate session or session already ended.'
            ]);
        }
    }

    private function invalidateUserSession($sessionId, $userId)
    {
        try {
            $sessionDriver = config('session.driver');

            switch ($sessionDriver) {
                case 'database':
                    // Delete from sessions table
                    DB::table('sessions')
                        ->where('id', $sessionId)
                        ->orWhere('user_id', $userId)
                        ->delete();
                    break;

                case 'file':
                    // Delete session file
                    $sessionPath = storage_path('framework/sessions/sess_' . $sessionId);
                    if (file_exists($sessionPath)) {
                        unlink($sessionPath);
                    }
                    break;

                case 'redis':
                    // Delete from Redis
                    $redis = app('redis');
                    $key = config('session.cookie') . ':' . $sessionId;
                    $redis->del($key);
                    break;
            }

            // Also invalidate remember token for the user
            DB::table('users')
                ->where('id', $userId)
                ->update(['remember_token' => null]);

        } catch (\Exception $e) {
            \Log::error('Failed to invalidate user session', [
                'session_id' => $sessionId,
                'user_id' => $userId,
                'error' => $e->getMessage()
            ]);
        }
    }

    public function render()
    {
        $query = UserLogin::with('user')
            ->when($this->searchTerm, function($query) {
                $query->whereHas('user', function($q) {
                    $q->where('name', 'like', '%' . $this->searchTerm . '%')
                      ->orWhere('email', 'like', '%' . $this->searchTerm . '%');
                })->orWhere('ip_address', 'like', '%' . $this->searchTerm . '%')
                  ->orWhere('device_type', 'like', '%' . $this->searchTerm . '%')
                  ->orWhere('browser', 'like', '%' . $this->searchTerm . '%');
            })
            ->when($this->userId, function($query) {
                $query->where('user_id', $this->userId);
            })
            ->when($this->dateFrom, function($query) {
                $query->whereDate('login_at', '>=', $this->dateFrom);
            })
            ->when($this->dateTo, function($query) {
                $query->whereDate('login_at', '<=', $this->dateTo);
            })
            ->when($this->statusFilter !== 'all', function($query) {
                if ($this->statusFilter === 'active') {
                    $query->activeSessions();
                } else {
                    $query->completedSessions();
                }
            })
            ->when($this->logoutTypeFilter !== 'all', function($query) {
                $query->where('logout_type', $this->logoutTypeFilter);
            });

        if ($this->viewMode === 'sessions') {
            $activities = $query->orderBy($this->sortBy, $this->sortDirection)
                ->paginate(15);
        } else {
            $activities = $query->orderBy($this->sortBy, $this->sortDirection)
                ->paginate(20);
        }

        $loginService = app(UserLoginService::class);

        $stats = [
            'total_sessions' => UserLogin::count(),
            'active_sessions' => $loginService->getActiveUserCount(), // Use improved method
            'unique_users_today' => UserLogin::today()->distinct('user_id')->count('user_id'),
            'total_users_online' => $loginService->getRealTimeActiveUserCount() // Use real-time count
        ];

        return view('livewire.administrators.user-logins', [
            'activities' => $activities,
            'stats' => $stats
        ]);
    }
}
