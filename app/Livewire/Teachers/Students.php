<?php

namespace App\Livewire\Teachers;

use App\Models\Teacher;
use App\Models\Student;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class Students extends Component
{
    use WithPagination;


    public $teacher;
    public $search = '';
    public $selectedGroup = '';
    public $selectedLevel = '';
    public $selectedSource = '';
    public $perPage = 10;
    public $sortBy = 'name';
    public $sortDirection = 'asc';
    public $showDetails = [];
    public $viewMode = 'table'; // 'table' or 'grid'

    public function mount()
    {
        $this->teacher = Teacher::where('user_id', Auth::id())->first();
    }

    public function getStudentsQuery()
    {


        return $this->teacher?->getStudentsWithDetails();
        $query = collect();

        // Get students from direct assignments
        $directStudents = $this->teacher->assignedStudents()
            ->with(['user', 'academicLevel', 'academicLevel.academicGroup'])
            ->get()
            ->map(function ($student) {
                $student->source = $student->pivot->is_primary ? 'Primary Assignment' : 'Secondary Assignment';
                return $student;
            });

        // Get students from academic groups
        $groupStudents = collect();
        foreach ($this->teacher->academicGroups as $group) {
            $students = Student::whereHas('academicLevel', function ($q) use ($group) {
                $q->where('academic_group_id', $group->id);
            })
            ->with(['user', 'academicLevel', 'academicLevel.academicGroup'])
            ->get()
            ->map(function ($student) use ($group) {
                $student->source = 'Academic Group: ' . $group->name;
                return $student;
            });
            $groupStudents = $groupStudents->merge($students);
        }

        // Get students from academic levels
        $levelStudents = collect();
        foreach ($this->teacher->academicLevels as $level) {
            $students = Student::where('academic_level_id', $level->id)
                ->with(['user', 'academicLevel', 'academicLevel.academicGroup'])
                ->get()
                ->map(function ($student) use ($level) {
                    $student->source = 'Academic Level: ' . $level->name;
                    return $student;
                });
            $levelStudents = $levelStudents->merge($students);
        }

        // Get students from student groups
        $studentGroupStudents = collect();
        foreach ($this->teacher->studentGroups as $studentGroup) {
            $students = $studentGroup->students()
                ->with(['user', 'academicLevel', 'academicLevel.academicGroup'])
                ->get()
                ->map(function ($student) use ($studentGroup) {
                    $student->source = 'Student Group: ' . $studentGroup->name;
                    return $student;
                });
            $studentGroupStudents = $studentGroupStudents->merge($students);
        }

        // Merge all students and remove duplicates
        $allStudents = $directStudents
            ->merge($groupStudents)
            ->merge($levelStudents)
            ->merge($studentGroupStudents)
            ->unique('id');

        // Apply filters
        if ($this->search) {
            $allStudents = $allStudents->filter(function ($student) {
                return stripos($student->user->name, $this->search) !== false ||
                       stripos($student->user->email, $this->search) !== false;
            });
        }

        if ($this->selectedGroup) {
            $allStudents = $allStudents->filter(function ($student) {
                return $student->academicLevel &&
                       $student->academicLevel->academicGroup &&
                       $student->academicLevel->academicGroup->id == $this->selectedGroup;
            });
        }

        if ($this->selectedLevel) {
            $allStudents = $allStudents->filter(function ($student) {
                return $student->academic_level_id == $this->selectedLevel;
            });
        }

        if ($this->selectedSource) {
            $allStudents = $allStudents->filter(function ($student) {
                return stripos($student->source, $this->selectedSource) !== false;
            });
        }

        // Apply sorting
        $allStudents = $allStudents->sortBy(function ($student) {
            switch ($this->sortBy) {
                case 'name':
                    return $student->user->name;
                case 'email':
                    return $student->user->email;
                case 'level':
                    return $student->academicLevel->name ?? '';
                case 'group':
                    return $student->academicLevel->academicGroup->name ?? '';
                default:
                    return $student->user->name;
            }
        }, SORT_REGULAR, $this->sortDirection === 'desc');

        return $allStudents;
    }

    public function sortBy($field)
    {
        if ($this->sortBy === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function toggleDetails($studentId)
    {
        if (in_array($studentId, $this->showDetails)) {
            $this->showDetails = array_diff($this->showDetails, [$studentId]);
        } else {
            $this->showDetails[] = $studentId;
        }
    }

    public function toggleViewMode()
    {
        $this->viewMode = $this->viewMode === 'table' ? 'grid' : 'table';
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function viewStudentDetails($studentId)
    {
        return redirect()->route('teachers.student.details', ['student' => $studentId]);
    }

    public function render()
    {
        $students = $this->getStudentsQuery() ?? collect();
        $totalStudents = $students?->count();

        // Manual pagination
        $currentPage = $this->getPage();
        $offset = ($currentPage - 1) * $this->perPage;
        $studentsForPage = $students?->slice($offset, $this->perPage)->values();

        $groups = $this->teacher?->academicGroups ?? [];
        $levels = $this->teacher?->academicLevels ?? [];
        $sources = $students?->pluck('source')->unique()->sort()->values() ?? [];

        return view('livewire.teachers.students', [
            'students' => $studentsForPage,
            'totalStudents' => $totalStudents,
            'groups' => $groups,
            'levels' => $levels,
            'sources' => $sources,
            'hasNextPage' => $offset + $this->perPage < $totalStudents,
            'hasPreviousPage' => $currentPage > 1,
            'totalPages' => ceil($totalStudents / $this->perPage),
            'currentPage' => $currentPage,
        ]);
    }
}
