<?php

namespace App\Livewire\Administrators;

use Livewire\Component;
use App\Models\Student;
use App\Models\Book;
use App\Models\BookBorrowing;
use App\Models\BookSubscription;
use App\Models\StudentGroup;
use App\Models\Teacher;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ReportsManagement extends Component
{
    public $reportType = 'borrowing';
    public $dateRange = 'month';
    public $startDate;
    public $endDate;
    public $studentGroupId;
    public $teacherId;

    public $studentGroups;
    public $teachers;

    public $chartData = [];

    protected $rules = [
        'reportType' => 'required|in:borrowing,subscription,assessment,attendance',
        'dateRange' => 'required|in:week,month,quarter,year,custom',
        'startDate' => 'required_if:dateRange,custom|nullable|date',
        'endDate' => 'required_if:dateRange,custom|nullable|date|after_or_equal:startDate',
        'studentGroupId' => 'nullable|exists:student_groups,id',
        'teacherId' => 'nullable|exists:teachers,id',
    ];

    public function mount()
    {
        $this->studentGroups = StudentGroup::all();
        $this->teachers = Teacher::with('user')->get();
        $this->setDefaultDates();
        $this->generateReport();
    }

    public function setDefaultDates()
    {
        $this->startDate = Carbon::now()->startOfMonth()->format('Y-m-d');
        $this->endDate = Carbon::now()->endOfMonth()->format('Y-m-d');
    }

    public function updatedDateRange()
    {
        switch ($this->dateRange) {
            case 'week':
                $this->startDate = Carbon::now()->startOfWeek()->format('Y-m-d');
                $this->endDate = Carbon::now()->endOfWeek()->format('Y-m-d');
                break;
            case 'month':
                $this->startDate = Carbon::now()->startOfMonth()->format('Y-m-d');
                $this->endDate = Carbon::now()->endOfMonth()->format('Y-m-d');
                break;
            case 'quarter':
                $this->startDate = Carbon::now()->startOfQuarter()->format('Y-m-d');
                $this->endDate = Carbon::now()->endOfQuarter()->format('Y-m-d');
                break;
            case 'year':
                $this->startDate = Carbon::now()->startOfYear()->format('Y-m-d');
                $this->endDate = Carbon::now()->endOfYear()->format('Y-m-d');
                break;
            // 'custom' will leave the dates as they are for user input
        }

        $this->generateReport();
    }

    public function generateReport()
    {
        $this->validate();

        switch ($this->reportType) {
            case 'borrowing':
                $this->generateBorrowingReport();
                break;
            case 'subscription':
                $this->generateSubscriptionReport();
                break;
            case 'assessment':
                $this->generateAssessmentReport();
                break;
            case 'attendance':
                $this->generateAttendanceReport();
                break;
        }
    }

    private function generateBorrowingReport()
    {
        $query = BookBorrowing::whereBetween('borrow_date', [$this->startDate, $this->endDate]);

        if ($this->studentGroupId) {
            $query->whereHas('student', function ($q) {
                $q->where('student_group_id', $this->studentGroupId);
            });
        }

        // Get daily borrowing counts
        $dailyBorrowings = $query->select(
                DB::raw('DATE(borrow_date) as date'),
                DB::raw('COUNT(*) as count')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Get book category distribution
        $categoryBorrowings = BookBorrowing::whereBetween('borrow_date', [$this->startDate, $this->endDate])
            ->whereHas('book', function ($q) {
                if ($this->studentGroupId) {
                    $q->whereHas('borrowings.student', function ($sq) {
                        $sq->where('student_group_id', $this->studentGroupId);
                    });
                }
            })
            ->with('book.bookCategory')
            ->get()
            ->groupBy('book.bookCategory.name')
            ->map(function ($items) {
                return count($items);
            });

        // Get overdue status
        $overdueStatus = BookBorrowing::whereBetween('borrow_date', [$this->startDate, $this->endDate])
            ->where(function ($q) {
                $q->where('status', 'borrowed')
                    ->where('due_date', '<', now());
            })
            ->when($this->studentGroupId, function ($q) {
                $q->whereHas('student', function ($sq) {
                    $sq->where('student_group_id', $this->studentGroupId);
                });
            })
            ->count();

        $onTimeReturns = BookBorrowing::whereBetween('borrow_date', [$this->startDate, $this->endDate])
            ->where('status', 'returned')
            ->whereColumn('return_date', '<=', 'due_date')
            ->when($this->studentGroupId, function ($q) {
                $q->whereHas('student', function ($sq) {
                    $sq->where('student_group_id', $this->studentGroupId);
                });
            })
            ->count();

        $lateReturns = BookBorrowing::whereBetween('borrow_date', [$this->startDate, $this->endDate])
            ->where('status', 'returned')
            ->whereColumn('return_date', '>', 'due_date')
            ->when($this->studentGroupId, function ($q) {
                $q->whereHas('student', function ($sq) {
                    $sq->where('student_group_id', $this->studentGroupId);
                });
            })
            ->count();

        $this->chartData = [
            'dailyBorrowings' => $dailyBorrowings,
            'categoryBorrowings' => $categoryBorrowings,
            'returnStatus' => [
                'overdue' => $overdueStatus,
                'onTime' => $onTimeReturns,
                'late' => $lateReturns
            ]
        ];
    }

    private function generateSubscriptionReport()
    {
        // Get active vs expired subscriptions
        $activeSubscriptions = BookSubscription::where('status', 'active')
            ->when($this->studentGroupId, function ($q) {
                $q->whereHas('student', function ($sq) {
                    $sq->where('student_group_id', $this->studentGroupId);
                });
            })
            ->count();

        $expiredSubscriptions = BookSubscription::where('status', 'expired')
            ->whereBetween('end_date', [$this->startDate, $this->endDate])
            ->when($this->studentGroupId, function ($q) {
                $q->whereHas('student', function ($sq) {
                    $sq->where('student_group_id', $this->studentGroupId);
                });
            })
            ->count();

        // Get new subscriptions per day
        $dailySubscriptions = BookSubscription::whereBetween('start_date', [$this->startDate, $this->endDate])
            ->when($this->studentGroupId, function ($q) {
                $q->whereHas('student', function ($sq) {
                    $sq->where('student_group_id', $this->studentGroupId);
                });
            })
            ->select(
                DB::raw('DATE(start_date) as date'),
                DB::raw('COUNT(*) as count')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Get book category distribution
        $categorySubscriptions = BookSubscription::whereBetween('start_date', [$this->startDate, $this->endDate])
            ->when($this->studentGroupId, function ($q) {
                $q->whereHas('student', function ($sq) {
                    $sq->where('student_group_id', $this->studentGroupId);
                });
            })
            ->with('book.bookCategory')
            ->get()
            ->groupBy('book.bookCategory.name')
            ->map(function ($items) {
                return count($items);
            });

        $this->chartData = [
            'subscriptionStatus' => [
                'active' => $activeSubscriptions,
                'expired' => $expiredSubscriptions
            ],
            'dailySubscriptions' => $dailySubscriptions,
            'categorySubscriptions' => $categorySubscriptions
        ];
    }

    private function generateAssessmentReport()
    {
        // Get average assessment scores by student group
        $groupScores = DB::table('assessments')
            ->join('students', 'assessments.student_id', '=', 'students.id')
            ->join('student_groups', 'students.student_group_id', '=', 'student_groups.id')
            ->select('student_groups.name', DB::raw('AVG(assessments.score) as average_score'))
            ->whereBetween('assessments.created_at', [$this->startDate, $this->endDate])
            ->when($this->studentGroupId, function ($q) {
                $q->where('students.student_group_id', $this->studentGroupId);
            })
            ->groupBy('student_groups.name')
            ->orderBy('average_score', 'desc')
            ->get();

        // Get score distribution
        $scoreDistribution = [
            '0-20' => 0,
            '21-40' => 0,
            '41-60' => 0,
            '61-80' => 0,
            '81-100' => 0,
        ];

        $scores = DB::table('assessments')
            ->join('students', 'assessments.student_id', '=', 'students.id')
            ->select('assessments.score')
            ->whereBetween('assessments.created_at', [$this->startDate, $this->endDate])
            ->when($this->studentGroupId, function ($q) {
                $q->where('students.student_group_id', $this->studentGroupId);
            })
            ->get();

        foreach ($scores as $score) {
            if ($score->score <= 20) {
                $scoreDistribution['0-20']++;
            } elseif ($score->score <= 40) {
                $scoreDistribution['21-40']++;
            } elseif ($score->score <= 60) {
                $scoreDistribution['41-60']++;
            } elseif ($score->score <= 80) {
                $scoreDistribution['61-80']++;
            } else {
                $scoreDistribution['81-100']++;
            }
        }

        $this->chartData = [
            'groupScores' => $groupScores,
            'scoreDistribution' => $scoreDistribution
        ];
    }

    private function generateAttendanceReport()
    {
        // In a real application, you would have an attendance model
        // This is just a placeholder for demonstration

        $this->chartData = [
            'attendanceRate' => [
                'present' => 85,
                'absent' => 15
            ],
            'attendanceByDay' => [
                ['day' => 'Monday', 'rate' => 90],
                ['day' => 'Tuesday', 'rate' => 88],
                ['day' => 'Wednesday', 'rate' => 92],
                ['day' => 'Thursday', 'rate' => 85],
                ['day' => 'Friday', 'rate' => 78]
            ]
        ];
    }

    public function render()
    {
        return view('livewire.administrators.reports');
    }
}
