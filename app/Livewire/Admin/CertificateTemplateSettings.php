<?php

namespace App\Livewire\Admin;

use App\Models\Lms\CertificateTemplate;
use App\Models\School;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithFileUploads;

class CertificateTemplateSettings extends Component
{
    use WithFileUploads;

    public ?School $school = null;

    // Template management
    public bool $showCreateModal = false;

    public bool $showEditModal = false;

    public ?CertificateTemplate $editingTemplate = null;

    // Form fields
    public string $name = '';

    public string $type = 'course';

    public string $description = '';

    public string $templateFile = 'elegant';

    public string $orientation = 'landscape';

    public string $paperSize = 'a4';

    public $backgroundImage = null;

    public bool $isActive = true;

    public array $defaultFields = [];

    protected $rules = [
        'name' => 'required|string|max:255',
        'type' => 'required|string|in:course,achievement,participation',
        'description' => 'nullable|string|max:1000',
        'templateFile' => 'required|string|in:elegant,modern,professional',
        'orientation' => 'required|string|in:landscape,portrait',
        'paperSize' => 'required|string|in:a4,letter,legal',
        'backgroundImage' => 'nullable|image|max:2048',
        'isActive' => 'boolean',
    ];

    public function mount(): void
    {
        $user = Auth::user();
        $this->school = $user->school ?? School::first();
        $this->initializeDefaultFields();
    }

    protected function initializeDefaultFields(): void
    {
        $this->defaultFields = [
            'title' => 'Certificate of Completion',
            'subtitle' => 'This is to certify that',
            'body_text' => 'has successfully completed the course',
            'footer_text' => 'Awarded on',
            'signature_label' => 'Authorized Signature',
        ];
    }

    public function openCreateModal(): void
    {
        $this->resetForm();
        $this->showCreateModal = true;
    }

    public function closeCreateModal(): void
    {
        $this->showCreateModal = false;
        $this->resetForm();
    }

    public function openEditModal(int $templateId): void
    {
        $this->editingTemplate = CertificateTemplate::find($templateId);

        if ($this->editingTemplate) {
            $this->name = $this->editingTemplate->name;
            $this->type = $this->editingTemplate->type;
            $this->description = $this->editingTemplate->description ?? '';
            $this->templateFile = $this->editingTemplate->template_file;
            $this->orientation = $this->editingTemplate->orientation;
            $this->paperSize = $this->editingTemplate->paper_size;
            $this->isActive = $this->editingTemplate->is_active;
            $this->defaultFields = $this->editingTemplate->default_fields ?? $this->defaultFields;
            $this->showEditModal = true;
        }
    }

    public function closeEditModal(): void
    {
        $this->showEditModal = false;
        $this->editingTemplate = null;
        $this->resetForm();
    }

    protected function resetForm(): void
    {
        $this->name = '';
        $this->type = 'course';
        $this->description = '';
        $this->templateFile = 'elegant';
        $this->orientation = 'landscape';
        $this->paperSize = 'a4';
        $this->backgroundImage = null;
        $this->isActive = true;
        $this->initializeDefaultFields();
        $this->resetValidation();
    }

    public function createTemplate(): void
    {
        $this->validate();

        $backgroundPath = null;
        if ($this->backgroundImage) {
            $backgroundPath = $this->backgroundImage->store('certificates/backgrounds', 'public');
        }

        CertificateTemplate::create([
            'school_id' => $this->school?->id,
            'name' => $this->name,
            'slug' => Str::slug($this->name).'-'.Str::random(6),
            'type' => $this->type,
            'description' => $this->description,
            'template_file' => $this->templateFile,
            'default_fields' => $this->defaultFields,
            'background_image' => $backgroundPath,
            'orientation' => $this->orientation,
            'paper_size' => $this->paperSize,
            'is_active' => $this->isActive,
        ]);

        $this->closeCreateModal();
        session()->flash('success', 'Certificate template created successfully.');
    }

    public function updateTemplate(): void
    {
        $this->validate();

        if (! $this->editingTemplate) {
            return;
        }

        $data = [
            'name' => $this->name,
            'type' => $this->type,
            'description' => $this->description,
            'template_file' => $this->templateFile,
            'default_fields' => $this->defaultFields,
            'orientation' => $this->orientation,
            'paper_size' => $this->paperSize,
            'is_active' => $this->isActive,
        ];

        if ($this->backgroundImage) {
            $data['background_image'] = $this->backgroundImage->store('certificates/backgrounds', 'public');
        }

        $this->editingTemplate->update($data);

        $this->closeEditModal();
        session()->flash('success', 'Certificate template updated successfully.');
    }

    public function toggleTemplateStatus(int $templateId): void
    {
        $template = CertificateTemplate::find($templateId);

        if ($template) {
            $template->update(['is_active' => ! $template->is_active]);
            session()->flash('success', 'Template status updated.');
        }
    }

    public function deleteTemplate(int $templateId): void
    {
        $template = CertificateTemplate::find($templateId);

        if ($template) {
            // Check if template has issued certificates
            if ($template->issuedCertificates()->exists()) {
                session()->flash('error', 'Cannot delete template with issued certificates.');

                return;
            }

            $template->delete();
            session()->flash('success', 'Certificate template deleted successfully.');
        }
    }

    public function duplicateTemplate(int $templateId): void
    {
        $template = CertificateTemplate::find($templateId);

        if ($template) {
            $newTemplate = $template->replicate();
            $newTemplate->name = $template->name.' (Copy)';
            $newTemplate->slug = Str::slug($newTemplate->name).'-'.Str::random(6);
            $newTemplate->save();

            session()->flash('success', 'Certificate template duplicated successfully.');
        }
    }

    public function getAvailableTemplateFiles(): array
    {
        return [
            'elegant' => [
                'name' => 'Elegant',
                'description' => 'Classic certificate with ornate borders and traditional styling',
            ],
            'modern' => [
                'name' => 'Modern',
                'description' => 'Clean, minimalist design with contemporary aesthetics',
            ],
            'professional' => [
                'name' => 'Professional',
                'description' => 'Corporate style with badges and formal layout',
            ],
        ];
    }

    public function render()
    {
        $templates = CertificateTemplate::query()
            ->when($this->school, function ($query) {
                $query->forSchool($this->school->id);
            })
            ->orderBy('name')
            ->get();

        return view('livewire.admin.certificate-template-settings', [
            'templates' => $templates,
            'templateTypes' => CertificateTemplate::getTypes(),
            'orientations' => CertificateTemplate::getOrientations(),
            'paperSizes' => CertificateTemplate::getPaperSizes(),
            'availableTemplateFiles' => $this->getAvailableTemplateFiles(),
        ]);
    }
}
