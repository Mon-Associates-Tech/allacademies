<?php

namespace App\Models;

use App\Traits\Trackable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property mixed $is_urgent
 * @property mixed $subject
 * @property mixed $attachments
 */
class Message extends Model
{
    use HasFactory, SoftDeletes, Trackable;

    const STATUS_DRAFT = 'draft';
    const STATUS_SCHEDULED = 'scheduled';
    const STATUS_SENDING = 'sending';
    const STATUS_SENT = 'sent';
    const STATUS_FAILED = 'failed';
    const TARGET_ROLE = 'role';
    const TARGET_ACADEMIC_GROUP = 'academic_group';
    const TARGET_ACADEMIC_LEVEL = 'academic_level';
    const TARGET_SUBJECT = 'subject';
    const TARGET_INDIVIDUAL = 'individual';
    const TARGET_CUSTOM = 'custom';
    protected $fillable = [
        'sender_id',
        'subject',
        'body',
        'target_type',
        'target_criteria',
        'is_urgent',
        'scheduled_at',
        'sent_at',
        'status'
    ];
    protected $casts = [
        'target_criteria' => 'array',
        'is_urgent' => 'boolean',
        'scheduled_at' => 'datetime',
        'sent_at' => 'datetime'
    ];

    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    public function attachments(): MorphMany
    {
        return $this->morphMany(MessageAttachment::class, 'attachable');
    }

    public function getRecipientCountAttribute(): int
    {
        return $this->recipients()->count();
    }

    public function recipients(): HasMany
    {
        return $this->hasMany(MessageRecipient::class);
    }

    public function getReadCountAttribute(): int
    {
        return $this->recipients()->whereNotNull('read_at')->count();
    }

    public function getUnreadCountAttribute(): int
    {
        return $this->recipients()->whereNull('read_at')->count();
    }

    public function scopeDrafts($query)
    {
        return $query->where('status', self::STATUS_DRAFT);
    }

    public function scopeSent($query)
    {
        return $query->where('status', self::STATUS_SENT);
    }

    public function scopeScheduled($query)
    {
        return $query->where('status', self::STATUS_SCHEDULED);
    }

    public function isScheduled(): bool
    {
        return $this->status === self::STATUS_SCHEDULED;
    }

    public function isDraft(): bool
    {
        return $this->status === self::STATUS_DRAFT;
    }

    public function isSent(): bool
    {
        return $this->status === self::STATUS_SENT;
    }

    public function readByUser($user)
    {
        // Check if user is sender (sender messages are always considered "read")
        if ($this->sender_id === $user->id) {
            return true;
        }

        // Check if there's a read receipt for this user
        return $this->readReceipts()
            ->where('user_id', $user->id)
            ->exists();
    }

    public function readReceipts()
    {
        return $this->hasMany(MessageReadReceipt::class, 'message_id');
    }

    public function markAsReadByUser($user)
    {
        // Don't mark sender's own messages as read
        if ($this->sender_id === $user->id) {
            return;
        }

        $this->readReceipts()->updateOrCreate(
            ['user_id' => $user->id],
            ['read_at' => now()]
        );
    }

}
