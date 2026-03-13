<?php

namespace App\Services\Quiz;

use App\Services\AcademicChatService;
use Illuminate\Support\Facades\Log;

class QuizGradingService
{
    public function __construct(protected AcademicChatService $chatService) {}

    /**
     * Grade a quiz based on standardized response data.
     */
    public function gradeQuiz(array $sections, array $responses): array
    {
        $totalEarned = 0;
        $totalPossible = 0;
        $sectionResults = [];

        foreach ($sections as $sectionIndex => $section) {
            $sectionEarned = 0;
            $sectionPossible = 0;
            $questionResults = [];

            $questions = $section['questions'] ?? [];
            foreach ($questions as $questionIndex => $question) {
                $response = $responses[$sectionIndex][$questionIndex] ?? null;
                $points = $question['points'] ?? 1;
                $sectionPossible += $points;

                $result = $this->gradeQuestion($question, $response, $section['grading_mode'] ?? 'automatic', $section['marking_scheme'] ?? null);

                $earned = $result['earned_marks'] ?? 0;
                $sectionEarned += $earned;

                $questionResults[] = array_merge($result, [
                    'question' => $question['text'] ?? '',
                    'response' => $response,
                    'correct_answer' => $question['correct_answer'] ?? null,
                    'possible_marks' => $points,
                ]);
            }

            $totalEarned += $sectionEarned;
            $totalPossible += $sectionPossible;

            $sectionResults[] = [
                'title' => $section['title'] ?? '',
                'earned_marks' => $sectionEarned,
                'possible_marks' => $sectionPossible,
                'questions' => $questionResults,
            ];
        }

        return [
            'total_earned' => $totalEarned,
            'total_possible' => $totalPossible,
            'percentage' => $totalPossible > 0 ? round(($totalEarned / $totalPossible) * 100, 2) : 0,
            'sections' => $sectionResults,
        ];
    }

    public function gradeQuestion(array $question, mixed $response, string $gradingMode, ?string $markingScheme = null): array
    {
        $type = $question['type'] ?? 'multiple_choice';
        $points = $question['points'] ?? 1;

        if ($response === null || $response === '') {
            return [
                'is_correct' => false,
                'earned_marks' => 0,
                'feedback' => 'No answer provided.',
                'status' => 'graded',
            ];
        }

        switch ($type) {
            case 'multiple_choice':
            case 'true_false':
                $isCorrect = (strtoupper((string) $response) === strtoupper((string) $question['correct_answer']));

                return [
                    'is_correct' => $isCorrect,
                    'earned_marks' => $isCorrect ? $points : 0,
                    'feedback' => $isCorrect ? 'Correct!' : 'Incorrect.',
                    'status' => 'graded',
                ];

            case 'essay':
            case 'short_answer':
                if ($gradingMode === 'manual') {
                    return [
                        'is_correct' => null,
                        'earned_marks' => 0,
                        'feedback' => 'Pending manual grading.',
                        'status' => 'pending',
                    ];
                }

                // AI Automatic Grading
                return $this->gradeEssayWithAI($question, (string) $response, $markingScheme);

            default:
                return [
                    'is_correct' => false,
                    'earned_marks' => 0,
                    'feedback' => 'Unknown question type.',
                    'status' => 'error',
                ];
        }
    }

    protected function gradeEssayWithAI(array $question, string $response, ?string $markingScheme = null): array
    {
        $prompt = "You are an AI assistant grading a student's answer for a {$question['type']} question.\n";
        $prompt .= "Question: {$question['text']}\n";
        $prompt .= "Student Response: {$response}\n";
        $prompt .= "Max Marks: {$question['points']}\n";

        if ($markingScheme) {
            $prompt .= "Marking Scheme/Reference Content: {$markingScheme}\n";
        } elseif (isset($question['correct_answer'])) {
            $prompt .= "Correct Answer/Reference: {$question['correct_answer']}\n";
        }

        $prompt .= "\nPlease evaluate the answer and provide marks. Return ONLY a JSON object with keys: 'earned_marks' (float), 'feedback' (string), 'is_correct' (boolean).";

        try {
            // Using a more standard method name from AcademicChatService if sendMessage isn't there
            // Based on structure it has processRequest or chat
            $aiResponse = $this->chatService->processRequest([
                'prompt' => $prompt,
                'educational_context' => 'grading',
            ], []);

            $content = $aiResponse['content'] ?? (is_string($aiResponse) ? $aiResponse : '');
            $result = json_decode($this->cleanAiJson($content), true);

            if ($result && isset($result['earned_marks'])) {
                return [
                    'is_correct' => $result['is_correct'] ?? ($result['earned_marks'] >= ($question['points'] / 2)),
                    'earned_marks' => (float) min($result['earned_marks'], $question['points']),
                    'feedback' => $result['feedback'] ?? 'Graded by AI.',
                    'status' => 'graded',
                ];
            }
        } catch (\Exception $e) {
            Log::error('AI Grading failed: '.$e->getMessage());
        }

        return [
            'is_correct' => null,
            'earned_marks' => 0,
            'feedback' => 'AI grading failed. Pending manual review.',
            'status' => 'pending',
        ];
    }

    protected function cleanAiJson(string $content): string
    {
        if (preg_match('/\{(?:[^{}]|(?R))*\}/s', $content, $matches)) {
            return $matches[0];
        }

        return $content;
    }
}
