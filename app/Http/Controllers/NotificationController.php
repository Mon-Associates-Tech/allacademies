<?php

namespace App\Http\Controllers;

use App\Models\AssignmentNotification;
use App\Models\QuizSession;
use Illuminate\Http\Request;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        if (! $user) {
            return redirect()->route('login');
        }

        // Get filter parameters
        $filter = $request->get('filter', 'all'); // all, unread, read
        $type = $request->get('type', 'all'); // all, assignment, assessment, other
        $search = $request->get('search', '');

        // Initialize collections
        $allNotifications = collect();

        // Get generic Laravel notifications
        $genericQuery = $user->notifications();

        // Apply read/unread filter for generic notifications
        if ($filter === 'unread') {
            $genericQuery = $user->unreadNotifications();
        } elseif ($filter === 'read') {
            $genericQuery = $user->readNotifications();
        }

        $genericNotifications = $genericQuery->latest()->get()->map(function ($notification) {
            $category = $this->getNotificationCategory($notification->type);

            return [
                'id' => $notification->id,
                'type' => 'generic',
                'category' => $category,
                'notification_type' => $notification->type,
                'title' => $this->getNotificationTitle($notification),
                'message' => $this->getNotificationMessage($notification),
                'created_at' => $notification->created_at,
                'read_at' => $notification->read_at,
                'data' => $notification->data,
                'icon' => $this->getNotificationIcon($category),
                'color' => $this->getNotificationColor($category),
            ];
        });

        // Add generic notifications based on type filter
        if ($type === 'all' || $type === 'other') {
            $allNotifications = $allNotifications->merge($genericNotifications);
        }

        // Get assignment notifications if user is a student
        if ($user->student && ($type === 'all' || $type === 'assignment')) {
            $assignmentQuery = AssignmentNotification::where('student_id', $user->student->id)
                ->with(['assignment', 'assignment.academicSubject', 'assignment.teacher.user']);

            // Apply read/unread filter
            if ($filter === 'unread') {
                $assignmentQuery->whereNull('read_at');
            } elseif ($filter === 'read') {
                $assignmentQuery->whereNotNull('read_at');
            }

            $assignmentNotifications = $assignmentQuery->latest('notified_at')->get()->map(function ($notification) {
                return [
                    'id' => $notification->id,
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
                        'teacher' => $notification->assignment->user->name ?? 'Unknown Teacher',
                    ],
                    'icon' => 'assignment',
                    'color' => 'blue',
                ];
            });

            $allNotifications = $allNotifications->merge($assignmentNotifications);
        }

        // Get assessment notifications (quiz sessions) if user has any
        if ($type === 'all' || $type === 'assessment') {
            $quizQuery = QuizSession::where('user_id', $user->id)
                ->where('status', 'completed')
                ->with(['book', 'subject']);

            // For assessments, they're always "read" once completed
            if ($filter !== 'unread') {
                $assessmentNotifications = $quizQuery->latest('completed_at')->get()->map(function ($session) {
                    $score = $session->results['percentage'] ?? 0;

                    return [
                        'id' => $session->id,
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
                        'icon' => 'assessment',
                        'color' => 'purple',
                    ];
                });

                $allNotifications = $allNotifications->merge($assessmentNotifications);
            }
        }

        // Apply search filter
        if ($search) {
            $searchTerm = strtolower($search);
            $allNotifications = $allNotifications->filter(function ($notification) use ($searchTerm) {
                return str_contains(strtolower($notification['title']), $searchTerm)
                    || str_contains(strtolower($notification['message'] ?? ''), $searchTerm);
            });
        }

        // Sort by created_at descending
        $allNotifications = $allNotifications->sortByDesc('created_at')->values();

        // Paginate the collection
        $page = $request->get('page', 1);
        $perPage = 15;
        $total = $allNotifications->count();
        $notifications = new \Illuminate\Pagination\LengthAwarePaginator(
            $allNotifications->forPage($page, $perPage),
            $total,
            $perPage,
            $page,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        // Calculate counts for filter badges
        $counts = $this->getNotificationCounts($user);

        return view('notifications.index', compact('notifications', 'filter', 'type', 'search', 'counts'));
    }

    /**
     * Get notification counts for filter badges.
     */
    private function getNotificationCounts($user): array
    {
        $counts = [
            'all' => 0,
            'unread' => 0,
            'read' => 0,
            'assignment' => 0,
            'assessment' => 0,
            'other' => 0,
        ];

        // Generic notifications
        $genericTotal = $user->notifications()->count();
        $genericUnread = $user->unreadNotifications()->count();
        $counts['other'] = $genericTotal;
        $counts['all'] += $genericTotal;
        $counts['unread'] += $genericUnread;
        $counts['read'] += ($genericTotal - $genericUnread);

        // Assignment notifications (for students)
        if ($user->student) {
            $assignmentTotal = AssignmentNotification::where('student_id', $user->student->id)->count();
            $assignmentUnread = AssignmentNotification::where('student_id', $user->student->id)
                ->whereNull('read_at')->count();
            $counts['assignment'] = $assignmentTotal;
            $counts['all'] += $assignmentTotal;
            $counts['unread'] += $assignmentUnread;
            $counts['read'] += ($assignmentTotal - $assignmentUnread);
        }

        // Assessment notifications (quiz sessions)
        $assessmentTotal = QuizSession::where('user_id', $user->id)
            ->where('status', 'completed')->count();
        $counts['assessment'] = $assessmentTotal;
        $counts['all'] += $assessmentTotal;
        // Assessments are always "read" once completed
        $counts['read'] += $assessmentTotal;

        return $counts;
    }

    public function show($type, $id)
    {
        $user = Auth::user();
        $notification = null;
        $notificationData = null;

        if ($type === 'generic') {
            $notification = DatabaseNotification::where('id', $id)
                ->where('notifiable_id', $user->id)
                ->first();

            if ($notification) {
                // Mark as read if not already
                if (! $notification->read_at) {
                    $notification->markAsRead();
                }

                // Determine the category based on notification type
                $category = $this->getNotificationCategory($notification->type);

                $notificationData = [
                    'id' => $notification->id,
                    'type' => 'generic',
                    'category' => $category,
                    'notification_type' => $notification->type,
                    'title' => $this->getNotificationTitle($notification),
                    'message' => $this->getNotificationMessage($notification),
                    'created_at' => $notification->created_at,
                    'read_at' => $notification->read_at,
                    'data' => $notification->data,
                    'icon' => $this->getNotificationIcon($category),
                    'color' => $this->getNotificationColor($category),
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
                    if (! $notification->read_at) {
                        $notification->update(['read_at' => now()]);
                    }

                    $notificationData = [
                        'id' => $notification->id,
                        'type' => 'assignment',
                        'category' => 'assignment',
                        'notification_type' => 'assignment',
                        'title' => $this->getAssignmentNotificationTitle($notification),
                        'message' => $notification->message ?? 'You have a new assignment to complete.',
                        'created_at' => $notification->notified_at,
                        'read_at' => $notification->read_at,
                        'data' => [
                            'assignment_id' => $notification->assignment_id,
                            'assignment_title' => $notification->assignment->title ?? 'Unknown Assignment',
                            'subject' => $notification->assignment->academicSubject->name ?? 'Unknown Subject',
                            'teacher' => $notification->assignment->user->name ?? 'Unknown Teacher',
                            'type' => $notification->assignment->type ?? 'Unknown',
                            'starts_at' => $notification->assignment->starts_at ?? null,
                            'ends_at' => $notification->assignment->ends_at ?? null,
                            'duration_in_minutes' => $notification->assignment->duration_in_minutes ?? null,
                            'status' => $notification->assignment->status ?? 'unknown',
                            'total_marks' => $notification->assignment->total_marks ?? null,
                        ],
                        'assignment' => $notification->assignment,
                        'icon' => 'assignment',
                        'color' => 'blue',
                    ];
                }
            }
        } elseif ($type === 'assessment') {
            // Handle quiz/assessment notifications
            $quizSession = QuizSession::where('id', $id)
                ->where('user_id', $user->id)
                ->with(['book', 'subject'])
                ->first();

            if ($quizSession) {
                $notification = $quizSession; // Use quiz session as the notification source

                $score = $quizSession->results['percentage'] ?? 0;
                $correctAnswers = $quizSession->results['correct_answers'] ?? 0;
                $totalQuestions = $quizSession->results['total_questions'] ?? 0;

                $notificationData = [
                    'id' => $quizSession->id,
                    'type' => 'assessment',
                    'category' => 'assessment',
                    'notification_type' => 'assessment',
                    'title' => $quizSession->book
                        ? "Quiz Completed: {$quizSession->book->title}"
                        : 'Quiz Completed: '.($quizSession->context['book_title'] ?? 'Self Assessment'),
                    'message' => "You scored {$score}% on this quiz ({$correctAnswers}/{$totalQuestions} correct answers).",
                    'created_at' => $quizSession->completed_at ?? $quizSession->created_at,
                    'read_at' => $quizSession->completed_at, // Assessments are "read" when completed
                    'data' => [
                        'quiz_id' => $quizSession->id,
                        'book_title' => $quizSession->book?->title ?? ($quizSession->context['book_title'] ?? 'Uploaded Content'),
                        'subject' => $quizSession->subject?->name ?? 'Self Assessment',
                        'score' => $score,
                        'correct_answers' => $correctAnswers,
                        'total_questions' => $totalQuestions,
                        'time_spent' => $quizSession->time_spent_seconds ?? null,
                        'difficulty' => $quizSession->difficulty ?? 'medium',
                        'status' => $quizSession->status,
                    ],
                    'quiz_session' => $quizSession,
                    'icon' => 'assessment',
                    'color' => 'purple',
                ];
            }
        }

        if (! $notification) {
            abort(404);
        }

        return view('notifications.show', compact('notificationData'));
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
                return $data['message'] ?? 'New assignment has been created.';
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
