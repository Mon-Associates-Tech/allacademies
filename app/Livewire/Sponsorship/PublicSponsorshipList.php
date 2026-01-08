<?php

namespace App\Livewire\Sponsorship;

use App\Models\SponsorshipProject;
use Livewire\Component;
use Livewire\WithPagination;

class PublicSponsorshipList extends Component
{
    use WithPagination;

    public $search = '';

    public $selectedType = '';

    public $sortBy = 'latest';

    protected $queryString = [
        'search' => ['except' => ''],
        'selectedType' => ['except' => ''],
        'sortBy' => ['except' => 'latest'],
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingSelectedType()
    {
        $this->resetPage();
    }

    public function render()
    {
        $query = SponsorshipProject::active()
            ->with(['user', 'beneficiaries', 'school']);

        // Search
        if ($this->search) {
            $search = $this->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('code', 'like', "%{$search}%");
            });
        }

        // Filter by type
        if ($this->selectedType) {
            $query->ofType($this->selectedType);
        }

        // Sorting
        switch ($this->sortBy) {
            case 'amount_high':
                $query->orderBy('amount_goal', 'desc');
                break;
            case 'amount_low':
                $query->orderBy('amount_goal', 'asc');
                break;
            case 'progress':
                $query->orderByRaw('(amount_raised / NULLIF(amount_goal, 0)) DESC');
                break;
            case 'deadline':
                $query->whereNotNull('deadline')->orderBy('deadline', 'asc');
                break;
            default:
                $query->orderBy('created_at', 'desc');
        }

        $projects = $query->paginate(12);

        // Add computed attributes
        $projects->getCollection()->transform(function ($project) {
            $project->goal_amount = $project->amount_goal;
            $project->realized_amount = $project->amount_raised;
            $project->left_amount = $project->amount_left;

            return $project;
        });

        return view('livewire.sponsorships.public-sponsorship-list', [
            'projects' => $projects,
            'types' => SponsorshipProject::getTypes(),
        ])->layout('components.layouts.guest', ['pageName' => 'Sponsorship Projects']);
    }
}
