<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AssignmentNotification extends Model
{
    use HasFactory;

    protected $fillable = [
        'assignment_id',
        'student_id',
        'notified_at',
        'read_at',
        'message',
    ];

    protected $casts = [
        'notified_at' => 'datetime',
        'read_at' => 'datetime',
    ];

    public function assignment(): BelongsTo
    {
        return $this->belongsTo(Assignment::class);
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }
}
