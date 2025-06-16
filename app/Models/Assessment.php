<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Assessment extends Model
{
    use LogsActivity;

    protected $fillable = [
        'student_id',
        'subject_id',
        'topic_id',
        'subtopic_id',
        'book_id',
        'title',
        'score',
        'max_score',
        'percentage_score',
        'start_time',
        'end_time',
        'status', // 'in_progress', 'completed', 'graded' (for essays)
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['title', 'score', 'max_score', 'percentage_score', 'status'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'student_id');
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

    public function responses(): HasMany|Assessment
    {
        return $this->hasMany(AssessmentResponse::class);
    }

    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class);
    }
}
