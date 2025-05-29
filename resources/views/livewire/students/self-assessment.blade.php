<div>
    @if($step === 'setup')
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <h2 class="text-xl font-bold mb-6">Create Self-Assessment</h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Subject Selection -->
                <div>
                    <label class="block text-sm font-medium mb-2" for="subject">Subject</label>
                    <select id="subject" wire:model="selectedSubject" class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 dark:focus:ring-indigo-600 focus:ring-opacity-50">
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
                        <select id="topic" wire:model="selectedTopic" class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 dark:focus:ring-indigo-600 focus:ring-opacity-50">
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
                        <select id="subtopic" wire:model="selectedSubtopic" class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 dark:focus:ring-indigo-600 focus:ring-opacity-50">
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
                    <input type="number" id="count" wire:model="questionCount" min="1" max="50" class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 dark:focus:ring-indigo-600 focus:ring-opacity-50">
                </div>

                <!-- Difficulty Level -->
                <div>
                    <label class="block text-sm font-medium mb-2" for="difficulty">Difficulty</label>
                    <select id="difficulty" wire:model="difficulty" class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 dark:focus:ring-indigo-600 focus:ring-opacity-50">
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
                        <input type="checkbox" wire:model="questionTypes.multiple_choice" class="rounded border-gray-300 dark:border-gray-600 text-indigo-600 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 dark:focus:ring-indigo-600 focus:ring-opacity-50">
                        <span class="ml-2">Multiple Choice</span>
                    </label>
                    <label class="inline-flex items-center">
                        <input type="checkbox" wire:model="questionTypes.true_false" class="rounded border-gray-300 dark:border-gray-600 text-indigo-600 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 dark:focus:ring-indigo-600 focus:ring-opacity-50">
                        <span class="ml-2">True/False</span>
                    </label>
                    <label class="inline-flex items-center">
                        <input type="checkbox" wire:model="questionTypes.essay" class="rounded border-gray-300 dark:border-gray-600 text-indigo-600 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 dark:focus:ring-indigo-600 focus:ring-opacity-50">
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
                <div class="text-sm font-medium bg-indigo-100 text-indigo-800 dark:bg-indigo-900 dark:text-indigo-200 px-3 py-1 rounded-full" x-data x-init="() => {
                    let interval;
                    const formatTime = (seconds) => {
                        const minutes = Math.floor(seconds / 60);
                        const remainingSeconds = seconds % 60;
                        return `${minutes}:${remainingSeconds < 10 ? '0' : ''}${remainingSeconds}`;
                    };

                    window.addEventListener('start-timer', (event) => {
                        let seconds = event.detail.seconds;
                        $el.textContent = formatTime(seconds);

                        clearInterval(interval);
                        interval = setInterval(() => {
                            seconds--;
                            $el.textContent = formatTime(seconds);

                            if (seconds <= 0) {
                                clearInterval(interval);
                                @this.completeAssessment();
                            }
                        }, 1000);
                    });
                }">
                    {{ gmdate('i:s', $timeRemaining) }}
                </div>
            </div>

            <div class="p-4">
                <!-- Question Navigation -->
                <div class="flex flex-wrap gap-2 mb-6">
                    @foreach($questions as $index => $question)
                        <button
                            wire:click="jumpToQuestion({{ $index }})"
                            class="w-8 h-8 flex items-center justify-center rounded-full text-sm font-medium
                                {{ $currentQuestionIndex === $index ? 'bg-indigo-600 text-white' : '' }}
                                {{ $responses[$index]['is_answered'] ? 'bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200' : 'bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200' }}"
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

                        <div class="mb-6">
                            <h4 class="text-lg font-medium mb-4">{{ $questions[$currentQuestionIndex]->question_text }}</h4>

                            <!-- Multiple Choice Question -->
                            @if($questions[$currentQuestionIndex]->question_type === 'multiple_choice')
                                <div class="space-y-3">
                                    @foreach($questions[$currentQuestionIndex]->options as $optionKey => $optionText)
                                        <label class="flex items-center p-3 border border-gray-200 dark:border-gray-700 rounded-lg
                                            {{ $responses[$currentQuestionIndex]['response'] === $optionKey ? 'bg-indigo-50 border-indigo-500 dark:bg-indigo-900 dark:border-indigo-500' : 'hover:bg-gray-50 dark:hover:bg-gray-700' }}">
                                            <input
                                                type="radio"
                                                name="question_{{ $questions[$currentQuestionIndex]->id }}"
                                                value="{{ $optionKey }}"
                                                wire:model="responses.{{ $currentQuestionIndex }}.response"
                                                class="h-4 w-4 text-indigo-600 border-gray-300 focus:ring-indigo-500 dark:focus:ring-indigo-600"
                                            >
                                            <span class="ml-3 block">{{ $optionText }}</span>
                                        </label>
                                    @endforeach
                                </div>

                            <!-- True/False Question -->
                            @elseif($questions[$currentQuestionIndex]->question_type === 'true_false')
                                <div class="space-y-3">
                                    <label class="flex items-center p-3 border border-gray-200 dark:border-gray-700 rounded-lg
                                        {{ $responses[$currentQuestionIndex]['response'] === 'true' ? 'bg-indigo-50 border-indigo-500 dark:bg-indigo-900 dark:border-indigo-500' : 'hover:bg-gray-50 dark:hover:bg-gray-700' }}">
                                        <input
                                            type="radio"
                                            name="question_{{ $questions[$currentQuestionIndex]->id }}"
                                            value="true"
                                            wire:model="responses.{{ $currentQuestionIndex }}.response"
                                            class="h-4 w-4 text-indigo-600 border-gray-300 focus:ring-indigo-500 dark:focus:ring-indigo-600"
                                        >
                                        <span class="ml-3 block">True</span>
                                    </label>
                                    <label class="flex items-center p-3 border border-gray-200 dark:border-gray-700 rounded-lg
                                        {{ $responses[$currentQuestionIndex]['response'] === 'false' ? 'bg-indigo-50 border-indigo-500 dark:bg-indigo-900 dark:border-indigo-500' : 'hover:bg-gray-50 dark:hover:bg-gray-700' }}">
                                        <input
                                            type="radio"
                                            name="question_{{ $questions[$currentQuestionIndex]->id }}"
                                            value="false"
                                            wire:model="responses.{{ $currentQuestionIndex }}.response"
                                            class="h-4 w-4 text-indigo-600 border-gray-300 focus:ring-indigo-500 dark:focus:ring-indigo-600"
                                        >
                                        <span class="ml-3 block">False</span>
                                    </label>
                                </div>

                            <!-- Essay Question -->
                            @elseif($questions[$currentQuestionIndex]->question_type === 'essay')
                                <div>
                                    <textarea
                                        wire:model="responses.{{ $currentQuestionIndex }}.response"
                                        rows="6"
                                        class="w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 dark:focus:ring-indigo-600 focus:ring-opacity-50"
                                        placeholder="Enter your answer here..."
                                    ></textarea>
                                </div>
                            @endif
                        </div>

                        <div class="flex justify-between mt-8">
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
                @endif
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
                        @if($result['percentageScore'] >= 80) bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200
                        @elseif($result['percentageScore'] >= 60) bg-yellow-100 dark:bg-yellow-900 text-yellow-800 dark:text-yellow-200
                        @else bg-red-100 dark:bg-red-900 text-red-800 dark:text-red-200 @endif">
                        <svg class="w-16 h-16" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                            <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"/>
                            <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"/>
                        </svg>
                    </div>

                    <h4 class="text-2xl font-bold mt-4">{{ $result['percentageScore'] }}%</h4>
                    <p class="text-gray-500 dark:text-gray-400">{{ $result['totalScore'] }} out of {{ $result['maxScore'] }} points</p>

                    @if($result['needsGrading'])
                        <div class="mt-4 bg-yellow-50 dark:bg-yellow-900/30 p-4 rounded-md">
                            <div class="flex">
                                <div class="flex-shrink-0">
                                    <svg class="h-5 w-5 text-yellow-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2h-1V9z" clip-rule="evenodd" />
                                    </svg>
                                </div>
                                <div class="ml-3">
                                    <p class="text-sm text-yellow-700 dark:text-yellow-200">
                                        Some questions (essays) need to be graded. Your final score will be updated when grading is complete.
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
                                <div class="text-2xl font-bold">{{ $result['byType']['multiple_choice']['percentage'] }}%</div>
                                <p class="text-sm text-gray-500 dark:text-gray-400">{{ $result['byType']['multiple_choice']['score'] }} / {{ $result['byType']['multiple_choice']['maxScore'] }}</p>
                            </div>
                        @endif

                        @if($result['byType']['true_false'])
                            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                                <h5 class="font-medium mb-2">True/False</h5>
                                <div class="text-2xl font-bold">{{ $result['byType']['true_false']['percentage'] }}%</div>
                                <p class="text-sm text-gray-500 dark:text-gray-400">{{ $result['byType']['true_false']['score'] }} / {{ $result['byType']['true_false']['maxScore'] }}</p>
                            </div>
                        @endif

                        @if($result['byType']['essay'])
                            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                                <h5 class="font-medium mb-2">Essay Questions</h5>
                                @if($result['needsGrading'])
                                    <div class="text-2xl font-bold text-yellow-600 dark:text-yellow-400">Pending</div>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Awaiting grading</p>
                                @else
                                    <div class="text-2xl font-bold">{{ $result['byType']['essay']['percentage'] }}%</div>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">{{ $result['byType']['essay']['score'] }} / {{ $result['byType']['essay']['maxScore'] }}</p>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Time Spent -->
                <div class="mb-8">
                    <h4 class="text-lg font-semibold mb-4">Time Spent</h4>
                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                        <div class="text-2xl font-bold">{{ $result['timeSpent'] }} minutes</div>
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
