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
     * Lookup student by ID or email and show payment form
     */
    public function lookupStudentDep(Request $request)
    {
        // Validate that at least one field is provided
        $request->validate([
            'student_id' => 'nullable|string|required_without:email',
            'email' => 'nullable|email|required_without:student_id',
        ], [
            'student_id.required_without' => 'Please provide either a student ID or email address.',
            'email.required_without' => 'Please provide either a student ID or email address.',
        ]);

        // Build query based on what was provided
        $query = Student::with([
            'user',
            'school',
            'academicGroup',
            'academicLevel'
        ]);

        // Search by student_id or email
        if ($request->filled('student_id') && $request->filled('email')) {
            // Both provided - search for student matching both
            $query->where('student_id', $request->student_id)
                ->whereHas('user', function ($q) use ($request) {
                    $q->where('email', $request->email);
                });
        } elseif ($request->filled('student_id')) {
            // Only student_id provided
            $query->where('student_id', $request->student_id);
        } else {
            // Only email provided
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('email', $request->email);
            });
        }

        $student = $query->first();

        if (!$student) {
            return back()
                ->withInput()
                ->withErrors([
                    'lookup' => 'Student not found. Please check the information and try again.'
                ]);
        }

        // Get available payment types and amounts
        $paymentOptions = $this->getPaymentOptionsForStudent($student);

        return view('payments.public.form', compact('student', 'paymentOptions'));
    }

    /**
     * Lookup student by ID or email and show payment form
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

        // Scenario 1: Lookup by Payment/Group Code
        if ($request->filled('payment_code')) {
            $code = $request->input('payment_code');
            $studentsFromCode = $this->resolvePaymentCode($code);

            if ($studentsFromCode->isEmpty()) {
                return back()
                    ->withInput()
                    ->withErrors(['lookup' => 'Invalid payment code or no students associated with this code.']);
            }
            $students = $studentsFromCode;
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

                // Use withoutGlobalScopes() to bypass school-based filtering for public lookups
                // Search by student_id first
                $student = Student::withoutGlobalScopes()
                    ->with(['user', 'school', 'academicGroup', 'academicLevel'])
                    ->where('student_id', $identifier)
                    ->first();

                // If not found by student_id, search by email through User model
                if (!$student) {
                    // Find user by email first, then get their student record
                    $user = \App\Models\User::where('email', $identifier)->first();

                    if ($user) {
                        $student = Student::withoutGlobalScopes()
                            ->with(['user', 'school', 'academicGroup', 'academicLevel'])
                            ->where('user_id', $user->id)
                            ->first();
                    }
                }

                if ($student) {
                    // Prevent duplicates
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
        $studentsData = $students->map(function ($student) {
            return [
                'student' => $student,
                'options' => $this->getPaymentOptionsForStudent($student)
            ];
        });

        return view('payments.public.form', [
            'studentsData' => $studentsData,
            'student' => $students->first(),
            'paymentOptions' => $this->getPaymentOptionsForStudent($students->first())
        ]);
    }
    /**
     * Resolve students from a payment code
     */
    protected function resolvePaymentCode(string $code)
    {
        // Lookup Financial Aid by the code provided
        $financialAid = FinancialAid::with(['beneficiaries.user', 'beneficiaries.school', 'beneficiaries.academicGroup', 'beneficiaries.academicLevel'])
            ->where('code', $code)
            ->where('status', 'active')
            ->first();

        if ($financialAid) {
            return $financialAid->beneficiaries;
        }

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
                'is_default' => true, // Mark tuition as default
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
     * Initialize payment
     */
    public function initializePaymentDep(Request $request)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'payment_type' => 'required|string',
            'amount' => 'required|numeric|min:1',
            'fixed_amount' => 'nullable|numeric',
            'payer_type' => 'required|in:parent,student,other',
            'payer_name' => 'required_if:payer_type,other|string|max:255',
            'payer_email' => 'required|email',
            'payer_phone' => 'nullable|string|max:20',
        ]);

        $student = Student::with('school')->findOrFail($validated['student_id']);

        // Create payment record
        $payment = SchoolPayment::create([
            'school_id' => $student->school_id,
            'student_id' => $student->id,
            'academic_group_id' => $student->academic_group_id,
            'academic_level_id' => $student->academic_level_id,
            'payment_type' => $validated['payment_type'],
            'amount' => $validated['amount'],
            'fixed_amount' => $validated['fixed_amount'] ?? $validated['amount'],
            'currency' => 'GHS',
            'payer_type' => $validated['payer_type'],
            'payer_id' => auth()->id(),
            'payer_name' => $validated['payer_name'] ?? (auth()->check() ? auth()->user()->name : null),
            'payer_email' => $validated['payer_email'],
            'payer_phone' => $validated['payer_phone'] ?? null,
            'status' => 'pending',
            'gateway' => 'paystack',
        ]);

        // Initialize Paystack transaction
        $callbackUrl = route('payments.public.callback');

        $paymentData = [
            'email' => $validated['payer_email'],
            'amount' => $validated['amount'] * 100, // Convert to pesewas
            'currency' => 'GHS',
            'reference' => $payment->reference,
            'callback_url' => $callbackUrl,
            'metadata' => [
                'payment_id' => $payment->id,
                'student_id' => $student->student_id,
                'student_name' => $student->user->name,
                'payment_type' => $validated['payment_type'],
            ],
        ];

        // Add subaccount if school has one
        $subaccount = $student->school->subaccount;
        if ($subaccount && $subaccount->subaccount_code) {
            $paymentData['subaccount'] = $subaccount->subaccount_code;
            $paymentData['bearer'] = 'account';
        }

        try {
            $response = $this->paystack->initializeTransaction($paymentData);

            if (empty($response['status']) || !$response['status']) {
                return back()->withErrors(['payment' => 'Unable to initialize payment. Please try again.']);
            }

            // Update payment with authorization URL
            $payment->update([
                'authorization_url' => $response['data']['authorization_url'],
                'transaction_id' => $response['data']['reference'],
            ]);

            return redirect($response['data']['authorization_url']);
        } catch (\Exception $e) {
            \Log::error('Payment initialization failed', [
                'error' => $e->getMessage(),
                'payment_id' => $payment->id,
            ]);

            return back()->withErrors(['payment' => 'Unable to initialize payment. Please try again or contact support.']);
        }
    }

    /**
     * Handle payment callback
     */
    public function paymentCallbackDep(Request $request)
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
                    ->withErrors(['payment' => 'Payment verification failed.']);
            }

            $data = $response['data'] ?? [];

            // Try to get payment_id from metadata or find by reference
            $paymentId = $data['metadata']['payment_id'] ?? null;

            if (!$paymentId) {
                // Fallback: try to find payment by reference
                $payment = SchoolPayment::where('reference', $reference)->first();

                if (!$payment) {
                    return redirect()->route('payments.public.lookup')
                        ->withErrors(['payment' => 'Invalid payment reference.']);
                }
            } else {
                $payment = SchoolPayment::findOrFail($paymentId);
            }

            // Update payment status
            DB::transaction(function () use ($payment, $response) {
                $payment->markAsSucceeded([
                    'gateway_response' => $response,
                    'transaction_id' => $response['data']['id'] ?? null,
                ]);
            });

            return redirect()->route('payments.public.success', $payment)
                ->with('success', 'Payment successful!');
        } catch (\Exception $e) {
            \Log::error('Payment callback failed', [
                'error' => $e->getMessage(),
                'reference' => $reference,
            ]);

            return redirect()->route('payments.public.lookup')
                ->withErrors(['payment' => 'An error occurred processing your payment. Please contact support with reference: ' . $reference]);
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

    /**
     * Initialize payment for multiple students
     */
    public function initializePayment(Request $request)
    {
        $validated = $request->validate([
            'payments' => 'required|array|min:1',
            'payments.*.student_id' => 'required|exists:students,id',
            'payments.*.payment_type' => 'required|string',
            'payments.*.amount' => 'required|numeric|min:1',

            'payer_type' => 'required|in:parent,student,other',
            'payer_name' => 'nullable|string|max:255',
            'payer_email' => 'required|email',
            'payer_phone' => 'nullable|string|max:20',
        ]);

        $totalAmount = 0;
        $paymentRecords = [];
        $batchReference = 'BATCH-' . now()->format('YmdHis') . '-' . strtoupper(\Str::random(6));

        DB::beginTransaction();
        try {
            foreach ($validated['payments'] as $item) {
                $student = Student::withoutGlobalScopes()
                    ->with(['school', 'user'])
                    ->find($item['student_id']);
                $amount = $item['amount'];
                $paymentType = $item['payment_type'];

                // Determine if this is a tuition payment (SchoolFee) or other payment (SchoolPayment)
                if ($paymentType === 'tuition') {
                    // Get current term for this student's school
                    $currentTerm = AcademicPeriod::where('school_id', $student->getUserSchoolId())
                        ->where(function($q) {
                            $q->where('is_current', 1)->orWhere('status', 'active');
                        })
                        ->first();

                    // Get fee structure for term total
                    $feeStructure = AcademicFeeStructure::where('school_id', $student->getUserSchoolId())
                        ->where('academic_group_id', $student->academic_group_id)
                        ->where('academic_level_id', $student->academic_level_id)
                        ->first();

                    $school = School::find($student->getUserSchoolId());

                    // Create SchoolFee record for tuition
                    $payment = SchoolFee::create([
                        'school_id' => $student->getUserSchoolId(),
                        'student_id' => $student->id,
                        'payer_id' => auth()->id(),
                        'payer_type' => $validated['payer_type'],
                        'school_name' => $school->name ?? '',
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
                            'payer_phone' => $validated['payer_phone'] ?? null,
                            'student_name' => $student->user->name ?? '',
                        ]),
                    ]);
                } else {
                    // Get current term for this student's school
                    $currentTerm = AcademicPeriod::where('school_id', $student->getUserSchoolId())
                        ->where(function($q) {
                            $q->where('is_current', 1)->orWhere('status', 'active');
                        })
                        ->first();

                    // Create SchoolPayment record for other payment types
                    $payment = SchoolPayment::create([
                        'school_id' => $student->getUserSchoolId(),
                        'student_id' => $student->id,
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
                        ]
                    ]);
                }

                // Store payment info for callback processing
                $paymentRecords[] = [
                    'model' => $payment,
                    'type' => $paymentType === 'tuition' ? 'fee' : 'payment',
                ];
                $totalAmount += $amount;
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Batch payment creation failed', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return back()->withErrors(['payment' => 'Failed to create payment records. Please try again.']);
        }

        // Initialize ONE Paystack transaction for the total amount
        $callbackUrl = route('payments.public.callback');

        $firstStudent = Student::withoutGlobalScopes()->with('school')->find($validated['payments'][0]['student_id']);

        $paymentData = [
            'email' => $validated['payer_email'],
            'amount' => $totalAmount * 100, // Convert to pesewas
            'currency' => 'GHS',
            'reference' => $batchReference,
            'callback_url' => $callbackUrl,
            'metadata' => [
                'batch_reference' => $batchReference,
                'payer_type' => $validated['payer_type'],
                'payment_count' => count($paymentRecords),
            ],
        ];

        // Subaccount handling
        $subaccount = $firstStudent->school->subaccount ?? null;
        if ($subaccount && $subaccount->subaccount_code) {
            $paymentData['subaccount'] = $subaccount->subaccount_code;
            $paymentData['bearer'] = 'account';
        }

        try {
            $response = $this->paystack->initializeTransaction($paymentData);

            if (empty($response['status']) || !$response['status']) {
                return back()->withErrors(['payment' => 'Unable to initialize payment gateway.']);
            }

            // Update all records with the gateway reference/url
            foreach ($paymentRecords as $record) {
                if ($record['type'] === 'fee') {
                    $record['model']->update([
                        'authorization_url' => $response['data']['authorization_url'],
                    ]);
                } else {
                    $record['model']->update([
                        'authorization_url' => $response['data']['authorization_url'],
                        'transaction_id' => $batchReference,
                    ]);
                }
            }

            return redirect($response['data']['authorization_url']);

        } catch (\Exception $e) {
            \Log::error('Paystack init failed', ['error' => $e->getMessage()]);
            return back()->withErrors(['payment' => 'Gateway initialization failed.']);
        }
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
            \Log::error('Callback error', ['error' => $e->getMessage(), 'trace' => $e->getTraceAsString()]);
            return redirect()->route('payments.public.lookup')
                ->withErrors(['payment' => 'Error processing payment verification.']);
        }
    }

}
