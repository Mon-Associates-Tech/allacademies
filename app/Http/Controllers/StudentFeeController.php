<?php

namespace App\Http\Controllers;

use App\Models\AcademicFeeStructure;
use App\Models\AcademicPeriod;
use App\Models\SchoolFee;
use App\Models\Student;
use App\Services\PaystackService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StudentFeeController extends Controller
{
    protected $paystack;

    public function __construct(PaystackService $paystack)
    {
        $this->paystack = $paystack;
    }

    /**
     * Display fee dashboard with payment history
     */
    public function index()
    {
        $student = Auth::user()->student;

        if (!$student) {
            $student = Student::where('user_id', Auth::id())->first();
        }

        if(!$student){
            return redirect()->route('dashboard')->with('error', 'Student profile not found.');

        }

        // Get current term
        $currentTerm = AcademicPeriod::where('is_current', 1)->first();

        // Get fee structure
        $feeStructure = AcademicFeeStructure::where('school_id', $student->school_id)
            ->where('academic_group_id', $student->academic_group_id)
            ->where('academic_level_id', $student->academic_level_id)
            ->where('current_term_id', $currentTerm->id ?? null)
            ->first();

        // Calculate payment stats
        $totalPaid = SchoolFee::where('student_id', $student->id)
            ->where('term_id', $currentTerm->id ?? null)
            ->where('status', 'succeeded')
            ->sum('amount');

        $termTotalAmount = $feeStructure->term_total_amount ?? $feeStructure->amount ?? 0;
        $remainingAmount = max($termTotalAmount - $totalPaid, 0);

        // Get all payment history
        $paymentHistory = SchoolFee::where('student_id', $student->id)
            ->with(['payer', 'academicPeriod', 'student.academicGroup', 'student.academicLevel'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        // Get pending payments
        $pendingPayments = SchoolFee::where('student_id', $student->id)
            ->where('status', 'pending')
            ->count();

        // Get payment stats by term
        $termPayments = SchoolFee::where('student_id', $student->id)
            ->select('term_id', DB::raw('SUM(amount) as total_paid'), DB::raw('COUNT(*) as payment_count'))
            ->groupBy('term_id')
            ->with('academicPeriod')
            ->get();

        return view('students.fees.index', compact(
            'student',
            'feeStructure',
            'totalPaid',
            'termTotalAmount',
            'remainingAmount',
            'paymentHistory',
            'currentTerm',
            'pendingPayments',
            'termPayments'
        ));
    }

    /**
     * Show payment form
     */
    public function payment()
    {
        $student = Auth::user()->student;

        if (!$student) {
            return redirect()->route('dashboard')->with('error', 'Student profile not found.');
        }

        $currentTerm = AcademicPeriod::where('is_current', 1)->first();

        $feeStructure = AcademicFeeStructure::where('school_id', $student->school_id)
            ->where('academic_group_id', $student->academic_group_id)
            ->where('academic_level_id', $student->academic_level_id)
            ->where('current_term_id', $currentTerm->id ?? null)
            ->first();

        $totalPaid = SchoolFee::where('student_id', $student->id)
            ->where('term_id', $currentTerm->id ?? null)
            ->where('status', 'succeeded')
            ->sum('amount');

        $termTotalAmount = $feeStructure->term_total_amount ?? $feeStructure->amount ?? 0;
        $remainingAmount = max($termTotalAmount - $totalPaid, 0);

        return view('students.fees.payment', compact(
            'student',
            'feeStructure',
            'totalPaid',
            'termTotalAmount',
            'remainingAmount',
            'currentTerm'
        ));
    }

    /**
     * Initialize payment
     */
    public function initializePayment(Request $request)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:1',
        ]);

        $student = Auth::user()->student;
        $school = $student->school;
        $currentTerm = AcademicPeriod::where('is_current', 1)->first();

        if (!$school) {
            return back()->withErrors(['error' => 'School information not found.']);
        }

        // Get subaccount
        $subaccount = \App\Models\Subaccount::where('school_id', $school->id)->first();

        $paymentData = [
            'email' => Auth::user()->email,
            'amount' => $validated['amount'] * 100,
            'currency' => 'GHS',
            'callback_url' => route('students.fees.callback'),
            'metadata' => [
                'student_id' => $student->id,
                'term_id' => $currentTerm->id ?? null,
                'school_id' => $school->id,
            ],
        ];

        if ($subaccount && $subaccount->subaccount_code) {
            $paymentData['subaccount'] = $subaccount->subaccount_code;
        }

        $response = $this->paystack->initializeTransaction($paymentData);

        if (empty($response['status']) || !$response['status']) {
            return back()->withErrors(['error' => 'Unable to initialize payment. Please try again.']);
        }

        $reference = $response['data']['reference'];

        $feeStructure = AcademicFeeStructure::where('school_id', $school->id)
            ->where('academic_group_id', $student->academic_group_id)
            ->where('academic_level_id', $student->academic_level_id)
            ->where('current_term_id', $currentTerm->id ?? null)
            ->first();

        SchoolFee::create([
            'school_id' => $school->id,
            'student_id' => $student->id,
            'payer_id' => Auth::id(),
            'payer_type' => get_class(Auth::user()),
            'school_name' => $school->name,
            'amount' => $validated['amount'],
            'term_total_amount' => $feeStructure->term_total_amount ?? $feeStructure->amount ?? 0,
            'term_id' => $currentTerm->id ?? null,
            'currency' => 'GHS',
            'status' => 'pending',
            'reference' => $reference,
            'authorization_url' => $response['data']['authorization_url'],
            'paystack_response' => json_encode($response),
        ]);

        return redirect($response['data']['authorization_url']);
    }

    /**
     * Payment callback
     */
    public function callback(Request $request)
    {
        $reference = $request->query('reference');

        if (!$reference) {
            return redirect()->route('students.fees.index')
                ->with('error', 'Payment reference not found.');
        }

        $response = $this->paystack->verifyTransaction($reference);

        $payment = SchoolFee::where('reference', $reference)->first();

        if ($payment) {
            if (!empty($response['status']) && $response['status'] && $response['data']['status'] === 'success') {
                $payment->update([
                    'status' => 'succeeded',
                    'paystack_response' => json_encode($response),
                ]);

                return redirect()->route('students.fees.receipt', $payment)
                    ->with('success', 'Payment successful!');
            } else {
                $payment->update(['status' => 'failed']);

                return redirect()->route('students.fees.index')
                    ->with('error', 'Payment verification failed.');
            }
        }

        return redirect()->route('students.fees.index')
            ->with('error', 'Payment record not found.');
    }

    /**
     * Show payment receipt
     */
    public function receipt(SchoolFee $payment)
    {
        // Ensure user can only view their own receipts
        if ($payment->student->user_id !== Auth::id()) {
            abort(403);
        }

        $payment->load(['student.academicGroup', 'student.academicLevel', 'academicPeriod', 'payer']);

        return view('students.fees.receipt', compact('payment'));
    }
}
