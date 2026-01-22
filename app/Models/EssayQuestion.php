<?php

namespace App\Models;

use App\Support\Mark;
use App\Traits\HasQuestionAndAnswer;
use App\Traits\QuestionOwnership;
use App\Traits\Trackable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class EssayQuestion extends Model
{
    use HasFactory;
    use HasQuestionAndAnswer;
    use QuestionOwnership;
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
        'academic_subtopic_id',
        'added_by',
        'modified_by',
    ];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'question' => Mark::class,
        'answer' => Mark::class,
    ];

    public function academicTopic(): BelongsTo
    {
        return $this->belongsTo(AcademicTopic::class);
    }

    public function topic(): BelongsTo
    {
        return $this->belongsTo(AcademicTopic::class, 'academic_topic_id');
    }

    public function subtopic(): BelongsTo
    {
        return $this->belongsTo(AcademicSubtopic::class, 'academic_subtopic_id');
    }

    public function question()
    {
        return $this->morphOne(Question::class, 'questionable');
    }

    public function getQuestion(): array
    {
        return $this->processQuestionModel($this);
    }
}
