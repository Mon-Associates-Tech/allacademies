<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
        'additional_info'
    ];

    protected $casts = [
        'has_hardcopy' => 'boolean',
        'has_softcopy' => 'boolean',
    ];

    public function author()
    {
        return $this->belongsTo(Author::class);
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

    public function approvals(){
        return $this->hasMany(BookApproval::class);
    }
}
