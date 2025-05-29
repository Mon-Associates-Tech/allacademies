<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GroupBookSubscription extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_group_id',
        'book_id',
        'start_date',
        'end_date',
        'status',
        'subscribed_by_type',
        'subscribed_by_id'
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
    ];

    public function studentGroup()
    {
        return $this->belongsTo(StudentGroup::class);
    }

    public function book()
    {
        return $this->belongsTo(Book::class);
    }

    public function subscribedBy()
    {
        return $this->morphTo('subscribed_by');
    }
}
