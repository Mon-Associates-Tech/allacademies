<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AcademicLevel extends Model
{
    use HasFactory;
    use SoftDeletes;

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'label',
    ];

    public function academicGroup()
    {
        return $this->belongsTo(AcademicGroup::class);
    }

    public function academicSubjects()
    {
        return $this->hasMany(AcademicSubject::class);
    }
}
