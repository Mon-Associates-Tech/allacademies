<div class="min-h-screen bg-gray-50 dark:bg-gray-900"
     x-data="{
         timer: @entangle('remainingSeconds'),
         timerStarted: @entangle('timerStarted'),
         init() {
             if (this.timerStarted && this.timer > 0) {
                 setInterval(() => {
                     if (this.timerStarted && this.timer > 0) {
                         $wire.updateTimer();
                     }
                 }, 1000);
             }
         }
     }"
     @keydown.window="
         if ($event.key === 'ArrowRight') $wire.nextQuestion();
         if ($event.key === 'ArrowLeft') $wire.previousQuestion();
     ">

    <!-- Already Submitted State -->
    @if($isSubmitted)
        <div class="min-h-screen flex items-center justify-center p-4">
            <div class="max-w-md w-full bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-8 text-center">
                <div class="w-20 h-20 mx-auto mb-6 bg-green-100 dark:bg-green-900/50 rounded-full flex items-center justify-center">
                    <svg class="w-10 h-10 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">Assignment Submitted!</h2>
                <p class="text-gray-600 dark:text-gray-400 mb-6">Your responses have been recorded successfully.</p>

                @if($canViewResults ?? false)
                    <a href="{{ route('public-assignments.results.token', $submission->result_token) }}"
                       class="inline-block w-full py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-xl transition-colors">
                        View Results
                    </a>
                @else
                    <p class="text-sm text-gray-500 dark:text-gray-400">Results will be available after the instructor releases them.</p>
                @endif
            </div>
        </div>
    @else
        <!-- Instructions Screen -->
        @if($showInstructions)
            <div class="min-h-screen flex items-center justify-center p-4">
                <div class="max-w-2xl w-full bg-white dark:bg-gray-800 rounded-2xl shadow-xl p-8">
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">{{ $assignment->title }}</h1>
                    <p class="text-gray-600 dark:text-gray-400 mb-6">{{ $assignment->description }}</p>

                    <div class="grid grid-cols-2 gap-4 mb-6">
                        <div class="p-4 bg-gray-50 dark:bg-gray-700/50 rounded-xl">
                            <div class="text-sm text-gray-500 dark:text-gray-400">Duration</div>
                            <div class="text-lg font-semibold text-gray-900 dark:text-white">{{ $formattedTime }}</div>
                        </div>
                        <div class="p-4 bg-gray-50 dark:bg-gray-700/50 rounded-xl">
                            <div class="text-sm text-gray-500 dark:text-gray-400">Questions</div>
                            <div class="text-lg font-semibold text-gray-900 dark:text-white">{{ $totalQuestions }}</div>
                        </div>
                    </div>

                    @if($assignment->instructions)
                        <div class="mb-6 p-4 bg-blue-50 dark:bg-blue-900/30 border border-blue-200 dark:border-blue-800 rounded-xl">
                            <h3 class="font-medium text-blue-800 dark:text-blue-300 mb-2">Instructions</h3>
                            <div class="text-sm text-blue-700 dark:text-blue-400 prose prose-sm dark:prose-invert">
                                {!! nl2br(e($assignment->instructions)) !!}
                            </div>
                        </div>
                    @endif

                    @if($assignment->proctoring_enabled)
                        <div class="mb-6 p-4 bg-amber-50 dark:bg-amber-900/30 border border-amber-200 dark:border-amber-800 rounded-xl">
                            <h3 class="font-medium text-amber-800 dark:text-amber-300 mb-2">⚠️ Proctoring Enabled</h3>
                            <ul class="text-sm text-amber-700 dark:text-amber-400 space-y-1">
                                @if($assignment->restrict_navigation)
                                    <li>• Tab switching is monitored (max {{ $assignment->max_tab_switches }} switches)</li>
                                @endif
                                @if($assignment->require_fullscreen)
                                    <li>• Fullscreen mode is required</li>
                                @endif
                                @if($assignment->auto_submit_on_violation)
                                    <li>• Assignment will auto-submit on violations</li>
                                @endif
                            </ul>
                        </div>
                    @endif

                    <button wire:click="startAssignment" class="w-full py-4 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-xl transition-colors text-lg">
                        Start Assignment
                    </button>
                </div>
            </div>
        @else
            <!-- Main Assignment Interface -->
            <div class="flex flex-col h-screen">
                <!-- Top Bar -->
                <div class="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 px-4 py-3">
                    <div class="max-w-7xl mx-auto flex items-center justify-between">
                        <div class="flex items-center gap-4">
                            <h1 class="font-semibold text-gray-900 dark:text-white truncate">{{ $assignment->title }}</h1>
                            @if($assignment->proctoring_enabled && $tabSwitchCount > 0)
                                <span class="px-2 py-1 text-xs bg-red-100 dark:bg-red-900/50 text-red-700 dark:text-red-300 rounded-full">
                                    {{ $tabSwitchCount }} tab switches
                                </span>
                            @endif
                        </div>

                        <!-- Timer -->
                        @if($assignment->duration_in_minutes)
                            <div class="flex items-center gap-2 px-4 py-2 rounded-lg {{ $remainingSeconds < 300 ? 'bg-red-100 dark:bg-red-900/50 text-red-700 dark:text-red-300' : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300' }}">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <span class="font-mono font-semibold">{{ $formattedTime }}</span>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Progress Bar -->
                <div class="bg-gray-200 dark:bg-gray-700 h-1">
                    <div class="bg-indigo-600 h-1 transition-all duration-300" style="width: {{ $progressPercentage }}%"></div>
                </div>

                <!-- Main Content -->
                <div class="flex-1 overflow-hidden flex">
                    <!-- Question Panel -->
                    <div class="flex-1 overflow-y-auto p-6">
                        <div class="max-w-3xl mx-auto">
                            @if($currentQuestion)
                                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg p-6">
                                    <!-- Question Header -->
                                    <div class="flex items-center justify-between mb-4">
                                        <span class="text-sm font-medium text-indigo-600 dark:text-indigo-400">
                                            Question {{ $currentQuestionIndex + 1 }} of {{ $totalQuestions }}
                                        </span>
                                        <span class="text-sm text-gray-500 dark:text-gray-400">
                                            {{ $currentQuestion->marks }} {{ Str::plural('mark', $currentQuestion->marks) }}
                                        </span>
                                    </div>

                                    <!-- Question Text -->
                                    <div class="text-lg text-gray-900 dark:text-white mb-6 prose dark:prose-invert max-w-none">
                                        {!! nl2br(e($currentQuestion->question)) !!}
                                    </div>

                                    <!-- Answer Options -->
                                    @if($currentQuestion->type === 'multiple_choice')
                                        <div class="space-y-3">
                                            @foreach($currentQuestion->options as $key => $option)
                                                <label class="flex items-center p-4 border-2 rounded-xl cursor-pointer transition-all
                                                    {{ ($responses[$currentQuestion->id]['response'] ?? '') === $key
                                                        ? 'border-indigo-500 bg-indigo-50 dark:bg-indigo-900/30'
                                                        : 'border-gray-200 dark:border-gray-600 hover:border-gray-300 dark:hover:border-gray-500' }}">
                                                    <input type="radio"
                                                           wire:model="responses.{{ $currentQuestion->id }}.response"
                                                           value="{{ $key }}"
                                                           class="w-4 h-4 text-indigo-600">
                                                    <span class="ml-3 text-gray-900 dark:text-white">{{ $key }}. {{ $option }}</span>
                                                </label>
                                            @endforeach
                                        </div>
                                    @elseif($currentQuestion->type === 'true_false')
                                        <div class="grid grid-cols-2 gap-4">
                                            @foreach(['true' => 'True', 'false' => 'False'] as $value => $label)
                                                <label class="flex items-center justify-center p-4 border-2 rounded-xl cursor-pointer transition-all
                                                    {{ ($responses[$currentQuestion->id]['response'] ?? '') === $value
                                                        ? 'border-indigo-500 bg-indigo-50 dark:bg-indigo-900/30'
                                                        : 'border-gray-200 dark:border-gray-600 hover:border-gray-300' }}">
                                                    <input type="radio"
                                                           wire:model="responses.{{ $currentQuestion->id }}.response"
                                                           value="{{ $value }}"
                                                           class="sr-only">
                                                    <span class="font-medium text-gray-900 dark:text-white">{{ $label }}</span>
                                                </label>
                                            @endforeach
                                        </div>
                                    @elseif($currentQuestion->type === 'short_answer')
                                        <input type="text"
                                               wire:model.lazy="responses.{{ $currentQuestion->id }}.response"
                                               placeholder="Type your answer..."
                                               class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-indigo-500">
                                    @elseif($currentQuestion->type === 'essay')
                                        <textarea wire:model.lazy="responses.{{ $currentQuestion->id }}.response"
                                                  rows="8"
                                                  placeholder="Write your answer..."
                                                  class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-xl dark:bg-gray-700 dark:text-white focus:ring-2 focus:ring-indigo-500 resize-none"></textarea>
                                    @endif
                                </div>

                                <!-- Navigation Buttons -->
                                <div class="flex items-center justify-between mt-6">
                                    <button wire:click="previousQuestion"
                                            @if($currentQuestionIndex === 0) disabled @endif
                                            class="px-6 py-3 border border-gray-300 dark:border-gray-600 rounded-xl text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 disabled:opacity-50 disabled:cursor-not-allowed transition-colors">
                                        ← Previous
                                    </button>

                                    @if($currentQuestionIndex === $totalQuestions - 1)
                                        <button wire:click="toggleReview"
                                                class="px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl transition-colors">
                                            Review & Submit
                                        </button>
                                    @else
                                        <button wire:click="nextQuestion"
                                                class="px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl transition-colors">
                                            Next →
                                        </button>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Question Navigator Sidebar -->
                    <div class="w-64 bg-white dark:bg-gray-800 border-l border-gray-200 dark:border-gray-700 p-4 overflow-y-auto hidden lg:block">
                        <h3 class="font-medium text-gray-900 dark:text-white mb-4">Questions</h3>
                        <div class="grid grid-cols-5 gap-2">
                            @foreach($questionOrder as $index => $questionId)
                                <button wire:click="goToQuestion({{ $index }})"
                                        class="w-10 h-10 rounded-lg text-sm font-medium transition-all
                                            {{ $currentQuestionIndex === $index ? 'ring-2 ring-indigo-500' : '' }}
                                            {{ $this->isQuestionAnswered($questionId)
                                                ? 'bg-green-100 dark:bg-green-900/50 text-green-700 dark:text-green-300'
                                                : 'bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400' }}">
                                    {{ $index + 1 }}
                                </button>
                            @endforeach
                        </div>
                        <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                <span class="font-medium">{{ $answeredCount }}</span> of {{ $totalQuestions }} answered
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Review Modal -->
            @if($showReview)
                <div class="fixed inset-0 z-50 overflow-y-auto" x-data x-transition>
                    <div class="flex items-center justify-center min-h-screen p-4">
                        <div class="fixed inset-0 bg-black/50" wire:click="toggleReview"></div>
                        <div class="relative bg-white dark:bg-gray-800 rounded-2xl shadow-xl max-w-lg w-full p-6">
                            <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-4">Review Your Answers</h2>

                            <div class="mb-6 p-4 bg-gray-50 dark:bg-gray-700/50 rounded-xl">
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600 dark:text-gray-400">Answered</span>
                                    <span class="font-medium text-gray-900 dark:text-white">{{ $answeredCount }} / {{ $totalQuestions }}</span>
                                </div>
                                <div class="mt-2 bg-gray-200 dark:bg-gray-600 rounded-full h-2">
                                    <div class="bg-green-500 h-2 rounded-full" style="width: {{ $progressPercentage }}%"></div>
                                </div>
                            </div>

                            @if($answeredCount < $totalQuestions)
                                <div class="mb-6 p-4 bg-amber-50 dark:bg-amber-900/30 border border-amber-200 dark:border-amber-800 rounded-xl">
                                    <p class="text-sm text-amber-700 dark:text-amber-300">
                                        ⚠️ You have {{ $totalQuestions - $answeredCount }} unanswered {{ Str::plural('question', $totalQuestions - $answeredCount) }}.
                                    </p>
                                </div>
                            @endif

                            <div class="flex gap-3">
                                <button wire:click="toggleReview" class="flex-1 py-3 border border-gray-300 dark:border-gray-600 rounded-xl text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">
                                    Continue Editing
                                </button>
                                <button wire:click="confirmSubmission" class="flex-1 py-3 bg-green-600 hover:bg-green-700 text-white rounded-xl">
                                    Submit Assignment
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Confirm Submit Modal -->
            @if($confirmSubmit)
                <div class="fixed inset-0 z-50 overflow-y-auto">
                    <div class="flex items-center justify-center min-h-screen p-4">
                        <div class="fixed inset-0 bg-black/50" wire:click="cancelSubmission"></div>
                        <div class="relative bg-white dark:bg-gray-800 rounded-2xl shadow-xl max-w-md w-full p-6 text-center">
                            <div class="w-16 h-16 mx-auto mb-4 bg-amber-100 dark:bg-amber-900/50 rounded-full flex items-center justify-center">
                                <svg class="w-8 h-8 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                                </svg>
                            </div>
                            <h2 class="text-xl font-bold text-gray-900 dark:text-white mb-2">Confirm Submission</h2>
                            <p class="text-gray-600 dark:text-gray-400 mb-6">Are you sure you want to submit? This action cannot be undone.</p>

                            <div class="flex gap-3">
                                <button wire:click="cancelSubmission" class="flex-1 py-3 border border-gray-300 dark:border-gray-600 rounded-xl text-gray-700 dark:text-gray-300">
                                    Cancel
                                </button>
                                <button wire:click="submitAssignment" class="flex-1 py-3 bg-green-600 hover:bg-green-700 text-white rounded-xl">
                                    Yes, Submit
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @endif
        @endif
    @endif
</div>
