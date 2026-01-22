<?php

namespace App\Models\Media;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MediaAttachment extends Model
{
    protected $fillable = [
        'media_file_id',
        'attachable_id',
        'attachable_type',
        'collection',
        'sort_order',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'array',
    ];

    public function mediaFile(): BelongsTo
    {
        return $this->belongsTo(MediaFile::class);
    }

    public function attachable()
    {
        return $this->morphTo();
    }
}
