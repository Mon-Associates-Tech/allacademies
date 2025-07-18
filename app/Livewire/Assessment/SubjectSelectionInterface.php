<?php

namespace App\Livewire\Assessment;

use Illuminate\Support\Collection;

interface SubjectSelectionInterface
{
    /**
     * Get available subjects for the authenticated user
     */
    public function getAvailableSubjects(): Collection;

    /**
     * Get topics for a specific subject
     */
    public function getTopicsForSubject(int $subjectId): Collection;

    /**
     * Get subtopics for a specific topic
     */
    public function getSubtopicsForTopic(int $topicId): Collection;

    /**
     * Validate if the user has access to the selected subject
     */
    public function canAccessSubject(int $subjectId): bool;

    /**
     * Validate the selection hierarchy (subject -> topic -> subtopic)
     */
    public function validateSelection(int $subjectId, ?int $topicId = null, ?int $subtopicId = null): bool;

    /**
     * Get the selection hierarchy as an array
     */
    public function getSelectionHierarchy(int $subjectId, ?int $topicId = null, ?int $subtopicId = null): array;
}
