<?php

namespace App\Services;

use App\Models\Note;
use Barryvdh\DomPDF\Facade\Pdf;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\Shared\Html;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class NoteExportService
{
    /**
     * Export a note to the specified format
     */
    public function export(Note $note, string $format = 'pdf'): array
    {
        return match(strtolower($format)) {
            'pdf' => $this->exportToPdf($note),
            'txt', 'text' => $this->exportToText($note),
            'docx', 'word' => $this->exportToDocx($note),
            default => throw new \InvalidArgumentException("Unsupported format: {$format}"),
        };
    }

    /**
     * Export note to PDF
     */
    public function exportToPdf(Note $note): array
    {
        try {
            $filename = $this->sanitizeFilename($note->title) . '.pdf';
            
            $pdf = Pdf::loadView('exports.note-pdf', [
                'note' => $note,
                'exportDate' => now()->format('F d, Y'),
            ]);

            // Configure PDF settings
            $pdf->setPaper('a4', 'portrait');
            $pdf->setOptions([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => true,
                'defaultFont' => 'sans-serif',
            ]);

            return [
                'success' => true,
                'content' => $pdf->output(),
                'filename' => $filename,
                'mime_type' => 'application/pdf',
            ];
        } catch (\Exception $e) {
            \Log::error('PDF export failed', [
                'note_id' => $note->id,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'error' => 'Failed to export PDF: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Export note to plain text
     */
    public function exportToText(Note $note): array
    {
        try {
            $filename = $this->sanitizeFilename($note->title) . '.txt';
            
            $text = $this->generateTextContent($note);

            return [
                'success' => true,
                'content' => $text,
                'filename' => $filename,
                'mime_type' => 'text/plain',
            ];
        } catch (\Exception $e) {
            \Log::error('Text export failed', [
                'note_id' => $note->id,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'error' => 'Failed to export text: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Export note to DOCX
     */
    public function exportToDocx(Note $note): array
    {
        try {
            if (!class_exists(PhpWord::class)) {
                throw new \RuntimeException('PhpWord library not available. Please install phpoffice/phpword');
            }

            $filename = $this->sanitizeFilename($note->title) . '.docx';
            
            $phpWord = new PhpWord();
            
            // Set document properties
            $properties = $phpWord->getDocInfo();
            $properties->setCreator($note->user->name);
            $properties->setTitle($note->title);
            $properties->setDescription('Note exported from ' . config('app.name'));
            $properties->setCreated(time());

            // Add section
            $section = $phpWord->addSection([
                'marginTop' => 1000,
                'marginBottom' => 1000,
                'marginLeft' => 1000,
                'marginRight' => 1000,
            ]);

            // Title
            $section->addText(
                $note->title,
                [
                    'bold' => true,
                    'size' => 18,
                    'color' => '1F2937',
                ],
                [
                    'alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER,
                    'spaceAfter' => 240,
                ]
            );

            // Metadata
            $metadataStyle = ['size' => 9, 'color' => '6B7280'];
            
            if ($note->user) {
                $section->addText(
                    'Author: ' . $note->user->name,
                    $metadataStyle,
                    ['spaceAfter' => 120]
                );
            }

            $section->addText(
                'Created: ' . $note->created_at->format('F d, Y'),
                $metadataStyle,
                ['spaceAfter' => 120]
            );

            if ($note->academicSubject) {
                $section->addText(
                    'Subject: ' . $note->academicSubject->name,
                    $metadataStyle,
                    ['spaceAfter' => 120]
                );
            }

            if ($note->book) {
                $section->addText(
                    'Book: ' . $note->book->title,
                    $metadataStyle,
                    ['spaceAfter' => 240]
                );
            }

            // Add separator
            $section->addLine([
                'weight' => 1,
                'width' => 450,
                'height' => 0,
                'color' => 'E5E7EB',
            ]);

            $section->addTextBreak(1);

            // Content - Convert HTML to Word format
            Html::addHtml($section, $note->content, false, false);

            // Footer
            $footer = $section->addFooter();
            $footer->addPreserveText(
                'Page {PAGE} of {NUMPAGES}',
                ['size' => 9, 'color' => '9CA3AF'],
                ['alignment' => \PhpOffice\PhpWord\SimpleType\Jc::CENTER]
            );

            // Save to temp file
            $tempFile = tempnam(sys_get_temp_dir(), 'note_export_');
            $writer = IOFactory::createWriter($phpWord, 'Word2007');
            $writer->save($tempFile);

            $content = file_get_contents($tempFile);
            unlink($tempFile);

            return [
                'success' => true,
                'content' => $content,
                'filename' => $filename,
                'mime_type' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            ];
        } catch (\Exception $e) {
            \Log::error('DOCX export failed', [
                'note_id' => $note->id,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'error' => 'Failed to export DOCX: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Generate plain text content from note
     */
    protected function generateTextContent(Note $note): string
    {
        $text = str_repeat('=', 70) . "\n";
        $text .= strtoupper($note->title) . "\n";
        $text .= str_repeat('=', 70) . "\n\n";

        // Metadata
        if ($note->user) {
            $text .= "Author: " . $note->user->name . "\n";
        }

        $text .= "Created: " . $note->created_at->format('F d, Y \a\t g:i A') . "\n";

        if ($note->created_at != $note->updated_at) {
            $text .= "Last Updated: " . $note->updated_at->format('F d, Y \a\t g:i A') . "\n";
        }

        if ($note->academicSubject) {
            $text .= "Subject: " . $note->academicSubject->name . "\n";
        }

        if ($note->book) {
            $text .= "Book: " . $note->book->title . "\n";
        }

        $text .= "Visibility: " . ($note->is_public ? 'Public' : 'Private') . "\n";

        $text .= "\n" . str_repeat('-', 70) . "\n\n";

        // Content - strip HTML tags
        $content = strip_tags($note->content);
        $content = html_entity_decode($content, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $content = preg_replace('/\n\s*\n\s*\n/', "\n\n", $content); // Remove excessive line breaks
        $text .= trim($content) . "\n\n";

        // Footer
        $text .= str_repeat('-', 70) . "\n";
        $text .= "Exported from " . config('app.name') . "\n";
        $text .= "Export Date: " . now()->format('F d, Y \a\t g:i A') . "\n";
        $text .= str_repeat('=', 70) . "\n";

        return $text;
    }

    /**
     * Sanitize filename
     */
    protected function sanitizeFilename(string $filename): string
    {
        // Remove or replace invalid characters
        $filename = preg_replace('/[^a-zA-Z0-9\s\-_]/', '', $filename);
        $filename = preg_replace('/\s+/', '_', $filename);
        $filename = Str::limit($filename, 100, '');
        
        return $filename ?: 'note_' . time();
    }

    /**
     * Get supported export formats
     */
    public static function getSupportedFormats(): array
    {
        return [
            'pdf' => [
                'label' => 'PDF Document',
                'extension' => 'pdf',
                'icon' => 'document-text',
                'mime_type' => 'application/pdf',
            ],
            'docx' => [
                'label' => 'Word Document',
                'extension' => 'docx',
                'icon' => 'document',
                'mime_type' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            ],
            'txt' => [
                'label' => 'Plain Text',
                'extension' => 'txt',
                'icon' => 'document-text',
                'mime_type' => 'text/plain',
            ],
        ];
    }
}
