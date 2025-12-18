@php use Carbon\Carbon; @endphp
<div x-data="{
    newSubtopic: '',
    showScrollToBottom: false,
    sidebarHistoryOpen: true,
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
        const textarea = $refs.messageInput;
        if (textarea) {
           // textarea.style.height = 'auto';
          //  const newRows = Math.ceil(textarea.scrollHeight / 24);
          //  this.messageRows = Math.max(3, Math.min(newRows, 8));
          //  textarea.style.height = 'auto';
        }
    }
}"
     class="min-h-screen rounded-t-lg bg-gradient-to-br from-gray-50 via-blue-50 to-indigo-50 dark:from-gray-900 dark:via-gray-800 dark:to-gray-900 transition-colors duration-200">

    <!-- Header -->
    <header class="bg-white/80 dark:bg-gray-800/80 backdrop-blur-md shadow-md border-b border-gray-200/50 dark:border-gray-700/50 sticky top-0 z-50 rounded-t-lg">
        <div class="max-w-full px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center space-x-3 flex-1 min-w-0">
                    <div class="flex-shrink-0 relative">
                        <div class="absolute inset-0 bg-blue-500 rounded-full blur-md opacity-50 animate-pulse"></div>
                        <svg class="h-10 w-10 text-blue-600 dark:text-blue-400 relative" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                        </svg>
                    </div>
                    <div class="min-w-0">
                        <h1 class="text-lg sm:text-xl font-bold bg-gradient-to-r from-blue-600 to-indigo-600 dark:from-blue-400 dark:to-indigo-400 bg-clip-text text-transparent truncate">
                            Research Assistant
                        </h1>
                        @if($conversationTitle)
                            <p class="text-xs text-gray-600 dark:text-gray-400 font-medium truncate">
                                {{ $conversationTitle }}
                            </p>
                        @else
                            <p class="text-xs text-gray-600 dark:text-gray-400 font-medium">Personalized learning support</p>
                        @endif
                    </div>
                </div>

                <div class="flex items-center gap-1 sm:gap-2 flex-shrink-0">
                    <!-- Toggle History Sidebar -->
                    <button @click="toggleHistorySidebar()"
                            :class="sidebarHistoryOpen ? 'bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400' : 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400'"
                            class="p-2 rounded-lg hover:bg-blue-100 dark:hover:bg-blue-900/30 transition-all shadow-sm border border-gray-200 dark:border-gray-600"
                            title="Toggle history">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </button>

                    <!-- Toggle Parameters Sidebar -->
                    <button @click="toggleParametersSidebar()"
                            :class="sidebarParametersOpen ? 'bg-purple-100 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400' : 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400'"
                            class="p-2 rounded-lg hover:bg-purple-100 dark:hover:bg-purple-900/30 transition-all shadow-sm border border-gray-200 dark:border-gray-600"
                            title="Toggle settings">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                    </button>

                    <!-- New Chat -->
                    <button wire:click="newConversation"
                            class="p-2 rounded-lg bg-red-100 dark:bg-red-900/20 text-red-600 dark:text-red-400 hover:bg-red-200 dark:hover:bg-red-900/30 transition-all shadow-sm border border-red-200 dark:border-red-800"
                            title="New conversation">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </header>

    <!-- Main Content Grid -->
    <div class="flex h-[calc(100vh-5rem)] overflow-hidden">

        <!-- History Sidebar -->
        <div :class="sidebarHistoryOpen ? 'lg:w-80 w-80' : 'lg:w-0 w-0'"
             class="hidden lg:flex lg:flex-col bg-white/95 dark:bg-gray-800/95 backdrop-blur-xl border-r border-gray-200/50 dark:border-gray-700/50 overflow-hidden transition-all duration-300 ease-in-out">

             <div class="flex justify-between">
            <div class="p-4 border-b border-gray-200 dark:border-gray-700">
                <h2 class="text-lg font-bold bg-gradient-to-r from-blue-600 to-indigo-600 dark:from-blue-400 dark:to-indigo-400 bg-clip-text text-transparent">
                    Chat History
                </h2>
            </div>
            <div class="my-auto m-2">
 <button @click="toggleHistorySidebar()"
                            :class="sidebarHistoryOpen ? 'bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400' : 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400'"
                            class="p-2 rounded-lg hover:bg-blue-100 dark:hover:bg-blue-900/30 transition-all shadow-sm border border-gray-200 dark:border-gray-600"
                            title="Toggle history">
                   <svg class="h-5 w-5 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
