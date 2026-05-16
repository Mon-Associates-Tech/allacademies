<?php

namespace App\MockExam\Controllers;

use App\Http\Controllers\Controller;
use App\MockExam\Models\MockExam;
use App\MockExam\Services\MockExamPdfService;
use Illuminate\Http\Response;

class MockExamPdfController extends Controller
{
    public function __construct(
        private readonly MockExamPdfService $pdfService
    ) {}

    public function examPdf(MockExam $mockExam): Response
    {
        abort_unless($mockExam->user_id === auth()->id(), 403);

        return $this->pdfService->generateExamPdf($mockExam);
    }

    public function answerKeyPdf(MockExam $mockExam): Response
    {
        abort_unless($mockExam->user_id === auth()->id(), 403);

        return $this->pdfService->generateAnswerKeyPdf($mockExam);
    }
}
