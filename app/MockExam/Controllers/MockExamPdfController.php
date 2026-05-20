<?php

namespace App\MockExam\Controllers;

use App\Http\Controllers\Controller;
use App\MockExam\Models\MockExam;
use App\MockExam\Services\MockExamPdfService;
use Illuminate\Http\Response;
use Illuminate\View\View;

class MockExamPdfController extends Controller
{
    public function __construct(
        private readonly MockExamPdfService $pdfService
    ) {}

    public function previewPage(MockExam $mockExam): View
    {
        abort_unless($mockExam->user_id === auth()->id(), 403);

        $fontSize = request()->input('font_size', 10.5);
        $fontSize = max(8, min(14, (float) $fontSize));

        return view('mock-exam.pdf.preview', compact('mockExam', 'fontSize'));
    }

    public function examPdf(MockExam $mockExam): Response
    {
        abort_unless($mockExam->user_id === auth()->id(), 403);

        $fontSize = request()->input('font_size', 10.5);
        
        // Validate font size range (8pt to 14pt)
        $fontSize = max(8, min(14, (float) $fontSize));

        return $this->pdfService->generateExamPdf($mockExam, $fontSize);
    }

    public function previewExamPdf(MockExam $mockExam): Response
    {
        abort_unless($mockExam->user_id === auth()->id(), 403);

        $fontSize = request()->input('font_size', 10.5);
        
        // Validate font size range (8pt to 14pt)
        $fontSize = max(8, min(14, (float) $fontSize));

        return $this->pdfService->previewExamPdf($mockExam, $fontSize);
    }

    public function answerKeyPdf(MockExam $mockExam): Response
    {
        abort_unless($mockExam->user_id === auth()->id(), 403);

        return $this->pdfService->generateAnswerKeyPdf($mockExam);
    }
}
