<div class="flex h-screen bg-gray-50" x-data="chatInterface()">
    <!-- Chat Groups Sidebar -->
    <div class="w-80 bg-white border-r border-gray-200 flex flex-col">
        <!-- Header -->
        <div class="p-4 border-b border-gray-200">
            <div class="flex items-center justify-between mb-3">
                <h2 class="text-lg font-semibold text-gray-900">Messages</h2>
                <button
                    @click="$dispatch('open-modal', { name: 'create-chat-group' })"
                    class="p-2 text-blue-600 hover:bg-blue-50 rounded-full transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                </button>

            </div>

            <!-- Search -->
            <div class="relative">
                <input
                    type="text"
                    wire:model.live.debounce.300ms="searchTerm"
                    placeholder="Search conversations..."
                    class="w-full px-3 py-2 pl-10 text-sm border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                <svg class="w-4 h-4 text-gray-400 absolute left-3 top-3" fill="none" stroke="currentColor"
                     viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
            </div>
        </div>

        <!-- Chat Groups List -->
        <div class="flex-1 overflow-y-auto">
            @forelse($chatGroups as $group)
                <div
                    wire:click="selectChatGroup({{ $group->id }})"
                    class="p-4 border-b border-gray-100 cursor-pointer hover:bg-gray-50 transition-colors
                           {{ $selectedChatGroup && $selectedChatGroup->id === $group->id ? 'bg-blue-50 border-l-4 border-l-blue-500' : '' }}">

                    <div class="flex items-start space-x-3">
                        <!-- Group Avatar -->
                        <div class="flex-shrink-0">
                            @if($group->type === 'direct')
                                <div
                                    class="w-10 h-10 bg-gradient-to-br from-blue-400 to-blue-600 rounded-full flex items-center justify-center">
                                    <span class="text-white font-medium text-sm">{{ substr($group->name, 0, 1) }}</span>
                                </div>
                            @else
                                <div
                                    class="w-10 h-10 bg-gradient-to-br from-green-400 to-green-600 rounded-full flex items-center justify-center">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor"
                                         viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                    </svg>
                                </div>
                            @endif
                        </div>

                        <!-- Group Info -->
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center justify-between">
                                <h3 class="text-sm font-medium text-gray-900 truncate">{{ $group->name }}</h3>
                                @if($group->getUnreadCount(auth()->user()) > 0)
                                    <span
                                        class="bg-blue-600 text-white text-xs rounded-full px-2 py-1 min-w-[20px] text-center">
                                        {{ $group->getUnreadCount(auth()->user()) }}
                                    </span>
                                @endif
                            </div>

                            @if($group->getLastMessage())
                                <p class="text-xs text-gray-500 mt-1 truncate">
                                    <span class="font-medium">{{ $group->getLastMessage()->user->name }}:</span>
                                    {{ $group->getLastMessage()->message ?? 'Sent an attachment' }}
                                </p>
                            @endif

                            <div class="flex items-center mt-1 text-xs text-gray-400">
                                <span>{{ $group->members_count ?? $group->members()->count() }} member{{ ($group->members_count ?? $group->members()->count()) !== 1 ? 's' : '' }}</span>
                                @if($group->getLastMessage())
                                    <span class="mx-1">•</span>
                                    <span>{{ $group->getLastMessage()->created_at->diffForHumans() }}</span>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="p-8 text-center text-gray-500">
                    <svg class="w-16 h-16 mx-auto mb-4 text-gray-300" fill="none" stroke="currentColor"
                         viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                    </svg>
                    <p class="font-medium">No conversations yet</p>
                    <p class="text-sm mt-1">Start a new conversation to get started</p>
                </div>
            @endforelse
        </div>
    </div>

    <!-- Chat Area -->
    <div class="flex-1 flex flex-col">
        @if($selectedChatGroup)
            <!-- Chat Header -->
            <div class="p-4 bg-white border-b border-gray-200">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <div
                            class="w-10 h-10 bg-gradient-to-br from-green-400 to-green-600 rounded-full flex items-center justify-center">
                            <span
                                class="text-white font-medium text-sm">{{ substr($selectedChatGroup->name, 0, 1) }}</span>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-900">{{ $selectedChatGroup->name }}</h3>
                            <p class="text-sm text-gray-500">
                                {{ $selectedChatGroup->members()->count() }}
                                member{{ $selectedChatGroup->members()->count() !== 1 ? 's' : '' }}
                                @if($selectedChatGroup->type !== 'direct')
                                    • {{ ucfirst(str_replace('_', ' ', $selectedChatGroup->type)) }}
                                @endif
                            </p>
                        </div>
                    </div>

                    <!-- Group Actions -->
                    <div class="flex items-center space-x-2">
                        @if($selectedChatGroup->canUserAddMembers(auth()->user()))
                            <button
                                x-on:click="showMembersModal = true"
                                class="p-2 text-gray-600 hover:bg-gray-100 rounded-full transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z"></path>
                                </svg>
                            </button>
                        @endif


                        <div class="relative" x-data="{ open: false }" @click.outside="open = false">
                            <button
                                @click="open = !open"
                                class="p-2 text-gray-600 hover:bg-gray-100 rounded-full transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"></path>
                                </svg>
                            </button>

                            <div
                                x-show="open"
                                x-transition
                                class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg py-1 z-10">
                                <button
                                    @click="$dispatch('open-modal', { name: 'group-info' }); open = false"
                                    class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    Group Info
                                </button>
                                <button
                                    @click="open = false"
                                    class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                    Mute Notifications
                                </button>
                                @if($selectedChatGroup && $selectedChatGroup->created_by === auth()->id())
                                    <button
                                        @click="$dispatch('open-modal', { name: 'delete-group-confirmation' }); open = false"
                                        class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                                        Delete Group
                                    </button>
                                @else
                                    <button
                                        @click="$dispatch('open-modal', { name: 'leave-group-confirmation' }); open = false"
                                        class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                                        Leave Group
                                    </button>
                                @endif
                            </div>
                        </div>

                    </div>
                </div>
            </div>

            <!-- Messages Area -->
            <div class="flex-1 overflow-y-auto p-4 space-y-4"
                 x-ref="messagesContainer"
                 @scroll="handleScroll">

                <!-- Load More Button -->
                @if(count($messages) >= 50)
                    <div class="text-center">
                        <button
                            wire:click="loadOlderMessages"
                            class="text-sm text-blue-600 hover:text-blue-800 font-medium">
                            Load older messages
                        </button>
                    </div>
                @endif

                <!-- Messages -->
                @foreach($messages as $message)
                    <div class="flex {{ $message->user_id === auth()->id() ? 'justify-end' : 'justify-start' }}">
                        <div
                            class="flex max-w-xs lg:max-w-md {{ $message->user_id === auth()->id() ? 'flex-row-reverse' : 'flex-row' }} items-end space-x-2">
                            <!-- Avatar -->
                            @if($message->user_id !== auth()->id())
                                <div
                                    class="w-8 h-8 rounded-full bg-gradient-to-br from-blue-400 to-blue-600 flex items-center justify-center flex-shrink-0">
                                    <span
                                        class="text-white text-xs font-medium">{{ substr($message->user->name, 0, 1) }}</span>
                                </div>
                            @endif

                            <!-- Message Bubble -->
                            <div class="relative group">
                                @if($message->reply_to_message_id && $message->replyTo)
                                    <div class="mb-2 p-2 bg-gray-100 rounded-lg text-xs">
                                        <p class="font-medium text-gray-700">{{ $message->replyTo->user->name }}</p>
                                        <p class="text-gray-600">{{ Str::limit($message->replyTo->message ?? 'Attachment', 50) }}</p>
                                    </div>
                                @endif

                                <div
                                    class="px-4 py-2 rounded-2xl {{ $message->user_id === auth()->id() ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-900' }}">
                                    @if($message->message_type === 'system')
                                        <p class="text-sm italic">{{ $message->message }}</p>
                                    @elseif($message->is_deleted)
                                        <p class="text-sm italic opacity-75">This message was deleted</p>
                                    @else
                                        @if($message->message)
                                            <p class="text-sm whitespace-pre-wrap">{{ $message->message }}</p>
                                        @endif

                                        @if($message->attachments && $message->attachments->count() > 0)
                                            <div class="mt-2 space-y-2">
                                                @foreach($message->attachments as $attachment)
                                                    @if($attachment->isImage())
                                                        <img
                                                            src="{{ $attachment->getUrl() }}"
                                                            alt="{{ $attachment->file_name }}"
                                                            class="max-w-full rounded-lg cursor-pointer hover:opacity-90 max-h-60 object-cover"
                                                            @click="showImageModal('{{ $attachment->getUrl() }}', '{{ $attachment->file_name }}')">
                                                    @else
                                                        <div
                                                            class="flex items-center space-x-2 p-2 bg-black bg-opacity-10 rounded-lg">
                                                            <svg class="w-6 h-6" fill="none" stroke="currentColor"
                                                                 viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                      stroke-width="2"
                                                                      d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                                            </svg>
                                                            <div class="flex-1 min-w-0">
                                                                <p class="text-sm font-medium truncate">{{ $attachment->file_name }}</p>
                                                                <p class="text-xs opacity-75">{{ $attachment->getFormattedSize() }}</p>
                                                            </div>
                                                            <a
                                                                href="{{ $attachment->getUrl() }}"
                                                                download="{{ $attachment->file_name }}"
                                                                class="text-sm underline hover:no-underline flex-shrink-0">
                                                                Download
                                                            </a>
                                                        </div>
                                                    @endif
                                                @endforeach
                                            </div>
                                        @endif
                                    @endif

                                    <div class="flex items-center justify-between mt-1 text-xs opacity-75">
                                        <span>{{ $message->created_at->format('g:i A') }}</span>
                                        @if($message->is_edited)
                                            <span class="ml-2">edited</span>
                                        @endif
                                    </div>
                                </div>

                                <!-- Message Actions -->
                                @if(!$message->is_deleted && $message->message_type !== 'system')
                                    <div
                                        class="absolute top-0 {{ $message->user_id === auth()->id() ? 'left-0 transform -translate-x-full' : 'right-0 transform translate-x-full' }} -translate-y-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                        <div
                                            class="flex items-center space-x-1 bg-white border border-gray-200 rounded-lg shadow-lg p-1">
                                            <button
                                                wire:click="setReplyTo({{ $message->id }})"
                                                class="p-1 text-gray-600 hover:bg-gray-100 rounded"
                                                title="Reply">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                     viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                          stroke-width="2"
                                                          d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"></path>
                                                </svg>
                                            </button>

                                            @if($message->canBeDeletedBy(auth()->user()))
                                                <button
                                                    wire:click="deleteMessage({{ $message->id }})"
                                                    onclick="return confirm('Are you sure you want to delete this message?')"
                                                    class="p-1 text-red-600 hover:bg-red-50 rounded"
                                                    title="Delete">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                         viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                              stroke-width="2"
                                                              d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                    </svg>
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                @endif

                                <!-- User name for received messages -->
                                @if($message->user_id !== auth()->id() && $message->message_type !== 'system')
                                    <div class="text-xs text-gray-500 mt-1">
                                        {{ $message->user->name }}
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach

                <!-- Typing Indicator -->
                <div x-show="showTypingIndicator" x-transition class="flex justify-start">
                    <div class="flex items-center space-x-2 bg-gray-200 rounded-full px-4 py-2">
                        <div class="flex space-x-1">
                            <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce"></div>
                            <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce"
                                 style="animation-delay: 0.1s"></div>
                            <div class="w-2 h-2 bg-gray-400 rounded-full animate-bounce"
                                 style="animation-delay: 0.2s"></div>
                        </div>
                        <span class="text-xs text-gray-600" x-text="typingUser + ' is typing...'"></span>
                    </div>
                </div>
            </div>

            <!-- Message Input -->
            <div class="p-4 bg-white border-t border-gray-200">
                <!-- Reply Preview -->
                @if($replyToMessage)
                    <div
                        class="flex items-center justify-between mb-3 p-3 bg-gray-50 rounded-lg border-l-4 border-l-blue-500">
                        <div class="flex-1 min-w-0">
                            <p class="text-xs font-medium text-gray-700">Replying
                                to {{ $replyToMessage->user->name }}</p>
                            <p class="text-sm text-gray-600 truncate">{{ Str::limit($replyToMessage->message ?? 'Attachment', 60) }}</p>
                        </div>
                        <button
                            wire:click="cancelReply"
                            class="p-1 text-gray-500 hover:text-gray-700 flex-shrink-0 ml-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                @endif

                <!-- File Previews -->
                @if(!empty($attachments))
                    <div class="mb-3 flex flex-wrap gap-2">
                        @foreach($attachments as $index => $attachment)
                            <div class="relative bg-gray-100 rounded-lg p-3 flex items-center space-x-2 max-w-xs">
                                <svg class="w-5 h-5 text-gray-600 flex-shrink-0" fill="none" stroke="currentColor"
                                     viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path>
                                </svg>
                                <span
                                    class="text-sm text-gray-700 truncate flex-1">{{ $attachment->getClientOriginalName() }}</span>
                                <button
                                    type="button"
                                    wire:click="removeAttachment({{ $index }})"
                                    class="text-red-500 hover:text-red-700 flex-shrink-0">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                            </div>
                        @endforeach
                    </div>
                @endif

                <!-- Error Messages -->
                @if ($errors->any())
                    <div class="mb-3 p-3 bg-red-50 border border-red-200 rounded-lg">
                        @foreach ($errors->all() as $error)
                            <p class="text-sm text-red-600">{{ $error }}</p>
                        @endforeach
                    </div>
                @endif

                <!-- Input Form -->
                <form wire:submit.prevent="sendMessage" class="flex items-end space-x-3">
                    <!-- File Upload -->
                    <div class="flex-shrink-0">
                        <label for="file-upload"
                               class="cursor-pointer p-2 text-gray-500 hover:text-gray-700 hover:bg-gray-100 rounded-full transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path>
                            </svg>
                        </label>
                        <input
                            id="file-upload"
                            type="file"
                            wire:model="attachments"
                            multiple
                            accept="image/*,.pdf,.doc,.docx,.txt,.xls,.xlsx"
                            class="hidden">
                    </div>

                    <!-- Message Input -->
                    <div class="flex-1">
                        <textarea
                            wire:model="newMessage"
                            x-ref="messageInput"
                            placeholder="Type a message..."
                            rows="1"
                            class="w-full px-4 py-2 border border-gray-300 rounded-2xl resize-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                            @keydown.enter.exact="if(!$event.shiftKey) { $event.preventDefault(); $wire.sendMessage(); }"
                            @input="autoResize($event.target)"
                            style="min-height: 40px; max-height: 120px;"></textarea>
                    </div>

                    <!-- Send Button -->
                    <div class="flex-shrink-0">
                        <button
                            type="submit"
                            class="p-2 bg-blue-600 text-white rounded-full hover:bg-blue-700 disabled:bg-gray-300 disabled:cursor-not-allowed transition-colors"
                            :disabled="!($wire.newMessage && $wire.newMessage.trim()) && (!$wire.attachments || $wire.attachments.length === 0)">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                            </svg>
                        </button>
                    </div>
                </form>

                <p class="text-xs text-gray-500 mt-2">
                    Press Enter to send, Shift + Enter for new line
                </p>
            </div>
        @else
            <!-- Welcome Screen -->
            <div class="flex-1 flex items-center justify-center bg-gray-50">
                <div class="text-center max-w-md">
                    <svg class="w-24 h-24 mx-auto mb-6 text-gray-300" fill="none" stroke="currentColor"
                         viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                    </svg>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">Welcome to School Chat</h3>
                    <p class="text-gray-600 mb-6">Select a conversation to start messaging or create a new group to get
                        started.</p>
                    <button
                        @click="$dispatch('open-modal', { name: 'create-chat-group' })"
                        class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition-colors">
                        Start New Conversation
                    </button>
                </div>
            </div>
        @endif
    </div>

    <!-- Create Group Modal -->
    <x-modal-component
        name="create-chat-group"
        title="Create New Chat Group"
        size="md"
        :show="$showCreateGroup"
        @close-modal.window="showCreateGroup = false"
    >
        <div class="py-4">
            <livewire:chats.chat-group-create wire:key="create-group-{{ now() }}"/>
        </div>

        <x-slot name="actions">
            <x-button.white
                type="button"
                @click="$dispatch('close-modal', {name: 'create-chat-group'})"
                class="">
                Cancel
            </x-button.white>

            <x-button.primary type="submit" form="create-chat-group-form" class="">Create Group</x-button.primary>
        </x-slot>
    </x-modal-component>

    <!-- Group Info Modal -->
    <x-modal-component
        name="group-info"
        title="Group Information"
        size="md"
        :show="$showGroupInfoModal"
        @close-modal.window="showGroupInfoModal = false"
    >
        <div class="py-4">
            @if($selectedChatGroup)
                <div class="space-y-4">
                    <div class="flex items-center space-x-4">
                        <div
                            class="w-12 h-12 bg-gradient-to-br from-green-400 to-green-600 rounded-full flex items-center justify-center">
                            <span class="text-white font-medium">{{ substr($selectedChatGroup->name, 0, 1) }}</span>
                        </div>
                        <div>
                            <h3 class="font-semibold text-gray-900">{{ $selectedChatGroup->name }}</h3>
                            <p class="text-sm text-gray-500">
                                {{ $selectedChatGroup->members()->count() }} members
                            </p>
                        </div>
                    </div>

                    <div>
                        <p class="text-sm text-gray-600">
                            {{ $selectedChatGroup->description ?? 'No description provided.' }}
                        </p>
                    </div>

                    <div>
                        <h4 class="font-medium text-gray-900 mb-2">Members</h4>
                        <div class="space-y-2 max-h-40 overflow-y-auto">
                            @foreach($selectedChatGroup->members as $member)
                                <div class="flex items-center space-x-3">
                                    <div
                                        class="w-8 h-8 rounded-full bg-gradient-to-br from-blue-400 to-blue-600 flex items-center justify-center">
                                        <span class="text-white text-xs">{{ substr($member->name, 0, 1) }}</span>
                                    </div>
                                    <div>
                                        <p class="text-sm font-medium">{{ $member->name }}</p>
                                        <p class="text-xs text-gray-500">{{ $member->email }}</p>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <x-slot name="actions">
            <x-button.white
                type="button"
                @click="$dispatch('close-modal', {name: 'group-info'})"
                class="px-4 py-2 text-gray-600 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                Close
            </x-button.white>
        </x-slot>
    </x-modal-component>

    <!-- Leave Group Confirmation Modal -->
    <x-modal-component
        name="leave-group-confirmation"
        title="Leave Group"
        size="md"
        :show="$showLeaveGroupModal"
        @close-modal.window="showLeaveGroupModal = false"
    >
        <div class="py-4">
            @if($selectedChatGroup)
                <p class="text-gray-600">
                    Are you sure you want to leave the group <strong>"{{ $selectedChatGroup->name }}"</strong>?
                    You will no longer have access to this group's messages.
                </p>
            @endif
        </div>

        <x-slot name="actions">
            <button
                type="button"
                @click="$dispatch('close-modal', { name: 'leave-group-confirmation' })"
                class="px-4 py-2 text-gray-600 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                Cancel
            </button>
            <button
                type="button"
                wire:click="leaveGroup"
                class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                Leave Group
            </button>
        </x-slot>
    </x-modal-component>

    <!-- Delete Group Confirmation Modal -->
    <x-modal-component
        name="delete-group-confirmation"
        title="Delete Group"
        size="md"
        :show="$showDeleteGroupModal"
        @close-modal.window="showDeleteGroupModal = false"
    >
        <div class="py-4">
            @if($selectedChatGroup)
                <p class="text-gray-600">
                    Are you sure you want to delete the group <strong>"{{ $selectedChatGroup->name }}"</strong>?
                    This action cannot be undone. All messages and files in this group will be permanently deleted.
                </p>
            @endif
        </div>

        <x-slot name="actions">
            <button
                type="button"
                @click="$dispatch('close-modal', { name: 'delete-group-confirmation' })"
                class="px-4 py-2 text-gray-600 border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                Cancel
            </button>
            <button
                type="button"
                wire:click="deleteGroup"
                class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                Delete Group
            </button>
        </x-slot>
    </x-modal-component>


    <!-- Members Modal -->
    @if($selectedChatGroup)
        <div x-show="showMembersModal" x-data="{ showMembersModal: false }"
             class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div
                x-show="showMembersModal"
                x-transition.opacity
                @click.away="showMembersModal = false"
                class="bg-white rounded-lg w-full max-w-lg mx-4 max-h-[90vh] overflow-y-auto">
                <div class="p-6">
                    <livewire:chats.group-members-manager :chat-group="$selectedChatGroup"
                                                          wire:key="members-{{ $selectedChatGroup->id }}"/>
                </div>
            </div>
        </div>
    @endif
