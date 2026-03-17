<?php

namespace App\Http\Controllers\Payroll;

use App\Http\Controllers\Controller;
use App\Models\PayrollSchedule;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PayrollScheduleController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:managePayroll,App\Models\User');
    }

    public function index()
    {
        return view('payroll.schedules.index');
    }

    public function create()
    {
        return view('payroll.schedules.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'frequency' => 'required|in:one_time,monthly,weekly,bi_weekly,quarterly',
            'run_date' => 'required|date',
        ]);
        
        $validated['school_id'] = auth()->user()->school_id;
        $validated['created_by'] = auth()->id();
        $validated['status'] = 'active';
        $validated['next_run_at'] = Carbon::parse($validated['run_date']);
        
        PayrollSchedule::create($validated);
        
        return redirect()->route('payroll.schedules.index')
            ->with('success', 'Payroll schedule created successfully.');
    }

    public function edit(PayrollSchedule $schedule)
    {
        return view('payroll.schedules.edit', compact('schedule'));
    }

    public function update(Request $request, PayrollSchedule $schedule)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'frequency' => 'required|in:one_time,monthly,weekly,bi_weekly,quarterly',
            'run_date' => 'required|date',
            'status' => 'required|in:active,paused,completed,cancelled',
        ]);
        
        if ($request->has('run_date')) {
            $validated['next_run_at'] = Carbon::parse($validated['run_date']);
        }
        
        $schedule->update($validated);
        
        return redirect()->route('payroll.schedules.index')
            ->with('success', 'Payroll schedule updated successfully.');
    }

    public function destroy(PayrollSchedule $schedule)
    {
        $schedule->update(['status' => 'cancelled']);
        
        return redirect()->route('payroll.schedules.index')
            ->with('success', 'Payroll schedule cancelled successfully.');
    }
}
