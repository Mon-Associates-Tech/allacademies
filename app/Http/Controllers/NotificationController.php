<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Notifications\DatabaseNotification;
use App\Models\AssignmentNotification;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $notifications = collect();

        // Get generic Laravel notifications
        if ($user) {
            $genericNotifications = $user->notifications()
                ->latest()
                ->paginate(20)
                ->through(function ($notification) {
                    return [
                        'id' => $notification->id,
                        'type' => 'generic',
                        'notification_type' => $notification->type,
                        'title' => $this->getNotificationTitle($notification),
                        'message' => $this->getNotificationMessage($notification),
                        'created_at' => $notification->created_at,
                        'read_at' => $notification->read_at,
                        'data' => $notification->data,
                        'model' => $notification,
                    ];
                });

            $notifications = $genericNotifications;
        }

        // Get assignment notifications if user is a student
        $assignmentNotifications = collect();
        if ($user && $user->student) {
            $assignmentNotifications = AssignmentNotification::where('student_id', $user->student->id)
                ->with(['assignment', 'assignment.academicSubject', 'assignment.teacher.user'])
                ->latest('notified_at')
                ->paginate(20)
                ->through(function ($notification) {
                    return [
                        'id' => $notification->id,
                        'type' => 'assignment',
                        'notification_type' => 'assignment',
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
                        'model' => $notification,
                    ];
                });
        }

        return view('notifications.index', compact('notifications', 'assignmentNotifications'));
    }

    public function show($type, $id)
    {
        $user = Auth::user();
        $notification = null;
        $notificationData = null;

        if ($type === 'generic') {
            $notification = DatabaseNotification::where('id', $id)
                ->where('notifiable_type', 'user')
                ->where('notifiable_id', $user->id)
                ->first();

            if ($notification) {
                // Mark as read if not already
                if (!$notification->read_at) {
                    $notification->markAsRead();
                }

                $notificationData = [
                    'id' => $notification->id,
                    'type' => 'generic',
                    'notification_type' => $notification->type,
                    'title' => $this->getNotificationTitle($notification),
                    'message' => $this->getNotificationMessage($notification),
                    'created_at' => $notification->created_at,
                    'read_at' => $notification->read_at,
                    'data' => $notification->data,
                ];
            }
        } elseif ($type === 'assignment') {
            if ($user->student) {
                $notification = AssignmentNotification::where('id', $id)
                    ->where('student_id', $user->student->id)
                    ->with(['assignment', 'assignment.academicSubject', 'assignment.teacher.user'])
                    ->first();

                if ($notification) {
                    // Mark as read if not already
                    if (!$notification->read_at) {
                        $notification->update(['read_at' => now()]);
                    }

                    $notificationData = [
                        'id' => $notification->id,
                        'type' => 'assignment',
                        'notification_type' => 'assignment',
                        'title' => $this->getAssignmentNotificationTitle($notification),
                        'message' => $notification->message,
                        'created_at' => $notification->notified_at,
                        'read_at' => $notification->read_at,
                        'data' => [
                            'assignment_id' => $notification->assignment_id,
                            'assignment_title' => $notification->assignment->title ?? 'Unknown Assignment',
                            'subject' => $notification->assignment->academicSubject->name ?? 'Unknown Subject',
                            'teacher' => $notification->assignment->teacher->user->name ?? 'Unknown Teacher',
                            'type' => $notification->assignment->type ?? 'Unknown',
                            'starts_at' => $notification->assignment->starts_at ?? null,
                            'ends_at' => $notification->assignment->ends_at ?? null,
                            'duration_in_minutes' => $notification->assignment->duration_in_minutes ?? null,
                        ],
                        'assignment' => $notification->assignment,
                    ];
                }
            }
        }

        if (!$notification) {
            abort(404);
        }

        return view('notifications.show', compact('notificationData'));
    }

    public function markAsRead($type, $id)
    {
        $user = Auth::user();

        if ($type === 'generic') {
            $notification = DatabaseNotification::where('id', $id)
                ->where('notifiable_type', get_class($user))
                ->where('notifiable_id', $user->id)
                ->first();

            if ($notification) {
                $notification->markAsRead();
            }
        } elseif ($type === 'assignment') {
            if ($user->student) {
                $notification = AssignmentNotification::where('id', $id)
                    ->where('student_id', $user->student->id)
                    ->first();

                if ($notification) {
                    $notification->update(['read_at' => now()]);
                }
            }
        }

        return response()->json(['success' => true]);
    }

    public function markAllAsRead()
    {
        $user = Auth::user();

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

        return response()->json(['success' => true]);
    }

    private function getNotificationTitle($notification)
    {
        $data = $notification->data;

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
}
