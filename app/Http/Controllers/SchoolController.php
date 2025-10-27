<?php

namespace App\Http\Controllers;

use App\Models\School;
use App\Models\Subaccount;
use App\Services\PaystackService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\SchoolFee;
use App\Models\AcademicGroup;;

use App\Models\AcademicLevel;
use App\Models\AcademicPeriod;
use App\Models\AcademicFeeStructure;
use Illuminate\Support\Facades\Auth;

class SchoolController extends Controller
{
    protected $paystack;

    public function __construct(PaystackService $paystack)
    {
        $this->paystack = $paystack;
    }

    // Show create school form
    public function create()
    {
        //  $schools = School::with('subaccount')->orderBy('name')->get();
        $schools = School::with('subaccount')->orderBy('name')->paginate(10);
        return view('payments.school-fees.create', compact('schools'));
    }



    // Store new school + create subaccount
    public function store(Request $request)
    {
        $request->validate([
            'name'            => 'required|string|max:255',
            'email'           => 'required|email|unique:schools,email',
            'phone'           => 'nullable|string|max:20',
            'address'         => 'nullable|string|max:255',
            'bank_code'       => 'required|string',
            'settlement_bank' => 'required|string',
            'account_number' => 'required|numeric|digits_between:8,16',
        ]);

        // Step 1: Create school record
        $school = School::create($request->only(['name', 'email', 'phone', 'address']));

        // Step 2: Create subaccount on Paystack
        $subaccountData = [
            'business_name'        => $school->name,
            'bank_code'      => $request->bank_code,   // from <select>
            'account_number'       => $request->account_number,
            'percentage_charge'    => 0, // system commission %
            'description'          => "Subaccount for {$school->name}",
            'primary_contact_name' => $school->name,
            'primary_contact_email' => $school->email,
            'primary_contact_phone' => $school->phone,
        ];

        $response = $this->paystack->createSubAccount($subaccountData);

        if (!isset($response['status']) || !$response['status']) {
            return back()->with('error', $response['message'] ?? 'Failed to create subaccount.');
        }

        // Step 3: Save subaccount to DB
        $school->subaccount()->create([
            'subaccount_code'   => $response['data']['subaccount_code'],
            'business_name'     => $response['data']['business_name'],
            'settlement_bank'   => $request->settlement_bank,
            'bank_code'        =>  $request->bank_code,
            'account_number'    => $response['data']['account_number'],
            'percentage_charge' => $response['data']['percentage_charge'],
            'description'       => $response['data']['description'] ?? null,
            'paystack_response' => $response['data'], // full JSON response
        ]);

        return redirect()
            ->route('schools.create')
            ->with('success', 'School and Subaccount created successfully.');
    }



    public function collectFees(School $school)
    {
        if (!$school->subaccount) {
            return back()->with('error', 'This school has no subaccount.');
        }

        $data = [
            'email'            => $school->email,
            'amount'           => 100000, // 1000 GHS in kobo
            'subaccount'       => $school->subaccount->subaccount_code,
            'metadata'         => [
                'school_id' => $school->id,
                'school'    => $school->name,
            ],
            'callback_url'     => route('schoolfees.callback'),
        ];

        $response = $this->paystack->initializeTransaction($data);

        if (!isset($response['status']) || !$response['status']) {
            return back()->with('error', $response['message'] ?? 'Payment initialization failed.');
        }

        return redirect($response['data']['authorization_url']);
    }


    public function schoolFeesCallback(Request $request)
    {
        $reference = $request->query('reference');

        // Step 1: Verify with Paystack
        $response = $this->paystack->verifyTransaction($reference);

        if (!$response['status'] || $response['data']['status'] !== 'success') {
            return redirect()
                ->route('schools.create') // or a "schools.index" page
                ->with('error', 'Payment failed or was cancelled.');
        }

        $paymentDetails = $response['data'];

        // Step 2: Get school info from metadata
        $schoolId = $paymentDetails['metadata']['school_id'] ?? null;
        $school   = School::find($schoolId);

        if (!$school) {
            return redirect()
                ->route('schools.create')
                ->with('error', 'Invalid school in payment metadata.');
        }

        // Step 3: Save payment in school_fees table
        DB::transaction(function () use ($school, $paymentDetails) {
            SchoolFee::create([
                'school_id'         => $school->id,
                'school_name'       => $school->name,
                'amount'            => $paymentDetails['amount'], // Paystack returns in pesewas
                'currency'          => $paymentDetails['currency'] ?? 'GHS',
                'status'            => $paymentDetails['status'], // success, failed
                'reference'         => $paymentDetails['reference'],
                'authorization_url' => $paymentDetails['authorization_url'] ?? null,
                //'paystack_response' => $paymentDetails,
            ]);
        });

        return redirect()
            ->route('schools.create')
            ->with('success', 'School fees payment successful! Ref: ' . $paymentDetails['reference']);
    }


