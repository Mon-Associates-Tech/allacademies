<?php

namespace App\Livewire\Parent;

use App\Livewire\AppComponent;
use App\Models\AcademicFeeStructure;
use App\Models\AcademicPeriod;
use App\Models\Assignment;
use App\Models\AssignmentSubmission;
use App\Models\Attendance\AttendanceRecord;
use App\Models\BookBorrowing;
use App\Models\BookSubscription;
use App\Models\SchoolFee;
use App\Models\SchoolPayment;
use App\Models\Student;
use App\Models\StudentParent;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;

class Dashboard extends AppComponent
{
    public $selectedWardId = null;

    public $searchTerm = '';

    public $sortBy = 'name';

    public $sortDirection = 'asc';

    public $without_scope = true;

    public function mount($without_scope = true)
    {
        $wards = $this->wards;
        $this->without_scope = $without_scope;
        if ($wards->isNotEmpty()) {
            $this->selectedWardId = $wards->first()->id;
        }
    }

    public function selectWard($wardId)
    {
        $this->selectedWardId = $wardId;
    }

    #[Computed]
    public function wards()
    {
        $students = StudentParent::withoutGlobalScopes()
            ->where('user_id', Auth::id())
            ->with([
                'students' => function ($query) {
                    $query->withoutGlobalScopes();
                },
                'students.user',
                'students.academicLevel.academicGroup',
                'students.studentGroup',
            ])
            ->get()
            ->flatMap(function ($parent) {
                return $parent->students;
            })
            ->unique('id');

        if ($this->searchTerm) {
            $students = $students->filter(function ($student) {
                return stripos($student->user->name, $this->searchTerm) !== false ||
                    stripos($student->academicLevel->name ?? '', $this->searchTerm) !== false ||
                    stripos($student->academicLevel->academicGroup->name ?? '', $this->searchTerm) !== false;
            });
        }

        return $students->sortBy($this->sortBy === 'name' ? 'user.name' : $this->sortBy,
            SORT_REGULAR, $this->sortDirection === 'desc');
    }

    #[Computed]
    public function selectedWard()
    {
        if (! $this->selectedWardId) {
            return null;
        }

        $student = Student::query();
        if ($this->without_scope) {
            $student = $student->withoutGlobalScopes();
        }

        return $student->with([
            'academicLevel.academicGroup',
            'academicGroup',
            'studentGroup',
            'user',
        ])->find($this->selectedWardId);
    }

    #[Computed]
    public function academicOverview()
    {
        if (! $this->selectedWard) {
            return $this->getEmptyAcademicOverview();
        }

        // Get assignment submissions
        $submissions = AssignmentSubmission::where('student_id', $this->selectedWardId)
            ->whereIn('status', ['submitted', 'graded'])
            ->get();

        // Get pending assignments
        $pendingAssignments = Assignment::where('status', 'published')
            ->where('ends_at', '>=', now())
            ->where(function ($query) {
                $query->whereHas('students', function ($q) {
                    $q->where('students.id', $this->selectedWardId);
                })
                    ->orWhereHas('academicLevels', function ($q) {
                        $q->where('academic_levels.id', $this->selectedWard->academic_level_id);
                    })
                    ->orWhereHas('academicGroups', function ($q) {
                        if ($this->selectedWard->academicLevel && $this->selectedWard->academicLevel->academic_group_id) {
                            $q->where('academic_groups.id', $this->selectedWard->academicLevel->academic_group_id);
                        }
                    });
            })
            ->whereNotIn('id', $submissions->pluck('assignment_id'))
            ->count();

        // Calculate scores
        $submissionsWithScores = $submissions->filter(fn ($s) => $s->total_marks > 0);
        $scores = $submissionsWithScores->map(fn ($s) => ($s->score / $s->total_marks) * 100);

        return [
            'total_submissions' => $submissions->count(),
            'pending_assignments' => $pendingAssignments,
            'average_score' => $scores->avg() ?? 0,
            'graded_count' => $submissions->where('status', 'graded')->count(),
            'recent_activity' => $submissions->sortByDesc('submitted_at')->take(3),
        ];
    }

