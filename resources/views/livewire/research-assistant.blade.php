@php use Carbon\Carbon; @endphp
<div x-data="{
    message: '',
    newSubtopic: '',
    showScrollToBottom: false,
    sidebarHistoryOpen: false,
    sidebarParametersOpen: false,
    messageRows: 3,

    addSubtopic() {
        if (this.newSubtopic.trim()) {
            $wire.subtopics.push(this.newSubtopic.trim());
            this.newSubtopic = '';
        }
    },

    scrollToBottom() {
        const chatContainer = $refs.chatContainer;
        chatContainer.scrollTop = chatContainer.scrollHeight;
    },

    checkScroll() {
        const chatContainer = $refs.chatContainer;
        this.showScrollToBottom = chatContainer.scrollHeight - chatContainer.scrollTop > chatContainer.clientHeight + 100;
    },

    toggleHistorySidebar() {
        this.sidebarHistoryOpen = !this.sidebarHistoryOpen;
    },

    toggleParametersSidebar() {
        this.sidebarParametersOpen = !this.sidebarParametersOpen;
    },

    adjustMessageRows() {
        const textarea = this.$refs.messageInput;
        if (textarea) {
            textarea.style.height = 'auto';
            const lineHeight = 24;
            const maxLines = 5;
            const maxHeight = lineHeight * maxLines;
            const newHeight = Math.min(textarea.scrollHeight, maxHeight);
            textarea.style.height = newHeight + 'px';
        }
    }
}"
    class="h-screen flex flex-col bg-slate-50 dark:bg-slate-950 transition-colors duration-200 overflow-hidden"
    @if($isLoading) wire:poll.2500ms="checkForResponse" @endif
    @scroll-to-bottom.window="scrollToBottom()">

    <!-- Header -->
    <header
        class="flex-shrink-0 bg-white/80 dark:bg-slate-900/80 backdrop-blur-xl border-b border-slate-200 dark:border-slate-800 sticky top-0 z-50">
        <div class="max-w-full px-4 sm:px-6">
            <div class="flex justify-between items-center h-16">
                <!-- Logo & Title -->
                <div class="flex items-center space-x-3 flex-1 min-w-0">
                    <div
                        class="flex-shrink-0 h-9 w-9 bg-gradient-to-br from-indigo-500 to-blue-600 rounded-lg flex items-center justify-center shadow-lg shadow-indigo-500/20">
                        <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0114 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z">
                            </path>
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <h1 class="text-base font-bold text-slate-900 dark:text-white truncate tracking-tight">
                            Research Assistant
                        </h1>
                        <p class="text-xs text-slate-500 dark:text-slate-400 font-medium truncate">
                            {{ $conversationTitle ?: 'Personalized learning support' }}
                        </p>
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex items-center gap-2 flex-shrink-0">
                    <button @click="toggleHistorySidebar()"
                        class="p-2 rounded-lg text-slate-500 hover:text-slate-900 dark:text-slate-400 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors"
                        title="Toggle history">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </button>
                    <button @click="toggleParametersSidebar()"
                        class="p-2 rounded-lg text-slate-500 hover:text-slate-900 dark:text-slate-400 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors"
                        title="Toggle settings">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z">
                            </path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                    </button>
                    <button wire:click="newConversation"
                        class="flex items-center gap-2 px-3 py-1.5 rounded-lg text-sm font-medium text-white bg-slate-900 dark:bg-white dark:text-slate-900 hover:bg-slate-800 dark:hover:bg-slate-100 transition-colors shadow-sm">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4">
                            </path>
                        </svg>
                        <span class="hidden sm:inline">New Chat</span>
                    </button>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content Grid -->
    <div class="flex flex-1 overflow-hidden">

        @php
            $now = \Carbon\Carbon::now();
            $grouped = collect($conversationHistory)->groupBy(function ($conv) use ($now) {
                $date = \Carbon\Carbon::parse($conv['created_at']);
                if ($date->isToday()) {
                    return 'Today';
                }
                if ($date->isYesterday()) {
                    return 'Yesterday';
                }
                if ($date->greaterThan($now->copy()->subDays(7))) {
                    return 'Previous 7 Days';
                }
                return $date->format('M Y');
            });
            $groupKeys = array_keys($grouped->toArray());
        @endphp

        <!-- History Sidebar -->
        <div :class="sidebarHistoryOpen ? 'w-80' : 'w-0'"
            class="flex flex-col bg-white dark:bg-slate-900 border-r border-slate-200 dark:border-slate-800 overflow-hidden transition-all duration-300 ease-in-out">
            <div
                class="p-4 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between flex-shrink-0">
                <h2 class="text-sm font-semibold text-slate-900 dark:text-white uppercase tracking-wider">History</h2>
                <button @click="toggleHistorySidebar()"
                    class="p-1.5 rounded-md text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                        </path>
                    </svg>
                </button>
            </div>

            <div x-data="{ openGroups: @js($groupKeys) }" class="flex-1 overflow-y-auto p-3 space-y-4 custom-scrollbar">
                @forelse($grouped as $label => $conversations)
                    <div>
                        <button
                            @click="let label = @js($label); if (openGroups.includes(label)) { openGroups = openGroups.filter(g => g !== label); } else { openGroups.push(label); }"
                            class="flex items-center justify-between w-full px-2 py-1 text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider hover:text-slate-700 dark:hover:text-slate-200 transition-colors">
                            <span>{{ $label }}</span>
                            <svg class="w-3 h-3 transition-transform duration-200"
                                :class="openGroups.includes(@js($label)) ? 'rotate-90' : ''"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7">
                                </path>
                            </svg>
                        </button>
                        <div x-show="openGroups.includes(@js($label))" class="mt-1 space-y-1">
                            @foreach ($conversations as $conversation)
                                <div wire:click="loadConversation('{{ $conversation['id'] }}')"
                                    class="group flex items-center justify-between p-2.5 rounded-lg cursor-pointer transition-all duration-150 {{ $conversationId === $conversation['id'] ? 'bg-slate-100 dark:bg-slate-800 text-slate-900 dark:text-white' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-800/50' }}">
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium truncate">{{ $conversation['title'] }}</p>
                                        <p class="text-xs text-slate-500 dark:text-slate-500 mt-0.5">
                                            {{ Carbon::parse($conversation['created_at'])->format('g:i A') }}
                                        </p>
                                    </div>
                                    <button wire:click.stop="deleteConversation('{{ $conversation['id'] }}')"
                                        wire:confirm="Delete this conversation?"
                                        class="opacity-0 group-hover:opacity-100 text-slate-400 hover:text-red-500 p-1.5 rounded-md hover:bg-red-50 dark:hover:bg-red-900/20 transition-all flex-shrink-0 ml-2">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                            </path>
                                        </svg>
                                    </button>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @empty
                    <div class="text-center py-12 px-4">
                        <svg class="mx-auto h-10 w-10 text-slate-300 dark:text-slate-700" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z">
                            </path>
                        </svg>
                        <p class="mt-3 text-xs text-slate-500 dark:text-slate-500 font-medium">No conversations yet</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Chat Area -->
        <div class="flex-1 flex flex-col relative overflow-hidden bg-slate-50 dark:bg-slate-950">

            <!-- Token Warning -->
            @if ($this->currentTokenWarning)
                <div
                    class="flex-shrink-0 mx-4 mt-4 p-3 rounded-lg border border-amber-200 dark:border-amber-900/50 bg-amber-50 dark:bg-amber-900/20 flex items-center gap-3">
                    <svg class="w-5 h-5 text-amber-500 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                            d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                            clip-rule="evenodd"></path>
                    </svg>
                    <p class="text-sm font-medium text-amber-800 dark:text-amber-200 flex-1">
                        @if ($this->currentTokenWarning === 'no_subscription')
                            Activate messenger package to get started
                        @elseif($this->currentTokenWarning === 'insufficient')
                            You have insufficient messengers. Top up to continue.
                        @elseif($this->currentTokenWarning === 'depleted')
                            Your messengers are depleted. Purchase more to continue.
                        @elseif($this->currentTokenWarning === 'expired')
                            Your subscription has expired. Renew to continue.
                        @endif
                    </p>
                    <a href="{{ route('token-subscriptions.create') }}"
                        class="text-xs font-semibold text-amber-700 dark:text-amber-300 hover:underline whitespace-nowrap">Get
                        Messengers &rarr;</a>
                </div>
            @endif

            <div class="flex-1 flex flex-col overflow-hidden {{ empty($messages) ? 'justify-center' : '' }}">
                <!-- Chat Messages -->
                <div x-ref="chatContainer" @scroll="checkScroll()"
                    class="{{ empty($messages) ? 'flex-none' : 'flex-1' }} overflow-y-auto p-4 sm:p-6 space-y-6 custom-scrollbar">

                    @if (empty($messages))
                        <div class="text-center px-4 pb-8">
                            <div
                                class="h-16 w-16 rounded-2xl bg-gradient-to-br from-indigo-500 to-blue-600 flex items-center justify-center shadow-lg shadow-indigo-500/20 mb-6 mx-auto">
                                <svg class="h-8 w-8 text-white" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0114 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z">
                                    </path>
                                </svg>
                            </div>
                            <h3 class="text-xl font-bold text-slate-900 dark:text-white tracking-tight">How can I help
                                you today?</h3>
                            <p class="mt-2 text-sm text-slate-500 dark:text-slate-400 max-w-md mx-auto">Ask me anything
                                about your studies, research topics, or complex concepts. I'm here to help you learn.
                            </p>
                        </div>
                    @endif

                    @foreach ($messages as $index => $message)
                        <div key="message-{{ $index }}-{{ $conversationId }}"
                            class="flex {{ $message['role'] === 'user' ? 'justify-end' : 'justify-start' }} animate-fade-in">
                            <div
                                class="max-w-3xl w-full flex flex-col {{ $message['role'] === 'user' ? 'items-end' : 'items-start' }}">

                                <!-- Message Content -->
                                <div
                                    class="inline-block max-w-full {{ $message['role'] === 'user' ? 'bg-slate-100 dark:bg-slate-700 text-slate-900 dark:text-slate-100 rounded-2xl rounded-tr-sm' : 'bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 rounded-2xl rounded-tl-sm border border-slate-200 dark:border-slate-700' }}">
                                    <div class="px-4 py-1">
                                        <div
                                            class="prose prose-sm max-w-none {{ $message['role'] === 'user' ? 'prose-invert' : 'prose-slate dark:prose-invert' }}">
                                            @if (is_string($message['content']))
                                                <x-prose-content :content="trim($message['content'])"></x-prose-content>
                                            @else
                                                <x-prose-content :content="$message['content']"></x-prose-content>
                                            @endif
                                        </div>

                                        @if (isset($message['images']) && !empty($message['images']))
                                            <div class="mt-3 grid grid-cols-1 sm:grid-cols-2 gap-2">
                                                @foreach ($message['images'] as $image)
                                                    <img src="{{ $image['url'] }}" alt="Generated image"
                                                        class="rounded-lg border border-slate-200 dark:border-slate-600 max-h-96 object-cover">
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                <!-- Avatar and Timestamp -->
                                <div
                                    class="flex items-center gap-2 mt-1.5 px-1 {{ $message['role'] === 'user' ? 'flex-row-reverse' : '' }}">
                                    <div class="flex-shrink-0">
                                        @if ($message['role'] === 'user')
                                            <div
                                                class="h-6 w-6 rounded-full bg-slate-200 dark:bg-slate-700 flex items-center justify-center overflow-hidden ring-2 ring-white dark:ring-slate-800">
                                                <x-avatar class="!w-6 !h-6" text-size="text-[10px]"
                                                    name="{{ auth()->user()->name }}"
                                                    avatar="{{ auth()->user()->avatar }}" />
                                            </div>
                                        @else
                                            <div
                                                class="h-6 w-6 rounded-lg bg-gradient-to-br from-indigo-500 to-blue-600 flex items-center justify-center shadow-sm">
                                                <svg class="h-3 w-3 text-white" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                        stroke-width="2"
                                                        d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0114 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z">
                                                    </path>
                                                </svg>
                                            </div>
                                        @endif
                                    </div>
                                    <p class="text-[11px] text-slate-400 dark:text-slate-500">
                                        {{ Carbon::parse($message['timestamp'])->diffForHumans() }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endforeach

                    @if ($isLoading)
                        <div class="flex justify-start animate-fade-in">
                            <div class="flex flex-col items-start max-w-3xl w-full">
                                <div
                                    class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl rounded-tl-sm px-4 py-3 shadow-sm">
                                    <div class="flex gap-1.5 items-center h-5">
                                        <div
                                            class="w-1.5 h-1.5 bg-slate-400 dark:bg-slate-500 rounded-full animate-bounce">
                                        </div>
                                        <div class="w-1.5 h-1.5 bg-slate-400 dark:bg-slate-500 rounded-full animate-bounce"
                                            style="animation-delay: 0.1s"></div>
                                        <div class="w-1.5 h-1.5 bg-slate-400 dark:bg-slate-500 rounded-full animate-bounce"
                                            style="animation-delay: 0.2s"></div>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2 mt-1.5 px-1">
                                    <div
                                        class="h-6 w-6 rounded-lg bg-gradient-to-br from-indigo-500 to-blue-600 flex items-center justify-center shadow-sm flex-shrink-0">
                                        <svg class="h-3 w-3 text-white" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0114 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z">
                                            </path>
                                        </svg>
                                    </div>
                                    <p class="text-[11px] text-slate-400 dark:text-slate-500">Typing...</p>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Scroll to Bottom -->
                <button x-show="showScrollToBottom" x-transition @click="scrollToBottom()"
                    class="absolute bottom-36 right-6 p-2.5 bg-white dark:bg-slate-800 text-slate-600 dark:text-slate-300 rounded-full shadow-lg border border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-700 transition-all z-10">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 14l-7 7m0 0l-7-7m7 7V3"></path>
                    </svg>
                </button>

                <!-- Message Input -->
                <div
                    class="flex-shrink-0 p-4 sm:p-6 bg-gradient-to-t from-slate-50 via-slate-50 to-transparent dark:from-slate-950 dark:via-slate-950 dark:to-transparent">
                    <div class="max-w-3xl mx-auto">
                        <div
                            class="relative flex items-end gap-2 p-2 bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-lg shadow-slate-200/50 dark:shadow-slate-900/50 focus-within:border-indigo-500 dark:focus-within:border-indigo-500 focus-within:ring-4 focus-within:ring-indigo-500/10 transition-all duration-200 {{ $this->messageInputDisabled ? 'opacity-60 cursor-not-allowed' : '' }}">

                            <!-- Left Action -->
                            <div class="flex-shrink-0 ml-1 mb-1">
                                <button @disabled($this->messageInputDisabled)
                                    class="p-2 text-slate-400 hover:text-indigo-600 dark:hover:text-indigo-400 hover:bg-slate-100 dark:hover:bg-slate-700 rounded-lg transition-all duration-200"
                                    title="Add resource">
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 4v16m8-8H4"></path>
                                    </svg>
                                </button>
                            </div>

                            <!-- Textarea -->
                            <textarea x-ref="messageInput" wire:model.live="message" @input="adjustMessageRows()"
                                @keydown.enter="if (!$event.shiftKey && !{{ $this->messageInputDisabled ? 'true' : 'false' }}) { $wire.sendMessage(); $event.preventDefault(); }"
                                rows="1"
                                class="flex-1 w-full px-3 py-2 bg-transparent border-0 focus:ring-0 text-slate-900 dark:text-white placeholder-slate-400 dark:placeholder-slate-500 text-sm resize-none max-h-[120px] overflow-y-auto leading-relaxed"
                                placeholder="{{ $this->messageInputDisabled ? 'Subscribe to chat...' : 'Ask anything...' }}"
                                @disabled($this->messageInputDisabled)></textarea>

                            <!-- Send Button -->
                            <div class="flex-shrink-0 mr-1 mb-1">
                                <button wire:click="sendMessage" wire:loading.attr="disabled"
                                    wire:target="sendMessage" @disabled($this->messageInputDisabled)
                                    class="flex items-center justify-center h-9 w-9 text-white bg-slate-900 dark:bg-white dark:text-slate-900 hover:bg-slate-800 dark:hover:bg-slate-100 rounded-xl shadow-sm transition-all duration-200 disabled:bg-slate-200 dark:disabled:bg-slate-700 disabled:text-slate-400 dark:disabled:text-slate-500 disabled:cursor-not-allowed">
                                    <svg wire:loading.remove wire:target="sendMessage" class="h-4 w-4" fill="none"
                                        stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                            d="M5 12h14M12 5l7 7-7 7"></path>
                                    </svg>
                                    <svg wire:loading wire:target="sendMessage" class="h-4 w-4 animate-spin"
                                        fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10"
                                            stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor"
                                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                        </path>
                                    </svg>
                                </button>
                            </div>
                        </div>
                        <div class="flex justify-between items-center px-2 mt-2">
                            <p class="text-[11px] text-slate-400 dark:text-slate-500 font-medium">
                                {{ !$this->messageInputDisabled ? 'Press Enter to send, Shift+Enter for new line' : 'Subscription required' }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Parameters Sidebar -->
        <div :class="sidebarParametersOpen ? 'w-80' : 'w-0'"
            class="flex flex-col bg-white dark:bg-slate-900 border-l border-slate-200 dark:border-slate-800 overflow-hidden transition-all duration-300 ease-in-out">
            <div
                class="p-4 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between flex-shrink-0">
                <h2 class="text-sm font-semibold text-slate-900 dark:text-white uppercase tracking-wider">Settings</h2>
                <div class="flex items-center gap-2">
                    <button wire:click="resetParameters"
                        class="text-xs font-medium text-slate-500 hover:text-slate-900 dark:text-slate-400 dark:hover:text-white transition-colors">Reset</button>
                    <button @click="toggleParametersSidebar()"
                        class="p-1.5 rounded-md text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors lg:hidden">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>
            <div class="flex-1 overflow-y-auto p-4 space-y-5 custom-scrollbar">
                <!-- Age -->
                <div>
                    <label
                        class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1.5 uppercase tracking-wide">Age</label>
                    <input type="number" wire:model.live="age" min="5" max="100"
                        class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 dark:focus:border-indigo-500 text-slate-900 dark:text-white text-sm transition-colors"
                        placeholder="Enter age">
                </div>

                <!-- Academic Level -->
                <div>
                    <label
                        class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1.5 uppercase tracking-wide">Level</label>
                    <select wire:model.live="academic_level"
                        class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 dark:focus:border-indigo-500 text-slate-900 dark:text-white text-sm transition-colors">
                        <option value="">Select level</option>
                        <option value="elementary">Elementary</option>
                        <option value="middle_school">Middle School</option>
                        <option value="high_school">High School</option>
                        <option value="college">College</option>
                        <option value="graduate">Graduate</option>
                    </select>
                </div>

                <!-- Subject -->
                <div>
                    <label
                        class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1.5 uppercase tracking-wide">Subject</label>
                    <select wire:model.live="subject"
                        class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 dark:focus:border-indigo-500 text-slate-900 dark:text-white text-sm transition-colors">
                        <option value="">Select subject</option>
                        @foreach ($availableSubjects as $subject => $topics)
                            <option value="{{ $subject }}">{{ ucwords(str_replace('_', ' ', $subject)) }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Learning Style -->
                <div>
                    <label
                        class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1.5 uppercase tracking-wide">Learning
                        Style</label>
                    <select wire:model.live="learning_style"
                        class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 dark:focus:border-indigo-500 text-slate-900 dark:text-white text-sm transition-colors">
                        <option value="">Select style</option>
                        <option value="visual">Visual</option>
                        <option value="auditory">Auditory</option>
                        <option value="kinesthetic">Kinesthetic</option>
                        <option value="reading">Reading/Writing</option>
                    </select>
                </div>

                <!-- Difficulty -->
                <div>
                    <label
                        class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1.5 uppercase tracking-wide">Difficulty</label>
                    <select wire:model.live="difficulty"
                        class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 dark:focus:border-indigo-500 text-slate-900 dark:text-white text-sm transition-colors">
                        <option value="beginner">Beginner</option>
                        <option value="intermediate">Intermediate</option>
                        <option value="advanced">Advanced</option>
                    </select>
                </div>

                <!-- Response Format -->
                <div>
                    <label
                        class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1.5 uppercase tracking-wide">Format</label>
                    <select wire:model.live="response_format"
                        class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 dark:focus:border-indigo-500 text-slate-900 dark:text-white text-sm transition-colors">
                        <option value="detailed">Detailed</option>
                        <option value="concise">Concise</option>
                        <option value="interactive">Interactive</option>
                    </select>
                </div>

                <!-- Response Length -->
                <div>
                    <label
                        class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1.5 uppercase tracking-wide">Response
                        Length</label>
                    <select wire:model.live="response_length"
                        class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-lg focus:ring-2 focus:ring-indigo-500/20 focus:border-indigo-500 dark:focus:border-indigo-500 text-slate-900 dark:text-white text-sm transition-colors">
                        <option value="500">Short</option>
                        <option value="1000">Medium</option>
                        <option value="1500">Long</option>
                        <option value="2000">Very Long</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <style>
        @keyframes fade-in {
            from {
                opacity: 0;
                transform: translateY(8px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-fade-in {
            animation: fade-in 0.3s ease-out;
        }

        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
        }

        .custom-scrollbar::-webkit-scrollbar-track {
            background: transparent;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 10px;
        }

        .dark .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #475569;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }
    </style>
</div>
