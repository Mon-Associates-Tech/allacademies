<?php

namespace App\MockExam\Controllers;

use App\Http\Controllers\Controller;
use App\MockExam\Models\MockExam;
use App\MockExam\Models\MockExamSubjectExam;
use App\MockExam\Models\MockExamTemplate;
use App\MockExam\Services\MockExamPdfService;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\URL;
use Illuminate\View\View;
use Spatie\Browsershot\Browsershot;

class MockExamPdfController extends Controller
{
    public function __construct(
        private readonly MockExamPdfService $pdfService
    ) {}

    public function previewPage(MockExam $mockExam): View
    {
        abort_unless($mockExam->user_id === auth()->id(), 403);
        $fontSize = max(8, min(14, (float) request()->input('font_size', 10.5)));
        return view('mock-exam.pdf.preview', compact('mockExam', 'fontSize'));
    }

    public function examPdf(MockExam $mockExam): Response
    {
        abort_unless($mockExam->user_id === auth()->id(), 403);
        $fontSize = max(8, min(14, (float) request()->input('font_size', 10.5)));
        return $this->pdfService->generateExamPdf($mockExam, $fontSize);
    }

    public function previewExamPdf(MockExam $mockExam): Response
    {
        abort_unless($mockExam->user_id === auth()->id(), 403);
        $fontSize = max(8, min(14, (float) request()->input('font_size', 10.5)));
        return $this->pdfService->previewExamPdf($mockExam, $fontSize);
    }

    public function answerKeyPdf(MockExam $mockExam): Response
    {
        abort_unless($mockExam->user_id === auth()->id(), 403);
        return $this->pdfService->generateAnswerKeyPdf($mockExam);
    }

    public function subjectExamPdf(MockExam $mockExam, MockExamSubjectExam $subjectExam): Response
    {
        $this->ensureOwner($mockExam);
        abort_unless($subjectExam->mock_exam_id === $mockExam->id, 404);
        $fontSize = max(8, min(14, (float) request()->input('font_size', 10.5)));
        return $this->pdfService->generateSubjectExamPdf($subjectExam, $fontSize);
    }

    public function previewSubjectExamPdf(MockExam $mockExam, MockExamSubjectExam $subjectExam): Response
    {
        $this->ensureOwner($mockExam);
        abort_unless($subjectExam->mock_exam_id === $mockExam->id, 404);
        $fontSize = max(8, min(14, (float) request()->input('font_size', 10.5)));
        return $this->pdfService->previewSubjectExamPdf($subjectExam, $fontSize);
    }

    public function previewSubjectExamPage(MockExam $mockExam, MockExamSubjectExam $subjectExam): View
    {
        $this->ensureOwner($mockExam);
        abort_unless($subjectExam->mock_exam_id === $mockExam->id, 404);
        $fontSize = max(8, min(14, (float) request()->input('font_size', 10.5)));
        return view('mock-exam.pdf.subject-preview', compact('mockExam', 'subjectExam', 'fontSize'));
    }

    // ═══════════════════════════════════════════════════════════════════════
    // NEW: BROWSERSHOT URL-BASED GENERATION (Fixes KaTeX/Alpine rendering)
    // ═══════════════════════════════════════════════════════════════════════

    /**
     * Returns the raw HTML view for Browsershot to render.
     * Secured by 'signed' middleware (no auth session required).
     */
    public function renderSubjectExamHtml(MockExam $mockExam, MockExamSubjectExam $subjectExam): View
    {
        // Security: Check ownership OR valid signed URL
        if ($mockExam->user_id !== auth()->id() && !request()->hasValidSignature()) {
            abort(403, 'Unauthorized or invalid signature.');
        }
        
        abort_unless($subjectExam->mock_exam_id === $mockExam->id, 404);
        
        $fontSize = max(8, min(14, (float) request()->input('font_size', 10.5)));
        
        return view('mock-exam.pdf.subject-exam-browsershot', compact('mockExam', 'subjectExam', 'fontSize'));
    }

    /**
     * Preview Subject Exam PDF (Inline) using Browsershot::url()
     */
    public function previewSubjectExamPdfUrl(MockExam $mockExam, MockExamSubjectExam $subjectExam): Response
    {
        $this->ensureOwner($mockExam);
        abort_unless($subjectExam->mock_exam_id === $mockExam->id, 404);
        
        $fontSize = max(8, min(14, (float) request()->input('font_size', 10.5)));

        $url = URL::signedRoute(
            'mock-exams.pdf.render.subject-exam', 
            [$mockExam, $subjectExam], 
            now()->addMinutes5(), // Valid for 5 minutes
            ['font_size' => $fontSize]
        );

        $pdf = $this->generatePdfFromUrl($url);

        return response($pdf, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="subject-exam.pdf"'
        ]);
    }

    /**
     * Download Subject Exam PDF (Attachment) using Browsershot::url()
     */
    public function subjectExamPdfUrl(MockExam $mockExam, MockExamSubjectExam $subjectExam): Response
    {
        $this->ensureOwner($mockExam);
        abort_unless($subjectExam->mock_exam_id === $mockExam->id, 404);

        $fontSize = max(8, min(14, (float) request()->input('font_size', 10.5)));

        $url = URL::signedRoute(
            'mock-exams.pdf.render.subject-exam', 
            [$mockExam, $subjectExam], 
            now()->addMinutes(5), 
            ['font_size' => $fontSize]
        );

        $pdf = $this->generatePdfFromUrl($url);

        return response($pdf, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="subject-exam.pdf"'
        ]);
    }

 /**
     * HELPER: Centralized Browsershot configuration
     */
    private function generatePdfFromUrl(string $url): string
    {
        return Browsershot::url($url)
            ->format('A4')
            ->margins(15, 15, 15, 15)
            ->showBackground() // Ensures Tailwind colors/borders print
            ->windowSize(1920, 1080) // Prevents layout shifts that can break JS
            ->waitUntilNetworkIdle() // Waits for Vite/JS files to load
            ->delay(2000) // 🔥 CRITICAL: Gives Alpine/KaTeX 2 seconds to render the math
            ->addChromiumArguments([
                '--no-sandbox',
                '--disable-setuid-sandbox',
                '--disable-dev-shm-usage',
                '--disable-gpu', // Prevents macOS rendering hangs
            ])
            ->pdf();
    }

    // ═══════════════════════════════════════════════════════════════════════
    // EXISTING METHODS
    // ═══════════════════════════════════════════════════════════════════════

    public function previewTemplateFrontPage(MockExamTemplate $template): \Illuminate\Http\Response
    {
        abort_unless($template->user_id === auth()->id(), 403);
        
        $fontSize = max(8, min(14, (float) request()->input('font_size', 11)));
        $blocks = $template->front_page_config['blocks'] ?? [];

        $html = view('livewire.mock-exam.partials.front-page-preview', [
            'template' => $template,
            'blocks' => $blocks,
            'fontSize' => $fontSize,
            'isPdf' => true,
        ])->render();

        $pdf = Browsershot::html($html)
            ->showBackground()
            ->format('A4')
            ->margins(0, 0, 0, 0)
            ->timeout(60000)
            ->addChromiumArguments([
                '--no-sandbox',
                '--disable-setuid-sandbox',
                '--disable-dev-shm-usage',
            ])
            ->pdf();

        return response($pdf, 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="front-page-preview.pdf"'
        ]);
    }

    private function ensureOwner(MockExam $exam): void
    {
        abort_unless($exam->user_id === auth()->id(), 403);
    }
}