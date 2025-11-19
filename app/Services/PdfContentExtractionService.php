<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use PhpOffice\PhpWord\IOFactory;
use Smalot\PdfParser\Exception\MissingCatalogException;
use Smalot\PdfParser\Parser;
use Imagick;

class PdfContentExtractionService
{
    /**
     * Extract text content from a PDF file
     *
     * @param string $filePath Full path to the PDF file
     * @param array $options Extraction options
     * @return string Extracted text content
     */
    public function extractText(string $filePath, array $options = []): string
    {
        $method = $options['method'] ?? 'auto';

        return match ($method) {
            'pdftotext' => $this->extractUsingPdfToText($filePath, $options),
            'parser' => $this->extractUsingParser($filePath, $options),
            'auto' => $this->extractWithFallback($filePath, $options),
            default => throw new \InvalidArgumentException("Unknown extraction method: {$method}"),
        };
    }

    /**
     * Extract text from a page range
     *
     * @param string $filePath Full path to the PDF file
     * @param int $pageStart Starting page (1-indexed)
     * @param int $pageEnd Ending page (1-indexed)
     * @param array $options Extraction options
     * @return string Extracted text content
     * @throws MissingCatalogException
     */
    public function extractPageRange(string $filePath, int $pageStart, int $pageEnd, array $options = []): string
    {
        if (!file_exists($filePath)) {
            throw new \RuntimeException("PDF file not found: {$filePath}");
        }

        $parser = new Parser();
        $pdf = $parser->parseFile($filePath);
        $pages = $pdf->getPages();

        if (empty($pages)) {
            Log::warning("No pages found in PDF: {$filePath}");
            return '';
        }

        $text = '';
        for ($i = $pageStart - 1; $i < $pageEnd && isset($pages[$i]); $i++) {
            $text .= $pages[$i]->getText() . "\n";
        }

        return $this->cleanText($text, $options);
    }

    /**
     * Extract text from specific pages
     *
     * @param string $filePath Full path to the PDF file
     * @param array $pageNumbers Array of page numbers (1-indexed)
     * @param array $options Extraction options
     * @return array Array of page numbers => text content
     * @throws MissingCatalogException
     */
    public function extractPages(string $filePath, array $pageNumbers, array $options = []): array
    {
        if (!file_exists($filePath)) {
            throw new \RuntimeException("PDF file not found: {$filePath}");
        }

        $parser = new Parser();
        $pdf = $parser->parseFile($filePath);
        $pages = $pdf->getPages();

        if (empty($pages)) {
            Log::warning("No pages found in PDF: {$filePath}");
            return [];
        }

        $result = [];
        foreach ($pageNumbers as $pageNum) {
            $index = $pageNum - 1;
            if (isset($pages[$index])) {
                $text = $pages[$index]->getText();
                $result[$pageNum] = $this->cleanText($text, $options);
            } else {
                $result[$pageNum] = '';
            }
        }

        return $result;
    }

    /**
     * Get total page count of a PDF
     *
     * @param string $filePath Full path to the PDF file
     * @return int Total number of pages
     * @throws MissingCatalogException
     */
    public function getPageCount(string $filePath): int
    {
        if (!file_exists($filePath)) {
            throw new \RuntimeException("PDF file not found: {$filePath}");
        }

        $parser = new Parser();
        $pdf = $parser->parseFile($filePath);
        $pages = $pdf->getPages();

        return count($pages);
    }

    /**
     * Convert PDF pages to images
     *
     * @param string $filePath Full path to the PDF file
     * @param array $options Conversion options
     * @return array Array of relative paths to generated images
     * @throws \ImagickException
     */
    public function convertPagesToImages(string $filePath, array $options = []): array
    {
        if (!file_exists($filePath)) {
            throw new \RuntimeException("PDF file not found: {$filePath}");
        }

        if (!extension_loaded('imagick')) {
            throw new \RuntimeException("Imagick extension is not installed");
        }

        $resolution = $options['resolution'] ?? 300;
        $format = $options['format'] ?? 'jpg';
        $outputDir = $options['output_dir'] ?? 'pdf_pages';

        $fullOutputDir = storage_path("app/public/{$outputDir}");

        if (!file_exists($fullOutputDir)) {
            mkdir($fullOutputDir, 0755, true);
        }

        $imagick = new Imagick();
        $imagick->setResolution($resolution, $resolution);
        $imagick->readImage($filePath);

        $images = [];
        foreach ($imagick as $i => $page) {
            $page->setImageFormat($format);
            $filename = 'pdf_page_' . $i . '.' . $format;
            $outputPath = $fullOutputDir . '/' . $filename;
            $page->writeImage($outputPath);
            $images[] = $outputDir . '/' . $filename;
        }

        $imagick->clear();
        $imagick->destroy();

        return $images;
    }

    /**
     * Extract text using pdftotext command-line tool
     *
     * @param string $filePath Full path to the PDF file
     * @param array $options Extraction options
     * @return string Extracted text content
     */
    protected function extractUsingPdfToText(string $filePath, array $options = []): string
    {
        if (!$this->isPdfToTextAvailable()) {
            throw new \RuntimeException("pdftotext command is not available");
        }

        $outputFile = tempnam(sys_get_temp_dir(), 'pdf_output');
        $layout = $options['preserve_layout'] ?? true ? '-layout' : '';

        $command = "pdftotext {$layout} " . escapeshellarg($filePath) . " " . escapeshellarg($outputFile);
        exec($command, $output, $returnCode);

        if ($returnCode !== 0 || !file_exists($outputFile)) {
            @unlink($outputFile);
            throw new \RuntimeException("pdftotext command failed with code: {$returnCode}");
        }

        $content = file_get_contents($outputFile);
        unlink($outputFile);

        return $this->cleanText($content, $options);
    }

