<?php

namespace App\Livewire\Chats;

use App\Models\ChatGroup;
use App\Models\ChatMessage;
use App\Services\ChatService;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithFileUploads;

class ChatInterface extends Component
{
    use WithFileUploads;

    public $selectedChatGroup = null;
    public $messages = [];
    public $newMessage = '';
    public $replyToMessage = null;
    public $attachments = [];
    public $showEmojiPicker = false;
    public $isTyping = false;
    public $chatGroups = [];
    public $showCreateGroup = false;
    public $searchTerm = '';

    protected $chatService;
    public ?bool $showMembersModal;
    public $showGroupInfoModal = false;
    public $showLeaveGroupModal = false;
    public $showDeleteGroupModal = false;

    public function boot(ChatService $chatService)
    {
        $this->chatService = $chatService;
    }

    public function mount()
    {
        $this->loadChatGroups();
    }

public function leaveGroup()
{
    if (!$this->selectedChatGroup) {
        return;
    }

    try {
        // Use the ChatService method to leave the group
        app(ChatService::class)->leaveGroup($this->selectedChatGroup, auth()->user());

        // Reset the selected group
        $this->selectedChatGroup = null;
        $this->messages = [];

        // Reload chat groups
        $this->loadChatGroups();

        // Close modal
        $this->showLeaveGroupModal = false;

        // Show success message
        session()->flash('message', 'You have left the group successfully.');
        $this->dispatch('close-modal', name: 'leave-group-confirmation');
    } catch (\Exception $e) {
        \Log::error('Error leaving group', [
            'group_id' => $this->selectedChatGroup->id,
            'user_id' => auth()->id(),
            'error' => $e->getMessage()
        ]);
        $this->addError('group', $e->getMessage());
    }
}

public function deleteGroup()
{
    if (!$this->selectedChatGroup) {
        return;
    }

    try {
        // Use the ChatService method to delete the group
        app(ChatService::class)->deleteGroup($this->selectedChatGroup, auth()->user());

        // Reset the selected group
        $this->selectedChatGroup = null;
        $this->messages = [];

        // Reload chat groups
        $this->loadChatGroups();

        // Close modal
        $this->showDeleteGroupModal = false;

        // Show success message
        session()->flash('message', 'Group has been deleted successfully.');
        $this->dispatch('close-modal', name: 'delete-group-confirmation');
    } catch (\Exception $e) {
        \Log::error('Error deleting group', [
            'group_id' => $this->selectedChatGroup->id,
            'user_id' => auth()->id(),
            'error' => $e->getMessage()
        ]);
        $this->addError('group', $e->getMessage());
    }
}


    public function loadChatGroups()
    {
        try {
            $this->chatGroups = app(ChatService::class)->getUserChatGroups(auth()->user());
        } catch (\Exception $e) {
            \Log::error('Error loading chat groups', ['error' => $e->getMessage(), 'user_id' => auth()->id()]);
            $this->chatGroups = collect();
            session()->flash('error', 'Unable to load chat groups.');
        }
    }

    public function selectChatGroup($groupId)
    {
        try {
            $group = ChatGroup::with(['members'])->find($groupId);

            if (!$group) {
                $this->addError('group', 'Chat group not found.');
                return;
            }

            if (!$group->isUserMember(auth()->user())) {
                $this->addError('access', 'You do not have access to this chat group.');
                return;
            }

            $this->selectedChatGroup = $group;
            $this->loadMessages();

            // Clear any previous errors
            $this->resetErrorBag();

            // Dispatch events for UI updates
            $this->dispatch('chatGroupSelected', $groupId);
            $this->dispatch('scrollToBottom');

        } catch (\Exception $e) {
            \Log::error('Error selecting chat group', ['group_id' => $groupId, 'error' => $e->getMessage()]);
            $this->addError('group', 'Unable to load chat group.');
        }
    }

    public function loadMessages($beforeMessageId = null)
    {
        if (!$this->selectedChatGroup) return;

        $chatGroup = ChatGroup::find($this->selectedChatGroup->id);

        if (!$chatGroup) return;

        try {
            $paginatedMessages = app(ChatService::class)->getGroupMessages(
                $chatGroup,
                auth()->user(),
                50,
                $beforeMessageId
            );

            if ($beforeMessageId) {
                $this->messages = array_merge($paginatedMessages->items(), $this->messages);
            } else {
                $this->messages = array_reverse($paginatedMessages->items());
            }
        } catch (\Exception $e) {
            \Log::error('Error loading messages', [
                'group_id' => $this->selectedChatGroup->id,
                'error' => $e->getMessage()
            ]);
            $this->addError('messages', 'Unable to load messages.');
        }
    }

