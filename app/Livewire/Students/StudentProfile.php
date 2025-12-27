<?php

namespace App\Livewire\Students;

use App\Models\Assessment;
use App\Models\Activity;
use App\Models\Student;
use App\Models\AcademicFeeStructure;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;
use Carbon\Carbon;

class StudentProfile extends Component
{
    use WithFileUploads;

    public $student;
    public $name;
    public $email;
    public $phone;
    public $address;
    public $date_of_birth;
    public $emergency_contact_name;
    public $emergency_contact_phone;
    public $avatar;
    public $currentAvatar;
    public $isEditing = false;

    // New properties for enhanced profile
    public $bio;
    public $favorite_subjects;
    public $learning_goals;
    public $social_links;

    protected $rules = [
        'name' => 'required|string|max:255',
        'email' => 'required|email|max:255',
        'phone' => 'nullable|string|max:20',
        'address' => 'nullable|string|max:500',
        'date_of_birth' => 'nullable|date',
        'emergency_contact_name' => 'nullable|string|max:255',
        'emergency_contact_phone' => 'nullable|string|max:20',
        'avatar' => 'nullable|image|max:2048',
        'bio' => 'nullable|string|max:500',
        'favorite_subjects' => 'nullable|string|max:255',
        'learning_goals' => 'nullable|string|max:1000',
        'social_links' => 'nullable|array'
    ];

    public function mount()
    {
        $this->student = Auth::user()->student;
        $this->student = Student::withoutGlobalScopes()->where("user_id", Auth::id())->first();

        if ($this->student) {
            $user = $this->student->user;
            $this->name = $user->name;
            $this->email = $user->email;
            $this->phone = $user->phone ?? '';
            $this->address = $user->address ?? '';
            $this->date_of_birth = $user->date_of_birth ? $user->date_of_birth->format('Y-m-d') : '';
            $this->emergency_contact_name = $user->emergency_contact_name ?? '';
            $this->emergency_contact_phone = $user->emergency_contact_phone ?? '';
            $this->currentAvatar = $user->avatar;

            // Load new fields
            $this->bio = $user->bio ?? '';
            $this->favorite_subjects = $user->favorite_subjects ?? '';
            $this->learning_goals = $user->learning_goals ?? '';
            $this->social_links = json_decode($user->social_links ?? '[]', true);
        }
    }

    public function toggleEdit()
    {
        $this->isEditing = !$this->isEditing;

        if (!$this->isEditing) {
            // Reset form when canceling edit
            $this->mount();
        }
    }

    public function updateProfile()
    {
        $this->validate();

        if (!$this->student) {
            session()->flash('error', 'Student profile not found.');
            return;
        }

        $user = $this->student->user;

        // Handle avatar upload
        if ($this->avatar) {
            // Delete old avatar if exists
            if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
                Storage::disk('public')->delete($user->avatar);
            }

            $avatarPath = $this->avatar->store('avatars', 'public');
            $user->avatar = $avatarPath;
            $this->currentAvatar = $avatarPath;
        }

        // Update user data
        $user->update([
            'name' => $this->name,
            'email' => $this->email,
            'phone' => $this->phone,
            'address' => $this->address,
            'date_of_birth' => $this->date_of_birth ? Carbon::parse($this->date_of_birth) : null,
            'emergency_contact_name' => $this->emergency_contact_name,
            'emergency_contact_phone' => $this->emergency_contact_phone,
            'avatar' => $user->avatar,
            'bio' => $this->bio,
            'favorite_subjects' => $this->favorite_subjects,
            'learning_goals' => $this->learning_goals,
            'social_links' => json_encode($this->social_links ?? [])
        ]);

        $this->isEditing = false;
        $this->avatar = null;

