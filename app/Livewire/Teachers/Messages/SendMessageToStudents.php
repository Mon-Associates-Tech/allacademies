<?php

namespace App\Livewire\Teachers\Messages;

use App\Models\Message;
use App\Models\User;
use App\Services\MessageService;
use App\Traits\MessageAttachments;
use Livewire\Component;

class SendMessageToStudents extends Component
{
    use MessageAttachments;

    public $subject = '';
    public $body = '';
    public $isUrgent = false;
    public $targetType = 'academic_group';

    // Target criteria
    public $selectedAcademicGroups = [];
    public $selectedAcademicLevels = [];
    public $selectedSubjects = [];
    public $selectedStudents = [];
    public $selectedStudentGroups = [];

    public $userSearch = '';
    public $searchedStudents = [];

    protected $rules = [
        'subject' => 'required|string|max:255',
        'body' => 'required|string',
        'attachments.*' => 'file|max:10240',
    ];

    protected $messages = [
        'subject.required' => 'Please enter a subject for your message.',
        'body.required' => 'Please enter the message content.',
        'attachments.*.max' => 'Each attachment must be smaller than 10MB.',
    ];

    public function updatedAttachments()
    {
        $this->handleNewAttachments();
    }

    public function updatedUserSearch()
    {
        if (strlen($this->userSearch) >= 2) {
            $this->searchedStudents = app(TeacherMessageService::class)
                ->searchStudentsByTeacher(auth()->id(), $this->userSearch);
        } else {
            $this->searchedStudents = [];
        }
    }

    public function toggleStudent($studentId)
    {
        if (in_array($studentId, $this->selectedStudents)) {
            $this->selectedStudents = array_diff($this->selectedStudents, [$studentId]);
        } else {
            $this->selectedStudents[] = $studentId;
        }
    }

    public function toggleAcademicGroup($groupId)
    {
        if (in_array($groupId, $this->selectedAcademicGroups)) {
            $this->selectedAcademicGroups = array_diff($this->selectedAcademicGroups, [$groupId]);
        } else {
            $this->selectedAcademicGroups[] = $groupId;
        }
    }

    public function toggleAcademicLevel($levelId)
    {
        if (in_array($levelId, $this->selectedAcademicLevels)) {
            $this->selectedAcademicLevels = array_diff($this->selectedAcademicLevels, [$levelId]);
        } else {
            $this->selectedAcademicLevels[] = $levelId;
        }
    }

    public function toggleSubject($subjectId)
    {
        if (in_array($subjectId, $this->selectedSubjects)) {
            $this->selectedSubjects = array_diff($this->selectedSubjects, [$subjectId]);
        } else {
            $this->selectedSubjects[] = $subjectId;
        }
    }

    public function sendMessage()
    {
        $this->validate();

        $messageService = app(MessageService::class);
        $teacherMessageService = app(TeacherMessageService::class);

        // Determine recipients based on target type
        $recipients = collect();

        switch ($this->targetType) {
            case 'academic_group':
                foreach ($this->selectedAcademicGroups as $groupId) {
                    $recipients = $recipients->merge(
                        $teacherMessageService->getStudentsInAcademicGroup($groupId)
                    );
                }
                break;

            case 'academic_level':
                foreach ($this->selectedAcademicLevels as $levelId) {
                    $recipients = $recipients->merge(
                        $teacherMessageService->getStudentsInAcademicLevel($levelId)
                    );
                }
                break;

            case 'subject':
                foreach ($this->selectedSubjects as $subjectId) {
                    $recipients = $recipients->merge(
                        $teacherMessageService->getStudentsInSubject($subjectId)
                    );
                }
                break;

            case 'individual':
                $recipients = User::whereIn('id', $this->selectedStudents)
                    ->whereHas('roles', function ($query) {
                        $query->where('name', 'student');
                    })->get();
                break;
        }

        // Remove duplicates
        $recipients = $recipients->unique('id');

        if ($recipients->isEmpty()) {
            session()->flash('error', 'No recipients found for the selected criteria.');
            return;
        }

        // Create message
        $message = Message::create([
            'sender_id' => auth()->id(),
            'subject' => $this->subject,
            'body' => $this->body,
            'target_type' => $this->targetType,
            'target_criteria' => $this->getTargetCriteria(),
            'is_urgent' => $this->isUrgent,
            'status' => Message::STATUS_SENDING,
        ]);

        // Save attachments
        $this->saveAttachments($message);

        // Send message
        try {
            $messageService->sendMessage($message);
            session()->flash('success', 'Message sent to students successfully!');
            $this->resetForm();
        } catch (\Exception $e) {
            session()->flash('error', 'Failed to send message. Please try again.');
        }
    }

    protected function getTargetCriteria(): array
    {
        return [
            'academic_group_ids' => $this->selectedAcademicGroups,
            'academic_level_ids' => $this->selectedAcademicLevels,
            'subject_ids' => $this->selectedSubjects,
            'user_ids' => $this->selectedStudents,
        ];
    }

    protected function resetForm()
    {
        $this->subject = '';
        $this->body = '';
        $this->isUrgent = false;
        $this->selectedAcademicGroups = [];
        $this->selectedAcademicLevels = [];
        $this->selectedSubjects = [];
        $this->selectedStudents = [];
        $this->tempAttachments = [];
        $this->attachments = [];
    }

    public function render()
    {
        $teacherMessageService = app(TeacherMessageService::class);

        return view('livewire.teacher.messages.send-message-to-students', [
            'academicGroups' => $teacherMessageService->getAcademicGroupsForTeacher(auth()->id()),
            'academicLevels' => $teacherMessageService->getAcademicLevelsForTeacher(auth()->id()),
            'subjects' => $teacherMessageService->getSubjectsForTeacher(auth()->id()),
        ]);
    }
}
