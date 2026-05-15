<?php
/**
 * Proctoring Session Model
 *
 * Represents an active or completed proctoring session linked to any
 * examinable model (quiz, exam, assignment, etc.) via polymorphic relations.
 * Provides methods to record violations and manage session lifecycle.
 */
namespace App\Models\Proctoring;

use App\Models\School;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class ExamProctoringSession extends Model
{
    protected $fillable = [
        'user_id', 'proctorable_type', 'proctorable_id', 'school_id',
        'session_token', 'violation_count', 'status', 'started_at', 'ended_at'
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'ended_at' => 'datetime',
    ];

    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function school(): BelongsTo { return $this->belongsTo(School::class); }
    public function violations(): HasMany { return $this->hasMany(ProctoringViolation::class); }

    /** Polymorphic relation to the examinable model */
    public function proctorable(): MorphTo { return $this->morphTo(); }

    public function recordViolation(string $type, array $metadata = []): self
    {
        $this->violations()->create([
            'type' => $type,
            'metadata' => $metadata,
            'severity' => 'low',
            'occurred_at' => now(),
        ]);
        $this->increment('violation_count');
        $this->refresh();
        return $this;
    }
}
