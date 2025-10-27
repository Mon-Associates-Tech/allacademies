<?php

namespace App\Livewire\School;

use App\Models\School;
use App\Models\SchoolSetting;
use App\Models\AcademicPeriod;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\Validate;
use Carbon\Carbon;

class SchoolSettingsDashboard extends Component
{
    use WithFileUploads;

    public School $school;
    public $activeTab = 'overview';
    public $darkMode = false;

    // School basic settings
    #[Validate('required|string|max:255')]
    public $schoolName;

    #[Validate('nullable|string|max:100')]
    public $schoolCode;

    #[Validate('nullable|email|max:255')]
    public $schoolEmail;

    #[Validate('nullable|string|max:20')]
    public $schoolPhone;

    #[Validate('nullable|url|max:255')]
    public $schoolWebsite;

    #[Validate('nullable|string|max:500')]
    public $schoolAddress;

    #[Validate('nullable|string|max:100')]
    public $schoolCity;

    #[Validate('nullable|string|max:100')]
    public $schoolState;

    #[Validate('nullable|string|max:100')]
    public $schoolCountry;

    #[Validate('nullable|string|max:20')]
    public $postalCode;

    #[Validate('nullable|string|max:50')]
    public $schoolType;

    #[Validate('nullable|string|max:1000')]
    public $schoolDescription;

    #[Validate('nullable|integer|min:1')]
    public $studentCapacity;

    #[Validate('nullable|string|max:100')]
    public $timezone;

    #[Validate('nullable|string|max:10')]
    public $currency;

    #[Validate('nullable|image|max:2048')]
    public $logoFile;

    // Academic Period Management
    public $periods = [];
    public $currentPeriod = null;
    public $showPeriodModal = false;
    public $editingPeriod = null;

    #[Validate('required|string|max:255')]
    public $periodTitle = '';

    #[Validate('required|in:semester,term,quarter,trimester,session')]
    public $periodType = '';

    #[Validate('required|integer|min:1|max:10')]
    public $periodSequence = 1;

    #[Validate('required|date')]
    public $periodStartDate = '';

    #[Validate('required|date|after:periodStartDate')]
    public $periodEndDate = '';

    #[Validate('required|in:upcoming,active,completed,cancelled')]
    public $periodStatus = 'upcoming';

    #[Validate('nullable|string|max:1000')]
    public $periodDescription = '';

    #[Validate('nullable|date')]
    public $registrationStart = '';

    #[Validate('nullable|date')]
    public $registrationEnd = '';

    #[Validate('nullable|date')]
    public $examStart = '';

    #[Validate('nullable|date')]
    public $examEnd = '';

    // Settings management
    public $settings = [];
    public $settingGroups = [];

    // Stats
    public $stats = [];

    public function mount()
    {
        $this->school = Auth::user()->school;

        if (!$this->school) {
            session()->flash('error', 'No school associated with your account.');
            return;
        }

        $this->loadSchoolData();
        $this->loadAcademicPeriods();
        $this->loadSettings();
        $this->loadStats();

        // Check for saved theme preference
        $this->darkMode = session('dark_mode', false);
    }

    public function loadSchoolData()
    {
        $this->schoolName = $this->school->name;
        $this->schoolCode = $this->school->code;
        $this->schoolEmail = $this->school->email;
        $this->schoolPhone = $this->school->phone;
        $this->schoolWebsite = $this->school->website;
        $this->schoolAddress = $this->school->address;
        $this->schoolCity = $this->school->city;
        $this->schoolState = $this->school->state;
        $this->schoolCountry = $this->school->country;
        $this->postalCode = $this->school->postal_code;
        $this->schoolType = $this->school->type;
        $this->schoolDescription = $this->school->description;
        $this->studentCapacity = $this->school->student_capacity;
        $this->timezone = $this->school->timezone ?? 'UTC';
        $this->currency = $this->school->currency ?? 'USD';
    }

