<?php

namespace App\Http\Controllers;

use App\Models\AcademicFeeStructure;
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
    public function lookupStudent(Request $request)
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
     * Get available payment options for student
     */
    protected function getPaymentOptionsForStudent(Student $student)
    {
        $paymentOptions = [
            'tuition' => [
                'name' => 'Tuition Fee',
                'amount' => 1000.00,
                'allow_custom' => true,
            ],
            'library' => [
                'name' => 'Library Fee',
                'amount' => 200.00,
                'allow_custom' => false,
            ],
            'transport' => [
                'name' => 'Transport Fee',
                'amount' => 150.00,
                'allow_custom' => false,
            ],
            'exam' => [
                'name' => 'Examination Fee',
                'amount' => 300.00,
                'allow_custom' => false,
            ],
            'sports' => [
                'name' => 'Sports Fee',
                'amount' => 100.00,
                'allow_custom' => false,
            ],
            'pta' => [
                'name' => 'PTA Dues',
                'amount' => 50.00,
                'allow_custom' => false,
            ],
            'development' => [
                'name' => 'Development Levy',
                'amount' => 200.00,
                'allow_custom' => false,
            ],
            'other' => [
                'name' => 'Other Payment',
                'amount' => 0.00,
                'allow_custom' => true,
            ],
        ];


         $feeStructure = AcademicFeeStructure::where('school_id', $student->getUserSchoolId())
             ->where('academic_group_id', $student->academic_group_id)
             ->where('academic_level_id', $student->academic_level_id)
             ->first();

        return $paymentOptions;
    }

    /**
     * Initialize payment
     */
    public function initializePayment(Request $request)
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
}
