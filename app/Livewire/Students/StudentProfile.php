<?php

namespace App\Livewire\Students;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Student;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

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

    protected $rules = [
        'name' => 'required|string|max:255',
        'email' => 'required|email|max:255',
        'phone' => 'nullable|string|max:20',
        'address' => 'nullable|string|max:500',
        'date_of_birth' => 'nullable|date',
        'emergency_contact_name' => 'nullable|string|max:255',
        'emergency_contact_phone' => 'nullable|string|max:20',
        'avatar' => 'nullable|image|max:2048'
    ];

    public function mount()
    {
        $this->student = Auth::user()->student;

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
            'date_of_birth' => $this->date_of_birth ? \Carbon\Carbon::parse($this->date_of_birth) : null,
            'emergency_contact_name' => $this->emergency_contact_name,
            'emergency_contact_phone' => $this->emergency_contact_phone,
            'avatar' => $user->avatar
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

    public function render()
    {
        return view('livewire.students.profile', [
            'student' => $this->student,
            'studentGroup' => $this->student?->studentGroup
        ]);
    }
}
