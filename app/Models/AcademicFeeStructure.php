<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AcademicFeeStructure extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_id',
        'academic_group_id',
        'academic_level_id',
        'current_term_id',
        'amount',
        'due_date',
        'payment_method',
    ];

    /**
     * 🔹 Relationships
     */
    public function school()
    {
        return $this->belongsTo(School::class);
    }

    public function academicGroup()
    {
        return $this->belongsTo(AcademicGroup::class);
    }

    public function academicLevel()
    {
        return $this->belongsTo(AcademicLevel::class);
    }

    public function currentTerm()
    {
        return $this->belongsTo(AcademicPeriod::class, 'current_term_id');
    }

    /**
     * 🔹 Accessors
     */
    public function getFormattedAmountAttribute()
    {
        return '₵'.number_format($this->amount, 2);
    }

    public function getFormattedDueDateAttribute()
    {
        return \Carbon\Carbon::parse($this->due_date)->format('M d, Y');
    }
}
