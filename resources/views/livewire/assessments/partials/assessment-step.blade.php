<div class="max-w-6xl mx-auto">
    <!-- Progress Header -->
    <div class="bg-gradient-to-r from-green-600 to-teal-600 rounded-xl p-6 text-white mb-8 shadow-xl">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold mb-2">Assessment in Progress</h1>
                <p class="text-green-100 text-lg">Answer each question carefully</p>
            </div>
            <div class="hidden md:block">
                <div class="text-center bg-white/20 backdrop-blur-sm rounded-xl p-4">
                    <div class="text-2xl font-bold text-yellow-300">{{ $currentQuestionIndex + 1 }}</div>
                    <div class="text-sm text-green-200">of {{ count($questions) }}</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Progress Bar -->
    <div class="mb-8">
        <div class="flex justify-between items-center mb-2">
            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">
                Question {{ $currentQuestionIndex + 1 }} of {{ count($questions) }}
            </span>
            <span class="text-sm text-gray-500 dark:text-gray-400">
                {{ round((($currentQuestionIndex + 1) / count($questions)) * 100) }}% Complete
            </span>
        </div>
        <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
            <div class="bg-gradient-to-r from-green-600 to-teal-600 h-2 rounded-full transition-all duration-300"
                 style="width: {{ (($currentQuestionIndex + 1) / count($questions)) * 100 }}%"></div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
        <!-- Question Navigation Sidebar -->
        <div class="lg:col-span-1 order-2 lg:order-1">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 sticky top-4">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Questions</h3>
                <div class="grid grid-cols-5 lg:grid-cols-1 gap-2">
                    @foreach($questions as $index => $question)
                        @php
                            $isAnswered = $this->isQuestionAnswered($index);
                        @endphp
                        <button wire:click="goToQuestion({{ $index }})"
                                class="w-full p-3 rounded-lg text-sm font-medium transition-all duration-200 flex items-center justify-center
                                    {{ $index === $currentQuestionIndex ? 'bg-indigo-600 text-white shadow-lg' :
                                       ($isAnswered ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400 border border-green-200 dark:border-green-600' : 'bg-gray-100 text-gray-600 dark:bg-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-600') }}">
                            <span class="lg:hidden">{{ $index + 1 }}</span>
                            <span class="hidden lg:block">
                                Question {{ $index + 1 }}
                                @if($isAnswered)
                                    <svg class="w-4 h-4 ml-2 inline-block" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                              d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                              clip-rule="evenodd"/>
                                    </svg>
                                @endif
                            </span>
                        </button>
                    @endforeach
                </div>

                <!-- Summary -->
                <div class="mt-6 p-4 bg-gray-50 dark:bg-gray-700 rounded-lg">
                    <div class="text-sm text-gray-600 dark:text-gray-400 space-y-1">
                        <div class="flex justify-between">
                            <span>Answered:</span>
                            <span class="font-medium">{{ $this->getAnsweredCount() }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Remaining:</span>
                            <span class="font-medium">{{ count($questions) - $this->getAnsweredCount() }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Question Area -->
        <div class="lg:col-span-3 order-1 lg:order-2">
            @if(isset($questions[$currentQuestionIndex]))
                @php
                    $question = $questions[$currentQuestionIndex];
                    $response = $responses[$currentQuestionIndex] ?? ['answer' => null];
                @endphp

                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-8">
                    <!-- Question Header -->
                    <div class="flex items-center justify-between mb-6">
                        <div class="flex items-center space-x-3">
                            <div
                                class="w-10 h-10 bg-indigo-100 dark:bg-indigo-800 rounded-full flex items-center justify-center">
                                <span
                                    class="text-indigo-600 dark:text-indigo-400 font-bold text-sm">{{ $currentQuestionIndex + 1 }}</span>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                                    Question {{ $currentQuestionIndex + 1 }}</h3>
                                <p class="text-sm text-gray-600 dark:text-gray-400">{{ ucfirst(str_replace('_', ' ', $question['type'])) }}</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="text-sm text-gray-500 dark:text-gray-400">Points</div>
                            <div
                                class="text-lg font-bold text-indigo-600 dark:text-indigo-400">{{ $question['points'] ?? 1 }}</div>
                        </div>
                    </div>

                    <!-- Question Content -->
                    <div class="mb-8">
                        <div class="text-lg text-gray-900 dark:text-white leading-relaxed">
                            {!! $question['formatted']['question'] !!}
                        </div>
                    </div>

                    <!-- Answer Input Based on Question Type -->
                    <div class="space-y-4">
                        @if($question['type'] === 'multiple_choice_question')
                            <div class="space-y-3">
                                @foreach($question['formatted']['options'] as $optionKey => $option)
                                    <label
                                        class="flex items-center p-4 border border-gray-200 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer transition-all duration-200">
                                        <input type="radio"
                                               wire:model.live="responses.{{ $currentQuestionIndex }}.answer"
                                               value="{{ $optionKey }}"
                                               class="w-4 h-4 text-indigo-600 bg-gray-100 border-gray-300 focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                                        <span class="ml-3 text-sm font-medium text-gray-700 dark:text-gray-300">
            {{ $optionKey }}. {{ $option }}
        </span>
                                    </label>
                                @endforeach
                            </div>

                        @elseif($question['type'] === 'true_or_false_question')
                            <div class="space-y-3">
                                <label
                                    class="flex items-center p-4 border border-gray-200 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer transition-all duration-200">
                                    <input type="radio"
                                           wire:model.changed="responses.{{ $currentQuestionIndex }}.answer"
                                           value="true"
                                           class="w-4 h-4 text-indigo-600 bg-gray-100 border-gray-300 focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                                    <span class="ml-3 text-sm font-medium text-gray-700 dark:text-gray-300">True</span>
                                </label>
                                <label
                                    class="flex items-center p-4 border border-gray-200 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer transition-all duration-200">
                                    <input type="radio"
                                           wire:model.changed="responses.{{ $currentQuestionIndex }}.answer"
                                           value="false"
                                           class="w-4 h-4 text-indigo-600 bg-gray-100 border-gray-300 focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                                    <span class="ml-3 text-sm font-medium text-gray-700 dark:text-gray-300">False</span>
                                </label>
                            </div>

                        @elseif($question['type'] === 'essay_question')
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Your
                                    Answer:</label>
                                <textarea wire:model.changed="responses.{{ $currentQuestionIndex }}.answer"
                                          class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-700 dark:text-white"
                                          rows="8"
                                          placeholder="Type your answer here..."></textarea>
                                <div class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                    Word count: {{ str_word_count($response['answer'] ?? '') }}
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- Navigation Buttons -->
                    <div
                        class="flex justify-between items-center mt-8 pt-6 border-t border-gray-200 dark:border-gray-600">
                        <button wire:click="previousQuestion"
                                class="flex items-center px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:bg-gray-700 dark:text-gray-300 dark:border-gray-600 dark:hover:bg-gray-600
                                {{ $currentQuestionIndex === 0 ? 'opacity-50 cursor-not-allowed' : '' }}"
                            {{ $currentQuestionIndex === 0 ? 'disabled' : '' }}>
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M15 19l-7-7 7-7"/>
                            </svg>
                            Previous
                        </button>

                        <div class="flex items-center space-x-4">
                            <!-- Answer Status -->
                            @if($this->isQuestionAnswered($currentQuestionIndex))
                                <div class="flex items-center text-green-600 dark:text-green-400">
                                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                              d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                              clip-rule="evenodd"/>
                                    </svg>
                                    <span class="text-sm font-medium">Answered</span>
                                </div>
                            @else
                                <div class="flex items-center text-gray-400">
                                    <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                              d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                              clip-rule="evenodd"/>
                                    </svg>
                                    <span class="text-sm font-medium">Not answered</span>
                                </div>
                            @endif

                            <!-- Next/Submit Button -->
                            @if($currentQuestionIndex === count($questions) - 1)
                                <button wire:click="submitAssessment"
                                        class="flex items-center px-6 py-2 text-sm font-medium text-white bg-gradient-to-r from-green-600 to-teal-600 rounded-md hover:from-green-700 hover:to-teal-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-all duration-200">
                                    Submit Assessment
                                    <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M5 13l4 4L19 7"/>
                                    </svg>
                                </button>
                            @else
                                <button wire:click="nextQuestion"
                                        class="flex items-center px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-md hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                    Next
                                    <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M9 5l7 7-7 7"/>
                                    </svg>
                                </button>
                            @endif
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Submit Assessment Modal/Section -->
    @if($this->getCanSubmitProperty())
        <div
            class="mt-8 bg-gradient-to-r from-green-50 to-teal-50 dark:from-green-900/20 dark:to-teal-900/20 rounded-xl p-6 border border-green-200 dark:border-green-600">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-green-100 dark:bg-green-800 rounded-full flex items-center justify-center">
                        <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                  d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                  clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-green-900 dark:text-green-400">Ready to Submit</h3>
                        <p class="text-sm text-green-700 dark:text-green-300">
                            You have answered {{ $this->getAnsweredCount() }} out of {{ count($questions) }} questions.
                        </p>
                    </div>
                </div>
                <button wire:click="submitAssessment"
                        class="px-6 py-3 bg-gradient-to-r from-green-600 to-teal-600 text-white rounded-lg hover:from-green-700 hover:to-teal-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-all duration-200 font-medium">
                    Submit Assessment
                </button>
            </div>
        </div>
    @endif
</div>
