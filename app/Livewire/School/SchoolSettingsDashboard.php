<?php

namespace App\Livewire\School;

use App\Constants\GhanaBanks;
use App\Models\School;
use App\Models\SchoolSetting;
use App\Models\AcademicPeriod;
use App\Models\AcademicYear;
use App\Services\ImportExportService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\Validate;
use Carbon\Carbon;

class SchoolSettingsDashboard extends Component
{
    use WithFileUploads;

    public ?School $school;
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

    // Academic Year Management
    public $academicYears = [];
    public $showAcademicYearModal = false;
    public $editingYearId = null;

    #[Validate('required|string|max:255')]
    public $yearName = '';

    #[Validate('required|date')]
    public $yearStartDate = '';

    #[Validate('required|date|after:yearStartDate')]
    public $yearEndDate = '';

    #[Validate('required|in:upcoming,active,completed')]
    public $yearStatus = 'upcoming';

    #[Validate('nullable|string|max:1000')]
    public $yearDescription = '';

    // Academic Period Management
    public $periods = [];
    public $currentPeriod = null;
    public $showPeriodModal = false;
    public $editingPeriod = null;

    #[Validate('required|exists:academic_years,id')]
    public $periodAcademicYearId = '';

    #[Validate('required|string|max:255')]
    public $periodName = '';

    #[Validate('required|in:semester,term,quarter,trimester,session')]
    public $periodType = 'term';

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

    // Account Information Management
    public $showAccountModal = false;
    public $editingAccountId = null;
    public $accountBank = '';
    public $accountBankCode = '';
    public $accountNumber = '';
    public $accountName = '';
    public $accountType = 'bank'; // bank, mobile_money
    public $isPrimaryAccount = false;

    // Import functionality
    public $showImportModal = false;
    public $showTemplateModal = false;
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
        $user = Auth::user();

        // For owners/super admins, get school from context, otherwise get from user's school
        if ($user->isOwner() || $user->isSuperAdmin()) {
            // Check for selected school context
            $schoolId = session('current_school_id');

            if ($schoolId) {
                $this->school = School::find($schoolId);
            } else {
                // No school selected - they need to select one
                $this->school = null;
            }
        } else {
            // Regular users get their assigned school
            $this->school = $user->school;
        }

        if (!$this->school) {
            session()->flash('error', 'Please select a school to manage its settings.');
            return redirect(route('dashboard'));
        }

        // Restore active tab from session or default to 'overview'
        $this->activeTab = session('school_settings_active_tab', 'overview');


