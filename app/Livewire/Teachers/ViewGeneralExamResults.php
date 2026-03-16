<?php

namespace App\Livewire\Teachers;

use App\Models\GeneralExam;
use App\Models\GeneralExamSubmission;
use App\Services\GeneralExam\GeneralExamParticipantVerificationService;
use App\Services\GeneralExam\GeneralExamGradingService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class ViewPublicAssignmentResults extends Component
{
    use WithPagination;

    public GeneralExam $assignment;

    public string $search = '';

    public string $statusFilter = '';

    public string $sortBy = 'submitted_at';

    public string $sortDirection = 'desc';

    public ?int $viewingSubmissionId = null;

    public ?array $submissionDetails = null;

    public ?GeneralExamSubmission $viewingSubmission = null;

    public ?array $gradingSummary = null;

    public bool $showGradingSummary = false;

    protected GeneralExamGradingService $gradingService;

    protected GeneralExamParticipantVerificationService $verificationService;

    protected $queryString = [
        'search' => ['except' => ''],
        'statusFilter' => ['except' => ''],
        'sortBy' => ['except' => 'submitted_at'],
        'sortDirection' => ['except' => 'desc'],
    ];

    public function boot(
        GeneralExamGradingService      $gradingService,
        GeneralExamParticipantVerificationService $verificationService
    ): void {
        $this->gradingService = $gradingService;
        $this->verificationService = $verificationService;
    }

    public function mount(GeneralExam $assignment): void
    {
        // Verify ownership by creator, fallback to legacy teacher link
        $user = Auth::user();
        $ownsByUser = $assignment->user_id === $user->id;
        $ownsByTeacher = $user?->teacher && $assignment->teacher_id === $user->teacher->id;

        if (! ($ownsByUser || $ownsByTeacher)) {
            abort(403, 'Unauthorized access to assignment results.');
        }

        $this->assignment = $assignment;
        $this->loadGradingSummary();
    }

    public function loadGradingSummary(): void
    {
        $this->gradingSummary = $this->gradingService->getGradingSummary($this->assignment);
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingStatusFilter(): void
    {
        $this->resetPage();
    }

    public function sortBy(string $field): void
    {
        if ($this->sortBy === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function viewSubmission(int $submissionId): void
    {
        $submission = GeneralExamSubmission::with(['participant', 'proctoringSession'])
            ->where('general_exam_id', $this->assignment->id)
            ->find($submissionId);

        if (! $submission) {
            session()->flash('error', 'Submission not found.');

            return;
        }

        $this->viewingSubmissionId = $submissionId;
        $this->viewingSubmission = $submission;
        $this->submissionDetails = [
            'submission' => $submission,
            'participant_name' => $submission->getParticipantName(),
            'participant_email' => $submission->getParticipantEmail(),
            'responses' => $submission->responses ?? [],
            'questions' => $this->assignment->questions->keyBy('id')->toArray(),
            'proctoring' => $submission->proctoringSession?->getViolationSummary() ?? null,
        ];
    }

    public function closeSubmissionView(): void
    {
        $this->viewingSubmissionId = null;
        $this->viewingSubmission = null;
        $this->submissionDetails = null;
    }

    public function toggleGradingSummary(): void
    {
        $this->showGradingSummary = ! $this->showGradingSummary;
    }

    public function gradeAllPending(): void
    {
        try {
            $results = $this->gradingService->bulkGradeAssignment($this->assignment);

            session()->flash('success', "Graded {$results['graded']} submissions. {$results['failed']} failed.");

            $this->loadGradingSummary();
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to grade submissions: '.$e->getMessage());
        }
    }

    public function releaseResults(): void
    {
        try {
            $this->assignment->releaseResults();

            // Send notifications to participants
            $notificationResults = $this->verificationService->sendBulkResultNotifications($this->assignment);

            session()->flash('success', "Results released! Notifications sent to {$notificationResults['sent']} participants.");

            $this->assignment->refresh();
            $this->loadGradingSummary();
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to release results: '.$e->getMessage());
        }
    }

    public function resendResultNotifications(): void
    {
        try {
            $notificationResults = $this->verificationService->sendBulkResultNotifications($this->assignment);
            session()->flash('success', "Result emails resent. Sent: {$notificationResults['sent']}, Failed: {$notificationResults['failed']}.");
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to resend result emails: '.$e->getMessage());
        }
    }

    public function exportResults(): void
    {
        // This would typically generate a CSV/Excel file
        // For now, we'll dispatch an event that can be handled by JavaScript
        $this->dispatch('export-results', assignmentId: $this->assignment->id);
    }

    public function getSubmissionsProperty()
    {
        $query = GeneralExamSubmission::where('general_exam_id', $this->assignment->id)
            ->with(['participant', 'proctoringSession']);

        // Search filter - search by participant name/email
        if (! empty($this->search)) {
            $query->where(function ($q) {
                // For GeneralExamParticipant
                $q->whereHasMorph('participant', ['App\Models\GeneralExamParticipant'], function ($query) {
                    $query->where('name', 'like', '%'.$this->search.'%')
                        ->orWhere('email', 'like', '%'.$this->search.'%');
                })
                // For Student
                    ->orWhereHasMorph('participant', ['App\Models\Student'], function ($query) {
                        $query->whereHas('user', function ($q) {
                            $q->where('name', 'like', '%'.$this->search.'%')
                                ->orWhere('email', 'like', '%'.$this->search.'%');
                        });
                    });
            });
        }

        // Status filter
        if (! empty($this->statusFilter)) {
            $query->where('status', $this->statusFilter);
        }

        // Sorting
        $query->orderBy($this->sortBy, $this->sortDirection);

        return $query->paginate(15);
    }

    public function getStatusOptionsProperty(): array
    {
        return [
            '' => 'All Statuses',
            'not_started' => 'Not Started',
            'in_progress' => 'In Progress',
            'submitted' => 'Submitted',
            'auto_graded' => 'Auto Graded',
            'manually_reviewed' => 'Manually Reviewed',
            'final' => 'Final',
        ];
    }

    protected function buildStats(): array
    {
        $subs = $this->assignment->submissions;
        $completedStatuses = [
            GeneralExamSubmission::STATUS_SUBMITTED,
            GeneralExamSubmission::STATUS_AUTO_GRADED,
            GeneralExamSubmission::STATUS_MANUALLY_REVIEWED,
            GeneralExamSubmission::STATUS_FINAL,
        ];

        $total = $subs->count();
        $completed = $subs->whereIn('status', $completedStatuses)->count();
        $inProgress = $subs->where('status', GeneralExamSubmission::STATUS_IN_PROGRESS)->count();
        $average = $subs->whereNotNull('percentage')->avg('percentage') ?? 0;
        $needsGrading = $subs->where('requires_manual_review', true)->count();

        return [
            'total' => $total,
            'completed' => $completed,
            'in_progress' => $inProgress,
            'average_score' => $average,
            'needs_grading' => $needsGrading,
        ];
    }

    public function getSubmissionCountsProperty(): array
    {
        $submissions = $this->assignment->submissions;

        return [
            'total' => $submissions->count(),
            'not_started' => $submissions->where('status', 'not_started')->count(),
            'in_progress' => $submissions->where('status', 'in_progress')->count(),
            'submitted' => $submissions->where('status', 'submitted')->count(),
            'graded' => $submissions->whereIn('status', ['auto_graded', 'manually_reviewed', 'final'])->count(),
            'pending_review' => $submissions->where('requires_manual_review', true)->count(),
        ];
    }

    protected function formatTimeSpent(?int $seconds): string
    {
        if ($seconds === null || $seconds < 0) {
            return '—';
        }
        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds % 3600) / 60);
        $secs = $seconds % 60;

        if ($hours > 0) {
            return sprintf('%dh %02dm', $hours, $minutes);
        }

        if ($minutes > 0) {
            return sprintf('%dm %02ds', $minutes, $secs);
        }

        return sprintf('%ds', $secs);
    }

    public function render()
    {
        return view('livewire.teachers.view-general-exam-results', [
            'submissions' => $this->submissions,
            'statusOptions' => $this->statusOptions,
            'submissionCounts' => $this->submissionCounts,
            'gradingSummary' => $this->gradingSummary,
            'stats' => $this->buildStats(),
        ]);
    }
}
