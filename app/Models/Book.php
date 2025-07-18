<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Book extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'author_id',
        'book_category_id',
        'edition',
        'publisher',
        'pages',
        'has_hardcopy',
        'has_softcopy',
        'additional_info',
        'cover_image',
        'cover_image_path',
        'content_url',
        'pdf_file_path',
        'annual_subscription_fee',
        'subscription_conditions'
    ];

    protected $casts = [
        'has_hardcopy' => 'boolean',
        'has_softcopy' => 'boolean',
        'cover_image' => 'string',
        'annual_subscription_fee' => 'decimal:2'
    ];

    public function author(): BelongsTo
    {
        return $this->belongsTo(Author::class);
    }

    public function publisher(): BelongsTo
    {
        return $this->belongsTo(Publisher::class);
    }

    public function copies(): HasMany
    {
        return $this->hasMany(BookInventory::class);
    }
    public function getCoverImageAttribute()
    {
        if ($this->attributes['cover_image']) {
            return asset('storage/' . $this->attributes['cover_image']);
        }
        return asset('images/book-cover.jpg');
    }

    public function getContentUrlAttribute()
    {
        if ($this->attributes['content_url']) {
            return asset('storage/' . $this->attributes['content_url']);
        }
        return asset('sample.pdf');
    }

    public function bookCategory(): BelongsTo
    {
        return $this->belongsTo(BookCategory::class, 'book_category_id');
    }

    public function borrowings(): HasMany
    {
        return $this->hasMany(BookBorrowing::class);
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(BookSubscription::class);
    }

    public function groupSubscriptions(): HasMany
    {
        return $this->hasMany(GroupBookSubscription::class);
    }

    public function approvals(): HasMany
    {
        return $this->hasMany(BookApproval::class);
    }

    public function students(): BelongsToMany
    {
        return $this->belongsToMany(Student::class)
            ->withTimestamps();
    }

    public function teachers(): BelongsToMany
    {
        return $this->belongsToMany(Teacher::class)
            ->withTimestamps();
    }

    public function getFormattedSubscriptionFeeAttribute()
    {
        return 'GHS ' . number_format($this->annual_subscription_fee, 2);
    }

    public function getSubscriptionConditionsAttribute()
    {
        return $this->attributes['subscription_conditions'] ??
            "1. Subscription is valid for one year from payment date\n" .
            "2. Book content is for reading only - no downloading, copying or printing allowed\n" .
            "3. Access will be revoked upon subscription expiry\n" .
            "4. Subscription is non-refundable\n" .
            "5. Content is protected by copyright laws";
    }

    public function getIsFreeAttribute(): bool
    {
        return !$this->annual_subscription_fee || $this->annual_subscription_fee == 0;
    }

    public function scopeFree($query)
    {
        return $query->whereNull('annual_subscription_fee')->orWhere('annual_subscription_fee', 0);
    }

    public function scopePaid($query)
    {
        return $query->where('annual_subscription_fee', '>', 0);
    }
}
