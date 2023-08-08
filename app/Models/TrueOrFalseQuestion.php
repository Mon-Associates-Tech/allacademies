<?php

namespace App\Models;

use App\Support\Mark;
use App\Traits\Trackable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TrueOrFalseQuestion extends Model
{
    use HasFactory;
    use Trackable;

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'question',
        'answer',
        'score',
        'difficulty_level',
    ];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'question' => Mark::class,
        'answer' => 'boolean',
    ];

    public function academicTopic()
    {
        return $this->belongsTo(AcademicTopic::class);
    }
}
