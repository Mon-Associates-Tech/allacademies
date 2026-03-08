<?php

namespace Tests\Unit;

use App\Models\AcademicGroup;
use App\Models\AcademicLevel;
use App\Models\Student;
use App\Models\User;
use App\Support\BeceGrade;
use App\Support\GradingSystemResolver;
use App\Support\WassceGrade;
use Tests\TestCase;

class GradingSystemResolverTest extends TestCase
{
    public function test_returns_bece_system_for_null_user(): void
    {
        $systemType = GradingSystemResolver::getSystemType(null);

        $this->assertEquals('bece', $systemType);
    }

    public function test_returns_bece_system_for_user_without_academic_level(): void
    {
        $user = User::factory()->create();

        $systemType = GradingSystemResolver::getSystemType($user);

        $this->assertEquals('bece', $systemType);
    }

    public function test_returns_bece_system_for_basic_academic_group(): void
    {
        $academicGroup = AcademicGroup::factory()->create(['tag' => 'basic']);
        $academicLevel = AcademicLevel::factory()->create(['academic_group_id' => $academicGroup->id]);
        $user = User::factory()->create(['preferred_academic_level_id' => $academicLevel->id]);

        $systemType = GradingSystemResolver::getSystemType($user);

        $this->assertEquals('bece', $systemType);
    }

    public function test_returns_wassce_system_for_senior_academic_group(): void
    {
        $academicGroup = AcademicGroup::factory()->create(['tag' => 'senior']);
        $academicLevel = AcademicLevel::factory()->create(['academic_group_id' => $academicGroup->id]);
        $user = User::factory()->create(['preferred_academic_level_id' => $academicLevel->id]);

        $systemType = GradingSystemResolver::getSystemType($user);

        $this->assertEquals('wassce', $systemType);
    }

    public function test_returns_university_system_for_university_academic_group(): void
    {
        $academicGroup = AcademicGroup::factory()->create(['tag' => 'university']);
        $academicLevel = AcademicLevel::factory()->create(['academic_group_id' => $academicGroup->id]);
        $user = User::factory()->create(['preferred_academic_level_id' => $academicLevel->id]);

        $systemType = GradingSystemResolver::getSystemType($user);

        $this->assertEquals('university', $systemType);
    }

    public function test_student_academic_level_takes_priority_over_preferred(): void
    {
        // Create a senior academic group for preferred level
        $seniorGroup = AcademicGroup::factory()->create(['tag' => 'senior']);
        $seniorLevel = AcademicLevel::factory()->create(['academic_group_id' => $seniorGroup->id]);

        // Create a basic academic group for student's assigned level
        $basicGroup = AcademicGroup::factory()->create(['tag' => 'basic']);
        $basicLevel = AcademicLevel::factory()->create(['academic_group_id' => $basicGroup->id]);

        // Create user with senior preferred level
        $user = User::factory()->create(['preferred_academic_level_id' => $seniorLevel->id]);

        // Create student with basic academic level
        Student::factory()->create([
            'user_id' => $user->id,
            'academic_level_id' => $basicLevel->id,
        ]);

        // Refresh user to load student relationship
        $user->refresh();

        $systemType = GradingSystemResolver::getSystemType($user);

        // Student's assigned level (basic) should take priority
        $this->assertEquals('bece', $systemType);
    }

    public function test_get_grade_returns_bece_grade_for_basic_user(): void
    {
        $academicGroup = AcademicGroup::factory()->create(['tag' => 'basic']);
        $academicLevel = AcademicLevel::factory()->create(['academic_group_id' => $academicGroup->id]);
        $user = User::factory()->create(['preferred_academic_level_id' => $academicLevel->id]);

        $gradeInfo = GradingSystemResolver::getGrade($user, 85);

        $this->assertEquals('bece', $gradeInfo['system']);
        $this->assertEquals(1, $gradeInfo['grade']); // 85% = Grade 1 in BECE
        $this->assertEquals('Excellent', $gradeInfo['interpretation']);
        $this->assertTrue($gradeInfo['is_passing']);
    }

    public function test_get_grade_returns_wassce_grade_for_senior_user(): void
    {
        $academicGroup = AcademicGroup::factory()->create(['tag' => 'senior']);
        $academicLevel = AcademicLevel::factory()->create(['academic_group_id' => $academicGroup->id]);
        $user = User::factory()->create(['preferred_academic_level_id' => $academicLevel->id]);

        $gradeInfo = GradingSystemResolver::getGrade($user, 85);

        $this->assertEquals('wassce', $gradeInfo['system']);
        $this->assertEquals('A1', $gradeInfo['grade']); // 85% = A1 in WASSCE
        $this->assertEquals('Excellent', $gradeInfo['interpretation']);
        $this->assertTrue($gradeInfo['is_passing']);
        $this->assertTrue($gradeInfo['is_credit']);
    }

