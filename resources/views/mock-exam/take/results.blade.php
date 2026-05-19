<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Results - {{ $exam->title }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full bg-slate-50 dark:bg-slate-950">

<div class="min-h-screen py-8 px-4 sm:px-6 lg:px-8">
    <div class="max-w-4xl mx-auto space-y-6">

        {{-- Header --}}
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-br from-violet-600 to-violet-400 rounded-[2px] shadow-lg shadow-violet-500/30 mb-4">
                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                </svg>
            </div>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Your Results</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">{{ $exam->title }}</p>
        </div>

        {{-- Errors --}}
        @if($errors->any())
            <div class="mb-4 px-4 py-3 rounded-[2px] bg-red-50 border border-red-200 text-red-700 text-sm dark:bg-red-900/20 dark:border-red-800 dark:text-red-400">
                @foreach($errors->all() as $e) <p>{{ $e }}</p> @endforeach
            </div>
        @endif

        {{-- Score Summary --}}
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-[2px] shadow-sm overflow-hidden">
            <div class="grid grid-cols-2 md:grid-cols-4 divide-x divide-slate-200 dark:divide-slate-700">
                @foreach([
                    ['label' => 'Score', 'value' => number_format($submission->score ?? 0, 1) . ' / ' . number_format($submission->total_marks ?? 0, 1)],
                    ['label' => 'Percentage', 'value' => number_format($submission->percentage ?? 0, 1) . '%'],
                    ['label' => 'Grade', 'value' => $submission->grade ?? '—'],
                    ['label' => 'Time Spent', 'value' => gmdate('i:s', $submission->time_spent_seconds ?? 0)],
                ] as $stat)
                    <div class="p-5 text-center">
                        <p class="text-2xl font-bold text-slate-900 dark:text-white">{{ $stat['value'] }}</p>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 uppercase tracking-wider">{{ $stat['label'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Detailed Analytics --}}
        <div class="grid md:grid-cols-3 gap-4">
            {{-- Accuracy Card --}}
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-[2px] shadow-sm p-5">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Accuracy</h3>
                    <svg class="w-5 h-5 text-violet-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                </div>
                <p class="text-3xl font-bold text-violet-600 dark:text-violet-400">{{ $analytics['accuracy_percentage'] }}%</p>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                    {{ $analytics['correct_answers'] }} correct / {{ $analytics['correct_answers'] + $analytics['incorrect_answers'] }} graded
                </p>
            </div>

            {{-- Questions Answered --}}
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-[2px] shadow-sm p-5">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Questions</h3>
                    <svg class="w-5 h-5 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <p class="text-3xl font-bold text-blue-600 dark:text-blue-400">{{ $analytics['answered_questions'] }}/{{ $analytics['total_questions'] }}</p>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                    @if($analytics['unanswered_questions'] > 0)
                        {{ $analytics['unanswered_questions'] }} unanswered
                    @else
                        All questions answered
                    @endif
                </p>
            </div>

            {{-- Time Analysis --}}
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-[2px] shadow-sm p-5">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Avg Time/Q</h3>
                    <svg class="w-5 h-5 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <p class="text-3xl font-bold text-amber-600 dark:text-amber-400">{{ gmdate('i:s', $analytics['avg_time_per_question']) }}</p>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                    Total: {{ $analytics['time_spent_formatted'] }}
                </p>
            </div>
        </div>

        {{-- Teacher Feedback --}}
        @if($submission->teacher_feedback)
            <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-[2px] p-5">
                <h2 class="font-semibold text-blue-800 dark:text-blue-300 mb-2 flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/>
                    </svg>
                    Instructor Feedback
                </h2>
                <p class="text-blue-700 dark:text-blue-400 whitespace-pre-wrap text-sm">{{ $submission->teacher_feedback }}</p>
            </div>
        @endif

        {{-- Subject Breakdown --}}
        @if($exam->canShowQuestionBreakdown() && !empty($analytics['subject_breakdown']))
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-[2px] shadow-sm overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-700">
                    <h2 class="font-bold text-slate-900 dark:text-white text-sm uppercase tracking-wider" style="letter-spacing: 0.1em;">
                        Performance by Subject
                    </h2>
                </div>
                <div class="divide-y divide-slate-100 dark:divide-slate-800">
                    @foreach($analytics['subject_breakdown'] as $subject)
                        <div class="px-6 py-4">
                            <div class="flex items-center justify-between mb-2">
                                <h3 class="font-semibold text-slate-900 dark:text-white text-sm">{{ $subject['subject_name'] }}</h3>
                                <span class="text-sm font-bold text-violet-600 dark:text-violet-400">{{ $subject['percentage'] }}%</span>
                            </div>
                            <div class="grid grid-cols-4 gap-4 text-xs">
                                <div>
                                    <p class="text-slate-500 dark:text-slate-400">Questions</p>
                                    <p class="font-semibold text-slate-900 dark:text-white">{{ $subject['answered_questions'] }}/{{ $subject['total_questions'] }}</p>
                                </div>
                                <div>
                                    <p class="text-slate-500 dark:text-slate-400">Correct</p>
                                    <p class="font-semibold text-emerald-600 dark:text-emerald-400">{{ $subject['correct_answers'] }}</p>
                                </div>
                                <div>
                                    <p class="text-slate-500 dark:text-slate-400">Marks</p>
                                    <p class="font-semibold text-slate-900 dark:text-white">{{ $subject['marks_earned'] }}/{{ $subject['marks_possible'] }}</p>
                                </div>
                                <div>
                                    <p class="text-slate-500 dark:text-slate-400">Accuracy</p>
                                    <p class="font-semibold text-blue-600 dark:text-blue-400">
                                        {{ $subject['total_questions'] > 0 ? round(($subject['correct_answers'] / $subject['total_questions']) * 100, 1) : 0 }}%
                                    </p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- Questions & Answers --}}
        @if($exam->canShowQuestionBreakdown())
        <div class="bg-white dark:bg-slate-900 rounded-[2px] border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-200 dark:border-slate-700">
                <h2 class="font-bold text-slate-900 dark:text-white text-sm uppercase tracking-wider" style="letter-spacing: 0.1em;">
                    Question Review
                </h2>
            </div>

            @php $responses = $submission->responses ?? []; @endphp
            @php $questionNumber = 1; @endphp

            @foreach($exam->subjectExams as $se)
                <div class="border-b border-slate-200 dark:border-slate-700 last:border-0">
                    <div class="px-6 py-3 bg-slate-50 dark:bg-slate-800/50">
                        <h3 class="font-semibold text-slate-900 dark:text-white text-sm">{{ $se->getDisplayTitle() }}</h3>
                    </div>

                    @foreach($se->sections as $section)
                        <div class="px-6 py-3 border-b border-slate-100 dark:border-slate-800">
                            <p class="text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider" style="letter-spacing:0.08em;">
                                {{ $section->title }} — {{ ucfirst(str_replace('_', ' ', $section->question_type)) }}
                            </p>
                        </div>

                        @foreach($section->questions as $q)
                            @php
                                $resp = $responses[$q->id] ?? null;
                                $participantAnswer = $resp['response'] ?? null;
                                $isCorrect = $resp['is_correct'] ?? null;
                                $pointsEarned = $resp['points_earned'] ?? 0;
                                $correctAnswer = $q->correct_answer;
                            @endphp

                            <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-800 last:border-0
                                {{ $isCorrect === true 
                                    ? 'bg-emerald-50/30 dark:bg-emerald-900/10' 
                                    : ($isCorrect === false ? 'bg-red-50/30 dark:bg-red-900/10' : '') }}">
                                
                                <div class="flex items-start gap-3 mb-3">
                                    <span class="flex-shrink-0 w-6 h-6 flex items-center justify-center rounded-full text-xs font-bold
                                        {{ $isCorrect === true 
                                            ? 'bg-emerald-500 text-white' 
                                            : ($isCorrect === false ? 'bg-red-500 text-white' : 'bg-slate-300 dark:bg-slate-600 text-slate-700 dark:text-slate-200') }}">
                                        {{ $questionNumber++ }}
                                    </span>
                                    <div class="flex-1">
                                        <p class="text-sm font-medium text-slate-900 dark:text-white mb-2">{{ $q->question_text }}</p>
                                        
                                        {{-- Participant's Answer --}}
                                        @if($exam->canShowParticipantResponses())
                                        <div class="mb-2">
                                            <p class="text-xs text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1" style="letter-spacing:0.08em;">Your Answer:</p>
                                            <div class="text-sm text-slate-700 dark:text-slate-300">
                                                @if(is_array($participantAnswer))
                                                    <ul class="list-disc list-inside space-y-1">
                                                        @foreach($participantAnswer as $answer)
                                                            <li>{{ is_array($answer) ? json_encode($answer) : $answer }}</li>
                                                        @endforeach
                                                    </ul>
                                                @else
                                                    <p>{{ $participantAnswer ?? 'Not answered' }}</p>
                                                @endif
                                            </div>
                                        </div>
                                        @endif

                                        {{-- Correct Answer (if incorrect or for review) --}}
                                        @if($exam->canShowCorrectAnswers() && ($isCorrect === false || $q->isEssay()))
                                            <div class="mt-2">
                                                <p class="text-xs text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1" style="letter-spacing:0.08em;">Correct Answer:</p>
                                                <div class="text-sm text-emerald-700 dark:text-emerald-400">
                                                    @if(is_array($correctAnswer))
                                                        <ul class="list-disc list-inside space-y-1">
                                                            @foreach($correctAnswer as $answer)
                                                                <li>{{ is_array($answer) ? json_encode($answer) : $answer }}</li>
                                                            @endforeach
                                                        </ul>
                                                    @else
                                                        <p>{{ $correctAnswer }}</p>
                                                    @endif
                                                </div>
                                            </div>
                                        @endif

                                        {{-- Points --}}
                                        <div class="mt-2 flex items-center gap-2">
                                            <span class="text-xs font-medium text-slate-600 dark:text-slate-400">
                                                Points: {{ number_format($pointsEarned, 1) }} / {{ number_format($q->marks, 1) }}
                                            </span>
                                            @if($isCorrect === true)
                                                <span class="inline-flex items-center px-2 py-0.5 text-xs font-semibold rounded-[2px] bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400">
                                                    ✓ Correct
                                                </span>
                                            @elseif($isCorrect === false)
                                                <span class="inline-flex items-center px-2 py-0.5 text-xs font-semibold rounded-[2px] bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400">
                                                    ✗ Incorrect
                                                </span>
                                            @endif
                                        </div>

                                        {{-- Feedback --}}
                                        @if(isset($resp['feedback']) && $resp['feedback'])
                                            <div class="mt-2 p-2 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-[2px]">
                                                <p class="text-xs text-blue-700 dark:text-blue-400">{{ $resp['feedback'] }}</p>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @endforeach
                </div>
            @endforeach
        </div>
        @else
            {{-- Question breakdown is hidden by administrator --}}
            <div class="bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 rounded-[2px] p-8 text-center">
                <svg class="w-16 h-16 mx-auto text-slate-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                </svg>
                <p class="text-slate-600 dark:text-slate-400 font-medium">Detailed question breakdown is not available</p>
                <p class="text-xs text-slate-500 dark:text-slate-500 mt-1">Your instructor has restricted access to individual question details.</p>
            </div>
        @endif

        {{-- Footer Actions --}}
        <div class="flex gap-3">
            <a href="{{ route('mock-exams.take.completed', $exam) }}"
               class="flex-1 px-5 py-3 border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 font-medium rounded-[2px] text-sm hover:bg-slate-50 dark:hover:bg-slate-800 transition-all text-center">
                ← Back
            </a>
            <a href="{{ route('mock-exams.take.join') }}"
               class="flex-1 px-5 py-3 bg-gradient-to-r from-violet-600 to-violet-500 hover:from-violet-700 hover:to-violet-600 text-white font-semibold rounded-[2px] text-sm shadow-[0_2px_10px_rgba(124,58,237,0.3)] transition-all text-center">
                Take Another Exam
            </a>
        </div>

        {{-- Footer Note --}}
        <div class="text-center text-xs text-slate-400 dark:text-slate-500">
            <p>If you have questions about your results, please contact your instructor.</p>
        </div>

    </div>
</div>

</body>
</html>
