<?php

namespace App\Livewire\Students;

use App\Models\AssignmentNotification;
use App\Models\AssignmentSubmission;
use App\Models\QuizSession;
use App\Notifications\NewAssignmentNotification;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Collection;
use Livewire\Component;
use Livewire\WithPagination;

class Notifications extends Component
{
    use WithPagination;

    // Filter properties
    public string $activeTab = 'all'; // all, unread, read

    public string $typeFilter = 'all'; // all, assignment, assessment, other

    public string $search = '';

    public string $sortBy = 'newest'; // newest, oldest

    // Stats
    public int $totalNotifications = 0;

    public int $unreadCount = 0;

    public int $assignmentCount = 0;

    public int $assessmentCount = 0;

    public int $otherCount = 0;

    // Assignment data
    public $upcomingAssignments = [];

    public $pendingAssignments = [];

    public int $completedAssignmentsCount = 0;

    protected $queryString = [
        'activeTab' => ['except' => 'all'],
        'typeFilter' => ['except' => 'all'],
        'search' => ['except' => ''],
    ];

    public function mount(): void
    {
        $this->loadStats();
        $this->loadAssignmentData();
        $this->loadCompletedAssignmentsCount();
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

        if (! $user || ! $user->student) {
            return;
        }

        // Count assignment notifications
        $assignmentNotificationsQuery = AssignmentNotification::where('student_id', $user->student->id);
        $this->assignmentCount = $assignmentNotificationsQuery->count();
        $assignmentUnread = (clone $assignmentNotificationsQuery)->whereNull('read_at')->count();

        // Count assessment notifications (quiz sessions)
        $this->assessmentCount = QuizSession::where('user_id', $user->id)
            ->where('status', 'completed')
            ->count();

        // Count generic/other notifications
        $genericNotificationsQuery = $user->notifications();
        $this->otherCount = $genericNotificationsQuery->count();
        $genericUnread = $user->unreadNotifications()->count();

        $this->totalNotifications = $this->assignmentCount + $this->assessmentCount + $this->otherCount;
        $this->unreadCount = $assignmentUnread + $genericUnread;
    }

