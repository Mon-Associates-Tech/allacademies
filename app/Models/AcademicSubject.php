<?php

namespace App\Models;

use App\Traits\Trackable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AcademicSubject extends Model
{
    use HasFactory;
    use SoftDeletes;
    use Trackable;

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
