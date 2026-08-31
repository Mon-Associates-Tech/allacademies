<?php

namespace App\Livewire;

use App\Models\AssignmentNotification;
use App\Models\QuizSession;
use Illuminate\Notifications\DatabaseNotification;
use Livewire\Component;
use Livewire\WithPagination;

class UnifiedNotifications extends Component
{
    use WithPagination;

    public $filter = 'all';

    public $type = 'all';

    public $search = '';

    public $selectedNotifications = [];

    public $showBulkActions = false;

    protected $queryString = ['filter', 'type', 'search'];

    public function mount()
    {
        $this->filter = request('filter', 'all');
        $this->type = request('type', 'all');
        $this->search = request('search', '');
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function setFilter($filter)
    {
        $this->filter = $filter;
        $this->resetPage();
    }

    public function setType($type)
    {
        $this->type = $type;
        $this->resetPage();
    }

    public function toggleNotification($notificationId)
    {
        if (in_array($notificationId, $this->selectedNotifications)) {
            $this->selectedNotifications = array_values(array_diff($this->selectedNotifications, [$notificationId]));
        } else {
            $this->selectedNotifications[] = $notificationId;
        }
        $this->showBulkActions = count($this->selectedNotifications) > 0;
    }

    public function selectAll()
    {
        $this->selectedNotifications = $this->getNotifications()->pluck('id')->toArray();
        $this->showBulkActions = true;
    }

    public function deselectAll()
    {
        $this->selectedNotifications = [];
        $this->showBulkActions = false;
    }

    public function markAsRead($type, $id)
    {
        $user = auth()->user();

        if ($type === 'generic') {
            $notification = DatabaseNotification::where('id', $id)
                ->where('notifiable_id', $user->id)
                ->first();

            if ($notification) {
                $notification->markAsRead();
            }
        } elseif ($type === 'assignment' && $user->student) {
            $notification = AssignmentNotification::where('id', $id)
                ->where('student_id', $user->student->id)
                ->first();

            if ($notification) {
                $notification->update(['read_at' => now()]);
            }
        } elseif ($type === 'submission' && $user->teacher) {
            // Handle submission notifications for teachers
            $notification = AssignmentNotification::where('id', $id)
                ->whereHas('assignment', function ($query) use ($user) {
                    $query->where('teacher_id', $user->teacher->id);
                })
                ->first();

            if ($notification) {
                $notification->update(['read_at' => now()]);
            }
        }

        $this->dispatch('notification-updated');
    }

    public function markAllAsRead()
    {
        $user = auth()->user();

        $user->unreadNotifications->markAsRead();

        if ($user->student) {
            AssignmentNotification::where('student_id', $user->student->id)
                ->whereNull('read_at')
                ->update(['read_at' => now()]);
        }

        if ($user->teacher) {
            AssignmentNotification::whereHas('assignment', function ($query) use ($user) {
                $query->where('teacher_id', $user->teacher->id);
            })
                ->whereNull('read_at')
                ->update(['read_at' => now()]);
        }

        $this->selectedNotifications = [];
        $this->showBulkActions = false;
        $this->dispatch('notification-updated');
        session()->flash('success', 'All notifications marked as read');
    }

    public function markSelectedAsRead()
    {
        if (empty($this->selectedNotifications)) {
            return;
        }

        $user = auth()->user();

        foreach ($this->selectedNotifications as $notificationId) {
            $parts = explode('_', $notificationId);
            $type = $parts[0];
            $id = $parts[1] ?? $notificationId;

            if ($type === 'generic') {
                DatabaseNotification::where('id', $id)
                    ->where('notifiable_id', $user->id)
                    ->whereNull('read_at')
                    ->update(['read_at' => now()]);
            } elseif ($type === 'assignment' && $user->student) {
                AssignmentNotification::where('id', $id)
                    ->where('student_id', $user->student->id)
                    ->whereNull('read_at')
                    ->update(['read_at' => now()]);
            } elseif ($type === 'submission' && $user->teacher) {
                AssignmentNotification::where('id', $id)
                    ->whereHas('assignment', function ($query) use ($user) {
                        $query->where('teacher_id', $user->teacher->id);
                    })
                    ->whereNull('read_at')
                    ->update(['read_at' => now()]);
            }
        }

        $this->selectedNotifications = [];
        $this->showBulkActions = false;
        $this->dispatch('notification-updated');
        session()->flash('success', 'Selected notifications marked as read');
    }

    public function deleteNotification($type, $id)
    {
        $user = auth()->user();

        if ($type === 'generic') {
            DatabaseNotification::where('id', $id)
                ->where('notifiable_id', $user->id)
                ->delete();
        } elseif ($type === 'assignment' && $user->student) {
            AssignmentNotification::where('id', $id)
                ->where('student_id', $user->student->id)
                ->delete();
        } elseif ($type === 'submission' && $user->teacher) {
            AssignmentNotification::where('id', $id)
                ->whereHas('assignment', function ($query) use ($user) {
                    $query->where('teacher_id', $user->teacher->id);
                })
                ->delete();
        }

        $this->dispatch('notification-updated');
        session()->flash('success', 'Notification deleted');
    }

    public function deleteSelected()
    {
        if (empty($this->selectedNotifications)) {
            return;
        }

        $user = auth()->user();

        foreach ($this->selectedNotifications as $notificationId) {
            $parts = explode('_', $notificationId);
            $type = $parts[0];
            $id = $parts[1] ?? $notificationId;

            if ($type === 'generic') {
                DatabaseNotification::where('id', $id)
                    ->where('notifiable_id', $user->id)
                    ->delete();
            } elseif ($type === 'assignment' && $user->student) {
                AssignmentNotification::where('id', $id)
                    ->where('student_id', $user->student->id)
                    ->delete();
            } elseif ($type === 'submission' && $user->teacher) {
                AssignmentNotification::where('id', $id)
                    ->whereHas('assignment', function ($query) use ($user) {
                        $query->where('teacher_id', $user->teacher->id);
                    })
                    ->delete();
            }
        }

        $this->selectedNotifications = [];
        $this->showBulkActions = false;
        $this->dispatch('notification-updated');
        session()->flash('success', 'Selected notifications deleted');
    }

    private function getNotifications()
    {
        $user = auth()->user();
        $allNotifications = collect();

        // Generic notifications
        $genericQuery = $user->notifications();

        if ($this->filter === 'unread') {
            $genericQuery = $user->unreadNotifications();
        } elseif ($this->filter === 'read') {
            $genericQuery = $user->readNotifications();
        }

        $genericNotifications = $genericQuery->latest()->get()->map(function ($notification) {
            $category = $this->getNotificationCategory($notification->type);

            return [
                'id' => 'generic_'.$notification->id,
                'original_id' => $notification->id,
                'type' => 'generic',
                'category' => $category,
                'notification_type' => $notification->type,
                'title' => $this->getNotificationTitle($notification),
                'message' => $this->getNotificationMessage($notification),
                'created_at' => $notification->created_at,
                'read_at' => $notification->read_at,
                'data' => $notification->data,
                'action_url' => $this->getNotificationActionUrl($notification),
                'icon' => $this->getNotificationIcon($category),
                'color' => $this->getNotificationColor($category),
            ];
        });

        if ($this->type === 'all' || $this->type === 'other') {
            $allNotifications = $allNotifications->merge($genericNotifications);
        }

        // Assignment notifications for students
        if ($user->student && ($this->type === 'all' || $this->type === 'assignment')) {
            $assignmentQuery = AssignmentNotification::where('student_id', $user->student->id)
                ->with(['assignment', 'assignment.academicSubject', 'assignment.teacher.user']);

            if ($this->filter === 'unread') {
                $assignmentQuery->whereNull('read_at');
            } elseif ($this->filter === 'read') {
                $assignmentQuery->whereNotNull('read_at');
            }

            $assignmentNotifications = $assignmentQuery->latest('notified_at')->get()->map(function ($notification) {
                return [
                    'id' => 'assignment_'.$notification->id,
                    'original_id' => $notification->id,
                    'type' => 'assignment',
                    'category' => 'assignment',
                    'notification_type' => 'assignment',
                    'title' => $this->getAssignmentNotificationTitle($notification),
                    'message' => $notification->message ?? 'You have a new assignment.',
                    'created_at' => $notification->notified_at,
                    'read_at' => $notification->read_at,
                    'data' => [
                        'assignment_id' => $notification->assignment_id,
                        'assignment_title' => $notification->assignment->title ?? 'Unknown Assignment',
                        'subject' => $notification->assignment->academicSubject->name ?? 'Unknown Subject',
                        'teacher' => $notification->assignment->teacher->user->name ?? 'Unknown Teacher',
                    ],
                    'icon' => 'assignment',
                    'color' => 'blue',
                ];
            });

            $allNotifications = $allNotifications->merge($assignmentNotifications);
        }

        // Submission notifications for teachers
        if ($user->teacher && ($this->type === 'all' || $this->type === 'submission')) {
            $submissionQuery = AssignmentNotification::whereHas('assignment', function ($query) use ($user) {
                    $query->where('teacher_id', $user->teacher->id);
                })
                ->with(['assignment', 'assignment.academicSubject', 'student.user'])
                ->whereNotNull('notified_at');

            if ($this->filter === 'unread') {
                $submissionQuery->whereNull('read_at');
            } elseif ($this->filter === 'read') {
                $submissionQuery->whereNotNull('read_at');
            }

            $submissionNotifications = $submissionQuery->latest('notified_at')->get()->map(function ($notification) {
                return [
                    'id' => 'submission_'.$notification->id,
                    'original_id' => $notification->id,
                    'type' => 'submission',
                    'category' => 'submission',
                    'notification_type' => 'submission',
                    'title' => 'Submission: ' . ($notification->assignment->title ?? 'Assignment'),
                    'message' => ($notification->student->user->name ?? 'Student') . ' submitted ' . ($notification->assignment->title ?? 'an assignment') . '.',
                    'created_at' => $notification->notified_at,
                    'read_at' => $notification->read_at,
                    'data' => [
                        'assignment_id' => $notification->assignment_id,
                        'assignment_title' => $notification->assignment->title ?? 'Unknown Assignment',
                        'subject' => $notification->assignment->academicSubject->name ?? 'Unknown Subject',
                        'student_name' => $notification->student->user->name ?? 'Unknown Student',
                        'notified_at' => $notification->notified_at,
                    ],
                    'icon' => 'submission',
                    'color' => 'purple',
                ];
            });

            $allNotifications = $allNotifications->merge($submissionNotifications);
        }

        // Assessment notifications
        if ($this->type === 'all' || $this->type === 'assessment') {
            $quizQuery = QuizSession::where('user_id', $user->id)
                ->where('status', 'completed')
                ->with(['book', 'subject']);

            if ($this->filter !== 'unread') {
                $assessmentNotifications = $quizQuery->latest('completed_at')->get()->map(function ($session) {
                    $score = $session->results['percentage'] ?? 0;

                    return [
                        'id' => 'assessment_'.$session->id,
                        'original_id' => $session->id,
                        'type' => 'assessment',
                        'category' => 'assessment',
                        'notification_type' => 'assessment',
                        'title' => $session->book
                            ? "Quiz Completed: {$session->book->title}"
                            : 'Quiz Completed: '.($session->context['book_title'] ?? 'Self Assessment'),
                        'message' => "You scored {$score}% on this quiz.",
                        'created_at' => $session->completed_at ?? $session->created_at,
                        'read_at' => $session->completed_at,
                        'data' => [
                            'quiz_id' => $session->id,
                            'score' => $score,
                            'subject' => $session->subject?->name ?? 'Self Assessment',
                        ],
                        'action_url' => null,
                        'icon' => 'assessment',
                        'color' => 'purple',
                    ];
                });

                $allNotifications = $allNotifications->merge($assessmentNotifications);
            }
        }

        // Apply search
        if ($this->search) {
            $searchTerm = strtolower($this->search);
            $allNotifications = $allNotifications->filter(function ($notification) use ($searchTerm) {
                return str_contains(strtolower($notification['title']), $searchTerm)
                    || str_contains(strtolower($notification['message'] ?? ''), $searchTerm);
            });
        }

        return $allNotifications->sortByDesc('created_at')->values();
    }

    public function getNotificationsProperty()
    {
        return $this->getNotifications();
    }

    public function getCountsProperty()
    {
        $user = auth()->user();

        $counts = [
            'all' => 0,
            'unread' => 0,
            'read' => 0,
            'assignment' => 0,
            'assessment' => 0,
            'submission' => 0,
            'other' => 0,
        ];

        $genericTotal = $user->notifications()->count();
        $genericUnread = $user->unreadNotifications()->count();
        $counts['other'] = $genericTotal;
        $counts['all'] += $genericTotal;
        $counts['unread'] += $genericUnread;
        $counts['read'] += ($genericTotal - $genericUnread);

        if ($user->student) {
            $assignmentTotal = AssignmentNotification::where('student_id', $user->student->id)->count();
            $assignmentUnread = AssignmentNotification::where('student_id', $user->student->id)
                ->whereNull('read_at')->count();
            $counts['assignment'] = $assignmentTotal;
            $counts['all'] += $assignmentTotal;
            $counts['unread'] += $assignmentUnread;
            $counts['read'] += ($assignmentTotal - $assignmentUnread);
        }

        if ($user->teacher) {
            $submissionTotal = AssignmentNotification::whereHas('assignment', function ($query) use ($user) {
                    $query->where('teacher_id', $user->teacher->id);
                })
                ->whereNotNull('notified_at')
                ->count();
            $submissionUnread = AssignmentNotification::whereHas('assignment', function ($query) use ($user) {
                    $query->where('teacher_id', $user->teacher->id);
                })
                ->whereNotNull('notified_at')
                ->whereNull('read_at')
                ->count();
            $counts['submission'] = $submissionTotal;
            $counts['all'] += $submissionTotal;
            $counts['unread'] += $submissionUnread;
            $counts['read'] += ($submissionTotal - $submissionUnread);
        }

        $assessmentTotal = QuizSession::where('user_id', $user->id)
            ->where('status', 'completed')->count();
        $counts['assessment'] = $assessmentTotal;
        $counts['all'] += $assessmentTotal;
        $counts['read'] += $assessmentTotal;

        return $counts;
    }

    private function getNotificationActionUrl($notification): ?string
    {
        $data = $notification->data;
        if (is_string($data)) {
            $data = json_decode($data, true) ?? [];
        }

        if (str_contains($notification->type, 'MessageNotification') && isset($data['message_id'])) {
            return route('admin.messages.show', $data['message_id']);
        }

        return $data['url'] ?? $data['action_url'] ?? null;
    }

    private function getNotificationCategory(string $notificationType): string
    {
        if (str_contains($notificationType, 'Assignment')) {
            return 'assignment';
        }
        if (str_contains($notificationType, 'Assessment') || str_contains($notificationType, 'Quiz')) {
            return 'assessment';
        }
        if (str_contains($notificationType, 'Attendance')) {
            return 'attendance';
        }
        if (str_contains($notificationType, 'Fee') || str_contains($notificationType, 'Payment')) {
            return 'payment';
        }
        if (str_contains($notificationType, 'Grade') || str_contains($notificationType, 'Score')) {
            return 'grade';
        }
        if (str_contains($notificationType, 'System') || str_contains($notificationType, 'Admin')) {
            return 'system';
        }
        if (str_contains($notificationType, 'Message') || str_contains($notificationType, 'Chat')) {
            return 'message';
        }

        return 'general';
    }

    private function getNotificationIcon(string $category): string
    {
        return match ($category) {
            'assignment' => 'assignment',
            'assessment' => 'assessment',
            'attendance' => 'attendance',
            'payment' => 'payment',
            'grade' => 'grade',
            'system' => 'system',
            'message' => 'message',
            default => 'general',
        };
    }

    private function getNotificationColor(string $category): string
    {
        return match ($category) {
            'assignment' => 'blue',
            'assessment' => 'purple',
            'attendance' => 'green',
            'payment' => 'amber',
            'grade' => 'indigo',
            'system' => 'gray',
            'message' => 'cyan',
            default => 'violet',
        };
    }

    private function getNotificationTitle($notification)
    {
        $data = $notification->data;
        if (is_string($data)) {
            $data = json_decode($data, true) ?? [];
        }

        return match (true) {
            str_contains($notification->type, 'MessageNotification') => $data['subject'] ?? $data['title'] ?? 'New Message',
            isset($data['title'])                                     => $data['title'],
            isset($data['subject'])                                   => $data['subject'],
            default                                                   => 'Notification',
        };
    }

    private function getNotificationMessage($notification)
    {
        $data = $notification->data;
        if (is_string($data)) {
            $data = json_decode($data, true) ?? [];
        }

        return match (true) {
            str_contains($notification->type, 'MessageNotification') => $data['body'] ?? $data['message'] ?? 'You have a new message.',
            isset($data['message'])                                   => $data['message'],
            isset($data['body'])                                      => $data['body'],
            default                                                   => '',
        };
    }

    private function getAssignmentNotificationTitle($notification)
    {
        if ($notification->assignment) {
            return "New {$notification->assignment->type}: {$notification->assignment->title}";
        }

        return 'Assignment Notification';
    }

    public function render()
    {
        return view('livewire.unified-notifications');
    }
}