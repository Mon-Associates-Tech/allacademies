<?php

namespace App\Livewire\Administrators;

use App\Models\AcademicGroup;
use App\Models\AcademicLevel;
use App\Models\AcademicPeriod;
use App\Models\AcademicYear;
use App\Models\SchoolPayment;
use App\Models\SchoolPaymentStructure;
use App\Models\Student;
use App\Models\StudentPaymentRecord;
use App\Models\Subaccount;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\WithPagination;

class PaymentManagement extends Component
{
    use WithPagination;

    public $viewMode = 'transactions'; // 'transactions' or 'students'
    
    // Filters
    public $searchTerm = '';
    public $filterPaymentType = '';
    public $filterStatus = '';
    public $filterAcademicGroup = '';
    public $filterAcademicLevel = '';
    public $filterAcademicYear = '';
    public $filterAcademicPeriod = '';
    public $filterSubaccount = '';
    public $filterDateFrom = '';
    public $filterDateTo = '';

    protected $queryString = [
        'viewMode',
        'searchTerm' => ['except' => ''],
        'filterPaymentType' => ['except' => ''],
        'filterStatus' => ['except' => ''],
    ];

    public function mount()
    {
        //
    }

    public function toggleViewMode()
    {
        $this->viewMode = $this->viewMode === 'transactions' ? 'students' : 'transactions';
        $this->resetPage();
    }

    public function updatedSearchTerm()
    {
        $this->resetPage();
    }

    public function updatedFilterPaymentType()
    {
        $this->resetPage();
    }

    public function updatedFilterStatus()
    {
        $this->resetPage();
    }

    public function updatedFilterAcademicGroup()
    {
        $this->filterAcademicLevel = '';
        $this->resetPage();
    }

    public function clearFilters()
    {
        $this->searchTerm = '';
        $this->filterPaymentType = '';
        $this->filterStatus = '';
        $this->filterAcademicGroup = '';
        $this->filterAcademicLevel = '';
        $this->filterAcademicYear = '';
        $this->filterAcademicPeriod = '';
        $this->filterSubaccount = '';
        $this->filterDateFrom = '';
        $this->filterDateTo = '';
        $this->resetPage();
    }

    public function render()
    {
        $schoolId = getSchoolId();

        if (!$schoolId) {
            session()->flash('error', 'Please select a school to manage payments.');
            return view('livewire.administrators.payment-management', [
                'data' => collect(),
                'stats' => [],
                'filterOptions' => [],
            ]);
        }

        $filterOptions = $this->getFilterOptions($schoolId);
        
        if ($this->viewMode === 'transactions') {
            $data = $this->getTransactions($schoolId);
            $stats = $this->getTransactionStats($schoolId);
        } else {
            $data = $this->getStudentPayments($schoolId);
            $stats = $this->getStudentPaymentStats($schoolId);
        }

        return view('livewire.administrators.payment-management', [
            'data' => $data,
            'stats' => $stats,
            'filterOptions' => $filterOptions,
        ]);
    }

