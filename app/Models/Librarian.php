<?php

namespace App\Models;

use App\Traits\ActivityLoggable;
use App\Traits\BelongsToSchoolEnhanced;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Librarian extends Model
{
    use ActivityLoggable;
    use BelongsToSchoolEnhanced;
    use HasFactory;

    protected $fillable = ['user_id', 'employee_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function bookApprovals()
    {
        return $this->hasMany(BookApproval::class);
    }

    public function bookLendings()
    {
        return $this->hasMany(BookLending::class);
    }

    public static function generateEmployeeId($schoolId)
    {
        $school = School::find($schoolId);
        if (! $school) {
            return null;
        }

        $lastLibrarian = static::withoutGlobalScope('school')
            ->where('school_id', $schoolId)
            ->where('employee_id', 'like', "{$school->code}L%")
            ->latest('employee_id')
            ->first();

        $sequence = $lastLibrarian ?
            (int) substr($lastLibrarian->employee_id, -4) + 1 : 1;

        return $school->code.'L'.str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }
}
