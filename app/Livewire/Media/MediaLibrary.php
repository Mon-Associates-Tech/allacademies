<?php

namespace App\Livewire\Media;

use App\Models\Media\MediaFile;
use App\Models\Media\MediaFolder;
use App\Services\MediaService;
use Livewire\Component;
use Livewire\WithFileUploads;

class MediaLibrary extends Component
{
    use WithFileUploads;

    public $currentFolderId = null;

    public $selectedFiles = [];

    public $selectedFolders = [];

    public $view = 'grid'; // 'grid' or 'list'

    public $sortBy = 'created_at';

    public $sortDirection = 'desc';

    public $search = '';

    public $filterMimeType = '';

    public $showUploadModal = false;

    public $showCreateFolderModal = false;

    public $showFileDetailsModal = false;

    public $showMoveModal = false;

    public $showDeleteModal = false;

    // Upload properties
    public $uploadFiles = [];

    public $uploadProgress = 0;

    // Folder creation
    public $newFolderName = '';

    public $newFolderDescription = '';

    // File details
    public $selectedFile = null;

    public $fileAltText = '';

    public $fileDescription = '';

    // Move operation
    public $moveToFolderId = null;

    // Breadcrumb
    public $breadcrumb = [];

    // Data
    public $folders = [];

    public $files = [];

    protected $listeners = [
        'fileUploaded' => 'refreshContent',
        'folderCreated' => 'refreshContent',
        'fileDeleted' => 'refreshContent',
        'fileMoved' => 'refreshContent',
    ];

    protected MediaService $mediaService;

    public function boot()
    {
        $this->mediaService = app(MediaService::class);
    }

    public function mount()
    {
        //        $this->currentFolderId = $folderId;
        $this->refreshContent();
    }

    public function render()
    {
        return view('livewire.media.media-library');
    }

    public function refreshContent()
    {
        $content = $this->mediaService->getFolderContents($this->currentFolderId, [
            'search' => $this->search,
            'mime_type' => $this->filterMimeType,
        ]);

        $this->folders = $content['folders'];
        $this->files = $content['files'];
        $this->breadcrumb = $this->mediaService->getBreadcrumb($this->currentFolderId);

        // Clear selections when navigating
        $this->selectedFiles = [];
        $this->selectedFolders = [];
    }

    public function navigateToFolder(?int $folderId)
    {
        $this->currentFolderId = $folderId;
        $this->refreshContent();
    }

    public function toggleFileSelection($fileId)
    {
        if (in_array($fileId, $this->selectedFiles)) {
            $this->selectedFiles = array_filter($this->selectedFiles, fn ($id) => $id !== $fileId);
        } else {
            $this->selectedFiles[] = $fileId;
        }
    }

    public function toggleFolderSelection($folderId)
    {
        if (in_array($folderId, $this->selectedFolders)) {
            $this->selectedFolders = array_filter($this->selectedFolders, fn ($id) => $id !== $folderId);
        } else {
            $this->selectedFolders[] = $folderId;
        }
    }

    public function selectAllFiles()
    {
        $this->selectedFiles = $this->files->pluck('id')->toArray();
    }

    public function deselectAll()
    {
        $this->selectedFiles = [];
        $this->selectedFolders = [];
    }

    // Upload functionality
    public function startUpload()
    {
        \Log::info('Start upload method called');
        $this->showUploadModal = true;
    }

    public function uploadFiles()
    {
        logError($this->uploadFiles);
        // Debug: Check if files are actually selected
        if (empty($this->uploadFiles)) {
            $this->dispatch('notify', 'No files selected for upload.', 'warning');

            return;
        }

        logError($this->uploadFiles);

        $this->validate([
            'uploadFiles.*' => 'required|file|max:10240', // 10MB max
        ]);

        try {
            foreach ($this->uploadFiles as $file) {
                $this->mediaService->uploadFile($file, $this->currentFolderId, auth()->id());
            }

            $this->uploadFiles = [];
            $this->showUploadModal = false;
            $this->refreshContent();

            $this->dispatch('notify', 'Files uploaded successfully!', 'success');
        } catch (\Exception $e) {
            $this->dispatch('notify', 'Upload failed: '.$e->getMessage(), 'error');
        }
    }

