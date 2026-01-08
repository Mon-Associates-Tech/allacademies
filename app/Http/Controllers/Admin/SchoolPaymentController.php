<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SchoolPayment;
use App\Models\SchoolPaymentStructure;
use App\Models\Student;
use App\Models\AcademicGroup;
use App\Models\AcademicLevel;
use App\Models\AcademicYear;
use App\Models\AcademicPeriod;
use App\Models\Subaccount;
use App\Models\School;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class SchoolPaymentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display payment dashboard with statistics and filters
     */
    public function index(Request $request)
    {
        $schoolId = getSchoolId();

        // Build query with filters
        $query = SchoolPayment::with([
            'student' => function ($query) use ($schoolId) {
                // Explicitly filter by school_id to work with global scope
                $query->withoutGlobalScope(\App\Scopes\SchoolScope::class)
                    ->where('school_id', $schoolId);
            },
            'student.user',
            'academicGroup',
            'academicLevel',
            'academicYear',
            'academicPeriod',
            'subaccount',
            'payer'
        ])
            ->where('school_id', $schoolId);

        // Apply filters
        if ($request->filled('payment_type')) {
            $query->where('payment_type', $request->payment_type);
        }

        if ($request->filled('academic_group_id')) {
            $query->where('academic_group_id', $request->academic_group_id);
        }

        if ($request->filled('academic_level_id')) {
            $query->where('academic_level_id', $request->academic_level_id);
        }

        if ($request->filled('academic_year_id')) {
            $query->where('academic_year_id', $request->academic_year_id);
        }

        if ($request->filled('academic_period_id')) {
            $query->where('academic_period_id', $request->academic_period_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('payer_type')) {
            $query->where('payer_type', $request->payer_type);
        }

        if ($request->filled('subaccount_id')) {
            $query->where('subaccount_id', $request->subaccount_id);
        }

        if ($request->filled('student_id')) {
            $query->where('student_id', $request->student_id);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('reference', 'like', "%{$search}%")
                    ->orWhere('payer_name', 'like', "%{$search}%")
                    ->orWhere('payer_email', 'like', "%{$search}%")
                    ->orWhereHas('student.user', function ($userQuery) use ($search) {
                        $userQuery->where('name', 'like', "%{$search}%");
                    });
            });
        }

        $payments = $query->latest()->paginate(20)->withQueryString();

        // Calculate statistics
        $stats = $this->calculateStatistics($schoolId, $request);

        // Get filter options
        $filterOptions = $this->getFilterOptions($schoolId);

        return view('admin.payments.index', compact('payments', 'stats', 'filterOptions'));
    }

    /**
     * Calculate payment statistics
     */
    protected function calculateStatistics($schoolId, Request $request)
    {
        $baseQuery = SchoolPayment::where('school_id', $schoolId);

        // Apply same filters as main query
        if ($request->filled('payment_type')) {
            $baseQuery->where('payment_type', $request->payment_type);
        }
        if ($request->filled('academic_year_id')) {
            $baseQuery->where('academic_year_id', $request->academic_year_id);
        }
        if ($request->filled('academic_period_id')) {
            $baseQuery->where('academic_period_id', $request->academic_period_id);
        }
        if ($request->filled('date_from') && $request->filled('date_to')) {
            $baseQuery->whereBetween('created_at', [$request->date_from, $request->date_to]);
        }

        return [
            'total_payments' => $baseQuery->count(),
            'total_amount' => $baseQuery->succeeded()->sum('amount'),
            'pending_amount' => $baseQuery->pending()->sum('amount'),
            'succeeded_count' => $baseQuery->succeeded()->count(),
            'pending_count' => $baseQuery->pending()->count(),
            'failed_count' => $baseQuery->where('status', 'failed')->count(),
            'this_month_amount' => $baseQuery->succeeded()->thisMonth()->sum('amount'),
            'this_year_amount' => $baseQuery->succeeded()->thisYear()->sum('amount'),

            // Group by payment type
            'by_type' => $baseQuery->succeeded()
                ->select('payment_type', DB::raw('SUM(amount) as total'), DB::raw('COUNT(*) as count'))
                ->groupBy('payment_type')
                ->get()
                ->pluck('total', 'payment_type'),

            // Group by academic group
            'by_group' => $baseQuery->succeeded()
                ->select('academic_group_id', DB::raw('SUM(amount) as total'))
                ->whereNotNull('academic_group_id')
                ->groupBy('academic_group_id')
                ->get()
                ->pluck('total', 'academic_group_id'),

            // Group by level
            'by_level' => $baseQuery->succeeded()
                ->select('academic_level_id', DB::raw('SUM(amount) as total'))
                ->whereNotNull('academic_level_id')
                ->groupBy('academic_level_id')
                ->get()
                ->pluck('total', 'academic_level_id'),
        ];
    }

    /**
     * Get filter options for dropdowns
     */
    protected function getFilterOptions($schoolId)
    {
        // Get predefined payment types
        $predefinedTypes = SchoolPayment::paymentTypes();
        
        // Get custom payment types from SchoolPaymentStructure
        $customTypes = SchoolPaymentStructure::where('school_id', $schoolId)
            ->distinct()
            ->pluck('payment_type')
            ->toArray();
        
        // Merge predefined and custom types, removing duplicates
        $allPaymentTypes = array_merge($predefinedTypes, array_flip(array_diff($customTypes, array_keys($predefinedTypes))));
        
        // Get subaccounts for the school
        $subaccounts = Subaccount::whereIn('subaccountable_type', ['App\Models\School', 'school', School::class])
            ->where('subaccountable_id', $schoolId)
            ->where('status', 'active')
            ->orderBy('is_primary', 'desc')
            ->orderBy('created_at', 'asc')
            ->get()
            ->map(function ($account) {
                return [
                    'id' => $account->id,
                    'name' => ($account->is_primary ? '[Primary] ' : '') . ($account->name ?? $account->business_name),
                ];
            });
        
        return [
            'payment_types' => $allPaymentTypes,
            'payer_types' => SchoolPayment::payerTypes(),
            'subaccounts' => $subaccounts,
            'academic_groups' => AcademicGroup::forSchool($schoolId)->get(),
            'academic_levels' => AcademicLevel::forSchool($schoolId)->get(),
            'academic_years' => AcademicYear::where('school_id', $schoolId)->orderBy('start_date', 'desc')->get(),
            'academic_periods' => AcademicPeriod::where('school_id', $schoolId)->orderBy('start_date', 'desc')->get(),
        ];
    }

    /**
     * Show detailed view of a payment
     */
    public function show(SchoolPayment $payment)
    {
        $schoolId = getSchoolId();

        $payment->load([
            'student' => function ($query) use ($schoolId) {
                // Explicitly filter by school_id to work with global scope
                $query->withoutGlobalScope(\App\Scopes\SchoolScope::class);
                  //  ->where('school_id', $schoolId);
            },
            'student.user',
            'student.academicGroup',
            'student.academicLevel',
            'academicYear',
            'academicPeriod',
            'payer',
            'creator',
            'verifier'
        ]);

        return view('admin.payments.show', compact('payment'));
    }

    /**
     * Export payments to CSV/Excel
     */
    public function export(Request $request)
    {
        // Implement export functionality
        // You can use Laravel Excel or similar package
    }
}
