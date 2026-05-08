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
}
