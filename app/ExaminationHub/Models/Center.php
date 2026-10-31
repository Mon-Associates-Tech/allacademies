<?php

namespace App\ExaminationHub\Models;

use App\ExaminationHub\Traits\HasUuid;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Center extends Model
{
    use HasFactory, HasUuid;

    protected $table = 'general_exam_centers';

    protected $fillable = [
        'uuid',
        'code',
        'name',
        'token_hash',
        'is_active',
        'last_seen_at',
        'last_pull_at',
        'last_push_at',
        'app_version',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'last_seen_at' => 'datetime',
            'last_pull_at' => 'datetime',
            'last_push_at' => 'datetime',
        ];
    }

    /**
     * Hash a raw token for storage.
     */
    public static function hashToken(string $token): string
    {
        return hash('sha256', $token);
    }

    /**
     * Verify a raw token against the stored hash.
     */
    public function verifyToken(string $token): bool
    {
        return $this->token_hash === self::hashToken($token);
    }

    /**
     * Generate a new random sync token.
     */
    public static function generateToken(): string
    {
        return bin2hex(random_bytes(32));
    }

    /**
     * Get all active centers.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Get centers that haven't been seen recently.
     */
    public function scopeStale($query, int $minutes = 60)
    {
        return $query->where('last_seen_at', '<', now()->subMinutes($minutes))
            ->where('is_active', true);
    }

    /**
     * Related exam submissions from this center.
     */
    public function submissions(): HasMany
    {
        return $this->hasMany(GeneralExamSubmission::class, 'center_id');
    }

    /**
     * Related proctoring logs from this center.
     */
    public function proctoringLogs(): HasMany
    {
        return $this->hasMany(ExamProctoringLog::class, 'center_id');
    }
}
