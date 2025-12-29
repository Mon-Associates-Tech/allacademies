<?php

namespace App\Models;

use App\Traits\AcademicGroupLogs;
use App\Traits\Trackable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class AcademicTopic extends Model
{
    use HasFactory;
    use SoftDeletes;
    use Trackable;
    use AcademicGroupLogs;

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'academic_subject_id',
        'description',

    ];

    public function academicSubject()
    {
        return $this->belongsTo(AcademicSubject::class);
    }

    public function multipleChoiceQuestions()
    {
        return $this->hasMany(MultipleChoiceQuestion::class);
    }

    public function essayQuestions()
    {
        return $this->hasMany(EssayQuestion::class);
    }

    public function trueOrFalseQuestions()
    {
        return $this->hasMany(TrueOrFalseQuestion::class);
    }

    public function subtopics(): Builder|HasMany|AcademicTopic
    {
        return $this->hasMany(AcademicSubtopic::class, 'academic_topic_id');
    }

    public function lessonNotes()
    {
        return $this->hasMany(LessonNote::class);
    }

    public function questions()
    {
        return $this->morphMany(Question::class, 'questionable');
    }

    public function resources(): MorphMany
    {
        return $this->morphMany(AcademicResource::class, 'resourceable');
    }

    public function todos(): MorphMany
    {
        return $this->morphMany(Todo::class, 'todoable');
    }

    public function notes(): MorphMany
    {
        return $this->morphMany(Note::class, 'noteable');
    }
}
