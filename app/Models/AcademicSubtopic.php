<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AcademicSubtopic extends Model
{
    use HasFactory;

    public function academicTopic(): BelongsTo {
        return $this->belongsTo(AcademicTopic::class);
    }

    protected $fillable = [
        'name',
        'academic_topic_id',
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
        return $this->hasMany(Question::class);
    }

}
