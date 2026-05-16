<?php

namespace App\MockExam\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class MockExamParticipant extends Model
{
    protected $fillable = [
        'name',
        'email',
        'phone',
        'verification_token',
        'email_verified_at',
        'verification_sent_at',
        'result_access_token',
        'user_id',
        'ip_address',
        'user_agent',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at'    => 'datetime',
            'verification_sent_at' => 'datetime',
        ];
    }

    // ─── Boot ────────────────────────────────────────────────────────────────

    protected static function boot(): void
    {
        parent::boot();

        static::creating(function (self $p) {
            if (empty($p->result_access_token)) {
                $p->result_access_token = self::generateUniqueResultToken();
            }
            if (empty($p->verification_token)) {
                $p->verification_token = Str::random(64);
            }
        });
    }

    // ─── Helpers ─────────────────────────────────────────────────────────────

    public static function generateUniqueResultToken(): string
    {
        do {
            $token = Str::random(64);
        } while (self::where('result_access_token', $token)->exists());

        return $token;
    }

    public static function findByEmail(string $email): ?self
    {
        return self::where('email', strtolower(trim($email)))->first();
    }

    public static function findByResultToken(string $token): ?self
    {
        return self::where('result_access_token', $token)->first();
    }

    public static function findByVerificationToken(string $token): ?self
    {
        return self::where('verification_token', $token)->first();
    }

    // ─── Relationships ────────────────────────────────────────────────────────

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function submissions(): HasMany
    {
        return $this->hasMany(MockExamSubmission::class, 'participant_id')
            ->where('participant_type', 'general');
    }

    // ─── Verification ─────────────────────────────────────────────────────────

    public function isEmailVerified(): bool
    {
        return $this->email_verified_at !== null;
    }

    public function markEmailAsVerified(): bool
    {
        return $this->forceFill([
            'email_verified_at'    => now(),
            'verification_token'   => null,
        ])->save();
    }

    public function canResendVerification(): bool
    {
        if (! $this->verification_sent_at) {
            return true;
        }

        return $this->verification_sent_at->addMinute()->isPast();
    }
}
