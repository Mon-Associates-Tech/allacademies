<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuestionImportBatch extends Model
{
    use HasFactory;

    public const STATUS_PENDING = 'pending';

    public const STATUS_PROCESSING = 'processing';

    public const STATUS_COMPLETED = 'completed';

    public const STATUS_FAILED = 'failed';

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'driver',
        'status',
        'file_path',
        'original_filename',
        'academic_subject_id',
        'academic_topic_id',
        'academic_subtopic_id',
        'results',
        'errors',
        'extraction_method',
        'error_message',
        'completed_at',
    ];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'results' => 'array',
        'errors' => 'array',
        'completed_at' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function academicSubject(): BelongsTo
    {
        return $this->belongsTo(AcademicSubject::class);
    }

    public function academicTopic(): BelongsTo
    {
        return $this->belongsTo(AcademicTopic::class);
    }

    public function academicSubtopic(): BelongsTo
    {
        return $this->belongsTo(AcademicSubtopic::class);
    }

    public function isFinished(): bool
    {
        return in_array($this->status, [self::STATUS_COMPLETED, self::STATUS_FAILED], true);
    }

    public function isOwnedBy(?int $userId): bool
    {
        return $userId !== null && $this->user_id === $userId;
    }
}
