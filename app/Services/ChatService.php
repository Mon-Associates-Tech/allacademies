<?php

namespace App\Services;

use App\Models\ChatGroup;
use App\Models\ChatMessage;
use App\Models\User;
use App\Models\AcademicLevel;
use App\Models\AcademicGroup;
use Illuminate\Http\UploadedFile;

class ChatService
{
    public function createChatGroup(array $data, User $creator): ChatGroup
    {
        $chatGroup = ChatGroup::create([
            'name' => $data['name'],
            'description' => $data['description'] ?? null,
            'type' => $data['type'],
            'school_id' => $creator->school_id,
            'created_by' => $creator->id,
            'academic_level_id' => $data['academic_level_id'] ?? null,
            'academic_group_id' => $data['academic_group_id'] ?? null,
            'is_private' => $data['is_private'] ?? false,
            'settings' => $data['settings'] ?? []
        ]);

        // Add creator as admin
        $chatGroup->addMember($creator, 'admin', true);

        // Auto-populate members for academic groups
        if (in_array($chatGroup->type, ['academic_level', 'academic_group'])) {
            $chatGroup->populateAcademicMembers();
        }

        return $chatGroup;
    }

    public function createDirectChat(User $user1, User $user2): ChatGroup
    {
        // Check if direct chat already exists
        $existingChat = ChatGroup::where('type', 'direct')
            ->where('school_id', $user1->school_id)
            ->whereHas('members', function ($query) use ($user1) {
                $query->where('user_id', $user1->id);
            })
            ->whereHas('members', function ($query) use ($user2) {
                $query->where('user_id', $user2->id);
            })
            ->first();

        if ($existingChat) {
            return $existingChat;
        }

        $chatGroup = ChatGroup::create([
            'name' => $user1->name . ' & ' . $user2->name,
            'type' => 'direct',
            'school_id' => $user1->school_id,
            'created_by' => $user1->id,
            'is_private' => true
        ]);

        $chatGroup->addMember($user1, 'admin', true);
        $chatGroup->addMember($user2, 'admin', true);

        return $chatGroup;
    }

    public function sendMessage(ChatGroup $chatGroup, User $user, string $message, ?ChatMessage $replyTo = null): ChatMessage
    {
        if (!$chatGroup->isUserMember($user)) {
            throw new \Exception('User is not a member of this chat group');
        }

        $chatMessage = ChatMessage::create([
            'chat_group_id' => $chatGroup->id,
            'user_id' => $user->id,
            'message' => $message,
            'message_type' => 'text',
            'reply_to_message_id' => $replyTo?->id
        ]);

        // Mark as read by sender
        $chatMessage->markAsRead($user);

        return $chatMessage;
    }

    public function sendMessageWithAttachments(ChatGroup $chatGroup, User $user, ?string $message, array $files, ?ChatMessage $replyTo = null): ChatMessage
    {
        if (!$chatGroup->isUserMember($user)) {
            throw new \Exception('User is not a member of this chat group');
        }

        $hasText = !empty(trim($message ?? ''));
        $messageType = empty($files) ? 'text' : (count($files) === 1 && $this->isImage($files[0]) ? 'image' : 'file');

        $chatMessage = ChatMessage::create([
            'chat_group_id' => $chatGroup->id,
            'user_id' => $user->id,
            'message' => $hasText ? $message : null,
            'message_type' => $messageType,
            'reply_to_message_id' => $replyTo?->id
        ]);

        // Handle file uploads
        foreach ($files as $file) {
            $this->attachFileToMessage($chatMessage, $file);
        }

        // Mark as read by sender
        $chatMessage->markAsRead($user);

        return $chatMessage;
    }

    private function attachFileToMessage(ChatMessage $message, UploadedFile $file): void
    {
        $fileName = $file->getClientOriginalName();
        $filePath = $file->store('chat-attachments/' . date('Y/m'), 'public');

        $message->attachments()->create([
            'file_name' => $fileName,
            'file_path' => $filePath,
            'file_type' => $file->getClientOriginalExtension(),
            'file_size' => $file->getSize(),
            'mime_type' => $file->getMimeType()
        ]);
    }

    private function isImage(UploadedFile $file): bool
    {
        return str_starts_with($file->getMimeType(), 'image/');
    }

