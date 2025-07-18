<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentParent extends Model
{
    protected $table  = 'parents';
    protected $fillable = [
        'user_id',
        'relationship',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function student(){
        return $this->belongsTo(Student::class);
    }
    public function students(){
        return $this->belongsToMany(Student::class, 'parent_student', 'parent_id', 'student_id');
    }
}
