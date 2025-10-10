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
use App\Services\PaystackService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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

    // Add bank properties
    public $settlement_bank;
    public $bank_code;
    public $account_number;


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
        'student_capacity' => 'required|integer|min:1|max:50000',
        'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        'timezone' => 'nullable|string|max:50',
        'currency' => 'nullable|string|size:3',
        'academic_year_start' => 'nullable|date',
        'academic_year_end' => 'nullable|date|after:academic_year_start',
        'selectedAcademicLevels' => 'required|array|min:1',


        // Add bank validation rules
        'settlement_bank' => 'nullable|string',
        'bank_code' => 'nullable|string',
        'account_number' => 'nullable|string',
    ];

    public function mount()
    {
        // Check if user already has a school
        $user = Auth::user();
        if ($user->school_id) {
            //return redirect()->route('dashboard');
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
        if (!empty($this->bank_code)) {
            $this->settlement_bank = $this->getBankNameFromCode($this->bank_code);
        }

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
            'student_capacity' => $this->student_capacity ?: null,
            'settings' => [
                'timezone' => $this->timezone,
                'currency' => $this->currency,
                'academic_year_start' => $this->academic_year_start,
                'academic_year_end' => $this->academic_year_end,
            ],
        ];

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

        $school = $this->createdSchool;

        if (!empty($this->bank_code)) {
            $subaccountData = [
                'business_name'         => $school->name,
                'bank_code'             => $this->bank_code,
                'account_number'        => $this->account_number,
                'percentage_charge'     => 0,
                'description'           => "Subaccount for {$school->name}",
                'primary_contact_name'  => $school->name,
                'primary_contact_email' => $school->email,
                'primary_contact_phone' => $school->phone,
            ];

            $paystack = app(PaystackService::class);
            $response = $paystack->createSubAccount($subaccountData);

            if (!isset($response['status']) || !$response['status']) {
                throw new \Exception($response['message'] ?? 'Failed to create subaccount.');
            }

            $school->subaccount()->create([
                'subaccount_code'   => $response['data']['subaccount_code'],
                'business_name'     => $response['data']['business_name'],
                'settlement_bank'   => $this->settlement_bank,
                'bank_code'         => $this->bank_code,
                'account_number'    => $response['data']['account_number'],
                'percentage_charge' => $response['data']['percentage_charge'],
                'description'       => $response['data']['description'] ?? null,
                'paystack_response' => $response['data'],
            ]);

            session()->flash('success', 'School created successfully');
        } else {
            // no bank code → skip Paystack
            session()->flash('success', 'School created successfully');
        }

        $this->currentStep = 4;
    } catch (ValidationException $e) {
        $this->currentStep = 1;
        throw $e;
    } catch (\Exception $e) {
        $errorMessage = 'An error occurred';
        if (method_exists($e, 'hasResponse') && $e->hasResponse()) {
            try {
                $responseBody = json_decode($e->getResponse()->getBody()->getContents(), true);
                if (isset($responseBody['message'])) {
                    $errorMessage = $responseBody['message'];
                }
            } catch (\Exception $jsonException) {
                $errorMessage = $e->getMessage();
            }
        } else {
            $errorMessage = $e->getMessage();
        }

        $errorMessage = preg_replace('/Client error:.*response:/', '', $errorMessage);
        $errorMessage = preg_replace('/\(truncated\.\.\.\)/', '', $errorMessage);
        $errorMessage = preg_replace('/SQLSTATE\[.*?\]:.*?Column/', 'Column', $errorMessage);
        $errorMessage = trim($errorMessage);

        session()->flash('error', 'Failed to create school: ' . $errorMessage);
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
            'Greater Accra',
            'Ashanti',
            'Western',
            'Central',
            'Eastern',
            'Volta',
            'Northern',
            'Upper East',
            'Upper West',
            'Brong Ahafo'
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

    private function getBankNameFromCode(string $code): string
    {
        $banks = [
            '030100' => 'Absa Bank Ghana Limited',
            '280100' => 'Access Bank (Ghana) Plc',
            '080100' => 'Agricultural Development Bank Plc',
            '300341' => 'Affinity Ghana Savings and Loans',
            'ATL'    => 'AirtelTigo Money',
            '070101' => 'ARB Apex Bank',
            '210100' => 'Bank of Africa Ghana Limited',
            '010100' => 'Bank of Ghana',
            '300335' => 'Best Point Savings and Loans',
            '140100' => 'CalBank PLC',
            '340100' => 'Consolidated Bank Ghana Limited',
            '130100' => 'Ecobank Ghana Plc',
            '200100' => 'FBNBank Ghana Limited',
            '240100' => 'Fidelity Bank Ghana Limited',
            '170100' => 'First Atlantic Bank Limited',
            '330100' => 'First National Bank Ghana Limited',
            '040100' => 'GCB Bank Limited',
            '230100' => 'Guaranty Trust Bank (Ghana) Limited',
            'MTN'    => 'MTN Mobile Money',
            '050100' => 'National Investment Bank Limited',
            '360100' => 'OmniBSIC Bank Ghana Limited',
            '300457' => 'Paystack Limited',
            '180100' => 'Prudential Bank Limited',
            '110100' => 'Republic Bank (Ghana) PLC',
            '300361' => 'Services Integrity Savings and Loans',
            '090100' => 'Société Générale Ghana Plc',
            '190100' => 'Stanbic Bank Ghana Limited',
            '020100' => 'Standard Chartered Bank Ghana Plc',
            '060100' => 'United Bank for Africa Ghana Limited',
            '100100' => 'Universal Merchant Bank Ghana Limited',
            'VOD'    => 'Vodafone Cash',
            '120100' => 'Zenith Bank Ghana',
        ];

        return $banks[$code] ?? $code;
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
