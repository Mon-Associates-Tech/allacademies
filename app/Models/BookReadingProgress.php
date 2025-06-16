<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\ActivityLog\Traits\LogsActivity;
use Spatie\ActivityLog\LogOptions;

class BookReadingProgress extends Model
{
    use LogsActivity;

    protected $fillable = [
        'book_id',
        'student_id',
        'current_page',
        'total_pages',
        'last_read_at'
    ];

    protected $casts = [
        'last_read_at' => 'datetime'
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['current_page', 'total_pages'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class);
    }
}
