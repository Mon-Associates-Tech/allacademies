<?php

namespace App\Services;

use Exception;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use PhpOffice\PhpWord\IOFactory;
use PhpOffice\PhpWord\Element\AbstractElement;
use RuntimeException;

class DocumentContentExtractionService
{
    /**
     * Extract content from Word document with HTML formatting preserved
     */
    public function extractFromWord(UploadedFile $file): array
    {
        try {
            if (!class_exists(IOFactory::class)) {
                throw new RuntimeException('PhpWord library not available');
            }

            $phpWord = IOFactory::load($file->getRealPath());
            $plainText = '';
            $htmlText = '';

            foreach ($phpWord->getSections() as $section) {
                foreach ($section->getElements() as $element) {
                    $plainText .= $this->extractPlainText($element);
                    $htmlText .= $this->elementToHtml($element);
                }
            }

            return [
                'plain' => trim($plainText),
                'html' => trim($htmlText)
            ];
        } catch (Exception $e) {
            Log::error('Word document processing failed', [
                'error' => $e->getMessage(),
                'file' => $file->getClientOriginalName(),
            ]);

            $errorMsg = 'Error processing document: ' . $e->getMessage();
            return [
                'plain' => $errorMsg,
                'html' => $errorMsg
            ];
        }
    }

    /**
     * Extract plain text from element
     */
    protected function extractPlainText($element): string
    {
        $text = '';
        
        if (method_exists($element, 'getText')) {
            $text .= $element->getText() . "\n";
        } elseif (method_exists($element, 'getElements')) {
            foreach ($element->getElements() as $childElement) {
                $text .= $this->extractPlainText($childElement);
            }
        }
        
        return $text;
    }

    /**
     * Convert PhpWord element to HTML preserving formatting
     */
    protected function elementToHtml($element): string
    {
        $html = '';
        
        if (method_exists($element, 'getText')) {
            $text = $element->getText();
            $html .= $this->applyFormatting($text, $element);
        } elseif (method_exists($element, 'getElements')) {
            foreach ($element->getElements() as $childElement) {
                $html .= $this->elementToHtml($childElement);
            }
        }
        
        return $html;
    }

    /**
     * Apply HTML formatting based on element style
     */
    protected function applyFormatting(string $text, $element): string
    {
        if (empty($text)) {
            return '';
        }

        $formatted = htmlspecialchars($text);
        $style = method_exists($element, 'getFontStyle') ? $element->getFontStyle() : null;

        if (!$style) {
            return $formatted . "\n";
        }

        // Apply bold
        if (method_exists($style, 'isBold') && $style->isBold()) {
            $formatted = '<strong>' . $formatted . '</strong>';
        }

        // Apply italic
        if (method_exists($style, 'isItalic') && $style->isItalic()) {
            $formatted = '<em>' . $formatted . '</em>';
        }

        // Apply underline
        if (method_exists($style, 'getUnderline') && $style->getUnderline() !== 'none' && $style->getUnderline() !== null) {
            $formatted = '<u>' . $formatted . '</u>';
        }

        return $formatted . "\n";
    }
}
