<?php

namespace Tests\Feature;

use App\Models\School;
use App\Models\SchoolExpense;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AccountantFinanceWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_accountant_can_record_paid_expense_and_reconcile_a_matching_statement_line(): void
    {
        $school = School::factory()->create();
        $accountant = User::factory()->create([
            'role' => 'accountant',
            'school_id' => $school->id,
        ]);

        $this->actingAs($accountant)->post(route('accountant.expenses.store'), [
            'category' => 'Utilities',
            'description' => 'Science lab electricity',
            'reference' => 'INV-1001',
            'amount' => '150.00',
            'currency' => 'GHS',
            'expense_date' => now()->toDateString(),
            'status' => 'paid',
        ])->assertRedirect(route('accountant.expenses.index'));

        $expense = SchoolExpense::query()->where('school_id', $school->id)->firstOrFail();
        $this->assertSame('paid', $expense->status);
        $this->get(route('accountant.ledger.index'))->assertOk()->assertSee('Science lab electricity');

        $this->post(route('accountant.ledger.reconcile'), [
            'source_type' => 'expense',
            'source_id' => $expense->id,
            'bank_reference' => 'BANK-501',
            'statement_date' => now()->toDateString(),
            'statement_amount' => '149.00',
            'statement_currency' => 'GHS',
        ])->assertSessionHasErrors('statement_amount');

        $this->assertDatabaseMissing('financial_reconciliations', [
            'school_id' => $school->id,
            'source_type' => 'expense',
            'source_id' => $expense->id,
        ]);

        $this->post(route('accountant.ledger.reconcile'), [
            'source_type' => 'expense',
            'source_id' => $expense->id,
            'bank_reference' => 'BANK-501',
            'statement_date' => now()->toDateString(),
            'statement_amount' => '150.00',
            'statement_currency' => 'GHS',
            'note' => 'Matched to monthly bank statement',
        ])->assertRedirect();

        $this->assertDatabaseHas('financial_reconciliations', [
            'school_id' => $school->id,
            'source_type' => 'expense',
            'source_id' => $expense->id,
            'bank_reference' => 'BANK-501',
            'statement_amount' => '150.00',
            'statement_currency' => 'GHS',
        ]);
        $this->get(route('accountant.ledger.index'))->assertSee('BANK-501');
    }

    public function test_accountant_cannot_view_or_mark_another_schools_expense_as_paid(): void
    {
        $school = School::factory()->create();
        $otherSchool = School::factory()->create();
        $accountant = User::factory()->create([
            'role' => 'accountant',
            'school_id' => $school->id,
        ]);
        $expense = SchoolExpense::create([
            'school_id' => $otherSchool->id,
            'category' => 'Supplies',
            'description' => 'Private school purchase',
            'amount' => '25.00',
            'currency' => 'GHS',
            'expense_date' => now()->toDateString(),
            'status' => 'pending',
        ]);

        $this->actingAs($accountant)
            ->get(route('accountant.expenses.index'))
            ->assertOk()
            ->assertDontSee('Private school purchase');

        $this->patch(route('accountant.expenses.paid', $expense))->assertNotFound();
        $this->assertDatabaseHas('school_expenses', ['id' => $expense->id, 'status' => 'pending']);
    }
}
