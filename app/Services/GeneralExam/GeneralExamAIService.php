<?php

namespace App\Services\GeneralExam;

use App\ExaminationHub\Models\GeneralExamQuestion;
use App\Services\ResearchAssistantService;
use App\Services\PdfContentExtractionService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class GeneralExamAIService
{
    public function __construct(
        protected ResearchAssistantService $chatService,
        protected PdfContentExtractionService $pdfExtractor
    ) {}

    /**
     * Generate questions from uploaded document
     */
    public function generateQuestionsFromDocument(
        UploadedFile $file,
        array $parameters = []
    ): array {
        $content = $this->extractDocumentContent($file);

        if (empty($content)) {
            throw new \RuntimeException('Could not extract content from the uploaded document.');
        }

        return $this->generateQuestions($content, $parameters);
    }

    /**
     * Generate questions from text content
     */
    public function generateQuestions(string $content, array $parameters = []): array
    {
        $questionTypes = $parameters['question_types'] ?? [
            'multiple_choice' => 5,
            'true_false' => 3,
            'short_answer' => 2,
            'essay' => 1,
        ];

        $difficulty = $parameters['difficulty'] ?? 'medium';
        $focusTopics = $parameters['focus_topics'] ?? [];
        $totalQuestions = array_sum($questionTypes);

        $prompt = $this->buildQuestionGenerationPrompt($content, $questionTypes, $difficulty, $focusTopics);

        try {
            $response = $this->chatService->chat([
                'input' => $prompt,
                'request_type' => 'quiz_generation',
                'creativity_level' => 0.7,
                'response_length' => 4000,
            ]);

            $content = $response['content'] ?? '';

            return $this->parseGeneratedQuestions($content, $questionTypes);
        } catch (\Exception $e) {
            Log::error('AI question generation failed', [
                'error' => $e->getMessage(),
                'parameters' => $parameters,
            ]);
            throw new \RuntimeException('Failed to generate questions: '.$e->getMessage());
        }
    }

    /**
     * Generate questions for a specific section
     */
    public function generateSectionQuestions(
        string $content,
        string $sectionTitle,
        string $sectionDescription,
        array $questionTypes,
        string $difficulty = 'medium'
    ): array {
        $prompt = $this->buildSectionQuestionPrompt(
            $content,
            $sectionTitle,
            $sectionDescription,
            $questionTypes,
            $difficulty
        );

        try {
            $response = $this->chatService->chat([
                'input' => $prompt,
                'request_type' => 'quiz_generation',
                'creativity_level' => 0.7,
                'response_length' => 3000,
            ]);

            $content = $response['content'] ?? '';

            return $this->parseGeneratedQuestions($content, $questionTypes);
        } catch (\Exception $e) {
            Log::error('AI section question generation failed', [
                'error' => $e->getMessage(),
                'section' => $sectionTitle,
            ]);
            throw new \RuntimeException('Failed to generate section questions: '.$e->getMessage());
        }
    }

    /**
     * Regenerate a single question
     */
    public function regenerateQuestion(
        GeneralExamQuestion $question,
        ?string             $additionalContext = null
    ): array {
        $assignment = $question->assignment;
        $context = $additionalContext ?? $assignment->description ?? '';

        $prompt = $this->buildSingleQuestionPrompt(
            $question->type,
            $question->difficulty,
            $context,
            $question->question // Original question for reference
        );

        try {
            $response = $this->chatService->chat([
                'input' => $prompt,
                'request_type' => 'quiz_generation',
                'creativity_level' => 0.8,
                'response_length' => 1000,
            ]);

            $content = $response['content'] ?? '';

            $questions = $this->parseGeneratedQuestions($content, [$question->type => 1]);

            return $questions[0] ?? throw new \RuntimeException('No question generated');
        } catch (\Exception $e) {
            Log::error('AI question regeneration failed', [
                'question_id' => $question->id,
                'error' => $e->getMessage(),
            ]);
            throw new \RuntimeException('Failed to regenerate question: '.$e->getMessage());
        }
    }

    /**
     * Generate a complete assignment with sections
     */
    public function generateCompleteAssignment(
        string $content,
        array $sectionConfigs,
        array $globalSettings = []
    ): array {
        $sections = [];

        foreach ($sectionConfigs as $index => $config) {
            $sectionQuestions = $this->generateSectionQuestions(
                $content,
                $config['title'],
                $config['description'] ?? '',
                $config['question_types'] ?? ['multiple_choice' => 5],
                $config['difficulty'] ?? $globalSettings['difficulty'] ?? 'medium'
            );

            $sections[] = [
                'title' => $config['title'],
                'description' => $config['description'] ?? null,
                'instructions' => $config['instructions'] ?? null,
                'order' => $index,
                'time_limit_minutes' => $config['time_limit_minutes'] ?? null,
                'questions' => $sectionQuestions,
            ];
        }

        return $sections;
    }

    /**
     * Extract content from uploaded document
     */
    protected function extractDocumentContent(UploadedFile $file): string
    {
        $extension = strtolower($file->getClientOriginalExtension());

        return match ($extension) {
            'pdf' => $this->extractPdfContent($file),
            'txt' => file_get_contents($file->getRealPath()),
            'doc', 'docx' => $this->extractWordContent($file),
            default => throw new \RuntimeException("Unsupported file type: {$extension}"),
        };
    }

    /**
     * Extract content from PDF
     */
    protected function extractPdfContent(UploadedFile $file): string
    {
        try {
            return $this->pdfExtractor->extractText($file->getRealPath());
        } catch (\Exception $e) {
            Log::warning('PDF extraction failed, trying alternative method', [
                'error' => $e->getMessage(),
            ]);

            // Fallback: try basic extraction
            $content = shell_exec("pdftotext -layout \"{$file->getRealPath()}\" -");

            return $content ?: '';
        }
    }

    /**
     * Extract content from Word document
     */
    protected function extractWordContent(UploadedFile $file): string
    {
        $extension = strtolower($file->getClientOriginalExtension());

        if ($extension === 'docx') {
            $zip = new \ZipArchive;
            if ($zip->open($file->getRealPath()) === true) {
                $content = $zip->getFromName('word/document.xml');
                $zip->close();

                if ($content) {
                    // Strip XML tags and clean up
                    $content = strip_tags($content);
                    $content = preg_replace('/\s+/', ' ', $content);

                    return trim($content);
                }
            }
        }

        // Fallback for .doc files or if .docx extraction fails
        $content = shell_exec("antiword \"{$file->getRealPath()}\" 2>/dev/null");

        return $content ?: '';
    }

    /**
     * Build prompt for question generation
     */
    protected function buildQuestionGenerationPrompt(
        string $content,
        array $questionTypes,
        string $difficulty,
        array $focusTopics
    ): string {
        $typeInstructions = $this->buildTypeInstructions($questionTypes);
        $focusTopicsStr = ! empty($focusTopics) ? 'Focus on these topics: '.implode(', ', $focusTopics) : '';

        $contentPreview = substr($content, 0, 8000); // Limit content length

        return <<<PROMPT
You are an expert educational content creator. Generate assessment questions based on the following content.

CONTENT:
{$contentPreview}

REQUIREMENTS:
- Difficulty level: {$difficulty}
{$focusTopicsStr}

QUESTION TYPES TO GENERATE:
{$typeInstructions}

For each question, provide the response in this exact JSON format:
{
    "questions": [
        {
            "type": "multiple_choice|true_false|short_answer|essay",
            "question": "The question text",
            "options": {"A": "Option A", "B": "Option B", "C": "Option C", "D": "Option D"}, // Only for multiple_choice
            "correct_answer": "A", // Letter for MCQ, "true"/"false" for T/F, expected answer for short_answer
            "explanation": "Why this is the correct answer",
            "keywords": ["keyword1", "keyword2"], // For short_answer and essay
            "grading_rubric": "Rubric for grading", // For essay questions
            "marks": 1, // Points for this question
            "difficulty": "easy|medium|hard"
        }
    ]
}

IMPORTANT:
- Ensure questions are clear and unambiguous
- Multiple choice questions should have exactly 4 options (A, B, C, D)
- True/False questions should have clear true or false answers
- Short answer questions should have specific expected answers
- Essay questions should have clear grading rubrics
- All questions should be directly related to the provided content
- Vary the difficulty within the specified level

Generate the questions now:
PROMPT;
    }

    /**
     * Build prompt for section-specific questions
     */
    protected function buildSectionQuestionPrompt(
        string $content,
        string $sectionTitle,
        string $sectionDescription,
        array $questionTypes,
        string $difficulty
    ): string {
        $typeInstructions = $this->buildTypeInstructions($questionTypes);
        $contentPreview = substr($content, 0, 6000);

        return <<<PROMPT
You are an expert educational content creator. Generate questions for a specific section of an assessment.

SECTION: {$sectionTitle}
SECTION DESCRIPTION: {$sectionDescription}

CONTENT TO BASE QUESTIONS ON:
{$contentPreview}

REQUIREMENTS:
- Difficulty level: {$difficulty}
- Questions should specifically relate to the section topic

QUESTION TYPES TO GENERATE:
{$typeInstructions}

Provide the response in this exact JSON format:
{
    "questions": [
        {
            "type": "multiple_choice|true_false|short_answer|essay",
            "question": "The question text",
            "options": {"A": "Option A", "B": "Option B", "C": "Option C", "D": "Option D"},
            "correct_answer": "A",
            "explanation": "Why this is the correct answer",
            "keywords": ["keyword1", "keyword2"],
            "grading_rubric": "Rubric for grading",
            "marks": 1,
            "difficulty": "easy|medium|hard"
        }
    ]
}

Generate the questions now:
PROMPT;
    }

    /**
     * Build prompt for single question regeneration
     */
    protected function buildSingleQuestionPrompt(
        string $type,
        string $difficulty,
        string $context,
        string $originalQuestion
    ): string {
        $typeDescription = match ($type) {
            'multiple_choice' => 'a multiple choice question with 4 options (A, B, C, D)',
            'true_false' => 'a true/false question',
            'short_answer' => 'a short answer question',
            'essay' => 'an essay question with grading rubric',
            default => 'a question',
        };

        return <<<PROMPT
You are an expert educational content creator. Generate a new {$typeDescription} to replace an existing one.

CONTEXT:
{$context}

ORIGINAL QUESTION (for reference, create something different):
{$originalQuestion}

REQUIREMENTS:
- Difficulty level: {$difficulty}
- Create a completely different question on a related topic
- Maintain the same question type

Provide the response in this exact JSON format:
{
    "questions": [
        {
            "type": "{$type}",
            "question": "The question text",
            "options": {"A": "Option A", "B": "Option B", "C": "Option C", "D": "Option D"},
            "correct_answer": "A",
            "explanation": "Why this is the correct answer",
            "keywords": ["keyword1", "keyword2"],
            "grading_rubric": "Rubric for grading",
            "marks": 1,
            "difficulty": "{$difficulty}"
        }
    ]
}

Generate the question now:
PROMPT;
    }

    /**
     * Build type instructions for prompt
     */
    protected function buildTypeInstructions(array $questionTypes): string
    {
        $instructions = [];

        foreach ($questionTypes as $type => $count) {
            if ($count > 0) {
                $typeName = str_replace('_', ' ', $type);
                $instructions[] = "- {$count} {$typeName} question(s)";
            }
        }

        return implode("\n", $instructions);
    }

    /**
     * Parse AI-generated questions response
     */
    protected function parseGeneratedQuestions(string $response, array $expectedTypes): array
    {
        try {
            // Extract JSON from response
            $jsonMatch = [];
            if (preg_match('/\{[\s\S]*\}/', $response, $jsonMatch)) {
                $parsed = json_decode($jsonMatch[0], true);

                if ($parsed && isset($parsed['questions']) && is_array($parsed['questions'])) {
                    return $this->normalizeQuestions($parsed['questions']);
                }
            }

            // Try to parse as array directly
            $parsed = json_decode($response, true);
            if ($parsed && isset($parsed['questions'])) {
                return $this->normalizeQuestions($parsed['questions']);
            }

        } catch (\Exception $e) {
            Log::warning('Failed to parse AI question response', [
                'error' => $e->getMessage(),
            ]);
        }

        // Fallback: try manual parsing
        return $this->manualParseQuestions($response, $expectedTypes);
    }

    /**
     * Normalize questions to consistent format
     */
    protected function normalizeQuestions(array $questions): array
    {
        $normalized = [];

        foreach ($questions as $index => $question) {
            $type = $question['type'] ?? 'multiple_choice';

            $normalizedQuestion = [
                'type' => $this->normalizeQuestionType($type),
                'question' => $question['question'] ?? '',
                'explanation' => $question['explanation'] ?? null,
                'marks' => (int) ($question['marks'] ?? 1),
                'difficulty' => $question['difficulty'] ?? 'medium',
                'ai_generated' => true,
                'order' => $index,
            ];

            // Handle options for multiple choice
            if ($normalizedQuestion['type'] === 'multiple_choice') {
                $normalizedQuestion['options'] = $this->normalizeOptions($question['options'] ?? []);
                $normalizedQuestion['correct_answer'] = strtoupper($question['correct_answer'] ?? 'A');
            } elseif ($normalizedQuestion['type'] === 'true_false') {
                $normalizedQuestion['options'] = ['A' => 'True', 'B' => 'False'];
                $answer = strtolower($question['correct_answer'] ?? 'true');
                $normalizedQuestion['correct_answer'] = in_array($answer, ['true', 't', '1', 'yes']) ? 'true' : 'false';
            } else {
                $normalizedQuestion['correct_answer'] = $question['correct_answer'] ?? null;
                $normalizedQuestion['keywords'] = $question['keywords'] ?? [];
                $normalizedQuestion['grading_rubric'] = $question['grading_rubric'] ?? null;
            }

            $normalized[] = $normalizedQuestion;
        }

        return $normalized;
    }

    /**
     * Normalize question type string
     */
    protected function normalizeQuestionType(string $type): string
    {
        $type = strtolower(str_replace([' ', '-'], '_', $type));

        return match ($type) {
            'multiple_choice', 'mcq', 'mc' => 'multiple_choice',
            'true_false', 'tf', 'truefalse' => 'true_false',
            'short_answer', 'short', 'sa' => 'short_answer',
            'essay', 'long_answer', 'la' => 'essay',
            default => 'multiple_choice',
        };
    }

    /**
     * Normalize options format
     */
    protected function normalizeOptions(array|string $options): array
    {
        if (is_string($options)) {
            return ['A' => $options];
        }

        $normalized = [];
        $letters = ['A', 'B', 'C', 'D', 'E'];
        $index = 0;

        foreach ($options as $key => $value) {
            if (is_numeric($key)) {
                $normalized[$letters[$index] ?? chr(65 + $index)] = $value;
            } else {
                $normalized[strtoupper($key)] = $value;
            }
            $index++;
        }

        return $normalized;
    }

    /**
     * Manual parsing fallback
     */
    protected function manualParseQuestions(string $response, array $expectedTypes): array
    {
        $questions = [];

        // Try to extract questions using patterns
        $patterns = [
            '/Question\s*\d*[:.]\s*(.+?)(?=Question\s*\d*[:.:]|$)/si',
            '/\d+[.)]\s*(.+?)(?=\d+[.)]|$)/si',
        ];

        foreach ($patterns as $pattern) {
            if (preg_match_all($pattern, $response, $matches)) {
                foreach ($matches[1] as $index => $match) {
                    $questions[] = [
                        'type' => 'multiple_choice',
                        'question' => trim($match),
                        'options' => ['A' => 'Option A', 'B' => 'Option B', 'C' => 'Option C', 'D' => 'Option D'],
                        'correct_answer' => 'A',
                        'marks' => 1,
                        'difficulty' => 'medium',
                        'ai_generated' => true,
                        'order' => $index,
                    ];
                }
                break;
            }
        }

        return $questions;
    }

    /**
     * Store uploaded document and return path
     */
    public function storeDocument(UploadedFile $file): string
    {
        $path = $file->store('general-exam-documents', 'local');

        return $path;
    }

    /**
     * Get stored document content
     */
    public function getStoredDocumentContent(string $path): string
    {
        if (! Storage::disk('local')->exists($path)) {
            throw new \RuntimeException('Document not found');
        }

        $fullPath = Storage::disk('local')->path($path);
        $extension = pathinfo($fullPath, PATHINFO_EXTENSION);

        return match (strtolower($extension)) {
            'pdf' => $this->pdfExtractor->extractText($fullPath),
            'txt' => Storage::disk('local')->get($path),
            default => '',
        };
    }
}
