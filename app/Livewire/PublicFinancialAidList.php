<?php

namespace App\Livewire;

use App\Models\FinancialAid;
use App\Models\School;
use App\Models\SchoolPayment;
use Livewire\Component;
use Livewire\WithPagination;

class PublicFinancialAidList extends Component
{
    use WithPagination;
    
    public $selectedSchools = [];
    public $search = '';
    public $status = '';
    public $sortBy = 'latest';
    
    protected $queryString = [
        'selectedSchools',
        'search',
        'status',
        'sortBy',
    ];
    
    public $dropdownOpen = false;
    
    protected $listeners = [
        'update-selectedSchools' => 'updateSelectedSchools',
    ];
    
    public function updateSelectedSchools($value)
    {
        $this->selectedSchools = $value;
        $this->resetPage();
    }
    
    public function mount()
    {
        $this->selectedSchools = [];
        $this->search = '';
        $this->status = '';
        $this->sortBy = 'latest';
    }
    
    public function updatedSelectedSchools()
    {
        $this->resetPage();
    }
    
    public function updatedSearch()
    {
        $this->resetPage();
    }
    
    public function updatedStatus()
    {
        $this->resetPage();
    }
    
    public function updatedSortBy()
    {
        $this->resetPage();
    }
    
    public function clearFilters()
    {
        $this->selectedSchools = [];
        $this->search = '';
        $this->status = '';
        $this->sortBy = 'latest';
        $this->resetPage();
    }
    
    public function closeDropdown()
    {
        $this->dropdownOpen = false;
    }
    
    public function render()
    {
        // Get all schools that have active financial aid programs
        $schools = School::whereHas('financialAids', function($query) {
            $query->withoutGlobalScopes()->where('status', 'active');
        })->orderBy('name')->get();
        
        // Build the query with filters (bypass global scope for public view)
        $query = FinancialAid::withoutGlobalScopes()->with(['school', 'beneficiaries.user']);
        
        // Apply status filter
        if ($this->status) {
            $query->where('status', $this->status);
        } else {
            $query->where('status', 'acstive');
        }
        
        // Apply school filter
        if (!empty($this->selectedSchools)) {
            $query->whereIn('school_id', $this->selectedSchools);
        }
        
        // Apply search filter
        if ($this->search) {
            $query->where(function($subQuery) {
                $subQuery->where('name', 'like', '%' . $this->search . '%')
                         ->orWhere('description', 'like', '%' . $this->search . '%')
                         ->orWhere('code', 'like', '%' . $this->search . '%');
            });
        }
        
        // Apply sorting
        switch ($this->sortBy) {
            case 'oldest':
                $query->orderBy('created_at', 'asc');
                break;
            case 'name':
                $query->orderBy('name', 'asc');
                break;
            case 'progress':
                $query->orderByRaw('(amount - COALESCE((SELECT SUM(amount) FROM school_payments WHERE payment_type = "donation" AND status = "succeeded" AND JSON_EXTRACT(metadata, "$.financial_aid_id") = financial_aids.id), 0)) ASC');
                break;
            case 'alphabetical':
            default:
                $query->orderBy('created_at', 'desc');
                break;
        }
        
        $aids = $query->paginate(9);
        
        // Calculate stats for each aid
        $aids->getCollection()->transform(function ($aid) {
            // Assuming 'amount' field in FinancialAid is the Total Goal as per requirements
            $goal = $aid->amount;

            // Calculate realized amount from payments marked as 'donation' for this aid
            // If you don't have 'donation' types yet, this will return 0
            $realized = SchoolPayment::where('payment_type', 'donation')
                ->where('status', 'succeeded')
                ->whereJsonContains('metadata->financial_aid_id', $aid->id)
                ->sum('amount');

            $aid->goal_amount = $goal;
            $aid->realized_amount = $realized;
            $aid->left_amount = max(0, $goal - $realized);
            $aid->progress_percentage = $goal > 0 ? min(100, round(($realized / $goal) * 100)) : 0;

            return $aid;
        });

        $hasActivePrograms = $aids->isNotEmpty();

        return view('livewire.public-financial-aid-list', [
            'aids' => $aids,
            'schools' => $schools,
            'hasActivePrograms' => $hasActivePrograms,
        ])->layout('components.layouts.guest', ['pageName' => 'Philanthropy & Aid']);
    }
}
