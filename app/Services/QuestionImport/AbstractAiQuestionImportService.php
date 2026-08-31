<?php

namespace App\Services\QuestionImport;

use App\Models\AcademicSubtopic;
use App\Models\AcademicTopic;
use App\Services\ResearchAssistantService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpWord\IOFactory as WordIOFactory;
use Smalot\PdfParser\Parser as PdfParser;

/**
 * Shared infrastructure for AI-driven question import: extracting formatted
 * content from Word/PDF, talking to the AI service, and parsing its JSON
 * response. Contains nothing about any specific question type — that lives
 * in QuestionTypeHandlerInterface implementations.
 */
abstract class AbstractAiQuestionImportService
{
    public function __construct(
        protected readonly ResearchAssistantService $chatService,
    ) {}

    /**
     * Extract HTML content from the uploaded file, preserving bold/underline
     * formatting where the underlying format supports it.
     *
     * Returns: ['html' => string, 'method' => 'docx_html'|'pdf_html'|'plain_no_formatting']
     */
    protected function extractWithFormatting(UploadedFile $file): array
    {
        $extension = strtolower($file->getClientOriginalExtension());

        return match ($extension) {
            'docx', 'doc' => $this->extractWordHtml($file),
            'pdf' => $this->extractPdfHtml($file),
            default => throw new \InvalidArgumentException("Unsupported file format: {$extension}. Only PDF and Word documents are supported."),
        };
    }

    private function extractWordHtml(UploadedFile $file): array
    {
        $phpWord = WordIOFactory::load($file->getRealPath());
        $html = '';

        foreach ($phpWord->getSections() as $section) {
            foreach ($section->getElements() as $element) {
                $html .= $this->wordElementToHtml($element);
            }
        }

        return ['html' => trim($html), 'method' => 'docx_html'];
    }

    private function wordElementToHtml($element): string
    {
        if (method_exists($element, 'getText')) {
            $text = htmlspecialchars($element->getText());
            $style = method_exists($element, 'getFontStyle') ? $element->getFontStyle() : null;

            if ($style) {
                if (method_exists($style, 'isBold') && $style->isBold()) {
                    $text = "<strong>{$text}</strong>";
                }
                if (method_exists($style, 'isItalic') && $style->isItalic()) {
                    $text = "<em>{$text}</em>";
                }
                if (method_exists($style, 'getUnderline') && $style->getUnderline() && $style->getUnderline() !== 'none') {
                    $text = "<u>{$text}</u>";
                }
            }

            return $text."\n";
        }

        if (method_exists($element, 'getElements')) {
            $html = '';
            foreach ($element->getElements() as $child) {
                $html .= $this->wordElementToHtml($child);
            }

            return $html."\n";
        }

        return '';
    }

    /**
     * PDFs lose bold formatting through pdftotext / Smalot text extraction.
     * Prefer `pdftohtml` (poppler-utils — same package as pdftotext) which
     * preserves <b>/<i> runs. Fall back to plain text if it's unavailable.
     */
    private function extractPdfHtml(UploadedFile $file): array
    {
        if ($this->isPdfToHtmlAvailable()) {
            $html = $this->extractPdfViaPdfToHtml($file->getRealPath());
            if ($html !== null && trim(strip_tags($html)) !== '') {
                return ['html' => $html, 'method' => 'pdf_html'];
            }
        }

        $parser = new PdfParser;
        $pdf = $parser->parseFile($file->getRealPath());
        $text = $pdf->getText();
        $text = preg_replace('/\n{3,}/', "\n\n", trim($text));

        return ['html' => htmlspecialchars($text), 'method' => 'plain_no_formatting'];
    }

    private function isPdfToHtmlAvailable(): bool
    {
        if (! function_exists('exec')) {
            return false;
        }

        $output = [];
        $returnCode = 0;
        @exec('which pdftohtml 2>&1', $output, $returnCode);

        return $returnCode === 0;
    }

    private function extractPdfViaPdfToHtml(string $filePath): ?string
    {
        $outputBase = tempnam(sys_get_temp_dir(), 'pdf_html_');
        @unlink($outputBase);
        $outputFile = $outputBase.'.html';

        $command = 'pdftohtml -i -s -noframes '.escapeshellarg($filePath).' '.escapeshellarg($outputBase).' 2>&1';
        exec($command, $cmdOutput, $returnCode);

        if ($returnCode !== 0 || ! file_exists($outputFile)) {
            Log::warning('pdftohtml extraction failed, falling back to plain text', [
                'output' => implode("\n", $cmdOutput),
            ]);
            @unlink($outputFile);

            return null;
        }

        $raw = file_get_contents($outputFile);
        @unlink($outputFile);

        if (preg_match('/<body[^>]*>(.*)<\/body>/is', $raw, $m)) {
            $raw = $m[1];
        }

        $allowedTags = '<b><strong><i><em><u><br><p><div>';
        $clean = strip_tags($raw, $allowedTags);
        $clean = preg_replace('/<(div|p)[^>]*>/i', "\n", $clean);
        $clean = preg_replace('/<\/(div|p)>/i', '', $clean);
        $clean = preg_replace('/\n{3,}/', "\n\n", trim($clean));

        return $clean;
    }

    /**
     * Call the AI service with a prompt and return its raw text content.
     * Throws nothing — failures are reported via the returned array's 'success' key.
     */
    protected function callAi(string $prompt): array
    {
        return $this->chatService->chat([
            'model' => 'gpt-4.1-nano',
            'input' => $prompt,
            'request_type' => 'quiz_generation',
            'creativity_level' => 0.2,
            'response_length' => 6000,
        ]);
    }

    /**
     * Parse a JSON object out of the AI's raw response text, tolerating
     * markdown code fences and stray surrounding text.
     */
    protected function parseJsonResponse(string $response): ?array
    {
        $response = trim($response);

        if (str_starts_with($response, '```')) {
            $response = preg_replace('/^```(?:json)?\s*/', '', $response);
            $response = preg_replace('/\s*```$/', '', $response);
        }

        try {
            return json_decode($response, true, 512, JSON_THROW_ON_ERROR);
        } catch (\JsonException) {
            if (preg_match('/\{(?:[^{}]|(?R))*\}/s', $response, $m)) {
                try {
                    return json_decode($m[0], true, 512, JSON_THROW_ON_ERROR);
                } catch (\JsonException) {
                    Log::warning('AI response JSON parse failed', ['preview' => substr($response, 0, 300)]);

                    return null;
                }
            }

            Log::warning('AI response had no JSON', ['preview' => substr($response, 0, 300)]);

            return null;
        }
    }

    protected function isMeaningfulContent(string $html): bool
    {
        return strlen(trim(strip_tags($html))) >= 10;
    }
}