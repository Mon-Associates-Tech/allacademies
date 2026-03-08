<?php

namespace App\Livewire\Teachers;

use App\Models\PublicAssignment;
use App\Models\PublicAssignmentQuestion;
use App\Models\PublicAssignmentSubmission;
use App\Models\Teacher;
use App\Services\PublicAssignment\PublicAssignmentGradingService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class GradePublicAssignmentSubmission extends Component
{
    public PublicAssignmentSubmission $submission;

    public PublicAssignment $assignment;

    public array $questionGrades = [];

    public string $overallFeedback = '';

    public int $currentQuestionIndex = 0;

    public bool $showProctoringDetails = false;

    protected ?Teacher $teacher = null;

    protected PublicAssignmentGradingService $gradingService;

    public function boot(PublicAssignmentGradingService $gradingService): void
    {
        $this->gradingService = $gradingService;
    }

    public function mount(PublicAssignmentSubmission $submission): void
    {
        $this->teacher = Teacher::where('user_id', Auth::id())->first();
        $this->submission = $submission;
        $this->assignment = $submission->assignment;

        // Verify ownership
        if (! $this->teacher || $this->assignment->teacher_id !== $this->teacher->id) {
            abort(403, 'Unauthorized access to grade this submission.');
        }

        $this->overallFeedback = $submission->teacher_feedback ?? '';
        $this->initializeQuestionGrades();
    }

    protected function initializeQuestionGrades(): void
    {
        $responses = $this->submission->responses ?? [];
        $questions = $this->assignment->questions;

        foreach ($questions as $question) {
            $questionId = $question->id;
            $response = $responses[$questionId] ?? [];

            $this->questionGrades[$questionId] = [
                'points' => $response['points_earned'] ?? 0,
                'feedback' => $response['manual_feedback'] ?? $response['feedback'] ?? '',
                'is_graded' => $response['manually_graded'] ?? false,
            ];
        }
    }

    public function nextQuestion(): void
    {
        $totalQuestions = $this->assignment->questions->count();
        if ($this->currentQuestionIndex < $totalQuestions - 1) {
            $this->currentQuestionIndex++;
        }
    }

    public function previousQuestion(): void
    {
        if ($this->currentQuestionIndex > 0) {
            $this->currentQuestionIndex--;
        }
    }

    public function goToQuestion(int $index): void
    {
        $totalQuestions = $this->assignment->questions->count();
        if ($index >= 0 && $index < $totalQuestions) {
            $this->currentQuestionIndex = $index;
        }
    }

    public function saveQuestionGrade(int $questionId): void
    {
        $question = PublicAssignmentQuestion::find($questionId);

        if (! $question || $question->public_assignment_id !== $this->assignment->id) {
            session()->flash('error', 'Invalid question.');

            return;
        }

        $gradeData = $this->questionGrades[$questionId] ?? null;

        if (! $gradeData) {
            session()->flash('error', 'Grade data not found.');

            return;
        }

        // Determine points
        $points = (float) ($gradeData['points'] ?? 0);

        // Auto-grade objective types
        if (in_array($question->type, ['multiple_choice', 'true_false'], true)) {
            $response = $this->getResponseForQuestion($questionId);
            $isCorrect = $this->isResponseCorrect($question, $response);
            $points = $isCorrect ? $question->marks : 0;
            $gradeData['feedback'] = $gradeData['feedback'] ?? ($isCorrect ? 'Auto-graded: correct' : 'Auto-graded: incorrect');
        }

        // Basic floor at 0
        $points = max(0, $points);

        try {
            $this->gradingService->manualGradeQuestion(
                $this->submission,
                $questionId,
                $points,
                $gradeData['feedback'] ?? null,
                $this->teacher->user_id ?? Auth::id()
            );

            $this->questionGrades[$questionId]['is_graded'] = true;
            $this->submission->refresh();

            session()->flash('success', 'Question grade saved successfully.');

            // Move to next question after save
            $totalQuestions = $this->assignment->questions->count();
            if ($this->currentQuestionIndex < $totalQuestions - 1) {
                $this->currentQuestionIndex++;
            }
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to save grade: '.$e->getMessage());
        }
    }

    public function saveAllGrades(): void
    {
        $questions = $this->assignment->questions;

        foreach ($questions as $question) {
            $questionId = $question->id;
            $gradeData = $this->questionGrades[$questionId] ?? null;

            if ($gradeData) {
                $points = (float) $gradeData['points'];
                $points = max(0, min($question->marks, $points));

                try {
                    $this->gradingService->manualGradeQuestion(
                        $this->submission,
                        $questionId,
                        $points,
                        $gradeData['feedback'] ?? null,
                        $this->teacher->user_id ?? Auth::id()
                    );
                } catch (\Exception $e) {
                    // Continue with other questions
                }
            }
        }

        $this->submission->refresh();
        $this->initializeQuestionGrades();

        session()->flash('success', 'All grades saved successfully.');
    }

    public function finalizeGrading(): void
    {
        try {
            $this->gradingService->finalizeGrading(
                $this->submission,
                $this->teacher->user ?? Auth::user(),
                $this->overallFeedback
            );

            $this->submission->refresh();

            session()->flash('success', 'Grading finalized successfully.');
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to finalize grading: '.$e->getMessage());
        }
    }

    public function toggleProctoringDetails(): void
    {
        $this->showProctoringDetails = ! $this->showProctoringDetails;
    }

    public function getQuestionsProperty()
    {
        return $this->assignment->questions()->orderBy('order')->get();
    }

    public function getCurrentQuestionProperty()
    {
        return $this->questions[$this->currentQuestionIndex] ?? null;
    }

    public function getResponseForQuestion(int $questionId): ?string
    {
        $responses = $this->submission->responses ?? [];

        return $responses[$questionId]['response'] ?? null;
    }

    protected function isResponseCorrect(PublicAssignmentQuestion $question, mixed $response): bool
    {
        if ($response === null) {
            return false;
        }

        if ($question->type === 'multiple_choice') {
            $normalizedResponse = strtoupper(trim((string) $response));
            $normalizedCorrect = strtoupper(trim((string) $question->correct_answer));
            return $normalizedResponse === $normalizedCorrect;
        }

        if ($question->type === 'true_false') {
            $trueValues = ['true', '1', 'yes', 't'];
            $falseValues = ['false', '0', 'no', 'f'];
            $normalizedResponse = strtolower(trim((string) $response));
            $normalizedCorrect = strtolower(trim((string) $question->correct_answer));
            $responseIsTrue = in_array($normalizedResponse, $trueValues, true);
            $responseIsFalse = in_array($normalizedResponse, $falseValues, true);
            $correctIsTrue = in_array($normalizedCorrect, $trueValues, true);
            return ($responseIsTrue && $correctIsTrue) || ($responseIsFalse && ! $correctIsTrue);
        }

        return false;
    }

    public function getGradingProgressProperty(): array
    {
        $total = $this->assignment->questions->count();
        $graded = 0;

        foreach ($this->questionGrades as $grade) {
            if ($grade['is_graded'] ?? false) {
                $graded++;
            }
        }

        return [
            'total' => $total,
            'graded' => $graded,
            'percentage' => $total > 0 ? round(($graded / $total) * 100) : 0,
        ];
    }

    public function getParticipantInfoProperty(): array
    {
        return [
            'name' => $this->submission->getParticipantName(),
            'email' => $this->submission->getParticipantEmail(),
            'started_at' => $this->submission->started_at?->format('M d, Y H:i'),
            'submitted_at' => $this->submission->submitted_at?->format('M d, Y H:i'),
            'time_spent' => $this->formatTimeSpent($this->submission->time_spent_seconds),
            'attempt_number' => $this->submission->attempt_number,
        ];
    }

    public function getProctoringInfoProperty(): ?array
    {
        $session = $this->submission->proctoringSession;

        if (! $session) {
            return null;
        }

        return [
            'status' => $session->status,
            'is_valid' => $session->is_valid,
            'violations' => $session->getViolationSummary(),
            'duration' => $this->formatTimeSpent($session->getDuration()),
        ];
    }

    protected function formatTimeSpent(?int $seconds): string
    {
        if (! $seconds) {
            return 'N/A';
        }

        $hours = floor($seconds / 3600);
        $minutes = floor(($seconds % 3600) / 60);
        $secs = $seconds % 60;

        if ($hours > 0) {
            return sprintf('%dh %dm %ds', $hours, $minutes, $secs);
        } elseif ($minutes > 0) {
            return sprintf('%dm %ds', $minutes, $secs);
        }

        return sprintf('%ds', $secs);
    }

    public function render()
    {
        return view('livewire.teachers.grade-public-assignment-submission', [
            'questions' => $this->questions,
            'currentQuestion' => $this->currentQuestion,
            'gradingProgress' => $this->gradingProgress,
            'participantInfo' => $this->participantInfo,
            'proctoringInfo' => $this->proctoringInfo,
        ]);
    }
}
