<div class="min-h-screen bg-gray-50 dark:bg-gray-900">
    @if($showSectionInfo)
        <div class="max-w-4xl mx-auto px-4 py-8">
            <div class="bg-white dark:bg-gray-800 shadow-lg rounded-lg p-8">
                <div class="text-center mb-6">
                    <h2 class="text-3xl font-bold text-gray-900 dark:text-white">{{ $this->section->title }}</h2>
                    <p class="text-gray-600 dark:text-gray-400 mt-2">Section {{ $sectionIndex + 1 }} of {{ $this->exam->sections->count() }}</p>
                </div>

                @if($this->section->description)
                    <div class="mb-6">
                        <p class="text-gray-700 dark:text-gray-300">{{ $this->section->description }}</p>
                    </div>
                @endif

                @if($this->section->instructions)
                    <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-6 mb-6">
                        <h3 class="font-semibold text-blue-900 dark:text-blue-100 mb-2">Section Instructions</h3>
                        <div class="text-sm text-blue-800 dark:text-blue-200 whitespace-pre-line">{{ $this->section->instructions }}</div>
                    </div>
                @endif

                <div class="grid md:grid-cols-3 gap-4 mb-6">
                    <div class="bg-gray-100 dark:bg-gray-700 rounded-lg p-4 text-center">
                        <p class="text-sm text-gray-600 dark:text-gray-400">Questions</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $this->questions->count() }}</p>
                    </div>
                    @if($this->section->time_limit_minutes)
                        <div class="bg-gray-100 dark:bg-gray-700 rounded-lg p-4 text-center">
                            <p class="text-sm text-gray-600 dark:text-gray-400">Time Limit</p>
                            <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $this->section->time_limit_minutes }} min</p>
                        </div>
                    @endif
                    <div class="bg-gray-100 dark:bg-gray-700 rounded-lg p-4 text-center">
                        <p class="text-sm text-gray-600 dark:text-gray-400">Question Type</p>
                        <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ str_replace('_', ' ', ucfirst($this->section->question_type)) }}</p>
                    </div>
                </div>

                <div class="flex justify-center">
                    <button wire:click="startSection" class="px-8 py-3 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 font-medium text-lg shadow-lg">
                        Start Section
                    </button>
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
        <div class="flex h-screen">
            <!-- Question Navigator Sidebar -->
            <div class="w-64 bg-white dark:bg-gray-800 border-r border-gray-200 dark:border-gray-700 overflow-y-auto flex-shrink-0">
                <div class="p-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="font-semibold text-gray-900 dark:text-white">{{ $this->section->title }}</h3>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">{{ $this->getAnsweredCount() }} of {{ $this->questions->count() }} answered</p>
                    <button wire:click="toggleSectionInfo" class="text-xs text-indigo-600 hover:text-indigo-800 mt-2">View Section Info</button>
                </div>

                <div class="p-4">
                    <div class="grid grid-cols-5 gap-2">
                        @foreach($this->questions as $index => $question)
                            <button 
                                wire:key="nav-btn-{{ $question->id }}"
                                wire:click="goToQuestion({{ $index }})"
                                class="aspect-square rounded-lg text-sm font-medium transition-colors flex items-center justify-center
                                    @if($currentQuestionIndex === $index)
                                        bg-indigo-600 text-white ring-2 ring-indigo-600 ring-offset-2
                                    @elseif(!empty($responses[$question->id]))
                                        bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-100 hover:bg-green-200
                                    @else
                                        bg-gray-100 text-gray-700 dark:bg-gray-700 dark:text-gray-300 hover:bg-gray-200
                                    @endif"
                            >
                                {{ $index + 1 }}
                            </button>
                        @endforeach
                    </div>
                </div>

                <div class="p-4 border-t border-gray-200 dark:border-gray-700">
                    <div class="flex items-center gap-2 text-xs mb-2">
                        <div class="w-4 h-4 bg-green-100 dark:bg-green-900 rounded"></div>
                        <span class="text-gray-600 dark:text-gray-400">Answered</span>
                    </div>
                    <div class="flex items-center gap-2 text-xs">
                        <div class="w-4 h-4 bg-gray-100 dark:bg-gray-700 rounded"></div>
                        <span class="text-gray-600 dark:text-gray-400">Not Answered</span>
                    </div>
                </div>
            </div>

            <!-- Main Question Area -->
            <div class="flex-1 flex flex-col min-w-0">
                <!-- Header -->
                <div class="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 px-6 py-4">
                    <div class="flex items-center justify-between">
                        <div>
                            <h2 class="text-xl font-semibold text-gray-900 dark:text-white">{{ $this->exam->title }}</h2>
                            <p class="text-sm text-gray-600 dark:text-gray-400">Question {{ $currentQuestionIndex + 1 }} of {{ $this->questions->count() }}</p>
                        </div>
                        @if($timeRemaining !== null)
                            <div class="flex items-center gap-2 px-4 py-2 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg">
                                <svg class="h-5 w-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span class="font-mono font-semibold text-red-700 dark:text-red-400">{{ $timeRemaining }} min</span>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Question Content -->
                <div class="flex-1 overflow-y-auto p-6">
                    @php
                        $question = $this->questions[$currentQuestionIndex];
                    @endphp

                    <div class="max-w-3xl mx-auto">
                        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-8">
                            <div class="mb-6">
                                <div class="flex items-start justify-between mb-4">
                                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">Question {{ $currentQuestionIndex + 1 }}</h3>
                                    <span class="px-3 py-1 bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-200 rounded-full text-sm">
                                        {{ $question->marks }} {{ $question->marks === 1 ? 'mark' : 'marks' }}
                                    </span>
                                </div>
                                <div class="text-gray-800 dark:text-gray-200 text-lg leading-relaxed">
                                    {!! $question->getFormattedQuestion() !!}
                                </div>
                            </div>

                            @if($question->isMultipleChoice())
                                <div class="space-y-3">
                                    @foreach($question->getOptionsForDisplay() as $key => $optionText)
                                        <label wire:key="opt-{{ $question->id }}-{{ $key }}" 
                                               class="flex gap-3 p-4 border-2 rounded-lg cursor-pointer transition-colors
                                            @if(isset($responses[$question->id]) && $responses[$question->id] === $key)
                                                border-indigo-600 bg-indigo-50 dark:bg-indigo-900/20
                                            @else
                                                border-gray-200 dark:border-gray-700 hover:border-indigo-300
                                            @endif">
                                            <input 
                                                type="radio" 
                                                name="question_{{ $question->id }}" 
                                                value="{{ $key }}"
                                                wire:model.live="responses.{{ $question->id }}"
                                                class="h-5 w-5 text-indigo-600 focus:ring-indigo-500 flex-shrink-0 mt-0.5"
                                            >
                                            <div class="flex-1 leading-relaxed">
                                                <span class="font-semibold text-gray-900 dark:text-white mr-2">{{ $key }}.</span>
                                                <span class="text-gray-700 dark:text-gray-300">{{ $optionText }}</span>
                                            </div>
                                        </label>
                                    @endforeach
                                </div>                                </div>
                            @elseif($question->isTrueFalse())
                                <div class="space-y-3">
                                    <label class="flex items-center gap-3 p-4 border-2 rounded-lg cursor-pointer transition-colors
                                        {{ isset($responses[$question->id]) && $responses[$question->id] === 'True' 
                                            ? 'border-indigo-600 bg-indigo-50 dark:bg-indigo-900/20' 
                                            : 'border-gray-200 dark:border-gray-700 hover:border-indigo-300' 
                                        }}">
                                        <input 
                                            type="radio" 
                                            name="question_{{ $question->id }}" 
                                            value="True"
                                            wire:model.live="responses.{{ $question->id }}"
                                            class="h-5 w-5 text-indigo-600 focus:ring-indigo-500 flex-shrink-0"
                                        >
                                        <span class="text-gray-700 dark:text-gray-300 font-medium">True</span>
                                    </label>
                                    <label class="flex items-center gap-3 p-4 border-2 rounded-lg cursor-pointer transition-colors
                                        {{ isset($responses[$question->id]) && $responses[$question->id] === 'False' 
                                            ? 'border-indigo-600 bg-indigo-50 dark:bg-indigo-900/20' 
                                            : 'border-gray-200 dark:border-gray-700 hover:border-indigo-300' 
                                        }}">
                                        <input 
                                            type="radio" 
                                            name="question_{{ $question->id }}" 
                                            value="False"
                                            wire:model.live="responses.{{ $question->id }}"
                                            class="h-5 w-5 text-indigo-600 focus:ring-indigo-500 flex-shrink-0"
                                        >
                                        <span class="text-gray-700 dark:text-gray-300 font-medium">False</span>
                                    </label>
                                </div>
                            @else
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Your Answer</label>
                                    <textarea 
                                        wire:model.live.debounce.500ms="responses.{{ $question->id }}"
                                        rows="8"
                                        class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-indigo-500 dark:bg-gray-700 dark:text-white"
                                        placeholder="Type your answer here..."
                                    ></textarea>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Navigation Footer -->
                <div class="bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700 px-6 py-4">
                    <div class="flex items-center justify-between max-w-3xl mx-auto">
                        <button 
                            wire:click="previousQuestion"
                            @if($currentQuestionIndex === 0) disabled @endif
                            class="px-6 py-2 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 disabled:opacity-50 disabled:cursor-not-allowed"
                        >
                            ← Previous
                        </button>

                        <div class="flex items-center gap-4">
                            @if($sectionIndex < $this->exam->sections->count() - 1)
                                <a href="{{ route('examinations-hub.take.section', [$this->exam, $sectionIndex + 1]) }}" class="px-6 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700">
                                    Next Section →
                                </a>
                            @else
                                <form method="POST" action="{{ route('examinations-hub.take.submit', $this->exam) }}">
                                    @csrf
                                    <button type="submit" class="px-6 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 font-medium">
                                        Submit Examination
                                    </button>
                                </form>
                            @endif
                        </div>

                        <button 
                            wire:click="nextQuestion"
                            @if($currentQuestionIndex === $this->questions->count() - 1) disabled @endif
                            class="px-6 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 disabled:opacity-50 disabled:cursor-not-allowed"
                        >
                            Next →
                        </button>
                    </div>
                </div>
            </div>
        </div>
        @endif
    @endif
</div>
