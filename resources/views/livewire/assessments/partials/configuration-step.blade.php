
<div class="max-w-4xl mx-auto">
    <!-- Header -->
    <div class="bg-gradient-to-r from-purple-600 to-indigo-600 rounded-xl p-8 text-white mb-8 shadow-xl">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-4xl font-bold mb-2">Configure Assessment</h1>
                <p class="text-purple-100 text-lg">Customize your assessment settings</p>
            </div>
            <div class="hidden md:block">
                <div class="w-20 h-20 bg-white/20 rounded-full flex items-center justify-center">
                    <svg class="w-10 h-10" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    @if($assessmentMode === 'self')
        <div class="space-y-8">
            <!-- Subject Info -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-8">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">Selected Subject</h2>
                <div class="flex items-center space-x-4">
                    <div class="w-12 h-12 bg-indigo-100 dark:bg-indigo-800 rounded-lg flex items-center justify-center">
                        <svg class="w-6 h-6 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ $subjects->firstWhere('id', $selectedSubject)->name ?? 'Unknown Subject' }}</h3>
                        <p class="text-gray-600 dark:text-gray-400">{{ $subjects->firstWhere('id', $selectedSubject)->academicLevel->name ?? '' }}</p>
                    </div>
                </div>
            </div>

            <!-- Topic Selection -->
            @if($topics->isNotEmpty())
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-8">
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">Select Topic (Optional)</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <input type="radio" id="all-topics" wire:model.live="selectedTopic" value="" class="sr-only">
                            <label for="all-topics" class="block cursor-pointer">
                                <div class="p-4 rounded-lg border-2 transition-all duration-200 hover:shadow-md
                                    {{ !$selectedTopic ? 'border-indigo-500 bg-indigo-50 dark:bg-indigo-900/20' : 'border-gray-200 dark:border-gray-600 hover:border-indigo-300' }}">
                                    <h3 class="font-semibold text-gray-900 dark:text-white">All Topics</h3>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">Include questions from all topics</p>
                                </div>
                            </label>
                        </div>
                        @foreach($topics as $topic)
                            <div>
                                <input type="radio" id="topic-{{ $topic->id }}" wire:model.live="selectedTopic" value="{{ $topic->id }}" class="sr-only">
                                <label for="topic-{{ $topic->id }}" class="block cursor-pointer">
                                    <div class="p-4 rounded-lg border-2 transition-all duration-200 hover:shadow-md
                                        {{ $selectedTopic == $topic->id ? 'border-indigo-500 bg-indigo-50 dark:bg-indigo-900/20' : 'border-gray-200 dark:border-gray-600 hover:border-indigo-300' }}">
                                        <h3 class="font-semibold text-gray-900 dark:text-white">{{ $topic->name }}</h3>
                                    </div>
                                </label>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Subtopic Selection -->
            @if(count($subtopics))
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-8">
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">Select Subtopic (Optional)</h2>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <input type="radio" id="all-subtopics" wire:model.live="selectedSubtopic" value="" class="sr-only">
                            <label for="all-subtopics" class="block cursor-pointer">
                                <div class="p-4 rounded-lg border-2 transition-all duration-200 hover:shadow-md
                                    {{ !$selectedSubtopic ? 'border-indigo-500 bg-indigo-50 dark:bg-indigo-900/20' : 'border-gray-200 dark:border-gray-600 hover:border-indigo-300' }}">
                                    <h3 class="font-semibold text-gray-900 dark:text-white">All Subtopics</h3>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">Include questions from all subtopics</p>
                                </div>
                            </label>
                        </div>
                        @foreach($subtopics as $subtopic)
                            <div>
                                <input type="radio" id="subtopic-{{ $subtopic->id }}" wire:model.live="selectedSubtopic" value="{{ $subtopic->id }}" class="sr-only">
                                <label for="subtopic-{{ $subtopic->id }}" class="block cursor-pointer">
                                    <div class="p-4 rounded-lg border-2 transition-all duration-200 hover:shadow-md
                                        {{ $selectedSubtopic == $subtopic->id ? 'border-indigo-500 bg-indigo-50 dark:bg-indigo-900/20' : 'border-gray-200 dark:border-gray-600 hover:border-indigo-300' }}">
                                        <h3 class="font-semibold text-gray-900 dark:text-white">{{ $subtopic->name }}</h3>
                                    </div>
                                </label>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            <!-- Assessment Settings -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-8">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">Assessment Settings</h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <!-- Question Types -->
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Question Types</h3>
                        <div class="space-y-3">
                            <label class="flex items-center">
                                <input type="checkbox" wire:model.live="questionTypes.multiple_choice_question" class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                                <span class="ml-3 text-sm text-gray-700 dark:text-gray-300">Multiple Choice</span>
                                @if(isset($questionCounts['multiple_choice_question']))
                                    <span class="ml-2 text-xs text-gray-500 dark:text-gray-400">({{ $questionCounts['multiple_choice_question'] }} available)</span>
                                @endif
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" wire:model.live="questionTypes.true_or_false_question" class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                                <span class="ml-3 text-sm text-gray-700 dark:text-gray-300">True/False</span>
                                @if(isset($questionCounts['true_or_false_question']))
                                    <span class="ml-2 text-xs text-gray-500 dark:text-gray-400">({{ $questionCounts['true_or_false_question'] }} available)</span>
                                @endif
                            </label>
                            <label class="flex items-center">
                                <input type="checkbox" wire:model.live="questionTypes.essay_question" class="w-4 h-4 text-indigo-600 border-gray-300 rounded focus:ring-indigo-500">
                                <span class="ml-3 text-sm text-gray-700 dark:text-gray-300">Essay</span>
                                @if(isset($questionCounts['essay_question']))
                                    <span class="ml-2 text-xs text-gray-500 dark:text-gray-400">({{ $questionCounts['essay_question'] }} available)</span>
                                @endif
                            </label>
                        </div>
                    </div>

                    <!-- Assessment Options -->
                    <div class="space-y-6">
                        <!-- Question Count -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Number of Questions
                            </label>
                            <div class="flex items-center space-x-4">
                                <input type="number" wire:model.live="questionCount" min="1" max="50"
                                       class="w-24 px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:bg-gray-700 dark:text-white">
                                @if(isset($questionCounts['total']))
                                    <span class="text-sm text-gray-500 dark:text-gray-400">
                                        ({{ $questionCounts['total'] }} questions available)
                                    </span>
                                @endif
                            </div>
                        </div>

                        <!-- Difficulty -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Difficulty Level
                            </label>
                            <select wire:model.live="difficulty"
                                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:bg-gray-700 dark:text-white">
                                <option value="all">All Levels</option>
                                <option value="easy">Easy</option>
                                <option value="medium">Medium</option>
                                <option value="hard">Hard</option>
                            </select>
                        </div>

                        <!-- Time Limit -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Time Limit (minutes)
                            </label>
                            <input type="number" wire:model.live="timeLimitMinutes" min="1" max="180" placeholder="Optional"
                                   class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:bg-gray-700 dark:text-white">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @else
        <!-- Assignment Configuration -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-8">
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">Assignment Details</h2>
            @php
                $assignment = $availableAssignments->firstWhere('id', $selectedAssignment);
            @endphp

            @if($assignment)
                <div class="space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">{{ $assignment->title }}</h3>
                            <p class="text-gray-600 dark:text-gray-400">{{ $assignment->academicSubject->name }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm text-gray-500 dark:text-gray-400">Instructor</p>
                            <p class="font-semibold text-gray-900 dark:text-white">{{ $assignment->user->name }}</p>
                        </div>
                    </div>

                    @if($assignment->description)
                        <div>
                            <h4 class="font-semibold text-gray-900 dark:text-white mb-2">Description</h4>
                            <p class="text-gray-600 dark:text-gray-400">{{ $assignment->description }}</p>
                        </div>
                    @endif

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        @if($assignment->duration_in_minutes)
                            <div class="bg-yellow-50 dark:bg-yellow-900/20 p-4 rounded-lg">
                                <div class="flex items-center">
                                    <svg class="w-5 h-5 text-yellow-600 dark:text-yellow-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    <span class="text-sm font-medium text-yellow-800 dark:text-yellow-200">Time Limit</span>
                                </div>
                                <p class="text-lg font-bold text-yellow-900 dark:text-yellow-100">{{ $assignment->duration_in_minutes }} minutes</p>
                            </div>
                        @endif

                        <div class="bg-blue-50 dark:bg-blue-900/20 p-4 rounded-lg">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 text-blue-600 dark:text-blue-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                <span class="text-sm font-medium text-blue-800 dark:text-blue-200">Due Date</span>
                            </div>
                            <p class="text-lg font-bold text-blue-900 dark:text-blue-100">{{ $assignment->ends_at->format('M d, Y') }}</p>
                        </div>

                        <div class="bg-green-50 dark:bg-green-900/20 p-4 rounded-lg">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 text-green-600 dark:text-green-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <span class="text-sm font-medium text-green-800 dark:text-green-200">Status</span>
                            </div>
                            <p class="text-lg font-bold text-green-900 dark:text-green-100">Ready</p>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    @endif

    <!-- Navigation -->
    <div class="mt-8 flex justify-between">
        <button wire:click="backToSelection"
                class="px-6 py-3 bg-gray-500 hover:bg-gray-600 text-white font-semibold rounded-lg transition-colors duration-200">
            <svg class="w-5 h-5 mr-2 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 17l-5-5m0 0l5-5m-5 5h12"/>
            </svg>
            Back to Selection
        </button>

        <button wire:click="startAssessment"
                class="px-8 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white font-semibold rounded-lg transition-colors duration-200">
            Start Assessment
            <svg class="w-5 h-5 ml-2 inline-block" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h1.5a1.5 1.5 0 001.5-1.5v-1a1.5 1.5 0 00-1.5-1.5H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </button>
    </div>
</div>
