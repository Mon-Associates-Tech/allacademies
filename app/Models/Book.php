<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Book extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'author_id',
        'book_category_id',
        'edition',
        'publisher',
        'pages',
        'has_hardcopy',
        'has_softcopy',
        'additional_info',
        'cover_image',
        'content_url',
        'annual_subscription_fee',
        'subscription_conditions'
    ];

    protected $casts = [
        'has_hardcopy' => 'boolean',
        'has_softcopy' => 'boolean',
        'cover_image' => 'string',
        'annual_subscription_fee' => 'decimal:2'
    ];

    public function author()
    {
        return $this->belongsTo(Author::class);
    }

    public function getCoverImageAttribute()
    {
        return asset('images/book-cover.jpg');
    }

    public function getContentUrlAttribute()
    {
        return asset('sample.pdf');
    }

    public function bookCategory()
    {
        return $this->belongsTo(BookCategory::class, 'book_category_id');
    }

    public function borrowings()
    {
        return $this->hasMany(BookBorrowing::class);
    }

    public function subscriptions()
    {
        return $this->hasMany(BookSubscription::class);
    }

    public function groupSubscriptions()
    {
        return $this->hasMany(GroupBookSubscription::class);
    }

    public function approvals()
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
}
