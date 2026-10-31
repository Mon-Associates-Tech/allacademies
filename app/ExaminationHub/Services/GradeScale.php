<?php

namespace App\ExaminationHub\Services;

/**
 * Grade scale resolver — embedded in the exam package.
 *
 * Centers never call GradingSystemService. Instead, the grading scale is
 * resolved on central at publish time and embedded in the exam package.
 * This class resolves a percentage to a grade letter using that scale.
 *
 * Format of the grading_scale JSON array:
 * [
 *     {"min": 90, "max": 100, "grade": "A+"},
 *     {"min": 80, "max": 89, "grade": "A"},
 *     {"min": 70, "max": 79, "grade": "B"},
 *     {"min": 60, "max": 69, "grade": "C"},
 *     {"min": 50, "max": 59, "grade": "D"},
 *     {"min": 0, "max": 49, "grade": "F"},
 * ]
 */
class GradeScale
{
    /**
     * Resolve a grade letter from a percentage using the embedded grading scale.
     *
     * Falls back to the default scale if no grading scale is provided.
     *
     * @param float $percentage  The score percentage (0-100).
     * @param array|null $gradingScale  The grading scale from the exam package.
     */
    public static function resolve(float $percentage, ?array $gradingScale): string
    {
        if (is_array($gradingScale) && !empty($gradingScale)) {
            return self::resolveFromScale($percentage, $gradingScale);
        }

        return self::defaultScale($percentage);
    }

    /**
     * Resolve from a custom grading scale array.
     *
     * @param float $percentage
     * @param array $gradingScale
     */
    private static function resolveFromScale(float $percentage, array $gradingScale): string
    {
        foreach ($gradingScale as $tier) {
            $min = (float) ($tier['min'] ?? 0);
            $max = (float) ($tier['max'] ?? 100);
            $grade = $tier['grade'] ?? 'F';

            if ($percentage >= $min && $percentage <= $max) {
                return $grade;
            }
        }

        // Fallback if no tier matches
        return self::defaultScale($percentage);
    }

    /**
     * Default grading scale — used when no custom scale is provided.
     */
    private static function defaultScale(float $percentage): string
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

    /**
     * Build the default grading scale array for embedding in a package.
     */
    public static function defaultArray(): array
    {
        return [
            ['min' => 90, 'max' => 100, 'grade' => 'A+'],
            ['min' => 80, 'max' => 89, 'grade' => 'A'],
            ['min' => 70, 'max' => 79, 'grade' => 'B'],
            ['min' => 60, 'max' => 69, 'grade' => 'C'],
            ['min' => 50, 'max' => 59, 'grade' => 'D'],
            ['min' => 0, 'max' => 49, 'grade' => 'F'],
        ];
    }
}
