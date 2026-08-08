<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

class DocumentQuestionExtractionService
{
    public function __construct(
        private readonly ResearchAssistantService $chatService
    ) {}

    /**
     * Extract or generate questions from document content
     * Intelligently detects if content contains pre-formatted questions or needs generation
     */
    public function processDocumentContent(string $content, string $questionType, int $count): array
    {
        // First, detect if the document contains pre-formatted questions
        $detectionResult = $this->detectQuestionFormat($content);
        
        Log::info('Document question detection', [
            'has_questions' => $detectionResult['has_questions'],
            'confidence' => $detectionResult['confidence'],
            'question_type' => $questionType,
        ]);

        if ($detectionResult['has_questions'] && $detectionResult['confidence'] > 0.7) {
            // Extract pre-formatted questions
            return $this->extractPreFormattedQuestions($content, $questionType, $count);
        }

        // Generate new questions from content
        return $this->generateQuestionsFromContent($content, $questionType, $count);
    }

    /**
     * Detect if document contains pre-formatted questions
     */
    private function detectQuestionFormat(string $content): array
    {
        $indicators = [
            'question_markers' => 0,
            'option_markers' => 0,
            'answer_markers' => 0,
        ];

        // Check for question numbering patterns
        if (preg_match_all('/(?:^|\n)\s*(?:\d+[\.\)]\s*|Q\d+[\.\):]|Question\s+\d+)/im', $content, $matches)) {
            $indicators['question_markers'] = count($matches[0]);
        }

        // Check for option patterns (A), B), a., b., etc.
        if (preg_match_all('/(?:^|\n)\s*[A-Ea-e][\.\)]\s+/m', $content, $matches)) {
            $indicators['option_markers'] = count($matches[0]);
        }

        // Check for answer indicators (bold, underline, "correct answer", etc.)
        $answerPatterns = [
            '/\*\*[^*]+\*\*/',  // **bold**
            '/__[^_]+__/',       // __underline__
            '/\b(?:correct|answer|right)\s*(?:answer|option)?[:\s]/i',
            '/<b>[^<]+<\/b>/',   // <b>bold</b>
            '/<u>[^<]+<\/u>/',   // <u>underline</u>
        ];

        foreach ($answerPatterns as $pattern) {
            if (preg_match_all($pattern, $content, $matches)) {
                $indicators['answer_markers'] += count($matches[0]);
            }
        }

        // Calculate confidence score
        $hasQuestions = $indicators['question_markers'] >= 3;
        $hasOptions = $indicators['option_markers'] >= 12; // At least 3 questions with 4 options
        $hasAnswers = $indicators['answer_markers'] >= 3;

        $confidence = 0;
        if ($hasQuestions) $confidence += 0.4;
        if ($hasOptions) $confidence += 0.4;
        if ($hasAnswers) $confidence += 0.2;

        return [
            'has_questions' => $hasQuestions && $hasOptions,
            'confidence' => $confidence,
            'indicators' => $indicators,
        ];
    }

