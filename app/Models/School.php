<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
class School extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'address',
        'phone',
        'email',
        'website',
        'logo',
        'status',
    ];

    public function academicGroups(){
        return $this->hasMany(AcademicGroup::class);
    }

    public function teachers(){
        return $this->hasMany(Teacher::class);
    }

    public function students(){
        return $this->hasMany(Student::class);
    }

}
