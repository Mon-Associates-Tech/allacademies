<?php

namespace App\BookShop\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Snapshots title/author/unit_price at order time (rather than always
 * joining to Book) so an order's receipt stays accurate even if the
 * catalog entry is later edited or deactivated — same rationale as
 * ExaminationHub's GeneralExamQuestion storing full content copies.
 */
class OrderItem extends Model
{
    use HasFactory;

    protected $table = 'bookshop_order_items';

    protected $fillable = [
        'order_id',
        'book_id',
        'title_snapshot',
        'author_snapshot',
        'unit_price',
        'quantity',
        'line_total',
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
        'line_total' => 'decimal:2',
        'quantity' => 'integer',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class);
    }
}