</svg>
                    </button>
            </div>
             </div>


            <div class="flex-1 overflow-y-auto p-4 space-y-2 thin-scrollbar">
                @forelse($conversationHistory as $conversation)
                    <div wire:click="loadConversation('{{ $conversation['id'] }}')"
                         class="group flex items-center justify-between p-3 rounded-xl hover:bg-blue-50 dark:hover:bg-gray-700 cursor-pointer transition-all duration-200 border {{ $conversationId === $conversation['id'] ? 'border-blue-300 dark:border-blue-700 bg-blue-50 dark:bg-blue-900/20' : 'border-transparent hover:border-blue-200 dark:hover:border-blue-800' }}">
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-semibold text-gray-900 dark:text-white truncate {{ $conversationId === $conversation['id'] ? 'text-blue-600 dark:text-blue-400' : '' }}">
                                {{ $conversation['title'] }}
                            </p>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                {{ Carbon::parse($conversation['created_at'])->format('M j, g:i A') }}
                            </p>
                        </div>
                        <button wire:click.stop="deleteConversation('{{ $conversation['id'] }}')"
                                wire:confirm="Delete this conversation?"
                                class="opacity-0 group-hover:opacity-100 text-red-500 hover:text-red-700 p-1.5 hover:bg-red-100 dark:hover:bg-red-900/30 rounded-lg transition-all flex-shrink-0">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
                        </button>
                    </div>
                @empty
                    <div class="text-center py-8">
                        <svg class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                        </svg>
                        <p class="mt-3 text-xs text-gray-500 dark:text-gray-400">No conversations yet</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Chat Area -->
        <div class="flex-1 flex flex-col bg-white/90 dark:bg-gray-800/90 backdrop-blur-xl relative overflow-hidden">

            <!-- Token Warning -->
            @if($this->currentTokenWarning)
                <div class="p-3 sm:p-4 border-b border-gray-200 dark:border-gray-700 bg-gradient-to-r from-yellow-50 to-amber-50 dark:from-yellow-900/20 dark:to-amber-900/20">
                    <div class="flex items-start gap-3">
                        <svg class="w-5 h-5 text-yellow-600 dark:text-yellow-400 flex-shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                        </svg>
                        <div class="flex-1">
                            <p class="text-sm font-semibold text-yellow-800 dark:text-yellow-200">
                                @if($this->currentTokenWarning === 'no_subscription')
                                    Activate  messenger package to get started
                                @elseif($this->currentTokenWarning === 'insufficient')
                                    You have insufficient messengers. Top up to continue.
                                @elseif($this->currentTokenWarning === 'depleted')
                                    Your messengers are depleted. Purchase more to continue.
                                @elseif($this->currentTokenWarning === 'expired')
                                    Your subscription has expired. Renew to continue.
                                @endif
                            </p>
                        </div>
                        <a href="{{ route('token-subscriptions.create') }}" class="text-xs font-semibold text-yellow-700 dark:text-yellow-300 hover:underline whitespace-nowrap">Get Tokens →</a>
                    </div>
                </div>
            @endif

            <!-- Chat Messages -->
            <div x-ref="chatContainer"
                 @scroll="checkScroll()"
                 class="flex-1 overflow-y-auto p-4 sm:p-6 space-y-6 bg-gradient-to-b from-transparent to-gray-50/50 dark:to-gray-900/50 thin-scrollbar">

                @if(empty($messages))
                    <div class="text-center py-20">
                        <div class="relative inline-block">
                            <div class="absolute inset-0 bg-gradient-to-r from-blue-500 to-indigo-500 rounded-full blur-2xl opacity-20"></div>
                            <svg class="relative mx-auto h-20 w-20 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                            </svg>
                        </div>
                        <h3 class="mt-6 text-2xl font-bold text-gray-900 dark:text-white">Start Learning</h3>
                        <p class="mt-2 text-gray-600 dark:text-gray-400">Ask me anything! I'm here to help.</p>
                    </div>
                @endif

                @foreach($messages as $index => $message)
                    <div key="message-{{ $index }}-{{ $conversationId }}" class="flex {{ $message['role'] === 'user' ? 'justify-end' : 'justify-start' }} animate-fade-in">
                        <div class="max-w-2xl lg:max-w-3xl">
                            <div class="flex items-start gap-3 {{ $message['role'] === 'user' ? 'flex-row-reverse' : '' }}">
                                <!-- Avatar -->
                                <div class="flex-shrink-0">
                                    @if($message['role'] === 'user')
                                        <div class="h-8 w-8 sm:h-10 sm:w-10 bg-gradient-to-br from-purple-500 to-indigo-600 rounded-full ring-2 ring-purple-200 dark:ring-purple-800 flex items-center justify-center flex-shrink-0">
                                            <x-avatar class="!w-8 !h-8 sm:!w-10 sm:!h-10" text-size="text-xs" name="{{auth()->user()->name}}" avatar="{{auth()->user()->avatar}}"/>
                                        </div>
                                    @else
                                        <div class="h-8 w-8 sm:h-10 sm:w-10 bg-gradient-to-br from-green-500 to-emerald-600 rounded-full ring-2 ring-green-200 dark:ring-green-800 flex items-center justify-center flex-shrink-0">
                                            <svg class="h-5 w-5 sm:h-6 sm:w-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0114 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                                            </svg>
                                        </div>
                                    @endif
                                </div>

                                <!-- Message -->
                                <div class="flex-1 {{ $message['role'] === 'user' ? 'text-right' : '' }}">
                                    <div class="px-4 py-0.5 rounded-xl shadow-sm {{ $message['role'] === 'user' ? 'bg-gradient-to-br from-purple-100 to-indigo-100 dark:from-purple-900/40 dark:to-indigo-900/40' : 'bg-white dark:bg-gray-700/50' }}">
                                        <div class="prose prose-sm max-w-none text-left dark:prose-invert
            prose-headings:text-gray-900 dark:prose-headings:text-gray-100
            prose-p:text-gray-900 dark:prose-p:text-gray-100
            prose-strong:text-gray-900 dark:prose-strong:text-gray-100
            prose-em:text-gray-800 dark:prose-em:text-gray-200
            prose-code:text-gray-900 dark:prose-code:text-gray-100
            prose-code:bg-gray-100 dark:prose-code:bg-gray-800
            prose-code:px-1.5 prose-code:py-0.5 prose-code:rounded
            prose-pre:text-gray-100 dark:prose-pre:text-gray-100
            prose-pre:bg-gray-800 dark:prose-pre:bg-gray-900
            prose-pre:shadow-inner
            prose-li:text-gray-900 dark:prose-li:text-gray-100
            prose-ul:text-gray-900 dark:prose-ul:text-gray-100
            prose-ol:text-gray-900 dark:prose-ol:text-gray-100
            prose-a:text-blue-600 dark:prose-a:text-blue-400
            prose-a:no-underline hover:prose-a:underline
            prose-blockquote:text-gray-800 dark:prose-blockquote:text-gray-200
            prose-blockquote:border-gray-300 dark:prose-blockquote:border-gray-600">
                                            @if(is_string($message['content']))
                                                <x-form.markdown-with-math :content="trim($message['content'])"></x-form.markdown-with-math>
                                            @else
                                                <x-form.markdown-with-math :content="$message['content']"></x-form.markdown-with-math>
                                            @endif

                                            @if(isset($message['images']) && !empty($message['images']))
                                                <div class="mt-3 grid grid-cols-1 sm:grid-cols-2 gap-2">
                                                    @foreach($message['images'] as $image)
                                                        <img src="{{ $image['url'] }}" alt="Generated image" class="rounded-lg border border-gray-300 dark:border-gray-600 max-h-96 object-cover">
                                                    @endforeach
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">{{ Carbon::parse($message['timestamp'])->diffForHumans() }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach

                @if($isLoading)
                    <div class="flex justify-start">
                        <div class="flex items-center gap-3">
                            <div class="h-8 w-8 sm:h-10 sm:w-10 bg-gradient-to-br from-green-500 to-emerald-600 rounded-full flex items-center justify-center">
                                <svg class="h-5 w-5 sm:h-6 sm:w-6 text-white animate-spin" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                            </div>
                            <div class="px-4 py-3 rounded-xl bg-white dark:bg-gray-700/50">
                                <div class="flex gap-1">
                                    <div class="w-2 h-2 bg-green-500 rounded-full animate-bounce"></div>
                                    <div class="w-2 h-2 bg-green-500 rounded-full animate-bounce" style="animation-delay: 0.1s"></div>
                                    <div class="w-2 h-2 bg-green-500 rounded-full animate-bounce" style="animation-delay: 0.2s"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Scroll to Bottom -->
            <button x-show="showScrollToBottom" @click="scrollToBottom()"
                    class="absolute bottom-32 right-6 p-3 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-full shadow-xl hover:shadow-2xl transition-all transform hover:scale-110 z-10">
                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path>
                </svg>
            </button>

            <!-- Message Input -->
            <div class="border-t border-gray-200 dark:border-gray-700 p-4 sm:p-6 bg-gradient-to-r from-gray-50/50 to-blue-50/50 dark:from-gray-800/50 dark:to-gray-700/50 {{ $this->messageInputDisabled ? 'opacity-50 pointer-events-none' : '' }}">
                <div class="flex gap-3">
                    <div class="flex-1 relative">
            <textarea x-ref="messageInput"
                      wire:model="message"
                      @input="adjustMessageRows()"
                      @keydown.enter="if (!$event.shiftKey && !{{ $this->messageInputDisabled ? 'true' : 'false' }}) { $wire.sendMessage(); $event.preventDefault(); }"
                      :rows="messageRows"
                      class="w-full px-4 py-3 border-2 border-gray-300 dark:border-gray-600 rounded-2xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white resize-none transition-all shadow-sm hover:shadow-md placeholder-gray-400 dark:placeholder-gray-500 text-sm"
                      placeholder="{{ $this->messageInputDisabled ? 'Subscribe to chat...' : 'Ask me anything... (Shift+Enter for new line)' }}"
                      @disabled($this->messageInputDisabled)></textarea>
                    </div>

                    <!-- Send Button -->
                    <button wire:click="sendMessage"
                            wire:loading.attr="disabled"
                            wire:target="sendMessage"
                            :disabled="!$wire.message.trim() || {{ $this->messageInputDisabled ? 'true' : 'false' }}"
                            class="flex-shrink-0 h-12 px-4 my-auto sm:px-6 text-white rounded-2xl bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 disabled:opacity-50 disabled:cursor-not-allowed transition-all shadow-md hover:shadow-lg transform hover:scale-105 font-semibold flex items-center justify-center">
                        <svg wire:loading.remove wire:target="sendMessage" class="h-5 w-5 rotate-90" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                        </svg>
                        <svg wire:loading wire:target="sendMessage" class="h-5 w-5 animate-spin" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                    </button>
                </div>
                <p class="text-xs text-gray-500 dark:text-gray-400 mt-2 ml-1">{{ !$this->messageInputDisabled ? '💡 Tip: Add details about your learning goals for better results' : '' }}</p>
            </div>
        </div>

        <!-- Parameters Sidebar -->
        <div :class="sidebarParametersOpen ? 'lg:w-80 w-80' : 'lg:w-0 w-0'"
             class="hidden lg:flex lg:flex-col bg-white/95 dark:bg-gray-800/95 backdrop-blur-xl border-l border-gray-200/50 dark:border-gray-700/50 overflow-hidden transition-all duration-300 ease-in-out">

            <div class="p-4 border-b border-gray-200 dark:border-gray-700">
                <div class="flex items-center justify-between">
                    <h2 class="text-lg font-bold bg-gradient-to-r from-purple-600 to-pink-600 dark:from-purple-400 dark:to-pink-400 bg-clip-text text-transparent">
                        Settings
                    </h2>
                    <button wire:click="resetParameters" class="text-xs font-semibold text-purple-600 dark:text-purple-400 hover:underline transition-colors">
                        Reset
                    </button>
                </div>
            </div>

            <div class="flex-1 overflow-y-auto p-4 space-y-4">
                <!-- Age -->
                <div>
                    <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Age</label>
                    <input type="number" wire:model.live="age" min="5" max="100"
                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm"
                           placeholder="Enter age">
                </div>

                <!-- Academic Level -->
                <div>
                    <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Level</label>
                    <select wire:model.live="academic_level"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm">
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
                    <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Subject</label>
                    <select wire:model.live="subject"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm">
                        <option value="">Select subject</option>
                        @foreach($availableSubjects as $subject => $topics)
                            <option value="{{ $subject }}">{{ ucwords(str_replace('_', ' ', $subject)) }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Learning Style -->
                <div>
                    <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Learning Style</label>
                    <select wire:model.live="learning_style"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm">
                        <option value="">Select style</option>
                        <option value="visual">Visual</option>
                        <option value="auditory">Auditory</option>
                        <option value="kinesthetic">Kinesthetic</option>
                        <option value="reading">Reading/Writing</option>
                    </select>
                </div>

                <!-- Difficulty -->
                <div>
                    <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Difficulty</label>
                    <select wire:model.live="difficulty"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm">
                        <option value="beginner">Beginner</option>
                        <option value="intermediate">Intermediate</option>
                        <option value="advanced">Advanced</option>
                    </select>
                </div>

                <!-- Response Format -->
                <div>
                    <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Format</label>
                    <select wire:model.live="response_format"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm">
                        <option value="detailed">Detailed</option>
                        <option value="concise">Concise</option>
                        <option value="interactive">Interactive</option>
                    </select>
                </div>

                <!-- Response Length -->
                <div>
                    <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 mb-1.5">Response Length</label>
                    <select wire:model.live="response_length"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm">
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
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-fade-in { animation: fade-in 0.3s ease-out; }
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb {
            background: linear-gradient(135deg, #3B82F6 0%, #6366F1 100%);
            border-radius: 10px;
        }
        .dark ::-webkit-scrollbar-thumb {
            background: linear-gradient(135deg, #60A5FA 0%, #818CF8 100%);
        }
    </style>
</div>
