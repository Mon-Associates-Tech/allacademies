<?php

namespace App\BookShop\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class BranchStockLevel extends Model
{
    use HasFactory;

    protected $table = 'bookshop_branch_stock_levels';

    protected $fillable = [
        'branch_id',
        'book_id',
        'quantity',
        'low_stock_threshold',
        'updated_by_staff_id',
    ];

    protected $casts = [
        'quantity' => 'integer',
        'low_stock_threshold' => 'integer',
    ];

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class);
    }

    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(Staff::class, 'updated_by_staff_id');
    }

    public function isLowStock(): bool
    {
        return $this->quantity <= $this->low_stock_threshold;
    }

    /**
     * Single source of truth for stock-level visibility, matching the
     * Branch::scopeVisibleTo(Staff $staff) pattern:
     *  - superadmin: unrestricted
     *  - admin: scoped to their own branch's stock rows only
     */
    public function scopeVisibleTo($query, Staff $staff)
    {
        if ($staff->isSuperAdmin()) {
            return $query;
        }

        return $query->where('branch_id', $staff->branch_id);
    }
}
