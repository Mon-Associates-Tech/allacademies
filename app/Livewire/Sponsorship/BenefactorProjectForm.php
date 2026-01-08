<?php

namespace App\Livewire\Sponsorship;

use App\Models\SponsorshipBeneficiary;
use App\Models\SponsorshipProject;
use App\Services\SponsorshipService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class BenefactorProjectForm extends Component
{
    public $projectId = null;
    public $name = '';
    public $type = 'project';
    public $description = '';
    public $affected_individuals = '';
    public $amount_goal = '';
    public $deadline = '';

    // Beneficiaries
    public $beneficiaries = [];
    public $newBeneficiaryName = '';
    public $newBeneficiaryType = 'individual';
    public $newBeneficiaryEmail = '';
    public $newBeneficiaryPhone = '';
    public $newBeneficiaryDescription = '';

    protected $rules = [
        'name' => 'required|string|min:3|max:255',
        'type' => 'required|in:project,cause,scholarship,emergency',
        'description' => 'nullable|string|max:5000',
        'affected_individuals' => 'nullable|string|max:2000',
        'amount_goal' => 'required|numeric|min:1',
        'deadline' => 'nullable|date|after:today',
    ];

    public function mount($projectId = null)
    {
        if ($projectId) {
            $project = SponsorshipProject::where('user_id', Auth::id())
                ->findOrFail($projectId);

            $this->projectId = $project->id;
            $this->name = $project->name;
            $this->type = $project->type;
            $this->description = $project->description;
            $this->affected_individuals = $project->affected_individuals;
            $this->amount_goal = $project->amount_goal;
            $this->deadline = $project->deadline?->format('Y-m-d');

            // Load existing beneficiaries
            $this->beneficiaries = $project->beneficiaries->map(function ($b) {
                return [
                    'id' => $b->id,
                    'name' => $b->beneficiary_name,
                    'type' => $b->beneficiary_type,
                    'email' => $b->beneficiary_email,
                    'phone' => $b->beneficiary_phone,
                    'description' => $b->beneficiary_description,
                ];
            })->toArray();
        }
    }

    public function removeBeneficiary($index)
    {
        unset($this->beneficiaries[$index]);
        $this->beneficiaries = array_values($this->beneficiaries);
    }

    public function save()
    {
        $this->validate();

        $sponsorshipService = app(SponsorshipService::class);

        $data = [
            'name' => $this->name,
            'type' => $this->type,
            'description' => $this->description,
            'affected_individuals' => $this->affected_individuals,
            'amount_goal' => $this->amount_goal,
            'deadline' => $this->deadline ?: null,
        ];

        if ($this->projectId) {
            $project = SponsorshipProject::where('user_id', Auth::id())
                ->findOrFail($this->projectId);

            // Only allow editing if in draft status
            if ($project->status !== SponsorshipProject::STATUS_DRAFT) {
                session()->flash('error', 'Cannot edit a project that is not in draft status.');
                return;
            }

            $project = $sponsorshipService->updateproject($project, $data);

            // Sync beneficiaries
            $this->syncBeneficiaries($project);

            session()->flash('message', 'project updated successfully.');
        } else {
            $project = $sponsorshipService->createproject(Auth::user(), $data);

            // Add beneficiaries
            foreach ($this->beneficiaries as $beneficiary) {
                $sponsorshipService->addBeneficiary($project, [
                    'beneficiary_name' => $beneficiary['name'],
                    'beneficiary_type' => $beneficiary['type'],
                    'beneficiary_email' => $beneficiary['email'],
                    'beneficiary_phone' => $beneficiary['phone'],
                    'beneficiary_description' => $beneficiary['description'],
                ]);
            }

            session()->flash('message', 'project created successfully.');
        }

        return redirect()->route('sponsorships.benefactor.index');
    }

    protected function syncBeneficiaries(SponsorshipProject $project)
    {
        $existingIds = collect($this->beneficiaries)->pluck('id')->filter()->toArray();

        // Delete removed beneficiaries
        $project->beneficiaries()->whereNotIn('id', $existingIds)->delete();

        // Update or create beneficiaries
        foreach ($this->beneficiaries as $beneficiary) {
            if ($beneficiary['id']) {
                SponsorshipBeneficiary::where('id', $beneficiary['id'])->update([
                    'beneficiary_name' => $beneficiary['name'],
                    'beneficiary_type' => $beneficiary['type'],
                    'beneficiary_email' => $beneficiary['email'],
                    'beneficiary_phone' => $beneficiary['phone'],
                    'beneficiary_description' => $beneficiary['description'],
                ]);
            } else {
                $project->beneficiaries()->create([
                    'beneficiary_name' => $beneficiary['name'],
                    'beneficiary_type' => $beneficiary['type'],
                    'beneficiary_email' => $beneficiary['email'],
                    'beneficiary_phone' => $beneficiary['phone'],
                    'beneficiary_description' => $beneficiary['description'],
                ]);
            }
        }
    }

    public function addBeneficiary()
    {
        $this->validate([
            'newBeneficiaryName' => 'required|string|min:2|max:255',
            'newBeneficiaryType' => 'required|in:individual,student,group,organization',
            'newBeneficiaryEmail' => 'nullable|email',
        ]);

        $this->beneficiaries[] = [
            'id' => null,
            'name' => $this->newBeneficiaryName,
            'type' => $this->newBeneficiaryType,
            'email' => $this->newBeneficiaryEmail,
            'phone' => $this->newBeneficiaryPhone,
            'description' => $this->newBeneficiaryDescription,
        ];

        $this->resetBeneficiaryForm();
    }

    protected function resetBeneficiaryForm()
    {
        $this->newBeneficiaryName = '';
        $this->newBeneficiaryType = 'individual';
        $this->newBeneficiaryEmail = '';
        $this->newBeneficiaryPhone = '';
        $this->newBeneficiaryDescription = '';
    }

    public function submitForVerification()
    {
        if (!$this->projectId) {
            session()->flash('error', 'Please save the project first.');
            return;
        }

        $project = SponsorshipProject::where('user_id', Auth::id())
            ->findOrFail($this->projectId);

        $sponsorshipService = app(SponsorshipService::class);

        if ($sponsorshipService->submitForVerification($project)) {
            session()->flash('message', 'project submitted for verification.');
            return redirect()->route('benefactors.index');
        }

        session()->flash('error', 'Unable to submit for verification. Please ensure all required fields are filled.');
    }

    public function render()
    {
        return view('livewire.sponsorships.benefactor-project-form', [
            'types' => SponsorshipProject::getTypes(),
            'beneficiaryTypes' => SponsorshipBeneficiary::getTypes(),
        ]);
    }
}
