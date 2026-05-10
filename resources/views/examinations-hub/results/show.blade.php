<x-layouts.exam>
<div class="min-h-screen bg-gray-50 dark:bg-gray-900 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <!-- Header -->
        <div class="mb-6">
            <a href="{{ route('examinations-hub.results.index', ['email' => $email]) }}" class="text-indigo-600 dark:text-indigo-400 hover:underline mb-4 inline-block">
                ← Back to Results
            </a>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-white">{{ $submission->assignment->title }}</h1>
            <p class="mt-2 text-gray-600 dark:text-gray-400">Submitted on {{ $submission->submitted_at?->format('F d, Y \a\t H:i') }}</p>
        </div>

        <!-- Score Summary -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6 mb-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                <div class="text-center">
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Score</p>
                    <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ $submission->score }}/{{ $submission->total_marks }}</p>
                </div>
                <div class="text-center">
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Percentage</p>
                    <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ number_format($submission->percentage, 1) }}%</p>
                </div>
                <div class="text-center">
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Grade</p>
                    <p class="text-3xl font-bold
                        @if($submission->grade === 'A+' || $submission->grade === 'A') text-green-600 dark:text-green-400
                        @elseif($submission->grade === 'B') text-blue-600 dark:text-blue-400
                        @elseif($submission->grade === 'C') text-yellow-600 dark:text-yellow-400
                        @else text-red-600 dark:text-red-400
                        @endif">
                        {{ $submission->grade }}
                    </p>
                </div>
                <div class="text-center">
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-1">Time Taken</p>
                    <p class="text-3xl font-bold text-gray-900 dark:text-white">{{ $submission->time_taken_minutes ?? 0 }} min</p>
                </div>
            </div>
        </div>

        <!-- Questions and Answers -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Questions & Answers</h2>
            </div>

            <div class="p-6 space-y-6">
                @php
                    $questionNumber = 1;
                    $responses = $submission->responses ?? [];
                @endphp

                @foreach($submission->assignment->sections as $section)
                    <div class="mb-8">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4 pb-2 border-b border-gray-200 dark:border-gray-700">
                            {{ $section->title }}
                        </h3>

                        @foreach($section->questions as $question)
                            @php
                                $response = $responses[$question->id] ?? null;
                                $isCorrect = $response['is_correct'] ?? null;
                                $pointsEarned = $response['points_earned'] ?? 0;
                            @endphp

                            <div class="mb-6 p-4 rounded-lg border
                                @if($isCorrect === true) border-green-200 dark:border-green-800 bg-green-50 dark:bg-green-900/10
                                @elseif($isCorrect === false) border-red-200 dark:border-red-800 bg-red-50 dark:bg-red-900/10
                                @else border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50
                                @endif">
                                
                                <div class="flex items-start justify-between mb-3">
                                    <div class="flex-1">
                                        <span class="font-semibold text-gray-900 dark:text-white">Question {{ $questionNumber++ }}</span>
                                        <span class="ml-2 text-sm text-gray-600 dark:text-gray-400">({{ $question->marks }} marks)</span>
                                    </div>
                                    <div class="text-right">
                                        <span class="text-sm font-semibold
                                            @if($isCorrect === true) text-green-600 dark:text-green-400
                                            @elseif($isCorrect === false) text-red-600 dark:text-red-400
                                            @else text-gray-600 dark:text-gray-400
                                            @endif">
                                            {{ $pointsEarned }}/{{ $question->marks }}
                                        </span>
                                    </div>
                                </div>

                                <div class="mb-3">
                                    <x-form.markdown-with-math :content="$question->question_text" />
                                </div>

                                @if($question->question_type === 'multiple_choice')
                                    <div class="space-y-2 mb-3">
                                        @foreach($question->options as $option)
                                            @php
                                                $isSelected = ($response['response'] ?? null) === $option;
                                                $isCorrectOption = $question->correct_answer === $option;
                                            @endphp
                                            <div class="flex items-center p-2 rounded
                                                @if($isCorrectOption) bg-green-100 dark:bg-green-900/20
                                                @elseif($isSelected && !$isCorrectOption) bg-red-100 dark:bg-red-900/20
                                                @endif">
                                                <span class="mr-2">
                                                    @if($isSelected)
                                                        @if($isCorrectOption)
                                                            <svg class="h-5 w-5 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                                            </svg>
                                                        @else
                                                            <svg class="h-5 w-5 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                                            </svg>
                                                        @endif
                                                    @elseif($isCorrectOption)
                                                        <svg class="h-5 w-5 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                                        </svg>
                                                    @else
                                                        <span class="inline-block h-5 w-5"></span>
                                                    @endif
                                                </span>
                                                <x-form.markdown-with-math :content="$option" />
                                            </div>
                                        @endforeach
                                    </div>
                                @elseif($question->question_type === 'true_false')
                                    <div class="mb-3">
                                        <p class="text-sm text-gray-600 dark:text-gray-400">Your answer: 
                                            <span class="font-semibold
                                                @if($isCorrect === true) text-green-600 dark:text-green-400
                                                @elseif($isCorrect === false) text-red-600 dark:text-red-400
                                                @endif">
                                                {{ $response['response'] ?? 'Not answered' }}
                                            </span>
                                        </p>
                                        @if($isCorrect === false)
                                            <p class="text-sm text-gray-600 dark:text-gray-400">Correct answer: 
                                                <span class="font-semibold text-green-600 dark:text-green-400">{{ $question->correct_answer }}</span>
                                            </p>
                                        @endif
                                    </div>
                                @elseif($question->question_type === 'essay')
                                    <div class="mb-3">
                                        <p class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Your answer:</p>
                                        <div class="p-3 bg-white dark:bg-gray-900 rounded border border-gray-200 dark:border-gray-700">
                                            <p class="text-gray-900 dark:text-white whitespace-pre-wrap">{{ $response['response'] ?? 'Not answered' }}</p>
                                        </div>
                                    </div>
                                @endif

                                @if($question->explanation && $isCorrect === false)
                                    <div class="mt-3 p-3 bg-blue-50 dark:bg-blue-900/20 rounded border border-blue-200 dark:border-blue-800">
                                        <p class="text-sm font-medium text-blue-900 dark:text-blue-300 mb-1">Explanation:</p>
                                        <x-form.markdown-with-math :content="$question->explanation" class="text-sm text-blue-800 dark:text-blue-200" />
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endforeach
            </div>
        </div>

    </div>
</div>
</x-layouts.exam>
