<?php

namespace App\Http\Controllers;

use App\Models\AcademicFeeStructure;
use App\Models\AcademicPeriod;
use App\Models\School;
use App\Models\SchoolFee;
use App\Models\SchoolPayment;
use App\Models\Student;
use App\Services\PaystackService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class StudentFeeController extends Controller
{
    protected $paystack;

    public function __construct(PaystackService $paystack)
    {
        $this->paystack = $paystack;

        // Prevent caching of payment pages to avoid browser cache miss errors
        $this->middleware(function ($request, $next) {
            $response = $next($request);

            return $response->header('Cache-Control', 'no-cache, no-store, must-revalidate')
                ->header('Pragma', 'no-cache')
                ->header('Expires', '0');
        })->only(['initializePayment', 'callback']);
    }

    /**
     * Display fee dashboard with payment history
     */
    public function index()
    {
        try {
            $user = Auth::user();
            $schoolId = getSchoolId();

            if (! $schoolId) {
                return redirect()->route('dashboard')
                    ->with('error', 'School context not found. Please contact your administrator.');
            }

            $student = Student::withoutGlobalScopes()->where('user_id', $user->id)->first();

            if (! $student) {
                Log::warning('Student accessing payments without student profile', [
                    'user_id' => $user->id,
                    'user_email' => $user->email,
                    'user_school_id' => $schoolId,
                ]);

                return redirect()->route('dashboard')
                    ->with('error', 'Student profile not found. Please contact your administrator.');
            }

            $currentTerm = AcademicPeriod::where('school_id', $schoolId)
                ->where('is_current', 1)
                ->orWhere('status', 'active')
                ->first();

            $feeStructure = AcademicFeeStructure::where('school_id', $schoolId)
                ->where('academic_group_id', $student->academic_group_id)
                ->where('academic_level_id', $student->academic_level_id)
                ->where('current_term_id', $currentTerm->id ?? null)
                ->first();

            // Calculate payment stats from SchoolFee
            $totalPaidFromFees = SchoolFee::where('student_id', $student->id)
                ->where('term_id', $currentTerm->id ?? null)
                ->where('status', 'succeeded')
                ->sum('amount');

            // Also include payments from SchoolPayment (public payment portal)
            $totalPaidFromPayments = SchoolPayment::where('student_id', $student->id)
                ->where('academic_period_id', $currentTerm->id ?? null)
                ->where('status', 'succeeded')
                ->sum('amount');

            $totalPaid = $totalPaidFromFees + $totalPaidFromPayments;

            $termTotalAmount = $feeStructure->term_total_amount ?? $feeStructure->amount ?? 0;
            $remainingAmount = max($termTotalAmount - $totalPaid, 0);

            // Get payment history from SchoolFee
            $feePayments = SchoolFee::where('student_id', $student->id)
                ->with(['payer', 'academicPeriod', 'student.academicGroup', 'student.academicLevel'])
                ->get();

            // Get payment history from SchoolPayment
            $schoolPayments = SchoolPayment::where('student_id', $student->id)
                ->with(['payer', 'academicPeriod', 'student.academicGroup', 'student.academicLevel'])
                ->get();

            // Combine and sort by created_at
            $paymentHistory = $feePayments->concat($schoolPayments)
                ->sortByDesc('created_at')
                ->values();

            // Paginate manually if needed (or use a union query)
            $page = request()->get('page', 1);
            $perPage = 10;
            $paymentHistory = new \Illuminate\Pagination\LengthAwarePaginator(
                $paymentHistory->forPage($page, $perPage),
                $paymentHistory->count(),
                $perPage,
                $page,
                ['path' => request()->url()]
            );

            // Get pending payments from both tables
            $pendingPayments = SchoolFee::where('student_id', $student->id)
                ->where('status', 'pending')
                ->count()
                + SchoolPayment::where('student_id', $student->id)
                    ->where('status', 'pending')
                    ->count();

            // ... existing code for termPayments ...
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
        } catch (\Exception $e) {
            Log::error('Error in StudentFeeController@index', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id(),
            ]);

            return redirect()->route('dashboard')
                ->with('error', 'An error occurred. Please try again.');
        }
    }

    /**
     * Show payment form
     */
    public function payment()
    {
        $user = Auth::user();
        $schoolId = getSchoolId(); // Get school_id from User

        if (! $schoolId) {
            return redirect()->route('dashboard')
                ->with('error', 'School context not found. Please contact your administrator.');
        }

        // Get student by user_id ONLY (student.school_id might be null)
        $student = Student::withoutGlobalScopes()->where('user_id', $user->id)->first();

        if (! $student) {
            Log::warning('Student accessing payments without student profile', [
                'user_id' => $user->id,
                'user_email' => $user->email,
                'user_school_id' => $schoolId,
            ]);

            return redirect()->route('dashboard')
                ->with('error', 'Student profile not found. Please contact your administrator.');
        }

        $currentTerm = AcademicPeriod::where('status', 'active')->first();

        $feeStructure = AcademicFeeStructure::where('school_id', $schoolId)
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

        $user = Auth::user();
        $schoolId = getSchoolId(); // Get school_id from User

        if (! $schoolId) {
            return redirect()->route('dashboard')
                ->with('error', 'School context not found. Please contact your administrator.');
        }

        // Get student by user_id ONLY (student.school_id might be null)
        $student = Student::withoutGlobalScopes()->where('user_id', $user->id)->first();

        if (! $student) {
            Log::warning('Student accessing payments without student profile', [
                'user_id' => $user->id,
                'user_email' => $user->email,
                'user_school_id' => $schoolId,
            ]);

            return redirect()->route('dashboard')
                ->with('error', 'Student profile not found. Please contact your administrator.');
        }

        $currentTerm = AcademicPeriod::where('status', 'active')->first();

        // Get subaccount
        $subaccount = \App\Models\Subaccount::where('school_id', $schoolId)->first();

        $paymentData = [
            'email' => Auth::user()->email,
            'amount' => $validated['amount'] * 100,
            'currency' => 'GHS',
            'callback_url' => route('students.fees.callback'),
            'metadata' => [
                'student_id' => $student->id,
                'term_id' => $currentTerm->id ?? null,
                'school_id' => $schoolId,
                'cancel_action' => route('students.fees.index'),
            ],
        ];

        if ($subaccount && $subaccount->subaccount_code) {
            $paymentData['subaccount'] = $subaccount->subaccount_code;
        }

        $response = $this->paystack->initializeTransaction($paymentData);

        if (empty($response['status']) || ! $response['status']) {
            return back()->withErrors(['error' => 'Unable to initialize payment. Please try again.']);
        }

        $reference = $response['data']['reference'];

        $feeStructure = AcademicFeeStructure::where('school_id', $schoolId)
            ->where('academic_group_id', $student->academic_group_id)
            ->where('academic_level_id', $student->academic_level_id)
            ->where('current_term_id', $currentTerm->id ?? null)
            ->first();

        $school = School::findOrFail($schoolId);

        SchoolFee::create([
            'school_id' => $schoolId,
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

        if (! $reference) {
            return redirect()->route('students.fees.index')
                ->with('error', 'Payment reference not found.');
        }

        $response = $this->paystack->verifyTransaction($reference);

        $payment = SchoolFee::where('reference', $reference)->first();

        if ($payment) {
            if (! empty($response['status']) && $response['status'] && $response['data']['status'] === 'success') {
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
