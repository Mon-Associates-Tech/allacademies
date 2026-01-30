<?php

namespace App\Models\Classroom;

use App\Models\School;
use App\Models\User;
use App\Traits\BelongsToSchoolEnhanced;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class SessionRecording extends Model
{
    use BelongsToSchoolEnhanced, HasFactory, SoftDeletes;

    protected $table = 'virtual_session_recordings';

    protected $fillable = [
        'virtual_session_id',
        'school_id',
        'recording_id',
        'internal_recording_id',
        'name',
        'description',
        'type',
        'status',
        'format',
        'playback_url',
        'download_url',
        'size_bytes',
        'duration_seconds',
        'storage_disk',
        'storage_path',
        'thumbnail_path',
        'recorded_at',
        'published_at',
        'downloaded_at',
        'expires_at',
        'is_public',
        'allow_download',
        'access_settings',
        'bbb_metadata',
        'playback_formats',
    ];

    protected $casts = [
        'recorded_at' => 'datetime',
        'published_at' => 'datetime',
        'downloaded_at' => 'datetime',
        'expires_at' => 'datetime',
        'is_public' => 'boolean',
        'allow_download' => 'boolean',
        'access_settings' => 'array',
        'bbb_metadata' => 'array',
        'playback_formats' => 'array',
    ];

    public function virtualSession(): BelongsTo
    {
        return $this->belongsTo(VirtualSession::class);
    }

    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    // Scopes
    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    public function scopeNotExpired($query)
    {
        return $query->where(function ($q) {
            $q->whereNull('expires_at')
                ->orWhere('expires_at', '>', now());
        });
    }

    // Helper Methods
    public function isPublished(): bool
    {
        return $this->status === 'published';
    }

    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    public function isDownloaded(): bool
    {
        return ! empty($this->storage_path) && ! empty($this->downloaded_at);
    }

    public function canAccess(User $user): bool
    {
        if ($this->is_public) {
            return true;
        }

        // Check if user is the session teacher
        if ($this->virtualSession->teacher_id === $user->teacher?->id) {
            return true;
        }

        // Check if user was a participant
        if ($this->virtualSession->participants()->where('user_id', $user->id)->exists()) {
            return true;
        }

        // Check custom access settings
        $accessSettings = $this->access_settings ?? [];
        if (in_array($user->id, $accessSettings['allowed_users'] ?? [])) {
            return true;
        }

        return false;
    }

    public function getFormattedSize(): string
    {
        if (! $this->size_bytes) {
            return 'Unknown';
        }

        $units = ['B', 'KB', 'MB', 'GB'];
        $bytes = $this->size_bytes;
        $unit = 0;

        while ($bytes >= 1024 && $unit < count($units) - 1) {
            $bytes /= 1024;
            $unit++;
        }

        return round($bytes, 2).' '.$units[$unit];
    }

    public function getFormattedDuration(): string
    {
        if (! $this->duration_seconds) {
            return 'Unknown';
        }

        $hours = floor($this->duration_seconds / 3600);
        $minutes = floor(($this->duration_seconds % 3600) / 60);
        $seconds = $this->duration_seconds % 60;

        if ($hours > 0) {
            return sprintf('%02d:%02d:%02d', $hours, $minutes, $seconds);
        }

        return sprintf('%02d:%02d', $minutes, $seconds);
    }
}
