<?php

namespace App\Models;

use App\Traits\Trackable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AcademicTopic extends Model
{
    use HasFactory;
    use Trackable;

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
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
}
