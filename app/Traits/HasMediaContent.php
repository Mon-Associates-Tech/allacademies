<?php

namespace App\Traits;

use App\Models\Book\BookMedia;

/**
 * Trait for models with media content
 */
trait HasMediaContent
{
    /**
     * Polymorphic relationship to media
     */
    public function media()
    {
        return $this->morphOne(BookMedia::class, 'mediable');
    }

    /**
     * Get the title for media display
     */
    public function getTitle()
    {
        return $this->title ?? $this->name ?? 'Untitled';
    }

    /**
     * Get the description for media display
     */
    public function getDescription()
    {
        return $this->description ?? $this->content ?? '';
    }

    /**
     * Check if resource has video content
     */
    public function hasVideoContent()
    {
        if (! $this->media) {
            return false;
        }

        return ! empty($this->media->single_video) || ! empty($this->media->chapter_videos);
    }

    /**
     * Check if resource has audio content
     */
    public function hasAudioContent()
    {
        if (! $this->media) {
            return false;
        }

        return ! empty($this->media->single_audio) || ! empty($this->media->chapter_audios);
    }

    /**
     * Check if resource has chapter-based content
     */
    public function hasChapterContent()
    {
        if (! $this->media) {
            return false;
        }

        return ! empty($this->media->chapter_videos) || ! empty($this->media->chapter_audios);
    }

    /**
     * Get video chapters
     */
    public function getVideoChapters()
    {
        return $this->media?->chapter_videos ?? [];
    }

    /**
     * Get audio chapters
     */
    public function getAudioChapters()
    {
        return $this->media?->chapter_audios ?? [];
    }

    /**
     * Get single video URL
     */
    public function getSingleVideoUrl()
    {
        return $this->media?->single_video;
    }

    /**
     * Get single audio URL
     */
    public function getSingleAudioUrl()
    {
        return $this->media?->single_audio;
    }

    /**
     * Scope to get resources with video content
     */
    public function scopeWithVideo($query)
    {
        return $query->whereHas('media', function ($query) {
            $query->where(function ($query) {
                $query->whereNotNull('single_video')
                    ->orWhereNotNull('chapter_videos');
            });
        });
    }

    /**
     * Scope to get resources with audio content
     */
    public function scopeWithAudio($query)
    {
        return $query->whereHas('media', function ($query) {
            $query->where(function ($query) {
                $query->whereNotNull('single_audio')
                    ->orWhereNotNull('chapter_audios');
            });
        });
    }

    /**
     * Helper method to create media for this resource
     */
    public function createMedia(array $mediaData)
    {
        return $this->media()->create($mediaData);
    }

    /**
     * Helper method to update media for this resource
     */
    public function updateMedia(array $mediaData)
    {
        if ($this->media) {
            return $this->media->update($mediaData);
        }

        return $this->createMedia($mediaData);
    }
}
