<div class="min-h-screen bg-gray-50 dark:bg-gray-900">
    <!-- Loading State -->
    @if($step === 'loading')
        <div class="flex items-center justify-center min-h-screen">
            <div class="text-center">
                <div class="animate-spin rounded-full h-32 w-32 border-b-2 border-violet-600 mx-auto"></div>
                <p class="mt-4 text-lg text-gray-600 dark:text-gray-400">Loading assignment...</p>
            </div>
        </div>
    @endif

    <!-- Taking Assessment -->
    @if($step === 'taking')
        <div class="container mx-auto px-4 py-6">
            @if(!empty($studentSnapshot))
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 mb-6">
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-4">
                        <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Assignment Completion</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-gray-100 mt-1">{{ $studentSnapshot['assignments']['completion_rate'] ?? 0 }}%</p>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">{{ $studentSnapshot['assignments']['upcoming'] ?? 0 }} upcoming</p>
                    </div>

                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-4">
                        <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Self Assessment Avg</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-gray-100 mt-1">{{ $studentSnapshot['quizzes']['average_score'] ?? 0 }}%</p>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">{{ $studentSnapshot['quizzes']['total'] ?? 0 }} total quizzes</p>
                    </div>

                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-4">
                        <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Reading Progress</p>
                        <p class="text-2xl font-bold text-gray-900 dark:text-gray-100 mt-1">{{ $studentSnapshot['reading']['books_in_progress'] ?? 0 }}</p>
                        <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">{{ $studentSnapshot['reading']['books_completed'] ?? 0 }} completed books</p>
                    </div>
                </div>
            @endif

            <!-- Header -->
            <div
                class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 mb-6">
                <div class="p-6">
                    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between">
                        <div class="flex-1 min-w-0">
                            <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100 mb-2">
                                {{ $assignment->title ?? 'Assignment' }}
                            </h1>
                            <div class="flex flex-wrap items-center gap-4 text-sm text-gray-600 dark:text-gray-400">
                                <span class="flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                    </svg>
                                    {{ $assignment->academicSubject->name ?? 'Unknown Subject' }}
                                </span>
                                <span class="flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                    </svg>
                                    {{ $assignment->teacher->user->name ?? 'Unknown Teacher' }}
                                </span>
                                <span class="flex items-center">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    {{ count($questions) }} Questions
                                </span>
                            </div>
                        </div>

                        <!-- Timer and Progress -->
                        <div class="mt-4 lg:mt-0 flex items-center space-x-4">
                            @if($timeRemaining !== null)
                                <div
                                    class="flex items-center space-x-2 px-4 py-2 bg-orange-50 dark:bg-orange-900/20 rounded-lg">
                                    <svg class="w-5 h-5 text-orange-600 dark:text-orange-400" fill="none"
                                         stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    <span class="text-orange-800 dark:text-orange-300 font-mono font-semibold">
                                        {{ sprintf('%02d:%02d:%02d', floor($timeRemaining / 3600), floor(($timeRemaining % 3600) / 60), $timeRemaining % 60) }}
                                    </span>
                                </div>
                            @endif

                            <!-- Progress -->
                            <div class="flex items-center space-x-2">
                                <span class="text-sm text-gray-600 dark:text-gray-400">Progress:</span>
                                <div class="w-24 bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                    <div
                                        class="bg-gradient-to-r from-violet-500 to-purple-600 h-2 rounded-full transition-all duration-300"
                                        style="width: {{ $this->getProgress() }}%"></div>
                                </div>
                                <span class="text-sm font-semibold text-gray-900 dark:text-gray-100">{{ $this->getProgress() }}%</span>
                            </div>
                        </div>
                    </div>

                    <!-- Progress Bar -->
                    <div class="mt-4">
                        <div class="flex items-center justify-between text-sm mb-2">
                            <span class="text-gray-600 dark:text-gray-300">
                                Question {{ $currentQuestionIndex + 1 }} of {{ count($questions) }}
                            </span>
                            <span class="text-gray-600 dark:text-gray-300">
                                {{ $this->getAnsweredCount() }} answered
                            </span>
                        </div>
                        <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                            <div
                                class="bg-gradient-to-r from-violet-500 to-purple-600 h-2 rounded-full transition-all duration-300"
                                style="width: {{ $this->getProgress() }}%"></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">
                <!-- Question Panel -->
                <div class="lg:col-span-3">
                    @if(isset($questions[$currentQuestionIndex]))
                        @php $question = $questions[$currentQuestionIndex]; @endphp

                        <div
                            class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
                            <!-- Question Header -->
                            <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                                <div class="flex items-center justify-between mb-4">
                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">
                                        Question {{ $currentQuestionIndex + 1 }}
                                    </h3>
                                    <div class="flex items-center space-x-2">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                            {{ $question['type'] === 'multiple_choice_question' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300' :
                                               ($question['type'] === 'true_or_false_question' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300' :
                                               'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-300') }}">
                                            {{ ucfirst(str_replace('_', ' ', $question['type'])) }}
                                        </span>
                                        <span class="text-sm text-gray-500 dark:text-gray-400">
                                            {{ $question['points'] ?? 1 }} point{{ ($question['points'] ?? 1) !== 1 ? 's' : '' }}
                                        </span>
                                    </div>
                                </div>

                                <div class="prose prose-gray max-w-none dark:prose-invert text-gray-900 dark:text-gray-100">
                                    <p class="text-gray-900 dark:text-gray-100 text-base leading-relaxed">{{ $question['question'] }}</p>
                                </div>
                            </div>

                            <!-- Question Content -->
                            <div class="p-6">
                                @if($question['type'] === 'multiple_choice_question')
                                    <div class="space-y-3">
                                        @foreach($question['options'] as $optionKey => $optionValue)
                                            <label wire:key="mcq-{{ $currentQuestionIndex }}-{{ $optionKey }}"
                                                   class="flex items-center p-4 rounded-lg border-2 border-gray-200 dark:border-gray-600 hover:border-violet-300 dark:hover:border-violet-500 hover:bg-violet-50 dark:hover:bg-violet-900/20 cursor-pointer transition-all duration-200 {{ ($responses[$currentQuestionIndex] ?? '') === $optionKey ? 'border-violet-500 dark:border-violet-400 bg-violet-50 dark:bg-violet-900/30' : 'bg-white dark:bg-gray-700' }}">
                                                <input type="radio"
                                                       name="question_{{ $currentQuestionIndex }}"
                                                       value="{{ $optionKey }}"
                                                       wire:model.live="responses.{{ $currentQuestionIndex }}"
                                                       wire:key="mcq-input-{{ $currentQuestionIndex }}-{{ $optionKey }}"
                                                       class="h-4 w-4 text-violet-600 focus:ring-violet-500 border-gray-300 dark:border-gray-500 dark:bg-gray-600 flex-shrink-0">
                                                <span class="inline-flex items-center justify-center w-7 h-7 bg-gray-100 dark:bg-gray-600 rounded-full text-sm font-semibold text-gray-700 dark:text-gray-200 ml-3 flex-shrink-0">
                                                    {{ $optionKey }}
                                                </span>
                                                <span class="text-gray-900 dark:text-gray-100 ml-3">{{ $optionValue }}</span>
                                            </label>
                                        @endforeach
                                    </div>

                                @elseif($question['type'] === 'true_or_false_question')
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                        <label wire:key="tf-{{ $currentQuestionIndex }}-true"
                                               class="flex items-center justify-center p-6 rounded-xl border-2 border-gray-200 dark:border-gray-600 hover:border-green-400 dark:hover:border-green-500 cursor-pointer transition-all duration-200 {{ ($responses[$currentQuestionIndex] ?? '') === 'true' ? 'border-green-500 dark:border-green-400 bg-green-50 dark:bg-green-900/30' : 'bg-white dark:bg-gray-700 hover:bg-green-50 dark:hover:bg-green-900/20' }}">
                                            <input type="radio"
                                                   name="question_{{ $currentQuestionIndex }}"
                                                   value="true"
                                                   wire:model.live="responses.{{ $currentQuestionIndex }}"
                                                   wire:key="tf-input-{{ $currentQuestionIndex }}-true"
                                                   class="sr-only">
                                            <div class="text-center">
                                                <div class="w-12 h-12 mx-auto mb-2 rounded-full bg-green-100 dark:bg-green-800 flex items-center justify-center">
                                                    <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                    </svg>
                                                </div>
                                                <span class="text-lg font-semibold text-green-700 dark:text-green-300">True</span>
                                            </div>
                                        </label>

                                        <label wire:key="tf-{{ $currentQuestionIndex }}-false"
                                               class="flex items-center justify-center p-6 rounded-xl border-2 border-gray-200 dark:border-gray-600 hover:border-red-400 dark:hover:border-red-500 cursor-pointer transition-all duration-200 {{ ($responses[$currentQuestionIndex] ?? '') === 'false' ? 'border-red-500 dark:border-red-400 bg-red-50 dark:bg-red-900/30' : 'bg-white dark:bg-gray-700 hover:bg-red-50 dark:hover:bg-red-900/20' }}">
                                            <input type="radio"
                                                   name="question_{{ $currentQuestionIndex }}"
                                                   value="false"
                                                   wire:model.live="responses.{{ $currentQuestionIndex }}"
                                                   wire:key="tf-input-{{ $currentQuestionIndex }}-false"
                                                   class="sr-only">
                                            <div class="text-center">
                                                <div class="w-12 h-12 mx-auto mb-2 rounded-full bg-red-100 dark:bg-red-800 flex items-center justify-center">
                                                    <svg class="w-6 h-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                    </svg>
                                                </div>
                                                <span class="text-lg font-semibold text-red-700 dark:text-red-300">False</span>
                                            </div>
                                        </label>
                                    </div>

                                @elseif($question['type'] === 'essay_question')
                                    <div>
                                        <textarea
                                            wire:model.live.debounce.500ms="responses.{{ $currentQuestionIndex }}"
                                            rows="8"
                                            class="w-full px-4 py-3 border-2 border-gray-200 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-violet-500 focus:border-violet-500 dark:focus:border-violet-400 bg-white dark:bg-gray-700 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-500 resize-none transition-colors"
                                            placeholder="Type your answer here..."></textarea>
                                        <div class="mt-2 flex items-center justify-between text-sm">
                                            <span class="text-gray-500 dark:text-gray-400">
                                                Word count: <span class="font-medium text-gray-700 dark:text-gray-300">{{ str_word_count($responses[$currentQuestionIndex] ?? '') }}</span>
                                            </span>
                                        </div>
                                    </div>
                                @endif
                            </div>

                            <!-- Question Navigation -->
                            <div
                                class="px-6 py-4 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50">
                                <div class="flex items-center justify-between">
                                    <button
                                        wire:click="previousQuestion"
                                        {{ $currentQuestionIndex === 0 ? 'disabled' : '' }}
                                        class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-600 border border-gray-300 dark:border-gray-500 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-500 disabled:opacity-50 disabled:cursor-not-allowed transition-colors">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M15 19l-7-7 7-7"></path>
                                        </svg>
                                        Previous
                                    </button>

                                    <span class="text-sm text-gray-500 dark:text-gray-400">
                                        Question {{ $currentQuestionIndex + 1 }} of {{ count($questions) }}
                                    </span>

                                    <button
                                        wire:click="nextQuestion"
                                        {{ $currentQuestionIndex === count($questions) - 1 ? 'disabled' : '' }}
                                        class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-gradient-to-r from-violet-500 to-purple-600 rounded-lg hover:from-violet-600 hover:to-purple-700 disabled:opacity-50 disabled:cursor-not-allowed transition-all duration-200 shadow-sm">
                                        Next
                                        <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor"
                                             viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                  stroke-width="2" d="M9 5l7 7-7 7"></path>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Separate Submit Section -->
                        <div class="mt-6 bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                            <div class="flex flex-col sm:flex-row items-center justify-between gap-4">
                                <div class="text-center sm:text-left">
                                    <h4 class="font-semibold text-gray-900 dark:text-gray-100">Ready to submit?</h4>
                                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                                        You have answered <span class="font-medium text-green-600 dark:text-green-400">{{ $this->getAnsweredCount() }}</span> of <span class="font-medium">{{ count($questions) }}</span> questions.
                                        @if($this->getAnsweredCount() < count($questions))
                                            <span class="text-orange-600 dark:text-orange-400">{{ count($questions) - $this->getAnsweredCount() }} unanswered.</span>
                                        @else
                                            <span class="text-green-600 dark:text-green-400">All questions answered!</span>
                                        @endif
                                    </p>
                                </div>
                                <button
                                    wire:click="submitAssessment"
                                    wire:confirm="Are you sure you want to submit this assignment? You cannot change your answers after submission."
                                    class="inline-flex items-center px-6 py-3 text-sm font-medium text-white bg-gradient-to-r from-green-500 to-emerald-600 rounded-lg hover:from-green-600 hover:to-emerald-700 transition-all duration-200 shadow-sm">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    Submit Assignment
                                </button>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Sidebar -->
                <div class="lg:col-span-1">
                    <!-- Question Navigator -->
                    <div
                        class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 mb-6">
                        <div class="p-4 border-b border-gray-200 dark:border-gray-700">
                            <h4 class="font-semibold text-gray-900 dark:text-gray-100">Questions</h4>
                        </div>
                        <div class="p-4">
                            <div class="grid grid-cols-5 gap-2">
                                @foreach($questions as $index => $question)
                                    <button
                                        wire:click="goToQuestion({{ $index }})"
                                        class="w-10 h-10 rounded-lg text-sm font-medium transition-colors
                                            {{ $index === $currentQuestionIndex ? 'bg-violet-600 text-white' :
                                               ($this->isQuestionAnswered($index) ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300' :
                                               'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600') }}">
                                        {{ $index + 1 }}
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <!-- Assignment Info -->
                    <div
                        class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
                        <div class="p-4 border-b border-gray-200 dark:border-gray-700">
                            <h4 class="font-semibold text-gray-900 dark:text-gray-100">Assignment Info</h4>
                        </div>
                        <div class="p-4 space-y-3 text-sm">
                            <div class="flex justify-between items-center">
                                <span class="text-gray-500 dark:text-gray-400">Total Questions</span>
                                <span
                                    class="font-medium text-gray-900 dark:text-gray-100">{{ count($questions) }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-gray-500 dark:text-gray-400">Answered</span>
                                <span
                                    class="font-medium text-green-600 dark:text-green-400">{{ $this->getAnsweredCount() }}</span>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-gray-500 dark:text-gray-400">Remaining</span>
                                <span
                                    class="font-medium text-orange-600 dark:text-orange-400">{{ count($questions) - $this->getAnsweredCount() }}</span>
                            </div>
                            @if($assignment->duration_in_minutes)
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-500 dark:text-gray-400">Duration</span>
                                    <span class="font-medium text-gray-900 dark:text-gray-100">{{ $assignment->duration_in_minutes }} min</span>
                                </div>
                            @endif
                            @if($assignment->total_marks)
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-500 dark:text-gray-400">Total Marks</span>
                                    <span
                                        class="font-medium text-gray-900 dark:text-gray-100">{{ $assignment->total_marks }}</span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Results -->
    @if($step === 'results')
        <div class="container mx-auto px-4 py-6">
            <div class="max-w-4xl mx-auto">
                <!-- Results Header -->
                <div
                    class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 mb-6">
                    <div class="p-6 text-center">
                        <div
                            class="w-20 h-20 mx-auto mb-4 bg-gradient-to-br from-green-500 to-emerald-600 rounded-full flex items-center justify-center">
                            <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100 mb-2">Assignment Completed!</h1>
                        <p class="text-gray-600 dark:text-gray-400">{{ $assignment->title }}</p>
                    </div>
                </div>

                <!-- Score Summary -->
                @if($results)
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
                        <div
                            class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6 text-center">
                            <div
                                class="text-3xl font-bold text-violet-600 dark:text-violet-400">{{ $results['percentage'] }}
                                %
                            </div>
                            <div class="text-gray-600 dark:text-gray-400">Overall Score</div>
                            <div class="mt-2">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    {{ $results['percentage'] >= 80 ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300' :
                                       ($results['percentage'] >= 60 ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300' :
                                       'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300') }}">
                                    Grade: {{ $this->getGrade($results['percentage']) }}
                                </span>
                            </div>
                        </div>

                        <div
                            class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6 text-center">
                            <div
                                class="text-3xl font-bold text-green-600 dark:text-green-400">{{ $results['correct_answers'] }}</div>
                            <div class="text-gray-600 dark:text-gray-400">Correct Answers</div>
                            <div class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                                out of {{ $results['total_questions'] }} questions
                            </div>
                        </div>

                        <div
                            class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6 text-center">
                            <div
                                class="text-3xl font-bold text-blue-600 dark:text-blue-400">{{ $results['total_score'] }}</div>
                            <div class="text-gray-600 dark:text-gray-400">Points Earned</div>
                            <div class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                                out of {{ $results['max_score'] }} points
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Action Buttons -->
                <div class="flex justify-center space-x-4">
                    <button
                        wire:click="toggleReview"
                        class="inline-flex items-center px-6 py-2 text-sm font-medium text-violet-600 dark:text-violet-400 border border-violet-300 dark:border-violet-600 rounded-lg hover:bg-violet-50 dark:hover:bg-violet-900/20 transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                        </svg>
                        {{ $showReview ? 'Hide Review' : 'Review Answers' }}
                    </button>

                    <button
                        wire:click="backToAssignments"
                        class="inline-flex items-center px-6 py-2 text-sm font-medium text-white bg-gradient-to-r from-violet-500 to-purple-600 rounded-lg hover:from-violet-600 hover:to-purple-700 transition-all duration-200 shadow-sm">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        Back to Assignments
                    </button>
                </div>

                <!-- Review Section -->
                @if($showReview && $results)
                    <div
                        class="mt-8 bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
                        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Answer Review</h3>
                        </div>
                        <div class="divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach($questions as $index => $question)
                                @php
                                    $graded = $results['graded_responses'][$index] ?? null;
                                    $isCorrect = $graded['is_correct'] ?? false;
                                    $needsGrading = $graded['needs_manual_grading'] ?? false;
                                @endphp

                                <div class="p-6">
                                    <div class="flex items-start justify-between mb-4">
                                        <h4 class="font-medium text-gray-900 dark:text-gray-100">
                                            Question {{ $index + 1 }}
                                        </h4>
                                        <div class="flex items-center space-x-2">
                                            @if($needsGrading)
                                                <span
                                                    class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300">
                                                    Pending Review
                                                </span>
                                            @elseif($isCorrect)
                                                <span
                                                    class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300">
                                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd"
                                                              d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                                              clip-rule="evenodd"></path>
                                                    </svg>
                                                    Correct
                                                </span>
                                            @else
                                                <span
                                                    class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300">
                                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd"
                                                              d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                                                              clip-rule="evenodd"></path>
                                                    </svg>
                                                    Incorrect
                                                </span>
                                            @endif
                                            <span class="text-sm text-gray-500 dark:text-gray-400">
                                                {{ $graded['score_earned'] ?? 0 }}/{{ $question['points'] ?? 1 }} pts
                                            </span>
                                        </div>
                                    </div>

                                    <div class="prose prose-gray max-w-none dark:prose-invert mb-4">
                                        {!! $question['question'] !!}
                                    </div>

                                    @if(isset($responses[$index]) && $responses[$index] !== null)
                                        <div class="mb-2">
                                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Your Answer:</span>
                                            <span class="ml-2 text-sm text-gray-900 dark:text-gray-100">
                                                @if($question['type'] === 'essay_question')
                                                    <div
                                                        class="mt-2 p-3 bg-gray-50 dark:bg-gray-700 rounded border text-sm">
                                                        {{ $responses[$index] }}
                                                    </div>
                                                @else
                                                    {{ $responses[$index] }}
                                                @endif
                                            </span>
                                        </div>
                                    @endif

                                    @if(isset($graded['correct_answer']) && !$needsGrading)
                                        <div class="mb-2">
                                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Correct Answer:</span>
                                            <span
                                                class="ml-2 text-sm text-green-600 dark:text-green-400">{{ $graded['correct_answer'] }}</span>
                                        </div>
                                    @endif

                                    @if(isset($graded['feedback']))
                                        <div class="text-sm text-gray-600 dark:text-gray-400">
                                            {{ $graded['feedback'] }}
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>
    @endif

    <!-- Timer Script -->
    @if($isTimerActive && $timeRemaining !== null)
        <script>
            let timer = setInterval(function () {
                @this.call('updateTimer');
            }, 1000);

            document.addEventListener('livewire:navigated', () => {
                clearInterval(timer);
            });
        </script>
    @endif

        <script>
            document.addEventListener('DOMContentLoaded', function() {
                @if($restrictNavigation && $step === 'taking')
                let tabSwitchCount = {{ $tabSwitchCount }};
                let isVisible = true;

                // Detect tab/window visibility changes
                document.addEventListener('visibilitychange', function() {
                    if (document.hidden && isVisible) {
                        isVisible = false;
                        tabSwitchCount++;

                        // Call Livewire method to record the switch
                        @this.call('recordTabSwitch');
                    } else if (!document.hidden) {
                        isVisible = true;
                    }
                });

                // Detect focus loss
                window.addEventListener('blur', function() {
                    if (isVisible) {
                        // Window lost focus but tab is still visible
                        setTimeout(function() {
                            if (!document.hasFocus()) {
                                @this.call('recordTabSwitch');
                            }
                        }, 100);
                    }
                });

                // Prevent right-click
                document.addEventListener('contextmenu', function(e) {
                    e.preventDefault();
                    alert('Right-click is disabled during the assignment.');
                });

                // Warn before leaving page
                window.addEventListener('beforeunload', function(e) {
                    e.preventDefault();
                    e.returnValue = 'You have an assignment in progress. Are you sure you want to leave? This may count as a violation.';
                    return e.returnValue;
                });

                // Disable certain keyboard shortcuts
                document.addEventListener('keydown', function(e) {
                    // Prevent Alt+Tab detection (limited)
                    if (e.altKey && e.key === 'Tab') {
                        e.preventDefault();
                    }

                    // Prevent F12 (DevTools)
                    if (e.key === 'F12') {
                        e.preventDefault();
                        alert('Developer tools are disabled during the assignment.');
                    }

                    // Prevent Ctrl+Shift+I/J/C (DevTools)
                    if ((e.ctrlKey || e.metaKey) && e.shiftKey && ['I', 'J', 'C'].includes(e.key.toUpperCase())) {
                        e.preventDefault();
                        alert('This shortcut is disabled during the assignment.');
                    }
                });
                @endif
            });

            // Listen for violation warnings
            window.addEventListener('show-violation-warning', event => {
                alert(event.detail.message);
            });
        </script>
</div>
