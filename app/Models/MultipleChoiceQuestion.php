<?php

namespace App\Models;

use App\Support\Mark;
use App\Traits\HasQuestionAndAnswer;
use App\Traits\Trackable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use Illuminate\Database\Eloquent\SoftDeletes;

class MultipleChoiceQuestion extends Model
{
    use HasFactory;
    use SoftDeletes;
    use Trackable;
    use HasQuestionAndAnswer;

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'question',
        'option_a',
        'option_b',
        'option_c',
        'option_d',
        'option_e',
        'answer',
        'score',
        'difficulty_level',
        'academic_subtopic_id',
    ];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'question' => Mark::class,
        'option_a' => Mark::class,
        'option_b' => Mark::class,
        'option_c' => Mark::class,
        'option_d' => Mark::class,
        'option_e' => Mark::class,
    ];

    public function academicTopic()
    {
        return $this->belongsTo(AcademicTopic::class);
    }

    public function topic()
    {
        return $this->belongsTo(AcademicTopic::class, 'academic_topic_id');
    }

    public function subtopic(): BelongsTo
    {
        return $this->belongsTo(AcademicSubtopic::class, 'academic_subtopic_id');
    }

    public function academicSubject(): HasOneThrough
    {
        return $this->hasOneThrough(AcademicSubject::class, AcademicTopic::class, 'id', 'id', 'academic_topic_id', 'academic_subject_id');
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
