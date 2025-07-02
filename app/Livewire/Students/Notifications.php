<?php

namespace App\Livewire\Students;

use App\Models\AssignmentSubmission;
use Livewire\Component;
use App\Models\AssignmentNotification;
use Illuminate\Notifications\DatabaseNotification;
use App\Notifications\NewAssignmentNotification;

class Notifications extends Component
{
    public $recentNotifications = [];
    public $upcomingAssignments = [];
    public $pendingAssignments = [];
    public $completedAssignmentsCount = 0;


    public function mount()
    {
        $this->loadRecentNotifications();
        $this->loadAssignmentData();
    }

    public function loadRecentNotifications()
    {
        $user = auth()->user();
        $notifications = collect();

        if (!$user || !$user->student) {
            return;
        }

        // Get assignment notifications
        $assignmentNotifications = AssignmentNotification::where('student_id', $user->student->id)
            ->whereNull('read_at')
            ->with(['assignment', 'assignment.academicSubject', 'assignment.teacher.user'])
            ->latest('notified_at')
            ->take(3)
            ->get()
            ->map(function ($notification) {
                return [
                    'id' => $notification->id,
                    'type' => 'assignment',
                    'title' => "New {$notification->assignment->type}: {$notification->assignment->title}",
                    'message' => $notification->message,
                    'subject' => $notification->assignment->academicSubject->name ?? 'Unknown Subject',
                    'teacher' => $notification->assignment->teacher->user->name ?? 'Unknown Teacher',
                    'created_at' => $notification->notified_at,
                    'assignment_id' => $notification->assignment_id,
                    'due_date' => $notification->assignment->ends_at,
                ];
            });

        $notifications = $notifications->merge($assignmentNotifications);

        // Get generic notifications
        $genericNotifications = $user->notifications()
            ->whereNull('read_at')
            ->latest()
            ->take(5 - $assignmentNotifications->count()) // Fill remaining slots
            ->get()
            ->map(function ($notification) {
                return [
                    'id' => $notification->id,
                    'type' => 'generic',
                    'title' => $this->getNotificationTitle($notification),
                    'message' => $this->getNotificationMessage($notification),
                    'created_at' => $notification->created_at,
                ];
            });

        $notifications = $notifications->merge($genericNotifications);

        $this->recentNotifications = $notifications->sortByDesc('created_at')->take(5)->values()->toArray();
    }

    public function loadAssignmentData()
    {
        $user = auth()->user();

        if (!$user || !$user->student) {
            return;
        }

        // Get upcoming assignments (within next 7 days)
        $this->upcomingAssignments = AssignmentNotification::where('student_id', $user->student->id)
            ->with(['assignment', 'assignment.academicSubject'])
            ->whereHas('assignment', function ($query) {
                $query->where('status', 'published')
                    ->where('starts_at', '>', now())
                    ->where('starts_at', '<=', now()->addWeek());
            })
            ->latest('notified_at')
            ->take(3)
            ->get()
            ->map(function ($notification) {
                return [
                    'id' => $notification->assignment->id,
                    'title' => $notification->assignment->title,
                    'type' => $notification->assignment->type,
                    'subject' => $notification->assignment->academicSubject->name ?? 'Unknown Subject',
                    'starts_at' => $notification->assignment->starts_at,
                    'duration' => $notification->assignment->duration_in_minutes,
                ];
            })
            ->toArray();

        // Get pending assignments (started but not submitted)
        $this->pendingAssignments = AssignmentNotification::where('student_id', $user->student->id)
            ->with(['assignment', 'assignment.academicSubject'])
            ->whereHas('assignment', function ($query) use ($user) {
                $query->where('status', 'published')
                    ->where('starts_at', '<=', now())
                    ->where('ends_at', '>', now())
                    ->whereDoesntHave('submissions', function ($subQuery) use ($user) {
                        $subQuery->where('student_id', $user->student->id);
                    });
            })
            ->latest('notified_at')
            ->take(3)
            ->get()
            ->map(function ($notification) {
                return [
                    'id' => $notification->assignment->id,
                    'title' => $notification->assignment->title,
                    'type' => $notification->assignment->type,
                    'subject' => $notification->assignment->academicSubject->name ?? 'Unknown Subject',
                    'ends_at' => $notification->assignment->ends_at,
                    'duration' => $notification->assignment->duration_in_minutes,
                    'time_remaining' => $notification->assignment->ends_at->diffInHours(now()),
                ];
            })
            ->toArray();
    }
    public function loadCompletedAssignmentsCount()
    {
        $user = auth()->user();

        if (!$user || !$user->student) {
            $this->completedAssignmentsCount = 0;
            return;
        }

        // Count completed assignments for this student
        $this->completedAssignmentsCount = AssignmentSubmission::where('student_id', $user->student->id)
            ->whereNotNull('submitted_at')
            ->count();
    }

    public function getCompletedAssignmentsCount()
    {
        return $this->completedAssignmentsCount;
    }

    public function markNotificationAsRead($notificationId, $type)
    {
        $user = auth()->user();

        if ($type === 'assignment') {
            $notification = AssignmentNotification::where('id', $notificationId)
                ->where('student_id', $user->student->id)
                ->first();

            if ($notification) {
                $notification->update(['read_at' => now()]);
            }
        } else {
            $notification = DatabaseNotification::where('id', $notificationId)
                ->where('notifiable_id', $user->id)
                ->first();

            if ($notification) {
                $notification->markAsRead();
            }
        }

        $this->loadRecentNotifications();
    }

    public function startAssignment($assignmentId)
    {
        return redirect()->route('assignments.take', $assignmentId);
    }

    private function getNotificationTitle($notification)
    {
        $data = $notification->data;

        return match ($notification->type) {
            NewAssignmentNotification::class => "New {$data['type']}: {$data['title']}",
            default => $data['title'] ?? 'Notification',
        };
    }

    private function getNotificationMessage($notification)
    {
        $data = $notification->data;

        switch ($notification->type) {
            case NewAssignmentNotification::class:
                return $data['message'] ?? "New assignment has been created.";
            default:
                return $data['message'] ?? 'You have a new notification.';
        }
    }

    public function render()
    {
        return view('livewire.students.notifications');
    }
}
