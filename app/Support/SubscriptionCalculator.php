<?php

namespace App\Support;

use App\Enums\SubscriptionPackage;

class SubscriptionCalculator
{


    public static function subscriptionPrice(
        $package = SubscriptionPackage::INDIVIDUAL_FULL,
        string $level = AcademicGroupTag::BASIC,
        int $duration = AcademicDuration::QUARTER,
        int $numberOfSubjects = 1,
        int $numberOfStudents = 1
    ): float|int
    {
        switch ($package) {

            case SubscriptionPackage::INDIVIDUAL_FULL:
                if ($level === AcademicGroupTag::BASIC) {
                    switch ($duration) {
                        case AcademicDuration::YEAR:
                            return SubscriptionAmount::BASIC_SCHOOL_PER_STUDENT_PER_YEAR * $numberOfSubjects;
                        case AcademicDuration::HALF:
                            return SubscriptionAmount::BASIC_SCHOOL_PER_STUDENT_PER_HALF * $numberOfSubjects;
                        case AcademicDuration::QUARTER:
                            return SubscriptionAmount::BASIC_SCHOOL_PER_STUDENT_PER_QUARTER * $numberOfSubjects;
                    }
                } elseif ($level === AcademicGroupTag::SENIOR) {
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
                if ($level === AcademicGroupTag::BASIC) {
                    switch ($duration) {
                        case AcademicDuration::YEAR:
                            return SubscriptionAmount::BASIC_SCHOOL_INST_PER_STUDENT_PER_YEAR_ALL_SUBJECTS * $numberOfStudents;
                        case AcademicDuration::HALF:
                            return SubscriptionAmount::BASIC_SCHOOL_INST_PER_STUDENT_PER_HALF_ALL_SUBJECTS * $numberOfStudents;
                        case AcademicDuration::QUARTER:
                            return SubscriptionAmount::BASIC_SCHOOL_INST_PER_STUDENT_PER_QUARTER_ALL_SUBJECTS * $numberOfStudents;
                    }
                } elseif ($level === AcademicGroupTag::SENIOR) {
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
                if ($level === AcademicGroupTag::BASIC) {
                    return SubscriptionAmount::BASIC_SCHOOL_INST_PER_STUDENT_MID_TERM_ONCE * $numberOfStudents;
                } elseif ($level === AcademicGroupTag::SENIOR) {
                    return SubscriptionAmount::SENIOR_SCHOOL_INST_PER_STUDENT_MID_TERM_ONCE * $numberOfStudents;
                }
                break;

            case SubscriptionPackage::INSTITUTION_MOCK_EXAMS:
                if ($level === AcademicGroupTag::BASIC) {
                    return SubscriptionAmount::BASIC_SCHOOL_INST_PER_STUDENT_MOCK_EXAMS_ONCE * $numberOfStudents;
                } elseif ($level === AcademicGroupTag::SENIOR) {
                    return SubscriptionAmount::SENIOR_SCHOOL_INST_PER_STUDENT_MOCK_EXAMS_ONCE * $numberOfStudents;
                }
                break;
        }

        // Fallback default value
        return 10;
    }

    public static function unitSubscriptionPrice(
        $package = SubscriptionPackage::INDIVIDUAL_FULL,
        int $duration = AcademicDuration::QUARTER,
        string $tag = AcademicGroupTag::BASIC
    ): int
    {
        switch ($package) {
            case SubscriptionPackage::INDIVIDUAL_FULL:
                if ($tag === AcademicGroupTag::BASIC) {
                    return match ($duration) {
                        AcademicDuration::YEAR => SubscriptionAmount::BASIC_SCHOOL_PER_STUDENT_PER_YEAR,
                        AcademicDuration::HALF => SubscriptionAmount::BASIC_SCHOOL_PER_STUDENT_PER_HALF,
                        default => SubscriptionAmount::BASIC_SCHOOL_PER_STUDENT_PER_QUARTER
                    };
                } elseif ($tag === AcademicGroupTag::SENIOR) {
                    return match ($duration) {
                        AcademicDuration::YEAR => SubscriptionAmount::SENIOR_SCHOOL_PER_STUDENT_PER_YEAR,
                        AcademicDuration::HALF => SubscriptionAmount::SENIOR_SCHOOL_PER_STUDENT_PER_HALF,
                        default => SubscriptionAmount::SENIOR_SCHOOL_PER_STUDENT_PER_QUARTER
                    };
                }
                break;

            case SubscriptionPackage::INSTITUTION_FULL:
                if ($tag === AcademicGroupTag::BASIC) {
                    return match ($duration) {
                        AcademicDuration::YEAR => SubscriptionAmount::BASIC_SCHOOL_INST_PER_STUDENT_PER_YEAR_ALL_SUBJECTS,
                        AcademicDuration::HALF => SubscriptionAmount::BASIC_SCHOOL_INST_PER_STUDENT_PER_HALF_ALL_SUBJECTS,
                        default => SubscriptionAmount::BASIC_SCHOOL_INST_PER_STUDENT_PER_QUARTER_ALL_SUBJECTS
                    };
                } elseif ($tag === AcademicGroupTag::SENIOR) {
                    return match ($duration) {
                        AcademicDuration::YEAR => SubscriptionAmount::SENIOR_SCHOOL_INST_PER_STUDENT_PER_YEAR_ALL_SUBJECTS,
                        AcademicDuration::HALF => SubscriptionAmount::SENIOR_SCHOOL_INST_PER_STUDENT_PER_HALF_ALL_SUBJECTS,
                        default => SubscriptionAmount::SENIOR_SCHOOL_INST_PER_STUDENT_PER_QUARTER_ALL_SUBJECTS
                    };
                }
                break;

            case SubscriptionPackage::INSTITUTION_MID_TERM:
                return $tag === AcademicGroupTag::BASIC
                    ? SubscriptionAmount::BASIC_SCHOOL_INST_PER_STUDENT_MID_TERM_ONCE
                    : SubscriptionAmount::SENIOR_SCHOOL_INST_PER_STUDENT_MID_TERM_ONCE;

            case SubscriptionPackage::INSTITUTION_MOCK_EXAMS:
                return $tag === AcademicGroupTag::BASIC
                    ? SubscriptionAmount::BASIC_SCHOOL_INST_PER_STUDENT_MOCK_EXAMS_ONCE
                    : SubscriptionAmount::SENIOR_SCHOOL_INST_PER_STUDENT_MOCK_EXAMS_ONCE;
        }

        return 0;
    }


}
