<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Notifications\DatabaseNotification;
use App\Models\AssignmentNotification;
use Illuminate\Support\Collection;

class NotificationsDropdown extends Component
{
    public $align = 'right';
    public $notifications;
    public $unreadCount = 0;

    public function mount($align = 'right')
    {
        $this->align = $align;
        $this->loadNotifications();
    }

    public function loadNotifications()
    {
        $user = auth()->user();
        $notifications = collect();

        // Get generic Laravel notifications
        if ($user) {
            $genericNotifications = $user->notifications()
                ->latest()
                ->take(10)
                ->get()
                ->map(function ($notification) {
                    return [
                        'id' => $notification->id,
                        'type' => 'generic',
                        'title' => $this->getNotificationTitle($notification),
                        'message' => $this->getNotificationMessage($notification),
                        'created_at' => $notification->created_at,
                        'read_at' => $notification->read_at,
                        'data' => $notification->data,
                    ];
                });

            $notifications = $notifications->merge($genericNotifications);
        }

        // Get assignment notifications if user is a student
        if ($user && $user->student) {
            $assignmentNotifications = AssignmentNotification::where('student_id', $user->student->id)
                ->with(['assignment', 'assignment.academicSubject', 'assignment.teacher.user'])
                ->latest('notified_at')
                ->take(10)
                ->get()
                ->map(function ($notification) {
                    return [
                        'id' => $notification->id,
                        'type' => 'assignment',
                        'title' => $this->getAssignmentNotificationTitle($notification),
                        'message' => $notification->message,
                        'created_at' => $notification->notified_at,
                        'read_at' => $notification->read_at,
                        'data' => [
                            'assignment_id' => $notification->assignment_id,
                            'assignment_title' => $notification->assignment->title ?? 'Unknown Assignment',
                            'subject' => $notification->assignment->academicSubject->name ?? 'Unknown Subject',
                            'teacher' => $notification->assignment->teacher->user->name ?? 'Unknown Teacher',
                        ],
                    ];
                });

            $notifications = $notifications->merge($assignmentNotifications);
        }

        // Sort by created_at and take latest 10
        $this->notifications = $notifications->sortByDesc('created_at')->take(10)->values();

        // Count unread notifications
        $this->unreadCount = $this->notifications->where('read_at', null)->count();
    }

    public function markAsRead($notificationId, $type)
    {
        if ($type === 'generic') {
            $notification = DatabaseNotification::find($notificationId);
            if ($notification) {
                $notification->markAsRead();
            }
        } elseif ($type === 'assignment') {
            $notification = AssignmentNotification::find($notificationId);
            if ($notification) {
                $notification->update(['read_at' => now()]);
            }
        }

        $this->loadNotifications();
    }

    public function markAllAsRead()
    {
        $user = auth()->user();

        // Mark all generic notifications as read
        if ($user) {
            $user->unreadNotifications->markAsRead();
        }

        // Mark all assignment notifications as read for student
        if ($user && $user->student) {
            AssignmentNotification::where('student_id', $user->student->id)
                ->whereNull('read_at')
                ->update(['read_at' => now()]);
        }

        $this->loadNotifications();
    }

    private function getNotificationTitle($notification)
    {
        $data = $notification->data;

        // Handle different notification types
        switch ($notification->type) {
            case 'App\Notifications\NewAssignmentNotification':
                return "New {$data['type']}: {$data['title']}";
            default:
                return $data['title'] ?? 'Notification';
        }
    }

    private function getNotificationMessage($notification)
    {
        $data = $notification->data;

        switch ($notification->type) {
            case 'App\Notifications\NewAssignmentNotification':
                return $data['message'] ?? "New assignment has been created.";
            default:
                return $data['message'] ?? 'You have a new notification.';
        }
    }

    private function getAssignmentNotificationTitle($notification)
    {
        if ($notification->assignment) {
            return "New {$notification->assignment->type}: {$notification->assignment->title}";
        }
        return 'Assignment Notification';
    }

    private function getNotificationIcon($notification)
    {
        switch ($notification['type']) {
            case 'assignment':
                return '📚';
            case 'generic':
                $type = $notification['data']['type'] ?? '';
                if (str_contains($type, 'assignment')) {
                    return '📚';
                }
                return '📣';
            default:
                return '📣';
        }
    }

    public function viewNotification($notificationId, $type)
    {
        // Mark as read and redirect to detailed view
        $this->markAsRead($notificationId, $type);
        return redirect()->route('notifications.show', ['type' => $type, 'id' => $notificationId]);
    }

    public function render()
    {
        return view('livewire.notifications-dropdown');
    }
}
