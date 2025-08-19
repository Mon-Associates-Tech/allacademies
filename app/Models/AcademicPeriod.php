<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AcademicPeriod extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type', // semester/term
        'start_date',
        'end_date',
        'description',
        'is_active',
        'academic_year',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'is_active' => 'boolean',
    ];

    public function courseOutlines(): HasMany
    {
        return $this->hasMany(CourseOutline::class);
    }
}
