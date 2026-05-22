<?php

namespace App\MockExam\Services;

use App\MockExam\Models\MockExam;
use App\MockExam\Models\MockExamSubjectExam;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;
use Illuminate\Support\Str;

class MockExamPdfService
{
    /**
     * Generate a downloadable PDF of the exam paper (questions only).
     */
    public function generateExamPdf(MockExam $mockExam, float $fontSize = 10.5): Response
    {
        $mockExam->load([
            'subjectExams.academicSubject',
            'subjectExams.sections.questions',
            'user',
        ]);

        $pdf = Pdf::loadView('mock-exam.pdf.exam', [
            'mockExam' => $mockExam,
            'fontSize' => $fontSize,
        ])
            ->setPaper('a4', 'portrait')
            ->setOption('isHtml5ParserEnabled', true)
            ->setOption('isRemoteEnabled', true)
            ->setOption('margin_top', 8)
            ->setOption('margin_right', 10)
            ->setOption('margin_bottom', 12)
            ->setOption('margin_left', 10);

        return $pdf->download($this->filename($mockExam, 'exam'));
    }

    /**
     * Generate a streamable PDF for preview.
     */
    public function previewExamPdf(MockExam $mockExam, float $fontSize = 10.5): Response
    {
        $mockExam->load([
            'subjectExams.academicSubject',
            'subjectExams.sections.questions',
            'user',
        ]);

        $pdf = Pdf::loadView('mock-exam.pdf.exam', [
            'mockExam' => $mockExam,
            'fontSize' => $fontSize,
        ])
            ->setPaper('a4', 'portrait')
            ->setOption('isHtml5ParserEnabled', true)
            ->setOption('isRemoteEnabled', true)
            ->setOption('margin_top', 8)
            ->setOption('margin_right', 10)
            ->setOption('margin_bottom', 12)
            ->setOption('margin_left', 10);

        return $pdf->stream($this->filename($mockExam, 'exam'));
    }

    /**
     * Generate a downloadable PDF of the answer key (correct answers + explanations).
     */
    public function generateAnswerKeyPdf(MockExam $mockExam): Response
    {
        $mockExam->load([
            'subjectExams.academicSubject',
            'subjectExams.sections.questions',
        ]);

        $pdf = Pdf::loadView('mock-exam.pdf.answer-key', ['mockExam' => $mockExam])
            ->setPaper('a4', 'portrait')
            ->setOption('isHtml5ParserEnabled', true);

        return $pdf->download($this->filename($mockExam, 'answer-key'));
    }

    /**
     * Generate a downloadable PDF for a single subject exam.
     */
    public function generateSubjectExamPdf(MockExamSubjectExam $subjectExam, float $fontSize = 10.5): Response
    {
        $subjectExam->load([
            'mockExam',
            'academicSubject',
            'academicLevel',
            'academicGroup',
            'sections.questions',
        ]);

        $pdf = Pdf::loadView('mock-exam.pdf.subject-exam', [
            'subjectExam' => $subjectExam,
            'fontSize' => $fontSize,
        ])
            ->setPaper('a4', 'portrait')
            ->setOption('isHtml5ParserEnabled', true)
            ->setOption('isRemoteEnabled', true)
            ->setOption('margin_top', 8)
            ->setOption('margin_right', 10)
            ->setOption('margin_bottom', 12)
            ->setOption('margin_left', 10);

        return $pdf->download($this->subjectExamFilename($subjectExam));
    }

    /**
     * Generate a streamable PDF for subject exam preview.
     */
    public function previewSubjectExamPdf(MockExamSubjectExam $subjectExam, float $fontSize = 10.5): Response
    {
        $subjectExam->load([
            'mockExam',
            'academicSubject',
            'academicLevel',
            'academicGroup',
            'sections.questions',
        ]);

        $pdf = Pdf::loadView('mock-exam.pdf.subject-exam', [
            'subjectExam' => $subjectExam,
            'fontSize' => $fontSize,
        ])
            ->setPaper('a4', 'portrait')
            ->setOption('isHtml5ParserEnabled', true)
            ->setOption('isRemoteEnabled', true)
            ->setOption('margin_top', 8)
            ->setOption('margin_right', 10)
            ->setOption('margin_bottom', 12)
            ->setOption('margin_left', 10);

        return $pdf->stream($this->subjectExamFilename($subjectExam));
    }

    private function filename(MockExam $mockExam, string $suffix): string
    {
        return Str::slug($mockExam->title) . '-' . $suffix . '.pdf';
    }

    private function subjectExamFilename(MockExamSubjectExam $subjectExam): string
    {
        $mockExamTitle = Str::slug($subjectExam->mockExam->title);
        $subjectName = Str::slug($subjectExam->academicSubject?->name ?? 'subject');
        return "{$mockExamTitle}-{$subjectName}.pdf";
    }
}
