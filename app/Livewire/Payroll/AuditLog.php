<?php

namespace App\Livewire\Payroll;

use App\Models\PayrollAuditLog;
use Livewire\Component;
use Livewire\WithPagination;

class AuditLog extends Component
{
    use WithPagination;

    public $filterAction = '';
    public $filterUser = '';
    public $dateFrom = '';
    public $dateTo = '';

    protected $queryString = ['filterAction', 'filterUser', 'dateFrom', 'dateTo'];

    public function updatingFilterAction()
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = PayrollAuditLog::query()
            ->where('school_id', auth()->user()->school_id)
            ->with(['user', 'subject']);

        if ($this->filterAction) {
            $query->where('action', 'like', '%' . $this->filterAction . '%');
        }

        if ($this->filterUser) {
            $query->where('user_id', $this->filterUser);
        }

        if ($this->dateFrom) {
            $query->whereDate('created_at', '>=', $this->dateFrom);
        }

        if ($this->dateTo) {
            $query->whereDate('created_at', '<=', $this->dateTo);
        }

        $logs = $query->latest('created_at')->paginate(20);

        return view('livewire.payroll.audit-log', [
            'logs' => $logs,
        ]);
    }
}
