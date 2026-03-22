<?php

namespace App\Livewire\Payroll;

use App\Models\PayrollEntry;
use App\Models\PayrollRole;
use Livewire\Component;
use Livewire\WithPagination;

class EntryIndex extends Component
{
    use WithPagination;

    public $search = '';
    public $filterRole = '';
    public $filterStatus = '';

    protected $queryString = ['search', 'filterRole', 'filterStatus'];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $schoolId = getSchoolId() ?? auth()->user()->school_id;
        
        $query = PayrollEntry::query()
            ->where('school_id', $schoolId)
            ->with(['user', 'payrollRole', 'bankAccount']);

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('first_name', 'like', '%' . $this->search . '%')
                    ->orWhere('last_name', 'like', '%' . $this->search . '%')
                    ->orWhere('email', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->filterRole) {
            $query->where('payroll_role_id', $this->filterRole);
        }

        if ($this->filterStatus) {
            $query->where('status', $this->filterStatus);
        }

        $entries = $query->latest()->paginate(15);
        
        $payrollRoles = PayrollRole::where('school_id', $schoolId)->get();

        return view('livewire.payroll.entry-index', [
            'entries' => $entries,
            'payrollRoles' => $payrollRoles,
        ]);
    }
}
