<?php

namespace App\Livewire\Student;

use App\Models\Assessment;
use App\Models\Assignment;
use App\Services\AssessmentConfigurationService;
use Illuminate\Support\Facades\Log;

trait StartsAssessment
{
    protected AssessmentConfigurationService $assessmentService;

    public function initializeAssessmentService(): void
    {
        if (! isset($this->assessmentService)) {
            $this->assessmentService = app(AssessmentConfigurationService::class);
        }
    }

    /**
     * Start assessment from assignment
     */
    public function startAssessmentFromAssignment(Assignment $assignment): void
    {
        $this->initializeAssessmentService();

        try {
            // Validate assignment eligibility
            if (! $this->canStartAssignment($assignment)) {
                session()->flash('error', 'You are not eligible to start this assignment or it is not available.');

                return;
            }

            // Create assessment from assignment
            $assessment = $this->assessmentService->createFromAssignment($assignment, auth()->user()->student);

            // Configure component for assessment
            $this->configureFromAssignment($assignment, $assessment);

            // Move to assessment step
            $this->step = 'assessment';

            session()->flash('success', 'Assessment started successfully!');

        } catch (\Exception $e) {
            Log::error('Failed to start assessment from assignment', [
                'assignment_id' => $assignment->id,
                'error' => $e->getMessage(),
                'student_id' => auth()->user()->student->id,
            ]);

            session()->flash('error', 'Failed to start assessment. Please try again.');
        }
    }

    /**
     * Start assessment from custom configuration
     */
    public function startSelfAssessment(array $config): void
    {
        $this->initializeAssessmentService();

        try {
            // Create assessment from configuration
            $assessment = $this->assessmentService->createFromConfiguration($config, auth()->user()->student);

            // Configure component for assessment
            $this->configureFromSelfAssessment($config, $assessment);

            // Move to assessment step
            $this->step = 'assessment';

            session()->flash('success', 'Self-assessment started successfully!');

        } catch (\Exception $e) {
            Log::error('Failed to start self-assessment', [
                'config' => $config,
                'error' => $e->getMessage(),
                'student_id' => auth()->user()->student->id,
            ]);

            session()->flash('error', 'Failed to start assessment. Please try again.');
        }
    }

    /**
     * Configure component from assignment
     */
    private function configureFromAssignment(Assignment $assignment, Assessment $assessment): void
    {
        $this->assessment = $assessment;
        $this->questions = [];
        $this->responses = [];
        $this->currentQuestionIndex = 0;

        // Set time limit if specified
        if ($assignment->duration_in_minutes) {
            $this->timeLimitSeconds = $assignment->duration_in_minutes * 60;
            $this->timeRemaining = $this->timeLimitSeconds;
        }

        // Generate and prepare questions
        $this->prepareQuestionsFromAssignment($assignment);
    }

    /**
     * Configure component from self-assessment
     */
    private function configureFromSelfAssessment(array $config, Assessment $assessment): void
    {
        $this->assessment = $assessment;
        $this->questions = [];
        $this->responses = [];
        $this->currentQuestionIndex = 0;

        // Set time limit if specified
        if (! empty($config['time_limit_minutes'])) {
            $this->timeLimitSeconds = $config['time_limit_minutes'] * 60;
            $this->timeRemaining = $this->timeLimitSeconds;
        }

        // Generate and prepare questions
        $this->prepareQuestionsFromConfiguration($config);
    }

    /**
     * Prepare questions from assignment
     */
    private function prepareQuestionsFromAssignment(Assignment $assignment): void
    {
        $this->questions = [];

        foreach ($assignment->assignmentSections as $section) {
            $sectionQuestions = $this->assessmentService->generateQuestionsForSection($section, $this->assessment);
            $this->questions = array_merge($this->questions, $sectionQuestions->toArray());
        }

        // Initialize responses array
        $this->responses = array_fill(0, count($this->questions), null);
    }

    /**
     * Prepare questions from configuration
     */
    private function prepareQuestionsFromConfiguration(array $config): void
    {
        $generatedQuestions = $this->assessmentService->generateQuestionsFromConfiguration($config, $this->assessment);
        $this->questions = $generatedQuestions->toArray();

        // Initialize responses array
        $this->responses = array_fill(0, count($this->questions), null);
    }

