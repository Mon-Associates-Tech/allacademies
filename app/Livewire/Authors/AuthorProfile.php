<?php

namespace App\Livewire\Authors;

use App\Models\Author;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithFileUploads;

class AuthorProfile extends Component
{
    use WithFileUploads;

    // Profile form properties
    public $name;

    public $email;

    public $phone;

    public $biography;

    public $avatar;

    public $website;

    public $pen_name;

    public $social_links = [
        'twitter' => '',
        'facebook' => '',
        'instagram' => '',
        'linkedin' => '',
        'goodreads' => '',
    ];

    // Author-specific fields
    public $genres = [];

    public $writing_experience;

    public $education;

    public $awards;

    public $author_statement;

    // Password form properties
    public $current_password;

    public $new_password;

    public $new_password_confirmation;

    // Modal states
    public $showPasswordModal = false;

    public $showPreferencesModal = false;

    // Statistics
    public $totalBooks = 0;

    public $totalSubscriptions = 0;

    public $totalBorrowings = 0;

    public $totalRevenue = 0;

    public $averageRating = 0;

    public $totalReviews = 0;

    // Author instance
    public $author;

    protected $rules = [
        'name' => ['required', 'string', 'max:255'],
        'email' => ['required', 'string', 'email', 'max:255'],
        'phone' => ['nullable', 'string', 'max:20'],
        'biography' => ['nullable', 'string', 'max:1000'],
        'avatar' => ['nullable', 'image', 'max:2048'], // 2MB max
        'website' => ['nullable', 'url', 'max:255'],
        'pen_name' => ['nullable', 'string', 'max:255'],
        'social_links.twitter' => ['nullable', 'string', 'max:255'],
        'social_links.facebook' => ['nullable', 'string', 'max:255'],
        'social_links.instagram' => ['nullable', 'string', 'max:255'],
        'social_links.linkedin' => ['nullable', 'string', 'max:255'],
        'social_links.goodreads' => ['nullable', 'string', 'max:255'],
        'writing_experience' => ['nullable', 'string', 'max:500'],
        'education' => ['nullable', 'string', 'max:500'],
        'awards' => ['nullable', 'string', 'max:500'],
        'author_statement' => ['nullable', 'string', 'max:1000'],
    ];

    protected $passwordRules = [
        'current_password' => ['required', 'string'],
        'new_password' => ['required', 'string', 'min:8', 'confirmed'],
        'new_password_confirmation' => ['required', 'string', 'min:8'],
    ];

    public function mount()
    {
        $this->author = Auth::user()->author;

        if (! $this->author) {
            // Create author profile if it doesn't exist
            $this->author = Author::create([
                'user_id' => Auth::id(),
            ]);
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
        $this->website = $user->website ?? '';
        $this->pen_name = $user->pen_name ?? '';

        // Load social links
        $socialLinks = $user->social_links ?? [];
        $this->social_links = array_merge($this->social_links, $socialLinks);

        // Load author-specific data
        $this->writing_experience = $user->writing_experience ?? '';
        $this->education = $user->education ?? '';
        $this->awards = $user->awards ?? '';
        $this->author_statement = $user->author_statement ?? '';
    }

    public function loadStatistics()
    {
        if ($this->author) {
            $books = $this->author->books();
            $this->totalBooks = $books->count();
            $this->totalSubscriptions = $books->withCount('subscriptions')->get()->sum('subscriptions_count');
            $this->totalBorrowings = $books->withCount('borrowings')->get()->sum('borrowings_count');
            $this->totalRevenue = $books->get()->sum('annual_subscription_fee');

            // Calculate average rating (you might need to add a reviews relationship)
            // $this->averageRating = $books->withAvg('reviews', 'rating')->get()->avg('reviews_avg_rating') ?? 0;
            // $this->totalReviews = $books->withCount('reviews')->get()->sum('reviews_count');
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

            // Update user data
            $user->name = $validatedData['name'];
            $user->email = $validatedData['email'];
            $user->phone = $validatedData['phone'];
            $user->biography = $validatedData['biography'];
            $user->website = $validatedData['website'];
            $user->pen_name = $validatedData['pen_name'];
            $user->social_links = $validatedData['social_links'];
            $user->writing_experience = $validatedData['writing_experience'];
            $user->education = $validatedData['education'];
            $user->awards = $validatedData['awards'];
            $user->author_statement = $validatedData['author_statement'];
            $user->save();

            // Reset avatar input
            $this->avatar = null;

            session()->flash('message', 'Profile updated successfully!');

            // Dispatch success event
            $this->dispatch('profile-updated', [
                'message' => 'Profile updated successfully!',
                'type' => 'success',
            ]);

        } catch (\Exception $e) {
            session()->flash('error', 'An error occurred while updating your profile. Please try again.');

            // Dispatch error event
            $this->dispatch('profile-error', [
                'message' => 'An error occurred while updating your profile.',
                'type' => 'error',
            ]);
        }
    }

    public function updatePassword()
    {
        $this->validate($this->passwordRules);

        $user = Auth::user();

        // Check if current password is correct
        if (! Hash::check($this->current_password, $user->password)) {
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
                'type' => 'success',
            ]);

        } catch (\Exception $e) {
            session()->flash('error', 'An error occurred while updating your password. Please try again.');

            // Dispatch error event
            $this->dispatch('password-error', [
                'message' => 'An error occurred while updating your password.',
                'type' => 'error',
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
            'type' => 'success',
        ]);
    }

    public function render()
    {
        return view('livewire.authors.author-profile', [
            'author' => $this->author,
            'totalBooks' => $this->totalBooks,
            'totalSubscriptions' => $this->totalSubscriptions,
            'totalBorrowings' => $this->totalBorrowings,
            'totalRevenue' => $this->totalRevenue,
            'averageRating' => $this->averageRating,
            'totalReviews' => $this->totalReviews,
        ]);
    }
}
