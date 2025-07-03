<?php

namespace App\Livewire\Teachers;

use App\Models\Assignment;
use App\Models\AssignmentNotification;
use App\Models\AssignmentSubmission;
use App\Models\Teacher;
use App\Notifications\NewAssignmentNotification;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class TeacherNotifications extends Component
{
    use WithPagination;

    public $teacher;
    public $notifications = [];
    public $filter = 'all'; // all, generic, assignments, submissions
    public $selectedNotification = null;
    public $showModal = false;
    public $perPage = 15;

    public function mount()
    {
        $this->teacher = Teacher::where('user_id', Auth::id())->first();
        $this->loadNotifications();
    }

    public function loadNotifications()
    {
        $user = auth()->user();

        if (!$user || !$this->teacher) {
            $this->notifications = [];
            return;
        }

        // Get generic notifications - convert to array immediately
        $genericNotifications = [];
        $genericQuery = $user->notifications()
            ->when($this->filter === 'generic', function ($query) {
                return $query->whereNull('read_at');
            })
            ->when($this->filter === 'all', function ($query) {
                return $query;
            })
            ->latest()
            ->get();

        foreach ($genericQuery as $notification) {
            $genericNotifications[] = [
                'id' => $notification->id,
                'type' => 'generic',
                'category' => $this->getNotificationCategory($notification),
                'title' => $this->getNotificationTitle($notification),
                'message' => $this->getNotificationMessage($notification),
                'created_at' => $notification->created_at->toISOString(),
                'read_at' => $notification->read_at ? $notification->read_at->toISOString() : null,
                'data' => $notification->data,
                'icon' => $this->getNotificationIcon($notification),
                'priority' => $this->getNotificationPriority($notification),
            ];
        }

        // Get assignment-related notifications - convert to array immediately
        $assignmentNotifications = [];
        if ($this->filter === 'all' || $this->filter === 'assignments') {
            $teacherAssignments = Assignment::where('teacher_id', $this->teacher->id)
                ->with(['notifications.student.user', 'academicSubject'])
                ->get();

            foreach ($teacherAssignments as $assignment) {
                foreach ($assignment->notifications as $notification) {
                    $assignmentNotifications[] = [
                        'id' => 'assignment_' . $notification->id,
                        'type' => 'assignment',
                        'category' => 'Assignment Notification',
                        'title' => "Assignment Assigned: {$assignment->title}",
                        'message' => "Assignment '{$assignment->title}' was assigned to {$notification->student->user->name}",
                        'created_at' => $notification->notified_at->toISOString(),
                        'read_at' => $notification->read_at ? $notification->read_at->toISOString() : null,
                        'data' => [
                            'assignment_id' => $assignment->id,
                            'assignment_title' => $assignment->title,
                            'assignment_type' => $assignment->type,
                            'assignment_status' => $assignment->status,
                            'student_id' => $notification->student->id,
                            'student_name' => $notification->student->user->name,
                            'subject' => $assignment->academicSubject->name ?? 'Unknown Subject',
                            'due_date' => $assignment->ends_at ? $assignment->ends_at->toISOString() : null,
                        ],
                        'icon' => 'assignment',
                        'priority' => 'medium',
                    ];
                }
            }
        }

        // Get submission notifications - convert to array immediately
        $submissionNotifications = [];
        if ($this->filter === 'all' || $this->filter === 'submissions') {
            $recentSubmissions = AssignmentSubmission::whereHas('assignment', function ($query) {
                $query->where('teacher_id', $this->teacher->id);
            })
                ->with(['assignment', 'student.user'])
                ->where('submitted_at', '>', now()->subDays(7))
                ->orderBy('submitted_at', 'desc')
                ->get();

            foreach ($recentSubmissions as $submission) {
                $submissionNotifications[] = [
                    'id' => 'submission_' . $submission->id,
                    'type' => 'submission',
                    'category' => 'Assignment Submission',
                    'title' => "New Submission: {$submission->assignment->title}",
                    'message' => "{$submission->student->user->name} submitted {$submission->assignment->title}",
                    'created_at' => $submission->submitted_at->toISOString(),
                    'read_at' => null,
                    'data' => [
                        'submission_id' => $submission->id,
                        'assignment_id' => $submission->assignment->id,
                        'assignment_title' => $submission->assignment->title,
                        'student_id' => $submission->student->id,
                        'student_name' => $submission->student->user->name,
                        'score' => $submission->score,
                        'total_marks' => $submission->total_marks,
                        'status' => $submission->status,
                        'time_spent' => $submission->time_spent_minutes,
                    ],
                    'icon' => 'submission',
                    'priority' => $submission->status === 'submitted' ? 'high' : 'medium',
                ];
            }
        }

        // Merge all notifications using array_merge instead of collections
        $allNotifications = array_merge($genericNotifications, $assignmentNotifications, $submissionNotifications);

        // Sort by created_at using usort
        usort($allNotifications, function ($a, $b) {
            return strcmp($b['created_at'], $a['created_at']);
        });

        $this->notifications = $allNotifications;
    }

    public function showNotificationDetails($notificationId)
    {
        $notification = null;
        foreach ($this->notifications as $n) {
            if ($n['id'] === $notificationId) {
                $notification = $n;
                break;
            }
        }

        if ($notification) {
            $this->selectedNotification = $notification;
            $this->showModal = true;
        }
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->selectedNotification = null;
    }

    public function markAsRead($notificationId)
    {
        $notification = null;
        foreach ($this->notifications as $n) {
            if ($n['id'] === $notificationId) {
                $notification = $n;
                break;
            }
        }

        if (!$notification) {
            return;
        }

        if ($notification['type'] === 'generic') {
            $dbNotification = DatabaseNotification::where('id', $notificationId)
                ->where('notifiable_id', auth()->id())
                ->first();

            if ($dbNotification) {
                $dbNotification->markAsRead();
            }
        } elseif ($notification['type'] === 'assignment') {
            $assignmentNotificationId = str_replace('assignment_', '', $notificationId);
            $assignmentNotification = AssignmentNotification::find($assignmentNotificationId);

            if ($assignmentNotification) {
                $assignmentNotification->update(['read_at' => now()]);
            }
        }

        $this->loadNotifications();
    }

    public function markAllAsRead()
    {
        $user = auth()->user();

        // Mark all generic notifications as read
        $user->unreadNotifications()->update(['read_at' => now()]);

        // Mark all assignment notifications as read
        AssignmentNotification::whereHas('assignment', function ($query) {
            $query->where('teacher_id', $this->teacher->id);
        })->whereNull('read_at')->update(['read_at' => now()]);

        $this->loadNotifications();
        $this->dispatch('success', 'All notifications marked as read.');
    }

    public function updatingFilter()
    {
        $this->loadNotifications();
    }

    public function getUnreadCount()
    {
        $count = 0;
        foreach ($this->notifications as $notification) {
            if ($notification['read_at'] === null) {
                $count++;
            }
        }
        return $count;
    }

    public function getNotificationsByType($type)
    {
        $count = 0;
        foreach ($this->notifications as $notification) {
            if ($notification['type'] === $type) {
                $count++;
            }
        }
        return $count;
    }

    private function getNotificationCategory($notification)
    {
        return match ($notification->type) {
            NewAssignmentNotification::class => 'Assignment',
            default => 'General',
        };
    }

    private function getNotificationTitle($notification)
    {
        $data = $notification->data;

        return match ($notification->type) {
            NewAssignmentNotification::class => "Assignment: {$data['title']}",
            default => $data['title'] ?? 'Notification',
        };
    }

    private function getNotificationMessage($notification)
    {
        $data = $notification->data;

        return match ($notification->type) {
            NewAssignmentNotification::class => $data['message'] ?? "Assignment has been created.",
            default => $data['message'] ?? 'You have a new notification.',
        };
    }

    private function getNotificationIcon($notification)
    {
        return match ($notification->type) {
            NewAssignmentNotification::class => 'assignment',
            default => 'notification',
        };
    }

    private function getNotificationPriority($notification)
    {
        return match ($notification->type) {
            NewAssignmentNotification::class => 'high',
            default => 'medium',
        };
    }

    public function render()
    {
        return view('livewire.teachers.teacher-notifications');
    }
}
