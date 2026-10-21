<?php

namespace App\MockExam\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MockExamPricingTier extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'user_group',
        'subject_count',
        'price_per_student',
        'print_flat_rate',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'user_group'        => MockExamUserGroup::class,
            'price_per_student' => 'decimal:2',
            'print_flat_rate'   => 'decimal:2',
            'is_active'         => 'boolean',
        ];
    }

    public function scopeActive($query): void
    {
        $query->where('is_active', true);
    }

    /**
     * Find the best-matching tier for a subject count and user group.
     * Falls back to the highest available tier for that group, then to 'teacher'.
     */
    public static function forSubjectCountAndGroup(int $count, string $userGroup = 'teacher'): ?self
    {
        $find = fn (string $group) => self::active()
            ->where('user_group', $group)
            ->where('subject_count', '<=', $count)
            ->orderByDesc('subject_count')
            ->first();

        return $find($userGroup) ?? ($userGroup !== 'teacher' ? $find('teacher') : null);
    }

    /** @deprecated Use forSubjectCountAndGroup() */
    public static function forSubjectCount(int $count): ?self
    {
        return self::forSubjectCountAndGroup($count, 'teacher');
    }
}
