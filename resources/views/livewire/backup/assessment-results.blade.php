<div class="max-w-4xl mx-auto space-y-6">
    <!-- Results Header -->
    <div class="bg-gradient-to-r from-green-500 to-emerald-600 rounded-xl p-8 text-white shadow-lg relative overflow-hidden">
        <div class="absolute top-0 right-0 -mt-4 -mr-4 w-24 h-24 bg-white/10 rounded-full blur-2xl"></div>
        <div class="absolute bottom-0 left-0 -mb-4 -ml-4 w-32 h-32 bg-white/10 rounded-full blur-2xl"></div>

        <div class="relative text-center">
            <div class="mx-auto w-16 h-16 bg-white/20 rounded-full flex items-center justify-center mb-4">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <h1 class="text-3xl font-bold mb-2">Assessment Complete!</h1>
            <p class="text-green-100 text-lg">{{ $assessment->title }}</p>
        </div>
    </div>

    <!-- Score Summary -->
    @if($assessmentResult)
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700">
            <div class="p-8">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                    <!-- Overall Score -->
                    <div class="text-center">
                        <div class="text-4xl font-bold text-gray-900 dark:text-gray-100 mb-2">
                            {{ $assessmentResult->percentage_score ?? 0 }}%
                        </div>
                        <div class="text-sm text-gray-600 dark:text-gray-400">Overall Score</div>
                    </div>

                    <!-- Correct Answers -->
                    <div class="text-center">
                        <div class="text-4xl font-bold text-green-600 dark:text-green-400 mb-2">
                            {{ $assessmentResult->correct_answers ?? 0 }}
                        </div>
                        <div class="text-sm text-gray-600 dark:text-gray-400">Correct</div>
                    </div>

                    <!-- Incorrect Answers -->
                    <div class="text-center">
                        <div class="text-4xl font-bold text-red-600 dark:text-red-400 mb-2">
                            {{ $assessmentResult->incorrect_answers ?? 0 }}
                        </div>
                        <div class="text-sm text-gray-600 dark:text-gray-400">Incorrect</div>
                    </div>

                    <!-- Time Taken -->
                    <div class="text-center">
                        <div class="text-4xl font-bold text-blue-600 dark:text-blue-400 mb-2">
                            {{ $assessmentResult->time_taken ?? 0 }}m
                        </div>
                        <div class="text-sm text-gray-600 dark:text-gray-400">Time Taken</div>
                    </div>
                </div>

                <!-- Performance Indicator -->
                <div class="mt-8">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-sm text-gray-600 dark:text-gray-400">Performance</span>
                        <span class="text-sm font-medium text-gray-900 dark:text-gray-100">
                            {{ $assessmentResult->percentage_score ?? 0 }}%
                        </span>
                    </div>
                    <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-3">
                        <div class="h-3 rounded-full transition-all duration-500 {{ ($assessmentResult->percentage_score ?? 0) >= 80 ? 'bg-green-500' : (($assessmentResult->percentage_score ?? 0) >= 60 ? 'bg-yellow-500' : 'bg-red-500') }}"
                             style="width: {{ $assessmentResult->percentage_score ?? 0 }}%"></div>
                    </div>
                </div>

                <!-- Performance Message -->
                <div class="mt-6 p-4 rounded-lg {{ ($assessmentResult->percentage_score ?? 0) >= 80 ? 'bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800' : (($assessmentResult->percentage_score ?? 0) >= 60 ? 'bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800' : 'bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800') }}">
                    <div class="flex items-center space-x-2">
                        @if(($assessmentResult->percentage_score ?? 0) >= 80)
                            <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span class="text-green-800 dark:text-green-200 font-medium">Excellent work!</span>
                        @elseif(($assessmentResult->percentage_score ?? 0) >= 60)
                            <svg class="w-5 h-5 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span class="text-yellow-800 dark:text-yellow-200 font-medium">Good effort!</span>
                        @else
                            <svg class="w-5 h-5 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span class="text-red-800 dark:text-red-200 font-medium">Keep practicing!</span>
                        @endif
                    </div>
                    <p class="text-sm mt-2 {{ ($assessmentResult->percentage_score ?? 0) >= 80 ? 'text-green-700 dark:text-green-300' : (($assessmentResult->percentage_score ?? 0) >= 60 ? 'text-yellow-700 dark:text-yellow-300' : 'text-red-700 dark:text-red-300') }}">
                        @if(($assessmentResult->percentage_score ?? 0) >= 80)
                            You've demonstrated excellent understanding of the material. Keep up the great work!
                        @elseif(($assessmentResult->percentage_score ?? 0) >= 60)
                            You're on the right track! Review the areas where you missed questions to improve further.
                        @else
                            Consider reviewing the material and trying again. Practice makes perfect!
                        @endif
                    </p>
                </div>
            </div>
        </div>
    @endif

    <!-- Question Review -->
    @if($assessmentResult && $assessmentResult->questions_data)
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700">
            <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-gray-100">Question Review</h2>
                <p class="text-gray-600 dark:text-gray-400 mt-1">Review your answers and see the correct solutions</p>
            </div>

            <div class="divide-y divide-gray-200 dark:divide-gray-700">
                @foreach($assessmentResult->questions_data as $index => $questionData)
                    <div class="p-6">
                        <div class="flex items-start space-x-4">
                            <!-- Question Number -->
                            <div class="flex-shrink-0 w-8 h-8 rounded-full flex items-center justify-center text-sm font-medium {{ $questionData['is_correct'] ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' }}">
                                {{ $index + 1 }}
                            </div>

                            <!-- Question Content -->
                            <div class="flex-1">
                                <div class="flex items-center space-x-2 mb-2">
                                    <span class="text-sm font-medium {{ $questionData['is_correct'] ? 'text-green-600 dark:text-green-400' : 'text-red-600 dark:text-red-400' }}">
                                        {{ $questionData['is_correct'] ? 'Correct' : 'Incorrect' }}
                                    </span>
                                    <span class="px-2 py-1 bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-300 text-xs rounded">
                                        {{ $questionData['points'] }} {{ $questionData['points'] == 1 ? 'point' : 'points' }}
                                    </span>
                                </div>

                                <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-2">
                                    {{ $questionData['question'] }}
                                </h3>

                                @if($questionData['type'] === 'multiple_choice_question')
                                    <div class="space-y-2">
                                        <div class="text-sm">
                                            <span class="text-gray-600 dark:text-gray-400">Your answer:</span>
                                            <span class="ml-2 px-2 py-1 rounded {{ $questionData['is_correct'] ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' }}">
                                                {{ $questionData['student_answer'] }}
                                            </span>
                                        </div>
                                        @if(!$questionData['is_correct'])
                                            <div class="text-sm">
                                                <span class="text-gray-600 dark:text-gray-400">Correct answer:</span>
                                                <span class="ml-2 px-2 py-1 bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200 rounded">
                                                    {{ $questionData['correct_answer'] }}
                                                </span>
                                            </div>
                                        @endif
                                    </div>
                                @elseif($questionData['type'] === 'true_or_false_question')
                                    <div class="space-y-2">
                                        <div class="text-sm">
                                            <span class="text-gray-600 dark:text-gray-400">Your answer:</span>
                                            <span class="ml-2 px-2 py-1 rounded {{ $questionData['is_correct'] ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' }}">
                                                {{ ucfirst($questionData['student_answer']) }}
                                            </span>
                                        </div>
                                        @if(!$questionData['is_correct'])
                                            <div class="text-sm">
                                                <span class="text-gray-600 dark:text-gray-400">Correct answer:</span>
                                                <span class="ml-2 px-2 py-1 bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200 rounded">
                                                    {{ ucfirst($questionData['correct_answer']) }}
                                                </span>
                                            </div>
                                        @endif
                                    </div>
                                @elseif($questionData['type'] === 'essay_question')
                                    <div class="space-y-2">
                                        <div class="text-sm text-gray-600 dark:text-gray-400">
                                            Essay questions require manual grading by your teacher.
                                        </div>
                                        @if($questionData['student_answer'])
                                            <div class="mt-2 p-3 bg-gray-50 dark:bg-gray-700 rounded">
                                                <div class="text-sm text-gray-600 dark:text-gray-400 mb-1">Your answer:</div>
                                                <p class="text-sm text-gray-900 dark:text-gray-100">
                                                    {{ Str::limit($questionData['student_answer'], 200) }}
                                                </p>
                                            </div>
                                        @endif
                                    </div>
                                @endif

                                @if($questionData['explanation'])
                                    <div class="mt-3 p-3 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded">
                                        <div class="text-sm text-blue-700 dark:text-blue-300">
                                            <strong>Explanation:</strong> {{ $questionData['explanation'] }}
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Actions -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg border border-gray-200 dark:border-gray-700">
        <div class="p-6">
            <div class="flex flex-wrap gap-4 justify-center">
                <button wire:click="$set('step', 'setup')"
                        class="flex items-center space-x-2 px-6 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                    <span>Take Another Assessment</span>
                </button>

                <a href="{{ route('student.performance') }}"
                   class="flex items-center space-x-2 px-6 py-3 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                    <span>View Performance</span>
                </a>

                <a href="{{ route('assessments.index') }}"
                   class="flex items-center space-x-2 px-6 py-3 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                    <span>Dashboard</span>
                </a>
            </div>
        </div>
    </div>
</div>
