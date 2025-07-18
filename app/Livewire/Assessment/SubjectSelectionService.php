<?php

namespace App\Livewire\Assessment;

use App\Models\AcademicSubject;
use App\Models\AcademicTopic;
use App\Models\AcademicSubtopic;
use App\Models\MultipleChoiceQuestion;
use App\Models\TrueOrFalseQuestion;
use App\Models\EssayQuestion;
use App\Models\Student;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class SubjectSelectionService implements SubjectSelectionInterface
{
    protected ?Student $student = null;

    public function __construct(?Student $student = null)
    {
        $this->student = $student ?? auth()->user()?->student;
    }

    /**
     * Get available subjects for the authenticated user
     */
    public function getAvailableSubjects(): Collection
    {
        if (!$this->student) {
            return collect();
        }

        $subjects = collect();

        // Get subjects from student's academic level
        if ($this->student->academicLevel) {
            $levelSubjects = $this->student->academicLevel->academicSubjects()
                ->with(['academicLevel', 'academicTopics'])
                ->get();
            $subjects = $subjects->merge($levelSubjects);
        }

        // Get individual subjects assigned to the student
        $individualSubjects = $this->student->individualSubjects()
            ->wherePivot('is_active', false) //  should be true
            ->with(['academicLevel', 'academicTopics'])
            ->get();

        // Merge individual subjects, removing duplicates
        foreach ($individualSubjects as $subject) {
            if (!$subjects->contains('id', $subject->id)) {
                $subjects->push($subject);
            }
        }

        // Remove subjects that are individually marked as inactive
        $removedSubjects = $this->student->individualSubjects()
            ->wherePivot('is_active', true) //  should be false
            ->pluck('academic_subjects.id');

        $subjects = $subjects->reject(function ($subject) use ($removedSubjects) {
            return $removedSubjects->contains($subject->id);
        });

        return $subjects->sortBy('name');
    }

    /**
     * Get topics for a specific subject
     */
    public function getTopicsForSubject(int $subjectId): Collection
    {
        if (!$this->canAccessSubject($subjectId)) {
            return collect();
        }

        return AcademicTopic::where('academic_subject_id', $subjectId)
            ->with(['subtopics'])
            ->orderBy('name')
            ->get();
    }

    /**
     * Get subtopics for a specific topic
     */
    public function getSubtopicsForTopic(int $topicId): Collection
    {
        $topic = AcademicTopic::find($topicId);

        if (!$topic || !$this->canAccessSubject($topic->academic_subject_id)) {
            return collect();
        }

        return AcademicSubtopic::where('academic_topic_id', $topicId)
            ->orderBy('name')
            ->get();
    }

    /**
     * Validate if the user has access to the selected subject
     */
    public function canAccessSubject(int $subjectId): bool
    {
        if (!$this->student) {
            return false;
        }

        $availableSubjects = $this->getAvailableSubjects();
        return $availableSubjects->contains('id', $subjectId);
    }

    /**
     * Validate the selection hierarchy (subject -> topic -> subtopic)
     */
    public function validateSelection(int $subjectId, ?int $topicId = null, ?int $subtopicId = null): bool
    {
        // Check if user has access to the subject
        if (!$this->canAccessSubject($subjectId)) {
            return false;
        }

        // If topic is selected, validate it belongs to the subject
        if ($topicId) {
            $topic = AcademicTopic::where('id', $topicId)
                ->where('academic_subject_id', $subjectId)
                ->first();

            if (!$topic) {
                return false;
            }

            // If subtopic is selected, validate it belongs to the topic
            if ($subtopicId) {
                $subtopic = AcademicSubtopic::where('id', $subtopicId)
                    ->where('academic_topic_id', $topicId)
                    ->first();

                if (!$subtopic) {
                    return false;
                }
            }
        }

        // If subtopic is selected without topic, it's invalid
        if ($subtopicId && !$topicId) {
            return false;
        }

        return true;
    }

    /**
     * Get the selection hierarchy as an array
     */
    public function getSelectionHierarchy(int $subjectId, ?int $topicId = null, ?int $subtopicId = null): array
    {
        if (!$this->validateSelection($subjectId, $topicId, $subtopicId)) {
            return [];
        }

        $hierarchy = [];

        // Get subject
        $subject = AcademicSubject::find($subjectId);
        if ($subject) {
            $hierarchy['subject'] = [
                'id' => $subject->id,
                'name' => $subject->name,
                'code' => $subject->code,
                'academic_level' => $subject->academicLevel->name ?? null,
            ];
        }

        // Get topic if selected
        if ($topicId) {
            $topic = AcademicTopic::find($topicId);
            if ($topic) {
                $hierarchy['topic'] = [
                    'id' => $topic->id,
                    'name' => $topic->name,
                ];
            }
        }

        // Get subtopic if selected
        if ($subtopicId) {
            $subtopic = AcademicSubtopic::find($subtopicId);
            if ($subtopic) {
                $hierarchy['subtopic'] = [
                    'id' => $subtopic->id,
                    'name' => $subtopic->name,
                    'description' => $subtopic->description,
                ];
            }
        }

        return $hierarchy;
    }

    /**
     * Get formatted selection string
     */
    public function getSelectionString(int $subjectId, ?int $topicId = null, ?int $subtopicId = null): string
    {
        $hierarchy = $this->getSelectionHierarchy($subjectId, $topicId, $subtopicId);

        if (empty($hierarchy)) {
            return '';
        }

        $parts = [];

        if (isset($hierarchy['subject'])) {
            $parts[] = $hierarchy['subject']['name'];
        }

        if (isset($hierarchy['topic'])) {
            $parts[] = $hierarchy['topic']['name'];
        }

        if (isset($hierarchy['subtopic'])) {
            $parts[] = $hierarchy['subtopic']['name'];
        }

        return implode(' → ', $parts);
    }

    /**
     * Get available question counts for the selection
     */
    public function getAvailableQuestionCounts(int $subjectId, ?int $topicId = null, ?int $subtopicId = null): array
    {
        if (!$this->validateSelection($subjectId, $topicId, $subtopicId)) {
            return [];
        }

        $counts = [];

        // Build base query based on selection level
        if ($subtopicId) {
            // Count questions for specific subtopic
            $counts['multiple_choice'] = MultipleChoiceQuestion::where('academic_subtopic_id', $subtopicId)->count();
            $counts['true_false'] = TrueOrFalseQuestion::where('academic_subtopic_id', $subtopicId)->count();
            $counts['essay'] = EssayQuestion::where('academic_subtopic_id', $subtopicId)->count();
        } elseif ($topicId) {
            // Count questions for all subtopics in topic
            $subtopicIds = AcademicSubtopic::where('academic_topic_id', $topicId)->pluck('id')->toArray();

            $counts['multiple_choice'] = MultipleChoiceQuestion::whereIn('academic_subtopic_id', $subtopicIds)->count();
            $counts['true_false'] = TrueOrFalseQuestion::whereIn('academic_subtopic_id', $subtopicIds)->count();
            $counts['essay'] = EssayQuestion::whereIn('academic_subtopic_id', $subtopicIds)->count();
        } else {
            // Count questions for entire subject
            $subtopicIds = AcademicSubtopic::whereHas('academicTopic', function ($query) use ($subjectId) {
                $query->where('academic_subject_id', $subjectId);
            })->pluck('id')->toArray();

            $counts['multiple_choice'] = MultipleChoiceQuestion::whereIn('academic_subtopic_id', $subtopicIds)->count();
            $counts['true_false'] = TrueOrFalseQuestion::whereIn('academic_subtopic_id', $subtopicIds)->count();
            $counts['essay'] = EssayQuestion::whereIn('academic_subtopic_id', $subtopicIds)->count();
        }

        $counts['total'] = array_sum($counts);

        return $counts;
    }

    /**
     * Set the student for this service instance
     */
    public function setStudent(Student $student): self
    {
        $this->student = $student;
        return $this;
    }
}
