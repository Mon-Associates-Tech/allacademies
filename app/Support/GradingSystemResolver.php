<?php

namespace App\Support;

use App\Enums\Grade;
use App\Models\User;

final class GradingSystemResolver
{
    /**
     * Academic group tag constants.
     */
    public const TAG_BASIC = 'basic';

    public const TAG_SENIOR = 'senior';

    public const TAG_UNIVERSITY = 'university';

    /**
     * Default grading system when no academic level is set.
     */
    public const DEFAULT_SYSTEM = 'bece';

    /**
     * Get the grading system type for a user.
     *
     * @return string Returns 'bece', 'wassce', or 'university'
     */
    public static function getSystemType(?User $user): string
    {
        if (! $user) {
            return self::DEFAULT_SYSTEM;
        }

        $tag = $user->getAcademicGroupTag();

        return match ($tag) {
            self::TAG_BASIC => 'bece',
            self::TAG_SENIOR => 'wassce',
            self::TAG_UNIVERSITY => 'university',
            default => self::DEFAULT_SYSTEM,
        };
    }

    /**
     * Get the grade for a given percentage score based on user's academic level.
     *
     * @return array{grade: string|int, interpretation: string, is_passing: bool}
     */
    public static function getGrade(?User $user, float|int $percentage): array
    {
        $systemType = self::getSystemType($user);

        return match ($systemType) {
            'bece' => self::getBeceGrade($percentage),
            'wassce' => self::getWassceGrade($percentage),
            'university' => self::getUniversityGrade($percentage),
            default => self::getBeceGrade($percentage),
        };
    }

    /**
     * Get BECE grade details.
     *
     * @return array{grade: int, interpretation: string, is_passing: bool}
     */
    private static function getBeceGrade(float|int $percentage): array
    {
        $grade = BeceGrade::getGrade($percentage);

        return [
            'grade' => $grade,
            'interpretation' => BeceGrade::getInterpretation($grade),
            'is_passing' => BeceGrade::isPassing($grade),
            'system' => 'bece',
        ];
    }

    /**
     * Get WASSCE grade details.
     *
     * @return array{grade: string, interpretation: string, is_passing: bool}
     */
    private static function getWassceGrade(float|int $percentage): array
    {
        $grade = WassceGrade::getGrade($percentage);

        return [
            'grade' => $grade,
            'interpretation' => WassceGrade::getInterpretation($grade),
            'is_passing' => WassceGrade::isPassing($grade),
            'is_credit' => WassceGrade::isCredit($grade),
            'system' => 'wassce',
        ];
    }

    /**
     * Get University grade details (using standard A-F grading).
     *
     * @return array{grade: string, interpretation: string, is_passing: bool}
     */
    private static function getUniversityGrade(float|int $percentage): array
    {
        $grade = Grade::fromPercentage($percentage);

        return [
            'grade' => $grade->value,
            'interpretation' => $grade->getDescription(),
            'is_passing' => $grade->isPassing(),
            'system' => 'university',
        ];
    }

    /**
     * Get the full grade details for a given percentage score.
     *
     * @return array{grade: string|int, interpretation: string, is_passing: bool, min_score: int, max_score: int, system: string}
     */
    public static function getDetails(?User $user, float|int $percentage): array
    {
        $systemType = self::getSystemType($user);

        return match ($systemType) {
            'bece' => array_merge(BeceGrade::getDetails($percentage), ['system' => 'bece']),
            'wassce' => array_merge(WassceGrade::getDetails($percentage), ['system' => 'wassce']),
            'university' => self::getUniversityGradeDetails($percentage),
            default => array_merge(BeceGrade::getDetails($percentage), ['system' => 'bece']),
        };
    }

    /**
     * Get detailed university grade information.
     */
    private static function getUniversityGradeDetails(float|int $percentage): array
    {
        $grade = Grade::fromPercentage($percentage);

        return [
            'grade' => $grade->value,
            'interpretation' => $grade->getDescription(),
            'is_passing' => $grade->isPassing(),
            'min_score' => $grade->getMinimumPercentage(),
            'max_score' => $grade->getMaximumPercentage(),
            'range' => $grade->getRange(),
            'system' => 'university',
        ];
    }

    /**
     * Get all available grades for a grading system.
     */
    public static function getAllGrades(?User $user): array
    {
        $systemType = self::getSystemType($user);

        return match ($systemType) {
            'bece' => BeceGrade::all(),
            'wassce' => WassceGrade::all(),
            'university' => self::getAllUniversityGrades(),
            default => BeceGrade::all(),
        };
    }

    /**
     * Get all university grades.
     */
    private static function getAllUniversityGrades(): array
    {
        $grades = [];

        foreach (Grade::cases() as $grade) {
            $grades[] = [
                'grade' => $grade->value,
                'min_score' => $grade->getMinimumPercentage(),
                'max_score' => $grade->getMaximumPercentage(),
                'interpretation' => $grade->getDescription(),
            ];
        }

        return $grades;
    }

    /**
     * Get the grading system name for display.
     */
    public static function getSystemName(?User $user): string
    {
        $systemType = self::getSystemType($user);

        return match ($systemType) {
            'bece' => 'BECE Grading System',
            'wassce' => 'WASSCE Grading System',
            'university' => 'University Grading System',
            default => 'BECE Grading System',
        };
    }
}
