<?php

namespace App\Livewire\Teachers;

use App\Models\Assignment;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class ViewAssignment extends Component
{
    public $assignment;

    public $teacher;

    public $eligibleStudents = [];

    public $studentsWithStatus = [];

    public $submissionsCount = 0;

    public $completedSubmissions = 0;

    public $inProgressSubmissions = 0;

    public $notStartedCount = 0;

    public $questionStatistics = [];

    public $previewQuestions = null;

    public $showQuestionPreview = false;

    public $showStudentDetails = false;

    public $selectedFilter = 'all'; // all, completed, in_progress, not_started

    public function mount(Assignment $assignment)
    {
        $this->assignment = $assignment->load([
            'academicSubject',
            'academicGroups',
            'academicLevels',
            'students.user',
            'studentGroups',
            'topics',
            'subtopics',
            'submissions.student.user',
        ]);
        // Ensure the assignment belongs to the current user
        if ($this->assignment->user_id !== Auth::id()) {
            abort(403, 'You are not authorized to view this assignment.');
        }

        $this->loadAssignmentData();

    }

    private function loadAssignmentData()
    {
        // Get all eligible students
        $this->eligibleStudents = $this->assignment->getEligibleStudents();

        // Build students with their submission status
        $this->buildStudentsWithStatus();

        // Get submission statistics
        $submissions = $this->assignment->submissions;
        $this->submissionsCount = $submissions->count();
        $this->completedSubmissions = $submissions->where('status', 'submitted')->count() +
                                     $submissions->where('status', 'graded')->count();
        $this->inProgressSubmissions = $submissions->where('status', 'in_progress')->count();
        $this->notStartedCount = $this->eligibleStudents->count() - $this->submissionsCount;

        // Get question statistics
        $this->questionStatistics = $this->assignment->getQuestionStatistics();

        // Get preview questions if not randomized
        if (! $this->assignment->is_randomized) {
            $this->previewQuestions = $this->assignment->getPreviewQuestions();
        }
    }

    private function buildStudentsWithStatus()
    {
        $submissions = $this->assignment->submissions->keyBy('student_id');

        $this->studentsWithStatus = $this->eligibleStudents->map(function ($student) use ($submissions) {
            $submission = $submissions->get($student->id);

            $status = 'not_started';
            $statusLabel = 'Not Started';
            $statusColor = 'gray';
            $submittedAt = null;
            $startedAt = null;
            $score = null;
            $timeSpent = null;

            if ($submission) {
                switch ($submission->status) {
                    case 'in_progress':
                        $status = 'in_progress';
                        $statusLabel = 'In Progress';
                        $statusColor = 'blue';
                        $startedAt = $submission->started_at;
                        $timeSpent = $submission->time_spent_minutes;
                        break;
                    case 'submitted':
                        $status = 'submitted';
                        $statusLabel = 'Submitted';
                        $statusColor = 'green';
                        $submittedAt = $submission->submitted_at;
                        $startedAt = $submission->started_at;
                        $timeSpent = $submission->time_spent_minutes;
                        break;
                    case 'graded':
                        $status = 'graded';
                        $statusLabel = 'Graded';
                        $statusColor = 'purple';
                        $submittedAt = $submission->submitted_at;
                        $startedAt = $submission->started_at;
                        $score = $submission->score;
                        $timeSpent = $submission->time_spent_minutes;
                        break;
                }
            }

            return [
                'id' => $student->id,
                'name' => $student->user->name ?? 'Unknown',
                'email' => $student->user->email ?? '',
                'status' => $status,
                'status_label' => $statusLabel,
                'status_color' => $statusColor,
                'submitted_at' => $submittedAt,
                'started_at' => $startedAt,
                'score' => $score,
                'total_marks' => $this->assignment->total_marks,
                'time_spent_minutes' => $timeSpent,
                'submission_id' => $submission ? $submission->id : null,
            ];
        });
    }

    public function getFilteredStudentsProperty()
    {
        if ($this->selectedFilter === 'all') {
            return $this->studentsWithStatus;
        }

        return $this->studentsWithStatus->filter(function ($student) {
            return $student['status'] === $this->selectedFilter;
        });
    }

    public function setFilter($filter)
    {
        $this->selectedFilter = $filter;
    }

    public function toggleQuestionPreview()
    {
        $this->showQuestionPreview = ! $this->showQuestionPreview;
    }

    public function toggleStudentDetails()
    {
        $this->showStudentDetails = ! $this->showStudentDetails;
    }

    public function deleteAssignment()
    {
        if ($this->assignment->user_id !== Auth::id()) {
            session()->flash('error', 'You are not authorized to delete this assignment.');

            return;
        }

        $assignmentTitle = $this->assignment->title;
        $assignmentId = $this->assignment->id;
        $this->assignment->delete();

        // Log activity
        Assignment::logActivityForModel('delete', 'Assignment Deleted', 'assignment', [
            'assignment_title' => $assignmentTitle,
            'assignment_id' => $assignmentId,
            'deleted_by' => auth()->user()?->name ?? 'Unknown',
        ]);

        session()->flash('success', 'Assignment deleted successfully.');

        return redirect()->route('teachers.dashboard');
    }

    public function duplicateAssignment()
    {
        $newAssignment = $this->assignment->replicate();
        $newAssignment->title = $this->assignment->title.' (Copy)';
        $newAssignment->status = 'draft';
        $newAssignment->created_at = now();
        $newAssignment->updated_at = now();
        $newAssignment->save();

        // Copy relationships
        $newAssignment->academicGroups()->attach($this->assignment->academicGroups->pluck('id'));
        $newAssignment->academicLevels()->attach($this->assignment->academicLevels->pluck('id'));
        $newAssignment->students()->attach($this->assignment->students->pluck('id'));
        $newAssignment->studentGroups()->attach($this->assignment->studentGroups->pluck('id'));
        $newAssignment->topics()->attach($this->assignment->topics->pluck('id'));
        $newAssignment->subtopics()->attach($this->assignment->subtopics->pluck('id'));

        session()->flash('success', 'Assignment duplicated successfully.');

        return redirect()->route('teachers.assignment.view', $newAssignment->id);
    }

    public function render()
    {
        return view('livewire.teachers.view-assignment');
    }
}
