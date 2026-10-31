<?php

namespace Tests\Feature\Center;

use App\ExaminationHub\Services\McqGrader;
use PHPUnit\Framework\TestCase;

class McqGraderTest extends TestCase
{
    // ─── Basic MCQ grading ────────────────────────────────────────────────────

    public function test_correct_answer_earns_full_marks(): void
    {
        $questionData = [
            'correct_answer' => 'B',
            'marks' => 1.0,
            'excluded_from_grading' => false,
            'award_marks_on_exclusion' => false,
        ];

        $result = McqGrader::grade($questionData, 'B');

        $this->assertTrue($result['is_correct']);
        $this->assertEquals(100, $result['points_earned']); // 1.0 * 100 hundredths
        $this->assertEquals(1.0, $result['points_earned_decimal']);
        $this->assertFalse($result['excluded']);
        $this->assertNull($result['feedback']);
    }

    public function test_incorrect_answer_earns_zero(): void
    {
        $questionData = [
            'correct_answer' => 'A',
            'marks' => 2.5,
            'excluded_from_grading' => false,
            'award_marks_on_exclusion' => false,
        ];

        $result = McqGrader::grade($questionData, 'C');

        $this->assertFalse($result['is_correct']);
        $this->assertEquals(0, $result['points_earned']);
        $this->assertEquals(0.0, $result['points_earned_decimal']);
        $this->assertNotNull($result['feedback']);
    }

    public function test_normalization_trims_and_uppercases(): void
    {
        $questionData = [
            'correct_answer' => '  a  ',
            'marks' => 1.0,
            'excluded_from_grading' => false,
            'award_marks_on_exclusion' => false,
        ];

        $result = McqGrader::grade($questionData, '  A  ');

        $this->assertTrue($result['is_correct']);
        $this->assertEquals(100, $result['points_earned']);
    }

    public function test_no_answer_earns_zero(): void
    {
        $questionData = [
            'correct_answer' => 'D',
            'marks' => 1.0,
            'excluded_from_grading' => false,
            'award_marks_on_exclusion' => false,
        ];

        $result = McqGrader::grade($questionData, null);

        $this->assertFalse($result['is_correct']);
        $this->assertEquals(0, $result['points_earned']);
    }

    public function test_empty_string_answer_earns_zero(): void
    {
        $questionData = [
            'correct_answer' => 'D',
            'marks' => 1.0,
            'excluded_from_grading' => false,
            'award_marks_on_exclusion' => false,
        ];

        $result = McqGrader::grade($questionData, '');

        $this->assertFalse($result['is_correct']);
        $this->assertEquals(0, $result['points_earned']);
    }

    // ─── Excluded questions ──────────────────────────────────────────────────

    public function test_excluded_question_no_award(): void
    {
        $questionData = [
            'correct_answer' => 'A',
            'marks' => 1.0,
            'excluded_from_grading' => true,
            'award_marks_on_exclusion' => false,
        ];

        $result = McqGrader::grade($questionData, 'A');

        $this->assertTrue($result['excluded']);
        $this->assertEquals(0, $result['points_earned']);
        $this->assertNull($result['is_correct']);
    }

    public function test_excluded_question_with_award(): void
    {
        $questionData = [
            'correct_answer' => 'A',
            'marks' => 1.5,
            'excluded_from_grading' => true,
            'award_marks_on_exclusion' => true,
        ];

        $result = McqGrader::grade($questionData, 'B');

        $this->assertTrue($result['excluded']);
        $this->assertEquals(150, $result['points_earned']); // 1.5 * 100
        $this->assertEquals(1.5, $result['points_earned_decimal']);
        $this->assertNull($result['is_correct']);
    }

    // ─── True/False grading ──────────────────────────────────────────────────

    public function test_true_false_correct(): void
    {
        $questionData = [
            'correct_answer' => 'True',
            'marks' => 1.0,
            'excluded_from_grading' => false,
            'award_marks_on_exclusion' => false,
        ];

        $result = McqGrader::gradeTrueFalse($questionData, 'true');

        $this->assertTrue($result['is_correct']);
        $this->assertEquals(100, $result['points_earned']);
    }

    public function test_true_false_incorrect(): void
    {
        $questionData = [
            'correct_answer' => 'False',
            'marks' => 1.0,
            'excluded_from_grading' => false,
            'award_marks_on_exclusion' => false,
        ];

        $result = McqGrader::gradeTrueFalse($questionData, 'true');

        $this->assertFalse($result['is_correct']);
        $this->assertEquals(0, $result['points_earned']);
    }

    public function test_true_false_aliases(): void
    {
        $questionData = [
            'correct_answer' => '1',
            'marks' => 1.0,
            'excluded_from_grading' => false,
            'award_marks_on_exclusion' => false,
        ];

        // 'yes' should match '1' (both are true values)
        $result = McqGrader::gradeTrueFalse($questionData, 'yes');
        $this->assertTrue($result['is_correct']);

        // 't' should match '1' (both are true values)
        $result = McqGrader::gradeTrueFalse($questionData, 't');
        $this->assertTrue($result['is_correct']);
    }

    // ─── Integer hundredths conversion ────────────────────────────────────────

    public function test_to_hundredths(): void
    {
        $this->assertEquals(100, McqGrader::toHundredths(1.0));
        $this->assertEquals(150, McqGrader::toHundredths(1.5));
        $this->assertEquals(25, McqGrader::toHundredths(0.25));
        $this->assertEquals(0, McqGrader::toHundredths(0));
        $this->assertEquals(100, McqGrader::toHundredths('1.00'));
        $this->assertEquals(333, McqGrader::toHundredths(3.33));
    }

    public function test_from_hundredths(): void
    {
        $this->assertEquals(1.0, McqGrader::fromHundredths(100));
        $this->assertEquals(1.5, McqGrader::fromHundredths(150));
        $this->assertEquals(0.25, McqGrader::fromHundredths(25));
        $this->assertEquals(0.0, McqGrader::fromHundredths(0));
    }

    // ─── Percentage calculation ───────────────────────────────────────────────

    public function test_percentage(): void
    {
        // 75 out of 100 = 75%
        $this->assertEquals(7500, McqGrader::percentage(7500, 10000));

        // 0 out of 100 = 0%
        $this->assertEquals(0, McqGrader::percentage(0, 10000));

        // 100 out of 100 = 100%
        $this->assertEquals(10000, McqGrader::percentage(10000, 10000));

        // Division by zero guard
        $this->assertEquals(0, McqGrader::percentage(50, 0));
    }

    public function test_percentage_to_float(): void
    {
        $this->assertEquals(75.0, McqGrader::percentageToFloat(7500));
        $this->assertEquals(85.5, McqGrader::percentageToFloat(8550));
        $this->assertEquals(0.0, McqGrader::percentageToFloat(0));
    }

    // ─── Total score ──────────────────────────────────────────────────────────

    public function test_total_score(): void
    {
        $this->assertEquals(350, McqGrader::totalScore([100, 150, 100]));
        $this->assertEquals(0, McqGrader::totalScore([]));
    }

    // ─── Parity: same input always produces same output ───────────────────────

    public function test_parity_same_input_same_output(): void
    {
        $questionData = [
            'correct_answer' => 'C',
            'marks' => 2.5,
            'excluded_from_grading' => false,
            'award_marks_on_exclusion' => false,
        ];

        $result1 = McqGrader::grade($questionData, 'C');
        $result2 = McqGrader::grade($questionData, 'C');

        $this->assertEquals($result1, $result2);
    }
}
