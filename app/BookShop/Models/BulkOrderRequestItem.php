<?php

namespace App\BookShop\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BulkOrderRequestItem extends Model
{
    use HasFactory;

    protected $table = 'bookshop_bulk_order_request_items';

    protected $fillable = [
        'bulk_order_request_id',
        'book_id',
        'title_snapshot',
        'requested_quantity',
        'quoted_unit_price',
        'quoted_quantity',
    ];

    protected $casts = [
        'requested_quantity' => 'integer',
        'quoted_unit_price' => 'decimal:2',
        'quoted_quantity' => 'integer',
    ];

    public function bulkOrderRequest(): BelongsTo
    {
        return $this->belongsTo(BulkOrderRequest::class);
    }

    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class);
    }

    public function isQuoted(): bool
    {
        return $this->quoted_unit_price !== null;
    }

    public function quotedLineTotal(): ?float
    {
        return $this->isQuoted()
            ? round((float) $this->quoted_unit_price * ($this->quoted_quantity ?? $this->requested_quantity), 2)
            : null;
    }
}
