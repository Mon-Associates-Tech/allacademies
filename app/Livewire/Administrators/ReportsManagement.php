<?php

namespace App\Livewire\Administrators;

use Illuminate\Support\Collection;
use Livewire\Component;
use App\Models\Student;
use App\Models\Book;
use App\Models\BookBorrowing;
use App\Models\BookSubscription;
use App\Models\StudentGroup;
use App\Models\Teacher;
use App\Models\Librarian;
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
        
        // Initialize all chart data arrays
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

    public function updatedReportType($value)
    {
        $this->generateReport();
    }

    public function generateReport()
    {
        // Reset all chart data except for the current report type
        $this->chartData = array_merge($this->chartData, [
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
            'teacherStudentRelationship' => [
                'relationship_summary' => $this->chartData['teacherStudentRelationship']['relationship_summary'],
                'teachers_distribution' => [],
                'students_distribution' => []
            ]
        ]);

        // Generate the appropriate report based on the selected type
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
            case 'teacher':
                $this->generateTeacherReport();
                break;
            case 'student':
                $this->generateStudentReport();
                break;
            case 'librarian':
                $this->generateLibrarianReport();
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

        $this->chartData = array_merge($this->chartData, [
            'dailyBorrowings' => $dailyBorrowings,
            'categoryBorrowings' => $categoryBorrowings,
            'returnStatus' => [
                'overdue' => $overdueStatus,
                'onTime' => $onTimeReturns,
                'late' => $lateReturns
            ]
        ]);
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

        $this->chartData = array_merge($this->chartData, [
            'subscriptionStatus' => [
                'active' => $activeSubscriptions,
                'expired' => $expiredSubscriptions
            ],
            'dailySubscriptions' => $dailySubscriptions,
            'categorySubscriptions' => $categorySubscriptions
        ]);
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

        $this->chartData = array_merge($this->chartData, [
            'groupScores' => $groupScores,
            'scoreDistribution' => $scoreDistribution
        ]);
    }

    private function generateAttendanceReport()
    {
        // In a real application, you would have an attendance model
        // This is just a placeholder for demonstration

        // Mock data for attendance rate
        $presentPercentage = rand(75, 95);
        $absentPercentage = 100 - $presentPercentage;

        // Mock data for daily attendance by day of week
        $attendanceByDay = [
            ['day' => 'Monday', 'rate' => rand(75, 100)],
            ['day' => 'Tuesday', 'rate' => rand(75, 100)],
            ['day' => 'Wednesday', 'rate' => rand(75, 100)],
            ['day' => 'Thursday', 'rate' => rand(75, 100)],
            ['day' => 'Friday', 'rate' => rand(75, 100)]
        ];

        $this->chartData = array_merge($this->chartData, [
            'attendanceRate' => [
                'present' => $presentPercentage,
                'absent' => $absentPercentage
            ],
            'attendanceByDay' => $attendanceByDay
        ]);
    }

    // New methods for generating reports for Teachers, Students, and Librarians

    /**
     * Prepare chart data for teacher subjects
     * @param array $teachersPerSubjectGrouped
     * @return array
     */
    private function prepareTeacherSubjectData($teachersPerSubjectGrouped)
    {
        return [
            'distribution' => $teachersPerSubjectGrouped->map(function ($items) {
                return collect($items)->unique('teacher_id')->count();
            }),
            'details' => $teachersPerSubjectGrouped->map(function ($items) {
                return collect($items)->unique('teacher_id')->values();
            })
        ];
    }

    /**
     * Prepare chart data for teacher-student relationship
     * @param Collection $teachersWithStudents
     * @param Collection $studentsWithTeachers
     * @return array
     */
    private function prepareTeacherStudentRelationshipData($teachersWithStudents, $studentsWithTeachers)
    {
        // Prepare teachers with students data
        $teachersData = $teachersWithStudents->mapWithKeys(function ($teacher) {
            return [$teacher->user->name => $teacher->assigned_students_count];
        });

        // Prepare students with teachers data
        $studentsData = $studentsWithTeachers->mapWithKeys(function ($student) {
            return [$student->user->name => $student->teachers_count];
        });

        return [
            'teachers_distribution' => $teachersData,
            'students_distribution' => $studentsData,
            'relationship_summary' => [
                'total_relationships' => $teachersData->values()->sum(),
                'average_per_teacher' => $teachersData->isNotEmpty() ? round($teachersData->avg(), 2) : 0,
                'average_per_student' => $studentsData->isNotEmpty() ? round($studentsData->avg(), 2) : 0,
                'teachers_with_students' => $teachersData->filter(function ($count) {
                    return $count > 0;
                })->count(),
                'students_with_teachers' => $studentsData->filter(function ($count) {
                    return $count > 0;
                })->count()
            ]
        ];
    }

    private function generateTeacherReport()
    {
        // Get teacher statistics
        $totalTeachers = Teacher::count();
        $teachersWithActiveClasses = Teacher::has('studentGroups')->count();

        // Get students per teacher
        $studentsPerTeacher = Teacher::withCount('students')
            ->get();

        // Get subject distribution - now we'll use a more reliable approach
        $teachersPerSubjectGrouped = Teacher::with('subjects')
            ->get()
            ->flatMap(function ($teacher) {
                return $teacher->subjects->map(function ($subject) use ($teacher) {
                    return [
                        'subject' => $subject->name,
                        'teacher_id' => $teacher->id,
                        'teacher_name' => $teacher->user->name
                    ];
                });
            })
            ->groupBy('subject');

        // Get prepared teacher subject data
        $teacherSubjectData = $this->prepareTeacherSubjectData($teachersPerSubjectGrouped);

        // Get teacher-student relationship statistics
        $teachersWithStudentsCount = Teacher::withCount('assignedStudents')->get();
        $studentsWithTeachersCount = Student::withCount('teachers')->get();

        // Prepare teacher-student relationship data
        $teacherStudentRelationshipData = $this->prepareTeacherStudentRelationshipData($teachersWithStudentsCount, $studentsWithTeachersCount);

        $this->chartData = array_merge($this->chartData, [
            'teacherStats' => [
                'total' => $totalTeachers,
                'withActiveClasses' => $teachersWithActiveClasses
            ],
            'studentsPerTeacher' => $studentsPerTeacher,
            'subjectDistribution' => $teacherSubjectData['distribution'],
            'teachersPerSubject' => $teacherSubjectData['details'],
            'teachersWithStudentsCount' => $teachersWithStudentsCount,
            'studentsWithTeachersCount' => $studentsWithTeachersCount,
            'teacherStudentRelationship' => $teacherStudentRelationshipData
        ]);
    }

    private function generateStudentReport()
    {
        // Get student statistics
        $totalStudents = Student::count();
        $activeStudents = Student::whereHas('enrollments', function ($q) {
            $q->where('status', 'active');
        })->count();

        // Get students by group
        $studentsByGroup = StudentGroup::withCount('students')
            ->get();

        // Get top performing students
        $topPerformingStudents = Student::with(['user', 'enrollments'])
            ->withAvg('enrollments', 'grade')
            ->orderByDesc('enrollments_avg_grade')
            ->take(3)
            ->get();

        $this->chartData['studentStats'] = [
            'total' => $totalStudents,
            'active' => $activeStudents
        ];

        $this->chartData['studentsByGroup'] = $studentsByGroup;
        $this->chartData['topPerformingStudents'] = $topPerformingStudents;
    }

    private function generateLibrarianReport()
    {
        // Get librarian statistics
        $totalLibrarians = Librarian::count();
        $activeLibrarians = Librarian::whereHas('bookApprovals', function ($q) {
            $q->where('status', 'approved');
        })->count();

        // Get book approvals by librarian
        $approvalsByLibrarian = Librarian::withCount('bookApprovals')
            ->get();

        // Get librarian activity
        $librarianActivity = Librarian::with(['user', 'bookApprovals'])
            ->withCount('bookApprovals')
            ->orderByDesc('book_approvals_count')
            ->take(3)
            ->get();

        $this->chartData['librarianStats'] = [
            'total' => $totalLibrarians,
            'active' => $activeLibrarians
        ];

        $this->chartData['approvalsByLibrarian'] = $approvalsByLibrarian;
        $this->chartData['librarianActivity'] = $librarianActivity;
    }

    public function render()
    {
        return view('livewire.administrators.reports');
    }
}
