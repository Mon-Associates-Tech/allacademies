<?php

namespace App\ExaminationHub\Services;

use App\ExaminationHub\Models\ExaminationHubGradeScale;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

class GradingSystemService
{
    private const CACHE_TTL = 300; // 5 minutes

    // ─── Grade resolution ─────────────────────────────────────────────────────

    /**
     * Resolve a percentage to a grade label.
     * Falls back to the built-in A–F scale when no custom scales exist.
     */
    public function resolveGrade(float $percentage, int $userId, ?int $schoolId = null): string
    {
        $scale = $this->resolveGradeScale($percentage, $userId, $schoolId);

        return $scale?->grade_label ?? $this->fallbackGrade($percentage);
    }

    /**
     * Resolve a percentage to the full ExaminationHubGradeScale record.
     */
    public function resolveGradeScale(float $percentage, int $userId, ?int $schoolId = null): ?ExaminationHubGradeScale
    {
        return $this->getActiveScales($userId, $schoolId)
            ->first(fn (ExaminationHubGradeScale $s) => $s->matches($percentage));
    }

    // ─── CRUD ────────────────────────────────────────────────────────────────

    public function createScale(int $userId, ?int $schoolId, array $data): ExaminationHubGradeScale
    {
        $this->guardOverlap($data['min_percentage'], $data['max_percentage'], $userId, $schoolId);

        $scale = ExaminationHubGradeScale::create([
            'user_id'        => $userId,
            'school_id'      => $schoolId,
            'grade_label'    => strtoupper(trim($data['grade_label'])),
            'min_percentage' => (int) $data['min_percentage'],
            'max_percentage' => (int) $data['max_percentage'],
            'grade_point'    => isset($data['grade_point']) ? (float) $data['grade_point'] : null,
            'is_passing'     => (bool) ($data['is_passing'] ?? true),
            'color_code'     => $data['color_code'] ?? '#6B7280',
            'is_active'      => true,
        ]);

        $this->clearCache($userId, $schoolId);

        return $scale;
    }

    public function updateScale(ExaminationHubGradeScale $scale, array $data): ExaminationHubGradeScale
    {
        $this->guardOverlap(
            $data['min_percentage'],
            $data['max_percentage'],
            $scale->user_id,
            $scale->school_id,
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

        $this->clearCache($scale->user_id, $scale->school_id);

        return $scale->fresh();
    }

    public function deleteScale(ExaminationHubGradeScale $scale): void
    {
        $userId   = $scale->user_id;
        $schoolId = $scale->school_id;

        $scale->delete();

        $this->clearCache($userId, $schoolId);
    }

    /**
     * Seed the default A+–F scale for a user (idempotent – no-op if scales exist).
     * Returns the number of records created.
     */
    public function initializeDefaults(int $userId, ?int $schoolId = null): int
    {
        $existing = ExaminationHubGradeScale::forUser($userId)->forSchool($schoolId)->count();

        if ($existing > 0) {
            return 0;
        }

        $created = 0;
        foreach (ExaminationHubGradeScale::defaults() as $row) {
            ExaminationHubGradeScale::create(array_merge($row, [
                'user_id'   => $userId,
                'school_id' => $schoolId,
            ]));
            $created++;
        }

        $this->clearCache($userId, $schoolId);

        return $created;
    }

    // ─── Query helpers ────────────────────────────────────────────────────────

    public function getScalesForUser(int $userId, ?int $schoolId = null): Collection
    {
        return ExaminationHubGradeScale::forUser($userId)
            ->forSchool($schoolId)
            ->orderByDesc('min_percentage')
            ->get();
    }

    public function hasCustomScales(int $userId, ?int $schoolId = null): bool
    {
        return ExaminationHubGradeScale::forUser($userId)
            ->forSchool($schoolId)
            ->active()
            ->exists();
    }

    // ─── Private helpers ─────────────────────────────────────────────────────

    private function getActiveScales(int $userId, ?int $schoolId): Collection
    {
        $cacheKey = "exam_hub_grade_scales.{$userId}." . ($schoolId ?? 'global');

        return Cache::remember($cacheKey, self::CACHE_TTL, fn () =>
            ExaminationHubGradeScale::forUser($userId)
                ->forSchool($schoolId)
                ->active()
                ->get()
        );
    }

    private function clearCache(int $userId, ?int $schoolId): void
    {
        Cache::forget("exam_hub_grade_scales.{$userId}." . ($schoolId ?? 'global'));
    }

    /**
     * Prevent two grade scales in the same user/school set from having
     * overlapping percentage ranges.
     */
    private function guardOverlap(
        int $min,
        int $max,
        int $userId,
        ?int $schoolId,
        ?int $excludeId = null
    ): void {
        $query = ExaminationHubGradeScale::forUser($userId)
            ->forSchool($schoolId)
            ->where(function ($q) use ($min, $max) {
                $q->whereBetween('min_percentage', [$min, $max])
                  ->orWhereBetween('max_percentage', [$min, $max])
                  ->orWhere(fn ($q2) =>
                      $q2->where('min_percentage', '<=', $min)
                         ->where('max_percentage', '>=', $max)
                  );
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
