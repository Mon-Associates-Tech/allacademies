<?php

namespace App\Models;

use App\Traits\Trackable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AcademicGroup extends Model
{
    use HasFactory;
    use Trackable;

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
    ];

    public function academicLevels()
    {
        return $this->hasMany(AcademicLevel::class);
    }
}
