<?php

namespace App\Models;

use App\Traits\Trackable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class AcademicGroup extends Model
{
    use HasFactory;
    use SoftDeletes;
    use Trackable;

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
    ];

    public function academicLevels(): AcademicGroup|HasMany
    {
        return $this->hasMany(AcademicLevel::class);
    }

    public function teachers(): AcademicGroup|HasMany
    {
        return $this->hasMany(Teacher::class);
    }
}
