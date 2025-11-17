<?php

namespace App\Models\Classroom;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class SessionInvitation extends Model
{
    use HasFactory;

    protected $fillable = [
        'virtual_session_id',
        'user_id',
        'guest_name',
        'guest_email',
        'token',
        'status',
        'message',
        'invited_at',
        'accepted_at',
        'declined_at',
        'expires_at',
        'last_reminder_at',
        'invited_by',
    ];

    protected $casts = [
        'invited_at' => 'datetime',
        'accepted_at' => 'datetime',
        'declined_at' => 'datetime',
        'expires_at' => 'datetime',
        'last_reminder_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($invitation) {
            if (empty($invitation->token)) {
                $invitation->token = Str::random(32);
            }
            if (empty($invitation->invited_at)) {
                $invitation->invited_at = now();
            }
        });
    }

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

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending')
            ->where(function ($q) {
                $q->whereNull('expires_at')
                  ->orWhere('expires_at', '>', now());
            });
    }

    // Helper Methods
    public function isPending(): bool
    {
        return $this->status === 'pending' &&
               (!$this->expires_at || $this->expires_at->isFuture());
    }

    public function isExpired(): bool
    {
        return $this->expires_at && $this->expires_at->isPast();
    }

    public function accept(): void
    {
        $this->update([
            'status' => 'accepted',
            'accepted_at' => now(),
        ]);
    }

    public function decline(): void
    {
        $this->update([
            'status' => 'declined',
            'declined_at' => now(),
        ]);
    }

    public function getRecipientName(): string
    {
        return $this->user?->name ?? $this->guest_name ?? 'Guest';
    }

    public function getRecipientEmail(): string
    {
        return $this->user?->email ?? $this->guest_email ?? '';
    }
}
