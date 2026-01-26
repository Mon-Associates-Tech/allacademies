<?php

namespace App\Livewire\Assessment;

use App\Models\Assessment;
use App\Models\AssessmentResponse;
use App\Models\Student;

interface StudentAssessmentServiceInterface
{
    /**
     * Create a new assessment for a student
     */
    public function createAssessment(Student $student, array $config): Assessment;

    /**
     * Start an assessment session
     */
    public function startAssessment(Assessment $assessment): array;

    /**
     * Process student response to a question
     */
    public function processResponse(Assessment $assessment, int $questionIndex, $response): bool;

    /**
     * Get current assessment progress
     */
    public function getProgress(Assessment $assessment): array;

    /**
     * Submit assessment for grading
     */
    public function submitAssessment(Assessment $assessment): AssessmentResponse;

    /**
     * Auto-grade assessment responses
     */
    public function gradeAssessment(Assessment $assessment): array;

    /**
     * Calculate final score and performance metrics
     */
    public function calculateResults(Assessment $assessment): array;

    /**
     * Generate detailed performance report
     */
    public function generatePerformanceReport(Assessment $assessment): array;

    /**
     * Get assessment recommendations
     */
    public function getRecommendations(Assessment $assessment): array;

    /**
     * Export assessment results
     */
    public function exportResults(Assessment $assessment, string $format = 'pdf'): string;
}
