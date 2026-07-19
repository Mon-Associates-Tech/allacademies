<?php

namespace App\BookShop\Models;

use App\BookShop\Enums\RestockRequestStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RestockRequest extends Model
{
    use HasFactory;

    protected $table = 'bookshop_restock_requests';

    protected $fillable = [
        'branch_id',
        'book_id',
        'requested_quantity',
        'status',
        'requested_by_staff_id',
        'reviewed_by_staff_id',
        'reviewed_at',
        'reason',
        'notes',
    ];

    protected $casts = [
        'status' => RestockRequestStatus::class,
        'requested_quantity' => 'integer',
        'reviewed_at' => 'datetime',
    ];

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class);
    }

    public function requestedBy(): BelongsTo
    {
        return $this->belongsTo(Staff::class, 'requested_by_staff_id');
    }

    public function reviewedBy(): BelongsTo
    {
        return $this->belongsTo(Staff::class, 'reviewed_by_staff_id');
    }

    public function isPending(): bool
    {
        return $this->status === RestockRequestStatus::PENDING;
    }

    /**
     * Single source of truth for restock-request visibility, matching the
     * Branch/BranchStockLevel/Order scopeVisibleTo(Staff $staff) pattern:
     *  - superadmin: unrestricted (needs to see every branch's requests
     *    to review them)
     *  - admin: scoped to their own branch's requests only
     */
    public function scopeVisibleTo($query, Staff $staff)
    {
        if ($staff->isSuperAdmin()) {
            return $query;
        }

        return $query->where('branch_id', $staff->branch_id);
    }
}