    public function loadAcademicPeriods()
    {
        $this->periods = $this->school->academicPeriods()
            ->orderBy('academic_year', 'desc')
            ->orderBy('year_sequence', 'asc')
            ->orderBy('sequence', 'asc')
            ->get()
            ->map(function ($period) {
                return [
                    'id' => $period->id,
                    'title' => $period->getDisplayName(),
                    'type' => $period->type,
                    'sequence' => $period->sequence,
                    'academic_year' => $period->academic_year,
                    'start_date' => $period->start_date->format('Y-m-d'),
                    'end_date' => $period->end_date->format('Y-m-d'),
                    'status' => $period->status,
                    'is_current' => $period->is_current,
                    'progress' => round($period->getProgressPercentage()),
                    'weeks' => $period->total_weeks ?? $period->getDurationInWeeks(),
                    'description' => $period->description,
                    'registration_start' => $period->registration_start?->format('Y-m-d'),
                    'registration_end' => $period->registration_end?->format('Y-m-d'),
                    'exam_start' => $period->exam_start?->format('Y-m-d'),
                    'exam_end' => $period->exam_end?->format('Y-m-d'),
                ];
            })
            ->toArray();

        $this->currentPeriod = $this->school->getCurrentPeriod();
    }

    public function loadSettings()
    {
        $this->settingGroups = SchoolSetting::forSchool($this->school->id)
            ->orderBy('sort_order')
            ->get()
            ->groupBy('group')
            ->map(function ($group) {
                return $group->mapWithKeys(function ($setting) {
                    return [
                        $setting->key => [
                            'key' => $setting->key,
                            'type' => $setting->type,
                            'label' => $setting->label,
                            'value' => $setting->value,
                            'options' => $setting->options ?? [],
                            'description' => $setting->description,
                        ]
                    ];
                });
            })
            ->toArray();

        $this->settings = SchoolSetting::forSchool($this->school->id)
            ->pluck('value', 'key')
            ->toArray();
    }

    public function loadStats()
    {
        $this->stats = $this->school->getStats();
    }

    public function toggleDarkMode()
    {
        $this->darkMode = !$this->darkMode;
        session(['dark_mode' => $this->darkMode]);
    }

    public function setActiveTab($tab)
    {
        $this->activeTab = $tab;
    }

    public function updateSchoolBasicInfo()
    {
        $this->validate([
            'schoolName' => 'required|string|max:255',
            'schoolEmail' => 'nullable|email|max:255',
            'schoolPhone' => 'nullable|string|max:20',
            'schoolWebsite' => 'nullable|url|max:255',
        ]);

        $this->school->update([
            'name' => $this->schoolName,
            'code' => $this->schoolCode,
            'email' => $this->schoolEmail,
            'phone' => $this->schoolPhone,
            'website' => $this->schoolWebsite,
            'address' => $this->schoolAddress,
            'city' => $this->schoolCity,
            'state' => $this->schoolState,
            'country' => $this->schoolCountry,
            'postal_code' => $this->postalCode,
            'type' => $this->schoolType,
            'description' => $this->schoolDescription,
            'student_capacity' => $this->studentCapacity,
            'timezone' => $this->timezone,
            'currency' => $this->currency,
        ]);

        if ($this->logoFile) {
            // Delete old logo if exists
            if ($this->school->logo) {
                Storage::disk('public')->delete($this->school->logo);
            }

            $logoPath = $this->logoFile->store('school-logos', 'public');
            $this->school->update(['logo' => $logoPath]);
        }

        session()->flash('success', 'School information updated successfully!');
        $this->loadStats();
        $this->loadSchoolData();
    }

    public function createAcademicPeriod()
    {
        $this->resetPeriodForm();
        $this->showPeriodModal = true;
        $this->editingPeriod = null;
    }

