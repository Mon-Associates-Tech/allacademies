<x-layouts.app>
    <div class="py-6">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="mb-6">
                <a href="{{ route('student.general-exams.index') }}" class="text-sm text-indigo-600 hover:text-indigo-700 dark:text-indigo-400 flex items-center gap-1 mb-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                    Back to My Assignments
                </a>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $submission->assignment->title ?? 'Assignment Result' }}</h1>
                <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">Submitted {{ $submission->submitted_at?->format('M d, Y \a\t H:i') ?? 'N/A' }}</p>
            </div>

            <!-- Score Card -->
            <div class="bg-gradient-to-r {{ $submission->score >= 70 ? 'from-green-500 to-emerald-600' : ($submission->score >= 50 ? 'from-amber-500 to-orange-600' : 'from-red-500 to-rose-600') }} rounded-2xl p-6 mb-6 text-white">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-lg font-medium opacity-90">Your Score</h2>
                        <p class="text-sm opacity-75 mt-1">
                            @if($submission->score >= 70)
                                Excellent work! 🎉
                            @elseif($submission->score >= 50)
                                Good effort! Keep practicing.
                            @else
                                Keep studying and try again.
                            @endif
                        </p>
                    </div>
                    <div class="text-right">
                        <div class="text-5xl font-bold">{{ number_format($submission->score ?? 0, 1) }}%</div>
                        <div class="text-sm opacity-75 mt-1">{{ $submission->points_earned ?? 0 }} / {{ $submission->total_points ?? $submission->assignment->total_marks ?? 0 }} points</div>
                    </div>
                </div>
            </div>

            <!-- Stats Grid -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mb-6">
                <div class="bg-white dark:bg-gray-800 rounded-xl p-4 shadow-sm border border-gray-200 dark:border-gray-700">
                    <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ $submission->assignment->questions()->count() }}</div>
                    <div class="text-sm text-gray-500 dark:text-gray-400">Questions</div>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-xl p-4 shadow-sm border border-gray-200 dark:border-gray-700">
                    @php
                        $correctCount = 0;
                        $responses = $submission->responses ?? [];
                        foreach ($responses as $response) {
                            if (isset($response['is_correct']) && $response['is_correct']) {
                                $correctCount++;
                            }
                        }
                    @endphp
                    <div class="text-2xl font-bold text-green-600">{{ $correctCount }}</div>
                    <div class="text-sm text-gray-500 dark:text-gray-400">Correct</div>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-xl p-4 shadow-sm border border-gray-200 dark:border-gray-700">
                    @php
                        $timeSpent = $submission->time_spent_seconds ?? 0;
                        $minutes = floor($timeSpent / 60);
                        $seconds = $timeSpent % 60;
                    @endphp
                    <div class="text-2xl font-bold text-gray-900 dark:text-white">{{ $minutes }}:{{ str_pad($seconds, 2, '0', STR_PAD_LEFT) }}</div>
                    <div class="text-sm text-gray-500 dark:text-gray-400">Time Spent</div>
                </div>
                <div class="bg-white dark:bg-gray-800 rounded-xl p-4 shadow-sm border border-gray-200 dark:border-gray-700">
                    <div class="text-2xl font-bold text-gray-900 dark:text-white">#{{ $submission->attempt_number ?? 1 }}</div>
                    <div class="text-sm text-gray-500 dark:text-gray-400">Attempt</div>
                </div>
            </div>

            <!-- Teacher Feedback -->
            @if($submission->teacher_feedback)
                <div class="bg-blue-50 dark:bg-blue-900/30 border border-blue-200 dark:border-blue-800 rounded-xl p-6 mb-6">
                    <h2 class="font-semibold text-blue-800 dark:text-blue-300 mb-2">Instructor Feedback</h2>
                    <p class="text-blue-700 dark:text-blue-400 whitespace-pre-wrap">{{ $submission->teacher_feedback }}</p>
                </div>
            @endif

            <!-- Questions Review -->
            @if($submission->assignment->show_correct_answers || $submission->assignment->results_released)
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                    <h2 class="font-semibold text-gray-900 dark:text-white mb-4">Question Review</h2>
                    <div class="space-y-6">
                        @foreach($submission->assignment->questions()->orderBy('order')->get() as $index => $question)
                            @php
                                $response = $submission->responses[$question->id] ?? null;
                                $isCorrect = $response['is_correct'] ?? null;
                                $userAnswer = $response['response'] ?? null;
                                $pointsEarned = $response['points_earned'] ?? 0;
                            @endphp
                            <div class="p-4 border rounded-xl {{ $isCorrect === true ? 'border-green-200 dark:border-green-800 bg-green-50 dark:bg-green-900/20' : ($isCorrect === false ? 'border-red-200 dark:border-red-800 bg-red-50 dark:bg-red-900/20' : 'border-gray-200 dark:border-gray-700') }}">
                                <div class="flex items-start justify-between gap-4 mb-3">
                                    <div class="flex items-center gap-2">
                                        <span class="text-sm font-medium text-gray-500">Q{{ $index + 1 }}</span>
                                        <span class="px-2 py-0.5 text-xs bg-gray-100 dark:bg-gray-700 rounded">{{ ucfirst(str_replace('_', ' ', $question->type)) }}</span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <span class="text-sm {{ $isCorrect === true ? 'text-green-600' : ($isCorrect === false ? 'text-red-600' : 'text-gray-500') }}">
                                            {{ $pointsEarned }}/{{ $question->marks }} pts
                                        </span>
                                        @if($isCorrect === true)
                                            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                            </svg>
                                        @elseif($isCorrect === false)
                                            <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                            </svg>
                                        @endif
                                    </div>
                                </div>

                                <p class="text-gray-900 dark:text-white mb-3">{{ $question->question }}</p>

                                <!-- Your Answer -->
                                <div class="mb-3">
                                    <span class="text-sm font-medium text-gray-600 dark:text-gray-400">Your answer: </span>
                                    @if($userAnswer)
                                        @if($question->type === 'multiple_choice')
                                            <span class="{{ $isCorrect ? 'text-green-600' : 'text-red-600' }}">
                                                {{ $userAnswer }}. {{ $question->options[$userAnswer] ?? '' }}
                                            </span>
                                        @elseif($question->type === 'true_false')
                                            <span class="{{ $isCorrect ? 'text-green-600' : 'text-red-600' }}">{{ ucfirst($userAnswer) }}</span>
                                        @else
                                            <span class="text-gray-900 dark:text-white">{{ $userAnswer }}</span>
                                        @endif
                                    @else
                                        <span class="text-gray-400 italic">No answer provided</span>
                                    @endif
                                </div>

                                <!-- Correct Answer (if showing) -->
                                @if($submission->assignment->show_correct_answers && in_array($question->type, ['multiple_choice', 'true_false']))
                                    <div class="mb-3">
                                        <span class="text-sm font-medium text-gray-600 dark:text-gray-400">Correct answer: </span>
                                        @if($question->type === 'multiple_choice')
                                            <span class="text-green-600">{{ $question->correct_answer }}. {{ $question->options[$question->correct_answer] ?? '' }}</span>
                                        @else
                                            <span class="text-green-600">{{ ucfirst($question->correct_answer) }}</span>
                                        @endif
                                    </div>
                                @endif

                                <!-- Explanation -->
                                @if($question->explanation && $submission->assignment->show_correct_answers)
                                    <div class="mt-3 p-3 bg-gray-100 dark:bg-gray-700/50 rounded-lg">
                                        <span class="text-sm font-medium text-gray-600 dark:text-gray-400">Explanation: </span>
                                        <span class="text-sm text-gray-700 dark:text-gray-300">{{ $question->explanation }}</span>
                                    </div>
                                @endif

                                <!-- Question Feedback -->
                                @if(isset($response['feedback']) && $response['feedback'])
                                    <div class="mt-3 p-3 bg-blue-50 dark:bg-blue-900/30 rounded-lg">
                                        <span class="text-sm font-medium text-blue-600 dark:text-blue-400">Feedback: </span>
                                        <span class="text-sm text-blue-700 dark:text-blue-300">{{ $response['feedback'] }}</span>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
            @else
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6 text-center">
                    <svg class="w-12 h-12 mx-auto text-gray-300 dark:text-gray-600 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                    </svg>
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">Detailed Results Not Available</h3>
                    <p class="text-gray-500 dark:text-gray-400">The instructor has not released detailed results for this assignment yet.</p>
                </div>
            @endif

            <!-- Actions -->
            <div class="mt-6 flex items-center justify-between">
                <a href="{{ route('student.general-exams.index') }}" class="px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700">
                    ← Back to Assignments
                </a>
                @if($submission->assignment->max_attempts > ($submission->attempt_number ?? 1) && $submission->assignment->status === 'published')
                    <a href="{{ route('general-exams.join.code', $submission->assignment->access_code) }}" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg">
                        Try Again
                    </a>
                @endif
            </div>
        </div>
    </div>
</x-layouts.app>
