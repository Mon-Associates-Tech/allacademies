<?php

namespace App\Services\GeneralExam;

use App\ExaminationHub\Models\GeneralExam;
use Barryvdh\DomPDF\Facade\Pdf;
use Barryvdh\DomPDF\PDF as PdfInstance;

class GeneralExamAnswerSheetService
{
    /**
     * Generate a combined question paper + answer sheet PDF for a print exam.
     */
    public function generate(GeneralExam $exam): PdfInstance
    {
        $exam->loadMissing(['questions', 'sections.questions', 'subscription.subjects']);

        $sections = $exam->sections->isNotEmpty()
            ? $exam->sections
            : collect([null]);

        $allQuestions = $exam->sections->isNotEmpty()
            ? $exam->sections->flatMap(fn ($s) => $s->questions)
            : $exam->questions;

        return Pdf::loadView('general-exams.print.answer-sheet', [
            'exam' => $exam,
            'sections' => $exam->sections->isNotEmpty() ? $exam->sections : null,
            'questions' => $allQuestions,
            'generatedAt' => now(),
        ])->setPaper('a4', 'portrait');
    }

    /**
     * Stream the PDF directly to the browser.
     */
    public function stream(GeneralExam $exam): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $filename = 'answer-sheet-'.str($exam->title)->slug().'-'.$exam->access_code.'.pdf';

        return $this->generate($exam)->stream($filename);
    }

    /**
     * Download the PDF as a file.
     */
    public function download(GeneralExam $exam): \Symfony\Component\HttpFoundation\Response
    {
        $filename = 'answer-sheet-'.str($exam->title)->slug().'-'.$exam->access_code.'.pdf';

        return $this->generate($exam)->download($filename);
    }
}
