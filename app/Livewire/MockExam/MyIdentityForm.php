<?php
namespace App\Livewire\MockExam;

use App\MockExam\Models\MockExamIdentityField;
use App\MockExam\Models\MockExamUserIdentity;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithFileUploads;

class MyIdentityForm extends Component
{
    use WithFileUploads;

    public array $fields = [];
    public array $values = [];
    public array $pendingUploads = [];

    public function mount(): void
    {
        $this->fields = MockExamIdentityField::ordered()->get()->all();

        $identity = MockExamUserIdentity::where('user_id', Auth::id())->first();

        foreach ($this->fields as $field) {
            $this->values[$field->key] = $identity?->valueFor($field->key);
        }
    }

    public function removeImage(string $key): void
    {
        $this->values[$key] = null;
        unset($this->pendingUploads[$key]);
    }

    public function save(): void
    {
        $rules = [];

        foreach ($this->fields as $field) {
            if ($field->type === 'image') {
                $required = $field->is_required && empty($this->values[$field->key]);
                $rules["pendingUploads.{$field->key}"] = [$required ? 'required' : 'nullable', 'image', 'max:2048'];
            } else {
                $rules["values.{$field->key}"] = [$field->is_required ? 'required' : 'nullable', 'string', 'max:255'];
            }
        }

        $this->validate($rules);

        foreach ($this->fields as $field) {
            if ($field->type === 'image' && isset($this->pendingUploads[$field->key])) {
                $this->values[$field->key] = $this->pendingUploads[$field->key]->store('mock-exam-identity', 'public');
            }
        }

        $identity = MockExamUserIdentity::firstOrNew(['user_id' => Auth::id()]);
        $identity->values = $this->values;
        $identity->save();

        $this->pendingUploads = [];

        session()->flash('success', 'Your exam identity details have been saved.');
    }

    public function render()
    {
        return view('livewire.mock-exam.my-identity-form');
    }
}
