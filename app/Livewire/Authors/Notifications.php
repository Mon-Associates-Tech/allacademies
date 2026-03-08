<?php

namespace App\Livewire\Authors;

use App\Livewire\AppComponent;
use Illuminate\Support\Facades\Auth;

class Notifications extends AppComponent
{
    public $notifications;

    public $unreadCount = 0;

    public $showAll = false;

    public $filterType = 'unread';

    public function mount()
    {
        $this->loadNotifications();
    }

    public function loadNotifications()
    {
        $user = Auth::user();

        if ($this->showAll) {
            $this->notifications = $user->notifications()->latest()->take(50)->get();
        } else {
            $this->notifications = $user->unreadNotifications()->latest()->take(20)->get();
        }

        $this->unreadCount = $user->unreadNotifications()->count();
    }

    public function markAsRead($notificationId)
    {
        $notification = Auth::user()->notifications()->find($notificationId);

        if ($notification) {
            $notification->markAsRead();
            $this->loadNotifications();

            $this->dispatch('notification-updated');
        }
    }

    public function markAllAsRead()
    {
        Auth::user()->unreadNotifications->markAsRead();
        $this->loadNotifications();

        $this->dispatch('notification-updated');
    }

    public function toggleShowAll()
    {
        $this->showAll = ! $this->showAll;
        $this->loadNotifications();
    }

    public function deleteNotification($notificationId)
    {
        $notification = Auth::user()->notifications()->find($notificationId);

        if ($notification) {
            $notificationType = $notification->type;
            $notificationId = $notification->id;

            $notification->delete();

            // Log activity
            Auth::user()->logActivity('delete', 'Notification Deleted', 'notification', [
                'notification_id' => $notificationId,
                'notification_type' => $notificationType,
                'deleted_by' => auth()->user()?->name ?? 'Unknown',
            ]);

            $this->loadNotifications();

            $this->dispatch('notification-deleted');
        }
    }

    public function render()
    {
        return view('livewire.authors.notifications');
    }
}
