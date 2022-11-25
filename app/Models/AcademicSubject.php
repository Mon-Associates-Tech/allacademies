<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AcademicSubject extends Model
{
    use HasFactory;

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'code',
    ];

    public function academicLevel()
    {
        return $this->belongsTo(AcademicLevel::class);
    }

    public function academicTopics()
    {
        return $this->hasMany(AcademicTopic::class);
    }

    public function examinations()
    {
        return $this->hasMany(Examination::class);
    }

    public function subscriptions()
    {
        return $this->belongsToMany(Subscription::class);
    }
}
