<?php

namespace App\BookShop\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Storage;

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
        'preview_pdf_path',
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

    public function warehouseStock(): HasOne
    {
        return $this->hasOne(WarehouseStock::class);
    }

    public function restockRequests(): HasMany
    {
        return $this->hasMany(RestockRequest::class);
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

    public function hasCover(): bool
    {
        return ! empty($this->cover_image_path);
    }

    public function hasPreview(): bool
    {
        return ! empty($this->preview_pdf_path);
    }

    /**
     * Both files live on the 'public' disk (storage/app/public, symlinked
     * to public/storage via `php artisan storage:link`) — see SETUP.md.
     */
    public function coverUrl(): ?string
    {
        return $this->hasCover() ? Storage::disk('public')->url($this->cover_image_path) : null;
    }

    public function previewUrl(): ?string
    {
        return $this->hasPreview() ? Storage::disk('public')->url($this->preview_pdf_path) : null;
    }
}
