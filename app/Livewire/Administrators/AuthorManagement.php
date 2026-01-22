<?php

namespace App\Livewire\Administrators;

use App\Models\Author;
use App\Models\Book;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class AuthorManagement extends Component
{
    use WithFileUploads, WithPagination;

    // Form properties
    public $name;

    public $email;

    public $password;

    public $password_confirmation;

    public $biography;

    public $specialization;

    public $profileImage;

    public $website;

    public $socialMedia = [];

    public $isActive = true;

    // Management properties
    public $searchTerm = '';

    public $isEditing = false;

    public $editingAuthorId;

    public $showForm = false;

    public $showDeleteModal = false;

    public $authorToDelete;

    // Filtering and sorting
    public $sortBy = 'created_at';

    public $sortDirection = 'desc';

    public $filterStatus = '';

    // Bulk operations
    public $selectedAuthors = [];

    public $selectAll = false;

    protected $rules = [
        'name' => 'required|string|min:3|max:255',
        'email' => 'required|email|unique:users,email',
        'password' => 'required|string|min:8|confirmed',
        'password_confirmation' => 'required|string|min:8',
        'biography' => 'nullable|string|max:2000',
        'specialization' => 'nullable|string|max:255',
        'website' => 'nullable|url|max:255',
        'profileImage' => 'nullable|image|max:2048',
        'socialMedia.twitter' => 'nullable|string|max:255',
        'socialMedia.linkedin' => 'nullable|string|max:255',
        'socialMedia.facebook' => 'nullable|string|max:255',
        'isActive' => 'boolean',
    ];

    protected $messages = [
        'name.required' => 'Author name is required.',
        'name.min' => 'Author name must be at least 3 characters.',
        'email.required' => 'Email address is required.',
        'email.email' => 'Please enter a valid email address.',
        'email.unique' => 'This email address is already registered.',
        'password.required' => 'Password is required for new authors.',
        'password.min' => 'Password must be at least 8 characters.',
        'password.confirmed' => 'Password confirmation does not match.',
        'biography.max' => 'Biography cannot exceed 2000 characters.',
        'website.url' => 'Please enter a valid website URL.',
        'profileImage.image' => 'Profile image must be a valid image file.',
        'profileImage.max' => 'Profile image cannot exceed 2MB.',
    ];

    public function mount()
    {
        $this->socialMedia = [
            'twitter' => '',
            'linkedin' => '',
            'facebook' => '',
        ];
    }

    // Real-time validation
    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);
    }

    // Password strength calculation
    public function getPasswordStrength()
    {
        $password = $this->password;
        $strength = 0;

        if (strlen($password) >= 8) {
            $strength++;
        }
        if (preg_match('/[a-z]/', $password)) {
            $strength++;
        }
        if (preg_match('/[A-Z]/', $password)) {
            $strength++;
        }
        if (preg_match('/[0-9]/', $password)) {
            $strength++;
        }
        if (preg_match('/[^a-zA-Z0-9]/', $password)) {
            $strength++;
        }

        return $strength;
    }

    public function getPasswordStrengthText()
    {
        $strength = $this->getPasswordStrength();

        return match ($strength) {
            0, 1 => 'Very Weak',
            2 => 'Weak',
            3 => 'Fair',
            4 => 'Good',
            5 => 'Strong',
            default => 'Very Weak'
        };
    }

    public function updatedSearchTerm()
    {
        $this->resetPage();
    }

    public function updatedSelectAll()
    {
        if ($this->selectAll) {
            $this->selectedAuthors = $this->getAuthorsProperty()->pluck('id')->toArray();
        } else {
            $this->selectedAuthors = [];
        }
    }

    public function showCreateForm()
    {
        $this->showForm = true;
        $this->isEditing = false;
        $this->resetForm();
    }

    public function hideForm()
    {
        $this->showForm = false;
        $this->resetForm();
    }

    public function getStatsProperty()
    {
        return [
            'total_authors' => Author::count(),
            'active_authors' => Author::with('user')->whereHas('user', function ($query) {
                $query->where('is_active', true);
            })->count(),
            'inactive_authors' => Author::with('user')->whereHas('user', function ($query) {
                $query->where('is_active', false);
            })->count(),
            //            'inactive_authors' =>  Author::where('is_active', false)->count(),
            'total_books' => Book::whereHas('author')->count(),
            'authors_with_books' => Author::has('books')->count(),
            'authors_without_books' => Author::doesntHave('books')->count(),
        ];
    }

    public function getSpecializationsProperty()
    {

        Author::whereNotNull('authors.writing_experience')
            ->where('authors.writing_experience', '!=', '')
            ->distinct()
            ->pluck('authors.writing_experience')
            ->sort()
            ->values();
    }

    public function create()
    {
        $this->validate();

        try {
            // Handle profile image upload
            $profileImagePath = null;
            if ($this->profileImage) {
                $profileImagePath = $this->profileImage->store('author-profiles', 'public');
            }

            // Create user account
            $user = User::create([
                'name' => $this->name,
                'email' => $this->email,
                'password' => Hash::make($this->password),
                'email_verified_at' => now(),
                'is_active' => $this->isActive,
            ]);

            // Assign author role
            $authorRole = Role::where('name', 'author')->first();
            if ($authorRole) {
                $user->roles()->attach($authorRole);
            }

            // Create author profile
            Author::create([
                'user_id' => $user->id,
                'biography' => $this->biography,
                //                'specialization' => $this->specialization,
                'website' => $this->website,
                'profile_image' => $profileImagePath,
                'social_media' => json_encode($this->socialMedia),
                'is_active' => $this->isActive,
            ]);

            $this->resetForm();
            $this->showForm = false;
            session()->flash('message', "Author '{$this->name}' has been created successfully!");

        } catch (\Exception $e) {
            session()->flash('error', 'Failed to create author. Please try again.');
        }
    }

    public function edit($authorId)
    {
        $this->isEditing = true;
        $this->editingAuthorId = $authorId;
        $this->showForm = true;

        $author = Author::with('user')->findOrFail($authorId);

        $this->name = $author->user->name;
        $this->email = $author->user->email;
        $this->password = '';
        $this->password_confirmation = '';
        $this->biography = $author->biography;
        $this->specialization = $author->specialization;
        $this->website = $author->website;
        $this->socialMedia = json_decode($author->social_media, true) ?: [
            'twitter' => '',
            'linkedin' => '',
            'facebook' => '',
        ];
        $this->isActive = $author->is_active;

        // Update validation rules for editing
        $this->rules['email'] = ['required', 'email', Rule::unique('users', 'email')->ignore($author->user_id)];
        $this->rules['password'] = 'nullable|string|min:8|confirmed';
        $this->rules['password_confirmation'] = 'nullable|string|min:8';
    }

    public function update()
    {
        $author = Author::with('user')->findOrFail($this->editingAuthorId);

        $this->validate();

        try {
            // Handle profile image upload
            $profileImagePath = $author->user->avatar;
            if ($this->profileImage) {
                // Delete old image if exists
                if ($profileImagePath) {
                    Storage::disk('public')->delete($profileImagePath);
                }
                $profileImagePath = $this->profileImage->store('avatars', 'public');
            }

            // Update user
            $userData = [
                'name' => $this->name,
                'email' => $this->email,
                'avatar' => $profileImagePath,
                'is_active' => $this->isActive,
            ];

            if ($this->password) {
                $userData['password'] = Hash::make($this->password);
            }

            $author->user->update($userData);

            // Update author profile
            $author->update([
                'biography' => $this->biography,
                //                'specialization' => $this->specialization,
                'website' => $this->website,
                'profile_image' => $profileImagePath,
                'social_media' => json_encode($this->socialMedia),
                'is_active' => $this->isActive,
            ]);

            $this->resetForm();
            $this->showForm = false;
            session()->flash('message', "Author '{$this->name}' has been updated successfully!");

        } catch (\Exception $e) {
            session()->flash('error', 'Failed to update author. Please try again.');
        }
    }

    public function confirmDelete($authorId)
    {
        $this->authorToDelete = Author::with('user')->findOrFail($authorId);
        $this->showDeleteModal = true;
    }

    public function delete()
    {
        try {
            // Delete profile image if exists
            if ($this->authorToDelete->profile_image) {
                Storage::disk('public')->delete($this->authorToDelete->profile_image);
            }

            // Delete author and user
            //            $this->authorToDelete->user->delete();
            $this->authorToDelete->delete();

            $this->showDeleteModal = false;
            session()->flash('message', "Author '{$this->authorToDelete->user->name}' has been deleted successfully!");

        } catch (\Exception $e) {
            logError($e);
            session()->flash('error', 'Failed to delete author. Please try again.');
        }
    }

    public function bulkDelete()
    {
        try {
            $authors = Author::whereIn('id', $this->selectedAuthors)->with('user')->get();

            foreach ($authors as $author) {
                if ($author->profile_image) {
                    Storage::disk('public')->delete($author->profile_image);
                }
                $author->user->delete();
                $author->delete();
            }

            $this->selectedAuthors = [];
            $this->selectAll = false;
            session()->flash('message', 'Selected authors have been deleted successfully!');

        } catch (\Exception $e) {
            session()->flash('error', 'Failed to delete selected authors. Please try again.');
        }
    }

    public function resetForm()
    {
        $this->reset([
            'name', 'email', 'password', 'password_confirmation',
            'biography', 'specialization', 'website', 'profileImage',
            'isEditing', 'editingAuthorId',
        ]);

        $this->socialMedia = [
            'twitter' => '',
            'linkedin' => '',
            'facebook' => '',
        ];
        $this->isActive = true;

        // Reset validation rules
        $this->rules['email'] = 'required|email|unique:users,email';
        $this->rules['password'] = 'required|string|min:8|confirmed';
        $this->rules['password_confirmation'] = 'required|string|min:8';
    }

    public function sortBy($field)
    {
        if ($this->sortBy === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $field;
            $this->sortDirection = 'asc';
        }
        $this->resetPage();
    }

    public function getAuthorsProperty()
    {
        return Author::with('user')
            ->when($this->searchTerm, function ($query) {
                $query->whereHas('user', function ($q) {
                    $q->where('name', 'like', '%'.$this->searchTerm.'%')
                        ->orWhere('email', 'like', '%'.$this->searchTerm.'%');
                });
                //    ->orWhere('specialization', 'like', '%' . $this->searchTerm . '%');
            })
            ->when($this->filterStatus !== '', function ($query) {
                $query->where('is_active', $this->filterStatus);
            })
            ->orderBy($this->sortBy, $this->sortDirection)
            ->paginate(15);
    }

    public function render()
    {
        return view('livewire.administrators.author-management', [
            'authors' => $this->getAuthorsProperty(),
            'specializations' => $this->getSpecializationsProperty(),
            'stats' => $this->getStatsProperty(),
        ]);
    }
}