    /**
     * Check if student can start assignment
     */
    private function canStartAssignment(Assignment $assignment): bool
    {
        $student = auth()->user()->student;

        // Check if assignment is published and active
        if ($assignment->status !== 'published') {
            return false;
        }

        // Check time constraints
        if ($assignment->starts_at > now() || $assignment->ends_at < now()) {
            return false;
        }

        // Check if a student is eligible
        $eligibleStudents = $assignment->getEligibleStudents();
        if (! $eligibleStudents->contains('id', $student->id)) {
            return false;
        }

        // Check if a student has already submitted
        $existingSubmission = $assignment->submissions()
            ->where('student_id', $student->id)
            ->exists();

        return ! $existingSubmission;
    }

    /**
     * Get available assignments for a student
     */
    public function getAvailableAssignments()
    {
        $student = auth()->user()->student;

        return Assignment::where('status', 'published')
            ->where('starts_at', '<=', now())
            ->where('ends_at', '>', now())
            ->whereHas('academicGroups', function ($query) use ($student) {
                $academicGroupIds = optional($student->academicGroups)?->pluck('id') ?? [];
                $query->whereIn('academic_groups.id', $academicGroupIds);
            })
            ->orWhereHas('academicLevels', function ($query) use ($student) {
                $query->where('academic_levels.id', $student->academic_level_id);
            })
            ->orWhereHas('students', function ($query) use ($student) {
                $query->where('students.id', $student->id);
            })
            ->with(['academicSubject', 'teacher.user'])
            ->get();
    }

    public function submitAssessment(): void
    {
        if (! $this->assessment) {
            session()->flash('error', 'No active assessment found.');

            return;
        }

        try {
            // Calculate time taken
            $timeTaken = $this->startTime ? now()->diffInMinutes($this->startTime) : 0;

            // Process responses and create assessment response data
            $processedData = $this->processAssessmentResponses();

            // Create assessment response
            $assessmentResponse = $this->assessment->assessmentResponse()->create([
                'data' => $processedData,
            ]);

            // Update assessment status
            $this->assessment->update([
                'end_time' => now(),
                'status' => $this->assessment->canAutoGrade() ? Assessment::STATUS_COMPLETED : Assessment::STATUS_PENDING_REVIEW,
            ]);

            // Update scores from response
            $this->assessment->updateScoreFromResponse();

            // Set results for display
            $this->assessmentResult = $assessmentResponse;
            $this->step = 'results';

            // Log assessment completion
            activity()->performedOn($this->assessment)
                ->causedBy(auth()->user())
                ->withProperties([
                    'action' => 'completed_assessment',
                    'assessment_id' => $this->assessment->id,
                    'time_taken' => $timeTaken,
                    'score' => $processedData['total_score'],
                    'max_score' => $processedData['max_score'],
                    'percentage' => $processedData['percentage'],
                ])
                ->log('Student completed assessment');

            session()->flash('success', 'Assessment submitted successfully!');

        } catch (\Exception $e) {
            Log::error('Failed to submit assessment', [
                'assessment_id' => $this->assessment->id,
                'student_id' => auth()->user()->student->id,
                'error' => $e->getMessage(),
                'responses' => $this->responses,
            ]);

            session()->flash('error', 'Failed to submit assessment. Please try again.');
        }
    }

