<?php

namespace App\Helpers;

use Barryvdh\DomPDF\PDF;

class DomPdfConfigurator
{
    /**
     * Applies standard, robust configuration to a DomPDF instance.
     * Crucial for rendering modern HTML5, SVGs, and Base64 images correctly.
     */
    public function configure(PDF $pdf): PDF
    {
        return $pdf
            ->setPaper('a4', 'portrait')
            // Required for <svg> tags and modern HTML5 elements
            ->setOption('isHtml5ParserEnabled', true)
            // Required for data:image/svg+xml;base64,... URIs and external fonts
            ->setOption('isRemoteEnabled', true)
            // Security: Prevents PDF from executing PHP code
            ->setOption('isPhpEnabled', false)
            // Reduces PDF file size by subsetting fonts
            ->setOption('isFontSubsettingEnabled', true)
            // Standard margins for A4
            ->setOption('margin_top', 8)
            ->setOption('margin_right', 10)
            ->setOption('margin_bottom', 12)
            ->setOption('margin_left', 10);
    }
}
