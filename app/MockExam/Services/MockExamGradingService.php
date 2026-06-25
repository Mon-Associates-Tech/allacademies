<?php

namespace App\MockExam\Services;

use App\MockExam\Models\GradeScale;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class MockExamGradingService
{
    private const CACHE_TTL = 300; // 5 minutes

    // ─── Grade resolution ─────────────────────────────────────────────────────

    /**
     * Resolve a percentage to a grade label.
     * Falls back to built-in A–F scale when the user has no custom scales.
     */
    public function resolveGrade(float $percentage, int $userId): string
    {
        $scale = $this->resolveGradeScale($percentage, $userId);

        return $scale?->grade_label ?? $this->fallbackGrade($percentage);
    }

    public function resolveGradeScale(float $percentage, int $userId): ?GradeScale
    {
        return $this->getActiveScales($userId)
            ->first(fn (GradeScale $s) => $s->matches($percentage));
    }

    // ─── CRUD ────────────────────────────────────────────────────────────────

    public function createScale(int $userId, array $data): GradeScale
    {
        $this->guardOverlap($data['min_percentage'], $data['max_percentage'], $userId);

        $scale = GradeScale::create([
            'user_id'        => $userId,
            'grade_label'    => strtoupper(trim($data['grade_label'])),
            'min_percentage' => (int) $data['min_percentage'],
            'max_percentage' => (int) $data['max_percentage'],
            'grade_point'    => isset($data['grade_point']) ? (float) $data['grade_point'] : null,
            'is_passing'     => (bool) ($data['is_passing'] ?? true),
            'color_code'     => $data['color_code'] ?? '#6B7280',
            'is_active'      => true,
        ]);

        $this->clearCache($userId);

        return $scale;
    }

    public function updateScale(GradeScale $scale, array $data): GradeScale
    {
        $this->guardOverlap(
            $data['min_percentage'],
            $data['max_percentage'],
            $scale->user_id,
            $scale->id
        );

        $scale->update([
            'grade_label'    => strtoupper(trim($data['grade_label'])),
            'min_percentage' => (int) $data['min_percentage'],
            'max_percentage' => (int) $data['max_percentage'],
            'grade_point'    => isset($data['grade_point']) ? (float) $data['grade_point'] : null,
            'is_passing'     => (bool) ($data['is_passing'] ?? true),
            'color_code'     => $data['color_code'] ?? $scale->color_code,
            'is_active'      => (bool) ($data['is_active'] ?? true),
        ]);

        $this->clearCache($scale->user_id);

        return $scale->fresh();
    }

    public function deleteScale(GradeScale $scale): void
    {
        $userId = $scale->user_id;
        $scale->delete();
        $this->clearCache($userId);
    }

    /**
     * Seed the default A+–F scale for a user (idempotent).
     * Returns the number of records created.
     */
    public function initializeDefaults(int $userId): int
    {
        $existing = GradeScale::forUser($userId)->count();

        if ($existing > 0) {
            return 0;
        }

        $created = 0;

        foreach (GradeScale::defaults() as $row) {
            GradeScale::create(array_merge($row, ['user_id' => $userId]));
            $created++;
        }

        $this->clearCache($userId);

        return $created;
    }

    // ─── Query helpers ────────────────────────────────────────────────────────

    public function getScalesForUser(int $userId): Collection
    {
        return GradeScale::forUser($userId)
            ->orderByDesc('min_percentage')
            ->get();
    }

    public function hasCustomScales(int $userId): bool
    {
        return GradeScale::forUser($userId)->active()->exists();
    }

    // ─── Private helpers ──────────────────────────────────────────────────────

    private function getActiveScales(int $userId): Collection
    {
        return Cache::remember(
            "mock_exam_grade_scales.{$userId}",
            self::CACHE_TTL,
            fn () => GradeScale::forUser($userId)->active()->get()
        );
    }

    private function clearCache(int $userId): void
    {
        Cache::forget("mock_exam_grade_scales.{$userId}");
    }

    private function guardOverlap(int $min, int $max, int $userId, ?int $excludeId = null): void
    {
        $query = GradeScale::forUser($userId)
            ->where(function ($q) use ($min, $max) {
                $q->whereBetween('min_percentage', [$min, $max])
                  ->orWhereBetween('max_percentage', [$min, $max])
                  ->orWhere(fn ($q2) => $q2->where('min_percentage', '<=', $min)
                                          ->where('max_percentage', '>=', $max));
            });

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        if ($query->exists()) {
            throw new \InvalidArgumentException(
                "The range {$min}–{$max}% overlaps with an existing grade scale."
            );
        }
    }

    private function fallbackGrade(float $percentage): string
    {
        return match (true) {
            $percentage >= 90 => 'A+',
            $percentage >= 80 => 'A',
            $percentage >= 70 => 'B',
            $percentage >= 60 => 'C',
            $percentage >= 50 => 'D',
            default           => 'F',
        };
    }
}