    private function processAssessmentResponses(): array
    {
        $processedQuestions = [];
        $totalScore = 0;
        $maxScore = 0;
        $correctAnswers = 0;
        $answeredQuestions = 0;

        foreach ($this->questions as $index => $question) {
            $questionModel = $question['model'];
            $questionType = $question['type'];
            $response = $this->responses[$index] ?? [];

            // Get user's answer based on question type
            $userAnswer = $this->extractUserAnswer($questionType, $response);
            $correctAnswer = $this->getCorrectAnswer($questionModel, $questionType);

            // Check if question was answered
            $isAnswered = ! empty($userAnswer);
            if ($isAnswered) {
                $answeredQuestions++;
            }

            // Auto-grade non-essay questions
            $isCorrect = false;
            $pointsEarned = 0;
            $isGraded = true;

            if ($questionType === 'essay_question') {
                // Essay questions need manual grading
                $isGraded = false;
            } else {
                // Auto-grade multiple choice and true/false
                $isCorrect = $this->isAnswerCorrect($questionType, $userAnswer, $correctAnswer);
                $pointsEarned = $isCorrect ? ($question['points'] ?? 1) : 0;

                if ($isCorrect) {
                    $correctAnswers++;
                }
            }

            $pointsPossible = $question['points'] ?? 1;
            $totalScore += $pointsEarned;
            $maxScore += $pointsPossible;

            // Format question data for storage
            $processedQuestions[] = [
                'question_id' => $question['id'] ?? $index,
                'questionable_id' => $questionModel->id,
                'questionable_type' => get_class($questionModel),
                'type' => $questionType,
                'question' => $this->getQuestionText($questionModel, $questionType),
                'options' => $this->getQuestionOptions($questionModel, $questionType),
                'student_answer' => $userAnswer,
                'correct_answer' => $correctAnswer,
                'is_correct' => $isCorrect,
                'points_possible' => $pointsPossible,
                'points_earned' => $pointsEarned,
                'response_time' => 0, // Could be calculated if tracking per-question time
                'is_graded' => $isGraded,
                'teacher_feedback' => null,
                'graded_by' => null,
                'graded_at' => null,
            ];
        }

        $percentage = $maxScore > 0 ? ($totalScore / $maxScore) * 100 : 0;

        return [
            'questions' => $processedQuestions,
            'total_questions' => count($this->questions),
            'answered_questions' => $answeredQuestions,
            'correct_answers' => $correctAnswers,
            'total_score' => $totalScore,
            'max_score' => $maxScore,
            'percentage' => round($percentage, 2),
            'time_taken' => $this->startTime ? now()->diffInMinutes($this->startTime) : 0,
            'subject_id' => $this->assessment->subject_id,
            'topic_id' => $this->assessment->topic_id,
            'subtopic_id' => $this->assessment->subtopic_id,
        ];
    }

    private function extractUserAnswer(string $questionType, array $response): ?string
    {
        switch ($questionType) {
            case 'multiple_choice_question':
            case 'true_or_false_question':
                return $response['selected_option'] ?? null;
            case 'essay_question':
                return $response['essay_answer'] ?? null;
            default:
                return null;
        }
    }

    private function getCorrectAnswer($questionModel, string $questionType): ?string
    {
        switch ($questionType) {
            case 'multiple_choice_question':
            case 'true_or_false_question':
                return $questionModel->answer ?? null;
                // Essays don't have a single correct answer
            default:
                return null;
        }
    }

    private function isAnswerCorrect(string $questionType, ?string $userAnswer, ?string $correctAnswer): bool
    {
        if (! $userAnswer || ! $correctAnswer) {
            return false;
        }

        return strtolower(trim($userAnswer)) === strtolower(trim($correctAnswer));
    }

    private function getQuestionText($questionModel, string $questionType): string
    {
        if ($questionType === 'multiple_choice_question') {
            $question = $questionModel->question;
            if (is_array($question)) {
                return $question['down'] ?? $question['up'] ?? '';
            }

            return $question?->down ?? $question?->up ?? '';
        }

        return $questionModel->question?->down ?? '';
    }

    private function getQuestionOptions($questionModel, string $questionType): array
    {
        if ($questionType !== 'multiple_choice_question') {
            return [];
        }

        $options = [];
        $optionFields = ['option_a', 'option_b', 'option_c', 'option_d', 'option_e'];

        foreach ($optionFields as $field) {
            if (isset($questionModel->$field) && $questionModel->$field) {
                $optionValue = $questionModel->$field;
                $letter = substr($field, -1);

                if (is_array($optionValue)) {
                    $text = $optionValue['down'] ?? $optionValue['up'] ?? '';
                } else {
                    $text = $optionValue;
                }

                if ($text) {
                    $options[] = [
                        'label' => strtoupper($letter),
                        'value' => $text,
                        'is_correct' => strtolower($letter) === strtolower($questionModel->answer ?? ''),
                    ];
                }
            }
        }

        return $options;
    }

    public function nextQuestion(): void
    {
        if ($this->currentQuestionIndex < count($this->questions) - 1) {
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
        if ($index >= 0 && $index < count($this->questions)) {
            $this->currentQuestionIndex = $index;
        }
    }

    public function timeUp(): void
    {
        session()->flash('warning', 'Time is up! Your assessment has been submitted automatically.');
        $this->submitAssessment();
    }
}
