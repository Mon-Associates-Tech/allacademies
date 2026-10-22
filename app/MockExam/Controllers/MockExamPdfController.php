<?php

namespace App\MockExam\Controllers;

use App\Http\Controllers\Controller;
use App\MockExam\Models\MockExam;
use App\MockExam\Models\MockExamSubjectExam;
use App\MockExam\Models\MockExamTemplate;
use App\MockExam\Services\MockExamPdfService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Spatie\Browsershot\Browsershot;

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

    /**
     * Generate and download the answer key / marking scheme as a PDF file.
     */
    public function answerKeyDownload(MockExam $mockExam): Response
    {
        $this->ensureOwner($mockExam);

        $data = $this->viewService->answerKeyData($mockExam);
        $this->inlineImagesForAnswerKey($data);
        $html = view('mock-exam.pdf.answer-key-pdf', $data)->render();

        $filename = preg_replace('/[^a-zA-Z0-9_\-\.\s]/', '', $mockExam->title) . '-answer-key.pdf';

        $pdf = Browsershot::html($html)
            ->setIncludePath('$PATH:/usr/local/bin')
            ->setNodeEnv(['PUPPETEER_CACHE_DIR' => config('app.puppeteer_cache_dir')])
            ->emulateMedia('print')
            ->showBackground()
            ->format('A4')
            ->margins(15, 15, 15, 15)
            ->noSandbox()
            ->setOption('args', ['--disable-dev-shm-usage'])
            ->timeout(120)
            ->pdf();

        while (ob_get_level() > 0) {
            ob_end_clean();
        }
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Content-Length: ' . strlen($pdf));
        header('X-Accel-Buffering: no');
        echo $pdf;
        exit;
    }

    /**
     * Download answer key for a single subject exam only.
     */
    public function subjectAnswerKeyDownload(MockExam $mockExam, MockExamSubjectExam $subjectExam): Response
    {
        $this->ensureOwner($mockExam);
        $this->ensureSubjectExamBelongsToMockExam($mockExam, $subjectExam);

        // Load sections, questions, template, and academic subject
        $subjectExam->load(['sections.questions', 'template', 'academicSubject']);
        $data = $this->viewService->answerKeyData($mockExam, $subjectExam);
        $this->inlineImagesForAnswerKey($data);
        $html = view('mock-exam.pdf.answer-key-pdf', $data)->render();

        $subjectName = $subjectExam->getDisplayTitle();
        $filename = preg_replace('/[^a-zA-Z0-9_\-\.\s]/', '', $subjectName) . '-answer-key.pdf';

        $pdf = Browsershot::html($html)
            ->setIncludePath('$PATH:/usr/local/bin')
            ->setNodeEnv(['PUPPETEER_CACHE_DIR' => config('app.puppeteer_cache_dir')])
            ->emulateMedia('print')
            ->showBackground()
            ->format('A4')
            ->margins(15, 15, 15, 15)
            ->noSandbox()
            ->setOption('args', ['--disable-dev-shm-usage'])
            ->timeout(120)
            ->pdf();

        while (ob_get_level() > 0) {
            ob_end_clean();
        }
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Content-Length: ' . strlen($pdf));
        header('X-Accel-Buffering: no');
        echo $pdf;
        exit;
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

    /**
     * Generate and download the subject exam as a PDF file using Browsershot.
     */
    public function subjectExamDownload(MockExam $mockExam, MockExamSubjectExam $subjectExam): Response
    {
        $this->ensureOwner($mockExam);
        $this->ensureSubjectExamBelongsToMockExam($mockExam, $subjectExam);

        // Increase limits for large PDF generation with base64 images
        set_time_limit(300);
        ini_set('memory_limit', '512M');

        // Flush any output buffers to prevent partial response issues
        while (ob_get_level() > 0) {
            ob_end_clean();
        }

        // Ensure template and academic subject are loaded for getDisplayTitle()
        $subjectExam->loadMissing(['template', 'academicSubject']);

        $fontSize = max(8, min(14, (float) request()->input('font_size', 10.5)));
        $data = $this->viewService->subjectExamData($subjectExam, $fontSize);

        // Inline all images as base64 data URIs so Browsershot doesn't need network access
        $this->inlineImagesForPdf($subjectExam, $data);

        try {
            $html = view('mock-exam.pdf.subject-exam-pdf', $data)->render();
        } catch (\Throwable $e) {
            \Log::error('Subject exam PDF view rendering failed', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
                'subjectExam' => $subjectExam->id,
            ]);
            return response('Error generating PDF: ' . $e->getMessage(), 500, [
                'Content-Type' => 'text/plain',
            ]);
        }

        $filename = preg_replace('/[^a-zA-Z0-9_\-\.\s]/', '', $subjectExam->getDisplayTitle()) . '.pdf';

        try {
            $pdf = Browsershot::html($html)
                ->setIncludePath('$PATH:/usr/local/bin')
                ->setNodeEnv(['PUPPETEER_CACHE_DIR' => config('app.puppeteer_cache_dir')])
                ->emulateMedia('print')
                ->showBackground()
                ->format('A4')
                ->margins(15, 15, 15, 15)
                ->noSandbox()
                ->setOption('args', ['--disable-dev-shm-usage'])
                ->timeout(120)
                ->pdf();
        } catch (\Throwable $e) {
            \Log::error('Browsershot PDF generation failed', [
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString(),
                'subjectExam' => $subjectExam->id,
                'htmlLength' => strlen($html),
            ]);
            return response('Error generating PDF: ' . $e->getMessage(), 500, [
                'Content-Type' => 'text/plain',
            ]);
        }

        if (empty($pdf)) {
            \Log::error('Browsershot returned empty PDF', [
                'subjectExam' => $subjectExam->id,
                'htmlLength' => strlen($html),
            ]);
            return response('Error: PDF generation produced empty output', 500, [
                'Content-Type' => 'text/plain',
            ]);
        }

        // Write PDF directly to response without buffering
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Content-Length: ' . strlen($pdf));
        header('X-Accel-Buffering: no');
        echo $pdf;
        exit;
    }

    /**
     * Convert all image storage paths to base64 data URIs so Browsershot
     * can render the PDF without fetching external resources.
     */
    private function inlineImagesForPdf(MockExamSubjectExam $subjectExam, array &$data): void
    {
        // Front page PDF images (from template upload mode)
        if ($subjectExam->template !== null) {
            $frontPageConfig = $subjectExam->template->front_page_config ?? null;
            if (!empty($frontPageConfig['attachment_pdf_images'])) {
                $images = $frontPageConfig['attachment_pdf_images'];
                foreach ($images as $i => $path) {
                    $images[$i] = $this->toBase64DataUri($path);
                }
                $frontPageConfig['attachment_pdf_images'] = $images;
                $subjectExam->template->front_page_config = $frontPageConfig;
            }
        }

        // Section attachment images
        foreach ($subjectExam->sections as $section) {
            // PDF attachment images (array)
            if (!empty($section->attachment_pdf_images)) {
                $images = $section->attachment_pdf_images;
                foreach ($images as $i => $path) {
                    $images[$i] = $this->toBase64DataUri($path);
                }
                $section->attachment_pdf_images = $images;
            }

            // Single image attachment
            if (!empty($section->attachment_image_path)) {
                $section->attachment_image_path = $this->toBase64DataUri($section->attachment_image_path);
            }
        }

        // Identity section images (logos)
        if (!empty($data['identity']['rows'])) {
            foreach ($data['identity']['rows'] as &$row) {
                foreach ($row as &$item) {
                    if (isset($item['type']) && $item['type'] === 'image' && !empty($item['value'])) {
                        $item['value'] = $this->toBase64DataUri($item['value']);
                    }
                }
            }
        }
    }

    /**
     * Read a file from public storage and return it as a base64 data URI.
     */
    private function toBase64DataUri(string $path): string
    {
        if (str_starts_with($path, 'data:')) {
            return $path; // already a data URI
        }

        $fullPath = Storage::disk('public')->path($path);
        if (!file_exists($fullPath)) {
            return ''; // return empty if file not found
        }

        $content = file_get_contents($fullPath);
        $mimeType = mime_content_type($fullPath);
        $base64 = base64_encode($content);

        return 'data:' . $mimeType . ';base64,' . $base64;
    }

    /**
     * Inline images in question text and answer fields for Browsershot PDF rendering.
     * This scans all questions in the answer key data and replaces storage image URLs
     * with base64 data URIs.
     */
    private function inlineImagesForAnswerKey(array &$data): void
    {
        $mockExam = $data['mockExam'];
        $subjectExam = $data['subjectExam'] ?? null;

        // Determine which subject exams to process
        $subjectExams = [];
        if ($subjectExam !== null) {
            $subjectExams[] = $subjectExam;
        } else {
            foreach ($mockExam->subjectExams as $se) {
                $subjectExams[] = $se;
            }
        }

        foreach ($subjectExams as $se) {
            foreach ($se->sections as $section) {
                foreach ($section->questions as $question) {
                    // Inline images in question_text
                    if (!empty($question->question_text)) {
                        $question->question_text = $this->inlineStorageImagesInHtml($question->question_text);
                    }
                    // Inline images in answer_explanation
                    if (!empty($question->answer_explanation)) {
                        $question->answer_explanation = $this->inlineStorageImagesInHtml($question->answer_explanation);
                    }
                }
            }
        }
    }

    /**
     * Replace storage image URLs in HTML content with base64 data URIs.
     */
    private function inlineStorageImagesInHtml(string $html): string
    {
        // Match <img> tags with src attributes containing storage paths
        return preg_replace_callback(
            '/<img([^>]*?)src=["\']([^"\']+)["\']([^>]*>)/i',
            function ($matches) {
                $beforeSrc = $matches[1];
                $src = $matches[2];
                $afterSrc = $matches[3];

                // Skip data URIs, external URLs, and relative URLs that aren't storage paths
                if (str_starts_with($src, 'data:') || str_starts_with($src, 'http') || str_starts_with($src, '//')) {
                    return $matches[0];
                }

                // Check if it's a storage path (starts with storage/ or matches public disk path)
                $path = ltrim($src, '/');
                $fullPath = Storage::disk('public')->path($path);

                if (!file_exists($fullPath)) {
                    return $matches[0]; // leave as-is if file not found
                }

                $dataUri = $this->toBase64DataUri($path);
                if ($dataUri) {
                    return '<img' . $beforeSrc . 'src="' . $dataUri . '"' . $afterSrc;
                }

                return $matches[0];
            },
            $html
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
     * Serve the editable version of the subject exam preview.
     */
    public function editableSubjectExam(MockExam $mockExam, MockExamSubjectExam $subjectExam): View
    {
        $this->ensureOwner($mockExam);
        $this->ensureSubjectExamBelongsToMockExam($mockExam, $subjectExam);

        $fontSize = max(8, min(14, (float) request()->input('font_size', 10.5)));

        return view('mock-exam.pdf.subject-exam-editable', [
            'subjectExam' => $subjectExam,
            'fontSize'    => $fontSize,
            'layoutConfig' => $subjectExam->layout_config ?? [],
        ]);
    }

    /**
     * Save layout config for a subject exam.
     */
    public function saveLayout(Request $request, MockExam $mockExam, MockExamSubjectExam $subjectExam): JsonResponse
    {
        $this->ensureOwner($mockExam);
        $this->ensureSubjectExamBelongsToMockExam($mockExam, $subjectExam);

        $validated = $request->validate([
            'layout_config' => 'required|array',
        ]);

        // Merge incoming config with existing config to preserve section-level settings
        $existingConfig = $subjectExam->layout_config ?? [];
        $mergedConfig = array_merge($existingConfig, $validated['layout_config']);

        $subjectExam->update([
            'layout_config' => $mergedConfig,
        ]);

        return response()->json(['success' => true]);
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
        return redirect()->route('user.mock-exams.subscription.dashboard');
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

    /**
     * Download answer key / marking scheme as PDF for subscription users.
     */
    public function answerKeyDownloadFromSubscription(MockExam $mockExam): Response
    {
        $this->ensureSubscriptionAccess($mockExam);

        $data = $this->viewService->answerKeyData($mockExam);
        $this->inlineImagesForAnswerKey($data);
        $html = view('mock-exam.pdf.answer-key-pdf', $data)->render();

        $filename = preg_replace('/[^a-zA-Z0-9_\-\.\s]/', '', $mockExam->title) . '-answer-key.pdf';

        $pdf = Browsershot::html($html)
            ->setIncludePath('$PATH:/usr/local/bin')
            ->setNodeEnv(['PUPPETEER_CACHE_DIR' => config('app.puppeteer_cache_dir')])
            ->emulateMedia('print')
            ->showBackground()
            ->format('A4')
            ->margins(15, 15, 15, 15)
            ->noSandbox()
            ->setOption('args', ['--disable-dev-shm-usage'])
            ->timeout(120)
            ->pdf();

        while (ob_get_level() > 0) {
            ob_end_clean();
        }
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Content-Length: ' . strlen($pdf));
        header('X-Accel-Buffering: no');
        echo $pdf;
        exit;
    }

    /**
     * Download answer key for a single subject exam for subscription users.
     */
    public function subjectAnswerKeyDownloadFromSubscription(MockExam $mockExam, MockExamSubjectExam $subjectExam): Response
    {
        $this->ensureSubscriptionAccess($mockExam);
        $this->ensureSubjectExamBelongsToMockExam($mockExam, $subjectExam);

        // Load sections, questions, template, and academic subject
        $subjectExam->load(['sections.questions', 'template', 'academicSubject']);
        $data = $this->viewService->answerKeyData($mockExam, $subjectExam);
        $this->inlineImagesForAnswerKey($data);
        $html = view('mock-exam.pdf.answer-key-pdf', $data)->render();

        $subjectName = $subjectExam->getDisplayTitle();
        $filename = preg_replace('/[^a-zA-Z0-9_\-\.\s]/', '', $subjectName) . '-answer-key.pdf';

        $pdf = Browsershot::html($html)
            ->setIncludePath('$PATH:/usr/local/bin')
            ->setNodeEnv(['PUPPETEER_CACHE_DIR' => config('app.puppeteer_cache_dir')])
            ->emulateMedia('print')
            ->showBackground()
            ->format('A4')
            ->margins(15, 15, 15, 15)
            ->noSandbox()
            ->setOption('args', ['--disable-dev-shm-usage'])
            ->timeout(120)
            ->pdf();

        while (ob_get_level() > 0) {
            ob_end_clean();
        }
        header('Content-Type: application/pdf');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Content-Length: ' . strlen($pdf));
        header('X-Accel-Buffering: no');
        echo $pdf;
        exit;
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
