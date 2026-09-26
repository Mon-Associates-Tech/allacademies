<?php

namespace App\MockExam\Controllers;

use App\Http\Controllers\Controller;
use App\MockExam\Models\MockExam;
use App\MockExam\Models\MockExamSubjectExam;
use App\MockExam\Models\MockExamTemplate;
use App\MockExam\Services\MockExamPdfService;
use Illuminate\View\View;

class MockExamPdfController extends Controller
{
    public function __construct(
        private readonly MockExamPdfService $viewService
    ) {}

    public function previewPage(MockExam $mockExam): View
    {
        $this->ensureOwner($mockExam);

        $fontSize = max(8, min(14, (float) request()->input('font_size', 10.5)));

        return view('mock-exam.pdf.preview', compact('mockExam', 'fontSize'));
    }

    public function exam(MockExam $mockExam): View
    {
        $this->ensureOwner($mockExam);

        $fontSize = max(8, min(14, (float) request()->input('font_size', 10.5)));

        return view(
            'mock-exam.pdf.exam',
            $this->viewService->examData($mockExam, $fontSize)
        );
    }

    public function answerKey(MockExam $mockExam): View
    {
        $this->ensureOwner($mockExam);

        return view(
            'mock-exam.pdf.answer-key',
            $this->viewService->answerKeyData($mockExam)
        );
    }

    public function subjectExam(MockExam $mockExam, MockExamSubjectExam $subjectExam): View
    {
        $this->ensureOwner($mockExam);
        $this->ensureSubjectExamBelongsToMockExam($mockExam, $subjectExam);

        $fontSize = max(8, min(14, (float) request()->input('font_size', 10.5)));

        return view(
            'mock-exam.pdf.subject-exam',
            $this->viewService->subjectExamData($subjectExam, $fontSize)
        );
    }

    public function previewSubjectExamPage(MockExam $mockExam, MockExamSubjectExam $subjectExam): View
    {
        $this->ensureOwner($mockExam);
        $this->ensureSubjectExamBelongsToMockExam($mockExam, $subjectExam);

        $fontSize = max(8, min(14, (float) request()->input('font_size', 10.5)));

        return view('mock-exam.pdf.subject-preview', compact(
            'mockExam',
            'subjectExam',
            'fontSize'
        ));
    }

    public function previewTemplateFrontPage(MockExamTemplate $template): View
    {
        abort_unless($template->user_id === auth()->id(), 403);

        $fontSize = max(8, min(14, (float) request()->input('font_size', 11)));

        $blocks = $template->front_page_config['blocks'] ?? [];

        /*
         * If this route is being opened inside a modal or Livewire preview area,
         * you may return the partial directly instead:
         *
         * return view('livewire.mock-exam.partials.front-page-preview', [
         *     'template' => $template,
         *     'blocks' => $blocks,
         *     'fontSize' => $fontSize,
         *     'isPdf' => false,
         * ]);
         */

        return view('mock-exam.pdf.template-front-page-preview', [
            'template' => $template,
            'blocks' => $blocks,
            'fontSize' => $fontSize,
            'isPdf' => false,
        ]);
    }

    private function ensureOwner(MockExam $exam): void
    {
        abort_unless($exam->user_id === auth()->id(), 403);
    }

    private function ensureSubjectExamBelongsToMockExam(
        MockExam $mockExam,
        MockExamSubjectExam $subjectExam
    ): void {
        abort_unless($subjectExam->mock_exam_id === $mockExam->id, 404);
    }
}
