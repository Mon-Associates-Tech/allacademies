<?php

namespace App\Examinations\Services;

use App\Services\DocumentQuestionExtractionService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpWord\IOFactory;
use Smalot\PdfParser\Parser as PdfParser;

class ExamQuestionGenerationService
{
    public function __construct(
        private readonly DocumentQuestionExtractionService $extractionService
    ) {}

    public function generateFromDocument(UploadedFile $file, string $questionType, int $count): array
    {
        $content = $this->extractContent($file);
        
        if (empty($content)) {
            return [];
        }

        return $this->generateQuestionsFromContent($content, $questionType, $count);
    }

    public function generateFromSubject(int $subjectId, array $topicIds, string $questionType, int $count): array
    {
        $subject = \App\Models\AcademicSubject::find($subjectId);
        
        if (!$subject) {
            return [];
        }

        $topicNames = [];
        if (!empty($topicIds)) {
            $topicNames = \App\Models\AcademicTopic::whereIn('id', $topicIds)
                ->pluck('name')
                ->toArray();
        }

        $content = "Subject: {$subject->name}";
        if (!empty($topicNames)) {
            $content .= "\nTopics: " . implode(', ', $topicNames);
        }
        $content .= "\n\nGenerate questions covering the key concepts and important topics in this subject area.";

        return $this->generateQuestionsFromContent($content, $questionType, $count);
    }

    public function generateQuestionsFromContent(string $content, string $questionType, int $count): array
    {
        return $this->extractionService->processDocumentContent($content, $questionType, $count);
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
                $text .= $this->extractElementsText($section->getElements());
            }

            return trim($text);
        } catch (\Exception $e) {
            Log::error('DOCX extraction failed', ['error' => $e->getMessage()]);
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
}
