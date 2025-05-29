<?php

namespace App\Livewire\Administrators;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use App\Models\Author;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class AuthorManagement extends Component
{
    use WithPagination, WithFileUploads;

    public $name;
    public $email;
    public $password;
    public $biography;
    public $profilePhoto;
    public $existingPhoto;
    public $searchTerm = '';
    public $isEditing = false;
    public $editingAuthorId;

    protected $rules = [
        'name' => 'required|min:3',
        'email' => 'required|email|unique:users,email',
        'password' => 'required|min:8',
        'biography' => 'nullable|string',
        'profilePhoto' => 'nullable|image|max:1024', // 1MB Max
    ];

    public function create()
    {
        $this->validate();

        // Create user
        $user = User::create([
            'name' => $this->name,
            'email' => $this->email,
            'password' => Hash::make($this->password),
        ]);

        // Assign author role
        $authorRole = Role::where('name', 'author')->first();
        $user->roles()->attach($authorRole);

        // Handle profile photo
        $photoPath = null;
        if ($this->profilePhoto) {
            $photoPath = $this->profilePhoto->store('profile-photos', 'public');
        }

        // Create author record
        Author::create([
            'user_id' => $user->id,
            'biography' => $this->biography,
            'profile_photo_path' => $photoPath,
        ]);

        $this->resetForm();
        session()->flash('message', 'Author created successfully!');
    }

    public function edit($authorId)
    {
        $this->isEditing = true;
        $this->editingAuthorId = $authorId;

        $author = Author::with('user')->findOrFail($authorId);
        $this->name = $author->user->name;
        $this->email = $author->user->email;
        $this->password = '';
        $this->biography = $author->biography;
        $this->existingPhoto = $author->profile_photo_path;
    }

    public function update()
    {
        $author = Author::with('user')->findOrFail($this->editingAuthorId);

        $this->validate([
            'name' => 'required|min:3',
            'email' => 'required|email|unique:users,email,'.$author->user_id,
            'biography' => 'nullable|string',
            'profilePhoto' => 'nullable|image|max:1024',
        ]);

        // Update user
        $userData = [
            'name' => $this->name,
            'email' => $this->email,
        ];

        if ($this->password) {
            $userData['password'] = Hash::make($this->password);
        }

        $author->user->update($userData);

        // Handle profile photo
        $photoPath = $author->profile_photo_path;
        if ($this->profilePhoto) {
            // Delete old photo if exists
            if ($photoPath && Storage::disk('public')->exists($photoPath)) {
                Storage::disk('public')->delete($photoPath);
            }

            $photoPath = $this->profilePhoto->store('profile-photos', 'public');
        }

        // Update author
        $author->update([
            'biography' => $this->biography,
            'profile_photo_path' => $photoPath,
        ]);

        $this->resetForm();
        session()->flash('message', 'Author updated successfully!');
    }

    public function delete($authorId)
    {
        $author = Author::findOrFail($authorId);
        $userId = $author->user_id;

        // Check if author has books
        if ($author->books()->count() > 0) {
            session()->flash('error', 'Cannot delete author who has books. Please reassign or delete the books first.');
            return;
        }

        // Delete profile photo
        if ($author->profile_photo_path && Storage::disk('public')->exists($author->profile_photo_path)) {
            Storage::disk('public')->delete($author->profile_photo_path);
        }

        // Delete author record
        $author->delete();

        // Delete user
        User::destroy($userId);

        session()->flash('message', 'Author deleted successfully!');
    }

    public function resetForm()
    {
        $this->name = '';
        $this->email = '';
        $this->password = '';
        $this->biography = '';
        $this->profilePhoto = null;
        $this->existingPhoto = null;
        $this->isEditing = false;
        $this->editingAuthorId = null;
        $this->resetValidation();
    }

    public function render()
    {
        $authors = Author::whereHas('user', function($query) {
                $query->where('name', 'like', '%'.$this->searchTerm.'%')
                    ->orWhere('email', 'like', '%'.$this->searchTerm.'%');
            })
            ->orWhere('biography', 'like', '%'.$this->searchTerm.'%')
            ->with(['user', 'books'])
            ->paginate(10);

        return view('livewire.administrators.author-management', [
            'authors' => $authors
        ]);
    }
}
