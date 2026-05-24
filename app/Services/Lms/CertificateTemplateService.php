<?php

namespace App\Services\Lms;

use App\ExaminationHub\Models\GeneralExamSubmission;
use setasign\Fpdi\Fpdi;

class CertificateTemplateService
{
    /**
     * Generate a PDF binary by overlaying submission data onto a PDF template.
     * Template path defaults to resources/pdf/certificate-template.pdf
     *
     * @return string PDF binary
     */
    public function generateFromTemplate(GeneralExamSubmission $submission, ?string $templatePath = null): string
    {
        $templatePath = $templatePath ?? resource_path('pdf/certificate-template.pdf');

        $participantName = $submission->participant_name ?? $submission->participant_email;
        $assignmentTitle = $submission->assignment->title ?? 'Examination';
        $percentage = number_format($submission->percentage ?? 0, 1).'%';

        $pdf = new Fpdi;

        // Import the existing template if available
        $pageCount = $pdf->setSourceFile($templatePath);
        $tplId = $pdf->importPage(1);
        $size = $pdf->getTemplateSize($tplId);

        $pdf->AddPage();
        $pdf->useTemplate($tplId);

        // Choose font and place text roughly centered. Adjust Y coordinates as needed for template.
        $pdf->SetFont('Helvetica', 'B', 28);
        $pdf->SetTextColor(16, 185, 129);
        $pdf->SetXY(0, $size['height'] * 0.40);
        $pdf->Cell(0, 14, $participantName, 0, 1, 'C');

        $pdf->SetFont('Helvetica', '', 16);
        $pdf->SetTextColor(79, 96, 117);
        $pdf->SetXY(0, $size['height'] * 0.52);
        $pdf->Cell(0, 10, $assignmentTitle, 0, 1, 'C');

        $pdf->SetFont('Helvetica', '', 12);
        $pdf->SetXY(0, $size['height'] * 0.6);
        $pdf->Cell(0, 8, 'Score: '.($submission->score ?? 0).'  —  Percentage: '.$percentage, 0, 1, 'C');

        // Output to string
        return $pdf->Output('', 'S');
    }
}
