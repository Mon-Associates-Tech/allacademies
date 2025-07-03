<?php

namespace App\Livewire\Teachers;

use App\Models\Teacher;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class Subjects extends Component
{
    use WithPagination;

    public $searchTerm = '';
    public $selectedSubject = null;
    public $showSubjectModal = false;
    public $teacher;

    public function mount()
    {
        $this->teacher = Auth::user()->teacher;

        if (!$this->teacher) {
            abort(403, 'Access denied. Teacher profile not found.');
        }
    }

    public function updatedSearchTerm()
    {
        $this->resetPage();
    }

    public function showSubjectDetails($subjectId)
    {
        // Use the correct relationship method
        $this->selectedSubject = $this->teacher->academicSubjects()
            ->with([
                'academicLevel.academicGroup',
                'lessons',
                'academicTopics',
                'quizzes',
                'examinations'
            ])
            ->findOrFail($subjectId);

        $this->showSubjectModal = true;
    }

    public function closeSubjectModal()
    {
        $this->showSubjectModal = false;
        $this->selectedSubject = null;
    }

    public function render()
    {
        // Use academicSubjects() instead of subjects() since that's the working relationship
        $query = $this->teacher->academicSubjects()
            ->with([
                'academicLevel.academicGroup'
            ]);

        if ($this->searchTerm) {
            $query->where(function($q) {
                $q->where('name', 'like', '%' . $this->searchTerm . '%')
                    ->orWhere('code', 'like', '%' . $this->searchTerm . '%')
                    ->orWhereHas('academicLevel', function($levelQuery) {
                        $levelQuery->where('name', 'like', '%' . $this->searchTerm . '%');
                    })
                    ->orWhereHas('academicLevel.academicGroup', function($groupQuery) {
                        $groupQuery->where('name', 'like', '%' . $this->searchTerm . '%');
                    });
            });
        }

        $subjects = $query->orderBy('name')->paginate(12);

        // Get teacher's academic groups and levels for overview
        $academicGroups = $this->teacher->academicGroups()->with('academicLevels')->get();
        $academicLevels = $this->teacher->academicLevels()->with('academicGroup')->get();

        return view('livewire.teachers.subjects', [
            'subjects' => $subjects,
            'academicGroups' => $academicGroups,
            'academicLevels' => $academicLevels,
            'teacherName' => $this->teacher->user->name
        ]);
    }
}
