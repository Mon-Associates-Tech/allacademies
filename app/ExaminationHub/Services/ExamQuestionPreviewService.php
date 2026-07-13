<?php

namespace App\ExaminationHub\Services;

use App\Models\EssayQuestion;
use App\Models\MultipleChoiceQuestion;
use App\Models\TrueOrFalseQuestion;
use App\Support\Mark;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class ExamQuestionPreviewService
{
    public function __construct(
        private readonly ExamQuestionGenerationService $generationService
    ) {}

    public function generateForSections(array $sections, bool $hardenedMode = false): array
    {
        Log::info('generateForSections called', [
            'sections_count' => count($sections),
            'sections_data' => $sections,
        ]);

        return collect($sections)->map(function (array $section, int $index) use ($hardenedMode) {
            if ($hardenedMode) {
                return $this->getPlaceholderForHardenedMode($section);
            }

            return match ($section['source_type'] ?? null) {
                'database' => $this->generateFromDatabase($section),
                'ai' => $this->generateFromAi($section),
                'mixed' => $this->generateMixed($section),
                'manual' => $this->getManualPlaceholder($section),
                default => [],
            };
        })->all();
    }

    private function generateFromDatabase(array $section): array
    {
        $count = (int) ($section['question_count'] ?? 0);
        $subjectId = $section['academic_subject_id'] ?? null;
        $topicIds = collect($section['topic_ids'] ?? [])->filter()->map(fn ($v) => (int) $v)->all();
        $subtopicIds = collect($section['subtopic_ids'] ?? [])->filter()->map(fn ($v) => (int) $v)->all();
        $questionType = $section['question_type'] ?? 'multiple_choice';

        if ($count <= 0 || ! $subjectId) {
            return [];
        }

        if ($questionType === 'mixed') {
            $third = max(1, (int) floor($count / 3));
            $mcq = $this->fetchMcq($subjectId, $topicIds, $subtopicIds, $third);
            $tof = $this->fetchTof($subjectId, $topicIds, $subtopicIds, $third);
            $essay = $this->fetchEssay($subjectId, $topicIds, $subtopicIds, $count - ($third * 2));

            return collect([$mcq, $tof, $essay])->flatten(1)->take($count)->values()->all();
        }

        return match ($questionType) {
            'multiple_choice' => $this->fetchMcq($subjectId, $topicIds, $subtopicIds, $count),
            'true_false' => $this->fetchTof($subjectId, $topicIds, $subtopicIds, $count),
            'short_answer', 'essay' => $this->fetchEssay($subjectId, $topicIds, $subtopicIds, $count),
            default => [],
        };
    }

    private function generateFromAi(array $section): array
    {
        $questionType = $section['question_type'] ?? 'multiple_choice';
        $count = (int) ($section['question_count'] ?? 0);
        $subjectId = $section['academic_subject_id'] ?? null;
        $topicIds = collect($section['topic_ids'] ?? [])->filter()->map(fn ($v) => (int) $v)->all();

        Log::info('generateFromAi called', [
            'has_document_path' => !empty($section['document_path']),
            'document_path' => $section['document_path'] ?? null,
            'document_name' => $section['document_name'] ?? null,
            'file_exists' => !empty($section['document_path']) && file_exists($section['document_path']),
            'count' => $count,
            'question_type' => $questionType,
        ]);

        if ($count <= 0) {
            Log::warning('AI generation skipped: count is 0');
            return [];
        }

        // Check if document path is provided (from Livewire upload)
        if (!empty($section['document_path']) && file_exists($section['document_path'])) {
            Log::info('Attempting to extract content from document path');
            try {
                $content = $this->extractContentFromPath($section['document_path'], $section['document_name'] ?? 'document.txt');
                Log::info('Content extracted', ['content_length' => strlen($content), 'preview' => substr($content, 0, 200)]);

                if (!empty($content)) {
                    $questions = $this->generationService->generateQuestionsFromContent($content, $questionType, $count);
                    Log::info('Questions generated from document', ['count' => count($questions)]);
                    return $questions;
                } else {
                    Log::warning('Extracted content is empty');
                }
            } catch (\Exception $e) {
                Log::error('AI generation from document path failed in preview', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]);
            }
        }

        // Check if document is provided as UploadedFile
        $document = $section['document'] ?? null;
        if ($document instanceof UploadedFile) {
            Log::info('Attempting to use UploadedFile');
            try {
                $questions = $this->generationService->generateFromDocument($document, $questionType, $count);
                Log::info('Questions generated from UploadedFile', ['count' => count($questions)]);
                return $questions;
            } catch (\Exception $e) {
                Log::error('AI generation from document failed in preview', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]);
            }
        }

        // Otherwise, use subject/topic-based AI generation
        if ($subjectId) {
            Log::info('Falling back to subject-based generation', ['subject_id' => $subjectId]);
            try {
                $questions = $this->generationService->generateFromSubject($subjectId, $topicIds, $questionType, $count);
                Log::info('Questions generated from subject', ['count' => count($questions)]);
                return $questions;
            } catch (\Exception $e) {
                Log::error('AI generation from subject failed in preview', [
                    'error' => $e->getMessage(),
                    'trace' => $e->getTraceAsString(),
                ]);
            }
        }

        Log::warning('No generation method succeeded, returning empty array');
        return [];
    }

    private function extractContentFromPath(string $filePath, string $fileName): string
    {
        $extension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        return match ($extension) {
            'txt', 'md' => file_get_contents($filePath),
            'pdf' => $this->extractPdfContentFromPath($filePath),
            'doc', 'docx' => $this->extractDocxContentFromPath($filePath),
            default => '',
        };
    }

    private function extractPdfContentFromPath(string $filePath): string
    {
        try {
            $parser = new \Smalot\PdfParser\Parser();
            $pdf = $parser->parseFile($filePath);
            return $pdf->getText();
        } catch (\Exception $e) {
            Log::error('PDF extraction from path failed', ['error' => $e->getMessage()]);
            return '';
        }
    }

    private function extractDocxContentFromPath(string $filePath): string
    {
        try {
            $phpWord = \PhpOffice\PhpWord\IOFactory::load($filePath);
            $text = '';

            foreach ($phpWord->getSections() as $section) {
                $text .= $this->extractElementsText($section->getElements());
            }

            return trim($text);
        } catch (\Exception $e) {
            Log::error('DOCX extraction from path failed', ['error' => $e->getMessage()]);
            return '';
        }
    }

    private function extractElementsText(array $elements): string
    {
        $text = '';

        foreach ($elements as $element) {
            $elementClass = get_class($element);

            // Handle Text elements
            if ($elementClass === 'PhpOffice\\PhpWord\\Element\\Text') {
                $text .= $element->getText() . "\n";
            }
            // Handle TextRun (contains multiple text elements with formatting)
            elseif ($elementClass === 'PhpOffice\\PhpWord\\Element\\TextRun') {
                if (method_exists($element, 'getElements')) {
                    $text .= $this->extractElementsText($element->getElements());
                }
            }
            // Handle Table elements
            elseif ($elementClass === 'PhpOffice\\PhpWord\\Element\\Table') {
                foreach ($element->getRows() as $row) {
                    foreach ($row->getCells() as $cell) {
                        $text .= $this->extractElementsText($cell->getElements());
                    }
                }
            }
            // Handle any other container elements
            elseif (method_exists($element, 'getElements')) {
                $text .= $this->extractElementsText($element->getElements());
            }
            // Fallback: try getText if available and ensure it's a string
            elseif (method_exists($element, 'getText')) {
                $textValue = $element->getText();
                if (is_string($textValue)) {
                    $text .= $textValue . "\n";
                }
            }
        }

        return $text;
    }

    private function generateMixed(array $section): array
    {
        $dbCount = (int) ($section['database_count'] ?? 0);
        $aiCount = (int) ($section['ai_count'] ?? 0);
        $manualCount = (int) ($section['manual_count'] ?? 0);

        $questions = [];

        if ($dbCount > 0) {
            $dbSection = array_merge($section, ['question_count' => $dbCount]);
            $questions = array_merge($questions, $this->generateFromDatabase($dbSection));
        }

        if ($aiCount > 0 && isset($section['document'])) {
            $aiSection = array_merge($section, ['question_count' => $aiCount]);
            $questions = array_merge($questions, $this->generateFromAi($aiSection));
        }

        if ($manualCount > 0) {
            $questions = array_merge($questions, $this->getManualPlaceholder(['question_count' => $manualCount]));
        }

        return $questions;
    }

    private function getManualPlaceholder(array $section): array
    {
        $count = (int) ($section['question_count'] ?? 0);
        return array_fill(0, $count, [
            'type' => 'manual',
            'question' => '[Manual question to be added]',
            'placeholder' => true,
        ]);
    }

    private function getPlaceholderForHardenedMode(array $section): array
    {
        $count = (int) ($section['question_count'] ?? 0);
        return [
            'hardened' => true,
            'count' => $count,
            'message' => 'Questions hidden in hardened mode',
        ];
    }

    private function fetchMcq(int $subjectId, array $topicIds, array $subtopicIds, int $count): array
    {
        $query = MultipleChoiceQuestion::query();
        $this->applyHierarchyFilters($query, $subjectId, $topicIds, $subtopicIds);

        return $query->inRandomOrder()->limit($count)->get()->map(function ($q) {
            return [
                'type'               => 'multiple_choice',
                'source_question_id' => $q->id,
                'question'           => $this->asText($q->question),
                'options'            => array_values(array_filter([$this->asText($q->option_a), $this->asText($q->option_b), $this->asText($q->option_c), $this->asText($q->option_d), $this->asText($q->option_e)])),
                'answer'             => strtoupper((string) $q->answer),
                'points'             => (float) ($q->score ?? 1),
            ];
        })->all();
    }

    private function fetchTof(int $subjectId, array $topicIds, array $subtopicIds, int $count): array
    {
        $query = TrueOrFalseQuestion::query();
        $this->applyHierarchyFilters($query, $subjectId, $topicIds, $subtopicIds);

        return $query->inRandomOrder()->limit($count)->get()->map(function ($q) {
            return [
                'type'               => 'true_false',
                'source_question_id' => $q->id,
                'question'           => $this->asText($q->question),
                'options'            => ['True', 'False'],
                'answer'             => $q->answer ? 'True' : 'False',
                'points'             => (float) ($q->score ?? 1),
            ];
        })->all();
    }

    private function fetchEssay(int $subjectId, array $topicIds, array $subtopicIds, int $count): array
    {
        $query = EssayQuestion::query();
        $this->applyHierarchyFilters($query, $subjectId, $topicIds, $subtopicIds);

        return $query->inRandomOrder()->limit($count)->get()->map(function ($q) {
            return [
                'type'               => 'essay',
                'source_question_id' => $q->id,
                'question'           => $this->asText($q->question),
                'answer'             => $this->asText($q->answer),
                'points'             => (float) ($q->score ?? 5),
            ];
        })->all();
    }

    private function applyHierarchyFilters($query, int $subjectId, array $topicIds, array $subtopicIds): void
    {
        $query->whereHas('topic', function ($q) use ($subjectId) {
            $q->where('academic_subject_id', $subjectId);
        });

        if (! empty($topicIds)) {
            $query->whereIn('academic_topic_id', $topicIds);
        }

        if (! empty($subtopicIds)) {
            $query->whereIn('academic_subtopic_id', $subtopicIds);
        }
    }

    private function asText(mixed $value): string
    {
        if ($value instanceof Mark) {
            return trim(strip_tags((string) ($value->down ?? '')));
        }

        return trim(strip_tags((string) $value));
    }
}

