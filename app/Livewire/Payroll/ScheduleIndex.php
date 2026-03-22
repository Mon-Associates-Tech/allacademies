<?php

namespace App\Livewire\Payroll;

use App\Models\PayrollSchedule;
use Livewire\Component;
use Livewire\WithPagination;

class ScheduleIndex extends Component
{
    use WithPagination;

    public $search = '';
    public $filterStatus = '';

    protected $queryString = ['search', 'filterStatus'];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $schoolId = getSchoolId() ?? auth()->user()->school_id;
        
        $query = PayrollSchedule::query()
            ->where('school_id', $schoolId)
            ->with(['creator'])
            ->withCount('payrollRuns');

        if ($this->search) {
            $query->where('name', 'like', '%' . $this->search . '%');
        }

        if ($this->filterStatus) {
            $query->where('status', $this->filterStatus);
        }

        $schedules = $query->latest()->paginate(15);

        return view('livewire.payroll.schedule-index', [
            'schedules' => $schedules,
        ]);
    }
}
