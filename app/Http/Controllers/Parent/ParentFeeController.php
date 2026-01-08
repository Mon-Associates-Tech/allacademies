<?php

namespace App\Http\Controllers\Parent;

use App\Http\Controllers\Controller;
use App\Models\AcademicFeeStructure;
use App\Models\AcademicPeriod;
use App\Models\School;
use App\Models\SchoolFee;
use App\Models\SchoolPayment;
use App\Models\Student;
use App\Models\StudentParent;
use App\Services\PaystackService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ParentFeeController extends Controller
{
    protected $paystack;

    public function __construct(PaystackService $paystack)
    {
        $this->paystack = $paystack;
    }

    /**
     * Display fee dashboard for parent with all their wards
     */
    public function index()
    {
        try {
            $user = Auth::user();
            $schoolId = getSchoolId();

            if (!$schoolId) {
                return redirect()->route('parent.dashboard')
                    ->with('error', 'School context not found. Please contact your administrator.');
            }

            // Get parent's wards
            $parent = StudentParent::withoutGlobalScopes()
                ->where('user_id', $user->id)
                ->first();

            if (!$parent) {
                return redirect()->route('parent.dashboard')
                    ->with('error', 'Parent profile not found. Please contact your administrator.');
            }

            $students = $parent->students()
                ->withoutGlobalScopes()
                ->with(['user', 'academicLevel', 'academicGroup'])
                ->get();

            if ($students->isEmpty()) {
                return redirect()->route('parent.dashboard')
                    ->with('info', 'No wards found in your account.');
            }

            $currentTerm = AcademicPeriod::where('school_id', $schoolId)
                ->where('is_current', 1)
                ->orWhere('status', 'active')
                ->first();

            // Get payment summary for each student
            $studentsWithFees = $students->map(function ($student) use ($schoolId, $currentTerm) {
                $feeStructure = AcademicFeeStructure::where('school_id', $schoolId)
                    ->where('academic_group_id', $student->academic_group_id)
                    ->where('academic_level_id', $student->academic_level_id)
                    ->where('current_term_id', $currentTerm->id ?? null)
                    ->first();

                $totalPaid = SchoolFee::where('student_id', $student->id)
                    ->where('term_id', $currentTerm->id ?? null)
                    ->where('status', 'succeeded')
                    ->sum('amount');

                $schoolPaymentsPaid = SchoolPayment::where('student_id', $student->id)
                    ->where('academic_period_id', $currentTerm->id ?? null)
                    ->where('status', 'succeeded')
                    ->sum('amount');

                $termTotalAmount = $feeStructure->term_total_amount ?? $feeStructure->amount ?? 0;
                $remainingAmount = max($termTotalAmount - $totalPaid, 0);

                return [
                    'student' => $student,
                    'feeStructure' => $feeStructure,
                    'totalPaid' => $totalPaid + $schoolPaymentsPaid,
                    'termTotalAmount' => $termTotalAmount,
                    'otherPayments' => $schoolPaymentsPaid,
                    'remainingAmount' => $remainingAmount,
                ];
            });

            return view('livewire.parent.fees.index', compact(
                'studentsWithFees',
                'currentTerm'
            ));
        } catch (\Exception $e) {
            Log::error('Error in ParentFeeController@index', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id()
            ]);

            return redirect()->route('parent.dashboard')
                ->with('error', 'An error occurred. Please try again.');
        }
    }

    /**
     * Show payment form for a specific student or allow selection
     */
    public function payment(Request $request, $studentId = null)
    {
        $user = Auth::user();
        $schoolId = getSchoolId();

        if (!$schoolId) {
            return redirect()->route('parent.dashboard')
                ->with('error', 'School context not found.');
        }

        $parent = StudentParent::withoutGlobalScopes()
            ->where('user_id', $user->id)
            ->first();

        if (!$parent) {
            return redirect()->route('parent.dashboard')
                ->with('error', 'Parent profile not found.');
        }

        $students = $parent->students()
            ->withoutGlobalScopes()
            ->with(['user', 'academicLevel', 'academicGroup'])
            ->get();

        $selectedStudent = null;
        $feeStructure = null;
        $totalPaid = 0;
        $termTotalAmount = 0;
        $remainingAmount = 0;
        $paymentType = $request->get('type', 'school_fee'); // school_fee or school_payment

        $paymentStructures = \App\Models\SchoolPaymentStructure::where('school_id', $schoolId)
            ->where('is_active', true)
            ->get();

        $currentTerm = AcademicPeriod::where('school_id', $schoolId)
            ->where('is_current', 1)
            ->orWhere('status', 'active')
            ->first();

        if ($studentId) {
            $selectedStudent = $students->firstWhere('id', $studentId);

            if ($selectedStudent) {
                $feeStructure = AcademicFeeStructure::where('school_id', $schoolId)
                    ->where('academic_group_id', $selectedStudent->academic_group_id)
                    ->where('academic_level_id', $selectedStudent->academic_level_id)
                    ->where('current_term_id', $currentTerm->id ?? null)
                    ->first();

                $totalPaid = SchoolFee::where('student_id', $selectedStudent->id)
                    ->where('term_id', $currentTerm->id ?? null)
                    ->where('status', 'succeeded')
                    ->sum('amount');

                $schoolPaymentsPaid = SchoolPayment::where('student_id', $selectedStudent->id)
                    ->where('academic_period_id', $currentTerm->id ?? null)
                    ->where('status', 'succeeded')
                    ->sum('amount');

//                $totalPaid += $schoolPaymentsPaid;
                $termTotalAmount = $feeStructure->term_total_amount ?? $feeStructure->amount ?? 0;
                $remainingAmount = max($termTotalAmount - $totalPaid, 0);
            }
        }

        return view('livewire.parent.fees.payment', compact(
            'students',
            'selectedStudent',
            'feeStructure',
            'totalPaid',
            'termTotalAmount',
            'remainingAmount',
            'currentTerm',
            'paymentType',
            'paymentStructures'
        ));
    }

    /**
     * Initialize payment
     */
    public function initializePayment(Request $request)
    {
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'amount' => 'required|numeric|min:1',
            'payment_type' => 'required|in:school_fee,school_payment',
            'payment_structure_id' => 'required_if:payment_type,school_payment|nullable|exists:school_payment_structures,id',
        ]);

        $user = Auth::user();
        $schoolId = getSchoolId();

        if (!$schoolId) {
            return back()->withErrors(['error' => 'School context not found.']);
        }

        $parent = StudentParent::withoutGlobalScopes()
            ->where('user_id', $user->id)
            ->first();

        if (!$parent) {
            return back()->withErrors(['error' => 'Parent profile not found.']);
        }

        $student = $parent->students()
            ->withoutGlobalScopes()
            ->where('students.id', $validated['student_id'])
            ->first();

        if (!$student) {
            return back()->withErrors(['error' => 'Student not found or not associated with your account.']);
        }

        $currentTerm = AcademicPeriod::where('school_id', $schoolId)
            ->where('is_current', 1)
            ->orWhere('status', 'active')
            ->first();

        // Get payment structure if school_payment
        $paymentStructure = null;
        $paymentTypeName = 'other';
        if ($validated['payment_type'] === 'school_payment' && isset($validated['payment_structure_id'])) {
            $paymentStructure = \App\Models\SchoolPaymentStructure::find($validated['payment_structure_id']);
            $paymentTypeName = $paymentStructure->payment_type ?? 'other';
        }

        // Get subaccount
        $subaccount = \App\Models\Subaccount::where('school_id', $schoolId)->first();

        $paymentData = [
            'email' => $user->email,
            'amount' => $validated['amount'] * 100,
            'currency' => 'GHS',
            'callback_url' => route('parent.fees.callback'),
            'metadata' => [
                'student_id' => $student->id,
                'term_id' => $currentTerm->id ?? null,
                'school_id' => $schoolId,
                'payer_id' => $user->id,
                'payer_type' => 'parent',
                'payment_type' => $validated['payment_type'],
                'payment_structure_id' => $validated['payment_structure_id'] ?? null,
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
        $school = School::findOrFail($schoolId);

        if ($validated['payment_type'] === 'school_fee') {
            $feeStructure = AcademicFeeStructure::where('school_id', $schoolId)
                ->where('academic_group_id', $student->academic_group_id)
                ->where('academic_level_id', $student->academic_level_id)
                ->where('current_term_id', $currentTerm->id ?? null)
                ->first();

            SchoolFee::create([
                'school_id' => $schoolId,
                'student_id' => $student->id,
                'payer_id' => $parent->id,
                'payer_type' => 'parent',
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
        } else {
            SchoolPayment::create([
                'school_id' => $schoolId,
                'student_id' => $student->id,
                'academic_group_id' => $student->academic_group_id,
                'academic_level_id' => $student->academic_level_id,
                'academic_period_id' => $currentTerm->id ?? null,
                'school_payment_structure_id' => $validated['payment_structure_id'] ?? null,
                'payment_type' => $paymentTypeName,
                'amount' => $validated['amount'],
                'currency' => 'GHS',
                'payer_type' => 'parent',
                'payer_id' => $parent->id,
                'payer_name' => $parent->user->name,
                'payer_email' => $parent->user->email,
                'status' => 'pending',
                'reference' => $reference,
                'gateway' => 'paystack',
                'authorization_url' => $response['data']['authorization_url'],
                'gateway_response' => $response,
                'created_by' => $user->id,
            ]);
        }

        return redirect($response['data']['authorization_url']);
    }
    /**
     * Payment callback
     */
    public function callback(Request $request)
    {
        $reference = $request->query('reference');

        if (!$reference) {
            return redirect()->route('parent.fees.index')
                ->with('error', 'Payment reference not found.');
        }

        $response = $this->paystack->verifyTransaction($reference);

        // Try SchoolFee first
        $payment = SchoolFee::where('reference', $reference)->first();
        $isSchoolFee = true;

        // If not found, try SchoolPayment
        if (!$payment) {
            $payment = SchoolPayment::where('reference', $reference)->first();
            $isSchoolFee = false;
        }

        if ($payment) {
            if (!empty($response['status']) && $response['status'] && $response['data']['status'] === 'success') {
                $payment->update([
                    'status' => 'succeeded',
                    'paystack_response' => $isSchoolFee ? json_encode($response) : $response,
                    'paid_at' => now(),
                ]);

                return redirect()->route('parent.fees.receipt', ['payment' => $payment->id, 'type' => $isSchoolFee ? 'school_fee' : 'school_payment'])
                    ->with('success', 'Payment successful!');
            } else {
                $payment->update(['status' => 'failed']);

                return redirect()->route('parent.fees.index')
                    ->with('error', 'Payment verification failed.');
            }
        }

        return redirect()->route('parent.fees.index')
            ->with('error', 'Payment record not found.');
    }

    /**
     * Show payment receipt
     */
    public function receipt(Request $request, $paymentId)
    {
        $type = $request->query('type', 'school_fee');

        if ($type === 'school_fee') {
            $payment = SchoolFee::findOrFail($paymentId);
        } else {
            $payment = SchoolPayment::findOrFail($paymentId);
        }

        // Ensure user can only view receipts for their wards
        $user = Auth::user();
        $parent = StudentParent::withoutGlobalScopes()
            ->where('user_id', $user->id)
            ->first();

        if (!$parent) {
            abort(403);
        }


        $studentIds = $parent->students()->withoutGlobalScopes()->pluck('students.id');

        if (!$studentIds->contains($payment->student_id)) {
            abort(403);
        }

        $payment->load(['student.academicGroup', 'student.academicLevel', 'student.user', 'academicPeriod', 'payer', 'school']);
        $payment->student = Student::withoutGlobalScopes()
            ->with(['academicGroup', 'academicLevel', 'user', 'school'])
            ->find($payment->student_id);
        return view('livewire.parent.fees.receipt', compact('payment', 'type'));
    }

    /**
     * Display all transactions for parent's wards (includes payments by both parent and students)
     */
    public function transactions(Request $request)
    {
        try {
            $user = Auth::user();
            $schoolId = getSchoolId();

            if (!$schoolId) {
                return redirect()->route('parent.dashboard')
                    ->with('error', 'School context not found.');
            }

            // Get parent's wards
            $parent = StudentParent::withoutGlobalScopes()
                ->where('user_id', $user->id)
                ->first();

            if (!$parent) {
                return redirect()->route('parent.dashboard')
                    ->with('error', 'Parent profile not found.');
            }

            $students = $parent->students()
                ->withoutGlobalScopes()
                ->with(['user', 'academicLevel', 'academicGroup'])
                ->get();

            if ($students->isEmpty()) {
                return redirect()->route('parent.dashboard')
                    ->with('info', 'No wards found in your account.');
            }

            $studentIds = $students->pluck('id');

            // Build query for filters
            $selectedStudentId = $request->get('student_id');
            $selectedType = $request->get('type'); // school_fee or school_payment
            $selectedStatus = $request->get('status');
            $dateFrom = $request->get('date_from');
            $dateTo = $request->get('date_to');

            // Get SchoolFees
            $schoolFeesQuery = SchoolFee::whereIn('student_id', $studentIds)
                ->with([
                    'payer',
                    'academicPeriod',
                    'student' => function($query) {
                        $query->withoutGlobalScopes()->with(['user', 'academicLevel', 'academicGroup']);
                    }
                ]);

            if ($selectedStudentId) {
                $schoolFeesQuery->where('student_id', $selectedStudentId);
            }
            if ($selectedStatus) {
                $schoolFeesQuery->where('status', $selectedStatus);
            }
            if ($dateFrom) {
                $schoolFeesQuery->whereDate('created_at', '>=', $dateFrom);
            }
            if ($dateTo) {
                $schoolFeesQuery->whereDate('created_at', '<=', $dateTo);
            }

            $schoolFees = $schoolFeesQuery->get()->map(function ($fee) {
                $fee->payment_category = 'School Fee';
                $fee->payment_model = 'school_fee';
                return $fee;
            });

            // Get SchoolPayments
            $schoolPaymentsQuery = SchoolPayment::whereIn('student_id', $studentIds)
                ->with([
                    'payer',
                    'academicPeriod',
                    'student' => function($query) {
                        $query->withoutGlobalScopes()->with(['user', 'academicLevel', 'academicGroup']);
                    }
                ]);

            if ($selectedStudentId) {
                $schoolPaymentsQuery->where('student_id', $selectedStudentId);
            }
            if ($selectedStatus) {
                $schoolPaymentsQuery->where('status', $selectedStatus);
            }
            if ($dateFrom) {
                $schoolPaymentsQuery->whereDate('created_at', '>=', $dateFrom);
            }
            if ($dateTo) {
                $schoolPaymentsQuery->whereDate('created_at', '<=', $dateTo);
            }

            $schoolPayments = $schoolPaymentsQuery->get()->map(function ($payment) {
                $payment->payment_category = 'School Payment';
                $payment->payment_model = 'school_payment';
                return $payment;
            });

            // Merge and sort
            $allTransactions = $schoolFees->concat($schoolPayments)
                ->sortByDesc('created_at');

            // Apply type filter if specified
            if ($selectedType) {
                if ($selectedType === 'school_fee') {
                    $allTransactions = $schoolFees->sortByDesc('created_at');
                } elseif ($selectedType === 'school_payment') {
                    $allTransactions = $schoolPayments->sortByDesc('created_at');
                }
            }

            // Paginate manually
            $perPage = 20;
            $currentPage = $request->get('page', 1);
            $transactions = new \Illuminate\Pagination\LengthAwarePaginator(
                $allTransactions->forPage($currentPage, $perPage),
                $allTransactions->count(),
                $perPage,
                $currentPage,
                ['path' => $request->url(), 'query' => $request->query()]
            );

            // Calculate statistics
            $stats = [
                'total_transactions' => $allTransactions->count(),
                'total_amount' => $allTransactions->where('status', 'succeeded')->sum('amount'),
                'pending_amount' => $allTransactions->where('status', 'pending')->sum('amount'),
                'succeeded_count' => $allTransactions->where('status', 'succeeded')->count(),
                'pending_count' => $allTransactions->where('status', 'pending')->count(),
                'failed_count' => $allTransactions->where('status', 'failed')->count(),
                'by_student' => $allTransactions->where('status', 'succeeded')->groupBy('student_id')->map(function ($group) {
                    return [
                        'student' => $group->first()->student,
                        'total' => $group->sum('amount'),
                        'count' => $group->count()
                    ];
                }),
                'by_payer' => $allTransactions->where('status', 'succeeded')->groupBy('payer_id')->map(function ($group) {
                    return [
                        'payer' => $group->first()->payer,
                        'total' => $group->sum('amount'),
                        'count' => $group->count()
                    ];
                }),
            ];

            $currentTerm = AcademicPeriod::where('school_id', $schoolId)
                ->where('is_current', 1)
                ->orWhere('status', 'active')
                ->first();

            return view('livewire.parent.fees.transactions', compact(
                'transactions',
                'students',
                'stats',
                'currentTerm',
                'selectedStudentId',
                'selectedType',
                'selectedStatus',
                'dateFrom',
                'dateTo'
            ));
        } catch (\Exception $e) {
            Log::error('Error in ParentFeeController@transactions', [
                'error' => $e->getMessage(),
                'user_id' => Auth::id()
            ]);

            return redirect()->route('parent.dashboard')
                ->with('error', 'An error occurred. Please try again.');
        }
    }
}
