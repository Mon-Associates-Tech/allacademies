<?php

namespace App\Traits;

use App\Models\Assignment;
use App\Models\Assessment;
use App\Services\AssessmentConfigurationService;
use Illuminate\Support\Facades\Log;

trait StartsAssessment
{
    protected AssessmentConfigurationService $assessmentService;

    public function initializeAssessmentService(): void
    {
        if (!isset($this->assessmentService)) {
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
            if (!$this->canStartAssignment($assignment)) {
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
                'student_id' => auth()->user()->student->id
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
                'student_id' => auth()->user()->student->id
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
        if (!empty($config['time_limit_minutes'])) {
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
        if (!$eligibleStudents->contains('id', $student->id)) {
            return false;
        }

        // Check if a student has already submitted
        $existingSubmission = $assignment->submissions()
            ->where('student_id', $student->id)
            ->exists();

        return !$existingSubmission;
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
            ->whereHas('academicGroups', function($query) use ($student) {
                $academicGroupIds = optional($student->academicGroups)?->pluck('id') ?? [];
                $query->whereIn('academic_groups.id', $academicGroupIds);
            })
            ->orWhereHas('academicLevels', function($query) use ($student) {
                $query->where('academic_levels.id', $student->academic_level_id);
            })
            ->orWhereHas('students', function($query) use ($student) {
                $query->where('students.id', $student->id);
            })
            ->with(['academicSubject', 'teacher.user'])
            ->get();
    }
}
