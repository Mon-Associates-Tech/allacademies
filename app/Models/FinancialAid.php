<?php

namespace App\Models;

use App\Traits\BelongsToSchoolEnhanced;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class FinancialAid extends Model
{
    use HasFactory, SoftDeletes, BelongsToSchoolEnhanced;

    protected $fillable = [
        'school_id',
        'name',
        'code',
        'description',
        'amount',
        'school_payment_structure_id',
        'status'
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($aid) {
            if (empty($aid->code)) {
                $aid->code = strtoupper('AID-' . Str::random(8));
            }
        });
    }

    public function beneficiaries(): BelongsToMany
    {
        return $this->belongsToMany(Student::class, 'financial_aid_student')
            ->withTimestamps();
    }

    public function schoolPaymentStructure(): BelongsTo
    {
        return $this->belongsTo(SchoolPaymentStructure::class);
    }


    public function isTuition(): bool
    {
        return $this->schoolPaymentStructure && $this->schoolPaymentStructure->payment_type === 'tuition';
    }
}
