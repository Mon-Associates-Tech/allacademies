<?php

namespace App\Http\Controllers\Accountants;

use App\Http\Controllers\Controller;
use App\Models\PayrollItem;
use App\Models\PayrollRun;
use App\Models\School;
use App\Models\Teacher;
use App\Support\PayrollAmount;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class PayrollController extends Controller
{
    public function index()
    {
        $runs = PayrollRun::where('school_id', $this->schoolId())
            ->withCount('items')
            ->orderByDesc('period_start')
            ->paginate(15);

        return view('accountant.payroll.index', compact('runs'));
    }

    public function store(Request $request)
    {
        $schoolId = $this->schoolId();
        $data = $request->validate([
            'period_start' => ['required', 'date'],
            'period_end' => ['required', 'date', 'after_or_equal:period_start'],
        ]);

        $run = DB::transaction(function () use ($data, $schoolId) {
            School::whereKey($schoolId)->lockForUpdate()->firstOrFail();

            $overlappingRun = PayrollRun::where('school_id', $schoolId)
                ->whereDate('period_start', '<=', $data['period_end'])
                ->whereDate('period_end', '>=', $data['period_start'])
                ->exists();

            if ($overlappingRun) {
                throw ValidationException::withMessages(['period_start' => 'This period overlaps an existing payroll run.']);
            }

            $teachers = Teacher::query()
                ->where('school_id', $schoolId)
                ->where('status', 'active')
                ->whereNotNull('salary')
                ->where('salary', '>', 0)
                ->with('user')
                ->orderBy('employee_id')
                ->get();

            if ($teachers->isEmpty()) {
                throw ValidationException::withMessages(['payroll' => 'No active teachers with a salary are available for this school.']);
            }

            $run = PayrollRun::create([
                'school_id' => $schoolId,
                'period_start' => $data['period_start'],
                'period_end' => $data['period_end'],
                'currency' => 'GHS',
                'status' => 'draft',
                'created_by' => auth()->id(),
            ]);

            foreach ($teachers as $teacher) {
                $run->items()->create([
                    'teacher_id' => $teacher->id,
                    'employee_id' => $teacher->employee_id,
                    'employee_name' => $teacher->user?->name ?? 'Employee '.$teacher->id,
                    'department' => $teacher->department,
                    'base_salary' => $teacher->salary,
                    'allowances' => '0.00',
                    'deductions' => '0.00',
                ]);
            }

            return $run;
        });

        return redirect()->route('accountant.payroll.show', $run)
            ->with('success', 'Draft payroll run created. Review and confirm every employee line before approval.');
    }

    public function show(int $payrollRun)
    {
        $run = $this->payrollRun($this->schoolId(), $payrollRun)->with('items')->firstOrFail();
        $items = $run->items->sortBy('employee_name')->values();
        $totalNetPayInCents = $items->sum(fn (PayrollItem $item) => $item->netPayInCents());
        $totalNetPay = PayrollAmount::formatCents($totalNetPayInCents);
        $canApprove = $items->isNotEmpty() && $items->every(fn (PayrollItem $item) => $item->confirmed_at !== null);

        return view('accountant.payroll.show', compact('run', 'items', 'totalNetPay', 'canApprove'));
    }

    public function updateItem(Request $request, int $payrollRun, int $payrollItem)
    {
        $schoolId = $this->schoolId();
        $data = $request->validate([
            'base_salary' => ['required', 'numeric', 'min:0', 'decimal:0,2'],
            'allowances' => ['required', 'numeric', 'min:0', 'decimal:0,2'],
            'deductions' => ['required', 'numeric', 'min:0', 'decimal:0,2'],
            'notes' => ['nullable', 'string', 'max:2000'],
        ]);

        if (PayrollAmount::netPayInCents($data['base_salary'], $data['allowances'], $data['deductions']) < 0) {
            return back()->withErrors(['deductions' => 'Deductions cannot exceed base pay plus allowances.'])->withInput();
        }

        $run = DB::transaction(function () use ($data, $schoolId, $payrollRun, $payrollItem) {
            $run = $this->payrollRun($schoolId, $payrollRun)->lockForUpdate()->firstOrFail();
            abort_unless($run->status === 'draft', 403);

            $item = $run->items()->whereKey($payrollItem)->lockForUpdate()->firstOrFail();
            $item->update([
                ...$data,
                'confirmed_by' => auth()->id(),
                'confirmed_at' => now(),
            ]);

            return $run;
        });

        return redirect()->route('accountant.payroll.show', $run)
            ->with('success', 'Payroll line confirmed.');
    }

    public function approve(int $payrollRun)
    {
        $schoolId = $this->schoolId();

        DB::transaction(function () use ($schoolId, $payrollRun) {
            $run = $this->payrollRun($schoolId, $payrollRun)->lockForUpdate()->firstOrFail();
            abort_unless($run->status === 'draft', 403);

            $items = $run->items()->lockForUpdate()->get();

            if ($items->isEmpty()) {
                throw ValidationException::withMessages(['payroll' => 'An empty payroll run cannot be approved.']);
            }

            if ($items->contains(fn (PayrollItem $item) => $item->confirmed_at === null)) {
                throw ValidationException::withMessages(['payroll' => 'Confirm every employee line before approving this run.']);
            }

            $run->update([
                'status' => 'approved',
                'approved_by' => auth()->id(),
                'approved_at' => now(),
            ]);
        });

        return redirect()->route('accountant.payroll.show', $payrollRun)
            ->with('success', 'Payroll run approved.');
    }

    public function markPaid(int $payrollRun)
    {
        $schoolId = $this->schoolId();

        DB::transaction(function () use ($schoolId, $payrollRun) {
            $run = $this->payrollRun($schoolId, $payrollRun)->lockForUpdate()->firstOrFail();
            abort_unless($run->status === 'approved', 403);

            $run->update([
                'status' => 'paid',
                'paid_by' => auth()->id(),
                'paid_at' => now(),
            ]);
        });

        return redirect()->route('accountant.payroll.show', $payrollRun)
            ->with('success', 'Payroll run marked as paid.');
    }

    private function schoolId(): int
    {
        $schoolId = getSchoolId();
        abort_unless($schoolId, 403, 'Select a school before accessing payroll.');

        return (int) $schoolId;
    }

    private function payrollRun(int $schoolId, int $payrollRun)
    {
        return PayrollRun::where('school_id', $schoolId)->whereKey($payrollRun);
    }
}
