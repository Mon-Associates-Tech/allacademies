<?php

namespace App\Livewire\Resources;

use App\Models\EducationalResource;
use Illuminate\Support\Collection;
use Livewire\Component;

class ResourceViewer extends Component
{
    public EducationalResource $resource;

    public function mount(EducationalResource $resource): void
    {
        $user = auth()->user();

        if (! $resource->canBeAccessedBy($user)) {
            abort(403, 'You do not have permission to view this resource.');
        }

        $this->resource = $resource->load([
            'academicSubject.academicLevel.academicGroup',
            'uploader',
            'topics',
            'subtopics',
            'school',
        ]);
    }

    public function getRelatedResourcesProperty(): Collection
    {
        $user = auth()->user();

        return EducationalResource::query()
            ->with(['academicSubject', 'uploader'])
            ->active()
            ->accessibleBy($user)
            ->where('id', '!=', $this->resource->id)
            ->where(function ($query) {
                // Same subject
                $query->where('academic_subject_id', $this->resource->academic_subject_id)
                    // Or same format
                    ->orWhere('format', $this->resource->format)
                    // Or overlapping tags
                    ->orWhere(function ($q) {
                        if (! empty($this->resource->tags)) {
                            foreach ($this->resource->tags as $tag) {
                                $q->orWhereJsonContains('tags', $tag);
                            }
                        }
                    });
            })
            ->orderBy('view_count', 'desc')
            ->limit(6)
            ->get();
    }

    public function getFormatIconProperty(): string
    {
        return match ($this->resource->format) {
            'video' => 'heroicon-o-play-circle',
            'pdf' => 'heroicon-o-document-text',
            'image' => 'heroicon-o-photo',
            'text' => 'heroicon-o-document',
            default => 'heroicon-o-document',
        };
    }

    public function getFormatColorProperty(): string
    {
        return match ($this->resource->format) {
            'video' => 'text-red-500',
            'pdf' => 'text-orange-500',
            'image' => 'text-green-500',
            'text' => 'text-blue-500',
            default => 'text-gray-500',
        };
    }

    public function getFormatBgColorProperty(): string
    {
        return match ($this->resource->format) {
            'video' => 'bg-red-100 dark:bg-red-900/30',
            'pdf' => 'bg-orange-100 dark:bg-orange-900/30',
            'image' => 'bg-green-100 dark:bg-green-900/30',
            'text' => 'bg-blue-100 dark:bg-blue-900/30',
            default => 'bg-gray-100 dark:bg-gray-900/30',
        };
    }

    public function canEdit(): bool
    {
        $user = auth()->user();

        if (! $user) {
            return false;
        }

        // Administrators, owners, and super-admins can edit any resource
        if ($user->hasAnyRole(['admin', 'owner', 'super-admin'])) {
            return true;
        }

        // Teachers and moderators can edit their own resources
        if ($user->hasAnyRole(['teacher', 'moderator'])) {
            return $this->resource->uploaded_by === $user->id;
        }

        return false;
    }

    public function canDelete(): bool
    {
        return $this->canEdit();
    }

    public function render()
    {
        return view('livewire.resources.resource-viewer', [
            'relatedResources' => $this->relatedResources,
        ]);
    }
}