    /**
     * Extract pre-formatted questions using AI
     */
    private function extractPreFormattedQuestions(string $content, string $questionType, int $count): array
    {
        $prompt = $this->buildExtractionPrompt($content, $questionType, $count);

        try {
            $response = $this->chatService->chat([
                'input' => $prompt,
                'request_type' => 'quiz_generation',
                'creativity_level' => 0.3, // Lower creativity for extraction
                'response_length' => 4000,
            ]);

            if (!$response['success']) {
                Log::error('AI extraction failed', ['error' => $response['error'] ?? 'Unknown error']);
                return [];
            }

            return $this->parseAiResponse($response['content'] ?? '', $questionType);
        } catch (\Exception $e) {
            Log::error('Question extraction failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return [];
        }
    }

    /**
     * Generate new questions from content using AI
     */
    private function generateQuestionsFromContent(string $content, string $questionType, int $count): array
    {
        $prompt = $this->buildGenerationPrompt($content, $questionType, $count);

        try {
            $response = $this->chatService->chat([
                'input' => $prompt,
                'request_type' => 'quiz_generation',
                'creativity_level' => 0.7,
                'response_length' => 4000,
            ]);

            if (!$response['success']) {
                Log::error('AI generation failed', ['error' => $response['error'] ?? 'Unknown error']);
                return [];
            }

            return $this->parseAiResponse($response['content'] ?? '', $questionType);
        } catch (\Exception $e) {
            Log::error('Question generation failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return [];
        }
    }

    /**
     * Build prompt for extracting pre-formatted questions
     */
    private function buildExtractionPrompt(string $content, string $questionType, int $count): string
    {
        $typeInstructions = $this->getTypeInstructions($questionType);

        return <<<PROMPT
The following document contains pre-formatted questions with options. Your task is to extract and structure them.

IMPORTANT INSTRUCTIONS:
1. Extract up to {$count} questions from the document
2. Identify the correct answer by looking for:
   - Bold text (marked with ** or <b> tags)
   - Underlined text (marked with __ or <u> tags)
   - Text marked as "correct answer" or similar
   - Any other formatting that indicates the correct option
3. Clean up the formatting and present in structured JSON format
4. Preserve the original question text and options
5. {$typeInstructions}

Return ONLY a JSON array with this exact structure:
[
  {
    "question": "The extracted question text",
    "options": ["Option A text", "Option B text", "Option C text", "Option D text"],
    "correct_answer": "A",
    "points": 1
  }
]

Document Content:
{$content}

Return ONLY the JSON array, no additional text or explanation.
PROMPT;
    }

    /**
     * Build prompt for generating new questions
     */
    private function buildGenerationPrompt(string $content, string $questionType, int $count): string
    {
        $typeInstructions = $this->getTypeInstructions($questionType);

        return <<<PROMPT
Based on the following content, generate exactly {$count} {$questionType} questions.

{$typeInstructions}

Format your response as a JSON array with this structure:
[
  {
    "question": "Question text here",
    "options": ["Option A", "Option B", "Option C", "Option D"],
    "correct_answer": "A",
    "explanation": "Brief explanation (optional)",
    "points": 1
  }
]

Content:
{$content}

Return ONLY the JSON array, no additional text.
PROMPT;
    }

    /**
     * Get type-specific instructions
     */
    private function getTypeInstructions(string $questionType): string
    {
        return match ($questionType) {
            'multiple_choice' => 'Each question must have 4-5 options (A, B, C, D, E). Indicate the correct answer as A, B, C, D, or E.',
            'true_false' => 'Each question must have exactly 2 options: "True" and "False". Indicate the correct answer.',
            'essay' => 'Generate essay questions that require detailed explanations. Provide key points for the answer.',
            'short_answer' => 'Generate short answer questions. Provide the expected answer.',
            default => 'Generate appropriate questions based on the content.',
        };
    }

    /**
     * Parse AI response into structured question array
     */
    private function parseAiResponse(string $response, string $questionType): array
    {
        $response = trim($response);

        // Remove markdown code blocks if present
        if (str_starts_with($response, '```json')) {
            $response = preg_replace('/^```json\s*/', '', $response);
            $response = preg_replace('/\s*```$/', '', $response);
        } elseif (str_starts_with($response, '```')) {
            $response = preg_replace('/^```\s*/', '', $response);
            $response = preg_replace('/\s*```$/', '', $response);
        }

        try {
            $questions = json_decode($response, true, 512, JSON_THROW_ON_ERROR);

            if (!is_array($questions)) {
                Log::warning('AI response is not an array', ['response' => $response]);
                return [];
            }

            return collect($questions)->map(function ($q) use ($questionType) {
                // Normalize the question structure
                $normalized = [
                    'type' => $questionType,
                    'question' => $q['question'] ?? '',
                    'points' => (float) ($q['points'] ?? $q['marks'] ?? 1),
                ];

                // Handle options for multiple choice and true/false
                if (in_array($questionType, ['multiple_choice', 'true_false'])) {
                    $normalized['options'] = $q['options'] ?? [];
                    $normalized['answer'] = strtoupper($q['correct_answer'] ?? $q['answer'] ?? '');
                }

                // Handle essay and short answer
                if (in_array($questionType, ['essay', 'short_answer'])) {
                    $normalized['answer'] = $q['answer'] ?? $q['expected_answer'] ?? '';
                }

                // Add optional fields
                if (!empty($q['explanation'])) {
                    $normalized['explanation'] = $q['explanation'];
                }

                return $normalized;
            })
            ->filter(fn($q) => !empty($q['question']))
            ->values()
            ->all();

        } catch (\Exception $e) {
            Log::error('Failed to parse AI response', [
                'error' => $e->getMessage(),
                'response_preview' => substr($response, 0, 500),
            ]);

            return [];
        }
    }

    /**
     * Public method to extract questions from raw text (for direct use)
     */
    public function extractFromText(string $text, string $questionType = 'multiple_choice', int $count = 10): array
    {
        return $this->processDocumentContent($text, $questionType, $count);
    }
}
