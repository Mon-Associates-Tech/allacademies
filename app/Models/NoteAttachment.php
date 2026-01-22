<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class NoteAttachment extends Model
{
    use HasFactory;

    protected $fillable = [
        'note_id',
        'user_id',
        'filename',
        'original_filename',
        'path',
        'size',
        'mime_type',
    ];

    protected $appends = ['file_size_human', 'file_icon', 'file_extension'];

    public function note(): BelongsTo
    {
        return $this->belongsTo(Note::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getFileSizeHumanAttribute(): string
    {
        $bytes = $this->size;
        $units = ['B', 'KB', 'MB', 'GB'];

        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, 2).' '.$units[$i];
    }

    public function getFileExtensionAttribute(): string
    {
        return strtolower(pathinfo($this->original_filename, PATHINFO_EXTENSION));
    }

    public function getFileIconAttribute(): string
    {
        return match ($this->file_extension) {
            'pdf' => '📄',
            'doc', 'docx' => '📝',
            'xls', 'xlsx' => '📊',
            'txt' => '📃',
            'zip', 'rar', '7z' => '🗜️',
            'jpg', 'jpeg', 'png', 'gif' => '🖼️',
            default => '📎'
        };
    }

    public function getFileColorAttribute(): string
    {
        return match ($this->file_extension) {
            'pdf' => 'red',
            'doc', 'docx' => 'blue',
            'xls', 'xlsx' => 'green',
            'txt' => 'gray',
            'zip', 'rar', '7z' => 'purple',
            'jpg', 'jpeg', 'png', 'gif' => 'pink',
            default => 'gray'
        };
    }

    public function deleteFile(): bool
    {
        if (Storage::disk('public')->exists($this->path)) {
            return Storage::disk('public')->delete($this->path);
        }

        return false;
    }
}
