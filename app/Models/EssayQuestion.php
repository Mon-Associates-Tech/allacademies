<?php

namespace App\Models;

use App\Support\Mark;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class EssayQuestion extends Model
{
    use HasFactory;
    use SoftDeletes;

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
        'answer' => Mark::class,
    ];

    public function academicTopic()
    {
        return $this->belongsTo(AcademicTopic::class);
    }
}
