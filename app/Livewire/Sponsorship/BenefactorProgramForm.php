<?php

namespace App\Livewire\Sponsorship;

use App\Models\SponsorshipBeneficiary;
use App\Models\SponsorshipProgram;
use App\Services\SponsorshipService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class BenefactorProgramForm extends Component
{
    public $programId = null;
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

    public function mount($programId = null)
    {
        if ($programId) {
            $program = SponsorshipProgram::where('user_id', Auth::id())
                ->findOrFail($programId);

            $this->programId = $program->id;
            $this->name = $program->name;
            $this->type = $program->type;
            $this->description = $program->description;
            $this->affected_individuals = $program->affected_individuals;
            $this->amount_goal = $program->amount_goal;
            $this->deadline = $program->deadline?->format('Y-m-d');

            // Load existing beneficiaries
            $this->beneficiaries = $program->beneficiaries->map(function ($b) {
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

    public function removeBeneficiary($index)
    {
        unset($this->beneficiaries[$index]);
        $this->beneficiaries = array_values($this->beneficiaries);
    }

    protected function resetBeneficiaryForm()
    {
        $this->newBeneficiaryName = '';
        $this->newBeneficiaryType = 'individual';
        $this->newBeneficiaryEmail = '';
        $this->newBeneficiaryPhone = '';
        $this->newBeneficiaryDescription = '';
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

        if ($this->programId) {
            $program = SponsorshipProgram::where('user_id', Auth::id())
                ->findOrFail($this->programId);

            // Only allow editing if in draft status
            if ($program->status !== SponsorshipProgram::STATUS_DRAFT) {
                session()->flash('error', 'Cannot edit a program that is not in draft status.');
                return;
            }

            $program = $sponsorshipService->updateProgram($program, $data);

            // Sync beneficiaries
            $this->syncBeneficiaries($program);

            session()->flash('message', 'Program updated successfully.');
        } else {
            $program = $sponsorshipService->createProgram(Auth::user(), $data);

            // Add beneficiaries
            foreach ($this->beneficiaries as $beneficiary) {
                $sponsorshipService->addBeneficiary($program, [
                    'beneficiary_name' => $beneficiary['name'],
                    'beneficiary_type' => $beneficiary['type'],
                    'beneficiary_email' => $beneficiary['email'],
                    'beneficiary_phone' => $beneficiary['phone'],
                    'beneficiary_description' => $beneficiary['description'],
                ]);
            }

            session()->flash('message', 'Program created successfully.');
        }

        return redirect()->route('sponsorship.benefactor.dashboard');
    }

    protected function syncBeneficiaries(SponsorshipProgram $program)
    {
        $existingIds = collect($this->beneficiaries)->pluck('id')->filter()->toArray();

        // Delete removed beneficiaries
        $program->beneficiaries()->whereNotIn('id', $existingIds)->delete();

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
                $program->beneficiaries()->create([
                    'beneficiary_name' => $beneficiary['name'],
                    'beneficiary_type' => $beneficiary['type'],
                    'beneficiary_email' => $beneficiary['email'],
                    'beneficiary_phone' => $beneficiary['phone'],
                    'beneficiary_description' => $beneficiary['description'],
                ]);
            }
        }
    }

    public function submitForVerification()
    {
        if (!$this->programId) {
            session()->flash('error', 'Please save the program first.');
            return;
        }

        $program = SponsorshipProgram::where('user_id', Auth::id())
            ->findOrFail($this->programId);

        $sponsorshipService = app(SponsorshipService::class);

        if ($sponsorshipService->submitForVerification($program)) {
            session()->flash('message', 'Program submitted for verification.');
            return redirect()->route('sponsorship.benefactor.dashboard');
        }

        session()->flash('error', 'Unable to submit for verification. Please ensure all required fields are filled.');
    }

    public function render()
    {
        return view('livewire.sponsorship.benefactor-program-form', [
            'types' => SponsorshipProgram::getTypes(),
            'beneficiaryTypes' => SponsorshipBeneficiary::getTypes(),
        ]);
    }
}
