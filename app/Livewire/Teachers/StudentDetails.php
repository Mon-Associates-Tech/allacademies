<?php

namespace App\Livewire\Teachers;

use App\Models\Student;
use App\Models\Teacher;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class StudentDetails extends Component
{
    public $student;

    public $teacher;

    public $academicHistory;

    public $assignments;

    public $activities;

    public $assignmentSubmissions;

    public $submissionFilter;

    public $historyFilter = '';

    public function mount(Student $student)
    {

        $this->teacher = Teacher::withoutGlobalScopes()->where('user_id', Auth::id())->first();
        // Check if the teacher has access to this student
        $hasAccess = $this->teacher->hasAccessToStudent($student);

        if (! $hasAccess) {
            return redirect()->route('teachers.students.index')
                ->with('error', 'You do not have access to view this student.');
        }

        $this->student = $student->load(['user', 'academicLevel.academicGroup']);

        $this->loadStudentData();
    }

    public function getTypeColor($type)
    {
        return match ($type) {
            'level_change' => 'bg-blue-500',
            'assessment' => 'bg-purple-500',
            'achievement' => 'bg-green-500',
            'attendance' => 'bg-yellow-500',
            'behavior' => 'bg-red-500',
            'award' => 'bg-indigo-500',
            'certification' => 'bg-pink-500',
            'milestone' => 'bg-teal-500',
            default => 'bg-gray-500',
        };
    }

    public function getTypeBadgeColor($type)
    {
        return match ($type) {
            'level_change' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200',
            'assessment' => 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200',
            'achievement' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
            'attendance' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
            'behavior' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200',
            'award' => 'bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-200',
            'certification' => 'bg-pink-100 text-pink-800 dark:bg-pink-900 dark:text-pink-200',
            'milestone' => 'bg-teal-100 text-teal-800 dark:bg-teal-900 dark:text-teal-200',
            default => 'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200',
        };
    }

    public function updatedHistoryFilter()
    {
        $this->loadStudentData();
    }

    private function loadStudentData()
    {
        $query = $this->student->academicHistory()
            ->orderBy('recorded_date', 'desc');

        if ($this->historyFilter) {
            $query->where('type', $this->historyFilter);
        }

        $this->academicHistory = $query->get();

        // Load assignment submissions with filtering
        $submissionsQuery = $this->student->assignmentSubmissions()
            ->with(['assignment'])
            ->orderBy('created_at', 'desc');

        if ($this->submissionFilter) {
            $submissionsQuery->where('status', $this->submissionFilter);
        }

        $this->assignmentSubmissions = $submissionsQuery->get();

        // Load recent activities
        $this->activities = $this->student->user->loginActivities()
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();
    }

    public function render()
    {
        return view('livewire.teachers.students.details');
    }
}
