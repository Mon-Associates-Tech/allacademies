<?php

namespace App\Livewire\Admin;

use App\Models\IdCardTemplate;
use App\Models\School;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithFileUploads;

class IdCardTemplateSettings extends Component
{
    use WithFileUploads;

    public ?School $school = null;

    public string $selectedTemplate = 'professional';

    public array $customFields = [];

    public array $fieldLabels = [];

    public array $enabledOptionalFields = [];

    // Preview mode
    public bool $showPreview = false;

    protected $rules = [
        'selectedTemplate' => 'required|string|in:professional,modern,academic',
        'customFields.*' => 'nullable|string|max:255',
        'enabledOptionalFields' => 'array',
    ];

    public function mount(): void
    {
        $user = Auth::user();
        $this->school = $user->school ?? School::first();

        if ($this->school) {
            $this->selectedTemplate = $this->school->id_card_template ?? 'professional';
            $this->customFields = $this->school->id_card_custom_fields ?? [];
        }

        $this->initializeFieldLabels();
        $this->initializeEnabledOptionalFields();
    }

    protected function initializeFieldLabels(): void
    {
        $this->fieldLabels = [
            'school_name' => $this->customFields['school_name_label'] ?? 'School Name',
            'student_name' => $this->customFields['student_name_label'] ?? 'Student Name',
            'student_id' => $this->customFields['student_id_label'] ?? 'Student ID',
            'card_number' => $this->customFields['card_number_label'] ?? 'Card Number',
            'academic_level' => $this->customFields['academic_level_label'] ?? 'Class/Grade',
            'student_group' => $this->customFields['student_group_label'] ?? 'Section',
            'date_of_birth' => $this->customFields['date_of_birth_label'] ?? 'Date of Birth',
            'blood_group' => $this->customFields['blood_group_label'] ?? 'Blood Group',
            'emergency_contact' => $this->customFields['emergency_contact_label'] ?? 'Emergency Contact',
            'issue_date' => $this->customFields['issue_date_label'] ?? 'Issue Date',
            'expiry_date' => $this->customFields['expiry_date_label'] ?? 'Expiry Date',
        ];
    }

    protected function initializeEnabledOptionalFields(): void
    {
        $this->enabledOptionalFields = $this->customFields['enabled_optional_fields'] ?? [
            'academic_level',
            'student_group',
            'date_of_birth',
            'barcode',
        ];
    }

    public function selectTemplate(string $template): void
    {
        $this->selectedTemplate = $template;
    }

    public function toggleOptionalField(string $field): void
    {
        if (in_array($field, $this->enabledOptionalFields)) {
            $this->enabledOptionalFields = array_values(array_diff($this->enabledOptionalFields, [$field]));
        } else {
            $this->enabledOptionalFields[] = $field;
        }
    }

    public function updateFieldLabel(string $field, string $label): void
    {
        $this->fieldLabels[$field] = $label;
    }

    public function togglePreview(): void
    {
        $this->showPreview = ! $this->showPreview;
    }

    public function saveSettings(): void
    {
        $this->validate();

        if (! $this->school) {
            session()->flash('error', 'No school found to save settings.');

            return;
        }

        // Prepare custom fields data
        $customFieldsData = [
            'enabled_optional_fields' => $this->enabledOptionalFields,
        ];

        // Save custom labels
        foreach ($this->fieldLabels as $field => $label) {
            $customFieldsData[$field.'_label'] = $label;
        }

        // Merge with existing custom fields
        $customFieldsData = array_merge($this->customFields, $customFieldsData);

        $this->school->update([
            'id_card_template' => $this->selectedTemplate,
            'id_card_custom_fields' => $customFieldsData,
        ]);

        $this->customFields = $customFieldsData;

        session()->flash('success', 'ID card template settings saved successfully.');
    }

    public function resetToDefaults(): void
    {
        $this->selectedTemplate = 'professional';
        $this->customFields = [];
        $this->initializeFieldLabels();
        $this->initializeEnabledOptionalFields();

        session()->flash('info', 'Settings reset to defaults. Click "Save Settings" to apply.');
    }

    public function getAvailableTemplates(): array
    {
        return [
            'professional' => [
                'name' => 'Professional',
                'description' => 'Clean corporate style with blue gradient header',
                'preview' => 'id-cards/previews/professional.png',
            ],
            'modern' => [
                'name' => 'Modern',
                'description' => 'Minimalist design with card-style layout and QR code',
                'preview' => 'id-cards/previews/modern.png',
            ],
            'academic' => [
                'name' => 'Academic',
                'description' => 'Traditional academic style with school crest emphasis',
                'preview' => 'id-cards/previews/academic.png',
            ],
        ];
    }

    public function getRequiredFields(): array
    {
        return IdCardTemplate::getRequiredFieldsList();
    }

    public function getOptionalFields(): array
    {
        return IdCardTemplate::getOptionalFieldsList();
    }

    public function render()
    {
        return view('livewire.admin.id-card-template-settings', [
            'availableTemplates' => $this->getAvailableTemplates(),
            'requiredFields' => $this->getRequiredFields(),
            'optionalFields' => $this->getOptionalFields(),
        ]);
    }
}
