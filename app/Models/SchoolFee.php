<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Relation;

class SchoolFee extends Model
{
    protected $fillable = [
        'school_id',
        'student_id',
        'financial_aid_id',
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

    protected static function boot()
    {
        parent::boot();

        Relation::morphMap([
            'parent', 'student', 'other' => User::class,
        ]);
    }

    public function academicFeeStructure()
    {
        return $this->belongsTo(AcademicFeeStructure::class, 'academic_fee_structure_id');
    }

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }


    // Relationship to term (academic period)
    public function academicPeriod()
    {
        return $this->belongsTo(AcademicPeriod::class, 'term_id');
    }

    // Relationship to academic group
    public function academicGroup()
    {
        return $this->belongsTo(AcademicGroup::class, 'academic_group_id');
    }

    // Relationship to academic level
    public function academicLevel()
    {
        return $this->belongsTo(AcademicLevel::class, 'academic_level_id');
    }

    public function financialAid()
    {
        return $this->belongsTo(FinancialAid::class);
    }

}
