<?php

namespace App\Livewire\Public;

use App\Models\GeneralExam;
use App\Models\GeneralExamParticipant;
use App\Models\Student;
use App\Services\GeneralExam\GeneralExamParticipantVerificationService;
use App\Services\GeneralExam\GeneralExamService;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class JoinGeneralExam extends Component
{
    // Step tracking: 1=access code, 2=participant type, 3=registration, 4=verification, 5=ready
    public int $currentStep = 1;

    // Access code
    public string $accessCode = '';

    public ?GeneralExam $assignment = null;

    // Participant type selection
    public string $participantType = ''; // 'student' or 'guest'

    // Guest registration
    public string $name = '';

    public string $email = '';

    public string $phone = '';

    // Verification
    public ?GeneralExamParticipant $participant = null;

    public bool $verificationSent = false;

    public bool $isVerified = false;

    // For authenticated students
    public ?Student $student = null;

    protected GeneralExamService $assignmentService;

    protected GeneralExamParticipantVerificationService $verificationService;

    protected $rules = [
        'accessCode' => 'required|string|min:6|max:12',
        'name' => 'required|string|min:2|max:255',
        'email' => 'required|email|max:255',
        'phone' => 'nullable|string|max:20',
    ];

    protected $messages = [
        'accessCode.required' => 'Please enter the access code.',
        'accessCode.min' => 'Access code must be at least 6 characters.',
        'name.required' => 'Please enter your name.',
        'email.required' => 'Please enter your email address.',
        'email.email' => 'Please enter a valid email address.',
    ];

    public function boot(
        GeneralExamService             $assignmentService,
        GeneralExamParticipantVerificationService $verificationService
    ): void {
        $this->assignmentService = $assignmentService;
        $this->verificationService = $verificationService;
    }

    public function mount(?string $code = null): void
    {
        // Check if user is authenticated and is a student
        if (Auth::check()) {
            $this->student = Student::where('user_id', Auth::id())->first();
        }

        // Pre-fill access code if provided in URL
        if ($code) {
            $this->accessCode = strtoupper($code);
            $this->validateAccessCode();
        }
    }

    public function validateAccessCode(): void
    {
        $this->validate(['accessCode' => 'required|string|min:6|max:12']);

        $result = $this->assignmentService->validateAccessCode($this->accessCode);

        if (! $result['valid']) {
            $this->addError('accessCode', $result['error']);

            return;
        }

        $this->assignment = $result['assignment'];
        $this->currentStep = 2;

        // If authenticated student, skip to step 2 with pre-selection
        if ($this->student) {
            $this->participantType = 'student';
        }
    }

    public function selectParticipantType(string $type): void
    {
        if (! in_array($type, ['student', 'guest'])) {
            return;
        }

        $this->participantType = $type;

        if ($type === 'student' && $this->student) {
            // Check if student can take the assignment
            $canTake = $this->assignmentService->canStudentTakeAssignment($this->assignment, $this->student);

            if (! $canTake['can_take']) {
                $this->addError('participantType', $canTake['reason']);

                return;
            }

            // Student is ready to start
            $this->currentStep = 5;
        } else {
            // Guest needs to register
            $this->currentStep = 3;

            // Pre-fill if user is logged in but not a student
            if (Auth::check() && ! $this->student) {
                $this->name = Auth::user()->name;
                $this->email = Auth::user()->email;
            }
        }
    }

    public function registerParticipant(): void
    {
        $this->validate([
            'name' => 'required|string|min:2|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
        ]);

        $result = $this->verificationService->registerAndVerify([
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ], $this->assignment);

        if (! $result['success']) {
            $this->addError('email', $result['error'] ?? 'Registration failed.');

            return;
        }

        $this->participant = $result['participant'];
        $this->verificationSent = true;

        if ($result['already_verified']) {
            $this->isVerified = true;
            $this->currentStep = 5;
        } else {
            $this->currentStep = 4;
        }
    }

    public function resendVerification(): void
    {
        if (! $this->participant || ! $this->assignment) {
            return;
        }

        $result = $this->verificationService->resendVerification($this->participant, $this->assignment);

        if ($result['success']) {
            session()->flash('success', $result['message']);
        } else {
            $this->addError('verification', $result['error']);
        }
    }

    public function checkVerification(): void
    {
        if (! $this->participant) {
            return;
        }

        $this->participant->refresh();

        if ($this->participant->isEmailVerified()) {
            $this->isVerified = true;
            $this->currentStep = 5;
        } else {
            $this->addError('verification', 'Email not yet verified. Please check your inbox.');
        }
    }

    public function startAssignment(): void
    {
        if (! $this->assignment) {
            $this->addError('general', 'Assignment not found.');

            return;
        }

        try {
            if ($this->participantType === 'student' && $this->student) {
                // Create submission for student
                $submission = $this->assignmentService->getOrCreateSubmission(
                    $this->assignment,
                    Student::class,
                    $this->student->id,
                    [
                        'ip_address' => request()->ip(),
                        'user_agent' => request()->userAgent(),
                    ]
                );
            } elseif ($this->participant) {
                // Check if participant can take assignment
                $canTake = $this->verificationService->canParticipantTakeAssignment(
                    $this->participant,
                    $this->assignment
                );

                if (! $canTake['can_take']) {
                    $this->addError('general', $canTake['message']);

                    return;
                }

                // Create submission for participant
                $submission = $this->assignmentService->getOrCreateSubmission(
                    $this->assignment,
                    GeneralExamParticipant::class,
                    $this->participant->id,
                    [
                        'ip_address' => request()->ip(),
                        'user_agent' => request()->userAgent(),
                    ]
                );
            } else {
                $this->addError('general', 'Invalid participant.');

                return;
            }

            // Redirect to take assignment
            $this->redirect(route('general-exams.take', $submission));

        } catch (\Exception $e) {
            $this->addError('general', 'Failed to start assignment: '.$e->getMessage());
        }
    }

    public function goBack(): void
    {
        if ($this->currentStep > 1) {
            $this->currentStep--;

            // Reset relevant data when going back
            if ($this->currentStep === 1) {
                $this->assignment = null;
                $this->participantType = '';
            } elseif ($this->currentStep === 2) {
                $this->participantType = '';
                $this->participant = null;
            }
        }
    }

    public function getAssignmentInfoProperty(): ?array
    {
        if (! $this->assignment) {
            return null;
        }

        return [
            'title' => $this->assignment->title,
            'description' => $this->assignment->description,
            'type' => ucfirst($this->assignment->type),
            'duration' => $this->assignment->duration_in_minutes
                ? $this->assignment->duration_in_minutes.' minutes'
                : 'No time limit',
            'questions_count' => $this->assignment->questions()->count(),
            'total_marks' => $this->assignment->total_marks,
            'starts_at' => $this->assignment->starts_at?->format('M d, Y H:i'),
            'ends_at' => $this->assignment->ends_at?->format('M d, Y H:i'),
            'proctoring_enabled' => $this->assignment->proctoring_enabled,
            'max_attempts' => $this->assignment->max_attempts,
        ];
    }

    public function render()
    {
        return view('livewire.public.join-general-exam', [
            'assignmentInfo' => $this->assignmentInfo,
            'isAuthenticated' => Auth::check(),
            'isStudent' => $this->student !== null,
        ])->layout('layouts.general-exam');
    }
}
