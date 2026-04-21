<?php

namespace App\Services\Students;

use App\Models\AcademicLevel;
use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\BookReadingProgress;
use App\Models\QuizSession;
use App\Models\Student;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;

class StudentProgressQueryService
{
    /**
     * Build a consistent student snapshot for dashboards and activity pages.
     */
    public function buildSnapshot(Student $student, ?Carbon $rangeStart = null): array
    {
        $assignedAssignments = $this->assignedAssignmentsQuery($student)->get();
        $availableAssignments = $this->availableAssignmentsQuery($student)->get();

        $allSubmissions = AssignmentSubmission::query()
            ->where('student_id', $student->id)
            ->with('assignment.academicSubject')
            ->get();

        $completedStatuses = ['completed', 'submitted', 'graded'];
        $submittedStatuses = ['submitted', 'completed', 'graded'];
        $gradedStatuses = ['graded', 'completed'];

        $submittedAssignments = $allSubmissions->whereIn('status', $submittedStatuses)->count();
        $gradedAssignments = $allSubmissions->whereIn('status', $gradedStatuses)->count();
        $completedAssignments = $allSubmissions->whereIn('status', $completedStatuses)->count();
        $ongoingAssignments = $allSubmissions->where('status', 'in_progress')->count();
        $gradedSubmissions = $allSubmissions->whereIn('status', $gradedStatuses);

        $overdueAssignments = $assignedAssignments
            ->filter(function (Assignment $assignment) use ($allSubmissions, $completedStatuses): bool {
                $submission = $allSubmissions->firstWhere('assignment_id', $assignment->id);

                return $assignment->ends_at < now()
                    && (! $submission || ! in_array($submission->status, $completedStatuses, true));
            })
            ->count();

        $upcomingAssignments = $availableAssignments
            ->filter(function (Assignment $assignment) use ($allSubmissions, $completedStatuses): bool {
                $submission = $allSubmissions->firstWhere('assignment_id', $assignment->id);

                return $assignment->ends_at >= now()
                    && $assignment->ends_at <= now()->addDays(7)
                    && (! $submission || ! in_array($submission->status, $completedStatuses, true));
            })
            ->count();

        $recentAssignments = $allSubmissions
            ->sortByDesc(function (AssignmentSubmission $submission): int {
                return $submission->submitted_at?->timestamp ?? $submission->updated_at->timestamp;
            })
            ->take(5)
            ->map(function (AssignmentSubmission $submission): array {
                $percentage = 0.0;
                if ((float) $submission->total_marks > 0.0) {
                    $percentage = ((float) $submission->score / (float) $submission->total_marks) * 100;
                }

                return [
                    'id' => $submission->assignment_id,
                    'title' => $submission->assignment?->title ?? 'Assignment',
                    'subject' => $submission->assignment?->academicSubject?->name ?? 'N/A',
                    'score' => (float) $submission->score,
                    'total_marks' => (float) $submission->total_marks,
                    'percentage' => round($percentage, 1),
                    'submitted_at' => $submission->submitted_at ?? $submission->updated_at,
                    'status' => (string) $submission->status,
                ];
            })
            ->values()
            ->all();

        $upcomingDueAssignments = $availableAssignments
            ->filter(function (Assignment $assignment) use ($allSubmissions, $completedStatuses): bool {
                $submission = $allSubmissions->firstWhere('assignment_id', $assignment->id);

                return $assignment->ends_at >= now()
                    && $assignment->ends_at <= now()->addDays(7)
                    && (! $submission || ! in_array($submission->status, $completedStatuses, true));
            })
            ->sortBy('ends_at')
            ->take(5)
            ->map(function (Assignment $assignment): array {
                $diffInHours = now()->diffInHours($assignment->ends_at, false);
                $daysUntilDue = $diffInHours < 24 && $diffInHours > 0
                    ? round($diffInHours / 24, 1)
                    : (int) floor($diffInHours / 24);

                return [
                    'id' => $assignment->id,
                    'title' => $assignment->title,
                    'subject' => $assignment->academicSubject?->name ?? 'N/A',
                    'due_date' => $assignment->ends_at,
                    'days_until_due' => $daysUntilDue,
                    'hours_until_due' => max(0, $diffInHours),
                ];
            })
            ->values()
            ->all();

        $quizSessions = QuizSession::query()
            ->where('user_id', $student->user_id)
            ->where('status', 'completed')
            ->with(['book', 'subject'])
            ->when($rangeStart, function (Builder $query) use ($rangeStart): void {
                $query->where('completed_at', '>=', $rangeStart);
            })
            ->orderByDesc('completed_at')
            ->get();

        $recentQuizzes = $quizSessions->take(5)
            ->map(function (QuizSession $session): array {
                return [
                    'id' => $session->id,
                    'book_title' => $session->book?->title ?? ($session->context['book_title'] ?? 'Uploaded Content'),
                    'score' => (float) ($session->results['percentage'] ?? 0),
                    'completed_at' => $session->completed_at,
                    'questions_count' => (int) ($session->question_count ?? 0),
                    'difficulty' => $session->difficulty ?? 'medium',
                    'type' => $session->book_id ? 'book' : 'uploaded',
                ];
            })
            ->values()
            ->all();

        $readingProgress = BookReadingProgress::query()
            ->where('user_id', $student->user_id)
            ->when($rangeStart, function (Builder $query) use ($rangeStart): void {
                $query->where(function (Builder $inner) use ($rangeStart): void {
                    $inner->where('last_read_at', '>=', $rangeStart)
                        ->orWhereNull('last_read_at');
                });
            })
            ->get();

        $booksCompleted = $readingProgress->filter(function (BookReadingProgress $progress): bool {
            return (int) $progress->total_pages > 0 && (int) $progress->current_page >= (int) $progress->total_pages;
        })->count();

        $booksInProgress = $readingProgress->filter(function (BookReadingProgress $progress): bool {
            return (int) $progress->current_page > 0
                && ((int) $progress->total_pages === 0 || (int) $progress->current_page < (int) $progress->total_pages);
        })->count();

        $totalAssigned = $assignedAssignments->count();
        $completionRate = $totalAssigned > 0
            ? round(($completedAssignments / max(1, $totalAssigned)) * 100, 1)
            : 0.0;

        return [
            'assignments' => [
                'total_assigned' => $totalAssigned,
                'total_available_now' => $availableAssignments->count(),
                'completed' => $completedAssignments,
                'submitted' => $submittedAssignments,
                'graded' => $gradedAssignments,
                'ongoing' => $ongoingAssignments,
                'overdue' => $overdueAssignments,
                'upcoming' => $upcomingAssignments,
                'completion_rate' => $completionRate,
                'average_score' => $gradedSubmissions->count() > 0
                    ? round($gradedSubmissions->avg(function (AssignmentSubmission $submission): float {
                        if ((float) $submission->total_marks <= 0.0) {
                            return 0.0;
                        }

                        return ((float) $submission->score / (float) $submission->total_marks) * 100;
                    }), 1)
                    : 0.0,
            ],
            'quizzes' => [
                'total' => $quizSessions->count(),
                'average_score' => $quizSessions->count() > 0
                    ? round($quizSessions->avg(fn (QuizSession $session): float => (float) ($session->results['percentage'] ?? 0)), 1)
                    : 0.0,
                'best_score' => $quizSessions->count() > 0
                    ? (float) $quizSessions->max(fn (QuizSession $session): float => (float) ($session->results['percentage'] ?? 0))
                    : 0.0,
                'last_completed_at' => $quizSessions->first()?->completed_at,
                'recent' => $recentQuizzes,
            ],
            'reading' => [
                'books_started' => $readingProgress->count(),
                'books_completed' => $booksCompleted,
                'books_in_progress' => $booksInProgress,
                'total_pages_read' => (int) $readingProgress->sum('current_page'),
                'last_read_at' => $readingProgress->max('last_read_at'),
            ],
            'recent' => [
                'assignments' => $recentAssignments,
                'upcoming_due_assignments' => $upcomingDueAssignments,
            ],
        ];
    }

