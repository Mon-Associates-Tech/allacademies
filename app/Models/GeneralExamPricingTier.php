<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GeneralExamPricingTier extends Model
{
    use HasFactory;

    protected $fillable = [
        'subject_count',
        'price_per_student',
        'print_flat_rate',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'price_per_student' => 'decimal:2',
            'print_flat_rate' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    public function scopeActive($query): void
    {
        $query->where('is_active', true);
    }

    /**
     * Get the tier for a given subject count, falling back to the highest available tier.
     */
    public static function forSubjectCount(int $count): ?self
    {
        return self::active()
            ->where('subject_count', $count)
            ->first()
            ?? self::active()
                ->where('subject_count', '<=', $count)
                ->orderByDesc('subject_count')
                ->first();
    }
}
