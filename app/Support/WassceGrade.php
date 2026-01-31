<?php

namespace App\Support;

final class WassceGrade
{
    /**
     * Grade definitions with score ranges and interpretations.
     * Format: [grade => [min_score, max_score, interpretation]]
     *
     * @var array<string, array{int, int, string}>
     */
    public const GRADES = [
        'A1' => [80, 100, 'Excellent'],
        'B2' => [70, 79, 'Very Good'],
        'B3' => [65, 69, 'Good'],
        'C4' => [60, 64, 'Credit'],
        'C5' => [55, 59, 'Credit'],
        'C6' => [50, 54, 'Credit'],
        'D7' => [45, 49, 'Pass'],
        'E8' => [40, 44, 'Pass'],
        'F9' => [0, 39, 'Fail'],
    ];

    /**
     * The minimum passing grade.
     */
    public const PASSING_GRADE = 'E8';

    /**
     * The failing grade.
     */
    public const FAILING_GRADE = 'F9';

    /**
     * Grades considered as credit or better (for university admission).
     *
     * @var array<int, string>
     */
    public const CREDIT_GRADES = ['A1', 'B2', 'B3', 'C4', 'C5', 'C6'];

    /**
     * Get the grade for a given score.
     */
    public static function getGrade(float|int $score): string
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
    public static function getInterpretation(string $grade): string
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
    public static function getScoreRange(string $grade): ?array
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
    public static function isPassing(string $grade): bool
    {
        $passingGrades = ['A1', 'B2', 'B3', 'C4', 'C5', 'C6', 'D7', 'E8'];

        return in_array($grade, $passingGrades, true);
    }

    /**
     * Check if a score is a passing score.
     */
    public static function isPassingScore(float|int $score): bool
    {
        return self::isPassing(self::getGrade($score));
    }

    /**
     * Check if a grade is a credit grade (C6 or better).
     */
    public static function isCredit(string $grade): bool
    {
        return in_array($grade, self::CREDIT_GRADES, true);
    }

    /**
     * Check if a score achieves a credit grade.
     */
    public static function isCreditScore(float|int $score): bool
    {
        return self::isCredit(self::getGrade($score));
    }

    /**
     * Get the numeric value of a grade (for aggregate calculations).
     */
    public static function getGradeValue(string $grade): int
    {
        $values = [
            'A1' => 1,
            'B2' => 2,
            'B3' => 3,
            'C4' => 4,
            'C5' => 5,
            'C6' => 6,
            'D7' => 7,
            'E8' => 8,
            'F9' => 9,
        ];

        return $values[$grade] ?? 9;
    }

    /**
     * Get all grades with their details.
     *
     * @return array<int, array{grade: string, min_score: int, max_score: int, interpretation: string}>
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
     * @return array{grade: string, grade_value: int, min_score: int, max_score: int, interpretation: string, is_passing: bool, is_credit: bool}
     */
    public static function getDetails(float|int $score): array
    {
        $grade = self::getGrade($score);
        $range = self::getScoreRange($grade);

        return [
            'grade' => $grade,
            'grade_value' => self::getGradeValue($grade),
            'min_score' => $range['min'],
            'max_score' => $range['max'],
            'interpretation' => self::getInterpretation($grade),
            'is_passing' => self::isPassing($grade),
            'is_credit' => self::isCredit($grade),
        ];
    }

    /**
     * Calculate the aggregate score from an array of grades.
     * Lower aggregate is better (minimum possible is 6 for 6 A1s).
     *
     * @param  array<int, string>  $grades
     */
    public static function calculateAggregate(array $grades, int $count = 6): int
    {
        $values = array_map([self::class, 'getGradeValue'], $grades);
        sort($values);

        return array_sum(array_slice($values, 0, $count));
    }
}