    /**
     * Extract text using Smalot PDF Parser
     *
     * @param string $filePath Full path to the PDF file
     * @param array $options Extraction options
     * @return string Extracted text content
     * @throws \Exception
     */
    protected function extractUsingParser(string $filePath, array $options = []): string
    {
        if (!file_exists($filePath)) {
            throw new \RuntimeException("PDF file not found: {$filePath}");
        }

        $parser = new Parser();
        $pdf = $parser->parseFile($filePath);
        $text = $pdf->getText();

        return $this->cleanText($text, $options);
    }

    /**
     * Try multiple extraction methods with fallback
     *
     * @param string $filePath Full path to the PDF file
     * @param array $options Extraction options
     * @return string Extracted text content
     */
    protected function extractWithFallback(string $filePath, array $options = []): string
    {
        // Try pdftotext first (usually faster and more accurate)
        if ($this->isPdfToTextAvailable()) {
            try {
                return $this->extractUsingPdfToText($filePath, $options);
            } catch (\Exception $e) {
                Log::warning("pdftotext extraction failed, falling back to parser", [
                    'error' => $e->getMessage()
                ]);
            }
        }

        // Fallback to parser
        try {
            return $this->extractUsingParser($filePath, $options);
        } catch (\Exception $e) {
            Log::error("PDF text extraction failed", [
                'file' => $filePath,
                'error' => $e->getMessage()
            ]);
            return '';
        }
    }

    /**
     * Clean extracted text
     *
     * @param string $text Raw extracted text
     * @param array $options Cleaning options
     * @return string Cleaned text
     */
    protected function cleanText(string $text, array $options = []): string
    {
        // Convert to UTF-8
        $text = mb_convert_encoding($text, 'UTF-8', 'UTF-8');

        // Remove control characters except newlines
        $text = preg_replace('/[^\P{C}\n]+/u', '', $text);

        // Normalize whitespace (unless preserving layout)
        if (!($options['preserve_layout'] ?? false)) {
            $text = preg_replace('/\s+/', ' ', $text);
        }

        // Remove excessive newlines
        $text = preg_replace('/\n{3,}/', "\n\n", $text);

        return trim($text);
    }

    /**
     * Split text into chunks for processing
     *
     * @param string $text Text to split
     * @param int $chunkSize Maximum size of each chunk
     * @param array $options Chunking options
     * @return array Array of text chunks
     */
    public function splitIntoChunks(string $text, int $chunkSize = 800, array $options = []): array
    {
        $preserveSentences = $options['preserve_sentences'] ?? true;

        if (!$preserveSentences) {
            return array_values(array_filter(str_split($text, $chunkSize)));
        }

        // Split by sentences and group into chunks
        $sentences = preg_split('/(?<=[.!?])\s+/', $text, -1, PREG_SPLIT_NO_EMPTY);
        $chunks = [];
        $currentChunk = '';

        foreach ($sentences as $sentence) {
            if (strlen($currentChunk) + strlen($sentence) > $chunkSize) {
                if (!empty($currentChunk)) {
                    $chunks[] = trim($currentChunk);
                }
                $currentChunk = $sentence;
            } else {
                $currentChunk .= ' ' . $sentence;
            }
        }

        if (!empty($currentChunk)) {
            $chunks[] = trim($currentChunk);
        }

        return array_filter($chunks);
    }

    /**
     * Check if pdftotext command is available
     *
     * @return bool
     */
    protected function isPdfToTextAvailable(): bool
    {
        if (!function_exists('exec')) {
            return false;
        }

        $output = [];
        $returnCode = 0;
        @exec('which pdftotext 2>&1', $output, $returnCode);

        return $returnCode === 0;
    }

    /**
     * Extract content from an UploadedFile (supports PDF, DOC, DOCX, TXT)
     *
     * @param UploadedFile $file
     * @param array $options
     * @return string Extracted text content
     */
    public function extractFromUploadedFile(UploadedFile $file, array $options = []): string
    {
        $extension = strtolower($file->getClientOriginalExtension());
        $tmpPath = $file->getRealPath();

        return match($extension) {
            'pdf' => $this->extractText($tmpPath, $options),
            'txt' => file_get_contents($tmpPath),
            'doc', 'docx' => $this->extractFromDocx($tmpPath),
            default => throw new \InvalidArgumentException("Unsupported file type: {$extension}")
        };
    }

    /**
     * Extract text from DOCX files
     *
     * @param string $filePath Full path to the DOCX file
     * @return string Extracted text content
     */
    public function extractFromDocx(string $filePath): string
    {
        try {
            if (!class_exists(IOFactory::class)) {
                throw new \RuntimeException('PhpWord library not available');
            }

            $phpWord = IOFactory::load($filePath);
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
            Log::error('DOCX processing failed', [
                'error' => $e->getMessage(),
                'file' => $filePath
            ]);

            throw new \RuntimeException('Error processing document: ' . $e->getMessage());
        }
    }

}
