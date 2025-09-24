@php use Carbon\Carbon; @endphp
<div x-data="{
    darkMode: localStorage.getItem('darkMode') === 'true' || (!localStorage.getItem('darkMode') && window.matchMedia('(prefers-color-scheme: dark)').matches),
    newSubtopic: '',
    showScrollToBottom: false,
    showHistory: $wire.showHistory,

    toggleDarkMode() {
        this.darkMode = !this.darkMode;
        localStorage.setItem('darkMode', this.darkMode);
    },

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
     :class="{ 'dark': darkMode }"
     class="min-h-screen h-auto bg-gray-50 dark:bg-gray-900 transition-colors duration-200">

    <!-- Overlay for mobile when sidebars are open -->
    <div x-show="showHistory || $wire.showParameters"
         @click="showHistory = false; $wire.showParameters = false;"
         class="fixed inset-0 bg-black bg-opacity-50 z-30 lg:hidden"></div>

    <!-- Header -->
    <header class="bg-white dark:bg-gray-800 shadow-sm border-b border-gray-200 dark:border-gray-700">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16">
                <div class="flex items-center space-x-4">
                    <div class="flex-shrink-0">
                        <svg class="h-8 w-8 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor"
                             viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-xl font-semibold text-gray-900 dark:text-white">Educational AI Assistant</h1>
                        <p class="text-sm text-gray-500 dark:text-gray-400">Personalized learning support</p>
                    </div>
                </div>

                <div class="flex items-center space-x-3">
                    <!-- History Toggle -->
                    <button
                        @click="toggleHistory()"
                        class="px-3 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">
                        <svg class="h-4 w-4 mr-1 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        History
                    </button>

                    <!-- Parameters Toggle -->
                    <button
                        wire:click="toggleParameters"
                        class="px-3 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">
                        <svg class="h-4 w-4 mr-1 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4"></path>
                        </svg>
                        Settings
                    </button>

                    <!-- Clear Chat -->
                    <button
                        wire:click="newConversation"
                        class="px-3 py-2 text-sm font-medium text-red-700 dark:text-red-400 bg-red-50 dark:bg-red-900/20 rounded-lg hover:bg-red-100 dark:hover:bg-red-900/30 transition-colors">
                        <svg class="h-4 w-4 mr-1 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                        New Chat
                    </button>

                    <!-- Dark Mode Toggle -->
                    <button
                        @click="toggleDarkMode()"
                        class="p-2 text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-lg transition-colors">
                        <svg x-show="!darkMode" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path>
                        </svg>
                        <svg x-show="darkMode" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </header>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
        <div class="grid grid-cols-1 gap-6">
            <!-- Conversation History Sidebar -->
            <div x-show="showHistory"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 -translate-x-full"
                 x-transition:enter-end="opacity-100 translate-x-0"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-x-0"
                 x-transition:leave-end="opacity-0 -translate-x-full"
                 class="fixed lg:relative inset-y-0 left-0 z-40 w-80 bg-white dark:bg-gray-800 rounded-r-lg shadow-xl lg:shadow-none border-r border-gray-200 dark:border-gray-700 overflow-y-auto h-[calc(100vh-5rem)] transform lg:translate-x-0 lg:opacity-100 lg:z-auto lg:rounded-none lg:border-0">
                <div class="p-4">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Chat History</h2>
                        <button @click="toggleHistory()"
                                class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>

                    <div class="space-y-2">
                        @forelse($conversationHistory as $conversation)
                            <div
                                class="group flex items-center justify-between p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 cursor-pointer"
                                wire:click="loadConversation('{{ $conversation['id'] }}')">
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-900 dark:text-white truncate">
                                        {{ $conversation['title'] }}
                                    </p>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">
                                        {{ Carbon::parse($conversation['created_at'])->format('M j, Y g:i A') }}
                                    </p>
                                </div>
                                <button wire:click.stop="deleteConversation('{{ $conversation['id'] }}')"
                                        wire:confirm="Are you sure you want to delete this conversation?"
                                        class="opacity-0 group-hover:opacity-100 text-red-500 hover:text-red-700">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                </button>
                            </div>
                        @empty
                            <div class="text-center py-8">
                                <svg class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-500" fill="none"
                                     stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                                </svg>
                                <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No conversations</h3>
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Start a new chat to see it
                                    here</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Parameters Panel -->
            <div x-show="$wire.showParameters"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 -translate-x-full"
                 x-transition:enter-end="opacity-100 translate-x-0"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-x-0"
                 x-transition:leave-end="opacity-0 -translate-x-full"
                 class="fixed lg:relative inset-y-0 left-0 z-40 w-80 bg-white dark:bg-gray-800 rounded-r-lg shadow-xl lg:shadow-none border-r border-gray-200 dark:border-gray-700 overflow-y-auto h-[calc(100vh-5rem)] transform lg:translate-x-0 lg:opacity-100 lg:z-auto lg:rounded-none lg:border-0">
                <div class="p-6">
                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Learning Parameters</h2>
                        <button
                            wire:click="resetParameters"
                            class="text-sm text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300">
                            Reset
                        </button>
                    </div>

                    <!-- Basic Info -->
                    <div class="space-y-4 mb-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Age</label>
                            <input
                                type="number"
                                wire:model.live="age"
                                min="5"
                                max="100"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                                placeholder="Enter age">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Academic
                                Level</label>
                            <select
                                wire:model.live="academic_level"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                                <option value="">Select level</option>
                                <option value="elementary">Elementary</option>
                                <option value="middle_school">Middle School</option>
                                <option value="high_school">High School</option>
                                <option value="college">College</option>
                                <option value="graduate">Graduate</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Academic
                                Group</label>
                            <input
                                type="text"
                                wire:model.live="academic_group"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                                placeholder="e.g., STEM, Liberal Arts">
                        </div>
                    </div>

                    <!-- Subject & Topics -->
                    <div class="space-y-4 mb-6">
                        <div>
                            <label
                                class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Subject</label>
                            <select
                                wire:model.live="subject"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                                <option value="">Select subject</option>
                                @foreach($availableSubjects as $subject => $topics)
                                    <option
                                        value="{{ $subject }}">{{ ucwords(str_replace('_', ' ', $subject)) }}</option>
                                @endforeach
                            </select>
                        </div>

                        @if($availableTopics)
                            <div>
                                <label
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Topics</label>
                                <div class="grid grid-cols-1 gap-2">
                                    @foreach($availableTopics as $topic)
                                        <label class="flex items-center">
                                            <input
                                                type="checkbox"
                                                wire:click="addTopic('{{ $topic }}')"
                                                @if(in_array($topic, $topics)) checked @endif
                                                class="rounded border-gray-300 dark:border-gray-600 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 dark:focus:ring-blue-800 dark:focus:ring-opacity-40 dark:bg-gray-700">
                                            <span
                                                class="ml-2 text-sm text-gray-700 dark:text-gray-300">{{ ucwords(str_replace('_', ' ', $topic)) }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <!-- Custom Subtopics -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Custom
                                Subtopics</label>
                            <div class="flex space-x-2 mb-2">
                                <input
                                    type="text"
                                    x-model="newSubtopic"
                                    @keydown.enter="addSubtopic()"
                                    class="flex-1 px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white text-sm"
                                    placeholder="Add subtopic">
                                <button
                                    @click="addSubtopic()"
                                    class="px-3 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors text-sm">
                                    Add
                                </button>
                            </div>
                            @if(!empty($subtopics))
                                <div class="flex flex-wrap gap-2">
                                    @foreach($subtopics as $index => $subtopic)
                                        <span
                                            class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200">
                                            {{ $subtopic }}
                                            <button
                                                wire:click="removeSubtopic({{ $index }})"
                                                class="ml-1 text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300">
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
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Learning
                                Style</label>
                            <select
                                wire:model.live="learning_style"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                                <option value="">Select style</option>
                                <option value="visual">Visual</option>
                                <option value="auditory">Auditory</option>
                                <option value="kinesthetic">Kinesthetic</option>
                                <option value="reading">Reading/Writing</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Difficulty
                                Level</label>
                            <select
                                wire:model.live="difficulty"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                                <option value="beginner">Beginner</option>
                                <option value="intermediate">Intermediate</option>
                                <option value="advanced">Advanced</option>
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Response
                                Format</label>
                            <select
                                wire:model.live="response_format"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                                <option value="detailed">Detailed</option>
                                <option value="concise">Concise</option>
                                <option value="interactive">Interactive</option>
                            </select>
                        </div>
                    </div>

                    <!-- Accommodations -->
                    <div class="space-y-4 mb-6">
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Special
                            Accommodations</label>
                        <div class="space-y-2">
                            <label class="flex items-center">
                                <input
                                    type="checkbox"
                                    wire:click="addAccommodation('simplified_language')"
                                    @if(in_array('simplified_language', $accommodations)) checked @endif
                                    class="rounded border-gray-300 dark:border-gray-600 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 dark:focus:ring-blue-800 dark:focus:ring-opacity-40 dark:bg-gray-700">
                                <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Simplified Language</span>
                            </label>
                            <label class="flex items-center">
                                <input
                                    type="checkbox"
                                    wire:click="addAccommodation('step_by_step')"
                                    @if(in_array('step_by_step', $accommodations)) checked @endif
                                    class="rounded border-gray-300 dark:border-gray-600 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 dark:focus:ring-blue-800 dark:focus:ring-opacity-40 dark:bg-gray-700">
                                <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Step-by-Step</span>
                            </label>
                            <label class="flex items-center">
                                <input
                                    type="checkbox"
                                    wire:click="addAccommodation('examples_heavy')"
                                    @if(in_array('examples_heavy', $accommodations)) checked @endif
                                    class="rounded border-gray-300 dark:border-gray-600 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 dark:focus:ring-blue-800 dark:focus:ring-opacity-40 dark:bg-gray-700">
                                <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">More Examples</span>
                            </label>
                        </div>
                    </div>

                    <!-- Advanced Settings -->
                    <div class="space-y-4">
                        <h3 class="text-sm font-medium text-gray-700 dark:text-gray-300">Advanced Settings</h3>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Creativity Level: {{ number_format($creativity_level, 1) }}
                            </label>
                            <input
                                type="range"
                                wire:model.live="creativity_level"
                                min="0"
                                max="1"
                                step="0.1"
                                class="w-full h-2 bg-gray-200 dark:bg-gray-700 rounded-lg appearance-none cursor-pointer slider">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Response
                                Length</label>
                            <select
                                wire:model.live="response_length"
                                class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                                <option value="500">Short (500 words)</option>
                                <option value="1000">Medium (1000 words)</option>
                                <option value="1500">Long (1500 words)</option>
                                <option value="2000">Very Long (2000 words)</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Chat Area -->
            <div
                class="flex flex-col bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 relative">
                <!-- Error Messages -->
                @if(!empty($errors))
                    <div class="p-4 bg-red-50 dark:bg-red-900/20 border-b border-red-200 dark:border-red-800">
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
                     class="flex-1 overflow-y-auto p-4 space-y-4">

                    @if(empty($messages))
                        <div class="text-center py-12">
                            <svg class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-500" fill="none"
                                 stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
                                      d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                            </svg>
                            <h3 class="mt-4 text-lg font-medium text-gray-900 dark:text-white">Start Learning</h3>
                            <p class="mt-2 text-gray-500 dark:text-gray-400">Ask me anything! I'm here to help you
                                learn.</p>
                        </div>
                    @endif

                    @foreach($messages as $message)
                        <div class="flex {{ $message['role'] === 'user' ? 'justify-end' : 'justify-start' }}">
                            <div class="max-w-3xl {{ $message['role'] === 'user' ? 'order-1' : '' }}">
                                <div
                                    class="flex items-start space-x-3 {{ $message['role'] === 'user' ? 'flex-row-reverse space-x-reverse' : '' }}">
                                    <!-- Avatar -->
                                    <div class="flex-shrink-0">
                                        @if($message['role'] === 'user')
                                            <x-avatar class="w-10 h-10" text-size="text-sm"
                                                      name="{{auth()->user()->name}}"
                                                      avatar="{{auth()->user()->avatar}}"/>
                                        @else
                                            <div
                                                class="h-8 w-8 bg-green-600 rounded-full flex items-center justify-center">
                                                <svg class="h-5 w-5 text-white" fill="none" stroke="currentColor"
                                                     viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                          stroke-width="2"
                                                          d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                                                </svg>
                                            </div>
                                        @endif
                                    </div>

                                    <!-- Message -->
                                    <div class="flex-1">
                                        <div class="px-4 py-3 rounded-lg {{ $message['role'] === 'user'
        ? 'bg-purple-100 dark:bg-purple-900/30 text-gray-800 dark:text-gray-100'
        : 'bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-100' }}">
                                            <div class="prose prose-sm max-w-none {{ $message['role'] === 'user'
            ? 'prose-invert'
            : 'dark:prose-invert' }}">
                                                <x-form.markdown-with-math
                                                    :content="$message['content']"></x-form.markdown-with-math>
                                            </div>
                                        </div>
                                        <div
                                            class="mt-1 text-xs text-gray-500 dark:text-gray-400 {{ $message['role'] === 'user' ? 'text-right' : '' }}">
                                            {{ Carbon::parse($message['timestamp'])->diffForHumans() }}
                                        </div>
                                    </div>

                                </div>
                            </div>
                        </div>
                    @endforeach

                    @if($isLoading)
                        <div class="flex justify-start">
                            <div class="max-w-3xl">
                                <div class="flex items-start space-x-3">
                                    <div class="flex-shrink-0">
                                        <div class="h-8 w-8 bg-green-600 rounded-full flex items-center justify-center">
                                            <svg class="h-5 w-5 text-white animate-spin" fill="none"
                                                 viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                                        stroke-width="4"></circle>
                                                <path class="opacity-75" fill="currentColor"
                                                      d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="px-4 py-3 bg-gray-100 dark:bg-gray-700 rounded-lg">
                                        <div class="flex items-center space-x-2">
                                            <div class="flex space-x-1">
                                                <div
                                                    class="h-2 w-2 bg-gray-400 dark:bg-gray-500 rounded-full animate-bounce"></div>
                                                <div
                                                    class="h-2 w-2 bg-gray-400 dark:bg-gray-500 rounded-full animate-bounce"
                                                    style="animation-delay: 0.1s"></div>
                                                <div
                                                    class="h-2 w-2 bg-gray-400 dark:bg-gray-500 rounded-full animate-bounce"
                                                    style="animation-delay: 0.2s"></div>
                                            </div>
                                            <span
                                                class="text-sm text-gray-500 dark:text-gray-400">AI is thinking...</span>
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
                    @click="scrollToBottom()"
                    class="absolute bottom-20 right-4 p-2 bg-blue-600 text-white rounded-full shadow-lg hover:bg-blue-700 transition-colors">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M19 14l-7 7m0 0l-7-7m7 7V3"></path>
                    </svg>
                </button>

                <!-- Message Input -->
                <div class="border-t border-gray-200 dark:border-gray-700 p-4">
                    <div class="flex space-x-3">
                        <div class="flex-1">
            <textarea
                wire:model="message"
                @keydown.enter.prevent.stop="if (!$event.shiftKey) { $wire.sendMessage(); }"
                rows="3"
                class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white resize-none"
                placeholder="Ask me anything about your learning topic or uploaded file... (Press Enter to send, Shift+Enter for new line)"
                :disabled="$wire.isLoading"></textarea>
                        </div>
                        <div class="flex flex-col justify-end space-y-2">
                            <!-- Hidden file input -->
                            <input
                                type="file"
                                wire:model="uploadedFile"
                                x-ref="fileInput"
                                class="hidden"
                            >

                            <!-- File upload button with paperclip icon -->
                            <button
                                type="button"
                                x-on:click="$refs.fileInput.click()"
                                class="p-2 text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-300 rounded-full hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors"
                                title="Upload file for evaluation">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path>
                                </svg>
                            </button>

                            <!-- Send button with paper airplane icon -->
                            <button
                                wire:click="sendMessage"
                                :disabled="$wire.isLoading || (!$wire.message.trim() && !$wire.fileContent)"
                                class="p-2 text-white rounded-full hover:bg-blue-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors bg-blue-600">
                                <svg wire:loading.remove wire:target="sendMessage" class="h-5 w-5" fill="none"
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

                    <!-- Display file info when uploaded -->
                    @if($fileName)
                        <div
                            class="mt-2 px-3 py-2 bg-blue-50 dark:bg-blue-900/30 border border-blue-200 dark:border-blue-800 rounded-lg">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <svg class="h-5 w-5 text-blue-500 dark:text-blue-400 mr-2" fill="none"
                                         stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    <span
                                        class="text-sm font-medium text-blue-800 dark:text-blue-200">{{ $fileName }}</span>
                                </div>
                                <span class="text-xs text-blue-600 dark:text-blue-400">Ready for evaluation</span>
                            </div>
                        </div>
                    @endif

                    <!-- File upload error message -->
                    {{--    @if($errors->has('uploadedFile'))
                            <p class="mt-2 text-sm text-red-600 dark:text-red-500">{{ $errors->first('uploadedFile') }}</p>
                        @endif--}}
                </div>
            </div>
        </div>
    </div>

    <!-- Auto-scroll to bottom when new messages arrive -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const chatContainer = document.querySelector('[x-ref="chatContainer"]');

            // Create a MutationObserver to watch for new messages
            const observer = new MutationObserver(function (mutations) {
                mutations.forEach(function (mutation) {
                    if (mutation.addedNodes.length > 0) {
                        // Check if we should auto-scroll (user is near bottom)
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
            background: #3B82F6;
            cursor: pointer;
        }

        .slider::-moz-range-thumb {
            height: 20px;
            width: 20px;
            border-radius: 50%;
            background: #3B82F6;
            cursor: pointer;
            border: none;
        }

        .prose h1, .prose h2, .prose h3, .prose h4, .prose h5, .prose h6 {
            margin-top: 1em;
            margin-bottom: 0.5em;
        }

        .prose p {
            margin-top: 0.5em;
            margin-bottom: 0.5em;
        }

        .prose ul, .prose ol {
            margin-top: 0.5em;
            margin-bottom: 0.5em;
        }
    </style>
</div>
