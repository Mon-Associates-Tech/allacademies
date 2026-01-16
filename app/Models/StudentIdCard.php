<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentIdCard extends Model
{
    protected $fillable = [
        'student_id',
        'card_number',
        'issue_date',
        'expiry_date',
        'photo_path',
        'barcode',
        'status', // active, expired, lost, etc.
    ];

    protected $casts = [
        'issue_date' => 'date',
        'expiry_date' => 'date',
    ];

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }
}
