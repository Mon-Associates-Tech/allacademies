<section x-data="{ showNewAssessment: false }">
    <!-- Header with Previous Assessments -->
    <div class="min-h-screen bg-gray-50 dark:bg-gray-900 transition-colors duration-200">
        @if(!empty($studentSnapshot))
            <div class="container mx-auto px-3 sm:px-4 pt-4 sm:pt-6 max-w-6xl">
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 sm:gap-4">
                    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-3 sm:p-4 shadow-sm">
                        <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Assignments</p>
                        <p class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ $studentSnapshot['assignments']['completion_rate'] ?? 0 }}%</p>
                        <p class="text-xs sm:text-sm text-gray-600 dark:text-gray-400 mt-1">
                            {{ $studentSnapshot['assignments']['upcoming'] ?? 0 }} due soon
                        </p>
                    </div>

                    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-3 sm:p-4 shadow-sm">
                        <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Quiz Performance</p>
                        <p class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ $studentSnapshot['quizzes']['average_score'] ?? 0 }}%</p>
                        <p class="text-xs sm:text-sm text-gray-600 dark:text-gray-400 mt-1">
                            {{ $studentSnapshot['quizzes']['total'] ?? 0 }} attempts
                        </p>
                    </div>

                    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-3 sm:p-4 shadow-sm">
                        <p class="text-xs uppercase tracking-wide text-gray-500 dark:text-gray-400">Reading Progress</p>
                        <p class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ $studentSnapshot['reading']['books_in_progress'] ?? 0 }}</p>
                        <p class="text-xs sm:text-sm text-gray-600 dark:text-gray-400 mt-1">
                            {{ $studentSnapshot['reading']['books_completed'] ?? 0 }} completed books
                        </p>
                    </div>
                </div>
            </div>
        @endif

        @if($step === 'selection' && !empty($previousAssessments))
            <!-- Previous Assessments Dashboard -->
            <div class="container mx-auto px-3 sm:px-4 py-4 sm:py-8 max-w-6xl" x-show="!showNewAssessment">
                <div class="mb-6 sm:mb-8">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between mb-4 sm:mb-6 space-y-3 sm:space-y-0">
                        <div>
                            <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 dark:text-white">Your Assessments</h1>
                            <p class="text-gray-600 dark:text-gray-400 mt-1 sm:mt-2 text-sm sm:text-base">Review your previous assessments or start a new one</p>
                        </div>
                        <x-button.primary @click="showNewAssessment = true" class="px-4 sm:px-6 py-2 sm:py-3 text-sm sm:text-base w-full sm:w-auto">
                            <svg class="w-4 h-4 sm:w-5 sm:h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            Start New Assessment
                        </x-button.primary>
                    </div>

                    <!-- Assessment Statistics -->
                    <div class="grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4 mb-6 sm:mb-8">
                        @php
                            $totalAssessments = count($previousAssessments);
                            $averageScore = $totalAssessments > 0 ? round(collect($previousAssessments)->avg('percentage_score'), 1) : 0;
                            $bestScore = $totalAssessments > 0 ? collect($previousAssessments)->max('percentage_score') : 0;
                            $recentAssessments = collect($previousAssessments)->filter(function($assessment) {
                                return \Carbon\Carbon::parse($assessment['created_at'])->isAfter(now()->subDays(7));
                            })->count();
                        @endphp

                        <div class="bg-white dark:bg-gray-800 p-3 sm:p-6 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
                            <div class="flex items-center">
                                <div class="p-2 sm:p-3 bg-blue-100 dark:bg-blue-900/20 rounded-lg">
                                    <svg class="w-4 h-4 sm:w-6 sm:h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                </div>
                                <div class="ml-2 sm:ml-4">
                                    <p class="text-lg sm:text-2xl font-bold text-gray-900 dark:text-white">{{ $totalAssessments }}</p>
                                    <p class="text-xs sm:text-sm text-gray-600 dark:text-gray-400">Total</p>
                                </div>
                            </div>
                        </div>

                        <div class="bg-white dark:bg-gray-800 p-3 sm:p-6 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
                            <div class="flex items-center">
                                <div class="p-2 sm:p-3 bg-green-100 dark:bg-green-900/20 rounded-lg">
                                    <svg class="w-4 h-4 sm:w-6 sm:h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                                    </svg>
                                </div>
                                <div class="ml-2 sm:ml-4">
                                    <p class="text-lg sm:text-2xl font-bold text-gray-900 dark:text-white">{{ $averageScore }}%</p>
                                    <p class="text-xs sm:text-sm text-gray-600 dark:text-gray-400">Average</p>
                                </div>
                            </div>
                        </div>

                        <div class="bg-white dark:bg-gray-800 p-3 sm:p-6 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
                            <div class="flex items-center">
                                <div class="p-2 sm:p-3 bg-yellow-100 dark:bg-yellow-900/20 rounded-lg">
                                    <svg class="w-4 h-4 sm:w-6 sm:h-6 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/>
                                    </svg>
                                </div>
                                <div class="ml-2 sm:ml-4">
                                    <p class="text-lg sm:text-2xl font-bold text-gray-900 dark:text-white">{{ $bestScore }}%</p>
                                    <p class="text-xs sm:text-sm text-gray-600 dark:text-gray-400">Best</p>
                                </div>
                            </div>
                        </div>

                        <div class="bg-white dark:bg-gray-800 p-3 sm:p-6 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
                            <div class="flex items-center">
                                <div class="p-2 sm:p-3 bg-purple-100 dark:bg-purple-900/20 rounded-lg">
                                    <svg class="w-4 h-4 sm:w-6 sm:h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                                <div class="ml-2 sm:ml-4">
                                    <p class="text-lg sm:text-2xl font-bold text-gray-900 dark:text-white">{{ $recentAssessments }}</p>
                                    <p class="text-xs sm:text-sm text-gray-600 dark:text-gray-400">This Week</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Previous Assessments List -->
                    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
                        <div class="p-4 sm:p-6 border-b border-gray-200 dark:border-gray-700">
                            <h2 class="text-lg sm:text-xl font-semibold text-gray-900 dark:text-white">Recent Assessments</h2>
                            <p class="text-gray-600 dark:text-gray-400 text-sm mt-1">Your latest assessment results</p>
                        </div>
                        <div class="divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach($previousAssessments as $prevAssessment)
                                <div class="p-4 sm:p-6 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors duration-200">
                                    <div class="flex flex-col sm:flex-row sm:items-center justify-between space-y-3 sm:space-y-0">
                                        <div class="flex-1">
                                            <div class="flex flex-col sm:flex-row sm:items-center space-y-2 sm:space-y-0 sm:space-x-3 mb-2 sm:mb-2">
                                                <h3 class="font-semibold text-gray-900 dark:text-white text-sm sm:text-base">{{ $prevAssessment['title'] }}</h3>
                                                @php $gradeClass = $prevAssessment['percentage_score'] >= 80 ? 'bg-green-100 text-green-800 dark:bg-green-900/20 dark:text-green-300' : ($prevAssessment['percentage_score'] >= 60 ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/20 dark:text-yellow-300' : 'bg-red-100 text-red-800 dark:bg-red-900/20 dark:text-red-300') @endphp
                                                <span class="px-2 py-1 text-xs font-medium rounded-full {{ $gradeClass }} w-fit">
                                                    {{ $this->getGrade($prevAssessment['percentage_score']) }}
                                                </span>
                                            </div>

                                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-2 sm:gap-4 text-xs sm:text-sm text-gray-600 dark:text-gray-400">
                                                <div class="flex items-center space-x-2">
                                                    <svg class="w-3 h-3 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                                    </svg>
                                                    <span>{{ \Carbon\Carbon::parse($prevAssessment['created_at'])->format('M d, Y') }}</span>
                                                </div>
                                                <div class="flex items-center space-x-2">
                                                    <svg class="w-3 h-3 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                                                    </svg>
                                                    <span>{{ number_format($prevAssessment['score']) }}/{{ number_format($prevAssessment['max_score']) }}</span>
                                                </div>
                                                <div class="flex items-center space-x-2">
                                                    <svg class="w-3 h-3 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                    </svg>
                                                    <span>{{ \Carbon\Carbon::parse($prevAssessment['created_at'])->diffForHumans() }}</span>
                                                </div>
                                                @if($prevAssessment['time_limit_minutes'])
                                                    <div class="flex items-center space-x-2">
                                                        <svg class="w-3 h-3 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                                        </svg>
                                                        <span>{{ $prevAssessment['time_limit_minutes'] }}min</span>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>

                                        <div class="flex items-center justify-between sm:justify-end sm:space-x-3">
                                            <div class="text-left sm:text-right">
                                                <div class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-white">
                                                    {{ $prevAssessment['percentage_score'] }}<span class="text-base sm:text-lg text-gray-500">%</span>
                                                </div>
                                            </div>

                                            <!-- Progress circle -->
                                            <div class="relative w-12 h-12 sm:w-16 sm:h-16">
                                                <svg class="w-12 h-12 sm:w-16 sm:h-16 transform -rotate-90" viewBox="0 0 36 36">
                                                    <path class="text-gray-200 dark:text-gray-700" stroke="currentColor" stroke-width="3" fill="none" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"/>
                                                    <path class="{{ $prevAssessment['percentage_score'] >= 80 ? 'text-green-500' : ($prevAssessment['percentage_score'] >= 60 ? 'text-yellow-500' : 'text-red-500') }}"
                                                          stroke="currentColor"
                                                          stroke-width="3"
                                                          fill="none"
                                                          stroke-dasharray="{{ $prevAssessment['percentage_score'] }}, 100"
                                                          d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"/>
                                                </svg>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        @endif

        @if($step === 'selection' && empty($previousAssessments))
            <!-- No Previous Assessments - Direct to New Assessment -->
            <div class="container mx-auto px-3 sm:px-4 py-8 max-w-4xl" x-show="!showNewAssessment">
                <div class="text-center py-8 sm:py-12">
                    <div class="max-w-md mx-auto">
                        <div class="mb-6 sm:mb-8">
                            <div class="mx-auto w-20 h-20 sm:w-24 sm:h-24 bg-blue-100 dark:bg-blue-900/20 rounded-full flex items-center justify-center mb-4">
                                <svg class="w-10 h-10 sm:w-12 sm:h-12 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                            </div>
                            <h1 class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-white mb-2">Welcome to Assessments</h1>
                            <p class="text-gray-600 dark:text-gray-400 text-sm sm:text-base px-4">Ready to test your knowledge? Create your first assessment to get started.</p>
                        </div>
                        <x-button.primary @click="showNewAssessment = true" class="px-6 sm:px-8 py-3 sm:py-4 text-base sm:text-lg w-full sm:w-auto">
                            <svg class="w-5 h-5 sm:w-6 sm:h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            Create Your First Assessment
                        </x-button.primary>
                    </div>
                </div>
            </div>
        @endif

        @if($step === 'selection')
            <!-- New Assessment Configuration Form -->
            <div class="container mx-auto px-3 sm:px-4 py-4 sm:py-8 max-w-4xl" x-show="showNewAssessment" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform scale-95" x-transition:enter-end="opacity-100 transform scale-100">
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
                    <!-- Header -->
                    <div class="bg-gradient-to-r from-blue-500 to-purple-600 text-white p-4 sm:p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <h1 class="text-xl sm:text-2xl md:text-3xl font-bold">Create Assessment</h1>
                                <p class="text-blue-100 mt-1 text-sm sm:text-base">Configure your assessment settings</p>
                            </div>
                            <button @click="showNewAssessment = false" class="text-white hover:text-blue-200 transition-colors duration-200">
                                <svg class="w-5 h-5 sm:w-6 sm:h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <div class="p-4 sm:p-6 space-y-4 sm:space-y-6">
                        <!-- Subject Selection -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 sm:gap-4">
                            <!-- Subject -->
                            <div>
                                <label for="subject" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Subject <span class="text-red-500">*</span>
                                </label>
                                <select wire:model.live="selectedSubject" id="subject"
                                        class="w-full px-3 py-2 text-sm sm:text-base border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white">
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
                                        class="w-full px-3 py-2 text-sm sm:text-base border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white"
                                    @disabled(empty($topics))>
                                    <option value="">All Topics</option>
                                    @foreach($topics as $topic)
                                        <option value="{{ $topic['id'] }}">{{ $topic['name'] }}</option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Subtopic -->
                            <div class="sm:col-span-2 lg:col-span-1">
                                <label for="subtopic" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                    Subtopic (Optional)
                                </label>
                                <select wire:model.live="selectedSubtopic" id="subtopic"
                                        class="w-full px-3 py-2 text-sm sm:text-base border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white"
                                    @disabled(empty($subtopics))>
                                    <option value="">All Subtopics</option>
                                    @foreach($subtopics as $subtopic)
                                        <option value="{{ $subtopic['id'] }}">{{ $subtopic['name'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- Question Configuration -->
                        <div class="">
                            <div class="mb-4">
                                <h3 class="text-base sm:text-lg font-medium text-gray-900 dark:text-white mb-3">Question Types</h3>
                                <div class="space-y-3 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-2 sm:gap-0">
                                    <label class="flex items-center">
                                        <input type="checkbox" wire:model.live="questionTypes.multiple_choice_question"
                                               class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                        <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Multiple Choice</span>
                                    </label>
                                    <label class="flex items-center">
                                        <input type="checkbox" wire:model.live="questionTypes.true_or_false_question"
                                               class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                        <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">True/False</span>
                                    </label>
                                    <label class="flex items-center">
                                        <input type="checkbox" wire:model.live="questionTypes.essay_question"
                                               class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500">
                                        <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Essay</span>
                                    </label>
                                </div>
                            </div>

                            <!-- Configuration Options -->
                            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 sm:gap-4 mt-3 mb-5">
                                <!-- Question Count -->
                                <div>
                                    <x-form.input required name="questionCount" label="Number of questions"
                                                  placeholder="Number of Questions" type="number"
                                                  wire:model.live="questionCount" id="questionCount" min="1" max="50"
                                                  class="w-full px-3 py-2 text-sm sm:text-base border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white">
                                    </x-form.input>
                                    @error('questionCount')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                    @enderror
                                </div>

                                <!-- Difficulty -->
                                <div>
                                    <x-form.select wire:model.live="difficulty" id="difficulty" name="difficulty"
                                                   :options="['all' => 'All Levels', 'easy' => 'Easy', 'medium' => 'Medium', 'hard' => 'Hard']"
                                                   class="w-full px-3 py-2 text-sm sm:text-base border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white">
                                    </x-form.select>
                                </div>

                                <!-- Time Limit -->
                                <div class="sm:col-span-2 lg:col-span-1">
                                    <x-form.input type="number" wire:model.live="timeLimitMinutes" id="timeLimit"
                                                  label="Time Limit (minutes)" min="1" max="180" name="timeLimit"
                                                  placeholder="No time limit"
                                                  class="w-full px-3 py-2 text-sm sm:text-base border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent dark:bg-gray-700 dark:text-white">
                                    </x-form.input>
                                </div>
                            </div>

                            <!-- Advanced Options -->
                            <div class="space-y-3">
                                <div class="flex items-start">
                                    <input type="checkbox" wire:model.live="balancedDistribution" id="balancedDistribution"
                                           class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500 mt-0.5">
                                    <div class="ml-2">
                                        <label for="balancedDistribution" class="text-sm text-gray-700 dark:text-gray-300">
                                            Balanced Difficulty Distribution
                                        </label>
                                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                            Ensures equal distribution of easy, medium, and hard questions when possible
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Question Distribution Preview -->
                        @if(!empty($questionDistribution) && config('app.debug'))
                            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-3 sm:p-4">
                                <h4 class="text-sm font-medium text-gray-900 dark:text-white mb-3">Available Questions by Type & Difficulty</h4>
                                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3 sm:gap-4 text-xs">
                                    @foreach($questionDistribution as $type => $difficulties)
                                        <div class="bg-white dark:bg-gray-800 p-3 rounded">
                                            <h5 class="font-medium text-gray-900 dark:text-white mb-2 capitalize text-sm">{{ str_replace('_', ' ', $type) }}</h5>
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
                            @if(config('app.debug'))
                                <button wire:click="debugQuestionData"
                                        class="px-4 py-2 bg-gray-600 hover:bg-gray-700 text-white rounded-lg font-medium transition-colors duration-200 text-sm sm:text-base">
                                    Debug Data
                                </button>
                            @endif
                            <x-button.white @click="showNewAssessment = false" class="text-sm sm:text-base">Cancel</x-button.white>
                            <x-button.primary wire:click="startAssessment"
                                              class="px-4 sm:px-6 py-2 sm:py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition-colors duration-200 text-sm sm:text-base">
                                Start Assessment
                            </x-button.primary>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        @if($step === 'taking')
            <!-- Assessment Taking Interface -->
            <div class="flex flex-col h-screen"
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

                <!-- Header -->
                <div class="bg-white dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 px-3 sm:px-4 py-2 sm:py-3">
                    <div class="flex items-center justify-between max-w-7xl mx-auto">
                        <div class="flex items-center space-x-2 sm:space-x-4 flex-1 min-w-0">
                            <button wire:click="backToSelection"
                                    class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200 flex-shrink-0">
                                <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                                </svg>
                            </button>
                            <h1 class="text-sm sm:text-lg md:text-xl font-semibold text-gray-900 dark:text-white truncate">
                                {{ $assessment->title ?? 'Assessment' }}
                            </h1>
                            <div class="hidden sm:flex items-center space-x-2 text-xs sm:text-sm text-gray-500 dark:text-gray-400">
                                <span>Q {{ $currentQuestionIndex + 1 }}/{{ count($questions) }}</span>
                                <span>•</span>
                                <span>{{ $this->getProgress() }}%</span>
                            </div>
                        </div>

                        @if($timeRemaining && !$isSubmitted)
                            <div class="flex items-center space-x-2 sm:space-x-3">
                                <!-- Timer -->
                                <div class="flex items-center space-x-1 sm:space-x-2 px-2 sm:px-3 py-1 sm:py-2 rounded-lg text-xs sm:text-sm
                               {{ $timeRemaining <= 300 ? 'bg-red-100 dark:bg-red-900/20 text-red-700 dark:text-red-300' : 'bg-blue-100 dark:bg-blue-900/20 text-blue-700 dark:text-blue-300' }}"
                                     wire:poll.1s="updateTimer">
                                    <svg class="w-3 h-3 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    <span class="font-mono font-medium">
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
                        <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-1.5">
                            <div class="bg-blue-600 h-1.5 rounded-full transition-all duration-300"
                                 style="width: {{ $this->getProgress() }}%"></div>
                        </div>
                    </div>
                </div>

                <!-- Main Content -->
                <div class="flex-1 flex overflow-hidden">
                    <!-- Question Navigation Sidebar (Desktop) -->
                    <div class="hidden lg:block w-64 bg-white dark:bg-gray-800 border-r border-gray-200 dark:border-gray-700 overflow-y-auto">
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
                        <div class="flex-1 overflow-y-auto p-3 sm:p-4 md:p-6">
                            <div class="max-w-4xl mx-auto">
                                @if(isset($questions[$currentQuestionIndex]))
                                    @php $currentQuestion = $questions[$currentQuestionIndex]; @endphp

                                    <div wire:key="question-{{ $currentQuestionIndex }}"
                                         class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                                        <!-- Question Header -->
                                        <div class="border-b border-gray-200 dark:border-gray-700 p-3 sm:p-4 md:p-6">
                                            <div class="flex flex-col sm:flex-row sm:items-center justify-between mb-3 sm:mb-4 space-y-2 sm:space-y-0">
                                                <div class="flex items-center space-x-3">
                                        <span class="flex-shrink-0 w-6 h-6 sm:w-8 sm:h-8 bg-blue-600 text-white rounded-full flex items-center justify-center text-xs sm:text-sm font-medium">
                                            {{ $currentQuestionIndex + 1 }}
                                        </span>
                                                    <div>
                                                        <h2 class="text-base sm:text-lg font-semibold text-gray-900 dark:text-white">
                                                            Question {{ $currentQuestionIndex + 1 }}
                                                        </h2>
                                                        <div class="flex flex-col sm:flex-row sm:items-center space-y-1 sm:space-y-0 sm:space-x-4 text-xs sm:text-sm text-gray-500 dark:text-gray-400">
                                                            <span class="capitalize">{{ str_replace('_', ' ', $currentQuestion['difficulty']) }}</span>
                                                            <span class="hidden sm:inline">•</span>
                                                            <span>{{ $currentQuestion['points'] }} {{ $currentQuestion['points'] == 1 ? 'point' : 'points' }}</span>
                                                        </div>
                                                    </div>
                                                </div>

                                                <!-- Question type indicator -->
                                                <div class="flex items-center space-x-2 text-xs">
                                                    @if($currentQuestion['type'] === 'multiple_choice_question')
                                                        <span class="px-2 py-1 bg-blue-100 dark:bg-blue-900/20 text-blue-700 dark:text-blue-300 rounded-full">Multiple Choice</span>
                                                    @elseif($currentQuestion['type'] === 'true_or_false_question')
                                                        <span class="px-2 py-1 bg-green-100 dark:bg-green-900/20 text-green-700 dark:text-green-300 rounded-full">True/False</span>
                                                    @elseif($currentQuestion['type'] === 'essay_question')
                                                        <span class="px-2 py-1 bg-purple-100 dark:bg-purple-900/20 text-purple-700 dark:text-purple-300 rounded-full">Essay</span>
                                                    @endif
                                                </div>
                                            </div>

                                            <!-- Question Text -->
                                            <div class="prose dark:prose-invert prose-sm sm:prose-base max-w-none"
                                                 x-html="marked.parse(@js($currentQuestion['question']))"></div>
                                        </div>

                                        <!-- Answer Options -->
                                        <div class="p-3 sm:p-4 md:p-6">
                                            @if($currentQuestion['type'] === 'multiple_choice_question')
                                                <div class="space-y-2 sm:space-y-3">
                                                    @foreach($currentQuestion['options'] as $key => $option)
                                                        @if($option)
                                                            <label class="flex items-start space-x-2 sm:space-x-3 p-3 sm:p-4 rounded-lg border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700/50 cursor-pointer transition-colors duration-200
                     {{ ($responses[$currentQuestionIndex] ?? '') === $key ? 'bg-blue-50 dark:bg-blue-900/20 border-blue-200 dark:border-blue-800' : '' }}"
                                                                   wire:key="mcq-{{ $currentQuestionIndex }}-{{ $key }}">
                                                                <input type="radio"
                                                                       wire:model="responses.{{ $currentQuestionIndex }}"
                                                                       value="{{ $key }}"
                                                                       name="question_{{ $currentQuestionIndex }}"
                                                                       wire:key="mcq-input-{{ $currentQuestionIndex }}-{{ $key }}"
                                                                       class="mt-1 w-4 h-4 text-blue-600 border-gray-300 focus:ring-blue-500">
                                                                <div class="flex-1">
                                                                    <div class="flex items-center space-x-2 mb-1">
                                                                        <span class="font-medium text-gray-900 dark:text-white text-sm sm:text-base">{{ $key }}.</span>
                                                                    </div>
                                                                    <div class="prose dark:prose-invert prose-sm max-w-none text-sm sm:text-base"
                                                                         x-html="marked.parse(@js($option))"></div>
                                                                </div>
                                                            </label>
                                                        @endif
                                                    @endforeach
                                                </div>

                                            @elseif($currentQuestion['type'] === 'true_or_false_question')
                                                <div class="space-y-2 sm:space-y-3">
                                                    <label class="flex items-center space-x-2 sm:space-x-3 p-3 sm:p-4 rounded-lg border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700/50 cursor-pointer transition-colors duration-200
             {{ ($responses[$currentQuestionIndex] ?? '') === 'true' ? 'bg-green-50 dark:bg-green-900/20 border-green-200 dark:border-green-800' : '' }}"
                                                           wire:key="tf-{{ $currentQuestionIndex }}-true">
                                                        <input type="radio"
                                                               wire:model="responses.{{ $currentQuestionIndex }}"
                                                               value="true"
                                                               name="question_{{ $currentQuestionIndex }}"
                                                               wire:key="tf-input-{{ $currentQuestionIndex }}-true"
                                                               class="w-4 h-4 text-green-600 border-gray-300 focus:ring-green-500">
                                                        <span class="text-base sm:text-lg font-medium text-gray-900 dark:text-white">True</span>
                                                    </label>
                                                    <label class="flex items-center space-x-2 sm:space-x-3 p-3 sm:p-4 rounded-lg border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-700/50 cursor-pointer transition-colors duration-200
             {{ ($responses[$currentQuestionIndex] ?? '') === 'false' ? 'bg-red-50 dark:bg-red-900/20 border-red-200 dark:border-red-800' : '' }}"
                                                           wire:key="tf-{{ $currentQuestionIndex }}-false">
                                                        <input type="radio"
                                                               wire:model="responses.{{ $currentQuestionIndex }}"
                                                               value="false"
                                                               name="question_{{ $currentQuestionIndex }}"
                                                               wire:key="tf-input-{{ $currentQuestionIndex }}-false"
                                                               class="w-4 h-4 text-red-600 border-gray-300 focus:ring-red-500">
                                                        <span class="text-base sm:text-lg font-medium text-gray-900 dark:text-white">False</span>
                                                    </label>
                                                </div>

                                            @elseif($currentQuestion['type'] === 'essay_question')
                                                <div>
                                        <textarea wire:model.live.debounce.500ms="responses.{{ $currentQuestionIndex }}"
                                                  placeholder="Type your answer here..."
                                                  rows="6"
                                                  class="w-full px-3 sm:px-4 py-2 sm:py-3 text-sm sm:text-base border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400"></textarea>
                                                    <div class="flex justify-between text-xs text-gray-500 dark:text-gray-400 mt-2">
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

                        @if(config('app.debug'))
                            <div class="mt-4 p-4 bg-gray-100 dark:bg-gray-700 rounded-lg mx-3 sm:mx-4 md:mx-6">
                                <p class="text-xs text-gray-600 dark:text-gray-400 break-all">Debug: {{ json_encode($this->responses) }}</p>
                            </div>
                        @endif

                        <!-- Navigation Footer -->
                        <div class="bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700 p-3 sm:p-4">
                            <div class="max-w-4xl mx-auto">
                                <!-- Mobile Question Navigation -->
                                <div class="lg:hidden mb-3 sm:mb-4">
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
                                         class="grid grid-cols-6 sm:grid-cols-8 gap-1.5 sm:gap-2">
                                        @foreach($questions as $index => $question)
                                            <button wire:click="goToQuestion({{ $index }})"
                                                    @click="showMobileMenu = false"
                                                    class="w-8 h-8 sm:w-10 sm:h-10 rounded-lg text-xs sm:text-sm font-medium transition-colors duration-200
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
                                            class="flex items-center space-x-1 sm:space-x-2 px-3 sm:px-4 py-2 bg-gray-600 hover:bg-gray-700 disabled:bg-gray-300 disabled:cursor-not-allowed text-white rounded-lg font-medium transition-colors duration-200 text-sm sm:text-base">
                                        <svg class="w-3 h-3 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                                        </svg>
                                        <span class="hidden sm:inline">Previous</span>
                                        <span class="sm:hidden">Prev</span>
                                    </button>

                                    <div class="flex items-center space-x-2 sm:space-x-4">
                                        @if($this->getAnsweredCount() > 0)
                                            <button wire:click="submitAssessment"
                                                    wire:confirm="Are you sure you want to submit? You have answered {{ $this->getAnsweredCount() }} out of {{ count($questions) }} questions."
                                                    class="px-3 sm:px-6 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg font-medium transition-colors duration-200 text-sm sm:text-base">
                                                <span class="hidden sm:inline">Submit Assessment</span>
                                                <span class="sm:hidden">Submit</span>
                                            </button>
                                        @endif

                                        @if($currentQuestionIndex < count($questions) - 1)
                                            <button wire:click="nextQuestion"
                                                    class="flex items-center space-x-1 sm:space-x-2 px-3 sm:px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition-colors duration-200 text-sm sm:text-base">
                                                <span class="hidden sm:inline">Next</span>
                                                <span class="sm:hidden">Next</span>
                                                <svg class="w-3 h-3 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                                </svg>
                                            </button>
                                        @endif
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
                    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-xl max-w-sm w-full p-4 sm:p-6">
                        <div class="flex items-center space-x-3 mb-4">
                            <div class="flex-shrink-0">
                                <svg class="w-6 h-6 sm:w-8 sm:h-8 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/>
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-base sm:text-lg font-semibold text-gray-900 dark:text-white">Time Warning</h3>
                            </div>
                        </div>
                        <p class="text-sm sm:text-base text-gray-600 dark:text-gray-400 mb-4 sm:mb-6">
                            You have less than 5 minutes remaining! Please finish and submit your answers.
                        </p>
                        <button @click="timeWarning = false"
                                class="w-full px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-lg font-medium transition-colors duration-200 text-sm sm:text-base">
                            Continue Assessment
                        </button>
                    </div>
                </div>
            </div>
        @endif

        @if($step === 'results')
            <!-- Results View -->
            <div class="container mx-auto px-3 sm:px-4 py-4 sm:py-8 max-w-4xl">
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700 overflow-hidden">
                    <!-- Results Header -->
                    <div class="bg-gradient-to-r from-blue-500 to-purple-600 text-white p-4 sm:p-6">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between space-y-2 sm:space-y-0">
                            <div>
                                <h1 class="text-xl sm:text-2xl md:text-3xl font-bold">Assessment Results</h1>
                                <p class="text-blue-100 mt-1 text-sm sm:text-base">{{ $assessment->title ?? 'Self Assessment' }}</p>
                            </div>
                            <div class="text-left sm:text-right">
                                <div class="text-2xl sm:text-3xl font-bold">{{ $results['percentage'] }}%</div>
                                <div class="text-sm">{{ $this->getGrade($results['percentage']) }} Grade</div>
                            </div>
                        </div>
                    </div>

                    <!-- Results Summary -->
                    <div class="p-4 sm:p-6">
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 sm:gap-4 mb-4 sm:mb-6">
                            <div class="text-center p-3 sm:p-4 bg-green-50 dark:bg-green-900/20 rounded-lg">
                                <div class="text-lg sm:text-2xl font-bold text-green-600 dark:text-green-400">{{ $results['correct_answers'] }}</div>
                                <div class="text-xs sm:text-sm text-gray-600 dark:text-gray-400">Correct</div>
                            </div>
                            <div class="text-center p-3 sm:p-4 bg-red-50 dark:bg-red-900/20 rounded-lg">
                                <div class="text-lg sm:text-2xl font-bold text-red-600 dark:text-red-400">{{ $results['total_questions'] - $results['correct_answers'] }}</div>
                                <div class="text-xs sm:text-sm text-gray-600 dark:text-gray-400">Incorrect</div>
                            </div>
                            <div class="text-center p-3 sm:p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                                <div class="text-lg sm:text-2xl font-bold text-blue-600 dark:text-blue-400">{{ $results['total_score'] }}/{{ $results['max_score'] }}</div>
                                <div class="text-xs sm:text-sm text-gray-600 dark:text-gray-400">Score</div>
                            </div>
                            <div class="text-center p-3 sm:p-4 bg-purple-50 dark:bg-purple-900/20 rounded-lg">
                                <div class="text-lg sm:text-2xl font-bold text-purple-600 dark:text-purple-400">{{ $results['completion_rate'] }}%</div>
                                <div class="text-xs sm:text-sm text-gray-600 dark:text-gray-400">Completed</div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex flex-col sm:flex-row gap-3 justify-center">
                            <button wire:click="toggleReview"
                                    class="px-4 sm:px-6 py-2 sm:py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition-colors duration-200 text-sm sm:text-base">
                                {{ $showReview ? 'Hide Review' : 'Review Answers' }}
                            </button>
                            <button wire:click="restartAssessment"
                                    class="px-4 sm:px-6 py-2 sm:py-3 bg-gray-600 hover:bg-gray-700 text-white rounded-lg font-medium transition-colors duration-200 text-sm sm:text-base">
                                Take Again
                            </button>
                        </div>

                        @if($showReview)
                            <!-- Answer Review -->
                            <div class="mt-6 sm:mt-8 space-y-4 sm:space-y-6">
                                <h3 class="text-lg sm:text-xl font-semibold text-gray-900 dark:text-white mb-4">Answer Review</h3>
                                @foreach($questions as $index => $question)
                                    <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-3 sm:p-4
                                   {{ isset($results['graded_responses'][$index]) && $results['graded_responses'][$index]['is_correct']
                                      ? 'bg-green-50 dark:bg-green-900/10 border-green-200 dark:border-green-800'
                                      : 'bg-red-50 dark:bg-red-900/10 border-red-200 dark:border-red-800' }}">
                                        <div class="flex items-start gap-2 sm:gap-3">
                                <span class="flex-shrink-0 w-6 h-6 sm:w-8 sm:h-8 rounded-full text-white text-xs sm:text-sm font-medium flex items-center justify-center
                                           {{ isset($results['graded_responses'][$index]) && $results['graded_responses'][$index]['is_correct']
                                              ? 'bg-green-500' : 'bg-red-500' }}">
                                    {{ $index + 1 }}
                                </span>
                                            <div class="flex-1 min-w-0">
                                                <div class="prose dark:prose-invert prose-sm max-w-none mb-2 sm:mb-3 text-sm sm:text-base"
                                                     x-html="marked.parse(@js($question['question']))"></div>

                                                @if($question['type'] === 'multiple_choice_question')
                                                    <div class="space-y-1 sm:space-y-2 mb-2 sm:mb-3">
                                                        @foreach($question['options'] as $key => $option)
                                                            @if($option)
                                                                <div class="flex items-start text-xs sm:text-sm space-x-2
                                                               {{ ($responses[$index] ?? '') === $key ? 'font-medium' : '' }}
                                                               {{ $question['answer'] === $key ? 'text-green-600 dark:text-green-400' : 'text-gray-700 dark:text-gray-300' }}">
                                                                    <span class="w-4 sm:w-6 flex-shrink-0">{{ $key }}.</span>
                                                                    <div class="flex-1 min-w-0">
                                                                        <span class="break-words" x-html="marked.parse(@js($option))"></span>
                                                                        <div class="flex flex-wrap gap-1 sm:gap-2 mt-1">
                                                                            @if(($responses[$index] ?? '') === $key)
                                                                                <span class="text-blue-600 text-xs">← Your answer</span>
                                                                            @endif
                                                                            @if($question['answer'] === $key)
                                                                                <span class="text-green-600 text-xs">✓ Correct</span>
                                                                            @endif
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            @endif
                                                        @endforeach
                                                    </div>
                                                @elseif($question['type'] === 'true_or_false_question')
                                                    <div class="mb-2 sm:mb-3 space-y-1">
                                                        <p class="text-xs sm:text-sm">Your answer: <span class="font-medium">{{ $responses[$index] ?? 'Not answered' }}</span></p>
                                                        <p class="text-xs sm:text-sm">Correct answer: <span class="font-medium text-green-600">{{ $question['answer'] ? 'True' : 'False' }}</span></p>
                                                    </div>
                                                @elseif($question['type'] === 'essay_question')
                                                    <div class="mb-2 sm:mb-3">
                                                        <p class="text-xs sm:text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Your Response:</p>
                                                        <div class="bg-white dark:bg-gray-700 p-2 sm:p-3 rounded border text-xs sm:text-sm max-h-32 overflow-y-auto">
                                                            {{ $responses[$index] ?? 'No response provided' }}
                                                        </div>
                                                        <p class="text-xs text-gray-500 mt-1 italic">Essay questions require manual grading</p>
                                                    </div>
                                                @endif

                                                @if(isset($results['graded_responses'][$index]['feedback']))
                                                    <div class="text-xs sm:text-sm {{ isset($results['graded_responses'][$index]) && $results['graded_responses'][$index]['is_correct'] ? 'text-green-700 dark:text-green-300' : 'text-red-700 dark:text-red-300' }}">
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
</section>
