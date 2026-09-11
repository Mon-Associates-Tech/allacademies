<?php

namespace App\MockExam\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use PhpOffice\PhpWord\IOFactory;

class MockExamAttachmentService
{
    private const IMAGE_EXTENSIONS = ['jpg', 'jpeg', 'png'];

    /**
     * Normalise an uploaded section attachment into storable/renderable data.
     *
     * @return array{
     *   attachment_original_name: string,
     *   attachment_extension: string,
     *   attachment_text: ?string,
     *   attachment_image_path: ?string,
     *   attachment_pdf_images: ?array,
     * }
     */
    public function process(UploadedFile $file): array
    {
        $extension = strtolower($file->getClientOriginalExtension());
        $original  = $file->getClientOriginalName();

        return match (true) {
            $extension === 'txt'  => $this->processText($file, $original, $extension),
            $extension === 'docx' => $this->processDocx($file, $original, $extension),
            $extension === 'pdf'  => $this->processPdf($file, $original, $extension),
            in_array($extension, self::IMAGE_EXTENSIONS, true) => $this->processImage($file, $original, $extension),
            default => throw new \InvalidArgumentException("Unsupported attachment type: {$extension}"),
        };
    }

    private function processText(UploadedFile $file, string $original, string $extension): array
    {
        return $this->result($original, $extension, text: file_get_contents($file->getRealPath()));
    }

    private function processDocx(UploadedFile $file, string $original, string $extension): array
    {
        $phpWord = IOFactory::load($file->getRealPath());
        $lines   = [];

        foreach ($phpWord->getSections() as $section) {
            foreach ($section->getElements() as $element) {
                if (method_exists($element, 'getText')) {
                    $lines[] = $element->getText();
                } elseif (method_exists($element, 'getElements')) {
                    foreach ($element->getElements() as $inner) {
                        if (method_exists($inner, 'getText')) {
                            $lines[] = $inner->getText();
                        }
                    }
                }
            }
        }

        return $this->result($original, $extension, text: implode("\n", array_filter($lines)));
    }

    private function processPdf(UploadedFile $file, string $original, string $extension): array
    {
        $imagick = new \Imagick();
        $imagick->setResolution(150, 150);
        $imagick->readImage($file->getRealPath());

        $paths = [];

        foreach ($imagick as $index => $page) {
            $page->setImageFormat('jpg');
            $page->setImageCompressionQuality(85);

            $path = 'mock-exam-attachments/'.Str::uuid()."-page-{$index}.jpg";
            Storage::disk('public')->put($path, $page->getImageBlob());
            $paths[] = $path;
        }

        $imagick->clear();

        return $this->result($original, $extension, pdfImages: $paths);
    }

    private function processImage(UploadedFile $file, string $original, string $extension): array
    {
        $path = $file->store('mock-exam-attachments', 'public');

        return $this->result($original, $extension, imagePath: $path);
    }

    private function result(
        string $original,
        string $extension,
        ?string $text = null,
        ?string $imagePath = null,
        ?array $pdfImages = null,
    ): array {
        return [
            'attachment_original_name' => $original,
            'attachment_extension'     => $extension,
            'attachment_text'          => $text,
            'attachment_image_path'    => $imagePath,
            'attachment_pdf_images'    => $pdfImages,
        ];
    }
}