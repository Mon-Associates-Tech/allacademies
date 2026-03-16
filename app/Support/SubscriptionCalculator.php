<?php

namespace App\Support;

use App\Enums\SubscriptionPackage;
use InvalidArgumentException;

class SubscriptionCalculator
{
    public static function unitSubscriptionPrice(
        SubscriptionPackage $package = SubscriptionPackage::INDIVIDUAL_FULL,
        int $duration = AcademicDuration::QUARTER,
        string $tag = AcademicGroupTag::BASIC
    ): int {
        switch ($package) {
            case SubscriptionPackage::INDIVIDUAL_FULL:
                if ($tag === AcademicGroupTag::BASIC) {
                    return match ($duration) {
                        AcademicDuration::YEAR => SubscriptionAmount::basicSchoolPerStudentPerYear(),
                        AcademicDuration::HALF => SubscriptionAmount::basicSchoolPerStudentPerHalf(),
                        AcademicDuration::ONE_OFF => SubscriptionAmount::basicSchoolOneOff(),
                        default => SubscriptionAmount::basicSchoolPerStudentPerQuarter()
                    };
                } elseif ($tag === AcademicGroupTag::SENIOR) {
                    return match ($duration) {
                        AcademicDuration::YEAR => SubscriptionAmount::seniorSchoolPerStudentPerYear(),
                        AcademicDuration::HALF => SubscriptionAmount::seniorSchoolPerStudentPerHalf(),
                        AcademicDuration::ONE_OFF => SubscriptionAmount::seniorSchoolOneOff(),
                        default => SubscriptionAmount::seniorSchoolPerStudentPerQuarter()
                    };
                } elseif ($tag === AcademicGroupTag::UNIVERSITY) {
                    return match ($duration) {
                        AcademicDuration::YEAR => SubscriptionAmount::universitySchoolPerStudentPerYear(),
                        AcademicDuration::HALF => SubscriptionAmount::universitySchoolPerStudentPerHalf(),
                        AcademicDuration::ONE_OFF => SubscriptionAmount::universityOneOff(),
                        default => SubscriptionAmount::universitySchoolPerStudentPerQuarter()
                    };
                }
                break;
            case SubscriptionPackage::INSTITUTION_FULL:
                if ($tag === AcademicGroupTag::BASIC) {
                    return match ($duration) {
                        AcademicDuration::YEAR => SubscriptionAmount::basicSchoolInstPerStudentPerYearAllSubjects(),
                        AcademicDuration::HALF => SubscriptionAmount::basicSchoolInstPerStudentPerHalfAllSubjects(),
                        AcademicDuration::ONE_OFF => SubscriptionAmount::basicSchoolOneOff(),
                        default => SubscriptionAmount::basicSchoolInstPerStudentPerQuarterAllSubjects()
                    };
                } elseif ($tag === AcademicGroupTag::SENIOR) {
                    return match ($duration) {
                        AcademicDuration::YEAR => SubscriptionAmount::seniorSchoolInstPerStudentPerYearAllSubjects(),
                        AcademicDuration::HALF => SubscriptionAmount::seniorSchoolInstPerStudentPerHalfAllSubjects(),
                        AcademicDuration::ONE_OFF => SubscriptionAmount::seniorSchoolOneOff(),
                        default => SubscriptionAmount::seniorSchoolInstPerStudentPerQuarterAllSubjects()
                    };
                } elseif ($tag === AcademicGroupTag::UNIVERSITY) {
                    return match ($duration) {
                        AcademicDuration::YEAR => SubscriptionAmount::universitySchoolInstPerStudentPerYearAllSubjects(),
                        AcademicDuration::HALF => SubscriptionAmount::universitySchoolInstPerStudentPerHalfAllSubjects(),
                        AcademicDuration::ONE_OFF => SubscriptionAmount::universityOneOff(),
                        default => SubscriptionAmount::universitySchoolInstPerStudentPerQuarterAllSubjects()
                    };
                }
                break;
            case SubscriptionPackage::INSTITUTION_MID_TERM:
                if ($tag === AcademicGroupTag::BASIC) {
                    return SubscriptionAmount::basicSchoolInstPerStudentMidTermOnce();
                } elseif ($tag === AcademicGroupTag::SENIOR) {
                    return SubscriptionAmount::seniorSchoolInstPerStudentMidTermOnce();
                } elseif ($tag === AcademicGroupTag::UNIVERSITY) {
                    return SubscriptionAmount::universitySchoolInstPerStudentMidTermOnce();
                }
                break;
            case SubscriptionPackage::INSTITUTION_MOCK_EXAMS:
                if ($tag === AcademicGroupTag::BASIC) {
                    return SubscriptionAmount::basicSchoolInstPerStudentMockExamsOnce();
                } elseif ($tag === AcademicGroupTag::SENIOR) {
                    return SubscriptionAmount::seniorSchoolInstPerStudentMockExamsOnce();
                } elseif ($tag === AcademicGroupTag::UNIVERSITY) {
                    return SubscriptionAmount::universitySchoolInstPerStudentMockExamsOnce();
                }
                break;
            default:
                throw new InvalidArgumentException('Invalid subscription package: '.$package->value);
        }

        throw new InvalidArgumentException('Unsupported combination of package: '.$package->value.', tag: '.$tag);
    }
}
