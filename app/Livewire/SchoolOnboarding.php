<?php

namespace App\Livewire;

use App\Models\AcademicGroup;
use App\Models\AcademicLevel;
use App\Services\SchoolOnboardingService;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Livewire\Component;
use Livewire\WithFileUploads;

class SchoolOnboarding extends Component
{
    use WithFileUploads;

    public $currentStep = 1;
    public $totalSteps = 3;

// School basic information
    public $name = '';
    public $email = '';
    public $phone = '';
    public $website = '';
    public $type = '';
    public $ownership = '';

// Address information
    public $address = '';
    public $city = '';
    public $state = '';
    public $country = 'Ghana';
    public $postal_code = '';

// Additional information
    public $description = '';
    public $established_date = '';
    public $student_capacity = '';
    public $logo;
    public $logoPreview = null;

// Academic groups selection (from existing groups)
    public $selectedAcademicGroups = [];
    public $selectedAcademicLevels = [];

// Settings
    public $timezone = 'Africa/Accra';
    public $currency = 'GHS';
    public $academic_year_start = '';
    public $academic_year_end = '';

// State management
    public $loading = false;
    public $createdSchool = null;

    protected $rules = [
        'name' => 'required|string|max:255|unique:schools,name',
        'email' => 'required|email|unique:schools,email',
        'phone' => 'nullable|string|max:20',
        'website' => 'nullable|url|max:255',
        'type' => 'required|in:primary,secondary,tertiary,mixed',
        'ownership' => 'required|in:public,private,mission',
        'address' => 'required|string|max:500',
        'city' => 'required|string|max:100',
        'state' => 'required|string|max:100',
        'country' => 'required|string|max:100',
        'postal_code' => 'nullable|string|max:20',
        'description' => 'nullable|string|max:1000',
        'established_date' => 'nullable|date|before_or_equal:today',
        'student_capacity' => 'nullable|integer|min:1|max:50000',
        'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        'timezone' => 'nullable|string|max:50',
        'currency' => 'nullable|string|size:3',
        'academic_year_start' => 'nullable|date',
        'academic_year_end' => 'nullable|date|after:academic_year_start',
    ];

    public function mount()
    {
// Check if user already has a school
        $user = Auth::user();
        if ($user->school_id) {
            return redirect()->route('dashboard');
        }

// Set default academic year dates
        $this->academic_year_start = now()->startOfYear()->format('Y-m-d');
        $this->academic_year_end = now()->endOfYear()->format('Y-m-d');
    }

    public function updatedLogo()
    {
        $this->validateOnly('logo');

        if ($this->logo) {
            $this->logoPreview = $this->logo->temporaryUrl();
        }
    }

    public function nextStep()
    {
        $this->validateCurrentStep();

        if ($this->currentStep < $this->totalSteps) {
            $this->currentStep++;
        }
    }

    public function validateCurrentStep()
    {
        if ($this->currentStep === 1) {
            $this->validate([
                'name' => 'required|string|max:255|unique:schools,name',
                'email' => 'required|email|unique:schools,email',
                'type' => 'required|in:primary,secondary,tertiary,mixed',
                'ownership' => 'required|in:public,private,mission',
                'address' => 'required|string|max:500',
                'city' => 'required|string|max:100',
                'state' => 'required|string|max:100',
                'country' => 'required|string|max:100',
            ]);
        }
    }

    public function previousStep()
    {
        if ($this->currentStep > 1) {
            $this->currentStep--;
        }
    }

    public function createSchool(): void
    {
        $this->loading = true;

        try {
            $this->validate();

            $schoolData = [
                'name' => $this->name,
                'email' => $this->email,
                'phone' => $this->phone,
                'website' => $this->website,
                'type' => $this->type,
                'ownership' => $this->ownership,
                'address' => $this->address,
                'city' => $this->city,
                'state' => $this->state,
                'status' => 'active',
                'country' => $this->country,
                'postal_code' => $this->postal_code,
                'description' => $this->description,
                'established_date' => $this->established_date,
                'student_capacity' => $this->student_capacity,
                'settings' => [
                    'timezone' => $this->timezone,
                    'currency' => $this->currency,
                    'academic_year_start' => $this->academic_year_start,
                    'academic_year_end' => $this->academic_year_end,
                ],
            ];

// Handle logo upload
            if ($this->logo) {
                $logoPath = $this->logo->store('schools/logos', 'public');
                $schoolData['logo_url'] = $logoPath;
            }

            $onboardingService = app(SchoolOnboardingService::class);
            $this->createdSchool = $onboardingService->createSchool(
                $schoolData,
                Auth::user(),
                $this->selectedAcademicGroups,
                $this->selectedAcademicLevels
            );

            $this->currentStep = 3;

            session()->flash('success', 'School created successfully!');

        } catch (ValidationException $e) {
            $this->currentStep = 1; // Go back to first step if validation fails
            throw $e;
        } catch (Exception $e) {
            session()->flash('error', 'Failed to create school: ' . $e->getMessage());
        } finally {
            $this->loading = false;
        }
    }

    public function completeOnboarding()
    {
        try {
            $onboardingService = app(SchoolOnboardingService::class);
            $onboardingService->completeOnboarding($this->createdSchool, Auth::user());

            return redirect()->route('dashboard')->with('success', 'Welcome to your school dashboard!');
        } catch (Exception $e) {
            session()->flash('error', 'Failed to complete onboarding: ' . $e->getMessage());
        }
    }

// Computed properties for form options
    public function getSchoolTypesProperty()
    {
        return [
            'primary' => 'Primary School',
            'secondary' => 'Secondary School',
            'tertiary' => 'Tertiary Institution',
            'mixed' => 'Mixed (Primary & Secondary)'
        ];
    }

    public function getOwnershipTypesProperty()
    {
        return [
            'public' => 'Public School',
            'private' => 'Private School',
            'mission' => 'Mission School'
        ];
    }

    public function getGhanaRegionsProperty()
    {
        return [
            'Greater Accra', 'Ashanti', 'Western', 'Central', 'Eastern',
            'Volta', 'Northern', 'Upper East', 'Upper West', 'Brong Ahafo'
        ];
    }

    public function getCurrenciesProperty()
    {
        return [
            'GHS' => 'Ghana Cedi (GHS)',
            'USD' => 'US Dollar (USD)',
            'EUR' => 'Euro (EUR)'
        ];
    }

    public function getTimezonesProperty()
    {
        return [
            'Africa/Accra' => 'Africa/Accra (GMT)',
            'UTC' => 'UTC'
        ];
    }

    public function getAvailableAcademicGroupsProperty()
    {
        return AcademicGroup::orderBy('name')->get();
    }

    public function getAvailableAcademicLevelsProperty()
    {
        if (empty($this->selectedAcademicGroups)) {
            return collect();
        }

        return AcademicLevel::whereIn('academic_group_id', $this->selectedAcademicGroups)
            ->orderBy('name')
            ->get();
    }

    public function render()
    {
        return view('livewire.school-onboarding');
    }
}
