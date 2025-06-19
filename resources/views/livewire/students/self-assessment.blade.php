<div>
    @if($step === 'setup')
        <div class="max-w-5xl mx-auto space-y-8">
            <!-- Enhanced Header Section -->
            <div
                class="bg-gradient-to-r from-indigo-500 via-purple-600 to-pink-600 rounded-xl p-8 text-white shadow-lg relative overflow-hidden">
                <div class="absolute top-0 right-0 -mt-4 -mr-4 w-24 h-24 bg-white/10 rounded-full blur-2xl"></div>
                <div class="absolute bottom-0 left-0 -mb-4 -ml-4 w-32 h-32 bg-white/10 rounded-full blur-2xl"></div>

                <div class="relative flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <div class="p-4 bg-white/20 backdrop-blur-sm rounded-full">
                            <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                        <div>
                            <h1 class="text-4xl font-bold">Create Assessment</h1>
                            <p class="text-indigo-100 mt-2 text-lg">Design your personalized learning assessment</p>
                        </div>
                    </div>

                    <!-- Progress Indicator -->
                    <div class="hidden lg:block text-center bg-white/10 backdrop-blur-sm rounded-xl p-4">
                        <div class="text-2xl font-bold text-yellow-300">Step 1</div>
                        <div class="text-sm text-indigo-200">Setup</div>
                    </div>
                </div>
            </div>

            <!-- Enhanced Form Container -->
            <div
                class="bg-white dark:bg-gray-800 rounded-xl shadow-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
                <!-- Progress Bar -->
                <div class="h-2 bg-gray-200 dark:bg-gray-700">
                    <div class="h-2 bg-gradient-to-r from-indigo-500 to-purple-600 rounded-full"
                         style="width: 33%"></div>
                </div>

                <div class="p-8 space-y-10">

                    <!-- Content Selection Section -->
                    <div class="space-y-6">
                        <div class="flex items-center space-x-3">
                            <div class="p-3 bg-blue-100 dark:bg-blue-900/30 rounded-xl">
                                <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor"
                                     viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                </svg>
                            </div>
                            <div>
                                <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Content Selection</h2>
                                <p class="text-gray-600 dark:text-gray-400">Choose what you want to be assessed on</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                            <!-- Subject Selection -->
                            <div class="space-y-3">
                                <label
                                    class="flex items-center space-x-2 text-sm font-semibold text-gray-700 dark:text-gray-300">
                                    <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor"
                                         viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                    </svg>
                                    <span>Subject <span class="text-red-500">*</span></span>
                                </label>
                                <div class="relative">
                                    <select id="subject" wire:model.live="selectedSubject"
                                            class="w-full pl-4 pr-10 py-4 rounded-xl border-2 border-gray-200 dark:border-gray-600 dark:bg-gray-700 bg-gray-50 dark:bg-gray-700/50 text-gray-900 dark:text-gray-100 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 dark:focus:ring-indigo-600 focus:ring-opacity-50 transition-all duration-200">
                                        <option value="">📚 Choose a subject</option>
                                        @foreach($subjects as $subject)
                                            <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                                        @endforeach
                                    </select>
                                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3">
                                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor"
                                             viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M19 9l-7 7-7-7"/>
                                        </svg>
                                    </div>
                                </div>
                                @error('selectedSubject')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                @enderror
                            </div>

                            <!-- Topic Selection -->
                            <div class="space-y-3">
                                <label
                                    class="flex items-center space-x-2 text-sm font-semibold text-gray-700 dark:text-gray-300">
                                    <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor"
                                         viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707L13.414 3.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                    </svg>
                                    <span>Topic (Optional)</span>
                                </label>
                                <div class="relative">
                                    <select id="topic" wire:model.live="selectedTopic"
                                            class="w-full pl-4 pr-10 py-4 rounded-xl border-2 border-gray-200 dark:border-gray-600 dark:bg-gray-700 bg-gray-50 dark:bg-gray-700/50 text-gray-900 dark:text-gray-100 shadow-sm focus:border-green-500 focus:ring-2 focus:ring-green-200 dark:focus:ring-green-600 focus:ring-opacity-50 transition-all duration-200 {{ !$selectedSubject ? 'opacity-50 cursor-not-allowed' : '' }}"
                                        {{ !$selectedSubject ? 'disabled' : '' }}>
                                        <option value="">🔍 Choose a topic (optional)</option>
                                        @if(isset($topics))
                                            @foreach($topics as $topic)
                                                <option value="{{ $topic->id }}">{{ $topic->name }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3">
                                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor"
                                             viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M19 9l-7 7-7-7"/>
                                        </svg>
                                    </div>
                                </div>
                            </div>

                            <!-- Subtopic Selection -->
                            <div class="space-y-3">
                                <label
                                    class="flex items-center space-x-2 text-sm font-semibold text-gray-700 dark:text-gray-300">
                                    <svg class="w-4 h-4 text-purple-500" fill="none" stroke="currentColor"
                                         viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                    <span>Subtopic (Optional)</span>
                                </label>
                                <div class="relative">
                                    <select id="subtopic" wire:model.live="selectedSubtopic"
                                            class="w-full pl-4 pr-10 py-4 rounded-xl border-2 border-gray-200 dark:border-gray-600 dark:bg-gray-700 bg-gray-50 dark:bg-gray-700/50 text-gray-900 dark:text-gray-100 shadow-sm focus:border-purple-500 focus:ring-2 focus:ring-purple-200 dark:focus:ring-purple-600 focus:ring-opacity-50 transition-all duration-200 {{ !$selectedTopic ? 'opacity-50 cursor-not-allowed' : '' }}"
                                        {{ !$selectedTopic ? 'disabled' : '' }}>
                                        <option value="">🎯 Choose a subtopic (optional)</option>
                                        @if(isset($subtopics))
                                            @foreach($subtopics as $subtopic)
                                                <option value="{{ $subtopic->id }}">{{ $subtopic->name }}</option>
                                            @endforeach
                                        @endif
                                    </select>
                                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3">
                                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor"
                                             viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M19 9l-7 7-7-7"/>
                                        </svg>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Assessment Configuration Section -->
                    <div class="space-y-6">
                        <div class="flex items-center space-x-3">
                            <div class="p-3 bg-orange-100 dark:bg-orange-900/30 rounded-xl">
                                <svg class="w-6 h-6 text-orange-600 dark:text-orange-400" fill="none"
                                     stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                            </div>
                            <div>
                                <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Assessment
                                    Configuration</h2>
                                <p class="text-gray-600 dark:text-gray-400">Customize your assessment parameters</p>
                            </div>
                        </div>

                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                            <!-- Question Types -->
                            <div class="space-y-4">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Question Types</h3>
                                <div class="space-y-3">
                                    <label
                                        class="flex items-center p-4 bg-gray-50 dark:bg-gray-700/50 rounded-xl border border-gray-200 dark:border-gray-600 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors cursor-pointer">
                                        <input type="checkbox" wire:model="questionTypes.multiple_choice_question"
                                               class="h-5 w-5 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                                        <div class="ml-4 flex-1">
                                            <div class="flex items-center space-x-2">
                                                <span class="text-2xl">🔘</span>
                                                <span class="font-medium text-gray-900 dark:text-gray-100">Multiple Choice</span>
                                            </div>
                                            <p class="text-sm text-gray-600 dark:text-gray-400">Questions with multiple
                                                answer options</p>
                                        </div>
                                    </label>

                                    <label
                                        class="flex items-center p-4 bg-gray-50 dark:bg-gray-700/50 rounded-xl border border-gray-200 dark:border-gray-600 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors cursor-pointer">
                                        <input type="checkbox" wire:model="questionTypes.true_or_false_question"
                                               class="h-5 w-5 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                                        <div class="ml-4 flex-1">
                                            <div class="flex items-center space-x-2">
                                                <span class="text-2xl">✅</span>
                                                <span
                                                    class="font-medium text-gray-900 dark:text-gray-100">True or False</span>
                                            </div>
                                            <p class="text-sm text-gray-600 dark:text-gray-400">Simple true/false
                                                questions</p>
                                        </div>
                                    </label>

                                    <label
                                        class="flex items-center p-4 bg-gray-50 dark:bg-gray-700/50 rounded-xl border border-gray-200 dark:border-gray-600 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors cursor-pointer">
                                        <input type="checkbox" wire:model="questionTypes.essay_question"
                                               class="h-5 w-5 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                                        <div class="ml-4 flex-1">
                                            <div class="flex items-center space-x-2">
                                                <span class="text-2xl">📝</span>
                                                <span class="font-medium text-gray-900 dark:text-gray-100">Essay Questions</span>
                                            </div>
                                            <p class="text-sm text-gray-600 dark:text-gray-400">Open-ended written
                                                responses</p>
                                        </div>
                                    </label>
                                </div>
                            </div>

                            <!-- Assessment Parameters -->
                            <div class="space-y-4">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">Parameters</h3>
                                <div class="space-y-4">
                                    <!-- Number of Questions -->
                                    <div>
                                        <label for="questionCount"
                                               class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                            Number of Questions
                                        </label>
                                        <div class="relative">
                                            <input type="number" id="questionCount" wire:model="questionCount" min="1"
                                                   max="50"
                                                   class="w-full pl-4 pr-10 py-3 rounded-xl border-2 border-gray-200 dark:border-gray-600 dark:bg-gray-700 bg-gray-50 dark:bg-gray-700/50 text-gray-900 dark:text-gray-100 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 dark:focus:ring-indigo-600 focus:ring-opacity-50 transition-all duration-200">
                                            <div
                                                class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none">
                                                <span class="text-gray-400 text-sm">questions</span>
                                            </div>
                                        </div>
                                        <div class="mt-1 flex justify-between text-xs text-gray-500">
                                            <span>Minimum: 1</span>
                                            <span>Maximum: 50</span>
                                        </div>
                                        @error('questionCount')
                                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <!-- Difficulty Level -->
                                    <div>
                                        <label for="difficulty"
                                               class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                            Difficulty Level
                                        </label>
                                        <select id="difficulty" wire:model="difficulty"
                                                class="w-full pl-4 pr-10 py-3 rounded-xl border-2 border-gray-200 dark:border-gray-600 dark:bg-gray-700 bg-gray-50 dark:bg-gray-700/50 text-gray-900 dark:text-gray-100 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-200 dark:focus:ring-indigo-600 focus:ring-opacity-50 transition-all duration-200">
                                            <option value="all">🎯 All Levels</option>
                                            <option value="easy">🟢 Easy</option>
                                            <option value="medium">🟡 Medium</option>
                                            <option value="hard">🔴 Hard</option>
                                        </select>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div
                        class="flex flex-col sm:flex-row sm:justify-between sm:items-center space-y-4 sm:space-y-0 pt-6 border-t border-gray-200 dark:border-gray-700">
                        <div class="text-sm text-gray-600 dark:text-gray-400">
                            <span class="font-medium">Note:</span> Select at least one question type to proceed
                        </div>

                        <div class="flex space-x-4">
                            <button type="button"
                                    class="inline-flex items-center px-6 py-3 border border-gray-300 dark:border-gray-600 shadow-sm text-sm font-medium rounded-xl text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                                </svg>
                                Reset
                            </button>

                            <button wire:click="startAssessment"
                                    class="inline-flex items-center px-8 py-3 border border-transparent text-sm font-medium rounded-xl text-white bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 shadow-lg transform hover:scale-105 transition-all duration-200">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                </svg>
                                Start Assessment
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Assessment and Results phases would follow similar enhancement patterns -->
    @if($step === 'assessment')
        @include('livewire.students.partials.assessment-in-progress')
          {{--  <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center">
                    <h3 class="text-lg font-semibold">Self Assessment</h3>
                    <div
                        class="text-sm font-medium bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-200 px-3 py-1 rounded-full"
                        x-data="{ time: {{ $timeRemaining }}, intervalId: null }"
                        x-init="
        if (time > 0) {
            intervalId = setInterval(() => {
                if (time > 0) {
                    time--;
                } else {
                    clearInterval(intervalId);
                    @this.completeAssessment();
                }
            }, 1000);
        }

        // Cleanup on destroy
        $watch('time', value => {
            if (value <= 0 && intervalId) {
                clearInterval(intervalId);
                @this.completeAssessment();
            }
        });
    "
                        x-text="Math.floor(time / 60) + ':' + (time % 60).toString().padStart(2, '0')">
                    </div>

                </div>
                <!-- Rest of assessment content... -->

                <div class="p-4">
                    <!-- Question Navigation -->
                    <div class="flex flex-wrap gap-2 mb-6">
                        @foreach($questions as $index => $question)
                            <button
                                wire:click="jumpToQuestion({{ $index }})"
                                class="w-8 h-8 flex items-center justify-center rounded-full text-sm font-medium
                                @if($currentQuestionIndex === $index)
                                bg-indigo-600 text-white
                                @else
                                {{ $responses[$index]['is_answered'] ? 'bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200' : 'bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200' }}
                               @endif"
                            >
                                {{ $index + 1 }}
                            </button>
                        @endforeach
                    </div>


                    <!-- Question Display -->
                    @if(isset($questions[$currentQuestionIndex]))
                        <div class="mb-8">
                            <div class="flex justify-between mb-4">
            <span class="text-sm font-medium text-gray-500 dark:text-gray-400">
                Question {{ $currentQuestionIndex + 1 }} of {{ count($questions) }}
            </span>
                                <span class="text-sm font-medium px-2 py-1 rounded
                @if($questions[$currentQuestionIndex]['difficulty_level'] === 'easy') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                @elseif($questions[$currentQuestionIndex]['difficulty_level'] === 'medium') bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200
                @elseif($questions[$currentQuestionIndex]['difficulty_level'] === 'hard') bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200
                @endif">
                {{ ucfirst($questions[$currentQuestionIndex]['difficulty_level']) }}
            </span>
                            </div>

                            <!-- Question Text -->
                            <div class="mb-6">
                                <h4 class="text-lg font-medium mb-4">
                                    {!! $questions[$currentQuestionIndex]['question_record']['questionable']->question->down !!}
                                </h4>

                                @php
                                    $type = class_basename($questions[$currentQuestionIndex]['question_record']['questionable_type']);
                                @endphp

                                    <!-- Multiple Choice -->
                                @if ($type === 'MultipleChoiceQuestion')
                                    @php
                                        $options = [];
                                        foreach(['a', 'b', 'c', 'd', 'e'] as $letter) {
                                            if (!is_null($questions[$currentQuestionIndex]['question_record']['questionable']->{'option_'.$letter}->down)) {
                                                $options[] = ['label' => strtoupper($letter), 'value' => $questions[$currentQuestionIndex]['question_record']['questionable']->{'option_'.$letter}];
                                            }
                                        }
                                    @endphp

                                    @foreach ($options as $option)
                                        <div class="flex items-center mb-3">
                                            <input type="radio"
                                                   id="option-{{ $loop->index }}-{{ $currentQuestionIndex }}"
                                                   name="response_{{ $currentQuestionIndex }}"
                                                   value="{{ $option['label'] }}"
                                                   wire:click="saveResponse({{ $currentQuestionIndex }}, '{{ $option['label'] }}')"
                                                   @if ($responses[$currentQuestionIndex]['response'] === $option['label']) checked
                                                   @endif
                                                   class="w-4 h-4 text-indigo-600 border-gray-300 focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:bg-gray-700 dark:border-gray-600"
                                            >
                                            <label for="option-{{ $loop->index }}-{{ $currentQuestionIndex }}"
                                                   class="ml-2 block text-sm font-medium text-gray-900 dark:text-gray-300">
                                                {{ $option['label'] }}. {{ $option['value']->down }}
                                            </label>
                                        </div>
                                    @endforeach


                                    <!-- True/False -->
                                @elseif ($type === 'TrueOrFalseQuestion')
                                    <div class="space-y-3">
                                        <div class="flex items-center">
                                            <input type="radio"
                                                   id="true_{{ $currentQuestionIndex }}"
                                                   name="response_{{ $currentQuestionIndex }}"
                                                   value="true"
                                                   wire:click="saveResponse({{ $currentQuestionIndex }}, 'true')"
                                                   @if ($responses[$currentQuestionIndex]['response'] === 'true') checked @endif
                                                   class="w-4 h-4 text-indigo-600 border-gray-300 focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:bg-gray-700 dark:border-gray-600"
                                            >
                                            <label for="true_{{ $currentQuestionIndex }}"
                                                   class="ml-2 block text-sm font-medium text-gray-900 dark:text-gray-300">True</label>
                                        </div>

                                        <div class="flex items-center">
                                            <input type="radio"
                                                   id="false_{{ $currentQuestionIndex }}"
                                                   name="response_{{ $currentQuestionIndex }}"
                                                   value="false"
                                                   wire:click="saveResponse({{ $currentQuestionIndex }}, 'false')"
                                                   @if ($responses[$currentQuestionIndex]['response'] === 'false') checked
                                                   @endif
                                                   class="w-4 h-4 text-indigo-600 border-gray-300 focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:bg-gray-700 dark:border-gray-600"
                                            >
                                            <label for="false_{{ $currentQuestionIndex }}"
                                                   class="ml-2 block text-sm font-medium text-gray-900 dark:text-gray-300">False</label>
                                        </div>
                                    </div>

                                    <!-- Essay -->
                                @elseif ($type === 'EssayQuestion')
                                    <textarea
                                        class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 dark:focus:ring-indigo-600 focus:ring-opacity-50"
                                        rows="6"
                                        placeholder="Write your answer here..."
                                        wire:model.lazy="responses.{{ $currentQuestionIndex }}.response"
                                    >{{ $responses[$currentQuestionIndex]['response'] }}</textarea>
                                @endif
                            </div>
                        </div>
                    @endif
                    <!-- Navigation Buttons -->
                    <div class="mt-6 flex justify-between">
                        <button
                            wire:click="previousQuestion"
                            class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-md shadow-sm text-sm font-medium text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800"
                            {{ $currentQuestionIndex === 0 ? 'disabled' : '' }}
                        >
                            Previous
                        </button>

                        <button
                            wire:click="{{ $currentQuestionIndex < count($questions) - 1 ? 'nextQuestion' : 'completeAssessment' }}"
                            class="ml-3 inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                        >
                            {{ $currentQuestionIndex < count($questions) - 1 ? 'Next Question' : 'Complete Assessment' }}
                        </button>
                    </div>

                </div>
            </div>--}}
    @endif

    <!-- Assessment view continues here... -->

    @if($step === 'results')
        @include('livewire.students.partials.assessment-results', ['result' => $this->result])
    @endif

    <div class="mt-4">
        <div class="py-4"><h4>Recent Assessments</h4></div>
        @if(count($this->recentAssessments) > 0)
            <div class="overflow-x-auto">
                <div class="min-w-full inline-block align-middle">
                    <div class="overflow-hidden border-b border-gray-200 dark:border-gray-700 shadow-sm sm:rounded-lg">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-100 dark:bg-gray-700">
                            <tr>
                                <!-- Subject - Always visible -->
                                <th scope="col"
                                    class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Subject
                                </th>
                                <!-- Topic - Hidden on mobile -->
                                <th scope="col"
                                    class="hidden md:table-cell px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Topic
                                </th>
                                <!-- Date - Hidden on mobile -->
                                <th scope="col"
                                    class="hidden md:table-cell px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Date
                                </th>
                                <!-- Score - Always visible -->
                                <th scope="col"
                                    class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Score
                                </th>
                                <!-- Status - Always visible -->
                                <th scope="col"
                                    class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Status
                                </th>
                            </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach($this->recentAssessments as $assessment)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50">
                                    <!-- Subject + Mobile Date -->
                                    <td class="px-4 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900 dark:text-gray-100">
                                            {{ $assessment->subject->name }}
                                        </div>
                                        <div class="md:hidden text-xs text-gray-500 dark:text-gray-400 mt-1">
                                            {{ $assessment->created_at->format('M d, Y') }}
                                        </div>
                                    </td>
                                    <!-- Topic -->
                                    <td class="hidden md:table-cell px-4 py-4 whitespace-nowrap text-sm text-gray-900 dark:text-gray-100">
                                        {{ $assessment->topic ? $assessment->topic->name : 'All Topics' }}
                                    </td>
                                    <!-- Date -->
                                    <td class="hidden md:table-cell px-4 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                        {{ $assessment->created_at->format('M d, Y') }}
                                    </td>
                                    <!-- Score -->
                                    <td class="px-4 py-4 whitespace-nowrap text-sm">
                                        @if($assessment->status === 'completed')
                                            <span
                                                class="font-semibold {{ $assessment->percentage_score >= 70 ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                            {{ round($assessment->percentage_score, 1) }}%
                          </span>
                                        @elseif($assessment->status === 'needs_grading')
                                            <span class="text-yellow-600 dark:text-yellow-400">Pending</span>
                                        @else
                                            <span class="text-gray-400">-</span>
                                        @endif
                                    </td>
                                    <!-- Status -->
                                    <td class="px-4 py-4 whitespace-nowrap">
                        <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full
                          @if($assessment->status === 'completed') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                          @elseif($assessment->status === 'in_progress') bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200
                          @elseif($assessment->status === 'needs_grading') bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200
                          @endif">
                          {{ ucfirst(str_replace('_', ' ', $assessment->status)) }}
                        </span>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="mt-4">
                {{$this->recentAssessments->links()}}
            </div>
        @else
            <div class="text-center py-8">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">No assessments completed</h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Take a self-assessment to track your
                    progress.</p>
                <div class="mt-6">
                    <button wire:click="$dispatch('studentTabChanged', {tab: 'self-assessment'})"
                            class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        <svg class="-ml-1 mr-2 h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20"
                             fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd"
                                  d="M10 3a1 1 0 011 1v5h5a1 1 0 110 2h-5v5a1 1 0 11-2 0v-5H4a1 1 0 110-2h5V4a1 1 0 011-1z"
                                  clip-rule="evenodd"/>
                        </svg>
                        Start Assessment
                    </button>
                </div>
            </div>
        @endif
    </div>
</div>



