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
                        AcademicDuration::YEAR => SubscriptionAmount::BASIC_SCHOOL_PER_STUDENT_PER_YEAR,
                        AcademicDuration::HALF => SubscriptionAmount::BASIC_SCHOOL_PER_STUDENT_PER_HALF,
                        AcademicDuration::ONE_OFF => SubscriptionAmount::BASIC_SCHOOL_ONE_OFF,
                        default => SubscriptionAmount::BASIC_SCHOOL_PER_STUDENT_PER_QUARTER
                    };
                } elseif ($tag === AcademicGroupTag::SENIOR) {
                    return match ($duration) {
                        AcademicDuration::YEAR => SubscriptionAmount::SENIOR_SCHOOL_PER_STUDENT_PER_YEAR,
                        AcademicDuration::HALF => SubscriptionAmount::SENIOR_SCHOOL_PER_STUDENT_PER_HALF,
                        AcademicDuration::ONE_OFF => SubscriptionAmount::SENIOR_SCHOOL_ONE_OFF,
                        default => SubscriptionAmount::SENIOR_SCHOOL_PER_STUDENT_PER_QUARTER
                    };
                } elseif ($tag === AcademicGroupTag::UNIVERSITY) {
                    return match ($duration) {
                        AcademicDuration::YEAR => SubscriptionAmount::UNIVERSITY_SCHOOL_PER_STUDENT_PER_YEAR,
                        AcademicDuration::HALF => SubscriptionAmount::UNIVERSITY_SCHOOL_PER_STUDENT_PER_HALF,
                        AcademicDuration::ONE_OFF => SubscriptionAmount::UNIVERSITY_ONE_OFF,
                        default => SubscriptionAmount::UNIVERSITY_SCHOOL_PER_STUDENT_PER_QUARTER
                    };
                }
                break;
            case SubscriptionPackage::INSTITUTION_FULL:
                if ($tag === AcademicGroupTag::BASIC) {
                    return match ($duration) {
                        AcademicDuration::YEAR => SubscriptionAmount::BASIC_SCHOOL_INST_PER_STUDENT_PER_YEAR_ALL_SUBJECTS,
                        AcademicDuration::HALF => SubscriptionAmount::BASIC_SCHOOL_INST_PER_STUDENT_PER_HALF_ALL_SUBJECTS,
                        AcademicDuration::ONE_OFF => SubscriptionAmount::BASIC_SCHOOL_ONE_OFF,
                        default => SubscriptionAmount::BASIC_SCHOOL_INST_PER_STUDENT_PER_QUARTER_ALL_SUBJECTS
                    };
                } elseif ($tag === AcademicGroupTag::SENIOR) {
                    return match ($duration) {
                        AcademicDuration::YEAR => SubscriptionAmount::SENIOR_SCHOOL_INST_PER_STUDENT_PER_YEAR_ALL_SUBJECTS,
                        AcademicDuration::HALF => SubscriptionAmount::SENIOR_SCHOOL_INST_PER_STUDENT_PER_HALF_ALL_SUBJECTS,
                        AcademicDuration::ONE_OFF => SubscriptionAmount::SENIOR_SCHOOL_ONE_OFF,
                        default => SubscriptionAmount::SENIOR_SCHOOL_INST_PER_STUDENT_PER_QUARTER_ALL_SUBJECTS
                    };
                } elseif ($tag === AcademicGroupTag::UNIVERSITY) {
                    return match ($duration) {
                        AcademicDuration::YEAR => SubscriptionAmount::UNIVERSITY_SCHOOL_INST_PER_STUDENT_PER_YEAR_ALL_SUBJECTS,
                        AcademicDuration::HALF => SubscriptionAmount::UNIVERSITY_SCHOOL_INST_PER_STUDENT_PER_HALF_ALL_SUBJECTS,
                        AcademicDuration::ONE_OFF => SubscriptionAmount::UNIVERSITY_ONE_OFF,
                        default => SubscriptionAmount::UNIVERSITY_SCHOOL_INST_PER_STUDENT_PER_QUARTER_ALL_SUBJECTS
                    };
                }
                break;
            case SubscriptionPackage::INSTITUTION_MID_TERM:
                if ($tag === AcademicGroupTag::BASIC) {
                    return SubscriptionAmount::BASIC_SCHOOL_INST_PER_STUDENT_MID_TERM_ONCE;
                } elseif ($tag === AcademicGroupTag::SENIOR) {
                    return SubscriptionAmount::SENIOR_SCHOOL_INST_PER_STUDENT_MID_TERM_ONCE;
                } elseif ($tag === AcademicGroupTag::UNIVERSITY) {
                    return SubscriptionAmount::UNIVERSITY_SCHOOL_INST_PER_STUDENT_MID_TERM_ONCE;
                }
                break;
            case SubscriptionPackage::INSTITUTION_MOCK_EXAMS:
                if ($tag === AcademicGroupTag::BASIC) {
                    return SubscriptionAmount::BASIC_SCHOOL_INST_PER_STUDENT_MOCK_EXAMS_ONCE;
                } elseif ($tag === AcademicGroupTag::SENIOR) {
                    return SubscriptionAmount::SENIOR_SCHOOL_INST_PER_STUDENT_MOCK_EXAMS_ONCE;
                } elseif ($tag === AcademicGroupTag::UNIVERSITY) {
                    return SubscriptionAmount::UNIVERSITY_SCHOOL_INST_PER_STUDENT_MOCK_EXAMS_ONCE;
                }
                break;
            default:
                throw new InvalidArgumentException('Invalid subscription package: '.$package->value);
        }

        throw new InvalidArgumentException('Unsupported combination of package: '.$package->value.', tag: '.$tag);
    }
}
