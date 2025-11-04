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
        'business_name',
        'settlement_bank',
        'account_number',
        'percentage_charge',
        'description',
        'paystack_response',
        'bank_code'
    ];

    protected $casts = [
        'paystack_response' => 'array',
        'percentage_charge' => 'decimal:2',
    ];

    /**
     * Get the parent subaccountable model (School, Author, User, etc.)
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
        return $this->business_name ?? $this->subaccountable?->name ?? 'Unknown';
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

    /**
     * Scope to get subaccounts for a specific model type
     */
    public function scopeForModel($query, string $modelClass)
    {
        return $query->where('subaccountable_type', $modelClass);
    }

    /**
     * Scope to get school subaccounts
     */
    public function scopeForSchools($query)
    {
        return $query->where('subaccountable_type', School::class);
    }

    /**
     * Scope to get author subaccounts
     */
    public function scopeForAuthors($query)
    {
        return $query->where('subaccountable_type', Author::class);
    }
}
