<?php

namespace App\Livewire\Parent;

use App\Livewire\AppComponent;
use Illuminate\Support\Facades\Auth;
use Illuminate\Notifications\DatabaseNotification;
use Livewire\Attributes\Computed;
use Livewire\WithPagination;

class ParentNotificationsHandler extends AppComponent
{
    use WithPagination;

    public $filter = 'all';
    public $selectedNotifications = [];
    public $showMarkAllModal = false;

    public function filterNotifications($filter)
    {
        $this->filter = $filter;
        $this->resetPage();
    }

    public function markAsRead($notificationId)
    {
        $notification = DatabaseNotification::find($notificationId);
        if ($notification && $notification->notifiable_id === Auth::id()) {
            $notification->markAsRead();
            $this->dispatch('notification-read', notificationId: $notificationId);
        }
    }

    public function markAllAsRead()
    {
        Auth::user()->unreadNotifications->markAsRead();
        $this->showMarkAllModal = false;
        $this->dispatch('all-notifications-read');
        session()->flash('success', 'All notifications marked as read!');
    }

    public function markSelectedAsRead()
    {
        if (empty($this->selectedNotifications)) return;

        DatabaseNotification::whereIn('id', $this->selectedNotifications)
            ->where('notifiable_id', Auth::id())
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        $this->selectedNotifications = [];
        $this->dispatch('selected-notifications-read');
        session()->flash('success', 'Selected notifications marked as read!');
    }

    public function toggleNotificationSelection($notificationId)
    {
        if (in_array($notificationId, $this->selectedNotifications)) {
            $this->selectedNotifications = array_filter($this->selectedNotifications, function($id) use ($notificationId) {
                return $id !== $notificationId;
            });
        } else {
            $this->selectedNotifications[] = $notificationId;
        }
    }

    public function deleteNotification($notificationId)
    {
        $notification = DatabaseNotification::find($notificationId);
        if ($notification && $notification->notifiable_id === Auth::id()) {
            $notification->delete();
            $this->dispatch('notification-deleted', notificationId: $notificationId);
            session()->flash('success', 'Notification deleted!');
        }
    }

    #[Computed]
    public function notifications()
    {
        $query = Auth::user()->notifications();

        switch ($this->filter) {
            case 'unread':
                $query->whereNull('read_at');
                break;
            case 'read':
                $query->whereNotNull('read_at');
                break;
            case 'academic':
                $query->where('type', 'LIKE', '%Academic%');
                break;
            case 'system':
                $query->where('type', 'LIKE', '%System%');
                break;
            case 'alerts':
                $query->where('type', 'LIKE', '%Alert%');
                break;
        }

        return $query->orderBy('created_at', 'desc')->paginate(10);
    }

    #[Computed]
    public function notificationCounts()
    {
        $user = Auth::user();
        $allNotifications = $user->notifications;

        return [
            'all' => $allNotifications->count(),
            'unread' => $allNotifications->whereNull('read_at')->count(),
            'read' => $allNotifications->whereNotNull('read_at')->count(),
            'academic' => $allNotifications->where('type', 'LIKE', '%Academic%')->count(),
            'system' => $allNotifications->where('type', 'LIKE', '%System%')->count(),
            'alerts' => $allNotifications->where('type', 'LIKE', '%Alert%')->count(),
        ];
    }

    #[Computed]
    public function hasUnreadNotifications()
    {
        return Auth::user()->unreadNotifications->count() > 0;
    }

    public function render()
    {
        return view('livewire.parent.ParentNotificationPage');
    }
}
