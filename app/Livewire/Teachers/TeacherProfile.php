<?php

namespace App\Livewire\Teachers;

use App\Models\Teacher;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithFileUploads;

class TeacherProfile extends Component
{
    use WithFileUploads;

    // Profile form properties
    public $name;
    public $email;
    public $phone;
    public $bio;
    public $avatar;
    public $cover_image;

    // Password form properties
    public $current_password;
    public $new_password;
    public $new_password_confirmation;

    // Modal states
    public $showPasswordModal = false;
    public $showPreferencesModal = false;

    // Statistics
    public $totalStudents = 0;
    public $totalAssignments = 0;
    public $totalSubjects = 0;

    // Teacher instance
    public $teacher;

    protected $rules = [
        'name' => ['required', 'string', 'max:255'],
        'email' => ['required', 'string', 'email', 'max:255'],
        'phone' => ['nullable', 'string', 'max:20'],
        'bio' => ['nullable', 'string', 'max:1000'],
        'avatar' => ['nullable', 'image', 'max:2048'], // 2MB max
        'cover_image' => ['nullable', 'image', 'max:5120'], // 5MB max
    ];

    protected $passwordRules = [
        'current_password' => ['required', 'string'],
        'new_password' => ['required', 'string', 'min:8', 'confirmed'],
        'new_password_confirmation' => ['required', 'string', 'min:8'],
    ];

    public function mount()
    {
        $this->teacher = Auth::user()->teacher;

        if (!$this->teacher) {
            abort(403, 'Access denied. Teacher profile not found.');
        }

        $this->loadProfileData();
        $this->loadStatistics();
    }

    public function loadProfileData()
    {
        $user = Auth::user();

        $this->name = $user->name;
        $this->email = $user->email;
        $this->phone = $user->phone ?? '';
        $this->bio = $user->bio ?? '';
    }

    public function loadStatistics()
    {
        if ($this->teacher) {
            $this->totalStudents = $this->teacher->assignedStudents()->count();
            $this->totalAssignments = $this->teacher->assignments()->count();
            $this->totalSubjects = $this->teacher->academicSubjects()->count();
        }
    }

    public function updateProfile()
    {
        $user = Auth::user();

        // Update email validation to exclude current user
        $this->rules['email'] = ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)];

        $validatedData = $this->validate();

        try {
            // Handle avatar upload
            if ($this->avatar) {
                // Delete old avatar if exists
                if ($user->avatar) {
                    Storage::disk('public')->delete($user->avatar);
                }

                // Store new avatar
                $avatarPath = $this->avatar->store('avatars', 'public');
                $user->avatar = $avatarPath;
            }

            // Handle cover image upload
            if ($this->cover_image) {
                // Delete old cover image if exists
                if ($user->cover_image) {
                    Storage::disk('public')->delete($user->cover_image);
                }

                // Store new cover image
                $coverPath = $this->cover_image->store('covers', 'public');
                $user->cover_image = $coverPath;
            }

            // Update user data
            $user->name = $validatedData['name'];
            $user->email = $validatedData['email'];
            $user->phone = $validatedData['phone'];
            $user->bio = $validatedData['bio'];
            $user->save();

            // Reset file inputs
            $this->avatar = null;
            $this->cover_image = null;

            session()->flash('message', 'Profile updated successfully!');

            // Dispatch success event
            $this->dispatch('profile-updated', [
                'message' => 'Profile updated successfully!',
                'type' => 'success'
            ]);

        } catch (\Exception $e) {
            session()->flash('error', 'An error occurred while updating your profile. Please try again.');

            // Dispatch error event
            $this->dispatch('profile-error', [
                'message' => 'An error occurred while updating your profile.',
                'type' => 'error'
            ]);
        }
    }

    public function removeCoverImage()
    {
        $user = Auth::user();

        if ($user->cover_image && Storage::disk('public')->exists($user->cover_image)) {
            Storage::disk('public')->delete($user->cover_image);
        }

        $user->update(['cover_image' => null]);

        session()->flash('message', 'Cover image removed successfully!');
    }

    public function updatePassword()
    {
        $this->validate($this->passwordRules);

        $user = Auth::user();

        // Check if current password is correct
        if (!Hash::check($this->current_password, $user->password)) {
            $this->addError('current_password', 'The current password is incorrect.');
            return;
        }

        try {
            // Update password
            $user->password = Hash::make($this->new_password);
            $user->save();

            // Reset password form
            $this->reset(['current_password', 'new_password', 'new_password_confirmation']);

            // Hide modal
            $this->showPasswordModal = false;

            session()->flash('message', 'Password updated successfully!');

            // Dispatch success event
            $this->dispatch('password-updated', [
                'message' => 'Password updated successfully!',
                'type' => 'success'
            ]);

        } catch (\Exception $e) {
            session()->flash('error', 'An error occurred while updating your password. Please try again.');

            // Dispatch error event
            $this->dispatch('password-error', [
                'message' => 'An error occurred while updating your password.',
                'type' => 'error'
            ]);
        }
    }

    public function showPasswordModal()
    {
        $this->showPasswordModal = true;
        $this->reset(['current_password', 'new_password', 'new_password_confirmation']);
    }

    public function hidePasswordModal()
    {
        $this->showPasswordModal = false;
        $this->reset(['current_password', 'new_password', 'new_password_confirmation']);
    }

    public function showPreferencesModal()
    {
        $this->showPreferencesModal = true;
    }

    public function hidePreferencesModal()
    {
        $this->showPreferencesModal = false;
    }

    public function refreshProfile()
    {
        $this->loadProfileData();
        $this->loadStatistics();

        session()->flash('message', 'Profile refreshed successfully!');

        // Dispatch refresh event
        $this->dispatch('profile-refreshed', [
            'message' => 'Profile refreshed successfully!',
            'type' => 'success'
        ]);
    }

    public function render()
    {
        return view('livewire.teachers.teacher-profile', [
            'teacher' => $this->teacher,
            'totalStudents' => $this->totalStudents,
            'totalAssignments' => $this->totalAssignments,
            'totalSubjects' => $this->totalSubjects,
        ]);
    }
}
