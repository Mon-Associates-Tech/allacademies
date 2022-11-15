<?php

namespace App\Models;

use App\Support\Mark;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Examination extends Model
{
    use HasFactory;

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'heading',
        'sections',
        'examiners',
    ];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'heading' => Mark::class,
        'sections' => 'array',
    ];

    public function academicSubject()
    {
        return $this->belongsTo(AcademicSubject::class);
    }
}
