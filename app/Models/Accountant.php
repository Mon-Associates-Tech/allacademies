<?php

namespace App\Models;

use App\Traits\BelongsToSchoolEnhanced;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Accountant extends Model
{
    use BelongsToSchoolEnhanced, HasFactory;

    protected $fillable = [
        'user_id',
        'school_id',
        'phone',
        'address',
        'date_of_birth',
        'employee_id',
        'hire_date',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'hire_date' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public static function generateEmployeeId($schoolId)
    {
        $school = School::find($schoolId);
        if (! $school) {
            return null;
        }

        $lastAccountant = static::withoutGlobalScope('school')
            ->where('school_id', $schoolId)
            ->where('employee_id', 'like', "{$school->code}A%")
            ->latest('employee_id')
            ->first();

        $sequence = $lastAccountant ?
            (int) substr($lastAccountant->employee_id, -4) + 1 : 1;

        return $school->code.'A'.str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }
}
