<?php

namespace App\Livewire\Assessment;

use App\Models\Student;
use App\Models\Assessment;
use App\Models\AssessmentResponse;
use App\Models\AcademicSubject;
use App\Livewire\Assessment\RandomQuestionSelectionService;
use App\Livewire\Assessment\SubjectSelectionService;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class StudentAssessmentService implements StudentAssessmentServiceInterface
{
    protected RandomQuestionSelectionService $questionService;
    protected SubjectSelectionService $subjectService;
    protected AssessmentGradingService $gradingService;

    public function __construct(
        RandomQuestionSelectionService $questionService,
        SubjectSelectionService $subjectService,
        AssessmentGradingService $gradingService
    ) {
        $this->questionService = $questionService;
        $this->subjectService = $subjectService;
        $this->gradingService = $gradingService;
    }

    /**
     * Create a new assessment for a student
     */
    public function createAssessment(Student $student, array $config): Assessment
    {
        // Validate configuration
        $this->validateConfiguration($config);

        // Generate questions
        $questions = $this->generateQuestions($config);

        if ($questions->isEmpty()) {
            throw new \Exception('No questions available for the selected criteria');
        }

        // Calculate assessment settings
        $maxScore = $questions->sum('points');
        $hasEssayQuestions = $questions->contains('type', 'essay_question');
        $timeLimit = $config['time_limit_minutes'] ?? $this->calculateDefaultTimeLimit($questions);

        // Create assessment record
        $assessment = Assessment::create([
            'student_id' => $student->id,
            'subject_id' => $config['subject_id'],
            'topic_id' => $config['topic_id'] ?? null,
            'subtopic_id' => $config['subtopic_id'] ?? null,
            'title' => $this->generateAssessmentTitle($config),
            'type' => Assessment::TYPE_SELF,
            'question_types' => array_keys(array_filter($config['question_types'])),
            'max_score' => $maxScore,
            'time_limit_minutes' => $timeLimit,
            'status' => Assessment::STATUS_NOT_STARTED,
            'has_essay_questions' => $hasEssayQuestions,
            'questions_data' => $questions->toArray()
        ]);

        $this->logActivity($student, 'assessment_created', [
            'assessment_id' => $assessment->id,
            'config' => $config
        ]);

        return $assessment;
    }

    /**
     * Start an assessment session
     */
    public function startAssessment(Assessment $assessment): array
    {
        if ($assessment->status !== Assessment::STATUS_NOT_STARTED) {
            throw new \Exception('Assessment has already been started');
        }

        // Update assessment status
        $assessment->update([
            'status' => Assessment::STATUS_IN_PROGRESS,
            'start_time' => now()
        ]);

        // Initialize response tracking
        $questions = collect($assessment->getQuestionsData());
        $responses = $questions->map(function ($question, $index) {
            return [
                'question_index' => $index,
                'question_id' => $question['id'],
                'response' => null,
                'is_answered' => false,
                'time_spent' => 0,
                'attempts' => 0
            ];
        });

        $sessionData = [
            'questions' => $questions->toArray(),
            'responses' => $responses->toArray(),
            'current_question' => 0,
            'progress' => 0,
            'time_remaining' => $assessment->time_limit_minutes * 60,
            'started_at' => now()->toISOString()
        ];

        $this->logActivity($assessment->student, 'assessment_started', [
            'assessment_id' => $assessment->id,
            'questions_count' => $questions->count()
        ]);

        return $sessionData;
    }

    /**
     * Process student response to a question
     */
    public function processResponse(Assessment $assessment, int $questionIndex, $response): bool
    {
        if ($assessment->status !== Assessment::STATUS_IN_PROGRESS) {
            throw new \Exception('Assessment is not in progress');
        }

        $questions = collect($assessment->getQuestionsData());

        if (!isset($questions[$questionIndex])) {
            throw new \Exception('Invalid question index');
        }

        $question = $questions[$questionIndex];

        // Validate response format
        if (!$this->validateResponse($question, $response)) {
            return false;
        }

        // Get existing assessment response or create new one
        $assessmentResponse = $assessment->assessmentResponse ?? new AssessmentResponse([
            'assessment_id' => $assessment->id,
            'data' => ['responses' => []]
        ]);

        $responseData = $assessmentResponse->data ?? ['responses' => []];

        // Update the specific response
        $responseData['responses'][$questionIndex] = [
            'question_id' => $question['id'],
            'question_type' => $question['type'],
            'response' => $response,
            'answered_at' => now()->toISOString(),
            'is_answered' => true
        ];

        // Update progress
        $answeredCount = count(array_filter($responseData['responses'], fn($r) => $r['is_answered'] ?? false));
        $responseData['progress'] = ($answeredCount / count($questions)) * 100;
        $responseData['last_updated'] = now()->toISOString();

        $assessmentResponse->data = $responseData;
        $assessmentResponse->save();

        $this->logActivity($assessment->student, 'question_answered', [
            'assessment_id' => $assessment->id,
            'question_index' => $questionIndex,
            'question_type' => $question['type']
        ]);

        return true;
    }

    /**
     * Get current assessment progress
     */
    public function getProgress(Assessment $assessment): array
    {
        $questions = collect($assessment->getQuestionsData());
        $responses = $assessment->assessmentResponse?->data['responses'] ?? [];

        $answeredCount = count(array_filter($responses, fn($r) => $r['is_answered'] ?? false));
        $totalCount = $questions->count();

        $timeElapsed = $assessment->start_time ?
            now()->diffInSeconds($assessment->start_time) : 0;

        $timeRemaining = max(0, ($assessment->time_limit_minutes * 60) - $timeElapsed);

        return [
            'total_questions' => $totalCount,
            'answered_questions' => $answeredCount,
            'progress_percentage' => $totalCount > 0 ? ($answeredCount / $totalCount) * 100 : 0,
            'time_elapsed' => $timeElapsed,
            'time_remaining' => $timeRemaining,
            'questions_by_type' => $this->getQuestionsByType($questions, $responses),
            'current_status' => $assessment->status
        ];
    }

    /**
     * Submit assessment for grading
     */
    public function submitAssessment(Assessment $assessment): AssessmentResponse
    {
        if ($assessment->status !== Assessment::STATUS_IN_PROGRESS) {
            throw new \Exception('Assessment is not in progress');
        }

        // Update assessment end time
        $assessment->update([
            'end_time' => now(),
            'status' => $assessment->has_essay_questions ?
                Assessment::STATUS_PENDING_REVIEW :
                Assessment::STATUS_COMPLETED
        ]);

        // Get or create assessment response
        $assessmentResponse = $assessment->assessmentResponse ?? AssessmentResponse::create([
            'assessment_id' => $assessment->id,
            'data' => ['responses' => []]
        ]);

        // Auto-grade if possible
        if (!$assessment->has_essay_questions) {
            $results = $this->gradeAssessment($assessment);

            // Update assessment with results
            $assessment->update([
                'score' => $results['total_score'],
                'percentage_score' => $results['percentage']
            ]);
        }

        $this->logActivity($assessment->student, 'assessment_submitted', [
            'assessment_id' => $assessment->id,
            'has_essay_questions' => $assessment->has_essay_questions
        ]);

        return $assessmentResponse;
    }

    /**
     * Auto-grade assessment responses
     */
    public function gradeAssessment(Assessment $assessment): array
    {
        $questions = collect($assessment->getQuestionsData());
        $responses = $assessment->assessmentResponse?->data['responses'] ?? [];

        $results = [
            'total_questions' => $questions->count(),
            'answered_questions' => 0,
            'correct_answers' => 0,
            'total_score' => 0,
            'max_score' => $questions->sum('points'),
            'percentage' => 0,
            'question_results' => [],
            'type_breakdown' => []
        ];

        foreach ($questions as $index => $question) {
            $response = $responses[$index] ?? null;
            $questionResult = $this->gradeQuestion($question, $response);

            $results['question_results'][] = $questionResult;

            if ($questionResult['is_answered']) {
                $results['answered_questions']++;

                if ($questionResult['is_correct']) {
                    $results['correct_answers']++;
                    $results['total_score'] += $questionResult['score_earned'];
                }
            }

            // Update type breakdown
            $type = $question['type'];
            if (!isset($results['type_breakdown'][$type])) {
                $results['type_breakdown'][$type] = [
                    'total' => 0,
                    'correct' => 0,
                    'score' => 0,
                    'max_score' => 0
                ];
            }

            $results['type_breakdown'][$type]['total']++;
            $results['type_breakdown'][$type]['max_score'] += $question['points'];

            if ($questionResult['is_correct']) {
                $results['type_breakdown'][$type]['correct']++;
                $results['type_breakdown'][$type]['score'] += $questionResult['score_earned'];
            }
        }

        $results['percentage'] = $results['max_score'] > 0 ?
            ($results['total_score'] / $results['max_score']) * 100 : 0;

        return $results;
    }

    /**
     * Calculate final score and performance metrics
     */
    public function calculateResults(Assessment $assessment): array
    {
        $basicResults = $this->gradeAssessment($assessment);

        // Add performance metrics
        $performanceMetrics = $this->calculatePerformanceMetrics($assessment, $basicResults);

        // Add difficulty analysis
        $difficultyAnalysis = $this->analyzeDifficultyPerformance($assessment, $basicResults);

        // Add time analysis
        $timeAnalysis = $this->analyzeTimePerformance($assessment);

        return array_merge($basicResults, [
            'performance_metrics' => $performanceMetrics,
            'difficulty_analysis' => $difficultyAnalysis,
            'time_analysis' => $timeAnalysis,
            'grade' => $this->calculateGrade($basicResults['percentage']),
            'recommendations' => $this->getRecommendations($assessment)
        ]);
    }

    /**
     * Generate detailed performance report
     */
    public function generatePerformanceReport(Assessment $assessment): array
    {
        $results = $this->calculateResults($assessment);

        return [
            'assessment_info' => [
                'title' => $assessment->title,
                'subject' => $assessment->subject->name,
                'topic' => $assessment->topic?->name,
                'subtopic' => $assessment->subtopic?->name,
                'date_taken' => $assessment->start_time->format('Y-m-d H:i:s'),
                'duration' => $assessment->end_time ?
                    $assessment->start_time->diffInMinutes($assessment->end_time) : null
            ],
            'overall_performance' => [
                'score' => $results['total_score'],
                'max_score' => $results['max_score'],
                'percentage' => $results['percentage'],
                'grade' => $results['grade'],
                'status' => $this->getPerformanceStatus($results['percentage'])
            ],
            'detailed_results' => $results,
            'strengths' => $this->identifyStrengths($results),
            'areas_for_improvement' => $this->identifyWeaknesses($results),
            'recommendations' => $results['recommendations']
        ];
    }

    /**
     * Get assessment recommendations
     */
    public function getRecommendations(Assessment $assessment): array
    {
        $results = $this->calculateResults($assessment);
        $recommendations = [];

        // Performance-based recommendations
        if ($results['percentage'] < 70) {
            $recommendations[] = [
                'type' => 'review',
                'priority' => 'high',
                'message' => 'Consider reviewing the fundamental concepts before attempting similar assessments.',
                'action' => 'review_fundamentals'
            ];
        }

        // Type-specific recommendations
        foreach ($results['type_breakdown'] as $type => $typeResults) {
            $typePercentage = $typeResults['max_score'] > 0 ?
                ($typeResults['score'] / $typeResults['max_score']) * 100 : 0;

            if ($typePercentage < 60) {
                $recommendations[] = [
                    'type' => 'practice',
                    'priority' => 'medium',
                    'message' => "Focus on improving {$type} question performance through targeted practice.",
                    'action' => "practice_{$type}"
                ];
            }
        }

        // Difficulty-based recommendations
        $difficultyAnalysis = $this->analyzeDifficultyPerformance($assessment, $results);
        foreach ($difficultyAnalysis as $difficulty => $analysis) {
            if ($analysis['percentage'] < 50) {
                $recommendations[] = [
                    'type' => 'difficulty',
                    'priority' => 'medium',
                    'message' => "Work on {$difficulty} level questions to improve overall performance.",
                    'action' => "practice_{$difficulty}"
                ];
            }
        }

        return $recommendations;
    }

    /**
     * Export assessment results
     */
    public function exportResults(Assessment $assessment, string $format = 'pdf'): string
    {
        $report = $this->generatePerformanceReport($assessment);

        switch ($format) {
            case 'pdf':
                return $this->exportToPdf($report);
            case 'json':
                return json_encode($report, JSON_PRETTY_PRINT);
            case 'csv':
                return $this->exportToCsv($report);
            default:
                throw new \Exception("Unsupported export format: {$format}");
        }
    }

    // Protected helper methods

    protected function validateConfiguration(array $config): void
    {
        $required = ['subject_id', 'question_types', 'question_count'];

        foreach ($required as $field) {
            if (!isset($config[$field])) {
                throw new \Exception("Missing required field: {$field}");
            }
        }

        if (!$this->subjectService->canAccessSubject($config['subject_id'])) {
            throw new \Exception('Student does not have access to this subject');
        }

        if (empty(array_filter($config['question_types']))) {
            throw new \Exception('At least one question type must be selected');
        }
    }

    protected function generateQuestions(array $config): Collection
    {
        return $this->questionService->generateQuestions($config);
    }

    protected function calculateDefaultTimeLimit(Collection $questions): int
    {
        // Base time: 1 minute per question
        $baseTime = $questions->count();

        // Adjust for question types
        $typeMultipliers = [
            'multiple_choice_question' => 1.0,
            'true_or_false_question' => 0.5,
            'essay_question' => 3.0
        ];

        $adjustedTime = $questions->sum(function ($question) use ($typeMultipliers) {
            return $typeMultipliers[$question['type']] ?? 1.0;
        });

        return max(5, min(180, (int)$adjustedTime));
    }

    protected function generateAssessmentTitle(array $config): string
    {
        $subject = AcademicSubject::find($config['subject_id']);
        $title = "Self Assessment - {$subject->name}";

        if (!empty($config['topic_id'])) {
            $topic = $subject->academicTopics()->find($config['topic_id']);
            $title .= " - {$topic->name}";
        }

        return $title;
    }

    protected function validateResponse($question, $response): bool
    {
        if ($response === null || $response === '') {
            return false;
        }

        switch ($question['type']) {
            case 'multiple_choice_question':
                return in_array($response, ['A', 'B', 'C', 'D', 'E']);
            case 'true_or_false_question':
                return in_array($response, ['true', 'false', true, false]);
            case 'essay_question':
                return is_string($response) && strlen(trim($response)) > 0;
            default:
                return false;
        }
    }

    protected function gradeQuestion($question, $response): array
    {
        $result = [
            'question_id' => $question['id'],
            'question_type' => $question['type'],
            'is_answered' => $response && ($response['is_answered'] ?? false),
            'is_correct' => false,
            'score_earned' => 0,
            'max_score' => $question['points'],
            'response' => $response['response'] ?? null
        ];

        if (!$result['is_answered']) {
            return $result;
        }

        if ($question['type'] === 'essay_question') {
            $result['is_correct'] = null; // Requires manual grading
            return $result;
        }

        $studentAnswer = $response['response'];
        $correctAnswer = $question['correct_answer'];

        switch ($question['type']) {
            case 'multiple_choice_question':
                $result['is_correct'] = strtoupper($studentAnswer) === strtoupper($correctAnswer);
                break;
            case 'true_or_false_question':
                $result['is_correct'] = (bool)$studentAnswer === (bool)$correctAnswer;
                break;
        }

        if ($result['is_correct']) {
            $result['score_earned'] = $question['points'];
        }

        return $result;
    }

    protected function getQuestionsByType(Collection $questions, array $responses): array
    {
        $breakdown = [];

        foreach ($questions as $index => $question) {
            $type = $question['type'];
            $response = $responses[$index] ?? null;

            if (!isset($breakdown[$type])) {
                $breakdown[$type] = [
                    'total' => 0,
                    'answered' => 0,
                    'percentage' => 0
                ];
            }

            $breakdown[$type]['total']++;

            if ($response && ($response['is_answered'] ?? false)) {
                $breakdown[$type]['answered']++;
            }
        }

        foreach ($breakdown as &$type) {
            $type['percentage'] = $type['total'] > 0 ?
                ($type['answered'] / $type['total']) * 100 : 0;
        }

        return $breakdown;
    }

    protected function calculatePerformanceMetrics(Assessment $assessment, array $results): array
    {
        return [
            'completion_rate' => $results['total_questions'] > 0 ?
                ($results['answered_questions'] / $results['total_questions']) * 100 : 0,
            'accuracy_rate' => $results['answered_questions'] > 0 ?
                ($results['correct_answers'] / $results['answered_questions']) * 100 : 0,
            'efficiency_score' => $this->calculateEfficiencyScore($assessment, $results),
            'consistency_score' => $this->calculateConsistencyScore($results)
        ];
    }

    protected function analyzeDifficultyPerformance(Assessment $assessment, array $results): array
    {
        $questions = collect($assessment->getQuestionsData());
        $difficultyBreakdown = [];

        foreach ($questions as $index => $question) {
            $difficulty = $question['difficulty'] ?? 'medium';
            $questionResult = $results['question_results'][$index] ?? null;

            if (!isset($difficultyBreakdown[$difficulty])) {
                $difficultyBreakdown[$difficulty] = [
                    'total' => 0,
                    'correct' => 0,
                    'score' => 0,
                    'max_score' => 0
                ];
            }

            $difficultyBreakdown[$difficulty]['total']++;
            $difficultyBreakdown[$difficulty]['max_score'] += $question['points'];

            if ($questionResult && $questionResult['is_correct']) {
                $difficultyBreakdown[$difficulty]['correct']++;
                $difficultyBreakdown[$difficulty]['score'] += $questionResult['score_earned'];
            }
        }

        foreach ($difficultyBreakdown as &$difficulty) {
            $difficulty['percentage'] = $difficulty['max_score'] > 0 ?
                ($difficulty['score'] / $difficulty['max_score']) * 100 : 0;
        }

        return $difficultyBreakdown;
    }

    protected function analyzeTimePerformance(Assessment $assessment): array
    {
        $totalTime = $assessment->end_time ?
            $assessment->start_time->diffInSeconds($assessment->end_time) : 0;

        $allocatedTime = $assessment->time_limit_minutes * 60;

        return [
            'total_time_seconds' => $totalTime,
            'allocated_time_seconds' => $allocatedTime,
            'time_efficiency' => $allocatedTime > 0 ? ($totalTime / $allocatedTime) * 100 : 0,
            'average_time_per_question' => $assessment->getQuestionsData() ?
                $totalTime / count($assessment->getQuestionsData()) : 0
        ];
    }

    protected function calculateGrade(float $percentage): string
    {
        if ($percentage >= 90) return 'A';
        if ($percentage >= 80) return 'B';
        if ($percentage >= 70) return 'C';
        if ($percentage >= 60) return 'D';
        return 'F';
    }

    protected function getPerformanceStatus(float $percentage): string
    {
        if ($percentage >= 85) return 'excellent';
        if ($percentage >= 70) return 'good';
        if ($percentage >= 60) return 'satisfactory';
        return 'needs_improvement';
    }

    protected function identifyStrengths(array $results): array
    {
        $strengths = [];

        foreach ($results['type_breakdown'] as $type => $typeResult) {
            $percentage = $typeResult['max_score'] > 0 ?
                ($typeResult['score'] / $typeResult['max_score']) * 100 : 0;

            if ($percentage >= 80) {
                $strengths[] = [
                    'area' => $type,
                    'performance' => $percentage,
                    'description' => "Strong performance in {$type} questions"
                ];
            }
        }

        return $strengths;
    }

    protected function identifyWeaknesses(array $results): array
    {
        $weaknesses = [];

        foreach ($results['type_breakdown'] as $type => $typeResult) {
            $percentage = $typeResult['max_score'] > 0 ?
                ($typeResult['score'] / $typeResult['max_score']) * 100 : 0;

            if ($percentage < 60) {
                $weaknesses[] = [
                    'area' => $type,
                    'performance' => $percentage,
                    'description' => "Room for improvement in {$type} questions"
                ];
            }
        }

        return $weaknesses;
    }

    protected function calculateEfficiencyScore(Assessment $assessment, array $results): float
    {
        $timeAnalysis = $this->analyzeTimePerformance($assessment);
        $accuracyRate = $results['answered_questions'] > 0 ?
            ($results['correct_answers'] / $results['answered_questions']) * 100 : 0;

        // Efficiency = (Accuracy * Time Usage Balance)
        $timeBalance = min(100, max(0, 100 - abs($timeAnalysis['time_efficiency'] - 100)));

        return ($accuracyRate * $timeBalance) / 100;
    }

    protected function calculateConsistencyScore(array $results): float
    {
        $typePerformances = [];

        foreach ($results['type_breakdown'] as $typeResult) {
            $typePerformances[] = $typeResult['max_score'] > 0 ?
                ($typeResult['score'] / $typeResult['max_score']) * 100 : 0;
        }

        if (count($typePerformances) <= 1) {
            return 100; // Perfect consistency with single type
        }

        $average = array_sum($typePerformances) / count($typePerformances);
        $variance = array_sum(array_map(fn($p) => pow($p - $average, 2), $typePerformances)) / count($typePerformances);
        $standardDeviation = sqrt($variance);

        // Lower standard deviation = higher consistency
        return max(0, 100 - $standardDeviation);
    }

    protected function exportToPdf(array $report): string
    {
        // Implementation for PDF export
        // This would use a PDF library like DomPDF or similar
        return 'PDF export not implemented yet';
    }

    protected function exportToCsv(array $report): string
    {
        // Implementation for CSV export
        $csv = "Assessment Report\n";
        $csv .= "Subject,{$report['assessment_info']['subject']}\n";
        $csv .= "Score,{$report['overall_performance']['score']}/{$report['overall_performance']['max_score']}\n";
        $csv .= "Percentage,{$report['overall_performance']['percentage']}%\n";
        $csv .= "Grade,{$report['overall_performance']['grade']}\n";

        return $csv;
    }

    protected function logActivity(Student $student, string $action, array $properties = []): void
    {
        activity()
            ->performedOn($student)
            ->causedBy($student->user)
            ->withProperties($properties)
            ->log($action);
    }
}
