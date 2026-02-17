<div class="py-6">
    <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
            <div>
                <a href="{{ route('teachers.public-assignments.results', $assignment) }}" class="text-sm text-indigo-600 hover:text-indigo-700 dark:text-indigo-400 flex items-center gap-1 mb-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                    Back to Results
                </a>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Grade Submission</h1>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">{{ $assignment->title }}</p>
            </div>
            <div class="flex items-center gap-3">
                <button wire:click="saveAllGrades" class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">
                    Save All
                </button>
                <button wire:click="finalizeGrading" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white font-medium rounded-lg transition-colors">
                    Finalize Grading
                </button>
            </div>
        </div>

        <!-- Flash Messages -->
        @if(session('success'))
            <div class="mb-6 p-4 bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-800 rounded-lg text-green-700 dark:text-green-300">
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div class="mb-6 p-4 bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800 rounded-lg text-red-700 dark:text-red-300">
                {{ session('error') }}
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left Column: Participant Info & Progress -->
            <div class="lg:col-span-1 space-y-6">
                <!-- Participant Info -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <h2 class="font-semibold text-gray-900 dark:text-white mb-4">Participant</h2>
                    <div class="space-y-3">
                        <div>
                            <div class="text-sm text-gray-500 dark:text-gray-400">Name</div>
                            <div class="font-medium text-gray-900 dark:text-white">{{ $participantInfo['name'] }}</div>
                        </div>
                        <div>
                            <div class="text-sm text-gray-500 dark:text-gray-400">Email</div>
                            <div class="text-gray-900 dark:text-white">{{ $participantInfo['email'] }}</div>
                        </div>
                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <div class="text-sm text-gray-500 dark:text-gray-400">Started</div>
                                <div class="text-sm text-gray-900 dark:text-white">{{ $participantInfo['started_at'] ?? 'N/A' }}</div>
                            </div>
                            <div>
                                <div class="text-sm text-gray-500 dark:text-gray-400">Submitted</div>
                                <div class="text-sm text-gray-900 dark:text-white">{{ $participantInfo['submitted_at'] ?? 'N/A' }}</div>
                            </div>
                        </div>
                        <div>
                            <div class="text-sm text-gray-500 dark:text-gray-400">Time Spent</div>
                            <div class="text-gray-900 dark:text-white">{{ $participantInfo['time_spent'] }}</div>
                        </div>
                    </div>
                </div>

                <!-- Grading Progress -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <h2 class="font-semibold text-gray-900 dark:text-white mb-4">Grading Progress</h2>
                    <div class="mb-4">
                        <div class="flex justify-between text-sm mb-1">
                            <span class="text-gray-600 dark:text-gray-400">{{ $gradingProgress['graded'] }} of {{ $gradingProgress['total'] }} graded</span>
                            <span class="font-medium text-gray-900 dark:text-white">{{ $gradingProgress['percentage'] }}%</span>
                        </div>
                        <div class="bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                            <div class="bg-indigo-600 h-2 rounded-full transition-all" style="width: {{ $gradingProgress['percentage'] }}%"></div>
                        </div>
                    </div>
                    <div class="text-center p-4 bg-gray-50 dark:bg-gray-700/50 rounded-xl">
                        <div class="text-3xl font-bold text-gray-900 dark:text-white">
                            {{ number_format($submission->score ?? 0, 1) }}%
                        </div>
                        <div class="text-sm text-gray-500 dark:text-gray-400">Current Score</div>
                    </div>
                </div>

                <!-- Proctoring Info -->
                @if($proctoringInfo)
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h2 class="font-semibold text-gray-900 dark:text-white">Proctoring</h2>
                            <button wire:click="toggleProctoringDetails" class="text-sm text-indigo-600 hover:text-indigo-700">
                                {{ $showProctoringDetails ? 'Hide' : 'Show' }} Details
                            </button>
                        </div>
                        <div class="flex items-center gap-2 mb-3">
                            <span class="w-3 h-3 rounded-full {{ $proctoringInfo['is_valid'] ? 'bg-green-500' : 'bg-red-500' }}"></span>
                            <span class="font-medium {{ $proctoringInfo['is_valid'] ? 'text-green-600' : 'text-red-600' }}">
                                {{ $proctoringInfo['is_valid'] ? 'Valid Session' : 'Flagged Session' }}
                            </span>
                        </div>
                        @if($showProctoringDetails && $proctoringInfo['violations'])
                            <div class="mt-4 space-y-2">
                                @foreach($proctoringInfo['violations'] as $violation => $count)
                                    <div class="flex justify-between text-sm">
                                        <span class="text-gray-600 dark:text-gray-400">{{ ucfirst(str_replace('_', ' ', $violation)) }}</span>
                                        <span class="font-medium text-gray-900 dark:text-white">{{ $count }}</span>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @endif

                <!-- Question Navigator -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <h2 class="font-semibold text-gray-900 dark:text-white mb-4">Questions</h2>
                    <div class="grid grid-cols-5 gap-2">
                        @foreach($questions as $index => $question)
                            <button wire:click="goToQuestion({{ $index }})"
                                    class="w-10 h-10 rounded-lg text-sm font-medium transition-all
                                        {{ $currentQuestionIndex === $index ? 'ring-2 ring-indigo-500' : '' }}
                                        {{ ($questionGrades[$question->id]['is_graded'] ?? false)
                                            ? 'bg-green-100 dark:bg-green-900/50 text-green-700 dark:text-green-300'
                                            : 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400' }}">
                                {{ $index + 1 }}
                            </button>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Right Column: Question Grading -->
            <div class="lg:col-span-2">
                @if($currentQuestion)
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                        <!-- Question Header -->
                        <div class="flex items-center justify-between mb-4">
                            <span class="text-sm font-medium text-indigo-600 dark:text-indigo-400">
                                Question {{ $currentQuestionIndex + 1 }} of {{ count($questions) }}
                            </span>
                            <span class="px-2 py-1 text-xs bg-gray-100 dark:bg-gray-700 rounded">
                                {{ ucfirst(str_replace('_', ' ', $currentQuestion->type)) }} • {{ $currentQuestion->marks }} marks
                            </span>
                        </div>

                        <!-- Question Text -->
                        <div class="mb-6 p-4 bg-gray-50 dark:bg-gray-700/50 rounded-xl">
                            <h3 class="font-medium text-gray-900 dark:text-white mb-2">Question</h3>
                            <p class="text-gray-700 dark:text-gray-300">{{ $currentQuestion->question }}</p>
                        </div>

                        <!-- Correct Answer (for auto-gradable questions) -->
                        @if(in_array($currentQuestion->type, ['multiple_choice', 'true_false']))
                            <div class="mb-6 p-4 bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-800 rounded-xl">
                                <h3 class="font-medium text-green-800 dark:text-green-300 mb-2">Correct Answer</h3>
                                @if($currentQuestion->type === 'multiple_choice')
                                    <p class="text-green-700 dark:text-green-400">
                                        {{ $currentQuestion->correct_answer }}. {{ $currentQuestion->options[$currentQuestion->correct_answer] ?? '' }}
                                    </p>
                                @else
                                    <p class="text-green-700 dark:text-green-400">{{ ucfirst($currentQuestion->correct_answer) }}</p>
                                @endif
                            </div>
                        @endif

                        <!-- Student's Response -->
                        <div class="mb-6 p-4 bg-blue-50 dark:bg-blue-900/30 border border-blue-200 dark:border-blue-800 rounded-xl">
                            <h3 class="font-medium text-blue-800 dark:text-blue-300 mb-2">Student's Response</h3>
                            @php
                                $response = $this->getResponseForQuestion($currentQuestion->id);
                            @endphp
                            @if($response)
                                @if($currentQuestion->type === 'multiple_choice')
                                    <p class="text-blue-700 dark:text-blue-400">
                                        {{ $response }}. {{ $currentQuestion->options[$response] ?? '' }}
                                    </p>
                                @elseif($currentQuestion->type === 'true_false')
                                    <p class="text-blue-700 dark:text-blue-400">{{ ucfirst($response) }}</p>
                                @else
                                    <p class="text-blue-700 dark:text-blue-400 whitespace-pre-wrap">{{ $response }}</p>
                                @endif
                            @else
                                <p class="text-gray-400 italic">No response provided</p>
                            @endif
                        </div>

                        <!-- Grading Form -->
                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Points (max: {{ $currentQuestion->marks }})
                                </label>
                                <input type="number"
                                       wire:model="questionGrades.{{ $currentQuestion->id }}.points"
                                       min="0"
                                       max="{{ $currentQuestion->marks }}"
                                       step="0.5"
                                       class="w-32 px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Feedback</label>
                                <textarea wire:model="questionGrades.{{ $currentQuestion->id }}.feedback"
                                          rows="3"
                                          placeholder="Optional feedback for this question..."
                                          class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white"></textarea>
                            </div>
                            <button wire:click="saveQuestionGrade({{ $currentQuestion->id }})"
                                    class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg transition-colors">
                                Save Grade
                            </button>
                        </div>

                        <!-- Navigation -->
                        <div class="flex items-center justify-between mt-8 pt-6 border-t border-gray-200 dark:border-gray-700">
                            <button wire:click="previousQuestion"
                                    @if($currentQuestionIndex === 0) disabled @endif
                                    class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 disabled:opacity-50 disabled:cursor-not-allowed">
                                ← Previous
                            </button>
                            <button wire:click="nextQuestion"
                                    @if($currentQuestionIndex === count($questions) - 1) disabled @endif
                                    class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg disabled:opacity-50 disabled:cursor-not-allowed">
                                Next →
                            </button>
                        </div>
                    </div>
                @endif

                <!-- Overall Feedback -->
                <div class="mt-6 bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <h2 class="font-semibold text-gray-900 dark:text-white mb-4">Overall Feedback</h2>
                    <textarea wire:model="overallFeedback"
                              rows="4"
                              placeholder="Provide overall feedback for this submission..."
                              class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg dark:bg-gray-700 dark:text-white"></textarea>
                </div>
            </div>
        </div>
    </div>
</div>