    // Folder creation
    public function createFolder()
    {
        $this->validate([
            'newFolderName' => 'required|string|max:255',
            'newFolderDescription' => 'nullable|string',
        ]);

        $this->mediaService->createFolder(
            $this->newFolderName,
            $this->currentFolderId,
            $this->newFolderDescription
        );

        $this->reset(['newFolderName', 'newFolderDescription', 'showCreateFolderModal']);
        $this->refreshContent();

        $this->dispatch('notify', 'Folder created successfully!', 'success');
    }

    // File details
    public function showFileDetails(MediaFile $file)
    {
        $this->selectedFile = $file;
        $this->fileAltText = $file->alt_text ?? '';
        $this->fileDescription = $file->description ?? '';
        $this->showFileDetailsModal = true;
    }

    public function updateFileDetails()
    {
        // Add a check to ensure selectedFile exists
        if (! $this->selectedFile) {
            $this->dispatch('notify', 'No file selected.', 'error');

            return;
        }
        $this->validate([
            'fileAltText' => 'nullable|string',
            'fileDescription' => 'nullable|string',
        ]);

        $this->selectedFile->update([
            'alt_text' => $this->fileAltText,
            'description' => $this->fileDescription,
        ]);

        $this->showFileDetailsModal = false;
        $this->refreshContent();

        $this->dispatch('notify', 'File details updated!', 'success');
    }

    // Move operations
    public function startMove()
    {
        if (empty($this->selectedFiles) && empty($this->selectedFolders)) {
            $this->dispatch('notify', 'Please select files or folders to move.', 'warning');

            return;
        }

        $this->showMoveModal = true;
    }

    public function moveSelected()
    {
        foreach ($this->selectedFiles as $fileId) {
            $file = MediaFile::find($fileId);
            if ($file) {
                $this->mediaService->moveFile($file, $this->moveToFolderId);
            }
        }

        $this->reset(['showMoveModal', 'moveToFolderId']);
        $this->selectedFiles = [];
        $this->selectedFolders = [];
        $this->refreshContent();

        $this->dispatch('notify', 'Items moved successfully!', 'success');
    }

    // Delete operations
    public function startDelete()
    {
        if (empty($this->selectedFiles) && empty($this->selectedFolders)) {
            $this->dispatch('notify', 'Please select files or folders to delete.', 'warning');

            return;
        }

        $this->showDeleteModal = true;
    }

    public function deleteSelected()
    {
        foreach ($this->selectedFiles as $fileId) {
            $file = MediaFile::find($fileId);
            if ($file) {
                $this->mediaService->deleteFile($file);
            }
        }

        foreach ($this->selectedFolders as $folderId) {
            $folder = MediaFolder::find($folderId);
            if ($folder) {
                try {
                    $this->mediaService->deleteFolder($folder, true);
                } catch (\Exception $e) {
                    $this->dispatch('notify', 'Error deleting folder: '.$e->getMessage(), 'error');

                    continue;
                }
            }
        }

        $this->reset(['showDeleteModal']);
        $this->selectedFiles = [];
        $this->selectedFolders = [];
        $this->refreshContent();

        $this->dispatch('notify', 'Items deleted successfully!', 'success');
    }

    // Search and filters
    public function updatedSearch()
    {
        $this->refreshContent();
    }

    public function updatedFilterMimeType()
    {
        $this->refreshContent();
    }

    public function clearFilters()
    {
        $this->reset(['search', 'filterMimeType']);
        $this->refreshContent();
    }

    // View toggles
    public function switchView($view)
    {
        $this->view = $view;
    }

    // Sorting
    public function sortBy($field)
    {
        if ($this->sortBy === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $field;
            $this->sortDirection = 'asc';
        }

        $this->refreshContent();
    }

    public function getAllFolders()
    {
        return MediaFolder::orderBy('path')->get();
    }
}
