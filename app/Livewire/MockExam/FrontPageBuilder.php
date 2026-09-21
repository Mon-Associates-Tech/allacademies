<?php

namespace App\Livewire\MockExam;

use App\MockExam\Models\MockExamTemplate;
use App\MockExam\Services\MockExamAttachmentService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithFileUploads;

class FrontPageBuilder extends Component
{
    use WithFileUploads;

    // ── Context ───────────────────────────────────────────────────────────────
    /** Null when creating a new template, set when editing. */
    public ?int $templateId = null;

    public ?MockExamTemplate $template = null;

    // ── Mode ──────────────────────────────────────────────────────────────────
    /** 'editor' (rich text) or 'upload' (an existing PDF used as-is). */
    public string $mode = 'editor';

    // ── Editor mode state ────────────────────────────────────────────────────
    /** Rich text HTML for the front page body, edited via the rich text editor. */
    public string $content = '';

    // ── Upload mode state ────────────────────────────────────────────────────
    #[Validate('nullable|file|mimes:pdf|max:10240')]
    public $documentUpload = null;

    public ?string $attachmentOriginalName = null;
    public ?string $attachmentExtension = null;
    public ?array $attachmentPdfImages = null;

    // ── Dependencies ──────────────────────────────────────────────────────────
    protected MockExamAttachmentService $attachmentService;

    public function boot(MockExamAttachmentService $attachmentService): void
    {
        $this->attachmentService = $attachmentService;
    }

    // ── Mount ─────────────────────────────────────────────────────────────────

    public function mount(?MockExamTemplate $template = null): void
    {
        $this->template = $template;

        if ($template && $template->exists) {
            $this->templateId = $template->id;

            $config = $template->front_page_config ?? [];

            // 'mode' won't exist on templates saved before this feature — defaults
            // to 'editor', which matches the old {'content': ...} shape exactly.
            $this->mode                   = $config['mode'] ?? 'editor';
            $this->content                = $config['content'] ?? '';
            $this->attachmentOriginalName = $config['attachment_original_name'] ?? null;
            $this->attachmentExtension    = $config['attachment_extension'] ?? null;
            $this->attachmentPdfImages    = $config['attachment_pdf_images'] ?? null;
        }
    }

    // ── Mode switching ───────────────────────────────────────────────────────

    public function switchMode(string $mode): void
    {
        $this->mode = $mode === 'upload' ? 'upload' : 'editor';
        $this->resetErrorBag('documentUpload');
    }

    // ── Upload handling ──────────────────────────────────────────────────────

    /**
     * Fires automatically once Livewire finishes the temporary upload of
     * $documentUpload (the #[Validate] attribute above already enforces
     * pdf-only, <=10MB before this runs). Converts the PDF's pages to images
     * via the same MockExamAttachmentService used for section attachments.
     */
    public function updatedDocumentUpload(): void
    {
        if (! $this->documentUpload) {
            return;
        }

        $result = $this->attachmentService->process($this->documentUpload);

        $this->attachmentOriginalName = $result['attachment_original_name'];
        $this->attachmentExtension    = $result['attachment_extension'];
        $this->attachmentPdfImages    = $result['attachment_pdf_images'];

        $this->mode = 'upload';
        $this->documentUpload = null;
    }

    public function removeDocument(): void
    {
        $this->attachmentOriginalName = null;
        $this->attachmentExtension    = null;
        $this->attachmentPdfImages    = null;
    }

    // ── Navigation ────────────────────────────────────────────────────────────

    /**
     * Persist only the front_page_config on an existing template, then stay on this page.
     * Only available in the edit flow (templateId is set).
     */
    public function saveFrontPage(): void
    {
        if (! $this->templateId) {
            return;
        }

        if (! $this->ensureModeIsComplete()) {
            return;
        }

        $template = MockExamTemplate::findOrFail($this->templateId);
        abort_unless($template->user_id === Auth::id(), 403);

        $template->update(['front_page_config' => $this->buildFrontPageConfig()]);
        session()->forget('template_front_page_config');

        session()->flash('success', 'Front page saved.');
    }

    /**
     * Serialise the current front page config to session and hand off to Step 2.
     *
     * The configure view reads the JSON from session and embeds it as a hidden
     * <input> so it travels with the normal form POST.
     */
    public function proceed(): void
    {
        if (! $this->ensureModeIsComplete()) {
            return;
        }

        session(['template_front_page_config' => json_encode($this->buildFrontPageConfig())]);

        $redirect = $this->templateId
            ? route('mock-exams.templates.edit', $this->templateId)       // Step 2, edit flow
            : route('mock-exams.templates.configure-create');              // Step 2, create flow

        $this->redirect($redirect, navigate: false);
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    private function buildFrontPageConfig(): array
    {
        if ($this->mode === 'upload') {
            return [
                'mode' => 'upload',
                'attachment_original_name' => $this->attachmentOriginalName,
                'attachment_extension'     => $this->attachmentExtension,
                'attachment_pdf_images'    => $this->attachmentPdfImages,
            ];
        }

        return [
            'mode'    => 'editor',
            'content' => $this->content,
        ];
    }

    private function ensureModeIsComplete(): bool
    {
        if ($this->mode === 'upload' && empty($this->attachmentPdfImages)) {
            $this->addError('documentUpload', 'Please upload a PDF before continuing.');

            return false;
        }

        return true;
    }

    // ── Render ────────────────────────────────────────────────────────────────

    public function render(): \Illuminate\View\View
    {
        $hierarchyTree = \App\MockExam\Models\MockExam::hierarchyTree();

        return view('livewire.mock-exam.front-page-builder',
            ['hierarchyTree' => $hierarchyTree]
        );
    }
}