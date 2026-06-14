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

    // ── Block state ───────────────────────────────────────────────────────────
    /**
     * Ordered list of front-page blocks.
     *
     * Each block is an array with at minimum: id (UUID), type.
     * Additional keys depend on type:
     *   heading    – level (h1/h2/h3), content (string)
     *   richtext   – content (HTML string)
     *   image      – src, alt, width (px int), alignment (left/center/right), source_type (url/upload), url_input
     *   divider    – (no extra keys)
     *   info_table – fields (string[])
     */
    public array $frontPageBlocks = [];

    /** Temporary upload slot used by uploadBlockImage(). */
    #[Validate('nullable|image|max:30720')]
    public $pendingImage = null;

    // ── Mount ─────────────────────────────────────────────────────────────────

    public function mount(?MockExamTemplate $template = null): void
    {
        if ($template && $template->exists) {
            $this->templateId      = $template->id;
            $this->frontPageBlocks = ($template->front_page_config['blocks'] ?? []);
        }
    }

    // ── Block management ──────────────────────────────────────────────────────

    public function addBlock(string $type): void
    {
        $base = ['id' => Str::uuid()->toString(), 'type' => $type];

        $this->frontPageBlocks[] = match ($type) {
            'heading'    => $base + ['level' => 'h2', 'content' => ''],
            'richtext'   => $base + ['content' => ''],
            'image'      => $base + [
                                'src'         => '',
                                'alt'         => '',
                                'width'       => 300,
                                'alignment'   => 'center',
                                'source_type' => 'url',
                                'url_input'   => '',
                            ],
            'divider'    => $base,
            'info_table' => $base + ['fields' => ['candidate_name', 'date', 'duration']],
            default      => $base,
        };
    }

    public function removeBlock(int $index): void
    {
        array_splice($this->frontPageBlocks, $index, 1);
        $this->frontPageBlocks = array_values($this->frontPageBlocks);
    }

    public function moveBlock(int $index, string $direction): void
    {
        $target = $direction === 'up' ? $index - 1 : $index + 1;

        if ($target < 0 || $target >= count($this->frontPageBlocks)) {
            return;
        }

        [$this->frontPageBlocks[$index], $this->frontPageBlocks[$target]] =
            [$this->frontPageBlocks[$target], $this->frontPageBlocks[$index]];
    }

    /**
     * Called from the @script Alpine bridge whenever a rich-text block's
     * content changes — updates the Livewire property without a full re-render.
     */
    public function updateBlockContent(int $index, string $html): void
    {
        if (isset($this->frontPageBlocks[$index])) {
            $this->frontPageBlocks[$index]['content'] = $html;
        }
    }

    /** Apply the URL typed into an image block's url_input field as its src. */
    public function applyImageUrl(int $index): void
    {
        $url = trim($this->frontPageBlocks[$index]['url_input'] ?? '');
        $this->frontPageBlocks[$index]['src'] = $url;
    }

    /**
     * Called from the Alpine @change handler via $wire.upload() after the
     * browser has streamed the file to Livewire.
     */
    public function uploadBlockImage(int $index): void
    {
        $this->validateOnly('pendingImage');

        $path = $this->pendingImage->store('mock-exam-front-pages', 'public');
        $url  = Storage::disk('public')->url($path);

        $this->frontPageBlocks[$index]['src'] = $url;
        $this->pendingImage = null;

        // Tell any Alpine listeners the new src so they can update previews
        // without waiting for a full Livewire re-render.
        $this->dispatch('block-image-ready', index: $index, src: $url);
    }

    /** Toggle a single field on/off in an info_table block's fields array. */
    public function toggleInfoField(int $blockIndex, string $field): void
    {
        $fields = $this->frontPageBlocks[$blockIndex]['fields'] ?? [];

        $this->frontPageBlocks[$blockIndex]['fields'] = in_array($field, $fields, true)
            ? array_values(array_filter($fields, fn ($f) => $f !== $field))
            : [...$fields, $field];
    }

    // ── Navigation ────────────────────────────────────────────────────────────

    /**
     * Serialise the current blocks to session and hand off to Step 2.
     *
     * The configure view reads the JSON from session and embeds it as a hidden
     * <input> so it travels with the normal form POST.
     */
    public function proceed(): void
    {
        session(['template_front_page_config' => json_encode(['blocks' => $this->frontPageBlocks])]);

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