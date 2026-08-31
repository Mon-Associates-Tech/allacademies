<?php

namespace App\Livewire\Accountant\Templates;

use App\Models\MessageTemplate;
use Livewire\Component;

class TemplateManager extends Component
{
    public bool $showForm = false;

    public ?int $editingId = null;

    public string $name = '';

    public string $category = 'general';

    public string $subject = '';

    public string $body = '';

    public string $smsBody = '';

    public string $search = '';

    protected function rules(): array
    {
        return [
            'name'     => 'required|string|max:255',
            'category' => 'required|in:fee,event,general,reminder',
            'subject'  => 'required|string|max:255',
            'body'     => 'required|string',
            'smsBody'  => 'nullable|string|max:160',
        ];
    }

    public function create(): void
    {
        $this->resetForm();
        $this->showForm  = true;
        $this->editingId = null;
    }

    public function edit(int $id): void
    {
        $template = MessageTemplate::findOrFail($id);

        if ($template->is_system) {
            session()->flash('error', 'System templates cannot be edited. Duplicate it to customise.');

            return;
        }

        $this->editingId = $id;
        $this->name      = $template->name;
        $this->category  = $template->category;
        $this->subject   = $template->subject;
        $this->body      = $template->body;
        $this->smsBody   = $template->sms_body ?? '';
        $this->showForm  = true;
    }

    public function duplicate(int $id): void
    {
        $template = MessageTemplate::findOrFail($id);

        $this->resetForm();
        $this->name     = 'Copy of '.$template->name;
        $this->category = $template->category;
        $this->subject  = $template->subject;
        $this->body     = $template->body;
        $this->smsBody  = $template->sms_body ?? '';
        $this->showForm = true;
    }

    public function save(): void
    {
        $this->validate();

        $schoolId = auth()->user()->school_id;
        $slug     = \Illuminate\Support\Str::slug($this->name).'-'.$schoolId;

        $data = [
            'school_id' => $schoolId,
            'slug'      => $slug,
            'name'      => $this->name,
            'category'  => $this->category,
            'subject'   => $this->subject,
            'body'      => $this->body,
            'sms_body'  => $this->smsBody ?: null,
            'is_system' => false,
            'is_active' => true,
        ];

        if ($this->editingId) {
            MessageTemplate::where('id', $this->editingId)
                ->where('school_id', $schoolId)
                ->update($data);
            session()->flash('success', 'Template updated.');
        } else {
            MessageTemplate::create($data);
            session()->flash('success', 'Template created.');
        }

        $this->showForm = false;
        $this->resetForm();
    }

    public function delete(int $id): void
    {
        $template = MessageTemplate::where('id', $id)
            ->where('school_id', auth()->user()->school_id)
            ->where('is_system', false)
            ->firstOrFail();

        $template->delete();
        session()->flash('success', 'Template deleted.');
    }

    public function cancel(): void
    {
        $this->showForm = false;
        $this->resetForm();
    }

    protected function resetForm(): void
    {
        $this->editingId = null;
        $this->name      = '';
        $this->category  = 'general';
        $this->subject   = '';
        $this->body      = '';
        $this->smsBody   = '';
        $this->resetValidation();
    }

    public function render()
    {
        $schoolId = auth()->user()->school_id;

        $templates = MessageTemplate::forSchoolOrSystem($schoolId)
            ->active()
            ->when($this->search, fn ($q) => $q->where('name', 'like', "%{$this->search}%"))
            ->orderByRaw('is_system DESC')
            ->orderBy('name')
            ->get();

        return view('livewire.accountant.templates.template-manager', [
            'templates' => $templates,
        ]);
    }
}
