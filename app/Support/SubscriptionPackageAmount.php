<?php

namespace App\Support;

use App\Enums\SubscriptionPackage;
use Brick\Money\Money;
use InvalidArgumentException;

class SubscriptionPackageAmount
{


    public static function subscriptionPrice(
        $package = SubscriptionPackage::INDIVIDUAL_FULL,
        string $level = AcademicLevel::BASIC,
        string $duration = AcademicDuration::QUARTER,
        int $numberOfSubjects = 1,
        int $numberOfStudents = 1
    )
    {
        switch ($package) {

            case SubscriptionPackage::INDIVIDUAL_FULL:
                if ($level === AcademicLevel::BASIC) {
                    switch ($duration) {
                        case AcademicDuration::YEAR:
                            return SubscriptionAmount::BASIC_SCHOOL_PER_STUDENT_PER_YEAR * $numberOfSubjects;
                        case AcademicDuration::HALF:
                            return SubscriptionAmount::BASIC_SCHOOL_PER_STUDENT_PER_HALF * $numberOfSubjects;
                        case AcademicDuration::QUARTER:
                            return SubscriptionAmount::BASIC_SCHOOL_PER_STUDENT_PER_QUARTER * $numberOfSubjects;
                    }
                } elseif ($level === AcademicLevel::SENIOR) {
                    switch ($duration) {
                        case AcademicDuration::YEAR:
                            return SubscriptionAmount::SENIOR_SCHOOL_PER_STUDENT_PER_YEAR * $numberOfSubjects;
                        case AcademicDuration::HALF:
                            return SubscriptionAmount::SENIOR_SCHOOL_PER_STUDENT_PER_HALF * $numberOfSubjects;
                        case AcademicDuration::QUARTER:
                            return SubscriptionAmount::SENIOR_SCHOOL_PER_STUDENT_PER_QUARTER * $numberOfSubjects;
                    }
                }
                break;

            case SubscriptionPackage::INSTITUTION_FULL:
                if ($level === AcademicLevel::BASIC) {
                    switch ($duration) {
                        case AcademicDuration::YEAR:
                            return SubscriptionAmount::BASIC_SCHOOL_INST_PER_STUDENT_PER_YEAR_ALL_SUBJECTS * $numberOfStudents;
                        case AcademicDuration::HALF:
                            return SubscriptionAmount::BASIC_SCHOOL_INST_PER_STUDENT_PER_HALF_ALL_SUBJECTS * $numberOfStudents;
                        case AcademicDuration::QUARTER:
                            return SubscriptionAmount::BASIC_SCHOOL_INST_PER_STUDENT_PER_QUARTER_ALL_SUBJECTS * $numberOfStudents;
                    }
                } elseif ($level === AcademicLevel::SENIOR) {
                    switch ($duration) {
                        case AcademicDuration::YEAR:
                            return SubscriptionAmount::SENIOR_SCHOOL_INST_PER_STUDENT_PER_YEAR_ALL_SUBJECTS * $numberOfStudents;
                        case AcademicDuration::HALF:
                            return SubscriptionAmount::SENIOR_SCHOOL_INST_PER_STUDENT_PER_HALF_ALL_SUBJECTS * $numberOfStudents;
                        case AcademicDuration::QUARTER:
                            return SubscriptionAmount::SENIOR_SCHOOL_INST_PER_STUDENT_PER_QUARTER_ALL_SUBJECTS * $numberOfStudents;
                    }
                }
                break;

            case SubscriptionPackage::INSTITUTION_MID_TERM:
                if ($level === AcademicLevel::BASIC) {
                    return SubscriptionAmount::BASIC_SCHOOL_INST_PER_STUDENT_MID_TERM_ONCE * $numberOfStudents;
                } elseif ($level === AcademicLevel::SENIOR) {
                    return SubscriptionAmount::SENIOR_SCHOOL_INST_PER_STUDENT_MID_TERM_ONCE * $numberOfStudents;
                }
                break;

            case SubscriptionPackage::INSTITUTION_MOCK_EXAMS:
                if ($level === AcademicLevel::BASIC) {
                    return SubscriptionAmount::BASIC_SCHOOL_INST_PER_STUDENT_MOCK_EXAMS_ONCE * $numberOfStudents;
                } elseif ($level === AcademicLevel::SENIOR) {
                    return SubscriptionAmount::SENIOR_SCHOOL_INST_PER_STUDENT_MOCK_EXAMS_ONCE * $numberOfStudents;
                }
                break;
        }

        // Fallback default value
        return 10;
    }

