@php use Carbon\Carbon; @endphp
<div x-data="{
    newSubtopic: '',
    showScrollToBottom: false,
    showHistory: $wire.showHistory,

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

    toggleHistory() {
        this.showHistory = !this.showHistory;
        $wire.toggleHistory();
    }
}"
     class="min-h-screen h-auto bg-gradient-to-br from-gray-50 via-blue-50 to-indigo-50 dark:from-gray-900 dark:via-gray-800 dark:to-gray-900 transition-colors duration-200">

    <!-- Header with gradient and glass effect -->
    <header class="bg-white/80 dark:bg-gray-800/80 backdrop-blur-xl shadow-lg border-b border-gray-200/50 dark:border-gray-700/50 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center space-x-4">
                    <div class="flex-shrink-0 relative">
                        <div class="absolute inset-0 bg-blue-500 rounded-full blur-md opacity-50 animate-pulse"></div>
                        <svg class="h-10 w-10 text-blue-600 dark:text-blue-400 relative" fill="none" stroke="currentColor"
                             viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-xl font-bold bg-gradient-to-r from-blue-600 to-indigo-600 dark:from-blue-400 dark:to-indigo-400 bg-clip-text text-transparent">
                            Educational AI Assistant
                        </h1>
                        <p class="text-sm text-gray-600 dark:text-gray-400 font-medium">Personalized learning support</p>
                    </div>
                </div>

                <div class="flex items-center space-x-2 sm:space-x-3">
                    <!-- History Toggle -->
                    <button
                        @click="toggleHistory()"
                        class="group px-3 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 rounded-xl hover:bg-gradient-to-r hover:from-blue-50 hover:to-indigo-50 dark:hover:from-gray-600 dark:hover:to-gray-600 transition-all duration-300 shadow-sm hover:shadow-md border border-gray-200 dark:border-gray-600">
                        <svg class="h-4 w-4 mr-1 inline group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span class="hidden sm:inline">History</span>
                    </button>

                    <!-- Parameters Toggle -->
                    <button
                        wire:click="toggleParameters"
                        class="group px-3 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 rounded-xl hover:bg-gradient-to-r hover:from-purple-50 hover:to-pink-50 dark:hover:from-gray-600 dark:hover:to-gray-600 transition-all duration-300 shadow-sm hover:shadow-md border border-gray-200 dark:border-gray-600">
                        <svg class="h-4 w-4 mr-1 inline group-hover:rotate-90 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4"></path>
                        </svg>
                        <span class="hidden sm:inline">Settings</span>
                    </button>

                    <!-- Clear Chat -->
                    <button
                        wire:click="newConversation"
                        class="group px-3 py-2 text-sm font-medium text-red-600 dark:text-red-400 bg-red-50 dark:bg-red-900/20 rounded-xl hover:bg-gradient-to-r hover:from-red-100 hover:to-pink-100 dark:hover:from-red-900/30 dark:hover:to-red-900/30 transition-all duration-300 shadow-sm hover:shadow-md border border-red-200 dark:border-red-800">
                        <svg class="h-4 w-4 mr-1 inline group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                        <span class="hidden sm:inline">New</span>
                    </button>
                </div>
            </div>
        </div>
    </header>

    <div class="max-w-7xl mx-auto relative ">
        <!-- Overlay for mobile when sidebars are open -->
        <div x-show="showHistory || $wire.showParameters"
             x-transition:opacity
             style="z-index: 10!important"
             @click="showHistory = false; $wire.showParameters = false;"
             class="fixed inset-0 bg-black/60 backdrop-blur-sm z-10 lg:hidden"></div>

        <div class="relative">
            <!-- Conversation History Sidebar with enhanced styling -->
            <div x-show="showHistory"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 -translate-x-full"
                 x-transition:enter-end="opacity-100 translate-x-0"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-x-0"
                 x-transition:leave-end="opacity-0 -translate-x-full"
                 class="absolute inset-y-0 left-0 z-20 w-80 bg-white/95 dark:bg-gray-800/95 backdrop-blur-xl shadow-2xl overflow-y-auto h-[calc(100vh-5rem)] transform lg:translate-x-0 lg:opacity-100 lg:z-auto lg:shadow-none lg:rounded-2xl lg:border lg:border-gray-200/50 dark:lg:border-gray-700/50"
                 style="top: 5rem; z-index: 20!important">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-lg font-bold bg-gradient-to-r from-blue-600 to-indigo-600 dark:from-blue-400 dark:to-indigo-400 bg-clip-text text-transparent">
                            Chat History
                        </h2>
                        <button @click="toggleHistory()"
                                class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 p-2 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-all">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>

                    <div class="space-y-2">
                        @forelse($conversationHistory as $conversation)
                            <div
                                class="group flex items-center justify-between p-3 rounded-xl hover:bg-gradient-to-r hover:from-blue-50 hover:to-indigo-50 dark:hover:from-gray-700 dark:hover:to-gray-700 cursor-pointer transition-all duration-300 border border-transparent hover:border-blue-200 dark:hover:border-blue-800 hover:shadow-md"
                                wire:click="loadConversation('{{ $conversation['id'] }}')">
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-semibold text-gray-900 dark:text-white truncate group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">
                                        {{ $conversation['title'] }}
                                    </p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                        {{ Carbon::parse($conversation['created_at'])->format('M j, Y g:i A') }}
                                    </p>
                                </div>
                                <button wire:click.stop="deleteConversation('{{ $conversation['id'] }}')"
                                        wire:confirm="Are you sure you want to delete this conversation?"
                                        class="opacity-0 group-hover:opacity-100 text-red-500 hover:text-red-700 p-2 hover:bg-red-100 dark:hover:bg-red-900/30 rounded-lg transition-all">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                </button>
                            </div>
                        @empty
                            <div class="text-center py-12">
                                <div class="relative inline-block">
                                    <div class="absolute inset-0 bg-blue-500 rounded-full blur-xl opacity-20 animate-pulse"></div>
                                    <svg class="relative mx-auto h-16 w-16 text-gray-400 dark:text-gray-500" fill="none"
                                         stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                              d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                                    </svg>
                                </div>
                                <h3 class="mt-4 text-sm font-semibold text-gray-900 dark:text-white">No conversations</h3>
                                <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">Start a new chat to see it here</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Parameters Panel with enhanced styling -->
            <div x-show="$wire.showParameters"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 -translate-x-full"
                 x-transition:enter-end="opacity-100 translate-x-0"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-x-0"
                 x-transition:leave-end="opacity-0 -translate-x-full"
                 class="absolute inset-y-0 left-0 z-40 w-80 bg-white/95 dark:bg-gray-800/95 backdrop-blur-xl shadow-2xl overflow-y-auto h-[calc(100vh-5rem)] transform lg:translate-x-0 lg:opacity-100 lg:z-auto lg:shadow-none lg:rounded-2xl lg:border lg:border-gray-200/50 dark:lg:border-gray-700/50"
                 style="top: 5rem; z-index: 20!important">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-lg font-bold bg-gradient-to-r from-purple-600 to-pink-600 dark:from-purple-400 dark:to-pink-400 bg-clip-text text-transparent">
                            Learning Parameters
                        </h2>
                        <button
                            wire:click="resetParameters"
                            class="text-sm text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 px-3 py-1 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-all">
                            Reset
                        </button>
                    </div>

                    <!-- Basic Info -->
                    <div class="space-y-4 mb-6">
                        <div class="group">
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Age</label>
                            <input
                                type="number"
                                wire:model.live="age"
                                min="5"
                                max="100"
                                class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white transition-all group-hover:border-blue-300 dark:group-hover:border-blue-600"
                                placeholder="Enter age">
                        </div>

                        <div class="group">
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Academic Level</label>
                            <select
                                wire:model.live="academic_level"
                                class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white transition-all group-hover:border-blue-300 dark:group-hover:border-blue-600">
                                <option value="">Select level</option>
                                <option value="elementary">Elementary</option>
                                <option value="middle_school">Middle School</option>
                                <option value="high_school">High School</option>
                                <option value="college">College</option>
                                <option value="graduate">Graduate</option>
                            </select>
                        </div>

                        <div class="group">
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Academic Group</label>
                            <input
                                type="text"
                                wire:model.live="academic_group"
                                class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white transition-all group-hover:border-blue-300 dark:group-hover:border-blue-600"
                                placeholder="e.g., STEM, Liberal Arts">
                        </div>
                    </div>

                    <!-- Subject & Topics -->
                    <div class="space-y-4 mb-6">
                        <div class="group">
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Subject</label>
                            <select
                                wire:model.live="subject"
                                class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white transition-all group-hover:border-blue-300 dark:group-hover:border-blue-600">
                                <option value="">Select subject</option>
                                @foreach($availableSubjects as $subject => $topics)
                                    <option value="{{ $subject }}">{{ ucwords(str_replace('_', ' ', $subject)) }}</option>
                                @endforeach
                            </select>
                        </div>

                        @if($availableTopics)
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">Topics</label>
                                <div class="grid grid-cols-1 gap-2">
                                    @foreach($availableTopics as $topic)
                                        <label class="flex items-center p-2 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700/50 cursor-pointer transition-all group">
                                            <input
                                                type="checkbox"
                                                wire:click="addTopic('{{ $topic }}')"
                                                @if(in_array($topic, $topics)) checked @endif
                                                class="rounded border-gray-300 dark:border-gray-600 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 dark:focus:ring-blue-800 dark:focus:ring-opacity-40 dark:bg-gray-700 transition-all">
                                            <span class="ml-3 text-sm text-gray-700 dark:text-gray-300 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">
                                                {{ ucwords(str_replace('_', ' ', $topic)) }}
                                            </span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <!-- Custom Subtopics -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Custom Subtopics</label>
                            <div class="flex space-x-2 mb-3">
                                <input
                                    type="text"
                                    x-model="newSubtopic"
                                    @keydown.enter="addSubtopic()"
                                    class="flex-1 px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm transition-all"
                                    placeholder="Add subtopic">
                                <button
                                    @click="addSubtopic()"
                                    class="px-4 py-2.5 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-xl hover:from-blue-700 hover:to-indigo-700 transition-all text-sm font-medium shadow-sm hover:shadow-md">
                                    Add
                                </button>
                            </div>
                            @if(!empty($subtopics))
                                <div class="flex flex-wrap gap-2">
                                    @foreach($subtopics as $index => $subtopic)
                                        <span class="inline-flex items-center px-3 py-1.5 rounded-full text-xs font-semibold bg-gradient-to-r from-blue-100 to-indigo-100 dark:from-blue-900/50 dark:to-indigo-900/50 text-blue-800 dark:text-blue-200 border border-blue-200 dark:border-blue-800 shadow-sm">
                                            {{ $subtopic }}
                                            <button
                                                wire:click="removeSubtopic({{ $index }})"
                                                class="ml-2 text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 hover:bg-blue-200 dark:hover:bg-blue-800 rounded-full p-0.5 transition-all">
                                                ×
                                            </button>
                                        </span>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Learning Preferences -->
                    <div class="space-y-4 mb-6">
                        <div class="group">
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Learning Style</label>
                            <select
                                wire:model.live="learning_style"
                                class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white transition-all group-hover:border-blue-300 dark:group-hover:border-blue-600">
                                <option value="">Select style</option>
                                <option value="visual">Visual</option>
                                <option value="auditory">Auditory</option>
                                <option value="kinesthetic">Kinesthetic</option>
                                <option value="reading">Reading/Writing</option>
                            </select>
                        </div>

                        <div class="group">
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Difficulty Level</label>
                            <select
                                wire:model.live="difficulty"
                                class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white transition-all group-hover:border-blue-300 dark:group-hover:border-blue-600">
                                <option value="beginner">Beginner</option>
                                <option value="intermediate">Intermediate</option>
                                <option value="advanced">Advanced</option>
                            </select>
                        </div>

                        <div class="group">
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Response Format</label>
                            <select
                                wire:model.live="response_format"
                                class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white transition-all group-hover:border-blue-300 dark:group-hover:border-blue-600">
                                <option value="detailed">Detailed</option>
                                <option value="concise">Concise</option>
                                <option value="interactive">Interactive</option>
                            </select>
                        </div>
                    </div>

                    <!-- Accommodations -->
                    <div class="space-y-4 mb-6">
                        <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300">Special Accommodations</label>
                        <div class="space-y-2">
                            <label class="flex items-center p-2 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700/50 cursor-pointer transition-all group">
                                <input
                                    type="checkbox"
                                    wire:click="addAccommodation('simplified_language')"
                                    @if(in_array('simplified_language', $accommodations)) checked @endif
                                    class="rounded border-gray-300 dark:border-gray-600 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 dark:focus:ring-blue-800 dark:focus:ring-opacity-40 dark:bg-gray-700 transition-all">
                                <span class="ml-3 text-sm text-gray-700 dark:text-gray-300 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">Simplified Language</span>
                            </label>
                            <label class="flex items-center p-2 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700/50 cursor-pointer transition-all group">
                                <input
                                    type="checkbox"
                                    wire:click="addAccommodation('step_by_step')"
                                    @if(in_array('step_by_step', $accommodations)) checked @endif
                                    class="rounded border-gray-300 dark:border-gray-600 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 dark:focus:ring-blue-800 dark:focus:ring-opacity-40 dark:bg-gray-700 transition-all">
                                <span class="ml-3 text-sm text-gray-700 dark:text-gray-300 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">Step-by-Step</span>
                            </label>
                            <label class="flex items-center p-2 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700/50 cursor-pointer transition-all group">
                                <input
                                    type="checkbox"
                                    wire:click="addAccommodation('examples_heavy')"
                                    @if(in_array('examples_heavy', $accommodations)) checked @endif
                                    class="rounded border-gray-300 dark:border-gray-600 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 dark:focus:ring-blue-800 dark:focus:ring-opacity-40 dark:bg-gray-700 transition-all">
                                <span class="ml-3 text-sm text-gray-700 dark:text-gray-300 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">More Examples</span>
                            </label>
                        </div>
                    </div>

                    <!-- Advanced Settings -->
                    <div class="space-y-4 p-4 bg-gradient-to-br from-purple-50 to-pink-50 dark:from-purple-900/20 dark:to-pink-900/20 rounded-xl border border-purple-200 dark:border-purple-800">
                        <h3 class="text-sm font-bold text-gray-700 dark:text-gray-300">Advanced Settings</h3>

                        <div class="hidden">
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">
                                Creativity Level: {{ number_format($creativity_level, 1) }}
                            </label>
                            <input
                                type="range"
                                wire:model.live="creativity_level"
                                min="1"
                                max="2"
                                step="1"
                                class="w-full h-2 bg-gradient-to-r from-purple-200 to-pink-200 dark:from-purple-700 dark:to-pink-700 rounded-lg appearance-none cursor-pointer slider">
                        </div>

                        <div class="group">
                            <label class="block text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Response Length</label>
                            <select
                                wire:model.live="response_length"
                                class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-purple-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white transition-all group-hover:border-purple-300 dark:group-hover:border-purple-600">
                                <option value="500">Short (500 words)</option>
                                <option value="1000">Medium (1000 words)</option>
                                <option value="1500">Long (1500 words)</option>
                                <option value="2000">Very Long (2000 words)</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <div class="hidden">
                <livewire:chats.token-usage-horizontal/>
            </div>

            <!-- Chat Area with enhanced styling -->
            <div style="z-index: 0!important"
                 class="!z-0 flex-1 flex flex-col bg-white/90 dark:bg-gray-800/90 backdrop-blur-xl rounded-2xl shadow-2xl border border-gray-200/50 dark:border-gray-700/50 relative overflow-hidden">

                <!-- Token Warning Banner -->
                @if(!$canSendMessage)
                    <div class="p-4 border-b border-gray-200 dark:border-gray-700">
                        @if($tokenWarningMessage === 'no_subscription')
                            <div class="bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 border border-blue-200 dark:border-blue-800 rounded-2xl p-4 shadow-sm">
                                <div class="flex items-start">
                                    <svg class="w-6 h-6 text-blue-600 dark:text-blue-400 mr-3 flex-shrink-0" fill="none"
                                         stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <div class="flex-1">
                                        <h3 class="text-sm font-bold text-blue-800 dark:text-blue-200 mb-1">
                                            🎁 Get Started with Free AI Tokens!
                                        </h3>
                                        <p class="text-sm text-blue-700 dark:text-blue-300 mb-3">
                                            You don't have any active token subscription. Activate your free trial
                                            package to start learning with AI assistance!
                                        </p>
                                        <a href="{{ route('token-subscriptions.create') }}"
                                           class="inline-flex items-center px-4 py-2.5 bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 text-white text-sm font-semibold rounded-xl transition-all shadow-md hover:shadow-lg transform hover:-translate-y-0.5">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor"
                                                 viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                            </svg>
                                            Get Free Tokens
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @elseif($tokenWarningMessage === 'depleted')
                            <div class="bg-gradient-to-r from-red-50 to-pink-50 dark:from-red-900/20 dark:to-pink-900/20 border border-red-200 dark:border-red-800 rounded-2xl p-4 shadow-sm">
                                <div class="flex items-start">
                                    <svg class="w-6 h-6 text-red-600 dark:text-red-400 mr-3 flex-shrink-0"
                                         fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                              d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                              clip-rule="evenodd"></path>
                                    </svg>
                                    <div class="flex-1">
                                        <h3 class="text-sm font-bold text-red-800 dark:text-red-200 mb-1">
                                            😔 Tokens Exhausted
                                        </h3>
                                        <p class="text-sm text-red-700 dark:text-red-300 mb-3">
                                            Your token balance is fully depleted. Top up now to continue using AI
                                            features and resume your learning journey!
                                        </p>
                                        <a href="{{ route('token-subscriptions.create') }}"
                                           class="inline-flex items-center px-4 py-2.5 bg-gradient-to-r from-red-600 to-pink-600 hover:from-red-700 hover:to-pink-700 text-white text-sm font-semibold rounded-xl transition-all shadow-md hover:shadow-lg transform hover:-translate-y-0.5">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor"
                                                 viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M12 4v16m8-8H4"></path>
                                            </svg>
                                            Buy More Tokens
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @elseif($tokenWarningMessage === 'expired')
                            <div class="bg-gradient-to-r from-orange-50 to-yellow-50 dark:from-orange-900/20 dark:to-yellow-900/20 border border-orange-200 dark:border-orange-800 rounded-2xl p-4 shadow-sm">
                                <div class="flex items-start">
                                    <svg class="w-6 h-6 text-orange-600 dark:text-orange-400 mr-3 flex-shrink-0"
                                         fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                              d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z"
                                              clip-rule="evenodd"></path>
                                    </svg>
                                    <div class="flex-1">
                                        <h3 class="text-sm font-bold text-orange-800 dark:text-orange-200 mb-1">
                                            ⏰ Subscription Expired
                                        </h3>
                                        <p class="text-sm text-orange-700 dark:text-orange-300 mb-3">
                                            Your token subscription has expired. Purchase a new package to continue
                                            learning with AI assistance.
                                        </p>
                                        <a href="{{ route('token-subscriptions.create') }}"
                                           class="inline-flex items-center px-4 py-2.5 bg-gradient-to-r from-orange-600 to-yellow-600 hover:from-orange-700 hover:to-yellow-700 text-white text-sm font-semibold rounded-xl transition-all shadow-md hover:shadow-lg transform hover:-translate-y-0.5">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor"
                                                 viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                            </svg>
                                            Renew Subscription
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @elseif($tokenWarningMessage === 'insufficient')
                            <div class="bg-gradient-to-r from-yellow-50 to-amber-50 dark:from-yellow-900/20 dark:to-amber-900/20 border border-yellow-200 dark:border-yellow-800 rounded-2xl p-4 shadow-sm">
                                <div class="flex items-start">
                                    <svg class="w-6 h-6 text-yellow-600 dark:text-yellow-400 mr-3 flex-shrink-0"
                                         fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                              d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z"
                                              clip-rule="evenodd"></path>
                                    </svg>
                                    <div class="flex-1">
                                        <h3 class="text-sm font-bold text-yellow-800 dark:text-yellow-200 mb-1">
                                            ⚠️ Insufficient Tokens
                                        </h3>
                                        <p class="text-sm text-yellow-700 dark:text-yellow-300 mb-3">
                                            You don't have enough tokens to continue. Top up now to keep learning
                                            without interruption!
                                        </p>
                                        <a href="{{ route('token-subscriptions.create') }}"
                                           class="inline-flex items-center px-4 py-2.5 bg-gradient-to-r from-yellow-600 to-amber-600 hover:from-yellow-700 hover:to-amber-700 text-white text-sm font-semibold rounded-xl transition-all shadow-md hover:shadow-lg transform hover:-translate-y-0.5">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor"
                                                 viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M12 4v16m8-8H4"></path>
                                            </svg>
                                            Top Up Tokens
                                        </a>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                @endif

                <!-- Error Messages -->
                @if($errors !== null)
                    <div class="p-4 bg-red-50 hidden dark:bg-red-900/20 border-b border-red-200 dark:border-red-800">
                        <div class="flex">
                            <svg class="h-5 w-5 text-red-400 dark:text-red-300" fill="none" stroke="currentColor"
                                 viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                            </svg>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-red-800 dark:text-red-200">There were errors with
                                    your request:</h3>
                                <ul class="mt-2 text-sm text-red-700 dark:text-red-300">
                                    @foreach($errors as $error)
                                        <li>• {{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Chat Messages -->
                <div x-ref="chatContainer"
                     @scroll="checkScroll()"
                     class="flex-1 overflow-y-auto p-6 space-y-6 bg-gradient-to-b from-transparent to-gray-50/50 dark:to-gray-900/50">

                    @if(empty($messages))
                        <div class="text-center py-16">
                            <div class="relative inline-block">
                                <div class="absolute inset-0 bg-gradient-to-r from-blue-500 to-indigo-500 rounded-full blur-2xl opacity-20 animate-pulse"></div>
                                <svg class="relative mx-auto h-20 w-20 text-gray-400 dark:text-gray-500" fill="none"
                                     stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                                          d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                                </svg>
                            </div>
                            <h3 class="mt-6 text-2xl font-bold bg-gradient-to-r from-gray-900 to-gray-600 dark:from-white dark:to-gray-300 bg-clip-text text-transparent">
                                Start Learning
                            </h3>
                            <p class="mt-3 text-gray-600 dark:text-gray-400 text-lg">Ask me anything! I'm here to help you learn.</p>
                        </div>
                    @endif

                    @foreach($messages as $message)
                        <div class="flex {{ $message['role'] === 'user' ? 'justify-end' : 'justify-start' }} animate-fade-in">
                            <div class="max-w-3xl {{ $message['role'] === 'user' ? 'order-1' : '' }}">
                                <div class="flex items-start space-x-3 {{ $message['role'] === 'user' ? 'flex-row-reverse space-x-reverse' : '' }}">
                                    <!-- Avatar -->
                                    <div class="flex-shrink-0">
                                        @if($message['role'] === 'user')
                                            <div class="relative">
                                                <div class="absolute inset-0 bg-purple-500 rounded-full blur-md opacity-40"></div>
                                                <x-avatar class="w-10 h-10 relative ring-2 ring-purple-200 dark:ring-purple-800" text-size="text-sm"
                                                          name="{{auth()->user()->name}}"
                                                          avatar="{{auth()->user()->avatar}}"/>
                                            </div>
                                        @else
                                            <div class="relative">
                                                <div class="absolute inset-0 bg-green-500 rounded-full blur-md opacity-40 animate-pulse"></div>
                                                <div class="h-10 w-10 bg-gradient-to-br from-green-500 to-emerald-600 rounded-full flex items-center justify-center relative shadow-lg ring-2 ring-green-200 dark:ring-green-800">
                                                    <svg class="h-6 w-6 text-white" fill="none" stroke="currentColor"
                                                         viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                              stroke-width="2"
                                                              d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                                                    </svg>
                                                </div>
                                            </div>
                                        @endif
                                    </div>

                                    <!-- Message -->
                                    <div class="flex-1 group">
                                        <div class=" px-4 py-1 rounded-xl shadow-sm hover:shadow-md transition-shadow {{ $message['role'] === 'user'
        ? 'bg-gradient-to-br from-purple-100 to-indigo-100 dark:from-purple-900/40 dark:to-indigo-900/40 text-gray-900 dark:text-gray-100 border border-purple-200 dark:border-purple-800'
        : 'bg-white dark:bg-gray-700/50 text-gray-900 dark:text-gray-100 border border-gray-200 dark:border-gray-600' }}">
                                            <div class="prose prose-sm max-w-none
                    prose-headings:text-gray-900 dark:prose-headings:text-gray-100
                    prose-p:text-gray-900 dark:prose-p:text-gray-100
                    prose-strong:text-gray-900 dark:prose-strong:text-gray-100
                    prose-code:text-gray-900 dark:prose-code:text-gray-100 prose-code:bg-gray-800/10 dark:prose-code:bg-gray-900/50 prose-code:px-1.5 prose-code:py-0.5 prose-code:rounded
                    prose-pre:text-gray-900 dark:prose-pre:text-gray-100 prose-pre:bg-gray-800 dark:prose-pre:bg-gray-900 prose-pre:shadow-inner
                    prose-li:text-gray-900 dark:prose-li:text-gray-100
                    prose-a:text-blue-600 dark:prose-a:text-blue-400 prose-a:no-underline hover:prose-a:underline">
                                                <x-form.markdown-with-math
                                                    :content="$message['content']"></x-form.markdown-with-math>
                                            </div>
                                        </div>
                                        <div class="mt-2 text-xs font-medium text-gray-500 dark:text-gray-400 flex items-center {{ $message['role'] === 'user' ? 'justify-end' : '' }}">
                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            {{ Carbon::parse($message['timestamp'])->diffForHumans() }}
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    @endforeach

                    @if($isLoading)
                        <div class="flex justify-start animate-fade-in">
                            <div class="max-w-3xl">
                                <div class="flex items-start space-x-3">
                                    <div class="flex-shrink-0">
                                        <div class="relative">
                                            <div class="absolute inset-0 bg-green-500 rounded-full blur-md opacity-40 animate-pulse"></div>
                                            <div class="h-10 w-10 bg-gradient-to-br from-green-500 to-emerald-600 rounded-full flex items-center justify-center relative shadow-lg">
                                                <svg class="h-6 w-6 text-white animate-spin" fill="none"
                                                     viewBox="0 0 24 24">
                                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                                            stroke-width="4"></circle>
                                                    <path class="opacity-75" fill="currentColor"
                                                          d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                                </svg>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="px-5 py-4 bg-white dark:bg-gray-700/50 rounded-2xl shadow-sm border border-gray-200 dark:border-gray-600">
                                        <div class="flex items-center space-x-3">
                                            <div class="flex space-x-1.5">
                                                <div class="h-2.5 w-2.5 bg-gradient-to-r from-green-400 to-emerald-500 rounded-full animate-bounce"></div>
                                                <div class="h-2.5 w-2.5 bg-gradient-to-r from-green-400 to-emerald-500 rounded-full animate-bounce"
                                                     style="animation-delay: 0.1s"></div>
                                                <div class="h-2.5 w-2.5 bg-gradient-to-r from-green-400 to-emerald-500 rounded-full animate-bounce"
                                                     style="animation-delay: 0.2s"></div>
                                            </div>
                                            <span class="text-sm font-medium text-gray-600 dark:text-gray-400">AI is thinking...</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Scroll to Bottom Button -->
                <button
                    x-show="showScrollToBottom"
                    x-transition
                    @click="scrollToBottom()"
                    class="absolute bottom-24 right-6 p-3 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-full shadow-xl hover:shadow-2xl hover:from-blue-700 hover:to-indigo-700 transition-all transform hover:scale-110 z-10">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M19 14l-7 7m0 0l-7-7m7 7V3"></path>
                    </svg>
                </button>

                <!-- Message Input -->
                <div class="border-t border-gray-200 dark:border-gray-700 p-6 bg-gradient-to-r from-gray-50/50 to-blue-50/50 dark:from-gray-800/50 dark:to-gray-700/50 {{ !$canSendMessage ? 'opacity-50 pointer-events-none' : '' }}">
                    <div class="flex space-x-3">
                        <div class="flex-1">
            <textarea
                wire:model="message"
                @keydown.enter.prevent.stop="if (!$event.shiftKey && {{ $canSendMessage ? 'true' : 'false' }}) { $wire.sendMessage(); }"
                rows="3"
                class="w-full px-5 py-4 border-2 border-gray-300 dark:border-gray-600 rounded-2xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 bg-white dark:bg-gray-700 text-gray-900 dark:text-white resize-none transition-all shadow-sm hover:shadow-md placeholder-gray-400 dark:placeholder-gray-500"
                placeholder="{{ $canSendMessage ? 'Ask me anything about your learning topic or uploaded file... (Press Enter to send, Shift+Enter for new line)' : 'Please activate or top up your token subscription to continue...' }}"
                :disabled="$wire.isLoading || !{{ $canSendMessage ? 'true' : 'false' }}"></textarea>
                        </div>
                        <div class="flex flex-col justify-center space-y-2 my-auto">
                            <!-- Hidden file input -->
                            <input
                                type="file"
                                wire:model="uploadedFile"
                                x-ref="fileInput"
                                class="hidden"
                                {{ !$canSendMessage ? 'disabled' : '' }}
                            >

                            <!-- File upload button -->
                            <button
                                type="button"
                                x-on:click="$refs.fileInput.click()"
                                class="group hidden p-3 text-gray-500 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400 rounded-xl hover:bg-gradient-to-r hover:from-blue-50 hover:to-indigo-50 dark:hover:from-blue-900/20 dark:hover:to-indigo-900/20 transition-all disabled:opacity-50 disabled:cursor-not-allowed shadow-sm hover:shadow-md"
                                title="Upload file for evaluation"
                                {{ !$canSendMessage ? 'disabled' : '' }}>
                                <svg class="h-5 w-5 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path>
                                </svg>
                            </button>

                            <!-- Send button -->
                            <button
                                wire:click="sendMessage"
                                :disabled="$wire.isLoading || (!$wire.message.trim() && !$wire.fileContent) || !{{ $canSendMessage ? 'true' : 'false' }}"
                                class="group p-3 text-white rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 hover:from-blue-700 hover:to-indigo-700 disabled:opacity-50 disabled:cursor-not-allowed transition-all shadow-md hover:shadow-lg transform hover:scale-105">
                                <svg wire:loading.remove wire:target="sendMessage" class="h-5 w-5 rotate-90 ml-0.5 group-hover:translate-x-0.5 group-hover:-translate-y-0.5 transition-transform"
                                     fill="none"
                                     stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                                </svg>
                                <svg wire:loading wire:target="sendMessage" class="h-5 w-5 animate-spin" fill="none"
                                     viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                            stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor"
                                          d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Auto-scroll to bottom when new messages arrive -->
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const chatContainer = document.querySelector('[x-ref="chatContainer"]');

                const observer = new MutationObserver(function (mutations) {
                    mutations.forEach(function (mutation) {
                        if (mutation.addedNodes.length > 0) {
                            const isNearBottom = chatContainer.scrollHeight - chatContainer.scrollTop <= chatContainer.clientHeight + 100;
                            if (isNearBottom) {
                                setTimeout(() => {
                                    chatContainer.scrollTop = chatContainer.scrollHeight;
                                }, 100);
                            }
                        }
                    });
                });

                if (chatContainer) {
                    observer.observe(chatContainer, {childList: true, subtree: true});
                }
            });
        </script>

        <style>
            .slider::-webkit-slider-thumb {
                appearance: none;
                height: 20px;
                width: 20px;
                border-radius: 50%;
                background: linear-gradient(135deg, #3B82F6 0%, #6366F1 100%);
                cursor: pointer;
                box-shadow: 0 2px 8px rgba(59, 130, 246, 0.4);
                transition: all 0.3s ease;
            }

            .slider::-webkit-slider-thumb:hover {
                transform: scale(1.1);
                box-shadow: 0 4px 12px rgba(59, 130, 246, 0.6);
            }

            .slider::-moz-range-thumb {
                height: 20px;
                width: 20px;
                border-radius: 50%;
                background: linear-gradient(135deg, #3B82F6 0%, #6366F1 100%);
                cursor: pointer;
                border: none;
                box-shadow: 0 2px 8px rgba(59, 130, 246, 0.4);
                transition: all 0.3s ease;
            }

            .slider::-moz-range-thumb:hover {
                transform: scale(1.1);
                box-shadow: 0 4px 12px rgba(59, 130, 246, 0.6);
            }

            .prose h1, .prose h2, .prose h3, .prose h4, .prose h5, .prose h6 {
                margin-top: 1.25em;
                margin-bottom: 0.75em;
                font-weight: 700;
            }

            .prose p {
                margin-top: 0.75em;
                margin-bottom: 0.75em;
                line-height: 1.75;
            }

            .prose ul, .prose ol {
                margin-top: 0.75em;
                margin-bottom: 0.75em;
            }

            @keyframes fade-in {
                from {
                    opacity: 0;
                    transform: translateY(10px);
                }
                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }

            .animate-fade-in {
                animation: fade-in 0.3s ease-out;
            }

            /* Custom scrollbar */
            ::-webkit-scrollbar {
                width: 8px;
                height: 8px;
            }

            ::-webkit-scrollbar-track {
                background: transparent;
            }

            ::-webkit-scrollbar-thumb {
                background: linear-gradient(135deg, #3B82F6 0%, #6366F1 100%);
                border-radius: 10px;
            }

            ::-webkit-scrollbar-thumb:hover {
                background: linear-gradient(135deg, #2563EB 0%, #4F46E5 100%);
            }

            /* Dark mode scrollbar */
            .dark ::-webkit-scrollbar-thumb {
                background: linear-gradient(135deg, #60A5FA 0%, #818CF8 100%);
            }

            .dark ::-webkit-scrollbar-thumb:hover {
                background: linear-gradient(135deg, #3B82F6 0%, #6366F1 100%);
            }
        </style>
    </div>
</div>
