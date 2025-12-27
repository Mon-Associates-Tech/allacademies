<?php

namespace App\Livewire\School;

use App\Models\School;
use App\Models\SchoolSetting;
use App\Models\AcademicPeriod;
use App\Models\AcademicGroup;
use App\Models\AcademicLevel;
use App\Models\AcademicSubject;
use App\Services\ImportExportService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\Validate;

class ComprehensiveSchoolDashboard extends Component
{
    use WithFileUploads;

    public School $school;
    public $activeTab = 'overview';

    // Academic structure data
    public $academicGroups = [];
    public $academicLevels = [];
    public $academicSubjects = [];
    public $periods = [];
    public $currentPeriod = null;

    // Stats
    public $stats = [];

    // School basic info
    public $schoolName;
    public $schoolCode;
    public $schoolEmail;
    public $schoolPhone;
    public $schoolWebsite;
    public $schoolAddress;
    public $schoolCity;
    public $schoolState;
    public $schoolCountry;
    public $postalCode;
    public $schoolType;
    public $schoolDescription;
    public $studentCapacity;
    public $timezone;
    public $currency;

    // Settings
    public $settings = [];
    public $settingGroups = [];

    // Modals
    public $showEditModal = false;
    public $showImportModal = false;
    public $showTemplateModal = false;
    public $editMode = false;

    // Import
    public $importType = 'students';
    public $importFile;
    public $defaultPassword = 'Welcome@2024';

    // Import options
    public $createMissingLevels = true;
    public $createMissingGroups = true;
    public $sendWelcomeEmail = false;

    // Supported import types
    public $importTypes = [
        'students' => 'Students',
        'teachers' => 'Teachers',
        'librarians' => 'Librarians',
        'administrators' => 'Administrators',
        'parents' => 'Parents',
    ];

    protected ImportExportService $importService;

    public function boot(ImportExportService $importService)
    {
        $this->importService = $importService;
    }

    public function mount()
    {
        $this->school = Auth::user()->school;

        if (!$this->school) {
            session()->flash('error', 'No school associated with your account.');
            return redirect()->route('dashboard');
        }

        $this->loadAllData();
    }

    public function loadAllData()
    {
        $this->loadSchoolBasicInfo();
        $this->loadAcademicStructure();
        $this->loadAcademicPeriods();
        $this->loadSettings();
        $this->loadStats();
    }