    #[Computed]
    public function attendanceOverview()
    {
        if (! $this->selectedWard) {
            return $this->getEmptyAttendanceOverview();
        }

        $currentMonth = now()->startOfMonth();
        $endOfMonth = now()->endOfMonth();

        $attendanceRecords = AttendanceRecord::where('attendance_records.student_id', $this->selectedWardId)
            ->join('attendances', 'attendance_records.attendance_id', '=', 'attendances.id')
            ->whereBetween('attendances.date', [$currentMonth, $endOfMonth])
            ->select('attendance_records.*', 'attendances.date')
            ->get();

        $totalDays = $attendanceRecords->count();
        $presentDays = $attendanceRecords->where('status', 'present')->count();
        $absentDays = $attendanceRecords->where('status', 'absent')->count();
        $lateDays = $attendanceRecords->where('status', 'late')->count();

        $attendanceRate = $totalDays > 0 ? ($presentDays / $totalDays) * 100 : 100;

        return [
            'attendance_rate' => round($attendanceRate, 1),
            'present_days' => $presentDays,
            'absent_days' => $absentDays,
            'late_days' => $lateDays,
            'total_days' => $totalDays,
            'status' => $attendanceRate >= 90 ? 'excellent' : ($attendanceRate >= 75 ? 'good' : 'needs_attention'),
        ];
    }

    #[Computed]
    public function feeOverview()
    {
        if (! $this->selectedWard) {
            return $this->getEmptyFeeOverview();
        }

        $schoolId = getSchoolId();
        $currentTerm = AcademicPeriod::where('school_id', $schoolId)
            ->where('is_current', 1)
            ->orWhere('status', 'active')
            ->first();

        // Get fee structure
        $feeStructure = AcademicFeeStructure::where('school_id', $schoolId)
            ->where('academic_group_id', $this->selectedWard->academic_group_id)
            ->where('academic_level_id', $this->selectedWard->academic_level_id)
            ->where('current_term_id', $currentTerm->id ?? null)
            ->first();

        // Calculate paid amount
        $totalPaid = SchoolFee::where('student_id', $this->selectedWardId)
            ->where('term_id', $currentTerm->id ?? null)
            ->where('status', 'succeeded')
            ->sum('amount');

        $otherPayments = SchoolPayment::where('student_id', $this->selectedWardId)
            ->where('academic_period_id', $currentTerm->id ?? null)
            ->where('status', 'succeeded')
            ->sum('amount');

        $totalDue = $feeStructure->term_total_amount ?? $feeStructure->amount ?? 0;
        $balance = max($totalDue - $totalPaid, 0);
        $paymentPercentage = $totalDue > 0 ? ($totalPaid / $totalDue) * 100 : 0;

        return [
            'total_due' => $totalDue,
            'total_paid' => $totalPaid,
            'other_payments' => $otherPayments,
            'balance' => $balance,
            'payment_percentage' => round($paymentPercentage, 1),
            'status' => $balance == 0 ? 'paid' : ($paymentPercentage >= 50 ? 'partial' : 'outstanding'),
            'currency' => 'GHS',
        ];
    }

    #[Computed]
    public function libraryActivity()
    {
        if (! $this->selectedWard) {
            return $this->getEmptyLibraryActivity();
        }

        $user = $this->selectedWard->user;

        // Active subscriptions
        $activeSubscriptions = BookSubscription::where('user_id', $user->id)
            ->where('status', 'active')
            ->count();

        // Current borrowings
        $currentBorrowings = BookBorrowing::where('user_id', $user->id)
            ->whereNull('return_date')
            ->with('book')
            ->get();

        // Overdue books
        $overdueBooks = $currentBorrowings->filter(function ($borrowing) {
            return $borrowing->due_date && $borrowing->due_date < now();
        });

        // Books borrowed this month
        $booksThisMonth = BookBorrowing::where('user_id', $user->id)
            ->where('borrow_date', '>=', now()->startOfMonth())
            ->count();

        return [
            'active_subscriptions' => $activeSubscriptions,
            'books_borrowed' => $currentBorrowings->count(),
            'overdue_books' => $overdueBooks->count(),
            'books_this_month' => $booksThisMonth,
            'current_borrowings' => $currentBorrowings->take(3),
            'has_overdues' => $overdueBooks->count() > 0,
        ];
    }

