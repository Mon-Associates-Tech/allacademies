<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class NoteShare extends Model
{
    use HasFactory;

    protected $fillable = [
        'note_id',
        'shared_with_user_id',
        'guest_email',
        'share_type',
        'shareable_type',
        'shareable_id',
        'can_edit',
        'notification_sent',
        'notified_at',
    ];

    protected $casts = [
        'can_edit' => 'boolean',
        'notification_sent' => 'boolean',
        'notified_at' => 'datetime',
    ];

    public function note(): BelongsTo
    {
        return $this->belongsTo(Note::class);
    }

    public function sharedWithUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'shared_with_user_id');
    }

    public function sharer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function shareable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Get the recipient email whether it's a user or guest
     */
    public function getRecipientEmail(): string
    {
        return $this->sharedWithUser?->email ?? $this->guest_email ?? '';
    }

    /**
     * Get the recipient name whether it's a user or guest
     */
    public function getRecipientName(): string
    {
        return $this->sharedWithUser?->name ?? $this->guest_email ?? 'Guest';
    }

    /**
     * Check if this share is for a guest (non-user)
     */
    public function isGuest(): bool
    {
        return ! $this->shared_with_user_id && ! empty($this->guest_email);
    }
}
