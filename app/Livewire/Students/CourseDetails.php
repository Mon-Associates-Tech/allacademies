<?php

namespace App\Livewire\Students;

use App\Models\AcademicSubject;
use App\Models\Student;
use Livewire\Component;

class CourseDetails extends Component
{
    public AcademicSubject $course;

    public $activeTab = 'overview';

    public function mount($courseId)
    {
        $student = auth()->user()->student;

        if (! $student) {
            abort(403, 'Student record not found.');
        }

        // Get the course with all related data
        $this->course = AcademicSubject::with([
            'academicLevel.academicGroup',
            'academicTopics' => function ($query) {
                $query->with(['subtopics' => function ($subQuery) {
                    $subQuery->orderBy('name');
                }])->orderBy('name');
            },
            'lessons' => function ($query) {
                $query->orderBy('title');
            },
            'quizzes' => function ($query) {
                $query->orderBy('title');
            },
            'examinations' => function ($query) {
                $query->orderBy('title');
            },
            'teachers',
        ])->findOrFail($courseId);

        // Verify student has access to this course
        $accessibleSubjects = $student->getSubjectDetails()['total_accessible'];
        if (! $accessibleSubjects->contains('id', $this->course->id)) {
            abort(403, 'You do not have access to this course.');
        }
    }

    public function setActiveTab($tab)
    {
        $this->activeTab = $tab;
    }

    public function render()
    {
        return view('livewire.students.course-details');
    }
}
