<?php

namespace App\Livewire\SchoolSettings;

use App\Models\SchoolSetting;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;

class Index extends Component
{
    use WithFileUploads;

    public $settings = [];
    public $showSettingModal = false;
    public $showValueModal = false;
    public $showDeleteModal = false;

    // Setting form properties
    public $settingId = null;
    public $key = '';
    public $type = 'text';
    public $label = '';
    public $description = '';
    public $group = 'general';
    public $options = [];
    public $required = false;
    public $sortOrder = 0;
    public $isEditing = false;

    // Value form properties
    public $currentSetting = null;
    public $value = '';
    public $fileValue = null;
    public $currentOptions = []; // Add this for managing options in value modal

    // Delete confirmation
    public $settingToDelete = null;

    protected $rules = [
        'key' => 'required|string|max:255',
        'type' => 'required|in:text,longtext,image,json,pdf,boolean,number,select,radio',
        'label' => 'required|string|max:255',
        'description' => 'nullable|string',
        'group' => 'required|string|max:255',
        'options' => 'nullable|array',
        'required' => 'boolean',
        'sortOrder' => 'integer'
    ];

    public function mount()
    {
        $this->loadSettings();
    }

    public function loadSettings()
    {
        $this->settings = SchoolSetting::getGrouped();
    }

    public function showCreateModal()
    {
        $this->resetSettingForm();
        $this->showSettingModal = true;
    }

    public function showEditModal($settingId)
    {
        try {
            $setting = SchoolSetting::findOrFail($settingId);

            $this->settingId = $setting->id;
            $this->key = $setting->key;
            $this->type = $setting->type;
            $this->label = $setting->label;
            $this->description = $setting->description;
            $this->group = $setting->group;
            $this->options = $setting->options ?: [];
            $this->required = $setting->required;
            $this->sortOrder = $setting->sort_order;
            $this->isEditing = true;

            $this->showSettingModal = true;
            $this->currentSetting  = $setting;
        } catch (\Exception $e) {
            session()->flash('error', 'Setting not found.');
        }
    }

    public function showValueEditModal($settingId): void
    {
        try {
            $this->currentSetting = SchoolSetting::findOrFail($settingId);
            $this->value = $this->currentSetting->raw_value ?? '';
            $this->fileValue = null;
            $this->currentOptions = $this->currentSetting->options ?: [];
            $this->showValueModal = true;
        } catch (\Exception $e) {
            session()->flash('error', 'Setting not found.');
        }
    }

    public function saveSetting()
    {
        $this->validate();

        // Additional validation for unique key
        $keyRule = $this->isEditing
            ? 'unique:school_settings,key,' . $this->settingId
            : 'unique:school_settings,key';

        $this->validate(['key' => $keyRule]);

        $data = [
            'key' => $this->key,
            'type' => $this->type,
            'label' => $this->label,
            'description' => $this->description,
            'group' => $this->group,
            'options' => array_filter($this->options), // Filter out empty options
            'required' => $this->required,
            'sort_order' => $this->sortOrder,
        ];

        if ($this->isEditing) {
            SchoolSetting::findOrFail($this->settingId)->update($data);
            session()->flash('success', 'Setting updated successfully.');
        } else {
            SchoolSetting::create($data);
            session()->flash('success', 'Setting created successfully.');
        }

        $this->closeSettingModal();
        $this->loadSettings();
    }

    public function saveValue()
    {
        $setting = $this->currentSetting;
        $value = $this->value;

        // Validate based on setting type
        $rules = $this->getValueValidationRules($setting);
        $this->validate($rules);

        // Handle file uploads
        if ($this->fileValue) {
            // Delete old file if exists
            if ($setting->raw_value && in_array($setting->type, ['image', 'pdf'])) {
                Storage::delete($setting->raw_value);
            }

            $path = $this->fileValue->store('settings', 'public');
            $value = $path;
        }

        // Update data
        $updateData = ['value' => $value];

        // If it's a select or radio type, also update the options
        if (in_array($setting->type, ['select', 'radio'])) {
            $updateData['options'] = array_filter($this->currentOptions);
        }

        $setting->update($updateData);

        session()->flash('success', 'Setting value updated successfully.');
        $this->closeValueModal();
        $this->loadSettings();
    }

    public function confirmDelete($settingId)
    {
        try {
            $this->settingToDelete = SchoolSetting::findOrFail($settingId);
            $this->showDeleteModal = true;
        } catch (\Exception $e) {
            session()->flash('error', 'Setting not found.');
        }
    }

    public function deleteSetting()
    {
        if ($this->settingToDelete) {
            // Delete associated file if exists
            if (in_array($this->settingToDelete->type, ['image', 'pdf']) && $this->settingToDelete->raw_value) {
                Storage::delete($this->settingToDelete->raw_value);
            }

            $this->settingToDelete->delete();
            session()->flash('success', 'Setting deleted successfully.');

            $this->closeDeleteModal();
            $this->loadSettings();
        }
    }

    public function closeSettingModal()
    {
        $this->showSettingModal = false;
        $this->currentSetting = null;
        $this->resetSettingForm();
    }

    public function closeValueModal()
    {
        $this->showValueModal = false;
        $this->currentSetting = null;
        $this->value = '';
        $this->fileValue = null;
        $this->currentOptions = [];
    }

    public function closeDeleteModal()
    {
        $this->showDeleteModal = false;
        $this->settingToDelete = null;
    }

    private function resetSettingForm()
    {
        $this->settingId = null;
        $this->key = '';
        $this->type = 'text';
        $this->label = '';
        $this->description = '';
        $this->group = 'general';
        $this->options = [];
        $this->required = false;
        $this->sortOrder = 0;
        $this->isEditing = false;
    }

    private function getValueValidationRules($setting)
    {
        $rules = [];

        switch ($setting->type) {
            case 'image':
                $rules['fileValue'] = 'required|image|mimes:jpeg,png,jpg,gif|max:2048';
                break;
            case 'pdf':
                $rules['fileValue'] = 'required|mimes:pdf|max:10240';
                break;
            case 'json':
                $rules['value'] = 'json';
                break;
            case 'number':
                $rules['value'] = 'numeric';
                break;
            case 'boolean':
                $rules['value'] = 'boolean';
                break;
            default:
                $rules['value'] = $setting->required ? 'required' : 'nullable';
        }

        return $rules;
    }

    // Options management for setting creation/editing
    public function addOption()
    {
        $this->options[] = '';
    }

    public function removeOption($index)
    {
        unset($this->options[$index]);
        $this->options = array_values($this->options);
    }

    // Options management for value editing modal
    public function addSettingOption()
    {
        $this->currentOptions[] = '';
    }

    public function removeSettingOption($index)
    {
        unset($this->currentOptions[$index]);
        $this->currentOptions = array_values($this->currentOptions);
    }

    public function render()
    {
        return view('livewire.school-settings.SchoolSettingsManagementView');
    }
}