    public function sendMessage(): void
    {
        $this->validate([
            'newMessage' => 'required_without:attachments|max:2000',
            'attachments.*' => 'file|max:10240' // 10MB max
        ]);

        if (!$this->selectedChatGroup) return;

        try {
            if (!empty($this->attachments)) {
                $message = $this->chatService->sendMessageWithAttachments(
                    $this->selectedChatGroup,
                    auth()->user(),
                    $this->newMessage,
                    $this->attachments,
                    $this->replyToMessage
                );
            } else {
                $message = $this->chatService->sendMessage(
                    $this->selectedChatGroup,
                    auth()->user(),
                    $this->newMessage,
                    $this->replyToMessage
                );
            }

            $this->messages[] = $message;
            $this->resetMessageForm();
            $this->loadChatGroups();

            // Clear any errors
            $this->resetErrorBag(['message']);
            $this->dispatch('scrollToBottom');
            $this->dispatch('messageSent', $message->id);

        } catch (\Exception $e) {
            \Log::error('Error sending message', [
                'group_id' => $this->selectedChatGroup->id,
                'user_id' => auth()->id(),
                'error' => $e->getMessage()
            ]);
            $this->addError('message', $e->getMessage());
        }
    }

    public function resetMessageForm()
    {
        $this->newMessage = '';
        $this->attachments = [];
        $this->replyToMessage = null;
    }

    public function setReplyTo($messageId): void
    {
        $this->replyToMessage = ChatMessage::find($messageId);
        $this->dispatch('focusMessageInput');
    }

    public function cancelReply(): void
    {
        $this->replyToMessage = null;
    }

    public function deleteMessage($messageId)
    {
        try {
            $message = ChatMessage::findOrFail($messageId);
            app(ChatService::class)->deleteMessage($message, auth()->user());

            // Update the message in the messages array
            foreach ($this->messages as $key => $msg) {
                if ($msg->id === $messageId) {
                    $this->messages[$key] = $message->fresh(['user', 'attachments']);
                    break;
                }
            }
        } catch (\Exception $e) {
            \Log::error('Error deleting message', [
                'message_id' => $messageId,
                'user_id' => auth()->id(),
                'error' => $e->getMessage()
            ]);
            $this->addError('message', 'Unable to delete message.');
        }
    }

    public function loadOlderMessages()
    {
        if (empty($this->messages)) return;

        $oldestMessage = collect($this->messages)->first();
        $this->loadMessages($oldestMessage->id);
    }


    public function onMessageReceived($data)
    {
        if ($data['chat_group_id'] == $this->selectedChatGroup?->id) {
            $message = ChatMessage::with(['user', 'attachments', 'replyTo.user'])
                ->find($data['message_id']);

            if ($message && !collect($this->messages)->contains('id', $message->id)) {
                $this->messages[] = $message;
                $this->dispatch('scrollToBottom');
            }
        }
    }


    public function onUserTyping($data)
    {
        if ($data['user_id'] !== auth()->id()) {
            $this->dispatch('showTypingIndicator', $data);
        }
    }

    public function removeAttachment($index)
    {
        if (isset($this->attachments[$index])) {
            unset($this->attachments[$index]);
            $this->attachments = array_values($this->attachments);
        }
    }
    protected function getListeners()
    {
        $listeners = [];

        if ($this->selectedChatGroup) {
            $listeners["echo:chat-group.{$this->selectedChatGroup->id},MessageSent"] = 'onMessageReceived';
            $listeners["echo:chat-group.{$this->selectedChatGroup->id},UserTyping"] = 'onUserTyping';
        }

        return $listeners;
    }

    public function updatedNewMessage()
    {
        if ($this->selectedChatGroup && !empty(trim($this->newMessage))) {
            $this->dispatch('userTyping', [
                'chat_group_id' => $this->selectedChatGroup->id,
                'user_id' => auth()->id(),
                'user_name' => auth()->user()->name
            ]);
        }
    }


    #[On('chatGroupCreated')]
    public function onChatGroupCreated($groupId)
    {
        $this->loadChatGroups();
        $this->selectChatGroup($groupId);
        $this->showCreateGroup = false;
    }

    #[On('closeCreateModal')]
    public function closeCreateModal()
    {
        $this->showCreateGroup = false;
    }

    #[On('memberAdded')]
    public function onMemberAdded()
    {
        // Refresh the selected chat group to get updated members
        if ($this->selectedChatGroup) {
            $this->selectedChatGroup = $this->selectedChatGroup->fresh(['members']);
        }
    }

    #[On('memberRemoved')]
    public function onMemberRemoved(): void
    {
        // Refresh the selected chat group to get updated members
        if ($this->selectedChatGroup) {
            $this->selectedChatGroup = $this->selectedChatGroup->fresh(['members']);
        }
    }
    public function showMembersModal(): void
    {
        $this->showMembersModal = true;
    }

    public function hideMembersModal(): void
    {
        $this->showMembersModal = false;
    }

    public function render()
    {
        return view('livewire.chats.chat-interface');
    }
}