        $this->loadSchoolData();
        $this->loadAcademicYears();
        $this->loadAcademicPeriods();
        $this->loadSettings();
        $this->loadStats();
        $this->loadAccountInformation();

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
        $this->currency = $this->school->currency ?? 'GHS';
    }

    public function loadAcademicYears()
    {
        $this->academicYears = AcademicYear::where('school_id', $this->school->id)
            ->orderBy('start_date', 'desc')
            ->get()
            ->toArray();
    }

    public function loadAcademicPeriods(): void
    {
        $this->periods = $this->school->academicPeriods()
            ->with('academicYear')
            ->orderBy('start_date', 'desc')
            ->get()
            ->map(function ($period) {
                return [
                    'id' => $period->id,
                    'name' => $period->getDisplayName(),
                    'type' => $period->type,
                    'sequence' => $period->sequence,
                    'academic_year' => $period->academic_year,
                    'academic_year_name' => $period->academicYear?->getDisplayName() ?? 'N/A',
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

    public function loadSettings(): void
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

    public function loadStats(): void
    {
        $this->stats = $this->school->getStats();
    }

    public function loadAccountInformation(): void
    {
        // Load existing subaccount if available
        $subaccount = $this->school->subaccount;

        if ($subaccount) {
            $this->accountBank = $subaccount->settlement_bank ?? '';
            $this->accountBankCode = $subaccount->bank_code ?? '';
            $this->accountNumber = $subaccount->account_number ?? '';
            $this->accountName = $subaccount->business_name ?? '';
        }
    }

    public function createAccount(): void
    {
        $this->resetAccountForm();
        $this->showAccountModal = true;
    }

    public function editAccount(): void
    {
        $subaccount = $this->school->subaccount;

        if ($subaccount) {
            $this->editingAccountId = $subaccount->id;
            $this->accountBank = $subaccount->settlement_bank ?? '';
            $this->accountBankCode = $subaccount->bank_code ?? '';
            $this->accountNumber = $subaccount->account_number ?? '';
            $this->accountName = $subaccount->business_name ?? $this->school->name;
            $this->showAccountModal = true;
        }
    }

    public function saveAccount(): void
    {
        $this->validate([
            'accountBankCode' => 'required|string',
            'accountNumber' => 'required|string',
            'accountName' => 'nullable|string|max:255',
        ]);

        try {
            // Get bank name from code
            $this->accountBank = $this->getBankNameFromCode($this->accountBankCode);

            $paystack = app(\App\Services\PaystackService::class);

            if ($this->editingAccountId) {
                // Update existing subaccount
                $subaccount = $this->school->subaccount;

                $updateData = [
                    'business_name' => $this->accountName ?: $this->school->name,
                     'bank_code' => $this->accountBankCode,
                    'account_number' => $this->accountNumber,
                ];

                $response = $paystack->updateSubAccount($subaccount->subaccount_code, $updateData);

                if (isset($response['status']) && $response['status']) {
                    $subaccount->update([
                        'business_name' => $this->accountName ?: $this->school->name,
                        'settlement_bank' => $this->accountBank,
                        'bank_code' => $this->accountBankCode,
                        'account_number' => $this->accountNumber,
                        'paystack_response' => $response['data'] ?? null,
                    ]);

                    session()->flash('success', 'Account information updated successfully!');
                } else {
                    throw new \Exception($response['message'] ?? 'Failed to update account');
                }
            } else {
                // Create new subaccount
                $subaccountData = [
                    'business_name' => $this->accountName ?: $this->school->name,
                    'bank_code' => $this->accountBankCode,
                    'account_number' => $this->accountNumber,
                    'percentage_charge' => 0,
                    'description' => "Payment account for {$this->school->name}",
                    'primary_contact_name' => $this->school->name,
                    'primary_contact_email' => $this->school->email,
                    'primary_contact_phone' => $this->school->phone,
                ];

                $response = $paystack->createSubAccount($subaccountData);

                if (isset($response['status']) && $response['status']) {
                    $this->school->subaccount()->create([
                        'subaccount_code' => $response['data']['subaccount_code'],
                        'business_name' => $this->accountName ?: $this->school->name,
                        'settlement_bank' => $this->accountBank,
                         'bank_code' => $this->accountBankCode,
                        'account_number' => $this->accountNumber,
                        'percentage_charge' => $response['data']['percentage_charge'] ?? 0,
                        'description' => $response['data']['description'] ?? null,
                        'paystack_response' => $response['data'],
                    ]);

                    session()->flash('success', 'Account information added successfully!');
                } else {
                    throw new \Exception($response['message'] ?? 'Failed to create account');
                }
            }

            $this->showAccountModal = false;
            $this->resetAccountForm();
            $this->loadAccountInformation();
        } catch (\Exception $e) {
            $errorMessage = 'Failed to save account: ' . $e->getMessage();
            session()->flash('error', $errorMessage);
        }
    }

    public function deleteAccount(): void
    {
        try {
            $subaccount = $this->school->subaccount;

            if ($subaccount) {
                // Note: Paystack doesn't allow deleting subaccounts via API
                // You may want to mark it as inactive instead
                $subaccount->delete();

                session()->flash('success', 'Account information removed successfully!');
                $this->loadAccountInformation();
            }
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to delete account: ' . $e->getMessage());
        }
    }

    private function resetAccountForm(): void
    {
        $this->accountBank = '';
        $this->accountBankCode = '';
        $this->accountNumber = '';
        $this->accountName = '';
        $this->accountType = 'bank';
        $this->isPrimaryAccount = false;
        $this->editingAccountId = null;
    }

    private function getBankNameFromCode(string $code): string
    {
        return GhanaBanks::getNameFromCode($code);
    }
    public function toggleDarkMode()
    {
        $this->darkMode = !$this->darkMode;
        session(['dark_mode' => $this->darkMode]);
    }

    public function setActiveTab($tab)
    {
        $this->activeTab = $tab;
        // Save the active tab to session
        session(['school_settings_active_tab' => $tab]);
    }

    #[On('tabChanged')]
    public function handleTabChange($tab)
    {
        $this->activeTab = $tab;
        session(['school_settings_active_tab' => $tab]);
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

    // Academic Year Management Methods
    public function createAcademicYear()
    {
        $this->resetAcademicYearForm();
        $this->showAcademicYearModal = true;
    }

    public function editAcademicYear($yearId)
    {
        $year = AcademicYear::where('school_id', $this->school->id)
            ->findOrFail($yearId);

        $this->editingYearId = $yearId;
        $this->yearName = $year->name;
        $this->yearStartDate = $year->start_date->format('Y-m-d');
        $this->yearEndDate = $year->end_date->format('Y-m-d');
        $this->yearStatus = $year->status ?? 'active';
        $this->yearDescription = $year->description;

        $this->showAcademicYearModal = true;
    }

    public function saveAcademicYear()
    {
        $this->validate([
            'yearName' => 'required|string|max:255',
            'yearStartDate' => 'required|date',
            'yearEndDate' => 'required|date|after:yearStartDate',
            'yearStatus' => 'required|in:upcoming,active,completed',
        ]);

        $data = [
            'school_id' => $this->school->id,
            'name' => $this->yearName,
            'start_date' => $this->yearStartDate,
            'end_date' => $this->yearEndDate,
            'status' => $this->yearStatus,
            'description' => $this->yearDescription,
        ];

        if ($this->editingYearId) {
            $year = AcademicYear::findOrFail($this->editingYearId);
            $year->update($data);
            session()->flash('success', 'Academic year updated successfully!');
        } else {
            AcademicYear::create($data);
            session()->flash('success', 'Academic year created successfully!');
        }

        $this->loadAcademicYears();
        $this->loadStats();
        $this->showAcademicYearModal = false;
        $this->resetAcademicYearForm();
    }

    public function deleteAcademicYear($yearId)
    {
        $year = AcademicYear::where('school_id', $this->school->id)
            ->findOrFail($yearId);

        // Check if there are periods associated with this year
        $periodsCount = $year->academicPeriods()->count();

        if ($periodsCount > 0) {
            session()->flash('error', "Cannot delete academic year. It has {$periodsCount} academic period(s) associated with it.");
            return;
        }

        $year->delete();

        $this->loadAcademicYears();
        $this->loadStats();
        session()->flash('success', 'Academic year deleted successfully!');
    }

    public function setCurrentAcademicYear($yearId)
    {
        AcademicYear::where('school_id', $this->school->id)
            ->update(['is_current' => false]);

        AcademicYear::where('school_id', $this->school->id)
            ->findOrFail($yearId)
            ->update(['is_current' => true]);

        $this->loadAcademicYears();
        session()->flash('success', 'Current academic year updated!');
    }

    private function resetAcademicYearForm()
    {
        $this->yearName = '';
        $this->yearStartDate = '';
        $this->yearEndDate = '';
        $this->yearStatus = 'upcoming';
        $this->yearDescription = '';
        $this->editingYearId = null;
    }

    // Academic Period Management Methods
    public function createAcademicPeriod(): void
    {
        $this->resetPeriodForm();
        $this->showPeriodModal = true;
        $this->editingPeriod = null;
    }

    public function editAcademicPeriod($periodId): void
    {
        $period = AcademicPeriod::where('school_id', $this->school->id)
            ->with('academicYear')
            ->findOrFail($periodId);

        $this->editingPeriod = $period;
        $this->periodAcademicYearId = $period->academic_year_id ?? '';
        $this->periodName = $period->name;
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

    public function saveAcademicPeriod(): void
    {
        $this->validate([
            'periodAcademicYearId' => 'required|exists:academic_years,id',
            'periodName' => 'required|string|max:255',
            'periodType' => 'required|in:semester,term,quarter,trimester,session',
            'periodSequence' => 'required|integer|min:1|max:10',
            'periodStartDate' => 'required|date',
            'periodEndDate' => 'required|date|after:periodStartDate',
            'periodStatus' => 'required|in:upcoming,active,completed,cancelled',
        ]);

        $data = [
            'school_id' => $this->school->id,
            'academic_year_id' => $this->periodAcademicYearId,
            'name' => $this->periodName,
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
        $this->loadAcademicYears();
        $this->loadStats();
        $this->showPeriodModal = false;
        $this->resetPeriodForm();
    }

    public function deleteAcademicPeriod($periodId): void
    {
        $period = AcademicPeriod::where('school_id', $this->school->id)
            ->findOrFail($periodId);

        $period->delete();

        $this->loadAcademicPeriods();
        $this->loadStats();
        session()->flash('success', 'Academic period deleted successfully!');
    }

    public function setCurrentPeriod($periodId): void
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

    public function updateSetting($key, $value, $group): void
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

    private function resetPeriodForm(): void
    {
        $this->periodAcademicYearId = '';
        $this->periodName = '';
        $this->periodType = 'term';
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

        $tabs = [
            ['key' => 'overview', 'label' => 'Overview'],
            ['key' => 'basic-info', 'label' => 'Basic Information'],
            ['key' => 'academic-periods', 'label' => 'Academic Periods'],
            ['key' => 'system-settings', 'label' => 'System Settings'],
            ['key' => 'account-information', 'label' => 'Account Information'],
            ['key' => 'fee-structure', 'label' => 'Fee Structure'],
            ['key' => 'letterheads', 'label' => 'Letterheads'],
        ];

        return view('livewire.school.school-settings-dashboard', [
            'schoolInitials' => $this->getSchoolInitials(),
            'schoolLogo' => $this->school->logo ? Storage::url($this->school->logo) : null,
            'tabs' => $tabs,
        ]);
    }

    private function getSchoolInitials(): string
    {
        $words = explode(' ', $this->school->name);
        if (count($words) >= 2) {
            return strtoupper(substr($words[0], 0, 1) . substr($words[1], 0, 1));
        }
        return strtoupper(substr($this->school->name, 0, 2));
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

     //   $this->validate([
      //      'importFile' => 'required|file|mimes:csv,xlsx,xls|max:10240',
     //       'importType' => 'required|in:students,teachers,librarians,administrators,parents',
      //      'defaultPassword' => 'required|string|min:6'
      //  ]);

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
                $this->loadStats();
            } else {
                session()->flash('error', 'Import failed: ' . $result['message']);
                logError('error '. json_encode($result));
            }

        } catch (\Exception $e) {
            session()->flash('error', 'Import failed: ' . $e->getMessage());
            logError('error: '. $e->getMessage());
        }
    }
}
