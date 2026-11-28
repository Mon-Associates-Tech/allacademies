<?php

namespace Tests\Feature;

use App\Models\School;
use App\Models\SchoolPayment;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AccountantTransactionExportTest extends TestCase
{
    use RefreshDatabase;

    public function test_accountant_can_export_filtered_transactions_for_their_school_as_csv(): void
    {
        $school = School::factory()->create();
        $otherSchool = School::factory()->create();
        $accountant = User::factory()->create([
            'role' => 'accountant',
            'school_id' => $school->id,
        ]);

        $targetStudent = $this->createStudent($school, 'Target Learner');
        $otherStudent = $this->createStudent($school, 'Other Learner');
        $otherSchoolStudent = $this->createStudent($otherSchool, 'Target Learner');

        $this->createPayment($school, $targetStudent, [
            'reference' => 'MATCH-001',
            'status' => 'succeeded',
            'payer_name' => '=2+2',
        ]);
        $this->createPayment($school, $targetStudent, [
            'reference' => 'MATCH-FAILED',
            'status' => 'failed',
        ]);
        $this->createPayment($school, $otherStudent, [
            'reference' => 'OTHER-001',
            'status' => 'succeeded',
        ]);
        $this->createPayment($otherSchool, $otherSchoolStudent, [
            'reference' => 'CROSS-SCHOOL',
            'status' => 'succeeded',
        ]);

        $response = $this->actingAs($accountant)->post(route('accountant.transactions.export'), [
            'search' => 'Target Learner',
            'status' => 'succeeded',
            'start_date' => now()->toDateString(),
            'end_date' => now()->toDateString(),
        ]);

        $response->assertOk()
            ->assertHeader('content-type', 'text/csv; charset=UTF-8')
            ->assertDownload();

        $csv = $response->streamedContent();
        $this->assertStringContainsString('MATCH-001', $csv);
        $this->assertStringContainsString("'=2+2", $csv);
        $this->assertStringNotContainsString('MATCH-FAILED', $csv);
        $this->assertStringNotContainsString('OTHER-001', $csv);
        $this->assertStringNotContainsString('CROSS-SCHOOL', $csv);
    }

    private function createStudent(School $school, string $name): Student
    {
        $user = User::factory()->create([
            'name' => $name,
            'role' => 'student',
            'school_id' => $school->id,
        ]);

        return Student::create([
            'user_id' => $user->id,
            'school_id' => $school->id,
            'student_id' => 'STU-'.$user->id,
        ]);
    }

    private function createPayment(School $school, Student $student, array $attributes = []): SchoolPayment
    {
        return SchoolPayment::create(array_merge([
            'school_id' => $school->id,
            'student_id' => $student->id,
            'payment_type' => 'tuition',
            'amount' => 100,
            'currency' => 'GHS',
            'payer_type' => 'other',
            'payer_name' => 'Test Payer',
            'status' => 'pending',
            'reference' => 'PAY-'.$student->id,
        ], $attributes));
    }
}
