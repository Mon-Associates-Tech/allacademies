<div class="min-h-screen bg-gray-50 dark:bg-gray-900 transition-colors duration-200"
     x-data="{
         darkMode: @entangle('darkMode'),
         showMobileMenu: false,
         timeWarning: false,
         init() {
             // Timer warning when 5 minutes left
             this.$watch('$wire.timeRemaining', (value) => {
                 if (value <= 300 && value > 0) {
                     this.timeWarning = true;
                 }
             });
         }
     }"
     :class="{ 'dark': darkMode }">

    @if($step === 'selection')
        <!-- Subject Selection and Configuration -->
        <div class="container mx-auto px-4 py-8 max-w-4xl">
            <div
                class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
                <!-- Header -->
                <div class="bg-gradient-to-r from-blue-500 to-purple-600 text-white p-6">
                    <h1 class="text-2xl md:text-3xl font-bold">Create Assessment</h1>
                    <p class="text-blue-100 mt-1">Select your subject and configure your assessment</p>
                </div>

                <div class="p-6 space-y-6">
                    <!-- Subject Selection -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <!-- Subject -->
                        <div>
                            <label for="subject"
                                   class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Subject <span class="text-red-500">*</span>
                            </label>
                            <select wire:model.live="selectedSubject" id="subject"
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white">
                                <option value="">Select Subject</option>
                                @foreach($subjects as $subject)
                                    <option value="{{ $subject['id'] }}">{{ $subject['name'] }}</option>
                                @endforeach
                            </select>
                            @error('selectedSubject')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Topic -->
                        <div>
                            <label for="topic" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Topic (Optional)
                            </label>
                            <select wire:model.live="selectedTopic" id="topic"
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white"
                                @disabled(empty($topics))>
                                <option value="">All Topics</option>
                                @foreach($topics as $topic)
                                    <option value="{{ $topic['id'] }}">{{ $topic['name'] }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Subtopic -->
                        <div>
                            <label for="subtopic"
                                   class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Subtopic (Optional)
                            </label>
                            <select wire:model.live="selectedSubtopic" id="subtopic"
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white"
                                @disabled(empty($subtopics))>
                                <option value="">All Subtopics</option>
                                @foreach($subtopics as $subtopic)
                                    <option value="{{ $subtopic['id'] }}">{{ $subtopic['name'] }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Question Configuration -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Question Types -->
                        <div>
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-3">Question Types</h3>
                            <div class="space-y-3">
                                <label class="flex items-center">
                                    <input type="checkbox" wire:model.live="questionTypes.multiple_choice_question"
                                           class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Multiple Choice</span>
                                    @if(isset($questionCounts['multiple_choice_question']))
                                        <span class="ml-auto text-xs text-gray-500">({{ $questionCounts['multiple_choice_question'] }} available)</span>
                                    @endif
                                </label>
                                <label class="flex items-center">
                                    <input type="checkbox" wire:model.live="questionTypes.true_or_false_question"
                                           class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">True/False</span>
                                    @if(isset($questionCounts['true_or_false_question']))
                                        <span class="ml-auto text-xs text-gray-500">({{ $questionCounts['true_or_false_question'] }} available)</span>
                                    @endif
                                </label>
                                <label class="flex items-center">
                                    <input type="checkbox" wire:model.live="questionTypes.essay_question"
                                           class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Essay</span>
                                    @if(isset($questionCounts['essay_question']))
                                        <span class="ml-auto text-xs text-gray-500">({{ $questionCounts['essay_question'] }} available)</span>
                                    @endif
                                </label>
                            </div>
                        </div>

                        <!-- Configuration Options -->
                        <div class="space-y-4">
                            <!-- Question Count -->
                            <div>
                                <label for="questionCount"
                                       class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Number of Questions <span class="text-red-500">*</span>
                                </label>
                                <input type="number" wire:model.live="questionCount" id="questionCount" min="1" max="50"
                                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white">
                                @if(isset($questionCounts['total']))
                                    <p class="text-xs text-gray-500 mt-1">{{ $questionCounts['total'] }} questions
                                        available</p>
                                @endif
                                @error('questionCount')
                                <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Difficulty -->
                            <div>
                                <label for="difficulty"
                                       class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Difficulty Level
                                </label>
                                <select wire:model.live="difficulty" id="difficulty"
                                        class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white">
                                    <option value="all">All Levels</option>
                                    <option value="easy">Easy</option>
                                    <option value="medium">Medium</option>
                                    <option value="hard">Hard</option>
                                </select>
                            </div>

                            <!-- Time Limit -->
                            <div>
                                <label for="timeLimit"
                                       class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Time Limit (Minutes)
                                </label>
                                <input type="number" wire:model.live="timeLimitMinutes" id="timeLimit" min="1" max="180"
                                       placeholder="No time limit"
                                       class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white">
                            </div>

                            <!-- Balanced Distribution -->
                            <div>
                                <label class="flex items-center">
                                    <input type="checkbox" wire:model.live="balancedDistribution"
                                           class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                    <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Balanced Difficulty Distribution</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Question Distribution Preview -->
                    @if(!empty($questionDistribution))
                        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                            <h4 class="text-sm font-medium text-gray-900 dark:text-white mb-3">Available Questions by
                                Type & Difficulty</h4>
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-xs">
                                @foreach($questionDistribution as $type => $difficulties)
                                    <div class="bg-white dark:bg-gray-800 p-3 rounded">
                                        <h5 class="font-medium text-gray-900 dark:text-white mb-2 capitalize">{{ str_replace('_', ' ', $type) }}</h5>
                                        <div class="space-y-1">
                                            <div class="flex justify-between">
                                                <span class="text-green-600">Easy:</span>
                                                <span>{{ $difficulties['easy'] ?? 0 }}</span>
                                            </div>
                                            <div class="flex justify-between">
                                                <span class="text-yellow-600">Medium:</span>
                                                <span>{{ $difficulties['medium'] ?? 0 }}</span>
                                            </div>
                                            <div class="flex justify-between">
                                                <span class="text-red-600">Hard:</span>
                                                <span>{{ $difficulties['hard'] ?? 0 }}</span>
                                            </div>
                                            <div class="flex justify-between font-medium border-t pt-1">
                                                <span>Total:</span>
                                                <span>{{ $difficulties['total'] ?? 0 }}</span>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Action Buttons -->
                    <div class="flex flex-col sm:flex-row gap-3 justify-end">
                        <button wire:click="debugQuestionData"
                                class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg font-medium transition-colors duration-200">
                            Debug Data
                        </button>
                        <button wire:click="startAssessment"
                                class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition-colors duration-200">
                            Start Assessment
                        </button>
                    </div>
                </div>
            </div>
        </div>

    @elseif($step === 'taking')
        <!-- Assessment Taking Interface -->
        <!-- Quiz Taking Interface -->
        <div class="flex flex-col h-screen">
            <!-- Header -->
            <div class="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 px-4 py-3">
                <div class="flex items-center justify-between max-w-7xl mx-auto">
                    <div class="flex items-center space-x-4">
                        <button wire:click="backToSelection"
                                class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M15 19l-7-7 7-7"/>
                            </svg>
                        </button>
                        <h1 class="text-lg md:text-xl font-semibold text-gray-900 dark:text-white truncate">
                            {{ $assessment->title ?? 'Assessment' }}
                        </h1>
                        <div class="hidden sm:flex items-center space-x-2 text-sm text-gray-500 dark:text-gray-400">
                            <span>Question {{ $currentQuestionIndex + 1 }} of {{ count($questions) }}</span>
                            <span>•</span>
                            <span>{{ $this->getProgress() }}% Complete</span>
                        </div>
                    </div>

                    @if($timeRemaining && !$isSubmitted)
                        <div class="flex items-center space-x-3">
                            <!-- Timer -->
                            <div class="flex items-center space-x-2 px-3 py-2 rounded-lg
                               {{ $timeRemaining <= 300 ? 'bg-red-100 dark:bg-red-900/20 text-red-700 dark:text-red-300' : 'bg-blue-100 dark:bg-blue-900/20 text-blue-700 dark:text-blue-300' }}"
                                 wire:poll.1s="updateTimer">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <span class="font-mono text-sm font-medium">
                            {{ sprintf('%02d:%02d', floor($timeRemaining / 60), $timeRemaining % 60) }}
                        </span>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Mobile progress -->
                <div class="sm:hidden mt-2">
                    <div class="flex justify-between text-xs text-gray-500 dark:text-gray-400 mb-1">
                        <span>Question {{ $currentQuestionIndex + 1 }} of {{ count($questions) }}</span>
                        <span>{{ $this->getProgress() }}% Complete</span>
                    </div>
                    <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                        <div class="bg-blue-600 h-2 rounded-full transition-all duration-300"
                             style="width: {{ $this->getProgress() }}%"></div>
                    </div>
                </div>
            </div>

            <!-- Main Content -->
            <div class="flex-1 flex overflow-hidden">
                <!-- Question Navigation Sidebar (Desktop) -->
                <div
                    class="hidden lg:block w-64 bg-white dark:bg-gray-800 border-r border-gray-200 dark:border-gray-700 overflow-y-auto">
                    <div class="p-4">
                        <h3 class="text-sm font-medium text-gray-900 dark:text-white mb-3">Questions</h3>
                        <div class="grid grid-cols-4 gap-2">
                            @foreach($questions as $index => $question)
                                <button wire:click="goToQuestion({{ $index }})"
                                        class="w-10 h-10 rounded-lg text-sm font-medium transition-colors duration-200
                                       {{ $index === $currentQuestionIndex
                                          ? 'bg-blue-600 text-white'
                                          : ($this->isQuestionAnswered($index)
                                             ? 'bg-green-100 dark:bg-green-900/20 text-green-700 dark:text-green-300 border border-green-200 dark:border-green-800'
                                             : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600') }}">
                                    {{ $index + 1 }}
                                </button>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Question Content -->
                <div class="flex-1 flex flex-col overflow-hidden">
                    <div class="flex-1 overflow-y-auto p-4 md:p-6">
                        <div class="max-w-4xl mx-auto">
                            @if(isset($questions[$currentQuestionIndex]))
                                @php $currentQuestion = $questions[$currentQuestionIndex]; @endphp

                                <div
                                    class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                                    <!-- Question Header -->
                                    <div class="border-b border-gray-200 dark:border-gray-700 p-4 md:p-6">
                                        <div class="flex items-center justify-between mb-4">
                                            <div class="flex items-center space-x-3">
                                        <span
                                            class="flex-shrink-0 w-8 h-8 bg-blue-600 text-white rounded-full flex items-center justify-center text-sm font-medium">
                                            {{ $currentQuestionIndex + 1 }}
                                        </span>
                                                <div>
                                                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">
                                                        Question {{ $currentQuestionIndex + 1 }}
                                                    </h2>
                                                    <div
                                                        class="flex items-center space-x-4 text-sm text-gray-500 dark:text-gray-400">
                                                        <span
                                                            class="capitalize">{{ str_replace('_', ' ', $currentQuestion['difficulty']) }}</span>
                                                        <span>•</span>
                                                        <span>{{ $currentQuestion['points'] }} {{ $currentQuestion['points'] == 1 ? 'point' : 'points' }}</span>
                                                    </div>
                                                </div>
                                            </div>

                                            <!-- Question type indicator -->
                                            <div class="flex items-center space-x-2 text-xs">
                                                @if($currentQuestion['type'] === 'multiple_choice_question')
                                                    <span
                                                        class="px-2 py-1 bg-blue-100 dark:bg-blue-900/20 text-blue-700 dark:text-blue-300 rounded-full">Multiple Choice</span>
                                                @elseif($currentQuestion['type'] === 'true_or_false_question')
                                                    <span
                                                        class="px-2 py-1 bg-green-100 dark:bg-green-900/20 text-green-700 dark:text-green-300 rounded-full">True/False</span>
                                                @elseif($currentQuestion['type'] === 'essay_question')
                                                    <span
                                                        class="px-2 py-1 bg-purple-100 dark:bg-purple-900/20 text-purple-700 dark:text-purple-300 rounded-full">Essay</span>
                                                @endif
                                            </div>
                                        </div>

                                        <!-- Question Text -->
                                        <div class="prose dark:prose-invert max-w-none"
                                             x-html="marked.parse(@js($currentQuestion['question']))"></div>
                                    </div>

                                    <!-- Answer Options -->
                                    <div class="p-4 md:p-6">
                                        @if($currentQuestion['type'] === 'multiple_choice_question')
                                            <div class="space-y-3">
                                                @foreach($currentQuestion['options'] as $key => $option)
                                                    @if($option)
                                                        <label class="flex items-start space-x-3 p-4 rounded-lg border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700/50 cursor-pointer transition-colors duration-200
                                                             {{ ($responses[$currentQuestionIndex] ?? '') === $key ? 'bg-blue-50 dark:bg-blue-900/20 border-blue-200 dark:border-blue-800' : '' }}">
                                                            <input type="radio"
                                                                   wire:model.live="responses.{{ $currentQuestionIndex }}"
                                                                   value="{{ $key }}"
                                                                   name="question_{{ $currentQuestionIndex }}"
                                                                   class="mt-1 w-4 h-4 text-blue-600 border-gray-300 focus:ring-blue-500">
                                                            <div class="flex-1">
                                                                <div class="flex items-center space-x-2 mb-1">
                                                                    <span
                                                                        class="font-medium text-gray-900 dark:text-white">{{ $key }}.</span>
                                                                </div>
                                                                <div class="prose dark:prose-invert prose-sm max-w-none"
                                                                     x-html="marked.parse(@js($option))"></div>
                                                            </div>
                                                        </label>
                                                    @endif
                                                @endforeach
                                            </div>

                                        @elseif($currentQuestion['type'] === 'true_or_false_question')
                                            <div class="space-y-3">
                                                <label class="flex items-center space-x-3 p-4 rounded-lg border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700/50 cursor-pointer transition-colors duration-200
                                                     {{ ($responses[$currentQuestionIndex] ?? '') === 'true' ? 'bg-green-50 dark:bg-green-900/20 border-green-200 dark:border-green-800' : '' }}">
                                                    <input type="radio"
                                                           wire:model.live="responses.{{ $currentQuestionIndex }}"
                                                           value="true"
                                                           name="question_{{ $currentQuestionIndex }}"
                                                           class="w-4 h-4 text-green-600 border-gray-300 focus:ring-green-500">
                                                    <span
                                                        class="text-lg font-medium text-gray-900 dark:text-white">True</span>
                                                </label>
                                                <label class="flex items-center space-x-3 p-4 rounded-lg border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700/50 cursor-pointer transition-colors duration-200
                                                     {{ ($responses[$currentQuestionIndex] ?? '') === 'false' ? 'bg-red-50 dark:bg-red-900/20 border-red-200 dark:border-red-800' : '' }}">
                                                    <input type="radio"
                                                           wire:model.live="responses.{{ $currentQuestionIndex }}"
                                                           value="false"
                                                           name="question_{{ $currentQuestionIndex }}"
                                                           class="w-4 h-4 text-red-600 border-gray-300 focus:ring-red-500">
                                                    <span class="text-lg font-medium text-gray-900 dark:text-white">False</span>
                                                </label>
                                            </div>

                                        @elseif($currentQuestion['type'] === 'essay_question')
                                            <div>
                                        <textarea wire:model.live="responses.{{ $currentQuestionIndex }}"
                                                  placeholder="Type your answer here..."
                                                  rows="8"
                                                  class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400"></textarea>
                                                <div
                                                    class="flex justify-between text-xs text-gray-500 dark:text-gray-400 mt-2">
                                                    <span>{{ str_word_count($responses[$currentQuestionIndex] ?? '') }} words</span>
                                                    <span>{{ strlen($responses[$currentQuestionIndex] ?? '') }} characters</span>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Navigation Footer -->
                    <div class="bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700 p-4">
                        <div class="max-w-4xl mx-auto">
                            <!-- Mobile Question Navigation -->
                            <div class="lg:hidden mb-4">
                                <div class="flex items-center justify-between mb-2">
                                    <span class="text-sm font-medium text-gray-900 dark:text-white">Questions</span>
                                    <button @click="showMobileMenu = !showMobileMenu"
                                            class="text-sm text-blue-600 dark:text-blue-400">
                                        <span x-text="showMobileMenu ? 'Hide' : 'Show'"></span>
                                    </button>
                                </div>
                                <div x-show="showMobileMenu"
                                     x-transition:enter="transition ease-out duration-200"
                                     x-transition:enter-start="opacity-0 scale-95"
                                     x-transition:enter-end="opacity-100 scale-100"
                                     x-transition:leave="transition ease-in duration-150"
                                     x-transition:leave-start="opacity-100 scale-100"
                                     x-transition:leave-end="opacity-0 scale-95"
                                     class="grid grid-cols-6 sm:grid-cols-8 gap-2">
                                    @foreach($questions as $index => $question)
                                        <button wire:click="goToQuestion({{ $index }})"
                                                @click="showMobileMenu = false"
                                                class="w-10 h-10 rounded-lg text-sm font-medium transition-colors duration-200
                                               {{ $index === $currentQuestionIndex
                                                  ? 'bg-blue-600 text-white'
                                                  : ($this->isQuestionAnswered($index)
                                                     ? 'bg-green-100 dark:bg-green-900/20 text-green-700 dark:text-green-300'
                                                     : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300') }}">
                                            {{ $index + 1 }}
                                        </button>
                                    @endforeach
                                </div>
                            </div>

                            <!-- Navigation Buttons -->
                            <div class="flex items-center justify-between">
                                <button wire:click="previousQuestion"
                                        @disabled($currentQuestionIndex === 0)
                                        class="flex items-center space-x-2 px-4 py-2 bg-gray-600 hover:bg-gray-700 disabled:bg-gray-300 disabled:cursor-not-allowed text-white rounded-lg font-medium transition-colors duration-200">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M15 19l-7-7 7-7"/>
                                    </svg>
                                    <span>Previous</span>
                                </button>

                                <div class="flex items-center space-x-4">
                                    @if($this->getAnsweredCount() > 0)
                                        <button wire:click="submitAssessment"
                                                wire:confirm="Are you sure you want to submit? You have answered {{ $this->getAnsweredCount() }} out of {{ count($questions) }} questions."
                                                class="px-6 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg font-medium transition-colors duration-200">
                                            Submit Assessment
                                        </button>
                                    @endif

                                    @if($currentQuestionIndex < count($questions) - 1)
                                        <button wire:click="nextQuestion"
                                                class="flex items-center space-x-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition-colors duration-200">
                                            <span>Next</span>
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M9 5l7 7-7 7"/>
                                            </svg>
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Time Warning Modal -->
        <div x-show="timeWarning && $wire.timeRemaining <= 300 && $wire.timeRemaining > 0"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center p-4"
             x-cloak>
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-md w-full p-6">
                <div class="flex items-center space-x-3 mb-4">
                    <div class="flex-shrink-0">
                        <svg class="w-8 h-8 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Time Warning</h3>
                    </div>
                </div>
                <p class="text-gray-600 dark:text-gray-400 mb-6">
                    You have less than 5 minutes remaining! Please finish and submit your answers.
                </p>
                <button @click="timeWarning = false"
                        class="w-full px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg font-medium transition-colors duration-200">
                    Continue Assessment
                </button>
            </div>
        </div>

    @elseif($step === 'results')
        <!-- Results Interface -->
        <!-- Results View -->
        <div class="container mx-auto px-4 py-8 max-w-4xl">
            <div
                class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
                <!-- Results Header -->
                <div class="bg-gradient-to-r from-blue-500 to-purple-600 text-white p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <h1 class="text-2xl md:text-3xl font-bold">Assessment Results</h1>
                            <p class="text-blue-100 mt-1">{{ $assessment->title ?? 'Self Assessment' }}</p>
                        </div>
                        <div class="text-right">
                            <div class="text-3xl font-bold">{{ $results['percentage'] }}%</div>
                            <div class="text-sm">{{ $this->getGrade($results['percentage']) }} Grade</div>
                        </div>
                    </div>
                </div>

                <!-- Results Summary -->
                <div class="p-6">
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                        <div class="text-center p-4 bg-green-50 dark:bg-green-900/20 rounded-lg">
                            <div
                                class="text-2xl font-bold text-green-600 dark:text-green-400">{{ $results['correct_answers'] }}</div>
                            <div class="text-sm text-gray-600 dark:text-gray-400">Correct</div>
                        </div>
                        <div class="text-center p-4 bg-red-50 dark:bg-red-900/20 rounded-lg">
                            <div
                                class="text-2xl font-bold text-red-600 dark:text-red-400">{{ $results['total_questions'] - $results['correct_answers'] }}</div>
                            <div class="text-sm text-gray-600 dark:text-gray-400">Incorrect</div>
                        </div>
                        <div class="text-center p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                            <div
                                class="text-2xl font-bold text-blue-600 dark:text-blue-400">{{ $results['total_score'] }}
                                /{{ $results['max_score'] }}</div>
                            <div class="text-sm text-gray-600 dark:text-gray-400">Score</div>
                        </div>
                        <div class="text-center p-4 bg-purple-50 dark:bg-purple-900/20 rounded-lg">
                            <div
                                class="text-2xl font-bold text-purple-600 dark:text-purple-400">{{ $results['completion_rate'] }}
                                %
                            </div>
                            <div class="text-sm text-gray-600 dark:text-gray-400">Completed</div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex flex-col sm:flex-row gap-3 justify-center">
                        <button wire:click="toggleReview"
                                class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition-colors duration-200">
                            {{ $showReview ? 'Hide Review' : 'Review Answers' }}
                        </button>
                        <button wire:click="restartAssessment"
                                class="px-6 py-3 bg-gray-600 hover:bg-gray-700 text-white rounded-lg font-medium transition-colors duration-200">
                            Take Again
                        </button>
                    </div>

                    @if($showReview)
                        <!-- Answer Review -->
                        <div class="mt-8 space-y-6">
                            <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">Answer Review</h3>
                            @foreach($questions as $index => $question)
                                <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4
                                   {{ isset($results['graded_responses'][$index]) && $results['graded_responses'][$index]['is_correct']
                                      ? 'bg-green-50 dark:bg-green-900/10 border-green-200 dark:border-green-800'
                                      : 'bg-red-50 dark:bg-red-900/10 border-red-200 dark:border-red-800' }}">
                                    <div class="flex items-start gap-3">
                                <span class="flex-shrink-0 w-8 h-8 rounded-full text-white text-sm font-medium flex items-center justify-center
                                           {{ isset($results['graded_responses'][$index]) && $results['graded_responses'][$index]['is_correct']
                                              ? 'bg-green-500' : 'bg-red-500' }}">
                                    {{ $index + 1 }}
                                </span>
                                        <div class="flex-1">
                                            <div class="prose dark:prose-invert prose-sm max-w-none mb-3"
                                                 x-html="marked.parse(@js($question['question']))"></div>

                                            @if($question['type'] === 'multiple_choice_question')
                                                <div class="space-y-2 mb-3">
                                                    @foreach($question['options'] as $key => $option)
                                                        @if($option)
                                                            <div class="flex items-center text-sm
                                                               {{ ($responses[$index] ?? '') === $key ? 'font-medium' : '' }}
                                                               {{ $question['answer'] === $key ? 'text-green-600 dark:text-green-400' : 'text-gray-700 dark:text-gray-300' }}">
                                                                <span class="w-6">{{ $key }}.</span>
                                                                <span x-html="marked.parse(@js($option))"></span>
                                                                @if(($responses[$index] ?? '') === $key)
                                                                    <span
                                                                        class="ml-2 text-blue-600">← Your answer</span>
                                                                @endif
                                                                @if($question['answer'] === $key)
                                                                    <span class="ml-2 text-green-600">✓ Correct</span>
                                                                @endif
                                                            </div>
                                                        @endif
                                                    @endforeach
                                                </div>
                                            @elseif($question['type'] === 'true_or_false_question')
                                                <div class="mb-3">
                                                    <p class="text-sm">Your answer: <span
                                                            class="font-medium">{{ $responses[$index] ?? 'Not answered' }}</span>
                                                    </p>
                                                    <p class="text-sm">Correct answer: <span
                                                            class="font-medium text-green-600">{{ $question['answer'] ? 'True' : 'False' }}</span>
                                                    </p>
                                                </div>
                                            @elseif($question['type'] === 'essay_question')
                                                <div class="mb-3">
                                                    <p class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                                        Your Response:</p>
                                                    <div class="bg-white dark:bg-gray-700 p-3 rounded border text-sm">
                                                        {{ $responses[$index] ?? 'No response provided' }}
                                                    </div>
                                                    <p class="text-xs text-gray-500 mt-1 italic">Essay questions require
                                                        manual grading</p>
                                                </div>
                                            @endif

                                            @if(isset($results['graded_responses'][$index]['feedback']))
                                                <div
                                                    class="text-sm {{ isset($results['graded_responses'][$index]) && $results['graded_responses'][$index]['is_correct'] ? 'text-green-700 dark:text-green-300' : 'text-red-700 dark:text-red-300' }}">
                                                    {{ $results['graded_responses'][$index]['feedback'] }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            </div>
        </div>
    @endif
</div>

<script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
