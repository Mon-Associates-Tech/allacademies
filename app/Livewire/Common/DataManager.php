<?php

namespace App\Livewire\Common;

use App\Services\ImportExportService;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\Validate;
use Illuminate\Support\Facades\Storage;

class DataManager extends Component
{
    use WithFileUploads;

    public $activeOperation = 'import'; // 'import' or 'export'
    public $selectedModel = '';
    public $importFile;
    public $exportFormat = 'csv';
    public $includeRelations = false;
    public $exportFilters = [];
    public $importOptions = [];
    public $isProcessing = false;
    public $processingMessage = '';
    public $lastResult = null;
    public $showAdvancedOptions = false;

    #[Validate('required|file|mimes:csv,xlsx|max:10240')] // 10MB max
    public $uploadedFile;

    protected $listeners = ['refreshComponent' => '$refresh'];

    public function mount()
    {
        $this->resetState();
    }

    public function updatedActiveOperation()
    {
        $this->resetState();
    }

    public function updatedSelectedModel()
    {
        $this->resetModelSpecificOptions();
    }

    private function resetState()
    {
        $this->selectedModel = '';
        $this->importFile = null;
        $this->uploadedFile = null;
        $this->exportFilters = [];
        $this->importOptions = [];
        $this->lastResult = null;
        $this->isProcessing = false;
        $this->showAdvancedOptions = false;
    }

    private function resetModelSpecificOptions()
    {
        $this->exportFilters = [];
        $this->importOptions = [];
        $this->showAdvancedOptions = false;
    }

    public function getAvailableModels()
    {
        return [
            'students' => [
                'label' => 'Students',
                'description' => 'Import/Export student records with user accounts',
                'icon' => 'academic-cap',
                'import_supported' => true,
                'export_supported' => true,
            ],
            'teachers' => [
                'label' => 'Teachers',
                'description' => 'Import/Export teacher records and assignments',
                'icon' => 'user-group',
                'import_supported' => true,
                'export_supported' => true,
            ],
            'books' => [
                'label' => 'Books',
                'description' => 'Import/Export book catalog and metadata',
                'icon' => 'book-open',
                'import_supported' => true,
                'export_supported' => true,
            ],
            'assignments' => [
                'label' => 'Assignments',
                'description' => 'Import/Export assignments and tasks',
                'icon' => 'clipboard-document-list',
                'import_supported' => false,
                'export_supported' => true,
            ],
            'academic_subjects' => [
                'label' => 'Academic Subjects',
                'description' => 'Import/Export subject definitions',
                'icon' => 'squares-plus',
                'import_supported' => true,
                'export_supported' => true,
            ],
            'academic_levels' => [
                'label' => 'Academic Levels',
                'description' => 'Import/Export grade levels and classes',
                'icon' => 'bars-3-bottom-left',
                'import_supported' => true,
                'export_supported' => true,
            ],
            'schools' => [
                'label' => 'Schools',
                'description' => 'Import/Export school information',
                'icon' => 'building-office-2',
                'import_supported' => true,
                'export_supported' => true,
            ],
            'library_cards' => [
                'label' => 'Library Cards',
                'description' => 'Export library card records',
                'icon' => 'identification',
                'import_supported' => false,
                'export_supported' => true,
            ],
        ];
    }

    public function getModelFilters()
    {
        $filters = [];

        switch ($this->selectedModel) {
            case 'students':
                $filters = [
                    'academic_level_id' => 'Filter by Academic Level',
                    'academic_group_id' => 'Filter by Academic Group',
                    'school_id' => 'Filter by School',
                    'has_library_card' => 'Only with Library Cards',
                    'active_only' => 'Active Students Only',
                ];
                break;
            case 'teachers':
                $filters = [
                    'school_id' => 'Filter by School',
                    'has_students' => 'Only with Assigned Students',
                    'has_subjects' => 'Only with Assigned Subjects',
                ];
                break;
            case 'books':
                $filters = [
                    'category' => 'Filter by Category',
                    'availability' => 'Filter by Availability',
                    'published_after' => 'Published After Date',
                ];
                break;
        }

        return $filters;
    }

