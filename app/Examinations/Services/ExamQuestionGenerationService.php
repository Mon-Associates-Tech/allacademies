<?php

namespace App\Examinations\Services;

use App\Services\AcademicChatService;
use App\Services\PdfContentExtractionService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpWord\IOFactory;
use Smalot\PdfParser\Parser as PdfParser;

class ExamQuestionGenerationService
{
    public function __construct(
        private readonly AcademicChatService $chatService,
        private readonly PdfContentExtractionService $pdfService
    ) {}

    public function generateFromDocument(UploadedFile $file, string $questionType, int $count): array
    {
        $content = $this->extractContent($file);
        
        if (empty($content)) {
            return [];
        }

        return $this->generateQuestionsFromContent($content, $questionType, $count);
    }

    public function generateQuestionsFromContent(string $content, string $questionType, int $count): array
    {
        $prompt = $this->buildPrompt($content, $questionType, $count);
        
        try {
            $response = $this->chatService->sendMessage($prompt, [
                'temperature' => 0.7,
                'max_tokens' => 3000,
            ]);

            return $this->parseAiResponse($response, $questionType);
        } catch (\Exception $e) {
            Log::error('AI question generation failed', [
                'error' => $e->getMessage(),
                'question_type' => $questionType,
                'count' => $count,
            ]);
            
            return [];
        }
    }

    private function extractContent(UploadedFile $file): string
    {
        $extension = strtolower($file->getClientOriginalExtension());

        return match ($extension) {
            'txt', 'md' => file_get_contents($file->getRealPath()),
            'pdf' => $this->extractPdfContent($file),
            'doc', 'docx' => $this->extractDocxContent($file),
            default => '',
        };
    }

    private function extractPdfContent(UploadedFile $file): string
    {
        try {
            $parser = new PdfParser();
            $pdf = $parser->parseFile($file->getRealPath());
            return $pdf->getText();
        } catch (\Exception $e) {
            Log::error('PDF extraction failed', ['error' => $e->getMessage()]);
            return '';
        }
    }

    private function extractDocxContent(UploadedFile $file): string
    {
        try {
            $phpWord = IOFactory::load($file->getRealPath());
            $text = '';

            foreach ($phpWord->getSections() as $section) {
                foreach ($section->getElements() as $element) {
                    if (method_exists($element, 'getText')) {
                        $text .= $element->getText() . "\n";
                    } elseif (method_exists($element, 'getElements')) {
                        foreach ($element->getElements() as $childElement) {
                            if (method_exists($childElement, 'getText')) {
                                $text .= $childElement->getText() . "\n";
                            }
                        }
                    }
                }
            }

            return trim($text);
        } catch (\Exception $e) {
            Log::error('DOCX extraction failed', ['error' => $e->getMessage()]);
            return '';
        }
    }

    private function buildPrompt(string $content, string $questionType, int $count): string
    {
        $typeInstructions = match ($questionType) {
            'multiple_choice' => 'Generate multiple choice questions with 4-5 options (A, B, C, D, E). Indicate the correct answer.',
            'true_false' => 'Generate true/false questions. Indicate whether the statement is true or false.',
            'essay' => 'Generate essay questions that require detailed explanations. Provide a sample answer or key points.',
            'short_answer' => 'Generate short answer questions. Provide the expected answer.',
            default => 'Generate appropriate questions based on the content.',
        };

        return <<<PROMPT
Based on the following content, generate exactly {$count} {$questionType} questions.

{$typeInstructions}

Format your response as a JSON array with this structure:
[
  {
    "question": "Question text here",
    "options": ["Option A", "Option B", "Option C", "Option D"], // Only for multiple choice
    "correct_answer": "A", // For multiple choice and true/false
    "explanation": "Brief explanation", // Optional
    "marks": 2 // Suggested marks
  }
]

Content:
{$content}

Return ONLY the JSON array, no additional text.
PROMPT;
    }

    private function parseAiResponse(string $response, string $questionType): array
    {
        $response = trim($response);
        
        if (str_starts_with($response, '```json')) {
            $response = preg_replace('/^```json\s*/', '', $response);
            $response = preg_replace('/\s*```$/', '', $response);
        }

        try {
            $questions = json_decode($response, true, 512, JSON_THROW_ON_ERROR);
            
            if (!is_array($questions)) {
                return [];
            }

            return collect($questions)->map(function ($q) use ($questionType) {
                return [
                    'type' => $questionType,
                    'question' => $q['question'] ?? '',
                    'options' => $q['options'] ?? [],
                    'correct_answer' => $q['correct_answer'] ?? '',
                    'explanation' => $q['explanation'] ?? '',
                    'marks' => $q['marks'] ?? 2,
                    'ai_generated' => true,
                ];
            })->filter(fn($q) => !empty($q['question']))->values()->all();
            
        } catch (\Exception $e) {
            Log::error('Failed to parse AI response', [
                'error' => $e->getMessage(),
                'response' => $response,
            ]);
            
            return [];
        }
    }
}
