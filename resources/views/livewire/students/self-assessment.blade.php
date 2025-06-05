<div>
    @if($step === 'setup')
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <h2 class="text-xl font-bold mb-6">Create Self-Assessment</h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Subject Selection -->
                <div>
                    <label class="block text-sm font-medium mb-2" for="subject">Subject</label>
                    <select id="subject" wire:model="selectedSubject"
                            class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 dark:focus:ring-indigo-600 focus:ring-opacity-50">
                        <option value="">Select a subject</option>
                        @foreach($subjects as $subject)
                            <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Topic Selection (if subject selected) -->
                @if($selectedSubject)
                    <div>
                        <label class="block text-sm font-medium mb-2" for="topic">Topic (Optional)</label>
                        <select id="topic" wire:model="selectedTopic"
                                class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 dark:focus:ring-indigo-600 focus:ring-opacity-50">
                            <option value="">All Topics</option>
                            @foreach($topics as $topic)
                                <option value="{{ $topic->id }}">{{ $topic->name }}</option>
                            @endforeach
                        </select>
                    </div>
                @endif

                <!-- Subtopic Selection (if topic selected) -->
                @if($selectedTopic)
                    <div>
                        <label class="block text-sm font-medium mb-2" for="subtopic">Subtopic (Optional)</label>
                        <select id="subtopic" wire:model="selectedSubtopic"
                                class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 dark:focus:ring-indigo-600 focus:ring-opacity-50">
                            <option value="">All Subtopics</option>
                            @foreach($subtopics as $subtopic)
                                <option value="{{ $subtopic->id }}">{{ $subtopic->name }}</option>
                            @endforeach
                        </select>
                    </div>
                @endif

                <!-- Question Count -->
                <div>
                    <label class="block text-sm font-medium mb-2" for="count">Number of Questions</label>
                    <input type="number" id="count" wire:model="questionCount" min="1" max="50"
                           class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 dark:focus:ring-indigo-600 focus:ring-opacity-50">
                </div>

                <!-- Difficulty Level -->
                <div>
                    <label class="block text-sm font-medium mb-2" for="difficulty">Difficulty</label>
                    <select id="difficulty" wire:model="difficulty"
                            class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 dark:focus:ring-indigo-600 focus:ring-opacity-50">
                        <option value="all">All Levels</option>
                        <option value="easy">Easy</option>
                        <option value="medium">Medium</option>
                        <option value="hard">Hard</option>
                    </select>
                </div>
            </div>

            <!-- Question Types -->
            <div class="mt-6">
                <label class="block text-sm font-medium mb-2">Question Types</label>
                <div class="flex flex-wrap gap-4">
                    <label class="inline-flex items-center">
                        <input type="checkbox" wire:model="questionTypes.multiple_choice_question"
                               class="rounded border-gray-300 dark:border-gray-600 text-indigo-600 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 dark:focus:ring-indigo-600 focus:ring-opacity-50">
                        <span class="ml-2">Multiple Choice</span>
                    </label>
                    <label class="inline-flex items-center">
                        <input type="checkbox" wire:model="questionTypes.true_or_false_question"
                               class="rounded border-gray-300 dark:border-gray-600 text-indigo-600 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 dark:focus:ring-indigo-600 focus:ring-opacity-50">
                        <span class="ml-2">True/False</span>
                    </label>
                    <label class="inline-flex items-center">
                        <input type="checkbox" wire:model="questionTypes.essay_question"
                               class="rounded border-gray-300 dark:border-gray-600 text-indigo-600 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 dark:focus:ring-indigo-600 focus:ring-opacity-50">
                        <span class="ml-2">Essay</span>
                    </label>
                </div>
                @error('questionTypes')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            @if(session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mt-6">
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            <!-- Start Button -->
            <div class="mt-8 flex justify-end">
                <button
                    wire:click="startAssessment"
                    class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                >
                    Start Assessment
                </button>
            </div>
        </div>
    @elseif($step === 'assessment')
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 flex justify-between items-center">
                <h3 class="text-lg font-semibold">Self Assessment</h3>
                <div class="text-sm font-medium bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-200 px-3 py-1 rounded-full"
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
                     x-on:mouseleave="if(time > 0) clearInterval(intervalId)"
                     x-text="Math.floor(time / 60) + ':' + ((time % 60).toString().padStart(2, '0'))"
                     >
                </div>

            </div>

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
                @if($questions[$currentQuestionIndex]->difficulty_level === 'easy') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                @elseif($questions[$currentQuestionIndex]->difficulty_level === 'medium') bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200
                @elseif($questions[$currentQuestionIndex]->difficulty_level === 'hard') bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200
                @endif">
                {{ ucfirst($questions[$currentQuestionIndex]->difficulty_level) }}
            </span>
                        </div>

                        <!-- Question Text -->
                        <div class="mb-6">
                            <h4 class="text-lg font-medium mb-4">
                                {!! $questions[$currentQuestionIndex]->questionable->question->down !!}
                            </h4>

                            @php
                                $type = class_basename($questions[$currentQuestionIndex]->questionable_type);
                            @endphp

                                <!-- Multiple Choice -->
                            @if ($type === 'MultipleChoiceQuestion')
                                @php
                                    $options = [];
                                    foreach(['a', 'b', 'c', 'd', 'e'] as $letter) {
                                        if (!is_null($questions[$currentQuestionIndex]->questionable->{'option_'.$letter}->down)) {
                                            $options[] = ['label' => strtoupper($letter), 'value' => $questions[$currentQuestionIndex]->questionable->{'option_'.$letter}];
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
                                               @if ($responses[$currentQuestionIndex]['response'] === $option['label']) checked @endif
                                               class="w-4 h-4 text-indigo-600 border-gray-300 focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:bg-gray-700 dark:border-gray-600"
                                        >
                                        <label for="option-{{ $loop->index }}-{{ $currentQuestionIndex }}" class="ml-2 block text-sm font-medium text-gray-900 dark:text-gray-300">
                                            {{ $option['label'] }}. {{ $option['value']->down }}
                                        </label>
                                    </div>
                                @endforeach


                                <!-- True/False -->
                            @elseif ($type === 'TrueOrFalseQuestion')
                                <div class="space-y-3">
                                    <div class="flex items-center">
                                        <input type="radio"
                                               id="true"
                                               name="response_{{ $currentQuestionIndex }}"
                                               value="true"
                                               wire:click="saveResponse({{ $currentQuestionIndex }}, 'true')"
                                               @if ($responses[$currentQuestionIndex]['response'] === 'true') checked @endif
                                               class="w-4 h-4 text-indigo-600 border-gray-300 focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:bg-gray-700 dark:border-gray-600"
                                        >
                                        <label for="true" class="ml-2 block text-sm font-medium text-gray-900 dark:text-gray-300">True</label>
                                    </div>

                                    <div class="flex items-center">
                                        <input type="radio"
                                               id="false"
                                               name="response"
                                               value="false"
                                               wire:click="saveResponse({{ $currentQuestionIndex }}, 'false')"
                                               @if ($responses[$currentQuestionIndex]['response'] === 'false') checked @endif
                                               class="w-4 h-4 text-indigo-600 border-gray-300 focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:bg-gray-700 dark:border-gray-600"
                                        >
                                        <label for="false" class="ml-2 block text-sm font-medium text-gray-900 dark:text-gray-300">False</label>
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
        </div>
    @elseif($step === 'results')
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h3 class="text-lg font-semibold">Assessment Results</h3>
            </div>
            <div class="p-6">
                <!-- Overall Score -->
                <div class="text-center mb-8">
                    <div class="inline-block p-4 rounded-full
                        @if($result['percentage_score'] >= 80) bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200
                        @elseif($result['percentage_score'] >= 60) bg-yellow-100 dark:bg-yellow-900 text-yellow-800 dark:text-yellow-200
                        @else bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-200 @endif">
                        <svg class="w-16 h-16" fill="currentColor" viewBox="0 0 20 20"
                             xmlns="http://www.w3.org/2000/svg">
                            <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"/>
                            <path fill-rule="evenodd"
                                  d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z"
                                  clip-rule="evenodd"/>
                        </svg>
                    </div>

                    <h4 class="text-2xl font-bold mt-4">{{ $result['percentage_score'] }}%</h4>
                    <p class="text-gray-500 dark:text-gray-400">{{ $result['total_score'] }} out
                        of {{ $result['max_score'] }} points</p>

                    @if($result['needs_grading'])
                        <div class="mt-4 bg-yellow-50 dark:bg-yellow-900/30 p-4 rounded-md">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-yellow-400" xmlns="http://www.w3.org/2000/svg"
                                         viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd"
                                              d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2h-1V9z"
                                              clip-rule="evenodd"/>
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm text-yellow-700 dark:text-yellow-200">
                                        Some questions (essays) need to be graded. Your final score will be updated when
                                        grading is complete.
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Score breakdown by question type -->
                <div class="mb-8">
                    <h4 class="text-lg font-semibold mb-4">Performance by Question Type</h4>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        @if($result['byType']['multiple_choice'])
                            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                                <h5 class="font-medium mb-2">Multiple Choice</h5>
                                <div class="text-2xl font-bold">{{ $result['byType']['multiple_choice']['percentage'] }}
                                    %
                                </div>
                                <p class="text-sm text-gray-500 dark:text-gray-400">{{ $result['byType']['multiple_choice']['score'] }}
                                    / {{ $result['byType']['multiple_choice']['max_score'] }}</p>
                            </div>
                        @endif

                        @if($result['byType']['true_false'])
                            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                                <h5 class="font-medium mb-2">True/False</h5>
                                <div class="text-2xl font-bold">{{ $result['byType']['true_false']['percentage'] }}%
                                </div>
                                <p class="text-sm text-gray-500 dark:text-gray-400">{{ $result['byType']['true_false']['score'] }}
                                    / {{ $result['byType']['true_false']['max_score'] }}</p>
                            </div>
                        @endif

                        @if($result['byType']['essay'])
                            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                                <h5 class="font-medium mb-2">Essay Questions</h5>
                                @if($result['needs_grading'])
                                    <div class="text-2xl font-bold text-yellow-600 dark:text-yellow-400">Pending</div>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Awaiting grading</p>
                                @else
                                    <div class="text-2xl font-bold">{{ $result['byType']['essay']['percentage'] }}%
                                    </div>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ $result['byType']['essay']['score'] }}
                                        / {{ $result['byType']['essay']['max_score'] }}</p>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Time Spent -->
                <div class="mb-8">
                    <h4 class="text-lg font-semibold mb-4">Time Spent</h4>
                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                        <div class="text-2xl font-bold">{{ $result['time_spent'] }} minutes</div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex justify-end mt-8">
                    <button
                        wire:click="startNewAssessment"
                        class="inline-flex justify-center py-2 px-4 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500"
                    >
                        Start New Assessment
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