    public function test_get_grade_returns_university_grade_for_university_user(): void
    {
        $academicGroup = AcademicGroup::factory()->create(['tag' => 'university']);
        $academicLevel = AcademicLevel::factory()->create(['academic_group_id' => $academicGroup->id]);
        $user = User::factory()->create(['preferred_academic_level_id' => $academicLevel->id]);

        $gradeInfo = GradingSystemResolver::getGrade($user, 85);

        $this->assertEquals('university', $gradeInfo['system']);
        $this->assertEquals('B', $gradeInfo['grade']); // 85% = B in University (80-89%)
        $this->assertEquals('Good', $gradeInfo['interpretation']);
        $this->assertTrue($gradeInfo['is_passing']);
    }

    public function test_bece_grade_boundaries(): void
    {
        // Test BECE grade boundaries
        $this->assertEquals(1, BeceGrade::getGrade(100));
        $this->assertEquals(1, BeceGrade::getGrade(80));
        $this->assertEquals(2, BeceGrade::getGrade(79));
        $this->assertEquals(2, BeceGrade::getGrade(70));
        $this->assertEquals(3, BeceGrade::getGrade(69));
        $this->assertEquals(3, BeceGrade::getGrade(60));
        $this->assertEquals(9, BeceGrade::getGrade(34));
        $this->assertEquals(9, BeceGrade::getGrade(0));
    }

    public function test_wassce_grade_boundaries(): void
    {
        // Test WASSCE grade boundaries
        $this->assertEquals('A1', WassceGrade::getGrade(100));
        $this->assertEquals('A1', WassceGrade::getGrade(80));
        $this->assertEquals('B2', WassceGrade::getGrade(79));
        $this->assertEquals('B2', WassceGrade::getGrade(70));
        $this->assertEquals('B3', WassceGrade::getGrade(69));
        $this->assertEquals('B3', WassceGrade::getGrade(65));
        $this->assertEquals('F9', WassceGrade::getGrade(39));
        $this->assertEquals('F9', WassceGrade::getGrade(0));
    }

    public function test_bece_passing_grades(): void
    {
        $this->assertTrue(BeceGrade::isPassing(1));
        $this->assertTrue(BeceGrade::isPassing(8));
        $this->assertFalse(BeceGrade::isPassing(9));
    }

    public function test_wassce_passing_grades(): void
    {
        $this->assertTrue(WassceGrade::isPassing('A1'));
        $this->assertTrue(WassceGrade::isPassing('E8'));
        $this->assertFalse(WassceGrade::isPassing('F9'));
    }

    public function test_wassce_credit_grades(): void
    {
        $this->assertTrue(WassceGrade::isCredit('A1'));
        $this->assertTrue(WassceGrade::isCredit('C6'));
        $this->assertFalse(WassceGrade::isCredit('D7'));
        $this->assertFalse(WassceGrade::isCredit('F9'));
    }

    public function test_get_system_name_returns_correct_names(): void
    {
        $this->assertEquals('BECE Grading System', GradingSystemResolver::getSystemName(null));

        $basicGroup = AcademicGroup::factory()->create(['tag' => 'basic']);
        $basicLevel = AcademicLevel::factory()->create(['academic_group_id' => $basicGroup->id]);
        $basicUser = User::factory()->create(['preferred_academic_level_id' => $basicLevel->id]);
        $this->assertEquals('BECE Grading System', GradingSystemResolver::getSystemName($basicUser));

        $seniorGroup = AcademicGroup::factory()->create(['tag' => 'senior']);
        $seniorLevel = AcademicLevel::factory()->create(['academic_group_id' => $seniorGroup->id]);
        $seniorUser = User::factory()->create(['preferred_academic_level_id' => $seniorLevel->id]);
        $this->assertEquals('WASSCE Grading System', GradingSystemResolver::getSystemName($seniorUser));

        $uniGroup = AcademicGroup::factory()->create(['tag' => 'university']);
        $uniLevel = AcademicLevel::factory()->create(['academic_group_id' => $uniGroup->id]);
        $uniUser = User::factory()->create(['preferred_academic_level_id' => $uniLevel->id]);
        $this->assertEquals('University Grading System', GradingSystemResolver::getSystemName($uniUser));
    }
}
