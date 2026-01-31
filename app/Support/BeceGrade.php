<?php

namespace App\Support;

final class BeceGrade
{
    /**
     * Grade definitions with score ranges and interpretations.
     * Format: [grade => [min_score, max_score, interpretation]]
     *
     * @var array<int, array{int, int, string}>
     */
    public const GRADES = [
        1 => [80, 100, 'Excellent'],
        2 => [70, 79, 'Very Good'],
        3 => [60, 69, 'Good'],
        4 => [55, 59, 'Credit'],
        5 => [50, 54, 'Credit'],
        6 => [45, 49, 'Credit'],
        7 => [40, 44, 'Pass'],
        8 => [35, 39, 'Pass'],
        9 => [0, 34, 'Fail'],
    ];

    /**
     * The minimum passing grade.
     */
    public const PASSING_GRADE = 8;

    /**
     * The failing grade.
     */
    public const FAILING_GRADE = 9;

    /**
     * Get the grade for a given score.
     */
    public static function getGrade(float|int $score): int
    {
        $score = (int) round($score);

        foreach (self::GRADES as $grade => [$min, $max]) {
            if ($score >= $min && $score <= $max) {
                return $grade;
            }
        }

        return self::FAILING_GRADE;
    }

    /**
     * Get the interpretation for a given grade.
     */
    public static function getInterpretation(int $grade): string
    {
        return self::GRADES[$grade][2] ?? 'Unknown';
    }

    /**
     * Get the interpretation for a given score.
     */
    public static function getInterpretationForScore(float|int $score): string
    {
        return self::getInterpretation(self::getGrade($score));
    }

    /**
     * Get the score range for a given grade.
     *
     * @return array{min: int, max: int}|null
     */
    public static function getScoreRange(int $grade): ?array
    {
        if (! isset(self::GRADES[$grade])) {
            return null;
        }

        return [
            'min' => self::GRADES[$grade][0],
            'max' => self::GRADES[$grade][1],
        ];
    }

    /**
     * Check if a grade is a passing grade.
     */
    public static function isPassing(int $grade): bool
    {
        return $grade >= 1 && $grade <= self::PASSING_GRADE;
    }

    /**
     * Check if a score is a passing score.
     */
    public static function isPassingScore(float|int $score): bool
    {
        return self::isPassing(self::getGrade($score));
    }

    /**
     * Get all grades with their details.
     *
     * @return array<int, array{grade: int, min_score: int, max_score: int, interpretation: string}>
     */
    public static function all(): array
    {
        $grades = [];

        foreach (self::GRADES as $grade => [$min, $max, $interpretation]) {
            $grades[] = [
                'grade' => $grade,
                'min_score' => $min,
                'max_score' => $max,
                'interpretation' => $interpretation,
            ];
        }

        return $grades;
    }

    /**
     * Get the full grade details for a given score.
     *
     * @return array{grade: int, min_score: int, max_score: int, interpretation: string, is_passing: bool}
     */
    public static function getDetails(float|int $score): array
    {
        $grade = self::getGrade($score);
        $range = self::getScoreRange($grade);

        return [
            'grade' => $grade,
            'min_score' => $range['min'],
            'max_score' => $range['max'],
            'interpretation' => self::getInterpretation($grade),
            'is_passing' => self::isPassing($grade),
        ];
    }
}
