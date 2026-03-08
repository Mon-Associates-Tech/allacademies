<?php

namespace App\Enums;

/**
 * Grade enum emulating the BECE (Basic Education Certificate Examination) grading system.
 *
 * Grade Scale:
 * - Grade 1: 80-100% (Excellent)
 * - Grade 2: 70-79% (Very Good)
 * - Grade 3: 60-69% (Good)
 * - Grade 4: 55-59% (Credit)
 * - Grade 5: 50-54% (Credit)
 * - Grade 6: 45-49% (Credit)
 * - Grade 7: 40-44% (Pass)
 * - Grade 8: 35-39% (Pass)
 * - Grade 9: 0-34% (Fail)
 */
enum Grade: string
{
    case GRADE_1 = '1';
    case GRADE_2 = '2';
    case GRADE_3 = '3';
    case GRADE_4 = '4';
    case GRADE_5 = '5';
    case GRADE_6 = '6';
    case GRADE_7 = '7';
    case GRADE_8 = '8';
    case GRADE_9 = '9';

    /**
     * Get the grade based on percentage score (BECE grading system)
     */
    public static function fromPercentage(float $percentage): self
    {
        return match (true) {
            $percentage >= 80 => self::GRADE_1,
            $percentage >= 70 => self::GRADE_2,
            $percentage >= 60 => self::GRADE_3,
            $percentage >= 55 => self::GRADE_4,
            $percentage >= 50 => self::GRADE_5,
            $percentage >= 45 => self::GRADE_6,
            $percentage >= 40 => self::GRADE_7,
            $percentage >= 35 => self::GRADE_8,
            default => self::GRADE_9,
        };
    }

    /**
     * Get the minimum percentage required for this grade
     */
    public function getMinimumPercentage(): int
    {
        return match ($this) {
            self::GRADE_1 => 80,
            self::GRADE_2 => 70,
            self::GRADE_3 => 60,
            self::GRADE_4 => 55,
            self::GRADE_5 => 50,
            self::GRADE_6 => 45,
            self::GRADE_7 => 40,
            self::GRADE_8 => 35,
            self::GRADE_9 => 0,
        };
    }

    /**
     * Get the maximum percentage for this grade
     */
    public function getMaximumPercentage(): int
    {
        return match ($this) {
            self::GRADE_1 => 100,
            self::GRADE_2 => 79,
            self::GRADE_3 => 69,
            self::GRADE_4 => 59,
            self::GRADE_5 => 54,
            self::GRADE_6 => 49,
            self::GRADE_7 => 44,
            self::GRADE_8 => 39,
            self::GRADE_9 => 34,
        };
    }

    /**
     * Get grade description/interpretation
     */
    public function getDescription(): string
    {
        return match ($this) {
            self::GRADE_1 => 'Excellent',
            self::GRADE_2 => 'Very Good',
            self::GRADE_3 => 'Good',
            self::GRADE_4 => 'Credit',
            self::GRADE_5 => 'Credit',
            self::GRADE_6 => 'Credit',
            self::GRADE_7 => 'Pass',
            self::GRADE_8 => 'Pass',
            self::GRADE_9 => 'Fail',
        };
    }

    /**
     * Check if this is a passing grade (Grade 1-8 are passing, Grade 9 is failing)
     */
    public function isPassing(): bool
    {
        return $this !== self::GRADE_9;
    }

    /**
     * Check if this is a credit grade (Grade 1-6)
     */
    public function isCredit(): bool
    {
        return in_array($this, [
            self::GRADE_1,
            self::GRADE_2,
            self::GRADE_3,
            self::GRADE_4,
            self::GRADE_5,
            self::GRADE_6,
        ], true);
    }

    /**
     * Get the numeric grade value
     */
    public function getNumericValue(): int
    {
        return (int) $this->value;
    }

    /**
     * Get all grade options as an array
     */
    public static function getOptions(): array
    {
        return [
            self::GRADE_1->value => self::GRADE_1->getDescription().' (80-100%)',
            self::GRADE_2->value => self::GRADE_2->getDescription().' (70-79%)',
            self::GRADE_3->value => self::GRADE_3->getDescription().' (60-69%)',
            self::GRADE_4->value => self::GRADE_4->getDescription().' (55-59%)',
            self::GRADE_5->value => self::GRADE_5->getDescription().' (50-54%)',
            self::GRADE_6->value => self::GRADE_6->getDescription().' (45-49%)',
            self::GRADE_7->value => self::GRADE_7->getDescription().' (40-44%)',
            self::GRADE_8->value => self::GRADE_8->getDescription().' (35-39%)',
            self::GRADE_9->value => self::GRADE_9->getDescription().' (0-34%)',
        ];
    }

    /**
     * Get grade range as string
     */
    public function getRange(): string
    {
        return $this->getMinimumPercentage().'-'.$this->getMaximumPercentage().'%';
    }

    /**
     * Get all grades with their full details
     *
     * @return array<int, array{grade: string, min_score: int, max_score: int, interpretation: string, is_passing: bool}>
     */
    public static function all(): array
    {
        $grades = [];

        foreach (self::cases() as $grade) {
            $grades[] = [
                'grade' => $grade->value,
                'min_score' => $grade->getMinimumPercentage(),
                'max_score' => $grade->getMaximumPercentage(),
                'interpretation' => $grade->getDescription(),
                'is_passing' => $grade->isPassing(),
            ];
        }

        return $grades;
    }
}
