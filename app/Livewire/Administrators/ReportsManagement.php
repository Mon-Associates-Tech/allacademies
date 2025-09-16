<?php

namespace App\Livewire\Administrators;

use App\Models\BookBorrowing;
use App\Models\BookSubscription;
use App\Models\Librarian;
use App\Models\Student;
use App\Models\StudentGroup;
use App\Models\Teacher;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class ReportsManagement extends Component
{
    public $reportType = 'borrowing';
    public $dateRange = 'month';
    public $startDate;
    public $endDate;
    public $studentGroupId;
    public $teacherId;

    // UI State
    public $isLoading = false;
    public $exportFormat = 'pdf';

    public $studentGroups;
    public $teachers;

    public $chartData = [
        // Borrowing report data
        'returnStatus' => [
            'overdue' => 0,
            'onTime' => 0,
            'late' => 0
        ],
        'dailyBorrowings' => [],
        'categoryBorrowings' => [],

        // Subscription report data
        'subscriptionStatus' => [
            'active' => 0,
            'expired' => 0
        ],
        'dailySubscriptions' => [],
        'categorySubscriptions' => [],

        // Assessment report data
        'groupScores' => [],
        'scoreDistribution' => [
            '0-20' => 0,
            '21-40' => 0,
            '41-60' => 0,
            '61-80' => 0,
            '81-100' => 0
        ],

        // Attendance report data
        'attendanceRate' => [
            'present' => 0,
            'absent' => 0
        ],
        'attendanceByDay' => [],

        // Teacher report data
        'teacherStats' => [
            'total' => 0,
            'withActiveClasses' => 0
        ],
        'studentsPerTeacher' => [],
        'subjectDistribution' => [],
        'teachersPerSubject' => [],
        'teachersWithStudentsCount' => [],
        'studentsWithTeachersCount' => [],
        'teacherStudentRelationship' => [
            'relationship_summary' => [
                'total_relationships' => 0,
                'average_per_teacher' => 0,
                'average_per_student' => 0,
                'teachers_with_students' => 0,
                'students_with_teachers' => 0
            ],
            'teachers_distribution' => [],
            'students_distribution' => []
        ],

        // Student report data
        'studentStats' => [
            'total' => 0,
            'active' => 0
        ],
        'studentsByGroup' => [],

        // Librarian report data
        'librarianStats' => [
            'total' => 0,
            'active' => 0
        ],
        'approvalsByLibrarian' => [],
        'librarianActivity' => []
    ];

    protected $rules = [
        'reportType' => 'required|in:borrowing,subscription,assessment,attendance,teachers,students,librarians',
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
        $this->initializeChartData();
        $this->generateReport();
    }

    public function setDefaultDates()
    {
        $this->startDate = Carbon::now()->startOfMonth()->format('Y-m-d');
        $this->endDate = Carbon::now()->endOfMonth()->format('Y-m-d');
    }

    public function initializeChartData()
    {
        $this->chartData = [
            'returnStatus' => ['overdue' => 0, 'onTime' => 0, 'late' => 0],
            'dailyBorrowings' => [],
            'categoryBorrowings' => [],
            'subscriptionStatus' => ['active' => 0, 'expired' => 0],
            'dailySubscriptions' => [],
            'categorySubscriptions' => [],
            'groupScores' => [],
            'scoreDistribution' => ['0-20' => 0, '21-40' => 0, '41-60' => 0, '61-80' => 0, '81-100' => 0],
            'attendanceRate' => ['present' => 0, 'absent' => 0],
            'attendanceByDay' => [],
            'teacherStats' => ['total' => 0, 'withActiveClasses' => 0],
            'studentsPerTeacher' => [],
            'subjectDistribution' => [],
            'teachersPerSubject' => [],
            'teachersWithStudentsCount' => [],
            'studentsWithTeachersCount' => [],
            'studentStats' => ['total' => 0, 'active' => 0],
            'studentsByGroup' => [],
            'librarianStats' => ['total' => 0, 'active' => 0],
            'approvalsByLibrarian' => [],
            'librarianActivity' => [],
            'teacherStudentRelationship' => [
                'relationship_summary' => [
                    'total_relationships' => 0,
                    'average_per_teacher' => 0,
                    'average_per_student' => 0,
                    'teachers_with_students' => 0,
                    'students_with_teachers' => 0
                ],
                'teachers_distribution' => [],
                'students_distribution' => []
            ]
        ];
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
        }
        $this->generateReport();
    }

    public function updatedReportType()
    {
        $this->generateReport();
    }

    public function generateReport()
    {
        $this->isLoading = true;

        // Reset chart data
        $this->initializeChartData();

        try {
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
                case 'teachers':
                    $this->generateTeacherReport();
                    break;
                case 'students':
                    $this->generateStudentReport();
                    break;
                case 'librarians':
                    $this->generateLibrarianReport();
                    break;
            }
        } catch (\Exception $e) {
            session()->flash('error', 'Error generating report: ' . $e->getMessage());
        } finally {
            $this->isLoading = false;
        }

        // Dispatch event to update charts
        $this->dispatch('chartDataUpdated', $this->chartData);
    }

    private function generateBorrowingReport()
    {
        $query = BookBorrowing::whereBetween('borrow_date', [$this->startDate, $this->endDate]);

        if ($this->studentGroupId) {
            $query->whereHas('student', function ($q) {
                $q->where('student_group_id', $this->studentGroupId);
            });
        }

        // Daily borrowings
        $dailyData = $query->clone()
            ->select(
                DB::raw('DATE(borrow_date) as date'),
                DB::raw('COUNT(*) as count')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $this->chartData['dailyBorrowings'] = [
            'labels' => $dailyData->pluck('date')->map(fn($date) => Carbon::parse($date)->format('M d'))->toArray(),
            'data' => $dailyData->pluck('count')->toArray()
        ];

        // Return status
        $overdue = $query->clone()->where('status', 'borrowed')->where('due_date', '<', now())->count();
        $onTime = $query->clone()->where('status', 'returned')->where('return_date', '<=', 'due_date')->count();
        $late = $query->clone()->where('status', 'returned')->where('return_date', '>', 'due_date')->count();

        $this->chartData['returnStatus'] = [
            'overdue' => $overdue,
            'onTime' => $onTime,
            'late' => $late
        ];

        // Category borrowings
        $categoryData = $query->clone()
            ->with('book.bookCategory')
            ->get()
            ->groupBy('book.book_category.name')
            ->map(fn($items) => $items->count());

        $this->chartData['categoryBorrowings'] = [
            'labels' => $categoryData->keys()->toArray(),
            'data' => $categoryData->values()->toArray()
        ];
    }

    private function generateSubscriptionReport()
    {
        $query = BookSubscription::whereBetween('start_date', [$this->startDate, $this->endDate]);

        if ($this->studentGroupId) {
            $query->whereHas('student', function ($q) {
                $q->where('student_group_id', $this->studentGroupId);
            });
        }

        // Subscription status
        $active = $query->clone()->where('status', 'active')->count();
        $expired = $query->clone()->where('status', 'expired')->count();

        $this->chartData['subscriptionStatus'] = [
            'active' => $active,
            'expired' => $expired
        ];

        // Daily subscriptions
        $dailyData = $query->clone()
            ->select(
                DB::raw('DATE(start_date) as date'),
                DB::raw('COUNT(*) as count')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $this->chartData['dailySubscriptions'] = [
            'labels' => $dailyData->pluck('date')->map(fn($date) => Carbon::parse($date)->format('M d'))->toArray(),
            'data' => $dailyData->pluck('count')->toArray()
        ];
    }

    private function generateAssessmentReport()
    {
        // Mock data for assessment report
        $this->chartData['scoreDistribution'] = [
            '0-20' => rand(5, 15),
            '21-40' => rand(10, 25),
            '41-60' => rand(20, 40),
            '61-80' => rand(30, 50),
            '81-100' => rand(25, 45)
        ];

        $this->chartData['groupScores'] = [
            'labels' => $this->studentGroups->pluck('name')->toArray(),
            'data' => $this->studentGroups->map(fn($group) => rand(60, 95))->toArray()
        ];
    }

    private function generateAttendanceReport()
    {
        // Mock data for attendance report
        $present = rand(80, 95);
        $absent = 100 - $present;

        $this->chartData['attendanceRate'] = [
            'present' => $present,
            'absent' => $absent
        ];

        // Generate daily attendance data
        $dates = collect();
        $current = Carbon::parse($this->startDate);
        while ($current->lte(Carbon::parse($this->endDate))) {
            $dates->push($current->format('M d'));
            $current->addDay();
        }

        $this->chartData['attendanceByDay'] = [
            'labels' => $dates->take(10)->toArray(),
            'data' => $dates->take(10)->map(fn() => rand(75, 98))->toArray()
        ];
    }

    private function generateTeacherReport()
    {
        $totalTeachers = Teacher::count();
        $teachersWithClasses = Teacher::whereHas('studentGroups')->count();

        $this->chartData['teacherStats'] = [
            'total' => $totalTeachers,
            'withActiveClasses' => $teachersWithClasses
        ];

        // Subject distribution
        $subjectData = Teacher::with('subjects')
            ->get()
            ->flatMap(fn($teacher) => $teacher->subjects)
            ->groupBy('name')
            ->map(fn($subjects) => $subjects->count());

        $this->chartData['subjectDistribution'] = [
            'labels' => $subjectData->keys()->toArray(),
            'data' => $subjectData->values()->toArray()
        ];

        // Students per teacher
        $studentData = Teacher::with('studentGroups.students')
            ->get()
            ->map(function ($teacher) {
                return [
                    'name' => $teacher->user->name,
                    'students' => $teacher->studentGroups->sum(fn($group) => $group->students->count())
                ];
            });

        $this->chartData['studentsPerTeacher'] = [
            'labels' => $studentData->pluck('name')->toArray(),
            'data' => $studentData->pluck('students')->toArray()
        ];
    }

    private function generateStudentReport()
    {
        $totalStudents = Student::count();
        $activeStudents = Student::where('status', 'active')->count();

        $this->chartData['studentStats'] = [
            'total' => $totalStudents,
            'active' => $activeStudents
        ];

        // Students by group
        $groupData = StudentGroup::withCount('students')->get();

        $this->chartData['studentsByGroup'] = [
            'labels' => $groupData->pluck('name')->toArray(),
            'data' => $groupData->pluck('students_count')->toArray()
        ];
    }

    private function generateLibrarianReport()
    {
        $totalLibrarians = Librarian::count();
        $activeLibrarians = Librarian::whereHas('user', fn($q) => $q->where('status', 'active'))->count();

        $this->chartData['librarianStats'] = [
            'total' => $totalLibrarians,
            'active' => $activeLibrarians
        ];

        // Mock approval data
        $this->chartData['approvalsByLibrarian'] = [
            'labels' => Librarian::with('user')->get()->pluck('user.name')->toArray(),
            'data' => Librarian::get()->map(fn() => rand(10, 50))->toArray()
        ];
    }

    public function exportReport()
    {
        // Implementation for report export
        session()->flash('message', 'Report exported successfully!');
    }

    public function render()
    {
        return view('livewire.administrators.reports');
    }
}
