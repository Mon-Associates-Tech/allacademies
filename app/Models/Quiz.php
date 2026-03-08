<?php

namespace App\Models;

use App\Traits\ActivityLoggable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Quiz extends Model
{
    use ActivityLoggable;
    use HasFactory;

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'starts_at',
        'ends_at',
        'duration_in_minutes',
        'sections',
    ];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'sections' => 'array',
    ];

    protected $with = [
        'academicSubject',
    ];

    public function academicSubject()
    {
        return $this->belongsTo(AcademicSubject::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    public function team()
    {
        return $this->belongsTo(Team::class);
    }

    public function worksheets()
    {
        return $this->hasMany(Worksheet::class);
    }
}
