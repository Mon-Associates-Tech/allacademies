<?php

namespace App\ExaminationHub\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class GeneralExamSubscriptionPlan extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type',
        'max_subjects',
        'max_exams',
        'max_participants',
        'duration_type',
        'duration_value',
        'base_price',
        'is_active',
        'description',
    ];

    protected function casts(): array
    {
        return [
            'base_price' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(GeneralExamSubscription::class);
    }

    public function scopeActive($query): void
    {
        $query->where('is_active', true);
    }

    public function isOnline(): bool
    {
        return $this->type === 'online';
    }

    public function isPrint(): bool
    {
        return $this->type === 'print';
    }

    /**
     * Calculate the price for this plan given a subject count and participant count.
     * If base_price is set (> 0), it overrides tier-based calculation.
     */
    public function calculatePrice(int $subjectCount, int $participantCount = 0): float
    {
        if ($this->base_price > 0) {
            return (float) $this->base_price;
        }

        $tier = GeneralExamPricingTier::forSubjectCount($subjectCount);

        if (! $tier) {
            return 0.0;
        }

        if ($this->type === 'print') {
            return (float) $tier->print_flat_rate * $subjectCount;
        }

        return (float) $tier->price_per_student * $participantCount;
    }
}
