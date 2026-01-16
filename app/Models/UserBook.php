<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class UserBook extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'slug',
        'description',
        'cover_image',
        'content_url',
        'sample_url',
        'table_of_contents',
        'chapter_audios',
        'chapter_videos',
        'has_audio',
        'has_video',
        'single_audio',
        'single_video',
        'pages',
        'edition',
        'publisher',
        'status',
    ];

    protected $casts = [
        'table_of_contents' => 'array',
        'chapter_audios' => 'array',
        'chapter_videos' => 'array',
        'has_audio' => 'boolean',
        'has_video' => 'boolean',
        'annual_subscription_fee' => 'decimal:2',
    ];

    protected static function booted(): void
    {
        static::creating(function ($userBook) {
            if (empty($userBook->slug)) {
                $userBook->slug = Str::slug($userBook->title);
            }
        });

        static::updating(function ($userBook) {
            if ($userBook->isDirty('title')) {
                $userBook->slug = Str::slug($userBook->title);
            }
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function shares(): HasMany
    {
        return $this->hasMany(UserBookShare::class);
    }

    public function getCoverImageUrlAttribute(): string
    {
        if ($this->attributes['cover_image']) {
            return asset('storage/'.$this->attributes['cover_image']);
        }

        $sampleCovers = [
            'images/book-cover.png',
            'images/book-cover-1.jpg',
            'images/book-cover-2.jpg',
        ];

        return asset($sampleCovers[array_rand($sampleCovers)]);
    }

    public function getContentUrlAttribute(): ?string
    {

        if ($this->attributes['content_url']) {
            // return $this->attributes['content_url'];
            return asset('/storage/'.$this->attributes['content_url']);
        }

        return null;
    }

    public function getSampleUrlAttribute(): ?string
    {
        if ($this->attributes['sample_url']) {
            return $this->attributes['sample_url'];

            return asset('storage/'.$this->sample_url);
        }

        return null;
    }

    public function getFormattedTableOfContentsAttribute(): array
    {
        $toc = $this->table_of_contents;

        if (! $toc) {
            return [];
        }

        return collect($toc)->map(function ($chapter) {
            return [
                'chapter_number' => $chapter['chapter'] ?? 1,
                'title' => $chapter['title'] ?? 'Untitled Chapter',
                'description' => $chapter['description'] ?? '',
                'page_range' => isset($chapter['page_start'], $chapter['page_end'])
                    ? "Pages {$chapter['page_start']}-{$chapter['page_end']}"
                    : '',
                'page_count' => isset($chapter['page_start'], $chapter['page_end'])
                    ? $chapter['page_end'] - $chapter['page_start'] + 1
                    : 0,
                'sections' => $chapter['sections'] ?? [],
            ];
        })->toArray();
    }

    public function shareCount(): int
    {
        return $this->shares()->count();
    }

    /**
     * Check if a user has access to this book
     */
    public function userHasAccess(User $user): bool
    {
        // Owner always has access
        if ($this->user_id === $user->id) {
            return true;
        }

        // Check active shares
        return UserBookShare::where('user_book_id', $this->id)
            ->active()
            ->forUser($user)
            ->exists();
    }

    /**
     * Get all users who have access to this book
     */
    public function getUsersWithAccess()
    {
        $shares = $this->shares()->active()->get();
        $users = collect([$this->user]); // Include owner

        foreach ($shares as $share) {
            $affected = $share->getAffectedUsers();
            if ($affected) {
                $users = $users->merge($affected);
            }
        }

        return $users->unique('id');
    }
}
