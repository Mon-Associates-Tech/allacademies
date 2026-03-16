<?php

declare(strict_types=1);

namespace App\Support;

use App\Models\PricingSetting;

final class SubscriptionAmount
{
    private const DEFAULTS = [
        'basic' => [
            'quarter' => 20,
            'half' => 30,
            'year' => 45,
            'one_off' => 10,
        ],
        'senior' => [
            'quarter' => 35,
            'half' => 50,
            'year' => 75,
            'one_off' => 15,
        ],
        'institution' => [
            'basic' => [
                'quarter' => 50,
                'half' => 75,
                'year' => 100,
                'mid_term' => 5,
                'mock_exams' => 10,
            ],
            'senior' => [
                'quarter' => 60,
                'half' => 90,
                'year' => 150,
                'mid_term' => 10,
                'mock_exams' => 20,
            ],
            'university' => [
                'quarter' => 35,
                'half' => 35,
                'year' => 35,
                'mid_term' => 0,
                'mock_exams' => 0,
            ],
        ],
        'university' => [
            'quarter' => 35,
            'half' => 35,
            'year' => 35,
            'one_off' => 20,
        ],
    ];

    public static function basicSchoolPerStudentPerYear(): int
    {
        return self::setting(
            'basic.individual.year',
            self::DEFAULTS['basic']['year'],
            'plans.basic.options.annual.price'
        );
    }

    public static function basicSchoolPerStudentPerHalf(): int
    {
        return self::setting(
            'basic.individual.half',
            self::DEFAULTS['basic']['half'],
            'plans.basic.options.biannual.price'
        );
    }

    public static function basicSchoolPerStudentPerQuarter(): int
    {
        return self::setting(
            'basic.individual.quarter',
            self::DEFAULTS['basic']['quarter'],
            'plans.basic.options.quarterly.price'
        );
    }

    public static function seniorSchoolPerStudentPerYear(): int
    {
        return self::setting(
            'senior.individual.year',
            self::DEFAULTS['senior']['year'],
            'plans.secondary.options.annual.price'
        );
    }

    public static function seniorSchoolPerStudentPerHalf(): int
    {
        return self::setting(
            'senior.individual.half',
            self::DEFAULTS['senior']['half'],
            'plans.secondary.options.biannual.price'
        );
    }

    public static function seniorSchoolPerStudentPerQuarter(): int
    {
        return self::setting(
            'senior.individual.quarter',
            self::DEFAULTS['senior']['quarter'],
            'plans.secondary.options.quarterly.price'
        );
    }

    public static function basicSchoolInstPerStudentPerYearAllSubjects(): int
    {
        return self::setting(
            'basic.institution.year',
            self::DEFAULTS['institution']['basic']['year'],
            'plans.institutional.options.basic_annual.price'
        );
    }

    public static function basicSchoolInstPerStudentPerHalfAllSubjects(): int
    {
        return self::setting(
            'basic.institution.half',
            self::DEFAULTS['institution']['basic']['half']
        );
    }

    public static function basicSchoolInstPerStudentPerQuarterAllSubjects(): int
    {
        return self::setting(
            'basic.institution.quarter',
            self::DEFAULTS['institution']['basic']['quarter'],
            'plans.institutional.options.quarterly.tiers.basic.price'
        );
    }

    public static function basicSchoolInstPerStudentMidTermOnce(): int
    {
        return self::setting(
            'basic.institution.mid_term',
            self::DEFAULTS['institution']['basic']['mid_term']
        );
    }

    public static function basicSchoolInstPerStudentMockExamsOnce(): int
    {
        return self::setting(
            'basic.institution.mock_exams',
            self::DEFAULTS['institution']['basic']['mock_exams']
        );
    }

    public static function seniorSchoolInstPerStudentPerYearAllSubjects(): int
    {
        return self::setting(
            'senior.institution.year',
            self::DEFAULTS['institution']['senior']['year'],
            'plans.institutional.options.secondary_annual.price'
        );
    }

    public static function seniorSchoolInstPerStudentPerHalfAllSubjects(): int
    {
        return self::setting(
            'senior.institution.half',
            self::DEFAULTS['institution']['senior']['half']
        );
    }

    public static function seniorSchoolInstPerStudentPerQuarterAllSubjects(): int
    {
        return self::setting(
            'senior.institution.quarter',
            self::DEFAULTS['institution']['senior']['quarter'],
            'plans.institutional.options.quarterly.tiers.secondary.price'
        );
    }

    public static function seniorSchoolInstPerStudentMidTermOnce(): int
    {
        return self::setting(
            'senior.institution.mid_term',
            self::DEFAULTS['institution']['senior']['mid_term']
        );
    }

    public static function seniorSchoolInstPerStudentMockExamsOnce(): int
    {
        return self::setting(
            'senior.institution.mock_exams',
            self::DEFAULTS['institution']['senior']['mock_exams']
        );
    }

    public static function basicSchoolOneOff(): int
    {
        return self::setting(
            'basic.individual.one_off',
            self::DEFAULTS['basic']['one_off']
        );
    }

    public static function seniorSchoolOneOff(): int
    {
        return self::setting(
            'senior.individual.one_off',
            self::DEFAULTS['senior']['one_off']
        );
    }

    public static function universityOneOff(): int
    {
        return self::setting(
            'university.individual.one_off',
            self::DEFAULTS['university']['one_off']
        );
    }

    public static function universitySchoolPerStudentPerYear(): int
    {
        return self::setting(
            'university.individual.year',
            self::DEFAULTS['university']['year']
        );
    }

    public static function universitySchoolPerStudentPerHalf(): int
    {
        return self::setting(
            'university.individual.half',
            self::DEFAULTS['university']['half']
        );
    }

    public static function universitySchoolPerStudentPerQuarter(): int
    {
        return self::setting(
            'university.individual.quarter',
            self::DEFAULTS['university']['quarter']
        );
    }

    public static function universitySchoolInstPerStudentPerYearAllSubjects(): int
    {
        return self::setting(
            'university.institution.year',
            self::DEFAULTS['institution']['university']['year']
        );
    }

    public static function universitySchoolInstPerStudentPerHalfAllSubjects(): int
    {
        return self::setting(
            'university.institution.half',
            self::DEFAULTS['institution']['university']['half']
        );
    }

    public static function universitySchoolInstPerStudentPerQuarterAllSubjects(): int
    {
        return self::setting(
            'university.institution.quarter',
            self::DEFAULTS['institution']['university']['quarter']
        );
    }

    public static function universitySchoolInstPerStudentMidTermOnce(): int
    {
        return self::setting(
            'university.institution.mid_term',
            self::DEFAULTS['institution']['university']['mid_term']
        );
    }

    public static function universitySchoolInstPerStudentMockExamsOnce(): int
    {
        return self::setting(
            'university.institution.mock_exams',
            self::DEFAULTS['institution']['university']['mock_exams']
        );
    }

    private static function setting(string $key, int $fallback, ?string $configPath = null): int
    {
        $value = PricingSetting::getValue($key);

        if ($value === null && $configPath) {
            $value = data_get(config('branding_pricing'), $configPath);
        }

        if (is_numeric($value)) {
            return (int) round((float) $value);
        }

        return $fallback;
    }
}
