<?php

namespace App\Models;

use App\Traits\Trackable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class AcademicLevel extends Model
{
    use HasFactory;
    use SoftDeletes;
    use Trackable;

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'label',
    ];

    public function academicGroup(): BelongsTo
    {
        return $this->belongsTo(AcademicGroup::class);
    }

    public function academicSubjects(): AcademicLevel|HasMany
    {
        return $this->hasMany(AcademicSubject::class);
    }

    public function students(): AcademicLevel|HasMany
    {
        return $this->hasMany(Student::class);
    }

    public function teachers(): AcademicLevel|HasMany
    {
        return $this->hasMany(Teacher::class);
    }

    public function subjects(): AcademicLevel|HasMany
    {
        return $this->hasMany(AcademicSubject::class);
    }
}
