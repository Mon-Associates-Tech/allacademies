<?php

namespace App\Services;

use App\Models\Assignment;
use App\Models\Assessment;
use App\Models\AssignmentSection;
use App\Models\Question;
use App\Models\MultipleChoiceQuestion;
use App\Models\TrueOrFalseQuestion;
use App\Models\EssayQuestion;
use App\Models\Student;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class AssessmentConfigurationService
{
    /**
     * Create assessment from assignment configuration
     */
    public function createFromAssignment(Assignment $assignment, Student $student): Assessment
    {
        $assessment = Assessment::create([
            'student_id' => $student->id,
            'subject_id' => $assignment->academic_subject_id,
            'title' => $assignment->title,
            'max_score' => $assignment->total_marks,
            'start_time' => now(),
            'status' => 'in_progress',
        ]);

        // Generate questions based on assignment sections
        $questions = $this->generateQuestionsFromAssignment($assignment, $assessment);

        // Log assessment creation
        activity()->performedOn($assessment)
            ->causedBy($student->user)
            ->withProperties([
                'action' => 'created_assessment_from_assignment',
                'assignment_id' => $assignment->id,
                'assignment_title' => $assignment->title,
                'total_questions' => $questions->count()
            ])
            ->log('Student created assessment from assignment');

        return $assessment;
    }

    /**
     * Create assessment from custom configuration (self-assessment)
     */
    public function createFromConfiguration(array $config, Student $student): Assessment
    {
        $assessment = Assessment::create([
            'student_id' => $student->id,
            'subject_id' => $config['subject_id'] ?? null,
            'topic_id' => $config['topic_id'] ?? null,
            'subtopic_id' => $config['subtopic_id'] ?? null,
            'title' => $config['title'] ?? 'Self Assessment',
            'max_score' => $config['question_count'] * ($config['marks_per_question'] ?? 1),
            'start_time' => now(),
            'status' => 'in_progress',
        ]);

        // Generate questions based on configuration
        $questions = $this->generateQuestionsFromConfiguration($config, $assessment);

        // Log assessment creation
        activity()->performedOn($assessment)
            ->causedBy($student->user)
            ->withProperties([
                'action' => 'created_self_assessment',
                'configuration' => $config,
                'total_questions' => $questions->count()
            ])
            ->log('Student created self assessment');

        return $assessment;
    }

    /**
     * Generate questions from assignment sections
     */
    public function generateQuestionsFromAssignment(Assignment $assignment, Assessment $assessment): Collection
    {
        $questions = collect();

        foreach ($assignment->assignmentSections as $section) {
            $sectionQuestions = $this->generateQuestionsForSection($section, $assessment);
            $questions = $questions->merge($sectionQuestions);
        }

        return $questions;
    }

    /**
     * Generate questions for a specific assignment section
     */
    public function generateQuestionsForSection(AssignmentSection $section, Assessment $assessment): Collection
    {
        $questions = collect();
        $questionType = $this->mapQuestionType($section->question_type);

        if (!$questionType) {
            Log::warning("Unknown question type: {$section->question_type}");
            return $questions;
        }

        // Build query for questions
        $query = $questionType::query();

        // Apply filters from assignment
        $this->applyAssignmentFilters($query, $assessment);

        // Get available questions
        $availableQuestions = $query->get();

        if ($availableQuestions->isEmpty()) {
            Log::warning("No questions found for section: {$section->title}");
            return $questions;
        }

        // Select questions (randomized or ordered)
        $selectedQuestions = $this->selectQuestions($availableQuestions, $section->question_count);

        // Create Question records
        foreach ($selectedQuestions as $questionModel) {
            $question = Question::create([
                'questionable_type' => get_class($questionModel),
                'questionable_id' => $questionModel->id,
                'subtopic_id' => $questionModel->subtopic_id ?? null,
                'topic_id' => $questionModel->academic_topic_id ?? null,
                'difficulty_level' => $questionModel->difficulty_level ?? 'medium',
                'points' => $section->marks_per_question,
                'user_id' => $assessment->student->user_id,
            ]);

            $questions->push([
                'id' => $question->id,
                'type' => $section->question_type,
                'model' => $questionModel,
                'question_record' => $question,
                'points' => $section->marks_per_question,
                'section_title' => $section->title,
            ]);
        }

        return $questions;
    }

    /**
     * Generate questions from custom configuration
     */
    public function generateQuestionsFromConfiguration(array $config, Assessment $assessment): Collection
    {
        $questions = collect();

        // Get all enabled question types
        $enabledTypes = array_filter($config['question_types'], function($enabled) {
            return $enabled;
        });

        $questionsPerType = intval($config['question_count'] / count($enabledTypes));
        $remainder = $config['question_count'] % count($enabledTypes);

        foreach ($enabledTypes as $type => $enabled) {
            if (!$enabled) continue;

            $count = $questionsPerType;
            if ($remainder > 0) {
                $count++;
                $remainder--;
            }

            $typeQuestions = $this->generateQuestionsForType($type, $count, $config, $assessment);
            $questions = $questions->merge($typeQuestions);
        }

        return $questions;
    }

    /**
     * Generate questions for a specific type with configuration
     */
    public function generateQuestionsForType(string $type, int $count, array $config, Assessment $assessment): Collection
    {
        $questions = collect();
        $questionClass = $this->mapQuestionTypeToClass($type);

        if (!$questionClass) {
            return $questions;
        }

        $query = $questionClass::query();

        // Apply difficulty filter
        if (isset($config['difficulty']) && $config['difficulty'] !== 'all') {
            $query->where('difficulty_level', $config['difficulty']);
        }

        // Apply content filters
        $this->applyContentFilters($query, $config);

        $availableQuestions = $query->get();

        if ($availableQuestions->isEmpty()) {
            return $questions;
        }

        $selectedQuestions = $this->selectQuestions($availableQuestions, $count);

        foreach ($selectedQuestions as $questionModel) {
            $question = Question::create([
                'questionable_type' => get_class($questionModel),
                'questionable_id' => $questionModel->id,
                'subtopic_id' => $questionModel->subtopic_id ?? null,
                'topic_id' => $questionModel->academic_topic_id ?? null,
                'difficulty_level' => $questionModel->difficulty_level ?? 'medium',
                'points' => $config['marks_per_question'] ?? 1,
                'user_id' => $assessment->student->user_id,
            ]);

            $questions->push([
                'id' => $question->id,
                'type' => $type,
                'model' => $questionModel,
                'question_record' => $question,
                'points' => $config['marks_per_question'] ?? 1,
            ]);
        }

        return $questions;
    }

    /**
     * Select questions from available pool
     */
    public function selectQuestions(Collection $questions, int $count): Collection
    {
        if ($questions->count() <= $count) {
            return $questions;
        }

        return $questions->shuffle()->take($count);
    }

    /**
     * Apply assignment-specific filters
     */
    public function applyAssignmentFilters($query, Assessment $assessment): void
    {
        if ($assessment->subject_id) {
            // Filter by subject topics if assignment has specific topics
            $query->whereHas('academicTopic', function($topicQuery) use ($assessment) {
                $topicQuery->where('academic_subject_id', $assessment->subject_id);
            });
        }
    }

    /**
     * Apply content filters for self-assessment
     */
    public function applyContentFilters($query, array $config): void
    {
        if (!empty($config['subject_id'])) {
            $query->whereHas('academicTopic', function($topicQuery) use ($config) {
                $topicQuery->where('academic_subject_id', $config['subject_id']);
            });
        }

        if (!empty($config['topic_id'])) {
            $query->where('academic_topic_id', $config['topic_id']);
        }

        if (!empty($config['subtopic_id'])) {
            $query->where('subtopic_id', $config['subtopic_id']);
        }
    }

    /**
     * Map assignment section question type to model question type
     */
    public function mapQuestionType(string $sectionType): ?string
    {
        $mapping = [
            'multiple_choice' => MultipleChoiceQuestion::class,
            'true_false' => TrueOrFalseQuestion::class,
            'essay' => EssayQuestion::class,
        ];

        return $mapping[$sectionType] ?? null;
    }

    /**
     * Map self-assessment question type to model class
     */
    public function mapQuestionTypeToClass(string $type): ?string
    {
        $mapping = [
            'multiple_choice_question' => MultipleChoiceQuestion::class,
            'true_or_false_question' => TrueOrFalseQuestion::class,
            'essay_question' => EssayQuestion::class,
        ];

        return $mapping[$type] ?? null;
    }

    /**
     * Get assessment configuration from assignment
     */
    public function getAssignmentConfiguration(Assignment $assignment): array
    {
        $config = [
            'title' => $assignment->title,
            'description' => $assignment->description,
            'instructions' => $assignment->instructions,
            'duration_minutes' => $assignment->duration_in_minutes,
            'total_marks' => $assignment->total_marks,
            'subject_id' => $assignment->academic_subject_id,
            'is_randomized' => $assignment->is_randomized,
            'sections' => [],
        ];

        foreach ($assignment->assignmentSections as $section) {
            $config['sections'][] = [
                'title' => $section->title,
                'instructions' => $section->instructions,
                'question_type' => $section->question_type,
                'question_count' => $section->question_count,
                'marks_per_question' => $section->marks_per_question,
                'order' => $section->order,
            ];
        }

        return $config;
    }
}