    public function addUserToGroup(ChatGroup $chatGroup, User $userToAdd, User $addedBy, string $role = 'member'): void
    {
        if (!$chatGroup->canUserAddMembers($addedBy)) {
            throw new \Exception('You do not have permission to add members to this group');
        }

        if ($chatGroup->isUserMember($userToAdd)) {
            throw new \Exception('User is already a member of this group');
        }

        $chatGroup->addMember($userToAdd, $role);

        // Send system message
        ChatMessage::create([
            'chat_group_id' => $chatGroup->id,
            'user_id' => $addedBy->id,
            'message' => "{$userToAdd->name} was added to the group by {$addedBy->name}",
            'message_type' => 'system'
        ]);
    }

    public function removeUserFromGroup(ChatGroup $chatGroup, User $userToRemove, User $removedBy): void
    {
        $membership = $chatGroup->allMembers()->where('user_id', $removedBy->id)->first();

        if (!$membership || !in_array($membership->pivot->role, ['admin', 'moderator']) || $removedBy->id !== $chatGroup->created_by) {
            throw new \Exception('You do not have permission to remove members from this group');
        }

        $chatGroup->removeMember($userToRemove);

        // Send system message
        ChatMessage::create([
            'chat_group_id' => $chatGroup->id,
            'user_id' => $removedBy->id,
            'message' => "{$userToRemove->name} was removed from the group by {$removedBy->name}",
            'message_type' => 'system'
        ]);
    }

    public function findUsersByUsername(string $username, User $currentUser, int $limit = 10): \Illuminate\Database\Eloquent\Collection
    {
        return User::where('school_id', $currentUser->school_id)
            ->where('id', '!=', $currentUser->id)
            ->where(function ($query) use ($username) {
                $query->where('name', 'like', "%{$username}%")
                    ->orWhere('email', 'like', "%{$username}%");
            })
            ->where('users.is_active', true)
            ->limit($limit)
            ->get(['id', 'name', 'email', 'avatar']);
    }

    public function getUserChatGroups(User $user): \Illuminate\Database\Eloquent\Collection
    {
        return ChatGroup::forUser($user)
            ->active()
            ->with(['lastMessage', 'members' => function ($query) {
                $query->limit(3);
            }])
            ->withCount('members')
            ->orderBy('updated_at', 'desc')
            ->get();
    }

    public function getGroupMessages(ChatGroup $chatGroup, User $user, int $perPage = 50, ?int $beforeMessageId = null): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        if (!$chatGroup->isUserMember($user)) {
            throw new \Exception('You do not have access to this chat group');
        }

        $query = $chatGroup->messages()
            ->with(['user', 'attachments', 'replyTo.user'])
            ->latest();

        if ($beforeMessageId) {
            $query->where('id', '<', $beforeMessageId);
        }

        $messages = $query->paginate($perPage);

        // Mark messages as read
        foreach ($messages as $message) {
            if (!$message->isReadBy($user)) {
                $message->markAsRead($user);
            }
        }

        // Update user's last read time
        $chatGroup->markAsRead($user);

        return $messages;
    }

    public function editMessage(ChatMessage $message, User $user, string $newContent): ChatMessage
    {
        if (!$message->canBeEditedBy($user)) {
            throw new \Exception('You cannot edit this message');
        }

        $message->update([
            'message' => $newContent,
            'is_edited' => true,
            'edited_at' => now()
        ]);

        return $message;
    }

    public function deleteMessage(ChatMessage $message, User $user): void
    {
        if (!$message->canBeDeletedBy($user)) {
            throw new \Exception('You cannot delete this message');
        }

        $message->softDelete();
    }

    public function getAcademicLevelOptions(User $user): \Illuminate\Database\Eloquent\Collection
    {
        if ($user->canAccessCrossSchool()) {
            $schoolId = app()->has('current_school') ? app('current_school')->id : $user->school_id;
        } else {
            $schoolId = $user->school_id;
        }

        return AcademicLevel::forSchool($schoolId)
            ->with('academicGroup')
            ->orderBy('name')
            ->get();
    }

    public function getAcademicGroupOptions(User $user): \Illuminate\Database\Eloquent\Collection
    {
        if ($user->canAccessCrossSchool()) {
            $schoolId = app()->has('current_school') ? app('current_school')->id : $user->school_id;
        } else {
            $schoolId = $user->school_id;
        }

        return AcademicGroup::forSchool($schoolId)
            ->orderBy('name')
            ->get();
    }
}
