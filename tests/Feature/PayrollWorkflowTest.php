<?php

namespace Tests\Feature;

use App\Models\PayrollRun;
use App\Models\School;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PayrollWorkflowTest extends TestCase
{
    use RefreshDatabase;

    private School $school;

    private User $accountant;

    protected function setUp(): void
    {
        parent::setUp();

        $this->school = School::factory()->create();
        $this->accountant = User::factory()->create([
            'role' => 'accountant',
            'school_id' => $this->school->id,
        ]);
    }

    public function test_only_accountants_can_open_payroll(): void
    {
        $this->actingAs($this->accountant)
            ->get(route('accountant.payroll.index'))
            ->assertOk();

        $teacher = User::factory()->create([
            'role' => 'teacher',
            'school_id' => $this->school->id,
        ]);

        $this->actingAs($teacher)
            ->get(route('accountant.payroll.index'))
            ->assertForbidden();
    }

    public function test_draft_run_snapshots_only_active_salaried_teachers_in_the_current_school(): void
    {
        $eligibleTeacher = $this->createTeacher($this->school, 'active', '2500.00');
        $this->createTeacher($this->school, 'inactive', '3000.00');
        $this->createTeacher($this->school, 'active', null);
        $otherSchool = School::factory()->create();
        $this->createTeacher($otherSchool, 'active', '5000.00');

        $response = $this->actingAs($this->accountant)->post(route('accountant.payroll.store'), [
            'period_start' => '2026-11-01',
            'period_end' => '2026-11-30',
        ]);

        $run = PayrollRun::where('school_id', $this->school->id)->firstOrFail();
        $response->assertRedirect(route('accountant.payroll.show', $run));
        $this->assertSame('draft', $run->status);
        $this->assertCount(1, $run->items);
        $this->assertSame($eligibleTeacher->employee_id, $run->items->first()->employee_id);
        $this->assertSame('2500.00', $run->items->first()->base_salary);
        $this->assertNull($run->items->first()->confirmed_at);
    }

    public function test_payroll_requires_confirmed_lines_and_enforces_approval_then_paid_transitions(): void
    {
        $teacher = $this->createTeacher($this->school, 'active', '2500.00');
        $run = $this->createRun($teacher);
        $item = $run->items()->firstOrFail();

        $this->actingAs($this->accountant)
            ->post(route('accountant.payroll.approve', $run))
            ->assertSessionHasErrors('payroll');
        $this->assertDatabaseHas('payroll_runs', ['id' => $run->id, 'status' => 'draft']);

        $this->put(route('accountant.payroll.items.update', [$run, $item]), [
            'base_salary' => '2500.00',
            'allowances' => '250.50',
            'deductions' => '100.25',
            'notes' => 'Reviewed against the approved salary record.',
        ])->assertRedirect(route('accountant.payroll.show', $run));

        $item->refresh();
        $this->assertSame('2650.25', $item->net_pay);
        $this->assertNotNull($item->confirmed_at);

        $this->post(route('accountant.payroll.approve', $run))
            ->assertRedirect(route('accountant.payroll.show', $run));
        $this->assertDatabaseHas('payroll_runs', [
            'id' => $run->id,
            'status' => 'approved',
            'approved_by' => $this->accountant->id,
        ]);

        $this->post(route('accountant.payroll.paid', $run))
            ->assertRedirect(route('accountant.payroll.show', $run));
        $this->assertDatabaseHas('payroll_runs', [
            'id' => $run->id,
            'status' => 'paid',
            'paid_by' => $this->accountant->id,
        ]);

        $this->put(route('accountant.payroll.items.update', [$run, $item]), [
            'base_salary' => '9999.00',
            'allowances' => '0.00',
            'deductions' => '0.00',
        ])->assertForbidden();
    }

    public function test_deductions_cannot_exceed_base_pay_and_allowances(): void
    {
        $teacher = $this->createTeacher($this->school, 'active', '2500.00');
        $run = $this->createRun($teacher);
        $item = $run->items()->firstOrFail();

        $this->actingAs($this->accountant)
            ->put(route('accountant.payroll.items.update', [$run, $item]), [
                'base_salary' => '100.00',
                'allowances' => '5.00',
                'deductions' => '105.01',
            ])
            ->assertSessionHasErrors('deductions');

        $this->assertNull($item->fresh()->confirmed_at);
    }

    public function test_overlapping_periods_and_cross_school_run_access_are_blocked(): void
    {
        $teacher = $this->createTeacher($this->school, 'active', '2500.00');
        $run = $this->createRun($teacher);

        $this->actingAs($this->accountant)
            ->post(route('accountant.payroll.store'), [
                'period_start' => '2026-11-15',
                'period_end' => '2026-12-15',
            ])
            ->assertSessionHasErrors('period_start');

        $otherSchool = School::factory()->create();
        $otherRun = PayrollRun::create([
            'school_id' => $otherSchool->id,
            'period_start' => '2026-12-01',
            'period_end' => '2026-12-31',
            'currency' => 'GHS',
            'status' => 'draft',
        ]);

        $this->get(route('accountant.payroll.show', $otherRun))->assertNotFound();
        $this->assertSame('draft', $run->fresh()->status);
    }

    private function createTeacher(School $school, string $status, ?string $salary): Teacher
    {
        return Teacher::factory()->create([
            'school_id' => $school->id,
            'employee_id' => 'EMP-'.fake()->unique()->numerify('####'),
            'status' => $status,
            'salary' => $salary,
        ]);
    }

    private function createRun(Teacher $teacher): PayrollRun
    {
        $run = PayrollRun::create([
            'school_id' => $this->school->id,
            'period_start' => '2026-11-01',
            'period_end' => '2026-11-30',
            'currency' => 'GHS',
            'status' => 'draft',
            'created_by' => $this->accountant->id,
        ]);

        $run->items()->create([
            'teacher_id' => $teacher->id,
            'employee_id' => $teacher->employee_id,
            'employee_name' => $teacher->user->name,
            'base_salary' => $teacher->salary,
        ]);

        return $run;
    }
}
