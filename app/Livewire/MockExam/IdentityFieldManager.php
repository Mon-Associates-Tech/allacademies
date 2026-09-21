<?php
// app/Livewire/MockExam/IdentityFieldManager.php
namespace App\Livewire\MockExam;

use App\Enums\UserRole;
use App\MockExam\Models\MockExamIdentityField;
use Illuminate\Validation\Rule;
use Livewire\Component;

class IdentityFieldManager extends Component
{
    public $fields = [];

    public ?int $editingId = null;
    public string $key = '';
    public string $label = '';
    public string $type = 'text';
    public ?int $fontSize = 16;
    public bool $sameRow = false;
    public ?string $pretext = null;
    public bool $isRequired = false;

    public function mount(): void
    {
        // Defense in depth — the wrapping controller already gates this route,
        // but Livewire's own update endpoint is independently reachable.
        //  abort_unless(auth()->user()?->role === UserRole::OWNER, 403);

        $this->loadFields();
    }

    public function loadFields(): void
    {
        $this->fields = MockExamIdentityField::ordered()->get()->all();
    }

    public function edit(int $id): void
    {
        $field = MockExamIdentityField::findOrFail($id);

        $this->editingId = $field->id;
        $this->key = $field->key;
        $this->label = $field->label;
        $this->type = $field->type;
        $this->pretext = $field->pretext;
        $this->fontSize = $field->font_size;
        $this->sameRow = $field->same_row;
        $this->isRequired = $field->is_required;
    }

    public function save(): void
    {
        $data = $this->validate([
            'key' => [
                'required', 'string', 'max:100', 'regex:/^[a-z0-9_]+$/',
                $this->editingId
                    ? Rule::unique('mock_exam_identity_fields', 'key')->ignore($this->editingId)
                    : Rule::unique('mock_exam_identity_fields', 'key'),
            ],
            'label' => ['required', 'string', 'max:255'],
            'type' => ['required', 'in:text,image'],
            'pretext' => ['nullable', 'string', 'max:255'],
            'fontSize' => ['nullable', 'integer', 'min:1', 'max:72'],
            'sameRow' => ['boolean'],
            'isRequired' => ['boolean'],
        ]);

        if ($this->editingId) {
            MockExamIdentityField::findOrFail($this->editingId)->update([
                'key' => $data['key'], 'label' => $data['label'], 'type' => $data['type'],
                'pretext' => $data['pretext'], 'is_required' => $data['isRequired'],
                'font_size' => $data['fontSize'], 'same_row' => $data['sameRow'],
            ]);
        } else {
            MockExamIdentityField::create([
                'key' => $data['key'], 'label' => $data['label'], 'type' => $data['type'],
                'pretext' => $data['pretext'], 'is_required' => $data['isRequired'],
                'font_size' => $data['fontSize'], 'same_row' => $data['sameRow'],
                'sort_order' => (MockExamIdentityField::max('sort_order') ?? 0) + 1,
            ]);
        }

        $this->resetForm();
        $this->loadFields();
    }

    public function delete(int $id): void
    {
        MockExamIdentityField::findOrFail($id)->delete();
        $this->loadFields();
    }

    public function moveUp(int $id): void
    {
        $this->swapOrder($id, -1);
    }

    public function moveDown(int $id): void
    {
        $this->swapOrder($id, 1);
    }

    private function swapOrder(int $id, int $direction): void
    {
        $ordered = MockExamIdentityField::ordered()->get();
        $index = $ordered->search(fn($f) => $f->id === $id);
        $swapIndex = $index + $direction;

        if ($index === false || $swapIndex < 0 || $swapIndex >= $ordered->count()) {
            return;
        }

        [$current, $swap] = [$ordered[$index], $ordered[$swapIndex]];
        [$current->sort_order, $swap->sort_order] = [$swap->sort_order, $current->sort_order];
        $current->save();
        $swap->save();

        $this->loadFields();
    }

    public function resetForm(): void
    {
        $this->reset(['editingId', 'key', 'label', 'pretext', 'isRequired', 'sameRow']);
        $this->type = 'text';
        $this->fontSize = 16;
    }

    public function render()
    {
        return view('livewire.mock-exam.identity-field-manager');
    }
}
