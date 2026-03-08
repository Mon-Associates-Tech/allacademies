<?php

namespace App\Models;

use App\Support\Mark;
use App\Traits\Trackable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Examination extends Model
{
    use HasFactory;
    use Trackable;

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'heading',
        'sections',
        'academic_subject_id',
        'creator_id',
        'team_id',
        'instructions',
        'metadata',
        'duration',
    ];

    /**
     * @var array<string, string>
     */
    protected $casts = [
        'heading' => Mark::class,
        'sections' => 'array',
        'metadata' => 'array',
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
}
