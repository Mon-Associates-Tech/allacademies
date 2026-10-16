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
            'mock-exam.pdf.mock-exam-preview',  // Changed to the correct view
            $this->viewService->mockExamOverviewData($mockExam, $fontSize)
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

    /**
     * Preview page for users coming from subscription dashboard (with font size controls)
     */
    public function previewSubjectExamPageFromSubscription(MockExam $mockExam, MockExamSubjectExam $subjectExam): View
    {
        // Check if user has access through subscription
        $this->ensureSubscriptionAccess($mockExam, $subjectExam);

        $fontSize = max(8, min(14, (float) request()->input('font_size', 10.5)));

        return view('mock-exam.pdf.subject-preview', compact(
            'mockExam',
            'subjectExam',
            'fontSize'
        ))->with('fromSubscription', true);
    }

    /**
     * Raw subject exam content for users coming from subscription dashboard (used inside iframe)
     */
    public function subjectExamFromSubscription(MockExam $mockExam, MockExamSubjectExam $subjectExam): View
    {
        // Check if user has access through subscription
        $this->ensureSubscriptionAccess($mockExam, $subjectExam);

        $fontSize = max(8, min(14, (float) request()->input('font_size', 10.5)));

        return view(
            'mock-exam.pdf.subject-exam',
            $this->viewService->subjectExamData($subjectExam, $fontSize)
        );
    }

    /**
     * Preview page for users coming from subscription dashboard.
     * Redirects to the subscription dashboard where users can select individual subjects to preview.
     */
    public function previewPageFromSubscription(MockExam $mockExam)
    {
        $this->ensureSubscriptionAccess($mockExam);
        return redirect()->route('mock-exams.subscription.dashboard');
    }

    /**
     * General mock exam preview for users coming from subscription dashboard
     */
    public function examFromSubscription(MockExam $mockExam): View
    {
        // Check if user has access through subscription
        $this->ensureSubscriptionAccess($mockExam);

        $fontSize = max(8, min(14, (float) request()->input('font_size', 10.5)));

        return view(
            'mock-exam.pdf.mock-exam-preview',
            $this->viewService->mockExamOverviewData($mockExam, $fontSize)
        );
    }

    /**
     * Answer key / marking scheme for users coming from subscription dashboard
     */
    public function answerKeyFromSubscription(MockExam $mockExam): View
    {
        // Check if user has access through subscription
        $this->ensureSubscriptionAccess($mockExam);

        return view(
            'mock-exam.pdf.answer-key',
            $this->viewService->answerKeyData($mockExam)
        )->with('fromSubscription', true);
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

    /**
     * Check if the authenticated user has access to the mock exam through subscription
     */
    private function ensureSubscriptionAccess(MockExam $mockExam, ?MockExamSubjectExam $subjectExam = null): void
    {
        $user = auth()->user();
        abort_unless($user, 403);
        
        // Check if the user owns the mock exam
        if ($mockExam->user_id === $user->id) {
            return;
        }
        
        // Check if the mock exam is linked to a subscription the user has
        $hasAccessThroughSubscription = $mockExam->mockExamSubscription && 
            $mockExam->mockExamSubscription->user_id === $user->id;
        
        abort_unless($hasAccessThroughSubscription, 403);
    }
}