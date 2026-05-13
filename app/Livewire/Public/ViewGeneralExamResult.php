<?php

namespace App\Livewire\Public;

use App\ExaminationHub\Models\GeneralExam;
use App\ExaminationHub\Models\GeneralExamParticipant;
use App\ExaminationHub\Models\GeneralExamSubmission;
use App\Models\Student;
use App\Services\GeneralExam\GeneralExamParticipantVerificationService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class ViewGeneralExamResult extends Component
{
    public ?GeneralExamSubmission $submission = null;

    public ?GeneralExam $assignment = null;

    public bool $resultsAvailable = false;

    public string $unavailableReason = '';

    public ?int $viewingQuestionIndex = null;

    protected GeneralExamParticipantVerificationService $verificationService;

    public function boot(GeneralExamParticipantVerificationService $verificationService): void
    {
        $this->verificationService = $verificationService;
    }

    public function mount($token = null, $submissionId = null): void
    {
        // Access via result token (for guest participants)
        if ($token) {
            $this->loadByToken($token);
        }
        // Access via submission ID (for authenticated users)
        elseif ($submissionId) {
            $this->loadBySubmissionId($submissionId);
        } else {
            $this->unavailableReason = 'No result identifier provided.';
        }
    }

    protected function loadByToken(string $token): void
    {
        $participant = $this->verificationService->validateResultToken($token);

        if (! $participant) {
            $this->unavailableReason = 'Invalid or expired result token.';

            return;
        }

        // Get the most recent submission for this participant
        $this->submission = $participant->submissions()
            ->whereNotNull('submitted_at')
            ->orderBy('submitted_at', 'desc')
            ->first();

        if (! $this->submission) {
            $this->unavailableReason = 'No completed submissions found.';

            return;
        }

        $this->assignment = $this->submission->assignment;
        $this->checkResultsAvailability();
    }

    protected function loadBySubmissionId(int $submissionId): void
    {
        $this->submission = GeneralExamSubmission::find($submissionId);

        if (! $this->submission) {
            $this->unavailableReason = 'Submission not found.';

            return;
        }

        // Verify access - must be the participant or a student
        if (! $this->canAccessSubmission()) {
            $this->unavailableReason = 'You do not have permission to view this result.';
            $this->submission = null;

            return;
        }

        $this->assignment = $this->submission->assignment;
        $this->checkResultsAvailability();
    }

    protected function canAccessSubmission(): bool
    {
        if (! $this->submission) {
            return false;
        }

        // Check if authenticated user is the participant
        if (Auth::check()) {
            // If participant is a student
            if ($this->submission->participant_type === Student::class) {
                $student = Student::where('user_id', Auth::id())->first();

                return $student && $student->id === $this->submission->participant_id;
            }

            // If participant is linked to a user
            if ($this->submission->participant_type === GeneralExamParticipant::class) {
                $participant = GeneralExamParticipant::find($this->submission->participant_id);

                return $participant && $participant->user_id === Auth::id();
            }
        }

        return false;
    }

    protected function checkResultsAvailability(): void
    {
        if (! $this->submission || ! $this->assignment) {
            $this->resultsAvailable = false;

            return;
        }

        if (! $this->submission->isSubmitted()) {
            $this->unavailableReason = 'This submission has not been completed yet.';
            $this->resultsAvailable = false;

            return;
        }

        if (! $this->assignment->canShowResults()) {
            $this->unavailableReason = match ($this->assignment->result_visibility) {
                'after_due_date' => 'Results will be available after '.$this->assignment->ends_at?->format('M d, Y H:i'),
                'manual_release' => 'Results have not been released yet. Please check back later.',
                default => 'Results are not available at this time.',
            };
            $this->resultsAvailable = false;

            return;
        }

        $this->resultsAvailable = true;
    }

    public function viewQuestion(int $index): void
    {
        $this->viewingQuestionIndex = $index;
    }

    public function closeQuestionView(): void
    {
        $this->viewingQuestionIndex = null;
    }

    public function getScoreSummaryProperty(): ?array
    {
        if (! $this->submission || ! $this->resultsAvailable) {
            return null;
        }

        return [
            'score' => $this->submission->score ?? 0,
            'total_marks' => $this->submission->total_marks ?? 0,
            'percentage' => $this->submission->percentage ?? 0,
            'grade' => $this->submission->grade ?? 'N/A',
            'status' => $this->submission->status,
            'submitted_at' => $this->submission->submitted_at?->format('M d, Y H:i'),
            'time_spent' => $this->formatTimeSpent($this->submission->time_spent_seconds),
            'teacher_feedback' => $this->submission->teacher_feedback,
        ];
    }

    public function getQuestionsWithResponsesProperty(): array
    {
        if (! $this->submission || ! $this->assignment || ! $this->resultsAvailable) {
            return [];
        }

        $questions = $this->assignment->questions()->orderBy('order')->get();
        $responses = $this->submission->responses ?? [];
        $showCorrectAnswers = $this->assignment->show_correct_answers;
        $showScoreBreakdown = $this->assignment->show_score_breakdown;

        $result = [];

        foreach ($questions as $index => $question) {
            $response = $responses[$question->id] ?? [];

            $questionData = [
                'index' => $index + 1,
                'id' => $question->id,
                'type' => $question->type,
                'question' => $question->question,
                'options' => $question->options,
                'marks' => $question->marks,
                'participant_response' => $response['response'] ?? null,
                'answered' => isset($response['response']) && $response['response'] !== null && $response['response'] !== '',
            ];

            if ($showScoreBreakdown) {
                $questionData['is_correct'] = $response['is_correct'] ?? null;
                $questionData['points_earned'] = $response['points_earned'] ?? 0;
                $questionData['feedback'] = $response['feedback'] ?? $response['manual_feedback'] ?? null;
            }

            if ($showCorrectAnswers) {
                $questionData['correct_answer'] = $question->correct_answer;
                $questionData['explanation'] = $question->explanation;
            }

            $result[] = $questionData;
        }

        return $result;
    }

    public function getStatisticsProperty(): ?array
    {
        if (! $this->submission || ! $this->resultsAvailable) {
            return null;
        }

        $questions = $this->questionsWithResponses;
        $totalQuestions = count($questions);
        $answered = 0;
        $correct = 0;
        $incorrect = 0;
        $partial = 0;

        foreach ($questions as $q) {
            if ($q['answered']) {
                $answered++;
            }

            if (isset($q['is_correct'])) {
                if ($q['is_correct'] === true) {
                    $correct++;
                } elseif ($q['is_correct'] === false) {
                    $incorrect++;
                } else {
                    $partial++;
                }
            }
        }

        return [
            'total_questions' => $totalQuestions,
            'answered' => $answered,
            'unanswered' => $totalQuestions - $answered,
            'correct' => $correct,
            'incorrect' => $incorrect,
            'partial' => $partial,
        ];
    }

    public function getGradeColorProperty(): string
    {
        $percentage = $this->submission?->percentage ?? 0;

        return match (true) {
            $percentage >= 90 => 'text-green-600',
            $percentage >= 70 => 'text-blue-600',
            $percentage >= 50 => 'text-yellow-600',
            default => 'text-red-600',
        };
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
        return view('livewire.public.view-general-exam-result', [
            'scoreSummary' => $this->scoreSummary,
            'questionsWithResponses' => $this->questionsWithResponses,
            'statistics' => $this->statistics,
            'gradeColor' => $this->gradeColor,
        ])->layout('layouts.general-exam');
    }
}
