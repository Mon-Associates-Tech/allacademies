<?php

namespace App\ExaminationHub\Services;

/**
 * Pure MCQ grader — the single canonical grading function.
 *
 * This class contains no database calls, no external service dependencies,
 * and no side effects. Given the same input, it always produces the same
 * output. This guarantees grading parity between center and central servers.
 *
 * All mark calculations use integer hundredths to avoid float drift:
 * e.g., 1.5 marks = 150 hundredths, 0.25 marks = 25 hundredths.
 */
class McqGrader
{
    /**
     * Grade a single MCQ answer.
     *
     * @param array{
     *     correct_answer: string,
     *     marks: float|string,
     *     excluded_from_grading: bool,
     *     award_marks_on_exclusion: bool
     * } $questionData  Question data from the exam package.
     * @param string|null $response  Candidate's raw response (single letter).
     * @return array{
     *     is_correct: bool|null,
     *     points_earned: int,     // Integer hundredths
     *     points_earned_decimal: float,
     *     excluded: bool,
     *     feedback: string|null
     * }
     */
    public static function grade(array $questionData, ?string $response): array
    {
        $excluded = (bool) ($questionData['excluded_from_grading'] ?? false);
        $awardOnExclusion = (bool) ($questionData['award_marks_on_exclusion'] ?? false);
        $marksHundredths = self::toHundredths($questionData['marks'] ?? 0);

        // Handle excluded questions
        if ($excluded) {
            if ($awardOnExclusion) {
                return [
                    'is_correct' => null,
                    'points_earned' => $marksHundredths,
                    'points_earned_decimal' => self::fromHundredths($marksHundredths),
                    'excluded' => true,
                    'feedback' => 'Question excluded from grading; marks awarded.',
                ];
            }

            return [
                'is_correct' => null,
                'points_earned' => 0,
                'points_earned_decimal' => 0.0,
                'excluded' => true,
                'feedback' => 'Question excluded from grading.',
            ];
        }

        // No response provided
        if ($response === null || trim($response) === '') {
            return [
                'is_correct' => false,
                'points_earned' => 0,
                'points_earned_decimal' => 0.0,
                'excluded' => false,
                'feedback' => 'No answer provided.',
            ];
        }

        // Normalize: trim and uppercase for comparison
        $normalizedResponse = strtoupper(trim($response));
        $normalizedCorrect = strtoupper(trim($questionData['correct_answer'] ?? ''));

        $isCorrect = $normalizedResponse === $normalizedCorrect;

        $pointsEarned = $isCorrect ? $marksHundredths : 0;

        return [
            'is_correct' => $isCorrect,
            'points_earned' => $pointsEarned,
            'points_earned_decimal' => self::fromHundredths($pointsEarned),
            'excluded' => false,
            'feedback' => $isCorrect
                ? null
                : "The correct answer is: {$questionData['correct_answer']}",
        ];
    }

    /**
     * Grade a True/False answer.
     *
     * @param array{
     *     correct_answer: string,
     *     marks: float|string,
     *     excluded_from_grading: bool,
     *     award_marks_on_exclusion: bool
     * } $questionData
     * @param string|null $response
     * @return array{is_correct: bool|null, points_earned: int, points_earned_decimal: float, excluded: bool, feedback: string|null}
     */
    public static function gradeTrueFalse(array $questionData, ?string $response): array
    {
        $excluded = (bool) ($questionData['excluded_from_grading'] ?? false);
        $awardOnExclusion = (bool) ($questionData['award_marks_on_exclusion'] ?? false);
        $marksHundredths = self::toHundredths($questionData['marks'] ?? 0);

        if ($excluded) {
            if ($awardOnExclusion) {
                return [
                    'is_correct' => null,
                    'points_earned' => $marksHundredths,
                    'points_earned_decimal' => self::fromHundredths($marksHundredths),
                    'excluded' => true,
                    'feedback' => 'Question excluded from grading; marks awarded.',
                ];
            }

            return [
                'is_correct' => null,
                'points_earned' => 0,
                'points_earned_decimal' => 0.0,
                'excluded' => true,
                'feedback' => 'Question excluded from grading.',
            ];
        }

        if ($response === null || trim($response) === '') {
            return [
                'is_correct' => false,
                'points_earned' => 0,
                'points_earned_decimal' => 0.0,
                'excluded' => false,
                'feedback' => 'No answer provided.',
            ];
        }

        $normalizedResponse = strtolower(trim($response));
        $normalizedCorrect = strtolower(trim($questionData['correct_answer'] ?? ''));

        $trueValues = ['true', '1', 'yes', 't'];
        $falseValues = ['false', '0', 'no', 'f'];

        $responseIsTrue = in_array($normalizedResponse, $trueValues);
        $responseIsFalse = in_array($normalizedResponse, $falseValues);
        $correctIsTrue = in_array($normalizedCorrect, $trueValues);

        $isCorrect = ($responseIsTrue && $correctIsTrue)
            || ($responseIsFalse && ! $correctIsTrue);

        $pointsEarned = $isCorrect ? $marksHundredths : 0;

        return [
            'is_correct' => $isCorrect,
            'points_earned' => $pointsEarned,
            'points_earned_decimal' => self::fromHundredths($pointsEarned),
            'excluded' => false,
            'feedback' => $isCorrect
                ? null
                : "The correct answer is: {$questionData['correct_answer']}",
        ];
    }

    /**
     * Convert a decimal mark value to integer hundredths.
     * e.g., 1.5 → 150, 0.25 → 25, "1.00" → 100
     */
    public static function toHundredths(float|string $marks): int
    {
        $float = (float) $marks;
        // Use round to avoid float precision issues: 1.5 * 100 = 150.0 → 150
        return (int) round($float * 100);
    }

    /**
     * Convert integer hundredths back to a decimal.
     * e.g., 150 → 1.5, 25 → 0.25
     */
    public static function fromHundredths(int $hundredths): float
    {
        return round($hundredths / 100, 2);
    }

    /**
     * Calculate the total score from an array of graded results.
     * Returns the total in integer hundredths.
     *
     * @param array<int> $pointsArray  Array of points_earned values (in hundredths).
     */
    public static function totalScore(array $pointsArray): int
    {
        return array_sum($pointsArray);
    }

    /**
     * Calculate percentage from score and total marks (both in hundredths).
     * Returns percentage as integer hundredths (e.g., 85.50% = 8550).
     */
    public static function percentage(int $scoreHundredths, int $totalMarksHundredths): int
    {
        if ($totalMarksHundredths <= 0) {
            return 0;
        }

        // percentage = (score / total) * 100
        // In hundredths: (scoreHundredths / totalMarksHundredths) * 10000
        // Use integer arithmetic to avoid float drift
        return (int) round(($scoreHundredths / $totalMarksHundredths) * 10000);
    }

    /**
     * Convert percentage from integer hundredths to a float.
     * e.g., 8550 → 85.50
     */
    public static function percentageToFloat(int $percentageHundredths): float
    {
        return round($percentageHundredths / 100, 2);
    }
}
