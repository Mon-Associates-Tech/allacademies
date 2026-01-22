<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ChatMessage extends Model
{
    protected $fillable = [
        'chat_group_id', 'user_id', 'message', 'message_type',
        'reply_to_message_id', 'is_edited', 'edited_at',
        'is_deleted', 'deleted_at',
    ];

    protected $casts = [
        'is_edited' => 'boolean',
        'is_deleted' => 'boolean',
        'edited_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    protected $with = ['user', 'attachments'];

    // Relationships
    public function chatGroup(): BelongsTo
    {
        return $this->belongsTo(ChatGroup::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function replyTo(): BelongsTo
    {
        return $this->belongsTo(ChatMessage::class, 'reply_to_message_id');
    }

    public function replies(): HasMany
    {
        return $this->hasMany(ChatMessage::class, 'reply_to_message_id')
            ->where('is_deleted', false);
    }

    public function attachments(): HasMany
    {
        return $this->hasMany(ChatMessageAttachment::class);
    }

    public function readBy(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'chat_message_reads')
            ->withPivot('read_at')
            ->withTimestamps();
    }

    // Helper methods
    public function markAsRead(User $user): void
    {
        if (! $this->readBy()->where('user_id', $user->id)->exists()) {
            $this->readBy()->attach($user->id, ['read_at' => now()]);
        }
    }

    public function isReadBy(User $user): bool
    {
        return $this->readBy()->where('user_id', $user->id)->exists();
    }

    public function getReadCount(): int
    {
        return $this->readBy()->count();
    }

    public function canBeEditedBy(User $user): bool
    {
        return $this->user_id === $user->id &&
            $this->created_at->diffInMinutes(now()) <= 15;
    }

    public function canBeDeletedBy(User $user): bool
    {
        // User can delete their own message or group admin/moderator can delete
        if ($this->user_id === $user->id) {
            return true;
        }

        $membership = $this->chatGroup->allMembers()
            ->where('user_id', $user->id)
            ->first();

        return $membership && in_array($membership->pivot->role, ['admin', 'moderator']);
    }

    public function softDelete(): void
    {
        $this->update([
            'is_deleted' => true,
            'deleted_at' => now(),
            'message' => null,
        ]);
    }
}
