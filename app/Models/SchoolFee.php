<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SchoolFee extends Model
{
    protected $fillable = [
        'school_id',
        'student_id',
        'payer_id',
        'payer_type',
        'school_name',
        'amount',
        'currency',
        'status',
        'reference',
        'authorization_url',
        'paystack_response',
        'term_total_amount',
        'term_id',
    ];

    // protected $casts = [
    //     'paystack_response' => 'array',
    // ];

    public function school()
    {
        return $this->belongsTo(School::class);
    }

   public function payer()
{
    return $this->morphTo();
}

    public function academicFeeStructure()
    {
        return $this->belongsTo(\App\Models\AcademicFeeStructure::class, 'academic_fee_structure_id');
    }

    public function student()
    {
        return $this->belongsTo(\App\Models\Student::class, 'student_id');
    }


    // Relationship to term (academic period)
    public function academicPeriod()
    {
        return $this->belongsTo(\App\Models\AcademicPeriod::class, 'term_id');
    }

    // Relationship to academic group
    public function academicGroup()
    {
        return $this->belongsTo(\App\Models\AcademicGroup::class, 'academic_group_id');
    }

    // Relationship to academic level
    public function academicLevel()
    {
        return $this->belongsTo(\App\Models\AcademicLevel::class, 'academic_level_id');
    }


}
