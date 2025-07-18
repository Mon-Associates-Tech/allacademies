<?php

namespace App\Livewire\Assessment;

use Illuminate\Support\Collection;

interface QuestionSelectionInterface
{
    /**
     * Generate random questions based on selection criteria
     */
    public function generateQuestions(array $config): Collection;

    /**
     * Get available question counts for selection criteria
     */
    public function getAvailableQuestionCounts(array $config): array;

    /**
     * Validate question selection configuration
     */
    public function validateConfiguration(array $config): bool;

    /**
     * Get questions for a specific question type
     */
    public function getQuestionsByType(string $type, array $config, int $count): Collection;

    /**
     * Mix questions from different types according to configuration
     */
    public function mixQuestions(array $config): Collection;

    /**
     * Format questions for assessment display
     */
    public function formatQuestionsForAssessment(Collection $questions): Collection;
}
