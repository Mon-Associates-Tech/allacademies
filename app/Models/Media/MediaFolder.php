<?php

namespace App\Models\Media;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;


class MediaFolder extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'parent_id',
        'path',
        'metadata'
    ];

    protected $casts = [
        'metadata' => 'array'
    ];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(MediaFolder::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(MediaFolder::class, 'parent_id');
    }

    public function files(): HasMany
    {
        return $this->hasMany(MediaFile::class, 'folder_id');
    }

    public function allFiles(): HasMany
    {
        return $this->files()->with('folder');
    }

    public function getFullPathAttribute(): string
    {
        if (!$this->parent) {
            return $this->name;
        }

        return $this->parent->full_path . '/' . $this->name;
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($folder) {
            if (empty($folder->slug)) {
                $folder->slug = Str::slug($folder->name);
            }

            // Generate path
            if ($folder->parent_id) {
                $parent = self::find($folder->parent_id);
                $folder->path = $parent->path . '/' . $folder->slug;
            } else {
                $folder->path = $folder->slug;
            }
        });
    }
}
