<?php

namespace App\Models;

use App\Support\Mark;
use App\Traits\Trackable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class TrueOrFalseQuestion extends Model
{
    use HasFactory;
    use SoftDeletes;
    use Trackable;

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'question',
        'answer',
        'score',
        'difficulty_level',
        'academic_topic_id',
        'academic_subtopic_id'
    ];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'question' => Mark::class,
        'answer' => 'boolean',
    ];

    public function academicTopic(): BelongsTo
    {
        return $this->belongsTo(AcademicTopic::class);
    }

    public function subtopic(): BelongsTo
    {
        return $this->belongsTo(AcademicSubtopic::class, 'academic_subtopic_id');
    }
}
