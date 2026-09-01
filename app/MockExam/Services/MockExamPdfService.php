<?php

namespace App\MockExam\Services;

use App\MockExam\Models\MockExam;
use App\MockExam\Models\MockExamSubjectExam;
use App\MockExam\Models\MockExamTemplate;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;
use Illuminate\Support\Str;

class MockExamPdfService
{
    /**
     * For subject exams with no template_id, attempt to resolve a template
     * by matching academic_subject_id (falling back to any template with a
     * front_page_config that has blocks).
     */
    private function resolveTemplates(MockExam $mockExam): void
    {
        $subjectIds = $mockExam->subjectExams
            ->whereNull('template_id')
            ->pluck('academic_subject_id')
            ->filter()
            ->unique();

        if ($subjectIds->isEmpty()) {
            return;
        }

        $templates = MockExamTemplate::whereIn('academic_subject_id', $subjectIds)
            ->whereNotNull('front_page_config')
            ->get()
            ->keyBy('academic_subject_id');

        foreach ($mockExam->subjectExams as $se) {
            if ($se->template_id === null && isset($templates[$se->academic_subject_id])) {
                $se->setRelation('template', $templates[$se->academic_subject_id]);
            }
        }
    }

    private function resolveSubjectExamTemplate(MockExamSubjectExam $subjectExam): void
    {
        if ($subjectExam->template_id !== null || ! $subjectExam->academic_subject_id) {
            return;
        }

        $template = MockExamTemplate::where('academic_subject_id', $subjectExam->academic_subject_id)
            ->whereNotNull('front_page_config')
            ->latest()
            ->first();

        if ($template) {
            $subjectExam->setRelation('template', $template);
        }
    }

    /**
     * Generate a downloadable PDF of the exam paper (questions only).
     */
    public function generateExamPdf(MockExam $mockExam, float $fontSize = 10.5): Response
    {
        $mockExam->load([
            'subjectExams.academicSubject',
            'subjectExams.sections.questions',
            'subjectExams.template',
            'user',
        ]);

        $this->resolveTemplates($mockExam);

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
            'subjectExams.template',
            'user',
        ]);

        $this->resolveTemplates($mockExam);

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
            'template',
        ]);

        $this->resolveSubjectExamTemplate($subjectExam);

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
            'template',
        ]);

        $this->resolveSubjectExamTemplate($subjectExam);

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