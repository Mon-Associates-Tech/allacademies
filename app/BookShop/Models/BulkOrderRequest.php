<?php

namespace App\BookShop\Models;

use App\BookShop\Enums\BulkOrderRequestStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class BulkOrderRequest extends Model
{
    use HasFactory;

    protected $table = 'bookshop_bulk_order_requests';

    protected $fillable = [
        'request_number',
        'customer_id',
        'branch_id',
        'institution_name',
        'institution_type',
        'contact_phone',
        'requested_delivery_date',
        'status',
        'notes',
        'staff_notes',
        'reviewed_by_staff_id',
        'quoted_at',
        'reviewed_at',
        'rejection_reason',
        'order_id',
    ];

    protected $casts = [
        'status' => BulkOrderRequestStatus::class,
        'requested_delivery_date' => 'date',
        'quoted_at' => 'datetime',
        'reviewed_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (BulkOrderRequest $request) {
            if (empty($request->request_number)) {
                $request->request_number = static::generateRequestNumber();
            }
        });
    }

    public static function generateRequestNumber(): string
    {
        do {
            $number = 'BULK-'.now()->format('ymd').'-'.strtoupper(substr(uniqid(), -5));
        } while (static::where('request_number', $number)->exists());

        return $number;
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function reviewedBy(): BelongsTo
    {
        return $this->belongsTo(Staff::class, 'reviewed_by_staff_id');
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(BulkOrderRequestItem::class);
    }

    public function isPending(): bool
    {
        return $this->status === BulkOrderRequestStatus::PENDING;
    }

    public function isQuoted(): bool
    {
        return $this->status === BulkOrderRequestStatus::QUOTED;
    }

    /**
     * Single source of truth for visibility, matching the Branch/Order/
     * RestockRequest scopeVisibleTo(Staff) pattern used everywhere else
     * in the module:
     *  - superadmin: unrestricted, sees every request
     *  - admin: scoped to requests resolved to their own branch
     */
    public function scopeVisibleTo($query, Staff $staff)
    {
        if ($staff->isSuperAdmin()) {
            return $query;
        }

        return $query->where('branch_id', $staff->branch_id);
    }

    public function scopeForCustomer($query, Customer $customer)
    {
        return $query->where('customer_id', $customer->id);
    }
}
