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
        return $this->attributes['single_audio']
            ? asset('storage/' . $this->attributes['single_audio'])
            : asset('/media/audio/friends_lovers_and_terrible_thing_sample.mp3');
    }

    public function getSingleVideoAttribute(): ?string
    {
        return $this->attributes['single_video']
            ? asset('storage/' . $this->attributes['single_video'])
            : asset('/media/video/the_ultimate_gift.mp4');
    }

    public function getChapterAudiosAttribute(): array
    {
        $files = $this->attributes['chapter_audios'] ?? [];
        if(is_array($files)){
            return array_map(function($file) {
                return asset('storage/' . $file);
            }, $files);
        }
      return [];
    }

    public function getChapterVideosAttribute(): array
    {
        $files = $this->attributes['chapter_videos'] ?? [];
        if(is_array($files)){
            return array_map(function($file) {
                return asset('storage/' . $file);
            }, $files);
        }
       return [];
    }
}
