<?php

namespace App\Http\Controllers\Accountants;

use App\Http\Controllers\Controller;
use App\Models\FinancialReconciliation;
use App\Models\SchoolExpense;
use Illuminate\Database\Query\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class FinanceController extends Controller
{
    public function ledger(Request $request)
    {
        $request->validate([
            'start_date' => ['nullable', 'date'],
            'end_date' => ['nullable', 'date', 'after_or_equal:start_date'],
        ]);

        $query = $this->ledgerQuery($this->schoolId());

        if ($request->filled('start_date')) {
            $query->whereDate('ledger.transaction_date', '>=', $request->input('start_date'));
        }

        if ($request->filled('end_date')) {
            $query->whereDate('ledger.transaction_date', '<=', $request->input('end_date'));
        }

        $entries = $query->orderByDesc('ledger.transaction_date')
            ->orderByDesc('ledger.source_id')
            ->paginate(25)
            ->withQueryString();

        return view('accountant.ledger.index', compact('entries'));
    }

    public function expenses()
    {
        $expenses = SchoolExpense::query()
            ->where('school_id', $this->schoolId())
            ->latest('expense_date')
            ->paginate(20);

        return view('accountant.expenses.index', compact('expenses'));
    }

    public function storeExpense(Request $request)
    {
        $data = $request->validate([
            'category' => ['required', 'string', 'max:100'],
            'description' => ['required', 'string', 'max:255'],
            'reference' => ['nullable', 'string', 'max:100'],
            'amount' => ['required', 'numeric', 'gt:0', 'decimal:0,2'],
            'currency' => ['required', 'string', 'size:3', 'alpha'],
            'expense_date' => ['required', 'date'],
            'status' => ['required', Rule::in(['pending', 'paid'])],
        ]);

        SchoolExpense::create([
            ...$data,
            'school_id' => $this->schoolId(),
            'created_by' => auth()->id(),
            'currency' => strtoupper($data['currency']),
            'paid_at' => $data['status'] === 'paid' ? now() : null,
        ]);

        return redirect()->route('accountant.expenses.index')->with('success', 'Expense recorded.');
    }

    public function markExpensePaid(int $expense)
    {
        $record = SchoolExpense::query()
            ->where('school_id', $this->schoolId())
            ->whereKey($expense)
            ->firstOrFail();

        if ($record->status === 'pending') {
            $record->update(['status' => 'paid', 'paid_at' => now()]);
        }

        return back()->with('success', 'Expense marked as paid.');
    }

    public function reconcile(Request $request)
    {
        $data = $request->validate([
            'source_type' => ['required', Rule::in(['payment', 'expense', 'payroll'])],
            'source_id' => ['required', 'integer', 'min:1'],
            'bank_reference' => ['nullable', 'string', 'max:100'],
            'statement_date' => ['required', 'date'],
            'statement_amount' => ['required', 'numeric', 'gt:0', 'decimal:0,2'],
            'statement_currency' => ['required', 'string', 'size:3', 'alpha'],
            'note' => ['nullable', 'string', 'max:1000'],
        ]);
        $schoolId = $this->schoolId();

        $entry = $this->ledgerQuery($schoolId)
            ->where('ledger.source_type', $data['source_type'])
            ->where('ledger.source_id', $data['source_id'])
            ->first();
        abort_unless($entry, 404);

        $statementCurrency = strtoupper($data['statement_currency']);
        if (number_format((float) $data['statement_amount'], 2, '.', '') !== number_format((float) $entry->amount, 2, '.', '')
            || $statementCurrency !== strtoupper($entry->currency)) {
            return back()
                ->withErrors(['statement_amount' => 'The statement amount and currency must match the selected ledger entry.'])
                ->withInput();
        }

        FinancialReconciliation::updateOrCreate(
            [
                'school_id' => $schoolId,
                'source_type' => $data['source_type'],
                'source_id' => $data['source_id'],
            ],
            [
                'reconciled_by' => auth()->id(),
                'bank_reference' => $data['bank_reference'] ?? null,
                'statement_date' => $data['statement_date'],
                'statement_amount' => $data['statement_amount'],
                'statement_currency' => $statementCurrency,
                'note' => $data['note'] ?? null,
                'reconciled_at' => now(),
            ]
        );

        return back()->with('success', 'Ledger entry marked as reconciled.');
    }

    public function unreconcile(string $sourceType, int $sourceId)
    {
        abort_unless(in_array($sourceType, ['payment', 'expense', 'payroll'], true), 404);

        FinancialReconciliation::query()
            ->where('school_id', $this->schoolId())
            ->where('source_type', $sourceType)
            ->where('source_id', $sourceId)
            ->delete();

        return back()->with('success', 'Ledger entry returned to unreconciled status.');
    }

    private function ledgerQuery(int $schoolId): Builder
    {
        $payments = DB::table('school_payments')
            ->leftJoin('financial_reconciliations as reconciliation', function ($join) {
                $join->on('reconciliation.source_id', '=', 'school_payments.id')
                    ->where('reconciliation.source_type', '=', 'payment')
                    ->on('reconciliation.school_id', '=', 'school_payments.school_id');
            })
            ->where('school_payments.school_id', $schoolId)
            ->where('school_payments.status', 'succeeded')
            ->whereNull('school_payments.deleted_at')
            ->selectRaw("'income' as direction, 'payment' as source_type, school_payments.id as source_id")
            ->selectRaw('COALESCE(school_payments.paid_at, school_payments.created_at) as transaction_date')
            ->selectRaw('school_payments.reference as reference, COALESCE(NULLIF(school_payments.description, \'\'), school_payments.payment_type) as description')
            ->selectRaw('school_payments.amount as amount, school_payments.currency as currency, reconciliation.id as reconciliation_id')
            ->selectRaw('reconciliation.bank_reference as bank_reference, reconciliation.statement_date as statement_date, reconciliation.note as reconciliation_note');

        $expenses = DB::table('school_expenses')
            ->leftJoin('financial_reconciliations as reconciliation', function ($join) {
                $join->on('reconciliation.source_id', '=', 'school_expenses.id')
                    ->where('reconciliation.source_type', '=', 'expense')
                    ->on('reconciliation.school_id', '=', 'school_expenses.school_id');
            })
            ->where('school_expenses.school_id', $schoolId)
            ->where('school_expenses.status', 'paid')
            ->selectRaw("'expense' as direction, 'expense' as source_type, school_expenses.id as source_id")
            ->selectRaw('COALESCE(school_expenses.paid_at, school_expenses.expense_date) as transaction_date')
            ->selectRaw('school_expenses.reference as reference, school_expenses.description as description')
            ->selectRaw('school_expenses.amount as amount, school_expenses.currency as currency, reconciliation.id as reconciliation_id')
            ->selectRaw('reconciliation.bank_reference as bank_reference, reconciliation.statement_date as statement_date, reconciliation.note as reconciliation_note');

        $payrollTotals = DB::table('payroll_items')
            ->select('payroll_run_id')
            ->selectRaw('SUM(base_salary + allowances - deductions) as amount')
            ->groupBy('payroll_run_id');

        $payroll = DB::table('payroll_runs')
            ->leftJoinSub($payrollTotals, 'payroll_totals', function ($join) {
                $join->on('payroll_totals.payroll_run_id', '=', 'payroll_runs.id');
            })
            ->leftJoin('financial_reconciliations as reconciliation', function ($join) {
                $join->on('reconciliation.source_id', '=', 'payroll_runs.id')
                    ->where('reconciliation.source_type', '=', 'payroll')
                    ->on('reconciliation.school_id', '=', 'payroll_runs.school_id');
            })
            ->where('payroll_runs.school_id', $schoolId)
            ->where('payroll_runs.status', 'paid')
            ->selectRaw("'expense' as direction, 'payroll' as source_type, payroll_runs.id as source_id")
            ->selectRaw('COALESCE(payroll_runs.paid_at, payroll_runs.period_end) as transaction_date')
            ->selectRaw('NULL as reference, \'Payroll run\' as description')
            ->selectRaw('COALESCE(payroll_totals.amount, 0) as amount, payroll_runs.currency as currency, reconciliation.id as reconciliation_id')
            ->selectRaw('reconciliation.bank_reference as bank_reference, reconciliation.statement_date as statement_date, reconciliation.note as reconciliation_note');

        return DB::query()->fromSub($payments->unionAll($expenses)->unionAll($payroll), 'ledger');
    }

    private function schoolId(): int
    {
        abort_unless(auth()->user()?->hasRole('accountant'), 403, 'Only accountants can access finance records.');
        $schoolId = getSchoolId();
        abort_unless($schoolId, 403, 'Select a school before accessing finance records.');

        return (int) $schoolId;
    }
}
