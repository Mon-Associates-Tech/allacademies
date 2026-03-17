<?php

namespace App\Livewire\Payroll;

use App\Models\PayrollRun;
use Livewire\Component;
use Livewire\WithPagination;

class RunIndex extends Component
{
    use WithPagination;

    public $filterStatus = '';
    public $filterType = '';
    public $dateFrom = '';
    public $dateTo = '';

    protected $queryString = ['filterStatus', 'filterType', 'dateFrom', 'dateTo'];

    public function updatingFilterStatus()
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = PayrollRun::query()
            ->where('school_id', auth()->user()->school_id)
            ->with(['schedule', 'initiator', 'approver']);

        if ($this->filterStatus) {
            $query->where('status', $this->filterStatus);
        }

        if ($this->filterType) {
            $query->where('run_type', $this->filterType);
        }

        if ($this->dateFrom) {
            $query->whereDate('created_at', '>=', $this->dateFrom);
        }

        if ($this->dateTo) {
            $query->whereDate('created_at', '<=', $this->dateTo);
        }

        $runs = $query->latest()->paginate(15);

        return view('livewire.payroll.run-index', [
            'runs' => $runs,
        ]);
    }
}