    public static function unitSubscriptionPrice(
        $package = SubscriptionPackage::INDIVIDUAL_FULL,
        string $level = AcademicLevel::BASIC,
        string $duration = AcademicDuration::QUARTER
    )
    {
        switch ($package) {
            case SubscriptionPackage::INDIVIDUAL_FULL:
                if ($level === AcademicLevel::BASIC) {
                    return match ($duration) {
                        AcademicDuration::YEAR => SubscriptionAmount::BASIC_SCHOOL_PER_STUDENT_PER_YEAR,
                        AcademicDuration::HALF => SubscriptionAmount::BASIC_SCHOOL_PER_STUDENT_PER_HALF,
                        AcademicDuration::QUARTER => SubscriptionAmount::BASIC_SCHOOL_PER_STUDENT_PER_QUARTER,
                        default => 0
                    };
                } elseif ($level === AcademicLevel::SENIOR) {
                    return match ($duration) {
                        AcademicDuration::YEAR => SubscriptionAmount::SENIOR_SCHOOL_PER_STUDENT_PER_YEAR,
                        AcademicDuration::HALF => SubscriptionAmount::SENIOR_SCHOOL_PER_STUDENT_PER_HALF,
                        AcademicDuration::QUARTER => SubscriptionAmount::SENIOR_SCHOOL_PER_STUDENT_PER_QUARTER,
                        default => 0
                    };
                }
                break;

            case SubscriptionPackage::INSTITUTION_FULL:
                if ($level === AcademicLevel::BASIC) {
                    return match ($duration) {
                        AcademicDuration::YEAR => SubscriptionAmount::BASIC_SCHOOL_INST_PER_STUDENT_PER_YEAR_ALL_SUBJECTS,
                        AcademicDuration::HALF => SubscriptionAmount::BASIC_SCHOOL_INST_PER_STUDENT_PER_HALF_ALL_SUBJECTS,
                        AcademicDuration::QUARTER => SubscriptionAmount::BASIC_SCHOOL_INST_PER_STUDENT_PER_QUARTER_ALL_SUBJECTS,
                        default => 0
                    };
                } elseif ($level === AcademicLevel::SENIOR) {
                    return match ($duration) {
                        AcademicDuration::YEAR => SubscriptionAmount::SENIOR_SCHOOL_INST_PER_STUDENT_PER_YEAR_ALL_SUBJECTS,
                        AcademicDuration::HALF => SubscriptionAmount::SENIOR_SCHOOL_INST_PER_STUDENT_PER_HALF_ALL_SUBJECTS,
                        AcademicDuration::QUARTER => SubscriptionAmount::SENIOR_SCHOOL_INST_PER_STUDENT_PER_QUARTER_ALL_SUBJECTS,
                        default => 0
                    };
                }
                break;

            case SubscriptionPackage::INSTITUTION_MID_TERM:
                return $level === AcademicLevel::BASIC
                    ? SubscriptionAmount::BASIC_SCHOOL_INST_PER_STUDENT_MID_TERM_ONCE
                    : SubscriptionAmount::SENIOR_SCHOOL_INST_PER_STUDENT_MID_TERM_ONCE;

            case SubscriptionPackage::INSTITUTION_MOCK_EXAMS:
                return $level === AcademicLevel::BASIC
                    ? SubscriptionAmount::BASIC_SCHOOL_INST_PER_STUDENT_MOCK_EXAMS_ONCE
                    : SubscriptionAmount::SENIOR_SCHOOL_INST_PER_STUDENT_MOCK_EXAMS_ONCE;
        }

        return 0;
    }


}
