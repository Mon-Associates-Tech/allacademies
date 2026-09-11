<?php

namespace App\Livewire\MockExam;

use App\MockExam\Models\MockExamTemplate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithFileUploads;

class FrontPageBuilder extends Component
{
    use WithFileUploads;

    // ── Context ───────────────────────────────────────────────────────────────
    /** Null when creating a new template, set when editing. */
    public ?int $templateId = null;

    public ?MockExamTemplate $template = null; // Added to hold template data for preview


// with:
    // ── Content state ─────────────────────────────────────────────────────────
    /** Rich text HTML for the front page body, edited via the rich text editor. */
    public string $content = '';

    // ── Mount ─────────────────────────────────────────────────────────────────

    public function mount(?MockExamTemplate $template = null): void
    {
        $this->template = $template;

        if ($template && $template->exists) {
            $this->templateId = $template->id;
            $this->content     = $template->front_page_config['content'] ?? '';
        }
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

        $template = MockExamTemplate::findOrFail($this->templateId);
        abort_unless($template->user_id === \Illuminate\Support\Facades\Auth::id(), 403);

        $template->update(['front_page_config' => ['content' => $this->content]]);
        session()->forget('template_front_page_config');

        session()->flash('success', 'Front page saved.');
    }

    /**
     * Serialise the current blocks to session and hand off to Step 2.
     *
     * The configure view reads the JSON from session and embeds it as a hidden
     * <input> so it travels with the normal form POST.
     */
    public function proceed(): void
    {
        session(['template_front_page_config' => json_encode(['content' => $this->content])]);

        $redirect = $this->templateId
            ? route('mock-exams.templates.edit', $this->templateId)       // Step 2, edit flow
            : route('mock-exams.templates.configure-create');              // Step 2, create flow

        $this->redirect($redirect, navigate: false);
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