    protected function getTransactions($schoolId)
    {
        $query = SchoolPayment::with([
            'student.user',
            'academicGroup',
            'academicLevel',
            'academicYear',
            'academicPeriod',
            'subaccount',
            'payer',
        ])->where('school_id', $schoolId);

        // Apply filters
        if ($this->filterPaymentType) {
            $query->where('payment_type', $this->filterPaymentType);
        }

        if ($this->filterStatus) {
            $query->where('status', $this->filterStatus);
        }

        if ($this->filterAcademicGroup) {
            $query->where('academic_group_id', $this->filterAcademicGroup);
        }

        if ($this->filterAcademicLevel) {
            $query->where('academic_level_id', $this->filterAcademicLevel);
        }

        if ($this->filterAcademicYear) {
            $query->where('academic_year_id', $this->filterAcademicYear);
        }

        if ($this->filterAcademicPeriod) {
            $query->where('academic_period_id', $this->filterAcademicPeriod);
        }

        if ($this->filterSubaccount) {
            $query->where('subaccount_id', $this->filterSubaccount);
        }

        if ($this->filterDateFrom) {
            $query->whereDate('created_at', '>=', $this->filterDateFrom);
        }

        if ($this->filterDateTo) {
            $query->whereDate('created_at', '<=', $this->filterDateTo);
        }

        if ($this->searchTerm) {
            $query->where(function ($q) {
                $q->where('reference', 'like', "%{$this->searchTerm}%")
                    ->orWhere('payer_name', 'like', "%{$this->searchTerm}%")
                    ->orWhere('payer_email', 'like', "%{$this->searchTerm}%")
                    ->orWhereHas('student.user', function ($userQuery) {
                        $userQuery->where('name', 'like', "%{$this->searchTerm}%");
                    });
            });
        }

        return $query->latest()->paginate(20);
    }

    protected function getStudentPayments($schoolId)
    {
        // Get all students with their payment records aggregated
        $query = Student::with([
            'user',
            'academicGroup',
            'academicLevel',
        ])
        ->where('school_id', $schoolId)
        ->where('status', 'active');

        // Apply filters
        if ($this->filterAcademicGroup) {
            $query->where('academic_group_id', $this->filterAcademicGroup);
        }

        if ($this->filterAcademicLevel) {
            $query->where('academic_level_id', $this->filterAcademicLevel);
        }

        if ($this->searchTerm) {
            $query->whereHas('user', function ($q) {
                $q->where('name', 'like', "%{$this->searchTerm}%")
                    ->orWhere('email', 'like', "%{$this->searchTerm}%");
            });
        }

        // Get students with payment summary
        $students = $query->paginate(20);
        
        // Attach payment summary to each student
        $students->getCollection()->transform(function ($student) use ($schoolId) {
            $paymentRecordsQuery = StudentPaymentRecord::where('student_id', $student->id)
                ->where('school_id', $schoolId);
            
            // Apply payment-specific filters
            if ($this->filterPaymentType) {
                $paymentRecordsQuery->where('payment_type', $this->filterPaymentType);
            }
            
            if ($this->filterAcademicYear) {
                $paymentRecordsQuery->where('academic_year_id', $this->filterAcademicYear);
            }
            
            if ($this->filterAcademicPeriod) {
                $paymentRecordsQuery->where('academic_period_id', $this->filterAcademicPeriod);
            }
            
            $student->payment_summary = [
                'total_expected' => $paymentRecordsQuery->sum('total_amount'),
                'total_paid' => $paymentRecordsQuery->sum('amount_paid'),
                'total_outstanding' => $paymentRecordsQuery->sum('amount_remaining'),
                'records_count' => $paymentRecordsQuery->count(),
                'overdue_count' => (clone $paymentRecordsQuery)->overdue()->count(),
            ];
            
            // Determine overall status
            if ($student->payment_summary['records_count'] == 0) {
                $student->payment_status = 'no_obligations';
            } elseif ($student->payment_summary['total_outstanding'] == 0) {
                $student->payment_status = 'paid';
            } elseif ($student->payment_summary['total_paid'] > 0) {
                $student->payment_status = 'partial';
            } elseif ($student->payment_summary['overdue_count'] > 0) {
                $student->payment_status = 'overdue';
            } else {
                $student->payment_status = 'unpaid';
            }
            
            return $student;
        });
        
        // Apply status filter after loading
        if ($this->filterStatus) {
            $students->setCollection(
                $students->getCollection()->filter(function ($student) {
                    return $student->payment_status === $this->filterStatus;
                })
            );
        }

        return $students;
    }

