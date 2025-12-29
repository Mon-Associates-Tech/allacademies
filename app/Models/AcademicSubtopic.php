<?php

namespace App\Models;

use App\Traits\AcademicGroupLogs;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

class AcademicSubtopic extends Model
{
    use HasFactory;
    use AcademicGroupLogs;

    public function academicTopic(): BelongsTo {
        return $this->belongsTo(AcademicTopic::class);
    }

    public function topic(): BelongsTo {
        return $this->belongsTo(AcademicTopic::class, 'academic_topic_id');
    }

    protected $fillable = [
        'name',
        'academic_topic_id',
        'slug',
        'description',
    ];

    public function essayQuestions(): HasMany {
        return $this->hasMany(EssayQuestion::class);
    }

    public function multipleChoiceQuestions()
    {
        return $this->hasMany(MultipleChoiceQuestion::class);
    }

    public function trueOrFalseQuestions()
    {
        return $this->hasMany(TrueOrFalseQuestion::class);
    }

    public function questions()
    {
        return $this->morphMany(Question::class, 'questionable');
    }

    public function subject()
    {
        return $this->academicTopic?->academicSubject;
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
