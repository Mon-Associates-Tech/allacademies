<?php

namespace App\Livewire\Common;

use Livewire\Component;

class RichEditor extends Component
{
    /** The HTML content — bind with wire:model */
    public string $value = '';

    /** Optional form field name for non-Livewire (plain POST) usage */
    public ?string $fieldName = null;

    /** Editor height in pixels */
    public int $height = 300;

    /** Optional label shown above the editor */
    public ?string $label = null;

    /** Whether the field is required */
    public bool $required = false;

    /** Unique DOM id — auto-generated on mount if not provided */
    public string $editorId = '';

    public function mount(
        string $value = '',
        ?string $fieldName = null,
        int $height = 300,
        ?string $label = null,
        bool $required = false,
        string $editorId = '',
    ): void {
        $this->value    = $value;
        $this->fieldName = $fieldName;
        $this->height   = $height;
        $this->label    = $label;
        $this->required = $required;
        $this->editorId = $editorId ?: 'rich-editor-' . uniqid();
    }

    /** Called from JS via $wire.setContent() on editor change/blur */
    public function setContent(string $html): void
    {
        $this->value = $html;
        $this->dispatch('rich-editor-updated', id: $this->editorId, value: $html);
    }

    public function render()
    {
        return view('livewire.common.rich-editor');
    }
}
