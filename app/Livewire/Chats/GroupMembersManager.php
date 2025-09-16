<?php

namespace App\Livewire\Chats;

use App\Models\ChatGroup;
use App\Services\ChatService;
use Livewire\Component;

class GroupMembersManager extends Component
{
    public ChatGroup $chatGroup;
    public $members = [];
    public $userSearch = '';
    public $searchResults = [];
    public $showAddMember = false;

    protected $chatService;

    public function boot(ChatService $chatService)
    {
        $this->chatService = $chatService;
    }

    public function mount()
    {
        $this->loadMembers();
    }

    public function loadMembers()
    {
        $this->members = $this->chatGroup->members()
            ->with('roles')
            ->get();
    }

    public function updatedUserSearch()
    {
        if (strlen($this->userSearch) >= 2) {
            $this->searchResults = $this->chatService->findUsersByUsername(
                $this->userSearch,
                auth()->user(),
                10
            )->reject(function ($user) {
                return $this->chatGroup->isUserMember($user);
            });
        } else {
            $this->searchResults = [];
        }
    }

    public function addMember($userId)
    {
        try {
            $user = \App\Models\User::findOrFail($userId);
            $this->chatService->addUserToGroup(
                $this->chatGroup,
                $user,
                auth()->user()
            );

            $this->loadMembers();
            $this->userSearch = '';
            $this->searchResults = [];
            $this->showAddMember = false;

            session()->flash('success', 'Member added successfully!');

        } catch (\Exception $e) {
            $this->addError('member', $e->getMessage());
        }
    }

    public function removeMember($userId)
    {
        try {
            $user = \App\Models\User::findOrFail($userId);
            $this->chatService->removeUserFromGroup(
                $this->chatGroup,
                $user,
                auth()->user()
            );

            $this->loadMembers();
            session()->flash('success', 'Member removed successfully!');

        } catch (\Exception $e) {
            $this->addError('member', $e->getMessage());
        }
    }

    public function canManageMembers()
    {
        return $this->chatGroup->canUserAddMembers(auth()->user());
    }

    public function render()
    {
        return view('livewire.chats.group-members-manager');
    }
}
