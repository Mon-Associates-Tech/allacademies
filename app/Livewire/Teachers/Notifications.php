<?php

namespace App\Livewire\Teachers;

use App\Models\Assignment;
use App\Models\AssignmentNotification;
use App\Models\AssignmentSubmission;
use App\Models\Teacher;
use App\Notifications\NewAssignmentNotification;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class Notifications extends Component
{
    use WithPagination;

    // Filter properties
    public string $activeTab = 'all'; // all, unread, read

    public string $typeFilter = 'all'; // all, assignment, submission, system

    public string $search = '';

    public string $sortBy = 'newest'; // newest, oldest

    // Stats
    public int $totalNotifications = 0;

    public int $unreadCount = 0;

    public int $assignmentCount = 0;

    public int $submissionCount = 0;

    public int $systemCount = 0;

    // Teacher data
    public $teacher = null;

    public $recentSubmissions = [];

    public $pendingGrading = [];

    protected $queryString = [
        'activeTab' => ['except' => 'all'],
        'typeFilter' => ['except' => 'all'],
        'search' => ['except' => ''],
    ];

    public function mount(): void
    {
        $this->teacher = Teacher::where('user_id', Auth::id())->first();
        $this->loadStats();
        $this->loadSubmissionData();
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedActiveTab(): void
    {
        $this->resetPage();
    }

    public function updatedTypeFilter(): void
    {
        $this->resetPage();
    }

    public function loadStats(): void
    {
        $user = auth()->user();

        if (! $user || ! $this->teacher) {
            return;
        }

        // Count assignment notifications (assignments created by this teacher)
        $this->assignmentCount = AssignmentNotification::whereHas('assignment', function ($query) {
            $query->where('teacher_id', $this->teacher->id);
        })->count();

        // Count submission notifications (recent submissions to teacher's assignments)
        $this->submissionCount = AssignmentSubmission::whereHas('assignment', function ($query) {
            $query->where('teacher_id', $this->teacher->id);
        })
            ->where('submitted_at', '>', now()->subDays(30))
            ->count();

        // Count system/generic notifications
        $this->systemCount = $user->notifications()->count();
        $genericUnread = $user->unreadNotifications()->count();

        // Count unread assignment notifications
        $assignmentUnread = AssignmentNotification::whereHas('assignment', function ($query) {
            $query->where('teacher_id', $this->teacher->id);
        })->whereNull('read_at')->count();

        $this->totalNotifications = $this->assignmentCount + $this->submissionCount + $this->systemCount;
        $this->unreadCount = $assignmentUnread + $genericUnread;
    }

    public function getNotificationsProperty(): Collection
    {
        $user = auth()->user();

        if (! $user || ! $this->teacher) {
            return collect();
        }

        $notifications = collect();

        // Get assignment notifications
        if ($this->typeFilter === 'all' || $this->typeFilter === 'assignment') {
            $assignmentQuery = AssignmentNotification::whereHas('assignment', function ($query) {
                $query->where('teacher_id', $this->teacher->id);
            })
                ->with(['assignment', 'assignment.academicSubject', 'student.user']);

            // Apply read/unread filter
            if ($this->activeTab === 'unread') {
                $assignmentQuery->whereNull('read_at');
            } elseif ($this->activeTab === 'read') {
                $assignmentQuery->whereNotNull('read_at');
            }

            $assignmentNotifications = $assignmentQuery->get()->map(function ($notification) {
                $studentName = $notification->student?->user?->name ?? 'Unknown Student';
                $title = $notification->assignment
                    ? "Assignment Assigned: {$notification->assignment->title}"
                    : 'Assignment Notification';

                return [
                    'id' => 'assignment_'.$notification->id,
                    'type' => 'assignment',
                    'category' => 'assignment',
                    'title' => $title,
                    'message' => "Assignment '{$notification->assignment?->title}' was assigned to {$studentName}",
                    'subject' => $notification->assignment?->academicSubject?->name ?? 'Unknown Subject',
                    'student' => $studentName,
                    'created_at' => $notification->notified_at,
                    'read_at' => $notification->read_at,
                    'is_read' => $notification->read_at !== null,
                    'assignment_id' => $notification->assignment_id,
                    'due_date' => $notification->assignment?->ends_at,
                    'icon' => 'assignment',
                    'color' => 'blue',
                ];
            });

            $notifications = $notifications->merge($assignmentNotifications);
        }

        // Get submission notifications
        if ($this->typeFilter === 'all' || $this->typeFilter === 'submission') {
            $submissionQuery = AssignmentSubmission::whereHas('assignment', function ($query) {
                $query->where('teacher_id', $this->teacher->id);
            })
                ->with(['assignment', 'assignment.academicSubject', 'student.user'])
                ->where('submitted_at', '>', now()->subDays(30));

            $submissionNotifications = $submissionQuery->get()->map(function ($submission) {
                $studentName = $submission->student?->user?->name ?? 'Unknown Student';
                $needsGrading = $submission->status === 'submitted';

                return [
                    'id' => 'submission_'.$submission->id,
                    'type' => 'submission',
                    'category' => 'submission',
                    'title' => "New Submission: {$submission->assignment->title}",
                    'message' => "{$studentName} submitted {$submission->assignment->title}".
                        ($submission->score !== null ? " - Score: {$submission->score}/{$submission->total_marks}" : ' - Pending grading'),
                    'subject' => $submission->assignment?->academicSubject?->name ?? 'Unknown Subject',
                    'student' => $studentName,
                    'created_at' => $submission->submitted_at,
                    'read_at' => $needsGrading ? null : $submission->graded_at,
                    'is_read' => ! $needsGrading,
                    'submission_id' => $submission->id,
                    'assignment_id' => $submission->assignment_id,
                    'score' => $submission->score,
                    'total_marks' => $submission->total_marks,
                    'status' => $submission->status,
                    'needs_grading' => $needsGrading,
                    'icon' => 'submission',
                    'color' => $needsGrading ? 'orange' : 'green',
                ];
            });

            // Apply read/unread filter for submissions
            if ($this->activeTab === 'unread') {
                $submissionNotifications = $submissionNotifications->filter(fn ($n) => ! $n['is_read']);
            } elseif ($this->activeTab === 'read') {
                $submissionNotifications = $submissionNotifications->filter(fn ($n) => $n['is_read']);
            }

            $notifications = $notifications->merge($submissionNotifications);
        }

        // Get system/generic notifications
        if ($this->typeFilter === 'all' || $this->typeFilter === 'system') {
            $genericQuery = $user->notifications();

            if ($this->activeTab === 'unread') {
                $genericQuery = $user->unreadNotifications();
            } elseif ($this->activeTab === 'read') {
                $genericQuery = $user->readNotifications();
            }

            $genericNotifications = $genericQuery->get()->map(function ($notification) {
                return [
                    'id' => $notification->id,
                    'type' => 'system',
                    'category' => 'system',
                    'title' => $this->getNotificationTitle($notification),
                    'message' => $this->getNotificationMessage($notification),
                    'subject' => null,
                    'student' => null,
                    'created_at' => $notification->created_at,
                    'read_at' => $notification->read_at,
                    'is_read' => $notification->read_at !== null,
                    'icon' => 'system',
                    'color' => 'gray',
                ];
            });

            $notifications = $notifications->merge($genericNotifications);
        }

        // Apply search filter
        if ($this->search) {
            $searchTerm = strtolower($this->search);
            $notifications = $notifications->filter(function ($notification) use ($searchTerm) {
                return str_contains(strtolower($notification['title']), $searchTerm)
                    || str_contains(strtolower($notification['message'] ?? ''), $searchTerm)
                    || str_contains(strtolower($notification['subject'] ?? ''), $searchTerm)
                    || str_contains(strtolower($notification['student'] ?? ''), $searchTerm);
            });
        }

        // Sort notifications
        if ($this->sortBy === 'newest') {
            $notifications = $notifications->sortByDesc('created_at');
        } else {
            $notifications = $notifications->sortBy('created_at');
        }

        return $notifications->values();
    }

    public function loadSubmissionData(): void
    {
        if (! $this->teacher) {
            return;
        }

        // Get recent submissions needing grading
        $this->pendingGrading = AssignmentSubmission::whereHas('assignment', function ($query) {
            $query->where('teacher_id', $this->teacher->id);
        })
            ->where('status', 'submitted')
            ->with(['assignment', 'assignment.academicSubject', 'student.user'])
            ->latest('submitted_at')
            ->take(5)
            ->get()
            ->map(function ($submission) {
                return [
                    'id' => $submission->id,
                    'assignment_id' => $submission->assignment_id,
                    'title' => $submission->assignment->title,
                    'student' => $submission->student?->user?->name ?? 'Unknown',
                    'subject' => $submission->assignment?->academicSubject?->name ?? 'Unknown',
                    'submitted_at' => $submission->submitted_at,
                ];
            })
            ->toArray();

        // Get recent submissions
        $this->recentSubmissions = AssignmentSubmission::whereHas('assignment', function ($query) {
            $query->where('teacher_id', $this->teacher->id);
        })
            ->with(['assignment', 'student.user'])
            ->latest('submitted_at')
            ->take(5)
            ->get()
            ->map(function ($submission) {
                return [
                    'id' => $submission->id,
                    'title' => $submission->assignment->title,
                    'student' => $submission->student?->user?->name ?? 'Unknown',
                    'score' => $submission->score,
                    'total_marks' => $submission->total_marks,
                    'status' => $submission->status,
                    'submitted_at' => $submission->submitted_at,
                ];
            })
            ->toArray();
    }

    public function markNotificationAsRead(string $notificationId, string $type): void
    {
        $user = auth()->user();

        if ($type === 'assignment') {
            $id = str_replace('assignment_', '', $notificationId);
            $notification = AssignmentNotification::whereHas('assignment', function ($query) {
                $query->where('teacher_id', $this->teacher->id);
            })->where('id', $id)->first();

            if ($notification) {
                $notification->update(['read_at' => now()]);
            }
        } elseif ($type === 'system') {
            $notification = DatabaseNotification::where('id', $notificationId)
                ->where('notifiable_id', $user->id)
                ->first();

            if ($notification) {
                $notification->markAsRead();
            }
        }

        $this->loadStats();
    }

    public function markAllAsRead(): void
    {
        $user = auth()->user();

        if (! $user || ! $this->teacher) {
            return;
        }

        // Mark all assignment notifications as read
        AssignmentNotification::whereHas('assignment', function ($query) {
            $query->where('teacher_id', $this->teacher->id);
        })
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        // Mark all generic notifications as read
        $user->unreadNotifications->markAsRead();

        $this->loadStats();

        $this->dispatch('notifications-marked-read');
    }

    public function deleteNotification(string $notificationId, string $type): void
    {
        $user = auth()->user();

        if ($type === 'system') {
            $notification = DatabaseNotification::where('id', $notificationId)
                ->where('notifiable_id', $user->id)
                ->first();

            if ($notification) {
                $notification->delete();
            }
        }

        $this->loadStats();
        $this->dispatch('notification-deleted');
    }

    public function viewSubmission(int $submissionId): mixed
    {
        return redirect()->route('teachers.submissions.show', ['submission' => $submissionId]);
    }

    public function viewAssignment(int $assignmentId): mixed
    {
        return redirect()->route('teachers.assignments.show', ['assignment' => $assignmentId]);
    }

    public function setActiveTab(string $tab): void
    {
        $this->activeTab = $tab;
        $this->resetPage();
    }

    public function setTypeFilter(string $type): void
    {
        $this->typeFilter = $type;
        $this->resetPage();
    }

    public function clearFilters(): void
    {
        $this->activeTab = 'all';
        $this->typeFilter = 'all';
        $this->search = '';
        $this->sortBy = 'newest';
        $this->resetPage();
    }

    private function getNotificationTitle($notification): string
    {
        $data = $notification->data;

        return match ($notification->type) {
            NewAssignmentNotification::class => "Assignment: {$data['title']}",
            default => $data['title'] ?? 'Notification',
        };
    }

    private function getNotificationMessage($notification): string
    {
        $data = $notification->data;

        return match ($notification->type) {
            NewAssignmentNotification::class => $data['message'] ?? 'Assignment notification.',
            default => $data['message'] ?? 'You have a new notification.',
        };
    }

    public function render()
    {
        return view('livewire.teachers.notifications', [
            'notifications' => $this->notifications,
        ]);
    }
}
