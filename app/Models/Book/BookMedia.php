<?php

namespace App\Models\Book;

use App\Models\Book;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BookMedia extends Model
{
    protected $fillable = [
        'book_id',
        'single_audio',
        'single_video',
        'chapter_audios',
        'chapter_videos',
    ];

    protected $casts = [
        'chapter_audios' => 'array',
        'chapter_videos' => 'array',
    ];

    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class);
    }

    public function getSingleAudioAttribute(): ?string
    {
        if (! $this->attributes['single_audio']) {
            return null;
        }

        $path = $this->attributes['single_audio'];

        return str_starts_with($path, 'http') ? $path : asset('storage/'.$path);
    }

    public function getSingleVideoAttribute(): ?string
    {
        if (! $this->attributes['single_video']) {
            return null;
        }

        $path = $this->attributes['single_video'];

        return str_starts_with($path, 'http') ? $path : asset('storage/'.$path);
    }

    public function getChapterAudiosAttribute(): array
    {
        $files = $this->attributes['chapter_audios'] ?? [];

        if (is_string($files)) {
            $files = json_decode($files, true) ?? [];
        }

        if (! is_array($files)) {
            return [];
        }

        return array_map(function ($item) {
            // New format: {chapter: 1, file: 'path/to/file.mp3', title: 'Chapter Title'}
            if (is_array($item) && isset($item['file'])) {
                $item['url'] = str_starts_with($item['file'], 'http')
                    ? $item['file']
                    : asset('storage/'.$item['file']);

                return $item;
            }

            // Legacy format: just file path
            if (is_string($item)) {
                return [
                    'file' => $item,
                    'url' => str_starts_with($item, 'http') ? $item : asset('storage/'.$item),
                ];
            }

            return $item;
        }, $files);
    }

    public function getChapterVideosAttribute(): array
    {
        $files = $this->attributes['chapter_videos'] ?? [];

        if (is_string($files)) {
            $files = json_decode($files, true) ?? [];
        }

        if (! is_array($files)) {
            return [];
        }

        return array_map(function ($item) {
            // New format: {chapter: 1, file: 'path/to/file.mp4', title: 'Chapter Title'}
            if (is_array($item) && isset($item['file'])) {
                $item['url'] = str_starts_with($item['file'], 'http')
                    ? $item['file']
                    : asset('storage/'.$item['file']);

                return $item;
            }

            // Legacy format: just file path
            if (is_string($item)) {
                return [
                    'file' => $item,
                    'url' => str_starts_with($item, 'http') ? $item : asset('storage/'.$item),
                ];
            }

            return $item;
        }, $files);
    }
}