    public function loadSchoolBasicInfo()
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
        $this->currency = $this->school->currency ?? 'GHS';
    }

    public function loadAcademicStructure()
    {
        // Load Academic Groups with their levels
        $this->academicGroups = $this->school->academicGroups()
            ->with(['academicLevels' => function($query) {
                $query->orderBy('name');
            }])
            ->wherePivot('is_active', true)
            ->get()
            ->map(function ($group) {
                return [
                    'id' => $group->id,
                    'name' => $group->name,
                    'tag' => $group->tag,
                    'levels_count' => $group->academicLevels->count(),
                    'students_count' => $group->students()->where('school_id', $this->school->id)->count(),
                    'teachers_count' => $group->teachers()->count(),
                    'levels' => $group->academicLevels->map(function($level) {
                        return [
                            'id' => $level->id,
                            'name' => $level->name,
                            'label' => $level->label,
                            'students_count' => $level->studentsForSchool($this->school->id)->count(),
                            'subjects_count' => $level->academicSubjects()->count(),
                        ];
                    })
                ];
            })
            ->toArray();

        // Load all Academic Levels for the school
        $this->academicLevels = $this->school->academicLevels()
            ->with(['academicGroup', 'academicSubjects'])
            ->wherePivot('is_active', true)
            ->orderBy('school_academic_level.sort_order')
            ->get()
            ->map(function ($level) {
                return [
                    'id' => $level->id,
                    'name' => $level->name,
                    'label' => $level->label,
                    'group_name' => $level->academicGroup->name ?? 'N/A',
                    'students_count' => $level->studentsForSchool($this->school->id)->count(),
                    'subjects_count' => $level->academicSubjects()->count(),
                    'subjects' => $level->academicSubjects->map(function($subject) {
                        return [
                            'id' => $subject->id,
                            'name' => $subject->name,
                            'code' => $subject->code,
                        ];
                    })
                ];
            })
            ->toArray();

        // Load all Subjects grouped by level
        $this->academicSubjects = AcademicSubject::whereHas('academicLevel.schools', function($query) {
            $query->where('school_id', $this->school->id);
        })
            ->with(['academicLevel.academicGroup'])
            ->orderBy('name')
            ->get()
            ->groupBy('academic_level_id')
            ->map(function($subjects, $levelId) {
                $level = $subjects->first()->academicLevel;
                return [
                    'level_name' => $level->name ?? 'N/A',
                    'group_name' => $level->academicGroup->name ?? 'N/A',
                    'subjects' => $subjects->map(function($subject) {
                        return [
                            'id' => $subject->id,
                            'name' => $subject->name,
                            'code' => $subject->code,
                            'description' => $subject->description,
                        ];
                    })
                ];
            })
            ->toArray();
    }

    public function loadAcademicPeriods()
    {
        $this->periods = $this->school->academicPeriods()
            ->orderBy('academic_year', 'desc')
            ->orderBy('sequence', 'asc')
            ->get()
            ->map(function ($period) {
                return [
                    'id' => $period->id,
                    'title' => $period->title,
                    'type' => $period->type,
                    'sequence' => $period->sequence,
                    'academic_year' => $period->academic_year,
                    'start_date' => $period->start_date->format('M d, Y'),
                    'end_date' => $period->end_date->format('M d, Y'),
                    'status' => $period->status,
                    'is_current' => $period->is_current,
                    'progress' => round($period->getProgressPercentage()),
                    'weeks' => $period->getDurationInWeeks(),
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
            ->toArray();

        $this->settings = SchoolSetting::forSchool($this->school->id)
            ->pluck('value', 'key')
            ->toArray();
    }

    public function loadStats()
    {
        $this->stats = [
            'total_students' => $this->school->students()->count(),
            'active_students' => $this->school->students()->where('status', 'active')->count(),
            'total_teachers' => $this->school->teachers()->count(),
            'active_teachers' => $this->school->teachers()->where('status', 'active')->count(),
            'total_parents' => $this->school->parents()->count(),
            'academic_groups' => $this->school->academicGroups()->wherePivot('is_active', true)->count(),
            'academic_levels' => $this->school->academicLevels()->wherePivot('is_active', true)->count(),
            'total_subjects' => AcademicSubject::whereHas('academicLevel.schools', function($query) {
                $query->where('school_id', $this->school->id);
            })->count(),
            'current_period' => $this->currentPeriod ? $this->currentPeriod->getDisplayName() : 'No active period',
            'total_periods' => $this->school->academicPeriods()->count(),
        ];
    }

    public function setActiveTab($tab)
    {
        $this->activeTab = $tab;
    }

    public function openEditModal()
    {
        $this->showEditModal = true;
        $this->editMode = true;
    }

    public function closeEditModal()
    {
        $this->showEditModal = false;
        $this->editMode = false;
    }

    public function updateSchoolInfo()
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

        session()->flash('success', 'School information updated successfully!');
        $this->closeEditModal();
        $this->loadAllData();
    }

    public function openImportModal()
    {
        $this->showImportModal = true;
        $this->importFile = null;
    }

    public function closeImportModal()
    {
        $this->showImportModal = false;
        $this->importFile = null;
    }

    public function openTemplateModal()
    {
        $this->showTemplateModal = true;
    }

    public function closeTemplateModal()
    {
        $this->showTemplateModal = false;
    }

    public function downloadTemplate($type)
    {
        return redirect()->route('school.download-template', ['type' => $type]);
    }

    public function performImport()
    {
        $this->validate([
            'importFile' => 'required|file|mimes:csv,xlsx,xls|max:10240',
            'importType' => 'required|in:students,teachers,librarians,administrators,parents',
            'defaultPassword' => 'required|string|min:6'
        ]);

        try {
            // Prepare import options
            $options = [
                'default_school_id' => $this->school->id,
                'default_password' => $this->defaultPassword,
                'create_missing_levels' => $this->createMissingLevels,
                'create_missing_groups' => $this->createMissingGroups,
                'send_welcome_email' => $this->sendWelcomeEmail,
            ];

            // Perform import using existing service
            $result = $this->importService->performImport(
                $this->importFile,
                $this->importType,
                $options
            );

            if ($result['success']) {
                $stats = $result['stats'];
                session()->flash('success', "Import completed! Imported: {$stats['imported']}, Skipped: {$stats['skipped']}, Errors: {$stats['errors']}");

                $this->closeImportModal();
                $this->loadAllData();
            } else {
                session()->flash('error', 'Import failed: ' . $result['message']);
            }

        } catch (\Exception $e) {
            session()->flash('error', 'Import failed: ' . $e->getMessage());
        }
    }

    public function refreshData()
    {
        $this->loadAllData();
        session()->flash('success', 'Data refreshed successfully!');
    }

    public function render()
    {
        return view('livewire.school.comprehensive-school-dashboard', [
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
