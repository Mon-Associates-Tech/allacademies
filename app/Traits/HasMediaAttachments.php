<?php

namespace App\Traits;

trait HasMediaAttachments
{
    public function mediaAttachments()
    {
        return $this->morphMany(MediaAttachment::class, 'attachable');
    }

    public function media()
    {
        return $this->morphToMany(MediaFile::class, 'attachable', 'media_attachments')
            ->withPivot(['collection', 'sort_order', 'metadata'])
            ->withTimestamps()
            ->orderBy('sort_order');
    }

    public function getMedia(string $collection = 'default')
    {
        return $this->media()->wherePivot('collection', $collection)->get();
    }

    public function getFirstMedia(string $collection = 'default'): ?MediaFile
    {
        return $this->getMedia($collection)->first();
    }

    public function attachMedia($mediaFileId, string $collection = 'default', array $metadata = []): void
    {
        $this->mediaAttachments()->create([
            'media_file_id' => $mediaFileId,
            'collection' => $collection,
            'metadata' => $metadata,
            'sort_order' => $this->mediaAttachments()->where('collection', $collection)->count(),
        ]);
    }

    public function detachMedia($mediaFileId, string $collection = 'default'): void
    {
        $this->mediaAttachments()
            ->where('media_file_id', $mediaFileId)
            ->where('collection', $collection)
            ->delete();
    }
}
