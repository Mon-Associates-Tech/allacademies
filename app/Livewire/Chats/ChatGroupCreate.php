<?php

namespace App\Livewire\Chats;

use App\Models\User;
use App\Services\ChatService;
use Exception;
use Livewire\Component;
use Log;

class ChatGroupCreate extends Component
{
    public $name = '';
    public $description = '';
    public $type = 'custom';
    public $academic_level_id = null;
    public $academic_group_id = null;
    public $is_private = false;
    public $selectedUsers = [];
    public $userSearch = '';
    public $searchResults = [];
    public $academicLevels = [];
    public $academicGroups = [];

    public function mount()
    {
        $chatService = app(ChatService::class);
        $this->academicLevels = $chatService->getAcademicLevelOptions(auth()->user());
        $this->academicGroups = $chatService->getAcademicGroupOptions(auth()->user());
    }

    public function rules()
    {
        return [
            'name' => 'required|min:3|max:100',
            'description' => 'nullable|max:500',
            'type' => 'required|in:custom,academic_level,academic_group',
            'academic_level_id' => 'required_if:type,academic_level|exists:academic_levels,id',
            'academic_group_id' => 'required_if:type,academic_group|exists:academic_groups,id',
            'is_private' => 'boolean',
        ];
    }

    public function updatedUserSearch()
    {
        if (strlen($this->userSearch) >= 2) {
            $this->searchResults = app(ChatService::class)->findUsersByUsername(
                $this->userSearch,
                auth()->user(),
                10
            );
        } else {
            $this->searchResults = [];
        }
    }

    public function addUser($userId)
    {
        if (!in_array($userId, $this->selectedUsers)) {
            $this->selectedUsers[] = $userId;
        }
        $this->userSearch = '';
        $this->searchResults = [];
    }

    public function removeUser($userId)
    {
        $this->selectedUsers = array_filter($this->selectedUsers, fn($id) => $id != $userId);
        $this->selectedUsers = array_values($this->selectedUsers);
    }

    public function createGroup()
    {
       // $this->validate();

        try {
            $chatService = app(ChatService::class);

            $chatGroup = $chatService->createChatGroup([
                'name' => $this->name,
                'description' => $this->description,
                'type' => $this->type,
                'academic_level_id' => $this->academic_level_id,
                'academic_group_id' => $this->academic_group_id,
                'is_private' => $this->is_private,
            ], auth()->user());

// Add selected users to custom groups
            if ($this->type === 'custom' && !empty($this->selectedUsers)) {
                foreach ($this->selectedUsers as $userId) {
                    $user = User::find($userId);
                    if ($user && $user->school_id === auth()->user()->school_id) {
                        $chatGroup->addMember($user);
                    }
                }
            }

// Reset form
            $this->reset([
                'name', 'description', 'selectedUsers', 'userSearch', 'searchResults',
                'academic_level_id', 'academic_group_id'
            ]);
            $this->type = 'custom';
            $this->is_private = false;

// Dispatch success event
            $this->dispatch('chatGroupCreated', $chatGroup->id);

        } catch (Exception $e) {
            Log::error('Chat group creation failed', [
                'user_id' => auth()->id(),
                'error' => $e->getMessage(),
                'data' => [
                    'name' => $this->name,
                    'type' => $this->type
                ]
            ]);
            $this->addError('general', 'Error creating group: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.chats.chat-group-create');
    }
}
