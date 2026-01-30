<?php

namespace App\Services;

use Imagick;
use PhpOffice\PhpWord\IOFactory;

class ExaminationSectionProcessor
{
    public function processAllSections(array $sections): array
    {
        return array_map(function ($section) {
            if (! isset($section['document'])) {
                return $section; // Return unmodified section if no document
            }

            return $this->processSection($section);
        }, $sections);
    }

    private function processSection(array $section): array
    {
        $processedSection = $section; // Keep all original section data

        if (! isset($section['document'])) {
            return $processedSection;
        }

        $path = storage_path('app/public/'.$section['document']);
        $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));

        $processedSection['extension'] = $ext;
        $processedSection['original_path'] = $section['document'];
        $processedSection['pdf_images'] = [];

        // Only process if file exists
        if (! file_exists($path)) {
            return $processedSection;
        }

        // Process Word documents
        if (in_array($ext, ['doc', 'docx'])) {
            // try {
            $processedSection['document'] = $this->processWordDocument($path);
            //            } catch (\Exception $e) {
            //                // Keep original document if processing fails
            //                $processedSection['document'] = $section['document'];
            //            }
        }

        // Process PDF documents
        if ($ext === 'pdf') {
            //  try {
            $processedSection['pdf_images'] = $this->processPdfDocument($path);
            //  } catch (\Exception $e) {
            // //  $processedSection['pdf_images'] = [];
            //  }
        }

        return $processedSection;
    }

    private function processWordDocument(string $path): string
    {
        $phpWord = IOFactory::load($path, 'Word2007');
        $docxText = '';

        foreach ($phpWord->getSections() as $section) {
            foreach ($section->getElements() as $element) {
                if (method_exists($element, 'getText')) {
                    $docxText .= $element->getText()."\n";
                }
            }
        }

        return $docxText;
    }

    private function processPdfDocumentDep(string $path): array
    {
        $outputDir = storage_path('app/public/pdf_pages');

        if (! file_exists($outputDir)) {
            mkdir($outputDir, 0755, true);
        }

        $imagick = new Imagick;
        $imagick->setResolution(300, 300);
        $imagick->readImage($path);

        $images = [];
        foreach ($imagick as $i => $page) {
            $page->setImageFormat('jpg');
            $filename = 'pdf_page_'.$i.'.jpg';
            $outputPath = $outputDir.'/'.$filename;
            $page->writeImage($outputPath);
            $images[] = 'pdf_pages/'.$filename;
        }

        return $images;
    }

    private function processPdfDocument(string $path): array
    {
        try {
            $pdfExtractor = app(PdfContentExtractionService::class);

            return $pdfExtractor->convertPagesToImages($path, [
                'resolution' => 300,
                'format' => 'jpg',
                'output_dir' => 'pdf_pages',
            ]);
        } catch (\Exception $e) {
            Log::error("PDF to image conversion failed: {$e->getMessage()}");

            return [];
        }
    }
}
