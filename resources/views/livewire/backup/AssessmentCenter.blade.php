<div>
    @if($step === 'setup')
        <div class="max-w-5xl mx-auto space-y-8">
            <!-- Enhanced Header Section -->
            <div
                class="bg-gradient-to-r from-{{ $assessmentMode === 'assignment' ? 'green-500 via-emerald-600 to-teal-600' : 'indigo-500 via-purple-600 to-pink-600' }} rounded-xl p-8 text-white shadow-lg relative overflow-hidden">
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
                            <h1 class="text-4xl font-bold">Assessment Center</h1>
                            <p class="text-{{ $assessmentMode === 'assignment' ? 'green' : 'indigo' }}-100 mt-2 text-lg">
                                {{ $assessmentMode === 'assignment' ? 'Practice with assignments' : 'Create your personalized assessment' }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Mode Selection -->
            <div
                class="bg-white dark:bg-gray-800 rounded-xl shadow-xl border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="p-6">
                    <div class="flex items-center justify-center space-x-4 mb-6">
                        <button wire:click="switchAssessmentMode('self')"
                                class="px-6 py-3 rounded-lg font-medium transition-all
                                       {{ $assessmentMode === 'self' ? 'bg-indigo-600 text-white shadow-lg' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                            Self Assessment
                        </button>
                        <button wire:click="switchAssessmentMode('assignment')"
                                class="px-6 py-3 rounded-lg font-medium transition-all
                                       {{ $assessmentMode === 'assignment' ? 'bg-emerald-600 text-white shadow-lg' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                            Assignment Practice
                        </button>
                    </div>

                    @if($assessmentMode === 'self')
                        <!-- Self Assessment Setup -->
                        <div class="space-y-8">
                            <!-- Subject Selection -->
                            <div class="space-y-6">
                                <div class="flex items-center space-x-3">
                                    <div class="p-3 bg-indigo-100 dark:bg-indigo-900/30 rounded-xl">
                                        <svg class="w-6 h-6 text-indigo-600 dark:text-indigo-400" fill="none"
                                             stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Subject &
                                            Topic</h2>
                                        <p class="text-gray-600 dark:text-gray-400">Choose your subject and specific
                                            topics</p>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                    <!-- Subject Selection -->
                                    <div class="space-y-2">
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Subject</label>
                                        <select wire:model.live="selectedSubject"
                                                class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:bg-gray-700 dark:text-white">
                                            <option value="">Select Subject</option>
                                            @foreach($subjects as $subject)
                                                <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <!-- Topic Selection -->
                                    <div class="space-y-2">
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Topic
                                            (Optional)</label>
                                        <select wire:model.live="selectedTopic"
                                                class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:bg-gray-700 dark:text-white" @disabled(!$selectedSubject)>
                                            <option value="">All Topics</option>
                                            @foreach($topics as $topic)
                                                <option value="{{ $topic->id }}">{{ $topic->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <!-- Subtopic Selection -->
                                    <div class="space-y-2">
                                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Subtopic
                                            (Optional)</label>
                                        <select wire:model.live="selectedSubtopic"
                                                class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:bg-gray-700 dark:text-white" @disabled(!$selectedTopic)>
                                            <option value="">All Subtopics</option>
                                            @foreach($subtopics as $subtopic)
                                                <option value="{{ $subtopic->id }}">{{ $subtopic->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <!-- Question Types -->
                            <div class="space-y-6">
                                <div class="flex items-center space-x-3">
                                    <div class="p-3 bg-purple-100 dark:bg-purple-900/30 rounded-xl">
                                        <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none"
                                             stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Question
                                            Types</h2>
                                        <p class="text-gray-600 dark:text-gray-400">Select the types of questions you
                                            want to practice</p>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <label
                                        class="flex items-center space-x-3 p-4 border border-gray-200 dark:border-gray-700 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer">
                                        <input type="checkbox" wire:model.live="questionTypes.multiple_choice_question"
                                               class="w-4 h-4 text-indigo-600 bg-gray-100 border-gray-300 rounded focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                                        <span class="text-sm font-medium text-gray-900 dark:text-gray-100">Multiple Choice</span>
                                    </label>

                                    <label
                                        class="flex items-center space-x-3 p-4 border border-gray-200 dark:border-gray-700 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer">
                                        <input type="checkbox" wire:model.live="questionTypes.true_or_false_question"
                                               class="w-4 h-4 text-indigo-600 bg-gray-100 border-gray-300 rounded focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                                        <span
                                            class="text-sm font-medium text-gray-900 dark:text-gray-100">True/False</span>
                                    </label>

                                    <label
                                        class="flex items-center space-x-3 p-4 border border-gray-200 dark:border-gray-700 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer">
                                        <input type="checkbox" wire:model.live="questionTypes.essay_question"
                                               class="w-4 h-4 text-indigo-600 bg-gray-100 border-gray-300 rounded focus:ring-indigo-500 dark:focus:ring-indigo-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                                        <span class="text-sm font-medium text-gray-900 dark:text-gray-100">Essay</span>
                                    </label>
                                </div>
                            </div>

                            <!-- Assessment Configuration -->
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                <!-- Number of Questions -->
                                <div class="space-y-2">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Number of
                                        Questions</label>
                                    <input type="number" wire:model.live="questionCount" min="1" max="50"
                                           class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:bg-gray-700 dark:text-white">
                                </div>

                                <!-- Difficulty Level -->
                                <div class="space-y-2">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Difficulty
                                        Level</label>
                                    <select wire:model.live="difficulty"
                                            class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:bg-gray-700 dark:text-white">
                                        <option value="all">All Levels</option>
                                        <option value="easy">Easy</option>
                                        <option value="medium">Medium</option>
                                        <option value="hard">Hard</option>
                                    </select>
                                </div>

                                <!-- Time Limit -->
                                <div class="space-y-2">
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Time Limit
                                        (minutes)</label>
                                    <input type="number" wire:model.live="timeLimitMinutes" min="1" max="300"
                                           placeholder="No limit"
                                           class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:bg-gray-700 dark:text-white">
                                </div>
                            </div>

                            <!-- Start Assessment Button -->
                            <div class="pt-6 border-t border-gray-200 dark:border-gray-700">
                                <button wire:click="startAssessment"
                                        class="w-full bg-gradient-to-r from-indigo-500 to-purple-600 text-white py-4 px-6 rounded-lg font-semibold text-lg hover:from-indigo-600 hover:to-purple-700 transition-all transform hover:scale-105 shadow-lg">
                                    Start Self Assessment
                                </button>
                            </div>
                        </div>
                    @else
                        <!-- Assignment Practice Setup -->
                        <div class="space-y-8">
                            <!-- Assignment Selection -->
                            <div class="space-y-6">
                                <div class="flex items-center space-x-3">
                                    <div class="p-3 bg-emerald-100 dark:bg-emerald-900/30 rounded-xl">
                                        <svg class="w-6 h-6 text-emerald-600 dark:text-emerald-400" fill="none"
                                             stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100">Select
                                            Assignment</h2>
                                        <p class="text-gray-600 dark:text-gray-400">Choose an assignment to practice
                                            with</p>
                                    </div>
                                </div>

                                @if($availableAssignments->isEmpty())
                                    <div class="text-center py-12">
                                        <div
                                            class="mx-auto w-24 h-24 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mb-4">
                                            <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor"
                                                 viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                                            </svg>
                                        </div>
                                        <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-2">No
                                            assignments available</h3>
                                        <p class="text-gray-600 dark:text-gray-400">There are no assignments available
                                            for practice at this time.</p>
                                    </div>
                                @else
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                        @foreach($availableAssignments as $assignment)
                                            <div
                                                class="border border-gray-200 dark:border-gray-700 rounded-xl p-6 hover:shadow-lg transition-shadow cursor-pointer {{ $selectedAssignment == $assignment->id ? 'ring-2 ring-emerald-500 bg-emerald-50 dark:bg-emerald-900/20' : '' }}"
                                                wire:click="$set('selectedAssignment', {{ $assignment->id }})">
                                                <div class="flex items-start space-x-4">
                                                    <input type="radio" wire:model.live="selectedAssignment"
                                                           value="{{ $assignment->id }}"
                                                           class="mt-1 w-4 h-4 text-emerald-600 bg-gray-100 border-gray-300 focus:ring-emerald-500 dark:focus:ring-emerald-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                                                    <div class="flex-1">
                                                        <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100">{{ $assignment->title }}</h3>
                                                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-3">{{ $assignment->description }}</p>

                                                        <div class="space-y-2">
                                                            <div
                                                                class="flex items-center text-sm text-gray-500 dark:text-gray-400">
                                                                <svg class="w-4 h-4 mr-2" fill="none"
                                                                     stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                                          stroke-width="2"
                                                                          d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                                                </svg>
                                                                {{ $assignment->academicSubject->name ?? 'Subject' }}
                                                            </div>

                                                            @if($assignment->duration_in_minutes)
                                                                <div
                                                                    class="flex items-center text-sm text-gray-500 dark:text-gray-400">
                                                                    <svg class="w-4 h-4 mr-2" fill="none"
                                                                         stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round"
                                                                              stroke-linejoin="round" stroke-width="2"
                                                                              d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                                    </svg>
                                                                    {{ $assignment->duration_in_minutes }} minutes
                                                                </div>
                                                            @endif

                                                            <div
                                                                class="flex items-center text-sm text-gray-500 dark:text-gray-400">
                                                                <svg class="w-4 h-4 mr-2" fill="none"
                                                                     stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                                          stroke-width="2"
                                                                          d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                                                </svg>
                                                                {{ $assignment->total_marks }} marks
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>

                            <!-- Start Assessment Button -->
                            @if(!$availableAssignments->isEmpty())
                                <div class="pt-6 border-t border-gray-200 dark:border-gray-700">
                                    <button wire:click="startAssessment"
                                            class="w-full bg-gradient-to-r from-emerald-500 to-teal-600 text-white py-4 px-6 rounded-lg font-semibold text-lg hover:from-emerald-600 hover:to-teal-700 transition-all transform hover:scale-105 shadow-lg" @disabled(!$selectedAssignment)>
                                        Start Assignment Practice
                                    </button>
                                </div>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
        </div>

    @elseif($step === 'assessment')
        <!-- Assessment Interface -->
        <div class="max-w-4xl mx-auto">
            <!-- Assessment Header -->
            <div
                class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 mb-6">
                <div class="p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <h1 class="text-2xl font-bold text-gray-900 dark:text-gray-100">{{ $assessment->title }}</h1>
                            <p class="text-gray-600 dark:text-gray-400">Question {{ $currentQuestionIndex + 1 }}
                                of {{ count($questions) }}</p>
                        </div>

                        @if($timeRemaining)
                            <div class="text-right">
                                <div class="text-2xl font-bold text-red-600 dark:text-red-400">
                                    {{ floor($timeRemaining / 60) }}:{{ sprintf('%02d', $timeRemaining % 60) }}
                                </div>
                                <div class="text-sm text-gray-600 dark:text-gray-400">Time Remaining</div>
                            </div>
                        @endif
                    </div>

                    <!-- Progress Bar -->
                    <div class="mt-4">
                        <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                            <div class="bg-blue-600 h-2 rounded-full transition-all duration-300"
                                 style="width: {{ (($currentQuestionIndex + 1) / count($questions)) * 100 }}%"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Question Display -->
            @if(count($questions) > 0 && isset($questions[$currentQuestionIndex]))
                @php
                    $currentQuestion = $questions[$currentQuestionIndex];
                @endphp

                <div
                    class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 p-6">
                    <div
                        class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
                        <div class="p-8 space-y-6">
                            <!-- Question Header -->
                            <div class="flex items-start space-x-4">
                                <div
                                    class="flex-shrink-0 w-12 h-12 bg-indigo-100 dark:bg-indigo-900 rounded-full flex items-center justify-center">
                        <span
                            class="text-lg font-bold text-indigo-600 dark:text-indigo-400">{{ $currentQuestionIndex + 1 }}</span>
                                </div>
                                <div class="flex-1">
                                    <div class="flex items-center space-x-2 mb-2">
                                        @if($currentQuestion['type'] === 'multiple_choice_question')
                                            <span
                                                class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                    🔘 Multiple Choice
                                </span>
                                        @elseif($currentQuestion['type'] === 'true_or_false_question')
                                            <span
                                                class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                    ✅ True/False
                                </span>
                                        @elseif($currentQuestion['type'] === 'essay_question')
                                            <span
                                                class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200">
                                    📝 Essay
                                </span>
                                        @endif

                                        @if($currentQuestion['model']->difficulty_level ?? false)
                                            @php
                                                $difficultyColors = [
                                                    'easy' => 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200',
                                                    'medium' => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200',
                                                    'hard' => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200'
                                                ];
                                            @endphp
                                            <span
                                                class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $difficultyColors[$currentQuestion['model']->difficulty_level] ?? 'bg-gray-100 text-gray-800' }}">
                                    {{ ucfirst($currentQuestion['model']->difficulty_level) }}
                                </span>
                                        @endif
                                    </div>
                                    <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100 leading-relaxed">
                                        {!!  $currentQuestion['model']->question->down !!}
                                    </h2>
                                </div>
                            </div>

                            <!-- Question Content -->
                            <div class="ml-16 space-y-4">
                                @if($currentQuestion['type'] === 'multiple_choice_question')
                                    <div class="space-y-3">
                                        @php
                                            $options = [];
                                            $question = $currentQuestion['model'];

                                            // Collect all non-empty options
                                            if (!empty($question->option_a)) $options['A'] = $question->option_a;
                                            if (!empty($question->option_b)) $options['B'] = $question->option_b;
                                            if (!empty($question->option_c)) $options['C'] = $question->option_c;
                                            if (!empty($question->option_d)) $options['D'] = $question->option_d;
                                            if (!empty($question->option_e)) $options['E'] = $question->option_e;
                                        @endphp

                                        @foreach($options as $letter => $option)
                                            <label
                                                class="flex items-start space-x-3 p-4 border border-gray-200 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer transition-colors group">
                                                <input type="radio"
                                                       name="question_{{ $currentQuestionIndex }}"
                                                       value="{{ $letter }}"
                                                       wire:model="responses.{{ $currentQuestionIndex }}.answer"
                                                       class="mt-1 h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 dark:border-gray-600">
                                                <div class="flex-1">
                    <span
                        class="text-sm font-medium text-gray-700 dark:text-gray-300 group-hover:text-gray-900 dark:group-hover:text-gray-100">
                        {{ $letter }}.
                    </span>
                                                    <span
                                                        class="ml-2 text-gray-900 dark:text-gray-100 group-hover:text-gray-900 dark:group-hover:text-gray-100">
                        {!! $option->down !!}
                    </span>
                                                </div>
                                            </label>
                                        @endforeach
                                    </div>

                                @elseif($currentQuestion['type'] === 'true_or_false_question')
                                    <div class="space-y-3">
                                        <label
                                            class="flex items-center space-x-3 p-4 border border-gray-200 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer transition-colors group">
                                            <input type="radio"
                                                   name="question_{{ $currentQuestionIndex }}"
                                                   value="true"
                                                   wire:model="responses.{{ $currentQuestionIndex }}.answer"
                                                   class="h-4 w-4 text-green-600 focus:ring-green-500 border-gray-300 dark:border-gray-600">
                                            <span
                                                class="text-lg font-medium text-green-600 dark:text-green-400">✅ True</span>
                                        </label>
                                        <label
                                            class="flex items-center space-x-3 p-4 border border-gray-200 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer transition-colors group">
                                            <input type="radio"
                                                   name="question_{{ $currentQuestionIndex }}"
                                                   value="false"
                                                   wire:model="responses.{{ $currentQuestionIndex }}.answer"
                                                   class="h-4 w-4 text-red-600 focus:ring-red-500 border-gray-300 dark:border-gray-600">
                                            <span
                                                class="text-lg font-medium text-red-600 dark:text-red-400">❌ False</span>
                                        </label>
                                    </div>
                                @elseif($currentQuestion['type'] === 'essay_question')
                                    <div>
                            <textarea wire:model="responses.{{ $currentQuestionIndex }}.answer"
                                      rows="8"
                                      class="w-full rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm"
                                      placeholder="Enter your answer here..."></textarea>
                                        <div class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                                <span wire:ignore>
                                    <span id="wordCount">0</span> words
                                </span>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Navigation -->
                        <div class="bg-gray-50 dark:bg-gray-700 px-8 py-4 flex items-center justify-between">
                            <button wire:click="previousQuestion"
                                    {{ $currentQuestionIndex === 0 ? 'disabled' : '' }}
                                    class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-600 hover:bg-gray-50 dark:hover:bg-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 disabled:opacity-50 disabled:cursor-not-allowed transition-colors">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M15 19l-7-7 7-7"/>
                                </svg>
                                Previous
                            </button>

                            <div class="flex items-center space-x-2">
                                @for($i = 0,$iMax = count($questions); $i < $iMax; $i++)

                                    <button wire:click="goToQuestion({{ $i }})"
                                            class="w-8 h-8 rounded-full text-xs font-medium transition-colors
                                {{ $i === $currentQuestionIndex ? 'bg-indigo-600 text-white' :
                                   (isset($responses[$i]) && !empty($responses[$i]['is_answered']) ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 'bg-gray-100 text-gray-600 dark:bg-gray-600 dark:text-gray-400') }}
                                hover:bg-indigo-500 hover:text-white">
                                        {{ $i + 1 }}
                                    </button>
                                @endfor
                            </div>

                            @if($currentQuestionIndex === count($questions) - 1)
                                <button wire:click="submitAssessment"
                                        onclick="return confirm('Are you sure you want to submit your assessment?')"
                                        class="inline-flex items-center px-6 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500 transition-colors">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    Submit Assessment
                                </button>
                            @else
                                <button wire:click="nextQuestion"
                                        class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-colors">
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

    @elseif($step === 'results')
        <!-- Results Display -->
        <div class="max-w-4xl mx-auto space-y-6">
            <!-- Results Header -->
            <div
                class="bg-gradient-to-r from-green-500 to-emerald-600 rounded-xl p-8 text-white shadow-lg relative overflow-hidden">
                <div class="absolute top-0 right-0 -mt-4 -mr-4 w-24 h-24 bg-white/10 rounded-full blur-2xl"></div>
                <div class="absolute bottom-0 left-0 -mb-4 -ml-4 w-32 h-32 bg-white/10 rounded-full blur-2xl"></div>

                <div class="relative text-center">
                    <div class="mx-auto w-16 h-16 bg-white/20 rounded-full flex items-center justify-center mb-4">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <h1 class="text-3xl font-bold mb-2">Assessment Complete!</h1>
                    <p class="text-green-100 text-lg">{{ $assessment->title }}</p>
                </div>
            </div>

            @include('livewire.students.partials.assessment-results', ['result' => $this->result])

            <!-- Action Buttons -->
            <div class="flex justify-center space-x-4">
                <button wire:click="$set('step', 'setup')"
                        class="px-6 py-3 bg-blue-600 text-white rounded-lg font-medium hover:bg-blue-700 transition-colors">
                    Take Another Assessment
                </button>

                <a href="{{ route('dashboard') }}"
                   class="px-6 py-3 bg-gray-500 text-white rounded-lg font-medium hover:bg-gray-600 transition-colors">
                    Return to Dashboard
                </a>
            </div>
        </div>
    @endif

    <div class="mt-4">
        <div class="py-4"><h4>Recent Assessments</h4></div>
        @if(isset($this->recentAssessments) &&  count($this->recentAssessments) > 0)
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