    protected function getTransactionStats($schoolId)
    {
        $baseQuery = SchoolPayment::where('school_id', $schoolId);

        // Apply same filters
        if ($this->filterPaymentType) {
            $baseQuery->where('payment_type', $this->filterPaymentType);
        }
        if ($this->filterAcademicYear) {
            $baseQuery->where('academic_year_id', $this->filterAcademicYear);
        }
        if ($this->filterAcademicPeriod) {
            $baseQuery->where('academic_period_id', $this->filterAcademicPeriod);
        }
        if ($this->filterDateFrom && $this->filterDateTo) {
            $baseQuery->whereBetween('created_at', [$this->filterDateFrom, $this->filterDateTo]);
        }

        return [
            'total_collected' => $baseQuery->succeeded()->sum('amount'),
            'pending_amount' => $baseQuery->pending()->sum('amount'),
            'succeeded_count' => $baseQuery->succeeded()->count(),
            'pending_count' => $baseQuery->pending()->count(),
            'failed_count' => $baseQuery->where('status', 'failed')->count(),
            'this_month' => $baseQuery->succeeded()->thisMonth()->sum('amount'),
        ];
    }

    protected function getStudentPaymentStats($schoolId)
    {
        $baseQuery = StudentPaymentRecord::where('school_id', $schoolId);

        // Apply same filters
        if ($this->filterPaymentType) {
            $baseQuery->where('payment_type', $this->filterPaymentType);
        }
        if ($this->filterAcademicYear) {
            $baseQuery->where('academic_year_id', $this->filterAcademicYear);
        }
        if ($this->filterAcademicPeriod) {
            $baseQuery->where('academic_period_id', $this->filterAcademicPeriod);
        }

        // Count students by status
        $totalStudents = Student::where('school_id', $schoolId)->where('status', 'active')->count();
        $studentsWithRecords = StudentPaymentRecord::where('school_id', $schoolId)
            ->distinct('student_id')
            ->count('student_id');

        return [
            'total_expected' => $baseQuery->sum('total_amount'),
            'total_collected' => $baseQuery->sum('amount_paid'),
            'total_outstanding' => $baseQuery->sum('amount_remaining'),
            'paid_count' => $baseQuery->paid()->distinct('student_id')->count('student_id'),
            'partial_count' => $baseQuery->partiallyPaid()->distinct('student_id')->count('student_id'),
            'unpaid_count' => $baseQuery->unpaid()->distinct('student_id')->count('student_id'),
            'overdue_count' => $baseQuery->overdue()->distinct('student_id')->count('student_id'),
            'total_students' => $totalStudents,
            'students_with_obligations' => $studentsWithRecords,
            'students_without_obligations' => $totalStudents - $studentsWithRecords,
        ];
    }

    protected function getFilterOptions($schoolId)
    {
        $predefinedTypes = SchoolPayment::paymentTypes();
        $customTypes = SchoolPaymentStructure::where('school_id', $schoolId)
            ->distinct()
            ->pluck('payment_type')
            ->toArray();
        $allPaymentTypes = array_merge($predefinedTypes, array_flip(array_diff($customTypes, array_keys($predefinedTypes))));

        $subaccounts = Subaccount::whereIn('subaccountable_type', ['App\\Models\\School', 'school', \App\Models\School::class])
            ->where('subaccountable_id', $schoolId)
            ->where('status', 'active')
            ->orderBy('is_primary', 'desc')
            ->get()
            ->map(fn($account) => [
                'id' => $account->id,
                'name' => ($account->is_primary ? '[Primary] ' : '') . ($account->name ?? $account->business_name),
            ]);

        return [
            'payment_types' => $allPaymentTypes,
            'subaccounts' => $subaccounts,
            'academic_groups' => AcademicGroup::forSchool($schoolId)->get(),
            'academic_levels' => AcademicLevel::forSchool($schoolId)->get(),
            'academic_years' => AcademicYear::where('school_id', $schoolId)->orderBy('start_date', 'desc')->get(),
            'academic_periods' => AcademicPeriod::where('school_id', $schoolId)->orderBy('start_date', 'desc')->get(),
        ];
    }
}
