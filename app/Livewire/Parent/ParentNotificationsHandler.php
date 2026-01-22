<?php

namespace App\Livewire\Parent;

use App\Livewire\AppComponent;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\WithPagination;

class ParentNotificationsHandler extends AppComponent
{
    use WithPagination;

    public $filter = 'all';

    public $selectedNotifications = [];

    public $showMarkAllModal = false;

    public $search = '';

    public function getListeners()
    {
        $userId = Auth::id();

        return [
            'notification-created' => 'handleNotificationCreated',
            "echo-notification:App.Models.User.{$userId},DatabaseNotificationCreated" => 'notificationReceived',
        ];
    }

    public function mount()
    {
        // Initialize if needed
    }

    public function filterNotifications($filter)
    {
        $this->filter = $filter;
        $this->selectedNotifications = [];
        $this->resetPage();
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function markAsRead($notificationId)
    {
        $notification = DatabaseNotification::find($notificationId);

        if ($notification && $notification->notifiable_id === Auth::id()) {
            $notification->markAsRead();
            $this->dispatch('notification-read', notificationId: $notificationId);
            $this->dispatch('notification-count-updated');
        }
    }

    public function markAllAsRead()
    {
        $count = Auth::user()->unreadNotifications()->count();

        if ($count > 0) {
            Auth::user()->unreadNotifications->markAsRead();
            $this->showMarkAllModal = false;
            $this->dispatch('all-notifications-read');
            $this->dispatch('notification-count-updated');
            session()->flash('success', "{$count} notification(s) marked as read!");
        }
    }

    public function markSelectedAsRead()
    {
        if (empty($this->selectedNotifications)) {
            session()->flash('warning', 'No notifications selected');

            return;
        }

        $count = DatabaseNotification::whereIn('id', $this->selectedNotifications)
            ->where('notifiable_id', Auth::id())
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        $this->selectedNotifications = [];
        $this->dispatch('selected-notifications-read');
        $this->dispatch('notification-count-updated');
        session()->flash('success', "{$count} notification(s) marked as read!");
    }

    public function toggleNotificationSelection($notificationId)
    {
        if (in_array($notificationId, $this->selectedNotifications)) {
            $this->selectedNotifications = array_values(
                array_filter($this->selectedNotifications, fn ($id) => $id !== $notificationId)
            );
        } else {
            $this->selectedNotifications[] = $notificationId;
        }
    }

    public function selectAllOnPage()
    {
        $pageNotificationIds = $this->notifications->pluck('id')->toArray();
        $this->selectedNotifications = array_unique(
            array_merge($this->selectedNotifications, $pageNotificationIds)
        );
    }

    public function deselectAll()
    {
        $this->selectedNotifications = [];
    }

    public function deleteNotification($notificationId)
    {
        $notification = DatabaseNotification::find($notificationId);

        if ($notification && $notification->notifiable_id === Auth::id()) {
            $notification->delete();

            // Remove from selected if exists
            $this->selectedNotifications = array_values(
                array_filter($this->selectedNotifications, fn ($id) => $id !== $notificationId)
            );

            $this->dispatch('notification-deleted', notificationId: $notificationId);
            $this->dispatch('notification-count-updated');
            session()->flash('success', 'Notification deleted!');
        }
    }

    public function deleteSelected()
    {
        if (empty($this->selectedNotifications)) {
            session()->flash('warning', 'No notifications selected');

            return;
        }

        $count = DatabaseNotification::whereIn('id', $this->selectedNotifications)
            ->where('notifiable_id', Auth::id())
            ->delete();

        $this->selectedNotifications = [];
        $this->dispatch('notifications-deleted');
        $this->dispatch('notification-count-updated');
        session()->flash('success', "{$count} notification(s) deleted!");
    }

    #[On('notification-received')]
    public function notificationReceived()
    {
        $this->dispatch('notification-count-updated');
    }

    #[Computed]
    public function notifications()
    {
        $query = Auth::user()->notifications();

        // Apply search filter
        if ($this->search) {
            $query->where(function ($q) {
                $q->where('data->title', 'like', "%{$this->search}%")
                    ->orWhere('data->message', 'like', "%{$this->search}%");
            });
        }

        // Apply type filter
        switch ($this->filter) {
            case 'unread':
                $query->whereNull('read_at');
                break;
            case 'read':
                $query->whereNotNull('read_at');
                break;
            case 'assignments':
                $query->where('type', 'LIKE', '%Assignment%');
                break;
            case 'attendance':
                $query->where('type', 'LIKE', '%Attendance%');
                break;
            case 'fees':
                $query->where('type', 'LIKE', '%Fee%')
                    ->orWhere('type', 'LIKE', '%Payment%');
                break;
            case 'grades':
                $query->where('type', 'LIKE', '%Grade%')
                    ->orWhere('type', 'LIKE', '%Assessment%');
                break;
            case 'system':
                $query->where('type', 'LIKE', '%System%');
                break;
        }

        return $query->latest()->paginate(15);
    }

    #[Computed]
    public function notificationCounts()
    {
        $userId = Auth::id();

        return [
            'all' => DatabaseNotification::where('notifiable_id', $userId)->count(),
            'unread' => DatabaseNotification::where('notifiable_id', $userId)
                ->whereNull('read_at')->count(),
            'read' => DatabaseNotification::where('notifiable_id', $userId)
                ->whereNotNull('read_at')->count(),
            'assignments' => DatabaseNotification::where('notifiable_id', $userId)
                ->where('type', 'LIKE', '%Assignment%')->count(),
            'attendance' => DatabaseNotification::where('notifiable_id', $userId)
                ->where('type', 'LIKE', '%Attendance%')->count(),
            'fees' => DatabaseNotification::where('notifiable_id', $userId)
                ->where(function ($q) {
                    $q->where('type', 'LIKE', '%Fee%')
                        ->orWhere('type', 'LIKE', '%Payment%');
                })->count(),
            'grades' => DatabaseNotification::where('notifiable_id', $userId)
                ->where(function ($q) {
                    $q->where('type', 'LIKE', '%Grade%')
                        ->orWhere('type', 'LIKE', '%Assessment%');
                })->count(),
            'system' => DatabaseNotification::where('notifiable_id', $userId)
                ->where('type', 'LIKE', '%System%')->count(),
        ];
    }

    #[Computed]
    public function hasUnreadNotifications()
    {
        return Auth::user()->unreadNotifications()->exists();
    }

    public function getNotificationIcon($notification)
    {
        $type = $notification->type;

        if (str_contains($type, 'Assignment')) {
            return 'assignment';
        }
        if (str_contains($type, 'Attendance')) {
            return 'attendance';
        }
        if (str_contains($type, 'Fee') || str_contains($type, 'Payment')) {
            return 'payment';
        }
        if (str_contains($type, 'Grade') || str_contains($type, 'Assessment')) {
            return 'grade';
        }
        if (str_contains($type, 'System')) {
            return 'system';
        }
        if (str_contains($type, 'Alert')) {
            return 'alert';
        }

        return 'default';
    }

    public function getNotificationColor($notification)
    {
        $type = $notification->type;

        if (str_contains($type, 'Assignment')) {
            return 'blue';
        }
        if (str_contains($type, 'Attendance')) {
            return 'green';
        }
        if (str_contains($type, 'Fee') || str_contains($type, 'Payment')) {
            return 'purple';
        }
        if (str_contains($type, 'Grade') || str_contains($type, 'Assessment')) {
            return 'indigo';
        }
        if (str_contains($type, 'Alert')) {
            return 'red';
        }
        if (str_contains($type, 'System')) {
            return 'gray';
        }

        return 'gray';
    }

    public function render()
    {
        return view('livewire.parent.ParentNotificationPage');
    }
}
