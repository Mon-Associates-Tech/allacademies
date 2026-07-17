<?php

namespace App\BookShop\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * BookShop's own catalog entity. Deliberately unrelated to the project's
 * existing Book model — per scope, this namespace doesn't reuse existing
 * models (User is the sole exception, and even that isn't used here).
 */
class Book extends Model
{
    use HasFactory;

    protected $table = 'bookshop_books';

    protected $fillable = [
        'category_id',
        'title',
        'author',
        'isbn',
        'description',
        'cover_image_path',
        'price',
        'is_active',
        'created_by_staff_id',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(Staff::class, 'created_by_staff_id');
    }

    public function stockLevels(): HasMany
    {
        return $this->hasMany(BranchStockLevel::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Total quantity across every branch. Not eager-loaded by default —
     * call ->withSum('stockLevels as total_stock', 'quantity') where needed
     * to avoid an N+1 on catalog listings.
     */
    public function totalStock(): int
    {
        return (int) $this->stockLevels()->sum('quantity');
    }
}
