<?php

namespace App\Http\Controllers;

use App\Models\AcademicFeeStructure;
use App\Models\AcademicPeriod;
use App\Models\FinancialAid;
use App\Models\School;
use App\Models\SchoolFee;
use App\Models\SchoolPayment;
use App\Models\Student;
use App\Services\PaystackService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PublicPaymentController extends Controller
{
    protected $paystack;

    public function __construct(PaystackService $paystack)
    {
        $this->paystack = $paystack;
    }

    /**
     * Show student lookup form
     */
    public function showLookupForm()
    {
        return view('payments.public.lookup');
    }

    /**
     * Lookup student by ID or email, or Financial Aid Code for bulk payment
     */
    public function lookupStudent(Request $request)
    {
        // Validate inputs
        $request->validate([
            'identifiers' => 'nullable|array',
            'identifiers.*' => 'nullable|string',
            'payment_code' => 'nullable|string',
        ]);

        $students = collect();
        $notFound = [];
        $financialAidId = null;
        $isBulkMode = false;
        $financialAid = null;
        $beneficiaryCount = 0;
        $beneficiaries = collect();

        // Scenario 1: Lookup by Payment/Group Code
        if ($request->filled('payment_code')) {
            $code = $request->input('payment_code');

            // 1a. Try to find Financial Aid Code (Bulk Mode)
            $financialAid = FinancialAid::where('code', $code)
                ->where('status', 'active')
                ->with('school') // Load school for view header
                ->first();

            if ($financialAid) {
                $financialAidId = $financialAid->id;
                $isBulkMode = true;
                // Count beneficiaries
                $beneficiaryCount = $financialAid->beneficiaries()->count();

                if ($beneficiaryCount === 0) {
                    return back()
                        ->withInput()
                        ->withErrors(['lookup' => 'This Financial Aid program has no beneficiaries listed yet.']);
                }

                // Load beneficiaries list for display (Optimized selection)
                $beneficiaries = $financialAid->beneficiaries()
                    ->with('user:id,name,avatar')
                    ->select('students.id', 'students.user_id', 'students.student_id')
                    ->get();

            } else {
                // 1b. Fallback: standard student lookup by group code (legacy behavior)
                $studentsFromCode = $this->resolvePaymentCode($code);

                if ($studentsFromCode->isEmpty()) {
                    return back()
                        ->withInput()
                        ->withErrors(['lookup' => 'Invalid payment code or no students associated with this code.']);
                }
                $students = $studentsFromCode;
            }
        }
        // Scenario 2: Lookup by Identifiers (IDs or Emails)
        elseif ($request->filled('identifiers')) {
            $identifiers = array_filter($request->input('identifiers'), function ($value) {
                return !is_null($value) && trim($value) !== '';
            });

            if (empty($identifiers)) {
                return back()
                    ->withInput()
                    ->withErrors(['lookup' => 'Please provide at least one Student ID or Email.']);
            }

            foreach ($identifiers as $identifier) {
                $identifier = trim($identifier);

                // Use withoutGlobalScopes to find across schools
                $student = Student::withoutGlobalScopes()
                    ->with(['user', 'school', 'academicGroup', 'academicLevel'])
                    ->where('student_id', $identifier)
                    ->first();

                // Try email if ID fails
                if (!$student) {
                    $user = \App\Models\User::where('email', $identifier)->first();
                    if ($user) {
                        $student = Student::withoutGlobalScopes()
                            ->with(['user', 'school', 'academicGroup', 'academicLevel'])
                            ->where('user_id', $user->id)
                            ->first();
                    }
                }

                if ($student) {
                    if (!$students->contains('id', $student->id)) {
                        $students->push($student);
                    }
                } else {
                    $notFound[] = $identifier;
                }
            }

            if ($students->isEmpty()) {
                return back()
                    ->withInput()
                    ->withErrors(['lookup' => 'No students found with the provided information.']);
            }

            if (!empty($notFound)) {
                session()->flash('warning', 'Could not find students with identifiers: ' . implode(', ', $notFound));
            }
        } else {
            return back()
                ->withInput()
                ->withErrors(['lookup' => 'Please provide Student IDs/Emails or a Payment Code.']);
        }

        // Prepare data for the view
        // If Bulk Mode, we pass an empty collection for studentsData to avoid individual list generation
        $studentsData = $isBulkMode ? collect() : $students->map(function ($student) {
            return [
                'student' => $student,
                'options' => $this->getPaymentOptionsForStudent($student)
            ];
        });

        // Determine context student for headers if not bulk
        $contextStudent = $students->first();

        return view('payments.public.form', [
            'studentsData' => $studentsData,
            'student' => $contextStudent, // Nullable in bulk mode
            'paymentOptions' => $contextStudent ? $this->getPaymentOptionsForStudent($contextStudent) : [],
            'financial_aid_id' => $financialAidId,
            'isBulkMode' => $isBulkMode,
            'financialAid' => $financialAid,
            'beneficiaryCount' => $beneficiaryCount,
            'beneficiaries' => $beneficiaries
        ]);
    }

    /**
     * Initialize payment transaction
     */
    public function initializePayment(Request $request)
    {
        $validated = $request->validate([
            // Standard payments validation (required unless bulk)
            'payments' => 'required_without:bulk_amount|array',
            'payments.*.student_id' => 'required_without:bulk_amount|exists:students,id',
            'payments.*.payment_type' => 'required_without:bulk_amount|string',
            'payments.*.amount' => 'required_without:bulk_amount|numeric|min:1',

            // Bulk payment validation (required if bulk)
            'bulk_amount' => 'required_without:payments|numeric|min:1',
            'financial_aid_id' => 'nullable|exists:financial_aids,id',

            // Payer details
            'payer_type' => 'required|in:parent,student,other',
            'payer_name' => 'nullable|string|max:255',
            'payer_email' => 'required|email',
            'payer_phone' => 'nullable|string|max:20',
        ]);

        // Logic check: if bulk_amount is present, financial_aid_id must be present
        if ($request->filled('bulk_amount') && !$request->filled('financial_aid_id')) {
            return back()->withErrors(['error' => 'Financial Aid context missing for bulk payment.']);
        }

        $totalAmount = 0;
        $paymentRecords = [];
        $batchReference = 'BATCH-' . now()->format('YmdHis') . '-' . strtoupper(\Str::random(6));
        $financialAidId = $request->input('financial_aid_id');

        DB::beginTransaction();
        try {
            // --- PATH A: Bulk Distribution Mode ---
            if ($request->filled('bulk_amount')) {
                $amount = $request->input('bulk_amount');
                $totalAmount = $amount;

                $financialAid = FinancialAid::with('school')->find($financialAidId);

                // Optimised fetch: Get beneficiary IDs directly to avoid hydrating thousands of models
                $beneficiaries = DB::table('financial_aid_student')
                    ->join('students', 'financial_aid_student.student_id', '=', 'students.id')
                    ->where('financial_aid_student.financial_aid_id', $financialAidId)
                    ->whereNull('students.deleted_at')
                    ->select(
                        'students.id',
                        'students.school_id',
                        'students.user_id'
                    )
                    ->get();

                $count = $beneficiaries->count();
                if ($count === 0) {
                    throw new \Exception("No beneficiaries found to distribute funds to.");
                }

                // Calculate split (floor to 2 decimals)
                $amountPerStudent = floor(($amount / $count) * 100) / 100;

                if ($amountPerStudent < 0.01) {
                    throw new \Exception("Amount per student is too small to process.");
                }

                // Common insert data
                $now = now();
                $payerId = auth()->id();
                $payerType = $validated['payer_type'];
                $payerName = $validated['payer_name'] ?? 'Guest';
                $payerEmail = $validated['payer_email'];

                // Get current term context for the school
                $currentTermId = AcademicPeriod::where('school_id', $financialAid->school_id)
                    ->where(function($q) {
                        $q->where('is_current', 1)->orWhere('status', 'active');
                    })
                    ->value('id');

                $feesToInsert = [];

                foreach ($beneficiaries as $student) {
                    $feesToInsert[] = [
                        'school_id' => $student->school_id,
                        'student_id' => $student->id,
                        'financial_aid_id' => $financialAidId,
                        'payer_id' => $payerId,
                        'payer_type' => $payerType,
                        'school_name' => $financialAid->school->name ?? '',
                        'amount' => $amountPerStudent,
                        'term_total_amount' => $amountPerStudent, // Placeholder
                        'term_id' => $currentTermId,
                        'currency' => 'GHS',
                        'status' => 'pending',
                        'reference' => $batchReference, // Shared reference
                        'authorization_url' => null,
                        'paystack_response' => json_encode([
                            'batch_reference' => $batchReference,
                            'payer_name' => $payerName,
                            'payer_email' => $payerEmail,
                            'distribution' => 'bulk_equal_share',
                            'financial_aid_code' => $financialAid->code
                        ]),
                        'created_at' => $now,
                        'updated_at' => $now,
                    ];
                }

                // Insert in chunks
                foreach (array_chunk($feesToInsert, 500) as $chunk) {
                    SchoolFee::insert($chunk);
                }

            }
            // --- PATH B: Individual Student Mode ---
            else {
                foreach ($validated['payments'] as $item) {
                    $student = Student::withoutGlobalScopes()
                        ->with(['school', 'user'])
                        ->find($item['student_id']);

                    if (!$student) continue;

                    $amount = $item['amount'];
                    $paymentType = $item['payment_type'];

                    // Current Term context
                    $currentTerm = AcademicPeriod::where('school_id', $student->getUserSchoolId())
                        ->where(function($q) {
                            $q->where('is_current', 1)->orWhere('status', 'active');
                        })
                        ->first();

                    if ($paymentType === 'tuition') {
                        // Create SchoolFee
                        $feeStructure = AcademicFeeStructure::where('school_id', $student->getUserSchoolId())
                            ->where('academic_group_id', $student->academic_group_id)
                            ->where('academic_level_id', $student->academic_level_id)
                            ->first();

                        $payment = SchoolFee::create([
                            'school_id' => $student->getUserSchoolId(),
                            'student_id' => $student->id,
                            'financial_aid_id' => $financialAidId,
                            'payer_id' => auth()->id(),
                            'payer_type' => $validated['payer_type'],
                            'school_name' => $student->school->name ?? '',
                            'amount' => $amount,
                            'term_total_amount' => $feeStructure->term_total_amount ?? $feeStructure->amount ?? $amount,
                            'term_id' => $currentTerm->id ?? null,
                            'currency' => 'GHS',
                            'status' => 'pending',
                            'reference' => 'FEE-' . now()->format('YmdHis') . '-' . strtoupper(\Str::random(6)),
                            'paystack_response' => json_encode([
                                'batch_reference' => $batchReference,
                                'payer_name' => $validated['payer_name'] ?? 'Guest',
                                'payer_email' => $validated['payer_email'],
                                'student_name' => $student->user->name ?? '',
                                'financial_aid_id' => $financialAidId,
                            ]),
                        ]);

                        $paymentRecords[] = ['model' => $payment, 'type' => 'fee'];

                    } else {
                        // Create SchoolPayment (Other types)
                        $payment = SchoolPayment::create([
                            'school_id' => $student->getUserSchoolId(),
                            'student_id' => $student->id,
                            'financial_aid_id' => $financialAidId,
                            'academic_group_id' => $student->academic_group_id,
                            'academic_level_id' => $student->academic_level_id,
                            'academic_period_id' => $currentTerm->id ?? null,
                            'payment_type' => $paymentType,
                            'amount' => $amount,
                            'fixed_amount' => $amount,
                            'currency' => 'GHS',
                            'payer_type' => $validated['payer_type'],
                            'payer_id' => auth()->id(),
                            'payer_name' => $validated['payer_name'] ?? (auth()->check() ? auth()->user()->name : 'Guest'),
                            'payer_email' => $validated['payer_email'],
                            'payer_phone' => $validated['payer_phone'] ?? null,
                            'status' => 'pending',
                            'gateway' => 'paystack',
                            'metadata' => [
                                'batch_reference' => $batchReference,
                                'student_name' => $student->user->name ?? '',
                                'financial_aid_id' => $financialAidId,
                            ]
                        ]);

                        $paymentRecords[] = ['model' => $payment, 'type' => 'payment'];
                    }

                    $totalAmount += $amount;
                }
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Payment Initialization Error', ['error' => $e->getMessage()]);
            return back()->withErrors(['payment' => 'Failed to initialize payment records. ' . $e->getMessage()]);
        }

        // --- Initialize Paystack ---
        $callbackUrl = route('payments.public.callback');

        $metadata = [
            'batch_reference' => $batchReference,
            'payer_type' => $validated['payer_type'],
            'financial_aid_id' => $financialAidId,
            'is_bulk' => $request->filled('bulk_amount'),
        ];

        $paymentData = [
            'email' => $validated['payer_email'],
            'amount' => $totalAmount * 100, // Convert to pesewas
            'currency' => 'GHS',
            'reference' => $batchReference,
            'callback_url' => $callbackUrl,
            'metadata' => $metadata,
        ];

        // Handle Subaccount (Try finding specific school context)
        $schoolForSubaccount = null;
        if ($financialAidId && $financialAid = FinancialAid::with('school.subaccount')->find($financialAidId)) {
            $schoolForSubaccount = $financialAid->school;
        } elseif (isset($paymentRecords[0])) {
            $schoolForSubaccount = $paymentRecords[0]['model']->school ?? null;
        }

        if ($schoolForSubaccount && $schoolForSubaccount->subaccount && $schoolForSubaccount->subaccount->subaccount_code) {
            $paymentData['subaccount'] = $schoolForSubaccount->subaccount->subaccount_code;
            $paymentData['bearer'] = 'account';
        }

        try {
            $response = $this->paystack->initializeTransaction($paymentData);

            if (empty($response['status']) || !$response['status']) {
                return back()->withErrors(['payment' => 'Unable to initialize payment gateway.']);
            }

            $authUrl = $response['data']['authorization_url'];
            $gatewayRef = $response['data']['reference'];

            // --- Update Local Records with Gateway Info ---
            if ($request->filled('bulk_amount')) {
                // Bulk Update using the shared reference
                SchoolFee::where('reference', $batchReference)
                    ->update([
                        'authorization_url' => $authUrl,
                        // Append gateway reference to JSON field
                        'paystack_response' => DB::raw("JSON_SET(paystack_response, '$.gateway_reference', '{$gatewayRef}')")
                    ]);
            } else {
                // Individual Update
                foreach ($paymentRecords as $record) {
                    $record['model']->update([
                        'authorization_url' => $authUrl,
                        // Only SchoolPayment usually has separate transaction_id column in this schema
                        'transaction_id' => $record['type'] === 'payment' ? $gatewayRef : null,
                    ]);

                    if ($record['type'] === 'fee') {
                        $record['model']->update([
                            'paystack_response' => json_encode(array_merge(
                                json_decode($record['model']->paystack_response, true) ?? [],
                                ['gateway_reference' => $gatewayRef]
                            ))
                        ]);
                    }
                }
            }

            return redirect($authUrl);

        } catch (\Exception $e) {
            Log::error('Paystack Init Failed', ['error' => $e->getMessage()]);
            return back()->withErrors(['payment' => 'Gateway initialization failed.']);
        }
    }

    /**
     * Resolve students from a payment code (Legacy/Standard groups)
     */
    protected function resolvePaymentCode(string $code)
    {
        // Note: Financial Aid resolution is now handled directly in lookupStudent

        // Placeholder for other StudentGroup logic
        // $group = \App\Models\StudentGroup::where('code', $code)->first();

        return collect();
    }

    /**
     * Get available payment options for student
     */
    protected function getPaymentOptionsForStudent(Student $student)
    {
        // Get fee structure for tuition amount
        $feeStructure = AcademicFeeStructure::where('school_id', $student->getUserSchoolId())
            ->where('academic_group_id', $student->academic_group_id)
            ->where('academic_level_id', $student->academic_level_id)
            ->first();

        $tuitionAmount = $feeStructure->term_total_amount ?? $feeStructure->amount ?? 0;

        $paymentOptions = [
            'tuition' => [
                'name' => 'Tuition Fee',
                'amount' => $tuitionAmount,
                'allow_custom' => true,
                'is_default' => true,
            ],
            'library' => [
                'name' => 'Library Fee',
                'amount' => 200.00,
                'allow_custom' => false,
                'is_default' => false,
            ],
            'transport' => [
                'name' => 'Transport Fee',
                'amount' => 150.00,
                'allow_custom' => false,
                'is_default' => false,
            ],
            'exam' => [
                'name' => 'Examination Fee',
                'amount' => 300.00,
                'allow_custom' => false,
                'is_default' => false,
            ],
            'sports' => [
                'name' => 'Sports Fee',
                'amount' => 100.00,
                'allow_custom' => false,
                'is_default' => false,
            ],
            'pta' => [
                'name' => 'PTA Dues',
                'amount' => 50.00,
                'allow_custom' => false,
                'is_default' => false,
            ],
            'development' => [
                'name' => 'Development Levy',
                'amount' => 200.00,
                'allow_custom' => false,
                'is_default' => false,
            ],
            'other' => [
                'name' => 'Other Payment',
                'amount' => 0.00,
                'allow_custom' => true,
                'is_default' => false,
            ],
        ];

        return $paymentOptions;
    }

    /**
     * Handle payment callback
     */
    public function paymentCallback(Request $request)
    {
        $reference = $request->query('reference');

        if (!$reference) {
            return redirect()->route('payments.public.lookup')
                ->withErrors(['payment' => 'Missing payment reference.']);
        }

        try {
            $response = $this->paystack->verifyTransaction($reference);

            if (empty($response['status']) || !$response['status']) {
                return redirect()->route('payments.public.lookup')
                    ->withErrors(['payment' => 'Payment verification failed at gateway.']);
            }

            // Find SchoolPayment records with this batch reference
            $schoolPayments = SchoolPayment::where('transaction_id', $reference)->get();

            if ($schoolPayments->isEmpty()) {
                $schoolPayments = SchoolPayment::whereJsonContains('metadata->batch_reference', $reference)->get();
            }

            // Find SchoolFee records with this batch reference in paystack_response
            $schoolFees = SchoolFee::where('paystack_response', 'like', '%"batch_reference":"' . $reference . '"%')->get();

            if ($schoolPayments->isEmpty() && $schoolFees->isEmpty()) {
                return redirect()->route('payments.public.lookup')
                    ->withErrors(['payment' => 'No local payment records found for this transaction.']);
            }

            DB::transaction(function () use ($schoolPayments, $schoolFees, $response) {
                // Update SchoolPayment records
                foreach ($schoolPayments as $payment) {
                    if ($payment->status !== 'succeeded') {
                        $payment->markAsSucceeded([
                            'gateway_response' => $response,
                        ]);
                    }
                }

                // Update SchoolFee records
                foreach ($schoolFees as $fee) {
                    if ($fee->status !== 'succeeded') {
                        $fee->update([
                            'status' => 'succeeded',
                            'paystack_response' => json_encode(array_merge(
                                json_decode($fee->paystack_response, true) ?? [],
                                ['gateway_response' => $response]
                            )),
                        ]);

                        // Update Financial Aid amount_raised if applicable
                        if ($fee->financial_aid_id) {
                            $aid = FinancialAid::find($fee->financial_aid_id);
                            if ($aid) {
                                $aid->increment('amount_raised', $fee->amount);
                            }
                        }
                    }
                }
            });

            // Combine all payments for success page
            $allPayments = $schoolPayments->concat($schoolFees);

            return view('payments.public.success', [
                'payment' => $allPayments->first(),
                'allPayments' => $allPayments,
            ]);

        } catch (\Exception $e) {
            Log::error('Callback error', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return redirect()->route('payments.public.lookup')
                ->withErrors(['payment' => 'Error processing payment verification.']);
        }
    }

    /**
     * Show payment success page
     */
    public function success(SchoolPayment $payment)
    {
        $payment->load('student.user', 'student.school');

        return view('payments.public.success', compact('payment'));
    }
}
