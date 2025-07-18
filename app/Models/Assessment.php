<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Assessment extends Model
{
    use LogsActivity;
    use HasFactory;

    public const STATUS_NOT_STARTED = 'not_started';
    public const STATUS_IN_PROGRESS = 'in_progress';
    public const STATUS_COMPLETED = 'completed';
    public const STATUS_PENDING_REVIEW = 'pending_review';
    public const STATUS_GRADED = 'graded';

    public const TYPE_SELF = 'self';
    public const TYPE_ASSIGNMENT = 'assignment';

    protected $fillable = [
        'student_id',
        'subject_id',
        'topic_id',
        'subtopic_id',
        'book_id',
        'assignment_id',
        'title',
        'type',
        'question_types',
        'score',
        'max_score',
        'percentage_score',
        'start_time',
        'end_time',
        'time_limit_minutes',
        'status',
        'has_essay_questions',
        'essay_grading_status',
        'graded_by',
        'graded_at',
        'teacher_feedback',
        'questions_data'
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'graded_at' => 'datetime',
        'question_types' => 'array',
        'has_essay_questions' => 'boolean',
        'score' => 'decimal:2',
        'max_score' => 'decimal:2',
        'percentage_score' => 'decimal:2',
        'questions_data' => 'array',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function subject(): BelongsTo
    {
        return $this->belongsTo(AcademicSubject::class);
    }

    public function topic(): BelongsTo
    {
        return $this->belongsTo(AcademicTopic::class);
    }

    public function subtopic(): BelongsTo
    {
        return $this->belongsTo(AcademicSubtopic::class);
    }

    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class);
    }

    public function assignment(): BelongsTo
    {
        return $this->belongsTo(Assignment::class);
    }

    public function assessmentResponse(): HasOne
    {
        return $this->hasOne(AssessmentResponse::class);
    }

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class, 'graded_by');
    }

    public function updateScoreFromResponse(): void
    {
        if ($this->assessmentResponse) {
            $summary = $this->assessmentResponse->getSummaryData();

            $this->update([
                'score' => $summary['total_score'],
                'max_score' => $summary['max_score'],
                'percentage_score' => $summary['percentage'],
            ]);
        }
    }

    public function canAutoGrade(): bool
    {
        return !$this->has_essay_questions;
    }

    public function needsManualGrading(): bool
    {
        return $this->has_essay_questions &&
               $this->essay_grading_status === 'pending' &&
               $this->assessmentResponse &&
               !$this->assessmentResponse->allEssaysGraded();
    }

    public function getTeacherForGrading(): ?Teacher
    {
        return $this->student->primaryTeacher()->first();
    }

    public function markAsCompleted(): void
    {
        $status = $this->canAutoGrade() ? self::STATUS_COMPLETED : self::STATUS_PENDING_REVIEW;
        $essayGradingStatus = $this->has_essay_questions ? 'pending' : null;

        $this->update([
            'status' => $status,
            'end_time' => now(),
            'essay_grading_status' => $essayGradingStatus,
        ]);

        if ($this->canAutoGrade()) {
            $this->updateScoreFromResponse();
        }
    }

    public function markAsGraded(): void
    {
        $this->update([
            'status' => self::STATUS_GRADED,
            'essay_grading_status' => 'completed',
            'graded_at' => now(),
        ]);

        $this->updateScoreFromResponse();
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['title', 'score', 'max_score', 'percentage_score', 'status'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    public function getQuestionsData(): array
    {
        return $this->questions_data ?? [];
    }

    public function setQuestionsData(array $questionsData): void
    {
        $this->update(['questions_data' => $questionsData]);
    }

}