    #[Computed]
    public function recentActivity()
    {
        if (! $this->selectedWard) {
            return collect();
        }

        $activities = collect();

        // Recent submissions (last 3)
        $submissions = AssignmentSubmission::where('student_id', $this->selectedWardId)
            ->whereIn('status', ['submitted', 'graded'])
            ->with('assignment.academicSubject')
            ->latest('submitted_at')
            ->take(3)
            ->get()
            ->map(function ($submission) {
                return [
                    'type' => 'assignment',
                    'icon' => 'document',
                    'color' => 'blue',
                    'title' => 'Submitted '.$submission->assignment->title,
                    'description' => $submission->assignment->academicSubject->name ?? 'Assignment',
                    'date' => $submission->submitted_at,
                    'meta' => $submission->status === 'graded' && $submission->total_marks > 0
                        ? number_format(($submission->score / $submission->total_marks) * 100, 1).'%'
                        : ucfirst($submission->status),
                ];
            });

        // Recent attendance (last 3 days) - Fixed to join with attendances table
        $attendance = AttendanceRecord::where('attendance_records.student_id', $this->selectedWardId)
            ->join('attendances', 'attendance_records.attendance_id', '=', 'attendances.id')
            ->select('attendance_records.*', 'attendances.date', 'attendances.session')
            ->orderBy('attendances.date', 'desc')
            ->take(3)
            ->get()
            ->map(function ($record) {
                return [
                    'type' => 'attendance',
                    'icon' => 'check',
                    'color' => $record->status === 'present' ? 'green' : ($record->status === 'absent' ? 'red' : 'yellow'),
                    'title' => ucfirst($record->status),
                    'description' => 'Attendance recorded',
                    'date' => \Carbon\Carbon::parse($record->date),
                    'meta' => $record->status,
                ];
            });

        // Recent library activity
        $libraryActivity = BookBorrowing::where('user_id', $this->selectedWard->user_id)
            ->with('book')
            ->latest('borrow_date')
            ->take(2)
            ->get()
            ->map(function ($borrowing) {
                return [
                    'type' => 'library',
                    'icon' => 'book',
                    'color' => 'purple',
                    'title' => 'Borrowed: '.($borrowing->book->title ?? 'Book'),
                    'description' => 'Library activity',
                    'date' => $borrowing->borrow_date,
                    'meta' => $borrowing->return_date ? 'Returned' : 'Active',
                ];
            });

        return $activities
            ->merge($submissions)
            ->merge($attendance)
            ->merge($libraryActivity)
            ->sortByDesc('date')
            ->take(8)
            ->values();
    }

    #[Computed]
    public function upcomingEvents()
    {
        if (! $this->selectedWard) {
            return collect();
        }

        $events = collect();

        // Upcoming assignments (due within 7 days)
        $upcomingAssignments = Assignment::with('academicSubject')
            ->where('status', 'published')
            ->whereBetween('ends_at', [now(), now()->addDays(7)])
            ->where(function ($query) {
                $query->whereHas('students', function ($q) {
                    $q->where('students.id', $this->selectedWardId);
                })
                    ->orWhereHas('academicLevels', function ($q) {
                        $q->where('academic_levels.id', $this->selectedWard->academic_level_id);
                    })
                    ->orWhereHas('academicGroups', function ($q) {
                        if ($this->selectedWard->academicLevel && $this->selectedWard->academicLevel->academic_group_id) {
                            $q->where('academic_groups.id', $this->selectedWard->academicLevel->academic_group_id);
                        }
                    });
            })
            ->orderBy('ends_at')
            ->take(5)
            ->get()
            ->map(function ($assignment) {
                return [
                    'type' => 'assignment',
                    'title' => $assignment->title,
                    'description' => $assignment->academicSubject->name ?? 'Assignment',
                    'date' => $assignment->ends_at,
                    'urgency' => $assignment->ends_at->diffInDays(now()) <= 2 ? 'high' : 'normal',
                    'icon' => 'clipboard',
                    'color' => 'blue',
                ];
            });

        // Overdue books
        $overdueBooks = BookBorrowing::where('user_id', $this->selectedWard->user_id)
            ->whereNull('return_date')
            ->where('due_date', '<', now())
            ->with('book')
            ->get()
            ->map(function ($borrowing) {
                return [
                    'type' => 'library',
                    'title' => 'Return: '.($borrowing->book->title ?? 'Book'),
                    'description' => 'Overdue book',
                    'date' => $borrowing->due_date,
                    'urgency' => 'high',
                    'icon' => 'exclamation',
                    'color' => 'red',
                ];
            });

        return $events
            ->merge($upcomingAssignments)
            ->merge($overdueBooks)
            ->sortBy('date')
            ->take(5)
            ->values();
    }

