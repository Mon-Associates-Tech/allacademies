<?php

namespace App\Livewire\Sponsorship;

use App\Models\SponsorshipBeneficiary;
use App\Models\SponsorshipProject;
use App\Services\SponsorshipService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

class BenefactorProjectForm extends Component
{
    use WithFileUploads;

    public $offerProject = null;
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

    // Attachments
    public $images = [];
    public $videos = [];
    public $existingImages = [];
    public $existingVideos = [];
    public $tempImages = [];
    public $tempVideos = [];

    protected $rules = [
        'name' => 'required|string|min:3|max:255',
        'type' => 'required|in:project,cause,scholarship,emergency',
        'description' => 'nullable|string|max:5000',
        'affected_individuals' => 'nullable|string|max:2000',
        'amount_goal' => 'required|numeric|min:1',
        'deadline' => 'nullable|date|after:today',
        'images.*' => 'nullable|image|max:10240',
        'videos.*' => 'nullable|mimes:mp4,mov,avi,wmv|max:204800',
    ];

    public function mount($project = null)
    {
        if ($project) {
            $project = SponsorshipProject::where('user_id', Auth::id())
                ->findOrFail($project);

            $this->offerProject = $project->id;
            $this->name = $project->name;
            $this->type = $project->type;
            $this->description = $project->description;
            $this->affected_individuals = $project->affected_individuals;
            $this->amount_goal = $project->amount_goal;
            $this->deadline = $project->deadline?->format('Y-m-d');

            // Load existing attachments
            $this->existingImages = is_array($project->images) ? $project->images : [];
            $this->existingVideos = is_array($project->videos) ? $project->videos : [];

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

    public function updatedImages()
    {
        $this->validate(['images.*' => 'image|max:10240']);
        $this->tempImages = array_merge($this->tempImages, $this->images);
        $this->images = [];
    }

    public function updatedVideos()
    {
        $this->validate(['videos.*' => 'mimes:mp4,mov,avi,wmv|max:204800']);
        $this->tempVideos = array_merge($this->tempVideos, $this->videos);
        $this->videos = [];
    }

    public function removeTempImage($index)
    {
        unset($this->tempImages[$index]);
        $this->tempImages = array_values($this->tempImages);
    }

    public function removeTempVideo($index)
    {
        unset($this->tempVideos[$index]);
        $this->tempVideos = array_values($this->tempVideos);
    }

    public function removeImage($index)
    {
        if (isset($this->existingImages[$index])) {
            Storage::disk('public')->delete($this->existingImages[$index]);
            unset($this->existingImages[$index]);
            $this->existingImages = array_values($this->existingImages);
        }
    }

    public function removeVideo($index)
    {
        if (isset($this->existingVideos[$index])) {
            Storage::disk('public')->delete($this->existingVideos[$index]);
            unset($this->existingVideos[$index]);
            $this->existingVideos = array_values($this->existingVideos);
        }
    }

    public function save()
    {
        $this->validate([
            'name' => 'required|string|min:3|max:255',
            'type' => 'required|in:project,cause,scholarship,emergency',
            'description' => 'nullable|string|max:5000',
            'affected_individuals' => 'nullable|string|max:2000',
            'amount_goal' => 'required|numeric|min:1',
            'deadline' => 'nullable|date|after:today',
            'images.*' => 'nullable|image|max:10240',
            'videos.*' => 'nullable|mimes:mp4,mov,avi,wmv|max:204800',
        ]);

        if (count($this->tempImages) + count($this->existingImages) > 10) {
            session()->flash('error', 'Maximum 10 images allowed.');
            return;
        }

        if (count($this->tempVideos) + count($this->existingVideos) > 2) {
            session()->flash('error', 'Maximum 2 videos allowed.');
            return;
        }

        $sponsorshipService = app(SponsorshipService::class);

        // Upload new images
        $imagePaths = $this->existingImages;
        foreach ($this->tempImages as $image) {
            $imagePaths[] = $image->store('sponsorship-projects/images', 'public');
        }

        // Upload new videos
        $videoPaths = $this->existingVideos;
        foreach ($this->tempVideos as $video) {
            $videoPaths[] = $video->store('sponsorship-projects/videos', 'public');
        }

        $data = [
            'name' => $this->name,
            'type' => $this->type,
            'description' => $this->description,
            'affected_individuals' => $this->affected_individuals,
            'amount_goal' => $this->amount_goal,
            'deadline' => $this->deadline ?: null,
            'images' => $imagePaths,
            'videos' => $videoPaths,
        ];

        if ($this->offerProject) {
            $project = SponsorshipProject::where('user_id', Auth::id())
                ->findOrFail($this->offerProject);

            // Only allow editing if in draft status
            if ($project->status !== SponsorshipProject::STATUS_DRAFT) {
                session()->flash('error', 'Cannot edit a project that is not in draft status.');
                return;
            }

            $project = $sponsorshipService->updateProject($project, $data);
            $this->syncBeneficiaries($project);
            session()->flash('message', 'Project updated successfully.');
        } else {
            $project = $sponsorshipService->createProject(Auth::user(), $data);

            foreach ($this->beneficiaries as $beneficiary) {
                $sponsorshipService->addBeneficiary($project, [
                    'beneficiary_name' => $beneficiary['name'],
                    'beneficiary_type' => $beneficiary['type'],
                    'beneficiary_email' => $beneficiary['email'],
                    'beneficiary_phone' => $beneficiary['phone'],
                    'beneficiary_description' => $beneficiary['description'],
                ]);
            }

            session()->flash('message', 'Project created successfully.');
        }

        return $this->redirect(route('benefactors.index'), navigate: true);
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
        if (!$this->offerProject) {
            session()->flash('error', 'Please save the project first.');
            return;
        }

        $project = SponsorshipProject::where('user_id', Auth::id())
            ->findOrFail($this->offerProject);

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
