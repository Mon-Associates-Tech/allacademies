<?php

namespace App\Http\Controllers;

use App\Models\ChatGroup;
use App\Services\ChatService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ChatController extends Controller
{
    public function __construct(
        private readonly ChatService $chatService
    ) {}

    public function getUserGroups(Request $request): JsonResponse
    {
        $groups = $this->chatService->getUserChatGroups($request->user());

        return response()->json([
            'groups' => $groups->map(function ($group) {
                return [
                    'id' => $group->id,
                    'name' => $group->name,
                    'type' => $group->type,
                    'unread_count' => $group->getUnreadCount(auth()->user()),
                    'last_message' => $group->getLastMessage()?->only(['message', 'created_at', 'user']),
                    'members_count' => $group->members_count,
                ];
            }),
        ]);
    }

    public function searchUsers(Request $request): JsonResponse
    {
        $request->validate([
            'query' => 'required|string|min:2',
        ]);

        $users = $this->chatService->findUsersByUsername(
            $request->query,
            $request->user()
        );

        return response()->json([
            'users' => $users->map(fn ($user) => $user->only(['id', 'name', 'email'])),
        ]);
    }

    public function markGroupAsRead(Request $request, ChatGroup $group): JsonResponse
    {
        if (! $group->isUserMember($request->user())) {
            return response()->json(['error' => 'Access denied'], 403);
        }

        $group->markAsRead($request->user());

        return response()->json(['success' => true]);
    }
}