        session()->flash('success', 'Profile updated successfully!');
    }

    public function removeAvatar()
    {
        if (!$this->student) return;

        $user = $this->student->user;

        if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
            Storage::disk('public')->delete($user->avatar);
        }

        $user->update(['avatar' => null]);
        $this->currentAvatar = null;

        session()->flash('success', 'Avatar removed successfully!');
    }

    // New methods for enhanced profile insights
    private function getProfileStats()
    {
        if (!$this->student) return [];

        $totalAssessments = Assessment::where('student_id', $this->student->id)->count();
        $averageScore = Assessment::where('student_id', $this->student->id)
            ->where('status', 'completed')
            ->avg('score') ?? 0;

        $thisMonthAssessments = Assessment::where('student_id', $this->student->id)
            ->whereMonth('created_at', Carbon::now()->month)
            ->count();

        $memberSince = $this->student->created_at?->diffForHumans();

        return [
            'total_assessments' => $totalAssessments,
            'average_score' => round($averageScore, 1),
            'this_month_assessments' => $thisMonthAssessments,
            'member_since' => $memberSince
        ];
    }

    private function getRecentActivity()
    {
        if (!$this->student) return collect();

        return Assessment::where('student_id', $this->student->id)
            ->with(['subject', 'topic'])
            ->latest()
            ->take(3)
            ->get();
    }

    private function getUpcomingTasks()
    {
        if (!$this->student) return collect();

        return Activity::forStudent($this->student->id)
            ->upcoming()
            ->with(['subject'])
            ->take(3)
            ->get();
    }

    public function render()
    {
        $profileStats = $this->getProfileStats();
        $recentActivity = $this->getRecentActivity();
        $upcomingTasks = $this->getUpcomingTasks();

        $feeDetails = null;
        $totalPaid = 0;
        $remainingAmount = 0;
        $feeStatus = 'Pending';
        $paymentMethod = 'Momo'; // default
        $paymentHistory = collect();

        if ($this->student) {
            // Get current term
            $currentTerm = \App\Models\AcademicPeriod::where('is_current', 1)->first();
            $currentTermId = $currentTerm->id ?? null;

            // Get the matching fee structure for this student
            $feeDetails = \App\Models\AcademicFeeStructure::where('school_id', $this->student->school_id)
                ->where('academic_group_id', $this->student->academic_group_id)
                ->where('academic_level_id', $this->student->academic_level_id)
                ->where('current_term_id', $currentTermId)
                ->first();

            $termTotalAmount = $feeDetails->term_total_amount ?? $feeDetails->amount ?? 0;
            $paymentMethod = $feeDetails->payment_method ?? 'Momo';

            // Sum total paid by this student for the current term
            $totalPaid = \App\Models\SchoolFee::where('student_id', $this->student->id)
                ->where('term_id', $currentTermId)
                ->sum('amount');

            // Compute remaining & status
            $remainingAmount = max($termTotalAmount - $totalPaid, 0);

            if ($totalPaid >= $termTotalAmount && $termTotalAmount > 0) {
                $feeStatus = 'Completed';
            } elseif ($totalPaid > 0 && $totalPaid < $termTotalAmount) {
                $feeStatus = 'Part Payment';
            } else {
                $feeStatus = 'Pending';
            }

            // Attach computed fields for easy Blade access
            if ($feeDetails) {
                $feeDetails->total_paid = $totalPaid;
                $feeDetails->remaining = $remainingAmount;
                $feeDetails->status = $feeStatus;
                $feeDetails->payment_method = $paymentMethod;
            }

            // ✅ Fetch payment history (all terms)
            $paymentHistory = \App\Models\SchoolFee::where('student_id', $this->student->id)
                ->with([
                    'payer',
                    'student.academicGroup',
                    'student.academicLevel',
                    'academicPeriod'
                ])
                ->orderBy('created_at', 'desc')
                ->get();
        }

        return view('livewire.students.profile', [
            'student' => $this->student,
            'studentGroup' => $this->student?->studentGroup,
            'profileStats' => $profileStats,
            'recentActivity' => $recentActivity,
            'upcomingTasks' => $upcomingTasks,
            'feeDetails' => $feeDetails,
            'paymentHistory' => $paymentHistory,
        ]);
    }
}
