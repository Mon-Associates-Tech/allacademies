<?php

namespace App\Models;

use App\Traits\BelongsToSchoolEnhanced;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BookAnnotation extends Model
{
    use BelongsToSchoolEnhanced;
    use HasFactory;

    protected $fillable = [
        'school_id',
        'book_id',
        'user_id',
        'page_number',
        'x_pct',
        'y_pct',
        'width_pct',
        'height_pct',
        'color',
        'resolved_at',
        'resolved_by',
    ];

    protected $casts = [
        'resolved_at' => 'datetime',
        'x_pct' => 'decimal:4',
        'y_pct' => 'decimal:4',
        'width_pct' => 'decimal:4',
        'height_pct' => 'decimal:4',
    ];

    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function resolver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'resolved_by');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(BookAnnotationComment::class);
    }
}