    public function getImportOptions()
    {
        $options = [];

        switch ($this->selectedModel) {
            case 'students':
                $options = [
                    'create_missing_levels' => 'Create missing Academic Levels',
                    'create_missing_groups' => 'Create missing Academic Groups',
                    'create_missing_schools' => 'Create missing Schools',
                    'default_password' => 'Default password for new users',
                    'send_welcome_email' => 'Send welcome emails to new students',
                    'assign_library_card' => 'Auto-assign library cards',
                ];
                break;
            case 'teachers':
                $options = [
                    'create_missing_subjects' => 'Create missing Academic Subjects',
                    'create_missing_levels' => 'Create missing Academic Levels',
                    'default_password' => 'Default password for new users',
                    'send_welcome_email' => 'Send welcome emails to new teachers',
                ];
                break;
            case 'books':
                $options = [
                    'create_missing_categories' => 'Create missing Categories',
                    'auto_generate_isbn' => 'Auto-generate ISBN if missing',
                    'default_availability' => 'Default availability status',
                ];
                break;
        }

        return $options;
    }

    public function validateFile()
    {
        $this->validate();

        if (!$this->uploadedFile) {
            $this->addError('uploadedFile', 'Please select a file to upload.');
            return;
        }

        $this->processingMessage = 'Validating file structure...';
        $this->isProcessing = true;

        try {
            $service = new ImportExportService();
            $result = $service->validateImportFile($this->uploadedFile, $this->selectedModel);

            if ($result['valid']) {
                $this->lastResult = [
                    'type' => 'validation_success',
                    'message' => $result['message'],
                    'details' => $result
                ];
                session()->flash('success', 'File validation successful! Ready to import.');
            } else {
                $this->addError('uploadedFile', $result['message']);
                $this->lastResult = [
                    'type' => 'validation_error',
                    'message' => $result['message'],
                    'details' => $result
                ];
            }
        } catch (\Exception $e) {
            $this->addError('uploadedFile', 'File validation failed: ' . $e->getMessage());
        } finally {
            $this->isProcessing = false;
        }
    }

    public function performImport()
    {
        $this->validate();

        if (!$this->uploadedFile) {
            $this->addError('uploadedFile', 'Please select a file to upload.');
            return;
        }

        $this->processingMessage = 'Importing data... This may take a few moments.';
        $this->isProcessing = true;

        try {
            $service = new ImportExportService();
            $result = $service->performImport(
                $this->uploadedFile,
                $this->selectedModel,
                $this->importOptions
            );

            if ($result['success']) {
                $this->lastResult = [
                    'type' => 'import_success',
                    'message' => $result['message'],
                    'stats' => $result['stats']
                ];
                session()->flash('success', 'Import completed successfully!');
                $this->uploadedFile = null;
            } else {
                $this->lastResult = [
                    'type' => 'import_error',
                    'message' => $result['message'],
                    'details' => $result['details'] ?? []
                ];
                session()->flash('error', 'Import failed: ' . $result['message']);
            }

        } catch (\Exception $e) {
            session()->flash('error', 'Import failed: ' . $e->getMessage());
        } finally {
            $this->isProcessing = false;
        }
    }

    public function performExport()
    {
        if (!$this->selectedModel) {
            session()->flash('error', 'Please select a model to export.');
            return;
        }

        $this->processingMessage = 'Preparing export... This may take a few moments.';
        $this->isProcessing = true;

        try {
            $service = new ImportExportService();
            $result = $service->performExport(
                $this->selectedModel,
                $this->exportFormat,
                $this->exportFilters,
                [
                    'include_relations' => $this->includeRelations
                ]
            );

            if ($result['success']) {
                $this->lastResult = [
                    'type' => 'export_success',
                    'message' => $result['message'],
                    'filename' => $result['filename'],
                    'download_url' => $result['download_url']
                ];
                session()->flash('success', 'Export completed successfully!');

                // Trigger download
                return redirect($result['download_url']);
            } else {
                session()->flash('error', 'Export failed: ' . $result['message']);
            }

        } catch (\Exception $e) {
            session()->flash('error', 'Export failed: ' . $e->getMessage());
        } finally {
            $this->isProcessing = false;
        }
    }

    public function downloadSampleFile()
    {
        if (!$this->selectedModel) {
            session()->flash('error', 'Please select a model first.');
            return;
        }

        try {
            $service = new ImportExportService();
            $result = $service->generateSampleFile($this->selectedModel);

            if ($result['success']) {
                return redirect($result['download_url']);
            } else {
                session()->flash('error', 'Failed to generate sample file.');
            }
        } catch (\Exception $e) {
            session()->flash('error', 'Error generating sample file: ' . $e->getMessage());
        }
    }

    public function toggleAdvancedOptions()
    {
        $this->showAdvancedOptions = !$this->showAdvancedOptions;
    }

    public function clearResults()
    {
        $this->lastResult = null;
    }

    public function render()
    {
        return view('livewire.common.data-manager', [
            'availableModels' => $this->getAvailableModels(),
            'modelFilters' => $this->getModelFilters(),
            'importOptions' => $this->getImportOptions(),
        ]);
    }
}