</div>

<script>
    function chatInterface() {
        return {
            showTypingIndicator: false,
            typingUser: '',
            showMembersModal: false,
            currentChannel: null, // For Echo channel management

            init() {
                // Auto-scroll to bottom when component loads
                this.$nextTick(() => {
                    this.scrollToBottom();
                });

                // Listen for scroll to bottom event
                this.$wire.on('scrollToBottom', () => {
                    this.$nextTick(() => this.scrollToBottom());
                });

                // Listen for focus message input event
                this.$wire.on('focusMessageInput', () => {
                    this.$nextTick(() => this.$refs.messageInput?.focus());
                });

                // Listen for typing indicator
                this.$wire.on('showTypingIndicator', (data) => {
                    this.showTypingIndicator = true;
                    this.typingUser = data.user_name;

                    setTimeout(() => {
                        this.showTypingIndicator = false;
                    }, 3000);
                });

                // Listen for chat group created event
                this.$wire.on('chatGroupCreated', (groupId) => {
                    this.$wire.call('selectChatGroup', groupId);
                    this.$wire.set('showCreateGroup', false);
                });
            },

            scrollToBottom() {
                if (this.$refs.messagesContainer) {
                    this.$refs.messagesContainer.scrollTop = this.$refs.messagesContainer.scrollHeight;
                }
            },

            handleScroll(event) {
                const container = event.target;
                if (container.scrollTop === 0) {
                    this.$wire.call('loadOlderMessages');
                }
            },

            autoResize(textarea) {
                textarea.style.height = 'auto';
                textarea.style.height = Math.min(textarea.scrollHeight, 120) + 'px';
            },

            showImageModal(url, name) {
                // You can implement a lightbox modal here
                // For now, just open in new tab
                window.open(url, '_blank');
            }
        }
    }
</script>