    #[Computed]
    public function insights()
    {
        if (! $this->selectedWard) {
            return collect();
        }

        $insights = collect();

        // Academic insights
        $academic = $this->academicOverview;
        if ($academic['average_score'] >= 80) {
            $insights->push([
                'type' => 'success',
                'title' => 'Excellent Academic Performance',
                'message' => 'Your ward is performing excellently with an average of '.number_format($academic['average_score'], 1).'%',
                'icon' => 'trophy',
            ]);
        } elseif ($academic['average_score'] < 60 && $academic['total_submissions'] > 0) {
            $insights->push([
                'type' => 'warning',
                'title' => 'Academic Support Needed',
                'message' => 'Consider additional support to improve performance',
                'icon' => 'alert',
            ]);
        }

        // Attendance insights
        $attendance = $this->attendanceOverview;
        if ($attendance['attendance_rate'] < 80) {
            $insights->push([
                'type' => 'warning',
                'title' => 'Attendance Concern',
                'message' => 'Attendance is at '.$attendance['attendance_rate'].'%. Regular attendance is important for success.',
                'icon' => 'calendar',
            ]);
        }

        // Fee insights
        $fees = $this->feeOverview;
        if ($fees['balance'] > 0) {
            $insights->push([
                'type' => 'info',
                'title' => 'Fee Balance',
                'message' => 'Outstanding balance of GHS '.number_format($fees['balance'], 2),
                'icon' => 'credit-card',
            ]);
        }

        // Library insights
        $library = $this->libraryActivity;
        if ($library['has_overdues']) {
            $insights->push([
                'type' => 'warning',
                'title' => 'Overdue Books',
                'message' => $library['overdue_books'].' book(s) overdue. Please return them soon.',
                'icon' => 'book',
            ]);
        }

        return $insights->take(3);
    }

    private function getEmptyAcademicOverview()
    {
        return [
            'total_submissions' => 0,
            'pending_assignments' => 0,
            'average_score' => 0,
            'graded_count' => 0,
            'recent_activity' => collect(),
        ];
    }

    private function getEmptyAttendanceOverview()
    {
        return [
            'attendance_rate' => 100,
            'present_days' => 0,
            'absent_days' => 0,
            'late_days' => 0,
            'total_days' => 0,
            'status' => 'no_data',
        ];
    }

    private function getEmptyFeeOverview()
    {
        return [
            'total_due' => 0,
            'total_paid' => 0,
            'other_payments' => 0,
            'balance' => 0,
            'payment_percentage' => 0,
            'status' => 'no_data',
            'currency' => 'GHS',
        ];
    }

    private function getEmptyLibraryActivity()
    {
        return [
            'active_subscriptions' => 0,
            'books_borrowed' => 0,
            'overdue_books' => 0,
            'books_this_month' => 0,
            'current_borrowings' => collect(),
            'has_overdues' => false,
        ];
    }

    public function render()
    {
        return view('livewire.parent.dashboard');
    }
}