    public function showFeeSetupForm()
    {
        $schoolId = Auth::user()->school_id;

        // Fetch groups and levels that belong to the logged-in admin’s school
//        $academicGroups = AcademicGroup::where('school_id', $schoolId)->get();
        $academicLevels = AcademicLevel::where('school_id', $schoolId)->get();
        $academicGroups = auth()->user()->school?->academicGroups()->get();

        // Academic periods (terms) are global — no school_id column
        $academicTerms = AcademicPeriod::orderBy('start_date', 'asc')->get();

        return view('payments.school-fees.setup', compact('academicGroups', 'academicLevels', 'academicTerms'));
    }


    public function storeFeeStructure(Request $request)
    {
        $validated = $request->validate([
            'academic_group_id' => 'required|exists:academic_groups,id',
            'academic_level_id' => 'required|exists:academic_levels,id',
            'current_term_id'   => 'required|exists:academic_periods,id',
            'amount'            => 'required|numeric|min:0',
            'due_date'          => 'required|date',
            'payment_method'    => 'nullable|string|max:50',
        ]);

        $validated['school_id'] = Auth::user()->school_id;

        AcademicFeeStructure::create($validated);

        return redirect()
            ->route('school.fee-setup')
            ->with('success', 'Fee structure created successfully!');
    }
}









/*
{"business_name":"Morning glory","description":"Subaccount for Morning glory","primary_contact_name":"Morning glory","primary_contact_email":"glory@gmail.com","primary_contact_phone":"0556709771","account_number":"1211010098973","percentage_charge":0,"settlement_bank":"GCB Bank Limited","currency":"GHS","bank":44,"integration":1588444,"domain":"test","account_name":"JOSEPH KWAME NKETIA","product":"collection","managed_by_integration":1588444,"subaccount_code":"ACCT_txstbu7htklyej0","is_verified":false,"settlement_schedule":"AUTO","active":true,"migrate":false,"id":1472808,"createdAt":"2025-09-22T14:22:41.943Z","updatedAt":"2025-09-22T14:22:41.943Z"}
*/

/*

CREATE TABLE `school_fees` (
  `id` BIGINT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `school_id` BIGINT UNSIGNED NOT NULL,
  `school_name` VARCHAR(255) NOT NULL,
  `amount` BIGINT NOT NULL, -- stored in pesewas (100000 = 1000.00 GHS)
  `currency` VARCHAR(10) NOT NULL DEFAULT 'GHS',
  `status` VARCHAR(50) NOT NULL DEFAULT 'pending', -- pending, success, failed
  `reference` VARCHAR(255) NOT NULL UNIQUE,
  `authorization_url` VARCHAR(500) NULL,
  `created_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,

  CONSTRAINT `fk_school_fees_school_id` FOREIGN KEY (`school_id`) REFERENCES `schools`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;



CREATE TABLE `subaccounts` (
  `id` BIGINT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
  `school_id` BIGINT UNSIGNED NOT NULL,
  `subaccount_code` VARCHAR(255) NOT NULL UNIQUE,
  `business_name` VARCHAR(255) NOT NULL,
  `settlement_bank` VARCHAR(255) NOT NULL,
  `account_number` VARCHAR(255) NOT NULL,
  `percentage_charge` DECIMAL(5,2) NOT NULL DEFAULT 0.00,
  `description` VARCHAR(255) NULL,
  `paystack_response` JSON NULL,
  `created_at` TIMESTAMP NULL,
  `updated_at` TIMESTAMP NULL,
  CONSTRAINT `subaccounts_school_id_foreign` FOREIGN KEY (`school_id`)
    REFERENCES `schools`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


*/
