<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AcademicTopic extends Model
{
    use HasFactory;

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

    public function multipleChoiceQuestion()
    {
        return $this->hasMany(MultipleChoiceQuestion::class);
    }

    public function essayQuestion()
    {
        return $this->hasMany(EssayQuestion::class);
    }

    public function trueOrFalseQuestion()
    {
        return $this->hasMany(TrueOrFalseQuestion::class);
    }
}