    /**
     * Reusable assignment eligibility query for a student.
     */
    public function assignedAssignmentsQuery(Student $student): Builder
    {
        return $this->baseEligibleAssignmentsQuery($student)
            ->where('status', 'published')
            ->with(['academicSubject', 'teacher.user']);
    }

    /**
     * Reusable active assignment query for a student.
     */
    public function availableAssignmentsQuery(Student $student): Builder
    {
        return $this->baseEligibleAssignmentsQuery($student)
            ->where('status', 'published')
            ->where('ends_at', '>', now())
            ->with(['academicSubject', 'teacher.user']);
    }

    protected function baseEligibleAssignmentsQuery(Student $student): Builder
    {
        $academicLevelGroupId = null;
        if ($student->academic_level_id) {
            $academicLevelGroupId = AcademicLevel::query()
                ->where('id', $student->academic_level_id)
                ->value('academic_group_id');
        }

        return Assignment::query()
            ->where(function (Builder $query) use ($student, $academicLevelGroupId): void {
                $hasCondition = false;

                if ($student->academic_level_id) {
                    $query->whereHas('academicLevels', function (Builder $inner) use ($student): void {
                        $inner->where('academic_levels.id', $student->academic_level_id);
                    });
                    $hasCondition = true;
                }

                if ($academicLevelGroupId) {
                    if ($hasCondition) {
                        $query->orWhereHas('academicGroups', function (Builder $inner) use ($academicLevelGroupId): void {
                            $inner->where('academic_groups.id', $academicLevelGroupId);
                        });
                    } else {
                        $query->whereHas('academicGroups', function (Builder $inner) use ($academicLevelGroupId): void {
                            $inner->where('academic_groups.id', $academicLevelGroupId);
                        });
                        $hasCondition = true;
                    }
                }

                if ($student->academic_group_id) {
                    if ($hasCondition) {
                        $query->orWhereHas('academicGroups', function (Builder $inner) use ($student): void {
                            $inner->where('academic_groups.id', $student->academic_group_id);
                        });
                    } else {
                        $query->whereHas('academicGroups', function (Builder $inner) use ($student): void {
                            $inner->where('academic_groups.id', $student->academic_group_id);
                        });
                        $hasCondition = true;
                    }
                }

                if ($student->student_group_id) {
                    if ($hasCondition) {
                        $query->orWhereHas('studentGroups', function (Builder $inner) use ($student): void {
                            $inner->where('student_groups.id', $student->student_group_id);
                        });
                    } else {
                        $query->whereHas('studentGroups', function (Builder $inner) use ($student): void {
                            $inner->where('student_groups.id', $student->student_group_id);
                        });
                        $hasCondition = true;
                    }
                }

                if ($hasCondition) {
                    $query->orWhereHas('students', function (Builder $inner) use ($student): void {
                        $inner->where('students.id', $student->id);
                    });
                } else {
                    $query->whereHas('students', function (Builder $inner) use ($student): void {
                        $inner->where('students.id', $student->id);
                    });
                }
            });
    }
}
