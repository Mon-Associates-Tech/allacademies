<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Facades\Storage;

class MessageAttachment extends Model
{
    use HasFactory;

    protected $fillable = [
        'attachable_id',
        'attachable_type',
        'filename',
        'original_filename',
        'path',
        'size',
        'mime_type',
    ];

    protected $casts = [
        'size' => 'integer',
    ];

    public function attachable(): MorphTo
    {
        return $this->morphTo();
    }

    public function getUrlAttribute(): string
    {
        return Storage::url($this->path);
    }

    public function getSizeInKbAttribute(): string
    {
        return number_format($this->size / 1024, 2).' KB';
    }

    public function getSizeInMbAttribute(): string
    {
        return number_format($this->size / (1024 * 1024), 2).' MB';
    }

    public function getHumanReadableSizeAttribute(): string
    {
        if ($this->size < 1024 * 1024) {
            return $this->size_in_kb;
        }

        return $this->size_in_mb;
    }
}
