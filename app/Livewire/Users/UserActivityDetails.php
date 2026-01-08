<?php

namespace App\Livewire\Users;

use App\Models\User;
use App\Models\LoginActivity;
use App\Services\UserLoginService;
use Livewire\Component;
use Livewire\WithPagination;

class UserActivityDetails extends Component
{
    use WithPagination;

    public User $user;
    public $searchTerm = '';
    public $statusFilter = 'all'; // all, active, completed
    public $dateFilter = 'all'; // all, today, week, month
    public $showStats = true;

    public function mount(User $user)
    {
        $this->user = $user;
    }

    public function forceLogout($sessionId)
    {
        $service = app(UserLoginService::class);

        if ($service->forceLogoutSpecificSession($sessionId)) {
            $this->dispatch('session-logged-out');
            session()->flash('success', 'Session has been terminated successfully.');
        } else {
            session()->flash('error', 'Failed to terminate session.');
        }
    }

    public function render()
    {
        $query = LoginActivity::where('user_id', $this->user->id)
            ->when($this->searchTerm, function($q) {
                $q->where(function($query) {
                    $query->where('ip_address', 'like', '%' . $this->searchTerm . '%')
                        ->orWhere('browser', 'like', '%' . $this->searchTerm . '%')
                        ->orWhere('platform', 'like', '%' . $this->searchTerm . '%')
                        ->orWhere('device_type', 'like', '%' . $this->searchTerm . '%')
                        ->orWhere('country', 'like', '%' . $this->searchTerm . '%');
                });
            })
            ->when($this->statusFilter !== 'all', function($q) {
                if ($this->statusFilter === 'active') {
                    $q->activeSessions();
                } else {
                    $q->completedSessions();
                }
            })
            ->when($this->dateFilter !== 'all', function($q) {
                switch ($this->dateFilter) {
                    case 'today':
                        $q->whereDate('login_at', today());
                        break;
                    case 'week':
                        $q->whereBetween('login_at', [now()->startOfWeek(), now()->endOfWeek()]);
                        break;
                    case 'month':
                        $q->whereMonth('login_at', now()->month)
                            ->whereYear('login_at', now()->year);
                        break;
                }
            });

        $activities = $query->latest('login_at')->paginate(20);

        // Get session stats
        $stats = null;
        if ($this->showStats) {
            $service = app(UserLoginService::class);
            $stats = $service->getUserSessionStats($this->user->id);

            // Add additional stats
            $stats['last_login'] = LoginActivity::where('user_id', $this->user->id)
                ->latest('login_at')
                ->first();

            $stats['unique_devices'] = LoginActivity::where('user_id', $this->user->id)
                ->distinct('device_type')
                ->count('device_type');

            $stats['unique_locations'] = LoginActivity::where('user_id', $this->user->id)
                ->distinct('country')
                ->count('country');
        }

        return view('livewire.users.user-activity-details', [
            'activities' => $activities,
            'stats' => $stats
        ]);
    }
}
