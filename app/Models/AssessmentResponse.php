<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssessmentResponse extends Model
{
    protected $fillable = [
        'assessment_id',
        'data',
    ];

    protected $casts = [
        'response' => 'array', // For multiple choice selections
        'is_correct' => 'boolean',
    ];

    public function assessment()
    {
        return $this->belongsTo(Assessment::class);
    }

    public function question()
    {
        return $this->belongsTo(Question::class);
    }
}
