<?php

namespace App\Livewire\Media;

use App\Models\MediaFile;
use App\Models\MediaFolder;
use App\Services\MediaService;
use Livewire\Component;

class MediaPicker extends Component
{
    public $isOpen = false;

    public $multiple = false;

    public $selectedMediaIds = [];

    public $currentFolderId = null;

    public $search = '';

    public $filterMimeType = '';

    public $acceptedTypes = []; // Array of mime types to filter by

    // Data
    public $folders = [];

    public $files = [];

    public $breadcrumb = [];

    // Events to emit back to parent
    public $mediaSelected = 'mediaSelected';

    protected MediaService $mediaService;

    public function boot()
    {
        $this->mediaService = app(MediaService::class);
    }

    public function mount($multiple = false, $acceptedTypes = [])
    {
        $this->multiple = $multiple;
        $this->acceptedTypes = $acceptedTypes;
    }

    public function render()
    {
        return view('livewire.media-picker');
    }

    public function open()
    {
        $this->isOpen = true;
        $this->refreshContent();
    }

    public function close()
    {
        $this->isOpen = false;
        $this->selectedMediaIds = [];
    }

    public function refreshContent()
    {
        $filters = [
            'search' => $this->search,
        ];

        // Apply accepted types filter
        if (! empty($this->acceptedTypes)) {
            if (in_array('image', $this->acceptedTypes)) {
                $filters['mime_type'] = 'image';
            } elseif (in_array('video', $this->acceptedTypes)) {
                $filters['mime_type'] = 'video';
            } elseif (in_array('document', $this->acceptedTypes)) {
                $filters['mime_type'] = 'application';
            }
        } elseif ($this->filterMimeType) {
            $filters['mime_type'] = $this->filterMimeType;
        }

        $content = $this->mediaService->getFolderContents($this->currentFolderId, $filters);

        $this->folders = $content['folders'];
        $this->files = $content['files'];

        // Apply additional accepted types filtering if needed
        if (! empty($this->acceptedTypes)) {
            $this->files = $this->files->filter(function ($file) {
                foreach ($this->acceptedTypes as $type) {
                    if ($type === 'image' && $file->isImage()) {
                        return true;
                    }
                    if ($type === 'video' && $file->isVideo()) {
                        return true;
                    }
                    if ($type === 'document' && $file->isDocument()) {
                        return true;
                    }
                    if (str_starts_with($file->mime_type, $type)) {
                        return true;
                    }
                }

                return false;
            });
        }

        $this->breadcrumb = $this->mediaService->getBreadcrumb($this->currentFolderId);
    }

    public function navigateToFolder(?int $folderId)
    {
        $this->currentFolderId = $folderId;
        $this->refreshContent();
    }

    public function toggleMediaSelection($mediaId)
    {
        if ($this->multiple) {
            if (in_array($mediaId, $this->selectedMediaIds)) {
                $this->selectedMediaIds = array_filter($this->selectedMediaIds, fn ($id) => $id !== $mediaId);
            } else {
                $this->selectedMediaIds[] = $mediaId;
            }
        } else {
            $this->selectedMediaIds = [$mediaId];
        }
    }

    public function selectMedia()
    {
        if (empty($this->selectedMediaIds)) {
            return;
        }

        $selectedMedia = MediaFile::whereIn('id', $this->selectedMediaIds)->get();

        if ($this->multiple) {
            $this->emit($this->mediaSelected, $selectedMedia->toArray());
        } else {
            $this->emit($this->mediaSelected, $selectedMedia->first()->toArray());
        }

        $this->close();
    }

    public function updatedSearch()
    {
        $this->refreshContent();
    }

    public function updatedFilterMimeType()
    {
        $this->refreshContent();
    }

    // Helper method to get all available folders for folder picker
    public function getAllFolders()
    {
        return MediaFolder::orderBy('path')->get();
    }
}
