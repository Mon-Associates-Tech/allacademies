<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CourseOutlineItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_outline_id',
        'topic_id',
        'subtopic_id',
        'planned_date',
        'teaching_strategy',
        'resources_needed',
        'learning_objectives',
        'assessment_method',
        'notes',
        'order',
    ];

    protected $casts = [
        'planned_date' => 'date',
    ];

    public function courseOutline(): BelongsTo
    {
        return $this->belongsTo(CourseOutline::class);
    }

    public function topic(): BelongsTo
    {
        return $this->belongsTo(AcademicTopic::class);
    }

    public function subtopic(): BelongsTo
    {
        return $this->belongsTo(AcademicSubtopic::class);
    }
}
