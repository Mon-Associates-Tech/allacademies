<?php

namespace App\BookShop\Models;

use App\BookShop\Enums\FulfillmentMethod;
use App\BookShop\Enums\OrderStatus;
use App\BookShop\Enums\PaymentStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Order extends Model
{
    use HasFactory;

    protected $table = 'bookshop_orders';

    protected $fillable = [
        'order_number',
        'customer_id',
        'branch_id',
        'status',
        'payment_status',
        'payment_reference',
        'paid_at',
        'fulfillment_method',
        'delivery_address',
        'subtotal',
        'notes',
        'cancelled_reason',
        'cancelled_at',
        'completed_at',
    ];

    protected $casts = [
        'status' => OrderStatus::class,
        'payment_status' => PaymentStatus::class,
        'fulfillment_method' => FulfillmentMethod::class,
        'paid_at' => 'datetime',
        'subtotal' => 'decimal:2',
        'cancelled_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    public function isPaid(): bool
    {
        return $this->payment_status === PaymentStatus::PAID;
    }

    public function isDelivery(): bool
    {
        return $this->fulfillment_method === FulfillmentMethod::DELIVERY;
    }

    protected static function booted(): void
    {
        static::creating(function (Order $order) {
            if (empty($order->order_number)) {
                $order->order_number = static::generateOrderNumber();
            }
        });
    }

    public static function generateOrderNumber(): string
    {
        do {
            $number = 'ORD-'.now()->format('ymd').'-'.strtoupper(substr(uniqid(), -5));
        } while (static::where('order_number', $number)->exists());

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

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Reverse of BulkOrderRequest::order() - lets an order that originated
     * from an accepted bulk quote link back to that request (e.g. to
     * show "this order came from bulk request BULK-260725-ABCDE").
     */
    public function bulkOrderRequest(): HasOne
    {
        return $this->hasOne(BulkOrderRequest::class);
    }

    /**
     * Single source of truth for order visibility, matching the
     * Branch::scopeVisibleTo(Staff $staff) / BranchStockLevel pattern:
     *  - superadmin: unrestricted
     *  - admin: scoped to their own branch's orders only
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
