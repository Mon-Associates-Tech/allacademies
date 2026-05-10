<div class="min-h-screen bg-gray-50 dark:bg-gray-900">
    @if($showSectionInfo)
        <div class="flex items-center justify-center min-h-screen px-4 py-8">
            <div class="bg-white dark:bg-gray-800 shadow-lg rounded-lg max-w-lg w-full">
                <!-- Header -->
                <div class="border-b border-gray-200 dark:border-gray-700 px-6 py-5">
                    <div class="flex items-center justify-between">
                        <h2 class="text-xl font-semibold text-gray-900 dark:text-white">{{ $this->section->title }}</h2>
                        <button wire:click="toggleSectionInfo" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                </div>

                <div class="px-6 py-6 space-y-6">
                    @if($this->section->description)
                        <div>
                            <h3 class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-2">Description</h3>
                            <p class="text-sm text-gray-700 dark:text-gray-300 leading-relaxed">{{ $this->section->description }}</p>
                        </div>
                    @endif

                    <!-- Stats -->
                    <div class="space-y-3">
                        <div class="flex items-center justify-between py-2 border-b border-gray-100 dark:border-gray-700">
                            <span class="text-sm text-gray-600 dark:text-gray-400">Questions</span>
                            <span class="font-semibold text-gray-900 dark:text-white">{{ $this->questions->count() }}</span>
                        </div>
                        @if($this->section->time_limit_minutes)
                            <div class="flex items-center justify-between py-2 border-b border-gray-100 dark:border-gray-700">
                                <span class="text-sm text-gray-600 dark:text-gray-400">Time Limit</span>
                                <span class="font-semibold text-gray-900 dark:text-white">{{ $this->section->time_limit_minutes }} minutes</span>
                            </div>
                        @endif
                        <div class="flex items-center justify-between py-2 border-b border-gray-100 dark:border-gray-700">
                            <span class="text-sm text-gray-600 dark:text-gray-400">Section</span>
                            <span class="font-semibold text-gray-900 dark:text-white">{{ $sectionIndex + 1 }} of {{ $this->exam->sections->count() }}</span>
                        </div>
                        <div class="flex items-center justify-between py-2">
                            <span class="text-sm text-gray-600 dark:text-gray-400">Type</span>
                            <span class="font-semibold text-gray-900 dark:text-white">{{ str_replace('_', ' ', ucfirst($this->section->question_type)) }}</span>
                        </div>
                    </div>

                    @if($this->section->instructions)
                        <div class="bg-gray-50 dark:bg-gray-700/50 rounded-lg p-4">
                            <h4 class="font-medium text-gray-900 dark:text-white mb-2 text-sm">Instructions</h4>
                            <div class="text-sm text-gray-700 dark:text-gray-300 leading-relaxed whitespace-pre-line">{{ $this->section->instructions }}</div>
                        </div>
                    @endif

                    <!-- Start Button -->
                    <div class="flex justify-center pt-2">
                        <button wire:click="startSection" 
                                class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2.5 rounded-lg font-medium transition-colors">
                            Start Section
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @else
        @if($this->questions->count() === 0)
            <div class="max-w-4xl mx-auto px-4 py-8">
                <div class="bg-white dark:bg-gray-800 shadow-lg rounded-lg overflow-hidden">
                    <div class="bg-gradient-to-r from-indigo-600 to-blue-600 px-8 py-6">
                        <h1 class="text-2xl font-bold text-white">{{ $this->exam->title }}</h1>
                        <p class="text-indigo-100 mt-1">Section {{ $sectionIndex + 1 }}: {{ $this->section->title }}</p>
                    </div>

                    <div class="p-8 space-y-6">
                        @if($this->section->description)
                            <div>
                                <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Section Description</h3>
                                <p class="text-gray-700 dark:text-gray-300">{{ $this->section->description }}</p>
                            </div>
                        @endif

                        @if($this->section->instructions)
                            <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-4">
                                <h3 class="font-semibold text-blue-900 dark:text-blue-100 mb-2 text-sm">Section Instructions</h3>
                                <div class="text-sm text-blue-800 dark:text-blue-200 whitespace-pre-line">{{ $this->section->instructions }}</div>
                            </div>
                        @endif

                        <div class="grid md:grid-cols-3 gap-4">
                            <div class="bg-gray-100 dark:bg-gray-700 rounded-lg p-4">
                                <p class="text-xs text-gray-600 dark:text-gray-400 mb-1">Question Type</p>
                                <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ str_replace('_', ' ', ucfirst($this->section->question_type)) }}</p>
                            </div>
                            @if($this->section->time_limit_minutes)
                                <div class="bg-gray-100 dark:bg-gray-700 rounded-lg p-4">
                                    <p class="text-xs text-gray-600 dark:text-gray-400 mb-1">Time Limit</p>
                                    <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ $this->section->time_limit_minutes }} minutes</p>
                                </div>
                            @endif
                            <div class="bg-gray-100 dark:bg-gray-700 rounded-lg p-4">
                                <p class="text-xs text-gray-600 dark:text-gray-400 mb-1">Expected Questions</p>
                                <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ $this->section->question_count }}</p>
                            </div>
                        </div>

                        <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg p-6">
                            <div class="flex items-start">
                                <svg class="h-6 w-6 text-yellow-600 mt-0.5 mr-3 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                </svg>
                                <div>
                                    <h3 class="text-lg font-semibold text-yellow-900 dark:text-yellow-100 mb-1">No Questions Available</h3>
                                    <p class="text-sm text-yellow-700 dark:text-yellow-300">This section does not have any questions yet. The exam administrator may still be preparing the questions. Please check back later or contact support.</p>
                                </div>
                            </div>
                        </div>

                        @if($this->exam->instructions)
                            <div class="bg-gray-50 dark:bg-gray-700/50 border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                                <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-2">General Exam Instructions</h3>
                                <div class="text-sm text-gray-700 dark:text-gray-300 whitespace-pre-line">{{ $this->exam->instructions }}</div>
                            </div>
                        @endif

                        <div class="flex justify-between items-center pt-4 border-t border-gray-200 dark:border-gray-700">
                            <a href="{{ route('examinations-hub.take.start', $this->exam) }}" class="px-6 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700">
                                ← Back to Exam Overview
                            </a>
                            @if($sectionIndex < $this->exam->sections->count() - 1)
                                <a href="{{ route('examinations-hub.take.section', [$this->exam, $sectionIndex + 1]) }}" class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                                    Skip to Next Section →
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @else
        <div class="flex flex-col h-screen">
            <!-- Top Header Bar -->
            <div class="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 px-6 py-4 flex-shrink-0">
                <div class="max-w-7xl mx-auto flex items-center justify-between">
                    <div>
                        <h2 class="text-xl font-bold text-gray-900 dark:text-white">{{ $this->exam->title }}</h2>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                            {{ $this->section->title }} • Question {{ $currentQuestionIndex + 1 }} of {{ $this->questions->count() }}
                        </p>
                    </div>
                    <div class="flex items-center gap-4">
                        @if($timeRemaining !== null)
                            <div class="flex items-center gap-2 px-4 py-2 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg">
                                <svg class="h-5 w-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span class="font-mono font-semibold text-red-700 dark:text-red-400">{{ $timeRemaining }} min</span>
                            </div>
                        @endif
                        <button wire:click="toggleSectionInfo" class="text-sm text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 font-medium">
                            Section Info
                        </button>
                    </div>
                </div>
            </div>

            <!-- Main Content Area -->
            <div class="flex-1 overflow-y-auto bg-gray-50 dark:bg-gray-900">
                <div class="max-w-4xl mx-auto px-6 py-8">
                    @php
                        $question = $this->questions[$currentQuestionIndex];
                    @endphp

                    <!-- Question Card -->
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-8 mb-6" wire:key="question-container-{{ $question->id }}">
                        <div class="flex items-start justify-between mb-6">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-indigo-100 dark:bg-indigo-900 rounded-lg flex items-center justify-center">
                                    <span class="text-lg font-bold text-indigo-600 dark:text-indigo-400">{{ $currentQuestionIndex + 1 }}</span>
                                </div>
                                <div>
                                    <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400">Question {{ $currentQuestionIndex + 1 }}</h3>
                                    <p class="text-xs text-gray-400 dark:text-gray-500">{{ $question->marks }} {{ $question->marks === 1 ? 'mark' : 'marks' }}</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="px-3 py-1 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-full text-xs font-medium">
                                    {{ str_replace('_', ' ', ucfirst($question->type)) }}
                                </span>
                            </div>
                        </div>

                        <div class="text-gray-800 dark:text-gray-200 text-lg leading-relaxed mb-8" wire:key="question-text-{{ $question->id }}">
                            <x-form.markdown-with-math :content="$question->getFormattedQuestion()" class="prose dark:prose-invert max-w-none" />
                        </div>

                        @if($question->isMultipleChoice())
                            <div class="space-y-2" wire:key="options-{{ $question->id }}">
                                @foreach($question->getOptionsForDisplay() as $key => $optionText)
                                    <label wire:key="opt-{{ $question->id }}-{{ $key }}" 
                                           class="flex items-start gap-3 p-3 border rounded-lg cursor-pointer transition-all hover:shadow-sm
                                        @if(isset($responses[$question->id]) && $responses[$question->id] === $key)
                                            border-indigo-500 bg-indigo-50 dark:bg-indigo-900/20 shadow-sm
                                        @else
                                            border-gray-200/60 dark:border-gray-700/60 hover:border-indigo-300/80
                                        @endif">
                                        <input 
                                            type="radio" 
                                            name="question_{{ $question->id }}" 
                                            value="{{ $key }}"
                                            wire:model.live="responses.{{ $question->id }}"
                                            class="h-5 w-5 text-indigo-600 focus:ring-indigo-500 flex-shrink-0 mt-0.5"
                                        >
                                        <div class="flex-1 flex items-start gap-2">
                                            <span class="inline-flex items-center justify-center min-w-[24px] h-6 bg-gray-100 dark:bg-gray-700 rounded font-semibold text-gray-900 dark:text-white text-sm flex-shrink-0">{{ $key }}</span>
                                            <div class="flex-1">
                                                <x-form.markdown-with-math :content="$optionText" class="text-gray-800 dark:text-gray-200" />
                                            </div>
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                        @elseif($question->isTrueFalse())
                            <div class="grid grid-cols-2 gap-3">
                                <label class="flex items-center justify-center gap-3 p-4 border rounded-lg cursor-pointer transition-all hover:shadow-sm
                                    {{ isset($responses[$question->id]) && $responses[$question->id] === 'True' 
                                        ? 'border-indigo-500 bg-indigo-50 dark:bg-indigo-900/20 shadow-sm' 
                                        : 'border-gray-200/60 dark:border-gray-700/60 hover:border-indigo-300/80' 
                                    }}">
                                    <input 
                                        type="radio" 
                                        name="question_{{ $question->id }}" 
                                        value="True"
                                        wire:model.live="responses.{{ $question->id }}"
                                        class="h-5 w-5 text-indigo-600 focus:ring-indigo-500 flex-shrink-0"
                                    >
                                    <span class="text-base font-semibold text-gray-700 dark:text-gray-300">True</span>
                                </label>
                                <label class="flex items-center justify-center gap-3 p-4 border rounded-lg cursor-pointer transition-all hover:shadow-sm
                                    {{ isset($responses[$question->id]) && $responses[$question->id] === 'False' 
                                        ? 'border-indigo-500 bg-indigo-50 dark:bg-indigo-900/20 shadow-sm' 
                                        : 'border-gray-200/60 dark:border-gray-700/60 hover:border-indigo-300/80' 
                                    }}">
                                    <input 
                                        type="radio" 
                                        name="question_{{ $question->id }}" 
                                        value="False"
                                        wire:model.live="responses.{{ $question->id }}"
                                        class="h-5 w-5 text-indigo-600 focus:ring-indigo-500 flex-shrink-0"
                                    >
                                    <span class="text-base font-semibold text-gray-700 dark:text-gray-300">False</span>
                                </label>
                            </div>
                        @else
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">Your Answer</label>
                                <textarea 
                                    wire:model.live.debounce.500ms="responses.{{ $question->id }}"
                                    rows="10"
                                    class="w-full px-4 py-3 border-2 border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white transition-colors"
                                    placeholder="Type your answer here..."
                                ></textarea>
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-2">Your answer is automatically saved as you type.</p>
                            </div>
                        @endif
                    </div>

                    <!-- Question Navigator -->
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 mb-6">
                        <div class="flex items-center justify-between mb-4">
                            <h4 class="font-semibold text-gray-900 dark:text-white">Question Navigator</h4>
                            <span class="text-sm text-gray-600 dark:text-gray-400">{{ $this->getAnsweredCount() }} of {{ $this->questions->count() }} answered</span>
                        </div>
                        <div class="grid grid-cols-10 sm:grid-cols-12 md:grid-cols-15 lg:grid-cols-20 gap-2">
                            @foreach($this->questions as $index => $q)
                                <button 
                                    wire:key="nav-btn-{{ $q->id }}"
                                    wire:click="goToQuestion({{ $index }})"
                                    class="aspect-square rounded-lg text-sm font-medium transition-all flex items-center justify-center
                                        @if($currentQuestionIndex === $index)
                                            bg-indigo-600 text-white ring-2 ring-indigo-600 ring-offset-2 scale-110
                                        @elseif(!empty($responses[$q->id]))
                                            bg-green-500 text-white hover:bg-green-600
                                        @else
                                            bg-gray-200 text-gray-700 dark:bg-gray-700 dark:text-gray-300 hover:bg-gray-300 dark:hover:bg-gray-600
                                        @endif"
                                    title="Question {{ $index + 1 }}"
                                >
                                    {{ $index + 1 }}
                                </button>
                            @endforeach
                        </div>
                        <div class="flex items-center gap-6 mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                            <div class="flex items-center gap-2 text-xs">
                                <div class="w-6 h-6 bg-indigo-600 rounded-lg"></div>
                                <span class="text-gray-600 dark:text-gray-400">Current</span>
                            </div>
                            <div class="flex items-center gap-2 text-xs">
                                <div class="w-6 h-6 bg-green-500 rounded-lg"></div>
                                <span class="text-gray-600 dark:text-gray-400">Answered</span>
                            </div>
                            <div class="flex items-center gap-2 text-xs">
                                <div class="w-6 h-6 bg-gray-200 dark:bg-gray-700 rounded-lg"></div>
                                <span class="text-gray-600 dark:text-gray-400">Not Answered</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bottom Navigation Bar -->
            <div class="bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700 px-6 py-4 flex-shrink-0">
                <div class="max-w-4xl mx-auto flex items-center justify-between">
                    <button 
                        wire:click="previousQuestion"
                        @if($currentQuestionIndex === 0) disabled @endif
                        class="flex items-center gap-2 px-6 py-3 border-2 border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 disabled:opacity-50 disabled:cursor-not-allowed font-medium transition-colors"
                    >
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                        Previous
                    </button>

                    <div class="flex items-center gap-3">
                        @if($sectionIndex < $this->exam->sections->count() - 1)
                            <a href="{{ route('examinations-hub.take.section', [$this->exam, $sectionIndex + 1]) }}" 
                               class="px-6 py-3 bg-gray-600 text-white rounded-lg hover:bg-gray-700 font-medium transition-colors">
                                Next Section →
                            </a>
                        @else
                            <form method="POST" action="{{ route('examinations-hub.take.submit', $this->exam) }}">
                                @csrf
                                <button type="submit" class="flex items-center gap-2 px-8 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 font-semibold shadow-lg hover:shadow-xl transition-all">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    Submit Examination
                                </button>
                            </form>
                        @endif
                    </div>

                    <button 
                        wire:click="nextQuestion"
                        @if($currentQuestionIndex === $this->questions->count() - 1) disabled @endif
                        class="flex items-center gap-2 px-6 py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 disabled:opacity-50 disabled:cursor-not-allowed font-medium transition-colors"
                    >
                        Next
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
        @endif
    @endif
</div>
