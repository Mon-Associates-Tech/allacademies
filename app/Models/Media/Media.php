<?php

namespace App\Models\Media;

use Illuminate\Database\Eloquent\Model;

class Media extends Model
{
    protected $fillable = [
        'title',
        'description',
        'type',
        'category',
        'tags',
        'single_video',
        'single_video_captions',
        'single_video_thumbnail',
        'single_audio',
        'single_audio_captions',
        'single_audio_thumbnail',
        'chapter_videos',
        'chapter_audios',
        'duration',
        'author',
        'instructor',
        'metadata',
    ];

    protected $casts = [
        'tags' => 'array',
        'chapter_videos' => 'array',
        'chapter_audios' => 'array',
        'metadata' => 'array',
    ];

    // Scopes for different types
    public function scopeVideos($query)
    {
        return $query->where('type', 'video');
    }

    public function scopeAudios($query)
    {
        return $query->where('type', 'audio');
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    public function scopeWithTag($query, $tag)
    {
        return $query->whereJsonContains('tags', $tag);
    }

    // Helper methods
    public function hasVideo()
    {
        return ! empty($this->single_video) || ! empty($this->chapter_videos);
    }

    public function hasAudio()
    {
        return ! empty($this->single_audio) || ! empty($this->chapter_audios);
    }

    public function getChaptersCount($type = 'video')
    {
        $field = "chapter_{$type}s";

        return $this->hasChapters($type) ? count($this->$field) : 0;
    }

    public function hasChapters($type = 'video')
    {
        $field = "chapter_{$type}s";

        return ! empty($this->$field);
    }
}
