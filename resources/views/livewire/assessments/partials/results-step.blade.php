<div class="max-w-6xl mx-auto">
    <!-- Results Header -->
    <div class="bg-gradient-to-r from-emerald-600 to-teal-600 rounded-xl p-8 text-white mb-8 shadow-xl">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-4xl font-bold mb-2">Assessment Complete!</h1>
                <p class="text-emerald-100 text-lg">Here are your results and performance analysis</p>
            </div>
            <div class="hidden md:block">
                <div class="w-24 h-24 bg-white/20 rounded-full flex items-center justify-center">
                    <svg class="w-12 h-12" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    @if($assessmentResult && $performance)
        <div class="space-y-8">
            <!-- Score Overview -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <!-- Overall Score -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 text-center">
                    <div class="w-16 h-16 bg-gradient-to-r from-indigo-600 to-purple-600 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-1">{{ $performance['overall_score'] }}%</h3>
                    <p class="text-gray-600 dark:text-gray-400">Overall Score</p>
                    <div class="mt-2">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                            {{ $performance['overall_score'] >= 80 ? 'bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400' :
                               ($performance['overall_score'] >= 60 ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/30 dark:text-yellow-400' :
                               'bg-red-100 text-red-800 dark:bg-red-900/30 dark:text-red-400') }}">
                            Grade: {{ $performance['grade'] }}
                        </span>
                    </div>
                </div>

                <!-- Questions Correct -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 text-center">
                    <div class="w-16 h-16 bg-gradient-to-r from-green-600 to-emerald-600 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-1">{{ $performance['questions_correct'] }}</h3>
                    <p class="text-gray-600 dark:text-gray-400">Correct Answers</p>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">out of {{ count($questions) }}</p>
                </div>

                <!-- Time Taken -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 text-center">
                    <div class="w-16 h-16 bg-gradient-to-r from-blue-600 to-indigo-600 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-1">{{ $performance['time_taken'] }}</h3>
                    <p class="text-gray-600 dark:text-gray-400">Minutes</p>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Time Taken</p>
                </div>

                <!-- Completion Rate -->
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-6 text-center">
                    <div class="w-16 h-16 bg-gradient-to-r from-purple-600 to-pink-600 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-1">{{ $performance['completion_rate'] }}%</h3>
                    <p class="text-gray-600 dark:text-gray-400">Completion</p>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ $performance['questions_answered'] }} answered</p>
                </div>
            </div>

            <!-- Performance Chart -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-8">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">Performance Breakdown</h2>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <!-- Score Distribution -->
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Score Distribution</h3>
                        <div class="space-y-4">
                            <div class="flex items-center justify-between">
                                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Correct</span>
                                <div class="flex items-center space-x-2">
                                    <div class="w-32 bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                        <div class="bg-green-600 h-2 rounded-full" style="width: {{ ($performance['questions_correct'] / count($questions)) * 100 }}%"></div>
                                    </div>
                                    <span class="text-sm text-gray-600 dark:text-gray-400">{{ $performance['questions_correct'] }}</span>
                                </div>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Incorrect</span>
                                <div class="flex items-center space-x-2">
                                    <div class="w-32 bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                        <div class="bg-red-600 h-2 rounded-full" style="width: {{ (($performance['questions_answered'] - $performance['questions_correct']) / count($questions)) * 100 }}%"></div>
                                    </div>
                                    <span class="text-sm text-gray-600 dark:text-gray-400">{{ $performance['questions_answered'] - $performance['questions_correct'] }}</span>
                                </div>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Unanswered</span>
                                <div class="flex items-center space-x-2">
                                    <div class="w-32 bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                        <div class="bg-gray-400 h-2 rounded-full" style="width: {{ ((count($questions) - $performance['questions_answered']) / count($questions)) * 100 }}%"></div>
                                    </div>
                                    <span class="text-sm text-gray-600 dark:text-gray-400">{{ count($questions) - $performance['questions_answered'] }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Question Types Performance -->
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Performance by Question Type</h3>
                        <div class="space-y-3">
                            @php
                                $questionTypes = collect($questions)->groupBy('type');
                                dd($assessmentResult);
                                $responseData = $assessmentResult['graded_responses'];
                            @endphp

                            @foreach($questionTypes as $type => $typeQuestions)
                                @php
                                    $typeResponses = collect($responseData['questions'])->filter(function($q) use ($type) {
                                        return $q['question_type'] === $type;
                                    });
                                    $correctCount = $typeResponses->where('is_correct', true)->count();
                                    $totalCount = $typeResponses->count();
                                    $percentage = $totalCount > 0 ? ($correctCount / $totalCount) * 100 : 0;
                                @endphp

                                <div class="flex items-center justify-between p-3 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                    <div>
                                        <span class="text-sm font-medium text-gray-900 dark:text-white">{{ ucfirst(str_replace('_', ' ', $type)) }}</span>
                                        <span class="text-xs text-gray-500 dark:text-gray-400 ml-2">{{ $correctCount }}/{{ $totalCount }}</span>
                                    </div>
                                    <div class="flex items-center space-x-2">
                                        <div class="w-24 bg-gray-200 dark:bg-gray-600 rounded-full h-2">
                                            <div class="bg-indigo-600 h-2 rounded-full" style="width: {{ $percentage }}%"></div>
                                        </div>
                                        <span class="text-sm font-medium text-gray-900 dark:text-white">{{ round($percentage) }}%</span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <!-- Detailed Question Review -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-8">
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-6">Question Review</h2>

                <div class="space-y-6">
                    @foreach($assessmentResult->data['questions'] as $index => $questionData)
                        @php
                            $question = $questions[$index];
                            $isCorrect = $questionData['is_correct'];
                            $isAnswered = $questionData['is_answered'];
                        @endphp

                        <div class="border border-gray-200 dark:border-gray-600 rounded-lg p-6 {{ $isCorrect ? 'bg-green-50 dark:bg-green-900/10' : ($isAnswered ? 'bg-red-50 dark:bg-red-900/10' : 'bg-gray-50 dark:bg-gray-700') }}">
                            <div class="flex items-start justify-between mb-4">
                                <div class="flex items-center space-x-3">
                                    <span class="bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200 text-sm font-semibold px-3 py-1 rounded-full">
                                        Q{{ $index + 1 }}
                                    </span>
                                    <span class="text-sm font-medium {{ $isCorrect ? 'text-green-600' : ($isAnswered ? 'text-red-600' : 'text-gray-600') }}">
                                        {{ $isCorrect ? 'Correct' : ($isAnswered ? 'Incorrect' : 'Not Answered') }}
                                    </span>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <span class="text-sm text-gray-600 dark:text-gray-400">{{ $questionData['score'] }}/{{ $questionData['max_score'] }} points</span>
                                    @if($isCorrect)
                                        <svg class="w-5 h-5 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                        </svg>
                                    @elseif($isAnswered)
                                        <svg class="w-5 h-5 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                        </svg>
                                    @else
                                        <svg class="w-5 h-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                        </svg>
                                    @endif
                                </div>
                            </div>

                            <div class="mb-4">

                                <p class="text-gray-900 dark:text-white font-medium">{!! $question['formatted']['question'] !!}</p>
                            </div>

                            @if($question['type'] === 'multiple_choice_question')
                                <div class="space-y-2">
                                    @foreach($question['formatted']['options'] as $optionIndex => $option)
                                        @php
                                            $isUserChoice = isset($questionData['response']['selected_option']) && $questionData['response']['selected_option'] == $optionIndex;
                                            $isCorrectAnswer = isset($question['model']['correct_answer']) && $question['model']['correct_answer'] == $optionIndex;
                                        @endphp

                                        <div class="flex items-center space-x-2 p-2 rounded {{ $isCorrectAnswer ? 'bg-green-100 dark:bg-green-900/20' : ($isUserChoice && !$isCorrectAnswer ? 'bg-red-100 dark:bg-red-900/20' : '') }}">
                                            <span class="text-sm font-medium text-gray-600 dark:text-gray-400">{{ chr(65 + (int) $optionIndex) }}.</span>
                                            <span class="text-sm text-gray-900 dark:text-white">{{ $option }}</span>
                                            @if($isUserChoice)
                                                <span class="text-xs bg-blue-100 text-blue-800 dark:bg-blue-900/30 dark:text-blue-400 px-2 py-1 rounded">Your Answer</span>
                                            @endif
                                            @if($isCorrectAnswer)
                                                <span class="text-xs bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400 px-2 py-1 rounded">Correct</span>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            @elseif($question['type'] === 'true_or_false_question')
                                <div class="space-y-2">
                                    @php
                                        $userAnswer = $questionData['response']['answer'] ?? null;
                                        $correctAnswer = $question['model']['is_true'] ? 'true' : 'false';
                                    @endphp

                                    <div class="flex items-center space-x-4">
                                        <span class="text-sm text-gray-600 dark:text-gray-400">Your Answer:</span>
                                        <span class="text-sm font-medium {{ $userAnswer === $correctAnswer ? 'text-green-600' : 'text-red-600' }}">
                                            {{ $userAnswer ? ucfirst($userAnswer) : 'Not Answered' }}
                                        </span>
                                    </div>
                                    <div class="flex items-center space-x-4">
                                        <span class="text-sm text-gray-600 dark:text-gray-400">Correct Answer:</span>
                                        <span class="text-sm font-medium text-green-600">{{ ucfirst($correctAnswer) }}</span>
                                    </div>
                                </div>
                            @elseif($question['type'] === 'essay_question')
                                <div class="space-y-3">
                                    <div>
                                        <span class="text-sm text-gray-600 dark:text-gray-400">Your Answer:</span>
                                        <div class="mt-1 p-3 bg-gray-100 dark:bg-gray-700 rounded text-sm text-gray-900 dark:text-white">
                                            {{ $questionData['response']['answer'] ?? 'Not answered' }}
                                        </div>
                                    </div>
                                    <div class="text-sm text-yellow-600 dark:text-yellow-400">
                                        * Essay questions require manual grading
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex justify-center space-x-4">
                <button wire:click="restartAssessment"
                        class="px-8 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-lg transition-colors duration-200 flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                    Start New Assessment
                </button>

                <button wire:click="viewDetailedResults"
                        class="px-8 py-3 bg-gray-600 hover:bg-gray-700 text-white font-semibold rounded-lg transition-colors duration-200 flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                    View Detailed Report
                </button>
            </div>
        </div>
    @else
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-lg p-8 text-center">
            <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">No Results Available</h2>
            <p class="text-gray-600 dark:text-gray-400">Assessment results could not be loaded.</p>
        </div>
    @endif
</div>
