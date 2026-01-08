<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Subaccount extends Model
{
    use HasFactory;

    protected $fillable = [
        'school_id',
        'subaccountable_type',
        'subaccountable_id',
        'subaccount_code',
        'name',
        'business_name',
        'settlement_bank',
        'account_number',
        'percentage_charge',
        'description',
        'paystack_response',
        'bank_code',
        'is_primary',
        'status'
    ];

    protected $casts = [
        'paystack_response' => 'array',
        'percentage_charge' => 'decimal:2',
        'is_primary' => 'boolean',
    ];

    /**
     * Get the parent subaccountable model (School, Author, User, etc.)
     * This is a true polymorphic relationship
     */
    public function subaccountable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Legacy relationship for backward compatibility
     * @deprecated Use subaccountable() instead
     */
    public function school(): BelongsTo
    {
        return $this->belongsTo(School::class);
    }

    /**
     * Check if subaccount belongs to a school
     */
    public function isSchoolAccount(): bool
    {
        return $this->subaccountable_type === School::class;
    }

    /**
     * Check if subaccount belongs to an author
     */
    public function isAuthorAccount(): bool
    {
        return $this->subaccountable_type === Author::class;
    }

    /**
     * Check if subaccount belongs to a user
     */
    public function isUserAccount(): bool
    {
        return $this->subaccountable_type === User::class;
    }

    /**
     * Get the owner's name
     */
    public function getOwnerNameAttribute(): string
    {
        return $this->name ?? $this->business_name ?? $this->subaccountable?->name ?? 'Unknown';
    }

    /**
     * Get the owner's email
     */
    public function getOwnerEmailAttribute(): ?string
    {
        if ($this->subaccountable) {
            return $this->subaccountable->email
                ?? $this->subaccountable->user?->email
                ?? null;
        }
        return null;
    }

    // Scopes for filtering
    public function scopeForModel($query, string $modelClass)
    {
        return $query->where('subaccountable_type', $modelClass);
    }

    public function scopeForSchools($query)
    {
        return $query->where('subaccountable_type', School::class);
    }

    public function scopeForAuthors($query)
    {
        return $query->where('subaccountable_type', Author::class);
    }

    public function scopeForUsers($query)
    {
        return $query->where('subaccountable_type', User::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopePrimary($query)
    {
        return $query->where('is_primary', true);
    }

    public function scopeSecondary($query)
    {
        return $query->where('is_primary', false);
    }

    public function scopeInactive($query)
    {
        return $query->where('status', 'inactive');
    }
}