<?php

namespace App\BookShop\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * The central stock pool a superadmin allocates from when approving a
 * branch's RestockRequest. One row per book (unique on book_id) — this
 * is deliberately NOT branch-scoped, unlike BranchStockLevel.
 */
class WarehouseStock extends Model
{
    use HasFactory;

    protected $table = 'bookshop_warehouse_stock';

    protected $fillable = [
        'book_id',
        'quantity',
        'updated_by_staff_id',
    ];

    protected $casts = [
        'quantity' => 'integer',
    ];

    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class);
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(Staff::class, 'updated_by_staff_id');
    }
}
