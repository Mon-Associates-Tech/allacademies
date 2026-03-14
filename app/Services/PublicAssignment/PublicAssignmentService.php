<?php

namespace App\Services\PublicAssignment;

use App\Models\ProctoringSession;
use App\Models\PublicAssignment;
use App\Models\PublicAssignmentParticipant;
use App\Models\PublicAssignmentQuestion;
use App\Models\PublicAssignmentSection;
use App\Models\PublicAssignmentSubmission;
use App\Models\Student;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class PublicAssignmentService
{
    /**
     * Create a new public assignment
     */
    public function createAssignment(User $user, array $data): PublicAssignment
    {
        return DB::transaction(function () use ($user, $data) {
            $assignment = PublicAssignment::create([
                'title' => $data['title'],
                'description' => $data['description'] ?? null,
                'user_id' => $user->id,
                'teacher_id' => $user->teacher->id ?? null,
                'school_id' => getSchoolId() ?? $user->school_id,
                'type' => $data['type'] ?? 'quiz',
                'duration_in_minutes' => $data['duration_in_minutes'] ?? null,
                'starts_at' => $data['starts_at'] ?? null,
                'ends_at' => $data['ends_at'] ?? null,
                'is_randomized' => $data['is_randomized'] ?? false,
                'instructions' => $data['instructions'] ?? null,
                'total_marks' => $data['total_marks'] ?? 0,
                'result_visibility' => $data['result_visibility'] ?? 'immediate',
                'show_correct_answers' => $data['show_correct_answers'] ?? true,
                'show_score_breakdown' => $data['show_score_breakdown'] ?? true,
                'proctoring_enabled' => $data['proctoring_enabled'] ?? true,
                'restrict_navigation' => $data['restrict_navigation'] ?? true,
                'max_tab_switches' => $data['max_tab_switches'] ?? 3,
                'auto_submit_on_violation' => $data['auto_submit_on_violation'] ?? true,
                'require_webcam' => $data['require_webcam'] ?? false,
                'require_fullscreen' => $data['require_fullscreen'] ?? true,
                'status' => 'draft',
                'max_attempts' => $data['max_attempts'] ?? 1,
            ]);

            // Create sections if provided
            if (! empty($data['sections'])) {
                $this->createSections($assignment, $data['sections']);
            }

            // Create questions if provided (without sections)
            if (! empty($data['questions'])) {
                $this->createQuestions($assignment, null, $data['questions']);
            }

            $assignment->recalculateTotalMarks();

            return $assignment;
        });
    }

    /**
     * Create sections for an assignment
     */
    public function createSections(PublicAssignment $assignment, array $sections): void
    {
        foreach ($sections as $index => $sectionData) {
            $section = PublicAssignmentSection::create([
                'public_assignment_id' => $assignment->id,
                'title' => $sectionData['title'],
                'description' => $sectionData['description'] ?? null,
                'instructions' => $sectionData['instructions'] ?? null,
                'order' => $sectionData['order'] ?? $index,
                'time_limit_minutes' => $sectionData['time_limit_minutes'] ?? null,
                'is_randomized' => $sectionData['is_randomized'] ?? false,
            ]);

            if (! empty($sectionData['questions'])) {
                $this->createQuestions($assignment, $section, $sectionData['questions']);
            }

            $section->recalculateTotalMarks();
        }
    }

    /**
     * Create questions for an assignment or section
     */
    public function createQuestions(PublicAssignment $assignment, ?PublicAssignmentSection $section, array $questions): void
    {
        foreach ($questions as $index => $questionData) {
            PublicAssignmentQuestion::create([
                'public_assignment_id' => $assignment->id,
                'public_assignment_section_id' => $section?->id,
                'type' => $questionData['type'],
                'question' => $questionData['question'],
                'explanation' => $questionData['explanation'] ?? null,
                'options' => $questionData['options'] ?? null,
                'correct_answer' => $questionData['correct_answer'] ?? null,
                'grading_rubric' => $questionData['grading_rubric'] ?? null,
                'keywords' => $questionData['keywords'] ?? null,
                'marks' => $questionData['marks'] ?? 1,
                'difficulty' => $questionData['difficulty'] ?? 'medium',
                'order' => $questionData['order'] ?? $index,
                'ai_generated' => $questionData['ai_generated'] ?? false,
            ]);
        }
    }

    /**
     * Publish an assignment
     */
    public function publishAssignment(PublicAssignment $assignment): PublicAssignment
    {
        $assignment->update(['status' => 'published']);

        return $assignment->fresh();
    }

    /**
     * Close an assignment
     */
    public function closeAssignment(PublicAssignment $assignment): PublicAssignment
    {
        $assignment->update(['status' => 'closed']);

        return $assignment->fresh();
    }

    /**
     * Find assignment by access code
     */
    public function findByAccessCode(string $code): ?PublicAssignment
    {
        return PublicAssignment::findByAccessCode($code);
    }

    /**
     * Validate access code and check if assignment is accessible
     */
    public function validateAccessCode(string $code): array
    {
        $assignment = $this->findByAccessCode($code);

        if (! $assignment) {
            return [
                'valid' => false,
                'error' => 'Invalid access code. Please check and try again.',
            ];
        }

        if ($assignment->status !== 'published') {
            return [
                'valid' => false,
                'error' => 'This assignment is not currently available.',
            ];
        }

        if ($assignment->starts_at && now()->lt($assignment->starts_at)) {
            return [
                'valid' => false,
                'error' => 'This assignment has not started yet. It will be available on '.$assignment->starts_at->format('M d, Y \a\t h:i A'),
            ];
        }

        if ($assignment->ends_at && now()->gt($assignment->ends_at)) {
            return [
                'valid' => false,
                'error' => 'This assignment has ended.',
            ];
        }

        return [
            'valid' => true,
            'assignment' => $assignment,
        ];
    }

    /**
     * Register or find a participant by email
     */
    public function registerParticipant(array $data): PublicAssignmentParticipant
    {
        $participant = PublicAssignmentParticipant::where('email', strtolower($data['email']))->first();

        if ($participant) {
            // Update name if different
            if ($participant->name !== $data['name']) {
                $participant->update(['name' => $data['name']]);
            }

            return $participant;
        }

        return PublicAssignmentParticipant::create([
            'name' => $data['name'],
            'email' => strtolower($data['email']),
            'phone' => $data['phone'] ?? null,
            'ip_address' => $data['ip_address'] ?? null,
            'user_agent' => $data['user_agent'] ?? null,
        ]);
    }

    /**
     * Check if a student can take the assignment
     */
    public function canStudentTakeAssignment(PublicAssignment $assignment, Student $student): array
    {
        if (! $assignment->isActive()) {
            return ['can_take' => false, 'reason' => 'Assignment is not active'];
        }

        $participantType = Student::class;
        $participantId = $student->id;

        if ($assignment->hasParticipantSubmitted($participantType, $participantId)) {
            $attemptCount = $assignment->getParticipantAttemptCount($participantType, $participantId);
            if ($attemptCount >= $assignment->max_attempts) {
                return ['can_take' => false, 'reason' => 'Maximum attempts reached'];
            }
        }

        return ['can_take' => true];
    }

    /**
     * Check if a participant can take the assignment
     */
    public function canParticipantTakeAssignment(PublicAssignment $assignment, PublicAssignmentParticipant $participant): array
    {
        if (! $assignment->isActive()) {
            return ['can_take' => false, 'reason' => 'Assignment is not active'];
        }

        if (! $participant->isEmailVerified()) {
            return ['can_take' => false, 'reason' => 'Email not verified'];
        }

        $participantType = PublicAssignmentParticipant::class;
        $participantId = $participant->id;

        if ($assignment->hasParticipantSubmitted($participantType, $participantId)) {
            $attemptCount = $assignment->getParticipantAttemptCount($participantType, $participantId);
            if ($attemptCount >= $assignment->max_attempts) {
                return ['can_take' => false, 'reason' => 'Maximum attempts reached'];
            }
        }

        return ['can_take' => true];
    }

    /**
     * Start a submission for a student
     */
    public function startStudentSubmission(PublicAssignment $assignment, Student $student, array $metadata = []): PublicAssignmentSubmission
    {
        return $this->createSubmission($assignment, Student::class, $student->id, $metadata);
    }

    /**
     * Start a submission for a participant
     */
    public function startParticipantSubmission(PublicAssignment $assignment, PublicAssignmentParticipant $participant, array $metadata = []): PublicAssignmentSubmission
    {
        return $this->createSubmission($assignment, PublicAssignmentParticipant::class, $participant->id, $metadata);
    }

    /**
     * Create a submission
     */
    protected function createSubmission(PublicAssignment $assignment, string $participantType, int $participantId, array $metadata = []): PublicAssignmentSubmission
    {
        return DB::transaction(function () use ($assignment, $participantType, $participantId, $metadata) {
            $attemptNumber = $assignment->getParticipantAttemptCount($participantType, $participantId) + 1;

            $submission = PublicAssignmentSubmission::create([
                'public_assignment_id' => $assignment->id,
                'participant_type' => $participantType,
                'participant_id' => $participantId,
                'attempt_number' => $attemptNumber,
                'ip_address' => $metadata['ip_address'] ?? null,
                'user_agent' => $metadata['user_agent'] ?? null,
                'status' => PublicAssignmentSubmission::STATUS_NOT_STARTED,
            ]);

            // Create proctoring session if enabled
            if ($assignment->proctoring_enabled) {
                ProctoringSession::create([
                    'public_assignment_submission_id' => $submission->id,
                    'status' => ProctoringSession::STATUS_PENDING,
                ]);
            }

            return $submission;
        });
    }

    /**
     * Get or create an in-progress submission
     */
    public function getOrCreateSubmission(PublicAssignment $assignment, string $participantType, int $participantId, array $metadata = []): PublicAssignmentSubmission
    {
        // Check for existing in-progress submission
        $existingSubmission = PublicAssignmentSubmission::where('public_assignment_id', $assignment->id)
            ->where('participant_type', $participantType)
            ->where('participant_id', $participantId)
            ->whereIn('status', [
                PublicAssignmentSubmission::STATUS_NOT_STARTED,
                PublicAssignmentSubmission::STATUS_IN_PROGRESS,
            ])
            ->first();

        if ($existingSubmission) {
            return $existingSubmission;
        }

        return $this->createSubmission($assignment, $participantType, $participantId, $metadata);
    }

    /**
     * Get questions for display (with optional randomization)
     */
    public function getQuestionsForSubmission(PublicAssignment $assignment): array
    {
        $questions = [];

        if ($assignment->sections->isNotEmpty()) {
            foreach ($assignment->sections as $section) {
                $sectionQuestions = $section->getQuestionsForDisplay();
                $questions[$section->id] = [
                    'section' => $section,
                    'questions' => $sectionQuestions,
                ];
            }
        } else {
            $allQuestions = $assignment->questions;
            if ($assignment->is_randomized) {
                $allQuestions = $allQuestions->shuffle();
            }
            $questions['default'] = [
                'section' => null,
                'questions' => $allQuestions,
            ];
        }

        return $questions;
    }

    /**
     * Get assignment statistics
     */
    public function getAssignmentStatistics(PublicAssignment $assignment): array
    {
        $submissions = $assignment->submissions;
        $completedSubmissions = $submissions->where('status', '!=', PublicAssignmentSubmission::STATUS_NOT_STARTED)
            ->where('status', '!=', PublicAssignmentSubmission::STATUS_IN_PROGRESS);

        $gradedSubmissions = $submissions->whereIn('status', [
            PublicAssignmentSubmission::STATUS_AUTO_GRADED,
            PublicAssignmentSubmission::STATUS_MANUALLY_REVIEWED,
            PublicAssignmentSubmission::STATUS_FINAL,
        ]);

        return [
            'total_submissions' => $submissions->count(),
            'completed_submissions' => $completedSubmissions->count(),
            'in_progress' => $submissions->where('status', PublicAssignmentSubmission::STATUS_IN_PROGRESS)->count(),
            'graded' => $gradedSubmissions->count(),
            'pending_review' => $submissions->where('requires_manual_review', true)->count(),
            'average_score' => $gradedSubmissions->avg('percentage') ?? 0,
            'highest_score' => $gradedSubmissions->max('percentage') ?? 0,
            'lowest_score' => $gradedSubmissions->min('percentage') ?? 0,
            'average_time_spent' => $completedSubmissions->avg('time_spent_seconds') ?? 0,
        ];
    }

    /**
     * Duplicate an assignment
     */
    public function duplicateAssignment(PublicAssignment $assignment, ?string $newTitle = null): PublicAssignment
    {
        return DB::transaction(function () use ($assignment, $newTitle) {
            $newAssignment = $assignment->replicate([
                'access_code',
                'status',
                'results_released',
                'results_released_at',
            ]);

            $newAssignment->title = $newTitle ?? $assignment->title.' (Copy)';
            $newAssignment->status = 'draft';
            $newAssignment->results_released = false;
            $newAssignment->user_id = $assignment->user_id ?? $assignment->teacher?->user_id;
            $newAssignment->save();

            // Duplicate sections and questions
            foreach ($assignment->sections as $section) {
                $newSection = $section->replicate(['public_assignment_id']);
                $newSection->public_assignment_id = $newAssignment->id;
                $newSection->save();

                foreach ($section->questions as $question) {
                    $newQuestion = $question->replicate(['public_assignment_id', 'public_assignment_section_id']);
                    $newQuestion->public_assignment_id = $newAssignment->id;
                    $newQuestion->public_assignment_section_id = $newSection->id;
                    $newQuestion->save();
                }
            }

            // Duplicate questions without sections
            foreach ($assignment->questions()->whereNull('public_assignment_section_id')->get() as $question) {
                $newQuestion = $question->replicate(['public_assignment_id']);
                $newQuestion->public_assignment_id = $newAssignment->id;
                $newQuestion->save();
            }

            return $newAssignment;
        });
    }
}