    public function editAcademicPeriod($periodId)
    {
        $period = AcademicPeriod::where('school_id', $this->school->id)
            ->findOrFail($periodId);

        $this->editingPeriod = $period;
        $this->periodTitle = $period->title;
        $this->periodType = $period->type;
        $this->periodSequence = $period->sequence;
        $this->periodStartDate = $period->start_date->format('Y-m-d');
        $this->periodEndDate = $period->end_date->format('Y-m-d');
        $this->periodStatus = $period->status;
        $this->periodDescription = $period->description;
        $this->registrationStart = $period->registration_start?->format('Y-m-d');
        $this->registrationEnd = $period->registration_end?->format('Y-m-d');
        $this->examStart = $period->exam_start?->format('Y-m-d');
        $this->examEnd = $period->exam_end?->format('Y-m-d');

        $this->showPeriodModal = true;
    }

    public function saveAcademicPeriod()
    {
        $this->validate([
            'periodTitle' => 'required|string|max:255',
            'periodType' => 'required|in:semester,term,quarter,trimester,session',
            'periodSequence' => 'required|integer|min:1|max:10',
            'periodStartDate' => 'required|date',
            'periodEndDate' => 'required|date|after:periodStartDate',
            'periodStatus' => 'required|in:upcoming,active,completed,cancelled',
        ]);

        $data = [
            'title' => $this->periodTitle,
            'type' => $this->periodType,
            'sequence' => $this->periodSequence,
            'start_date' => $this->periodStartDate,
            'end_date' => $this->periodEndDate,
            'status' => $this->periodStatus,
            'description' => $this->periodDescription,
            'registration_start' => $this->registrationStart ?: null,
            'registration_end' => $this->registrationEnd ?: null,
            'exam_start' => $this->examStart ?: null,
            'exam_end' => $this->examEnd ?: null,
        ];

        if ($this->editingPeriod) {
            $this->editingPeriod->update($data);
            session()->flash('success', 'Academic period updated successfully!');
        } else {
            $this->school->createAcademicPeriod($data);
            session()->flash('success', 'Academic period created successfully!');
        }

        $this->loadAcademicPeriods();
        $this->loadStats();
        $this->showPeriodModal = false;
        $this->resetPeriodForm();
    }

    public function deleteAcademicPeriod($periodId)
    {
        $period = AcademicPeriod::where('school_id', $this->school->id)
            ->findOrFail($periodId);

        $period->delete();

        $this->loadAcademicPeriods();
        $this->loadStats();
        session()->flash('success', 'Academic period deleted successfully!');
    }

    public function setCurrentPeriod($periodId)
    {
        AcademicPeriod::where('school_id', $this->school->id)
            ->update(['is_current' => false]);

        AcademicPeriod::where('school_id', $this->school->id)
            ->findOrFail($periodId)
            ->update(['is_current' => true]);

        $this->loadAcademicPeriods();
        $this->loadStats();
        session()->flash('success', 'Current academic period updated!');
    }

    public function updateSetting($key, $value, $group)
    {
        SchoolSetting::updateOrCreate(
            [
                'school_id' => $this->school->id,
                'key' => $key,
            ],
            [
                'value' => $value,
                'group' => $group,
            ]
        );

        $this->loadSettings();
        session()->flash('success', 'Setting updated successfully!');
    }

    private function resetPeriodForm()
    {
        $this->periodTitle = '';
        $this->periodType = 'semester';
        $this->periodSequence = 1;
        $this->periodStartDate = '';
        $this->periodEndDate = '';
        $this->periodStatus = 'upcoming';
        $this->periodDescription = '';
        $this->registrationStart = '';
        $this->registrationEnd = '';
        $this->examStart = '';
        $this->examEnd = '';
        $this->editingPeriod = null;
    }

    public function render()
    {
        return view('livewire.school.school-settings-dashboard', [
            'schoolInitials' => $this->getSchoolInitials(),
            'schoolLogo' => $this->school->logo ? Storage::url($this->school->logo) : null,
        ]);
    }

    private function getSchoolInitials()
    {
        $words = explode(' ', $this->school->name);
        if (count($words) >= 2) {
            return strtoupper(substr($words[0], 0, 1) . substr($words[1], 0, 1));
        }
        return strtoupper(substr($this->school->name, 0, 2));
    }
}
