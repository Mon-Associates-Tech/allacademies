<?php

namespace App\Livewire\Payroll;

use App\Models\PayrollRole;
use Livewire\Component;
use Livewire\WithPagination;

class RoleIndex extends Component
{
    use WithPagination;

    public $search = '';

    protected $queryString = ['search'];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $schoolId = getSchoolId() ?? auth()->user()->school_id;
        
        $query = PayrollRole::query()
            ->where('school_id', $schoolId)
            ->withCount('payrollEntries');

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('description', 'like', '%' . $this->search . '%');
            });
        }

        $roles = $query->latest()->paginate(15);

        return view('livewire.payroll.role-index', [
            'roles' => $roles,
        ]);
    }
}
