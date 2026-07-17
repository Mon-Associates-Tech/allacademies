<?php

namespace App\BookShop\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Branch extends Model
{
    use HasFactory;

    protected $table = 'bookshop_branches';

    protected $fillable = [
        'name',
        'code',
        'country',
        'country_code',
        'region',
        'city',
        'address',
        'phone',
        'email',
        'latitude',
        'longitude',
        'is_active',
        'created_by_staff_id',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
    ];

    protected static function booted(): void
    {
        static::creating(function (Branch $branch) {
            if (empty($branch->code)) {
                $branch->code = static::generateCode($branch->city ?? $branch->region ?? 'BR');
            }
        });
    }

    public static function generateCode(string $seed): string
    {
        $prefix = strtoupper(substr(preg_replace('/[^A-Za-z]/', '', $seed) ?: 'BR', 0, 3));

        do {
            $code = $prefix.'-'.strtoupper(substr(uniqid(), -5));
        } while (static::where('code', $code)->exists());

        return $code;
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(Staff::class, 'created_by_staff_id');
    }

    public function staff(): HasMany
    {
        return $this->hasMany(Staff::class);
    }

    public function stockLevels(): HasMany
    {
        return $this->hasMany(BranchStockLevel::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    // public function restockRequests(): HasMany { return $this->hasMany(RestockRequest::class); } // Phase 5

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Single source of truth for branch visibility, mirroring the
     * ExaminationHub scopeVisibleTo(User $user) pattern:
     *  - superadmin: unrestricted, sees all branches
     *  - admin: scoped to their own branch only
     */
    public function scopeVisibleTo($query, Staff $staff)
    {
        if ($staff->isSuperAdmin()) {
            return $query;
        }

        return $query->where('id', $staff->branch_id);
    }
}
