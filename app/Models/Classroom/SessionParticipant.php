<?php

namespace App\Models\Classroom;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SessionParticipant extends Model
{
    use HasFactory;

    protected $table  = 'virtual_session_participants';

    protected $fillable = [
        'virtual_session_id',
        'user_id',
        'role',
        'status',
        'full_name',
        'bbb_user_id',
        'joined_at',
        'left_at',
        'duration_seconds',
        'invited_at',
        'invited_by',
        'invitation_message',
        'has_joined',
        'join_count',
        'join_history',
    ];

    protected $casts = [
        'joined_at' => 'datetime',
        'left_at' => 'datetime',
        'invited_at' => 'datetime',
        'has_joined' => 'boolean',
        'join_history' => 'array',
    ];

    public function virtualSession(): BelongsTo
    {
        return $this->belongsTo(VirtualSession::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function invitedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'invited_by');
    }

    // Helper Methods
    public function isModerator(): bool
    {
        return $this->role === 'moderator';
    }

    public function hasJoined(): bool
    {
        return $this->has_joined;
    }

    public function markAsJoined(string $bbbUserId = null): void
    {
        $joinHistory = $this->join_history ?? [];
        $joinHistory[] = [
            'joined_at' => now()->toIso8601String(),
            'bbb_user_id' => $bbbUserId,
        ];

        $this->update([
            'status' => 'joined',
            'joined_at' => $this->joined_at ?? now(),
            'bbb_user_id' => $bbbUserId,
            'has_joined' => true,
            'join_count' => $this->join_count + 1,
            'join_history' => $joinHistory,
        ]);
    }

    public function markAsLeft(): void
    {
        $duration = $this->joined_at ? now()->diffInSeconds($this->joined_at) : 0;

        $this->update([
            'status' => 'left',
            'left_at' => now(),
            'duration_seconds' => $duration,
        ]);
    }
}