    public function getNotificationsProperty(): Collection
    {
        $user = auth()->user();

        if (! $user || ! $user->student) {
            return collect();
        }

        $notifications = collect();

        // Get assignment notifications
        if ($this->typeFilter === 'all' || $this->typeFilter === 'assignment') {
            $assignmentQuery = AssignmentNotification::where('student_id', $user->student->id)
                ->with(['assignment', 'assignment.academicSubject', 'assignment.teacher.user']);

            // Apply read/unread filter
            if ($this->activeTab === 'unread') {
                $assignmentQuery->whereNull('read_at');
            } elseif ($this->activeTab === 'read') {
                $assignmentQuery->whereNotNull('read_at');
            }

            $assignmentNotifications = $assignmentQuery->get()->map(function ($notification) {
                $title = $notification->assignment
                    ? "New {$notification->assignment->type}: {$notification->assignment->title}"
                    : 'Assignment Notification';

                return [
                    'id' => $notification->id,
                    'type' => 'assignment',
                    'category' => 'assignment',
                    'title' => $title,
                    'message' => $notification->message ?? 'You have a new assignment.',
                    'subject' => $notification->assignment?->academicSubject?->name ?? 'Unknown Subject',
                    'teacher' => $notification->assignment?->teacher?->user?->name ?? 'Unknown Teacher',
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

        // Get assessment notifications (completed quiz sessions)
        if ($this->typeFilter === 'all' || $this->typeFilter === 'assessment') {
            $quizQuery = QuizSession::where('user_id', $user->id)
                ->where('status', 'completed')
                ->with(['book', 'subject']);

            $quizNotifications = $quizQuery->get()->map(function ($session) {
                $title = $session->book
                    ? "Quiz completed: {$session->book->title}"
                    : 'Quiz completed: '.($session->context['book_title'] ?? 'Uploaded Content');

                $score = $session->results['percentage'] ?? 0;

                return [
                    'id' => 'quiz_'.$session->id,
                    'type' => 'assessment',
                    'category' => 'assessment',
                    'title' => $title,
                    'message' => "You scored {$score}% on this quiz.",
                    'subject' => $session->subject?->name ?? 'Self Assessment',
                    'teacher' => null,
                    'created_at' => $session->completed_at,
                    'read_at' => $session->completed_at, // Assessments are considered "read" once completed
                    'is_read' => true,
                    'quiz_id' => $session->id,
                    'score' => $score,
                    'icon' => 'assessment',
                    'color' => 'purple',
                ];
            });

            // For assessments, only show in "all" or "read" tabs (they're always "read")
            if ($this->activeTab !== 'unread') {
                $notifications = $notifications->merge($quizNotifications);
            }
        }

        // Get generic/other notifications
        if ($this->typeFilter === 'all' || $this->typeFilter === 'other') {
            $genericQuery = $user->notifications();

            if ($this->activeTab === 'unread') {
                $genericQuery = $user->unreadNotifications();
            } elseif ($this->activeTab === 'read') {
                $genericQuery = $user->readNotifications();
            }

            $genericNotifications = $genericQuery->get()->map(function ($notification) {
                return [
                    'id' => $notification->id,
                    'type' => 'generic',
                    'category' => 'other',
                    'title' => $this->getNotificationTitle($notification),
                    'message' => $this->getNotificationMessage($notification),
                    'subject' => null,
                    'teacher' => null,
                    'created_at' => $notification->created_at,
                    'read_at' => $notification->read_at,
                    'is_read' => $notification->read_at !== null,
                    'icon' => 'other',
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
                    || str_contains(strtolower($notification['subject'] ?? ''), $searchTerm);
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

    public function loadAssignmentData(): void
    {
        $user = auth()->user();

        if (! $user || ! $user->student) {
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
            ->take(5)
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
            ->take(5)
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

    public function loadCompletedAssignmentsCount(): void
    {
        $user = auth()->user();

        if (! $user || ! $user->student) {
            $this->completedAssignmentsCount = 0;

            return;
        }

        $this->completedAssignmentsCount = AssignmentSubmission::where('student_id', $user->student->id)
            ->whereNotNull('submitted_at')
            ->count();
    }

    public function getCompletedAssignmentsCount(): int
    {
        return $this->completedAssignmentsCount;
    }

    public function markNotificationAsRead(string $notificationId, string $type): void
    {
        $user = auth()->user();

        if ($type === 'assignment') {
            $notification = AssignmentNotification::where('id', $notificationId)
                ->where('student_id', $user->student->id)
                ->first();

            if ($notification) {
                $notification->update(['read_at' => now()]);
            }
        } elseif ($type === 'generic') {
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

        if (! $user || ! $user->student) {
            return;
        }

        // Mark all assignment notifications as read
        AssignmentNotification::where('student_id', $user->student->id)
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        // Mark all generic notifications as read
        $user->unreadNotifications->markAsRead();

        $this->loadStats();

        $this->dispatch('notifications-marked-read');
    }

    public function startAssignment(int $assignmentId): mixed
    {
        return redirect()->route('students.assignment.take', ['assignment' => $assignmentId]);
    }

    public function viewQuizResults(int $quizId): mixed
    {
        return redirect()->route('learning.quiz', ['quiz' => $quizId]);
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
            NewAssignmentNotification::class => "New {$data['type']}: {$data['title']}",
            default => $data['title'] ?? 'Notification',
        };
    }

    private function getNotificationMessage($notification): string
    {
        $data = $notification->data;

        return match ($notification->type) {
            NewAssignmentNotification::class => $data['message'] ?? 'New assignment has been created.',
            default => $data['message'] ?? 'You have a new notification.',
        };
    }

    public function render()
    {
        return view('livewire.students.notifications', [
            'notifications' => $this->notifications,
        ]);
    }
}
