<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $exam->title }} – Completed</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-full bg-slate-50 dark:bg-slate-950 flex items-center justify-center p-4">

<div class="w-full max-w-lg text-center">

    {{-- Success icon --}}
    <div class="inline-flex items-center justify-center w-20 h-20 bg-gradient-to-br from-emerald-500 to-emerald-400 rounded-full shadow-lg shadow-emerald-500/30 mb-6">
        <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
        </svg>
    </div>

    <h1 class="text-2xl font-bold text-slate-900 dark:text-white mb-2">Examination Submitted!</h1>
    <p class="text-slate-500 dark:text-slate-400 mb-8">
        Your responses have been recorded. Thank you for completing <strong class="text-slate-700 dark:text-slate-300">{{ $exam->title }}</strong>.
    </p>

    {{-- Results (if visible) --}}
    @if($submission && $exam->canShowResults() && $submission->isGraded())
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-[2px] shadow-sm overflow-hidden mb-6 text-left">
            <div class="px-5 py-4 bg-gradient-to-br from-violet-50 to-violet-100 dark:from-violet-900/30 dark:to-violet-900/20 border-b border-violet-200 dark:border-violet-800">
                <h2 class="text-xs font-bold text-violet-700 dark:text-violet-400 uppercase tracking-widest">Your Results</h2>
            </div>
            <div class="p-5">
                <div class="flex items-center justify-center gap-8 mb-4">
                    <div class="text-center">
                        <p class="text-3xl font-bold text-violet-600 dark:text-violet-400">
                            {{ $submission->grade ?? '—' }}
                        </p>
                        <p class="text-xs text-slate-400 mt-1">Grade</p>
                    </div>
                    <div class="text-center">
                        <p class="text-3xl font-bold text-slate-800 dark:text-slate-200">
                            {{ number_format($submission->percentage ?? 0, 1) }}%
                        </p>
                        <p class="text-xs text-slate-400 mt-1">Score</p>
                    </div>
                    <div class="text-center">
                        <p class="text-3xl font-bold text-slate-800 dark:text-slate-200">
                            {{ number_format($submission->score ?? 0, 1) }}
                        </p>
                        <p class="text-xs text-slate-400 mt-1">of {{ number_format($submission->total_marks ?? 0, 1) }} marks</p>
                    </div>
                </div>

                @if($submission->teacher_feedback)
                    <div class="mt-4 p-3 bg-slate-50 dark:bg-slate-800 rounded-[2px] text-sm text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-slate-700">
                        <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1">Instructor Feedback</p>
                        {{ $submission->teacher_feedback }}
                    </div>
                @endif

                @if($submission->requires_manual_review)
                    <p class="mt-3 text-xs text-amber-600 dark:text-amber-400 text-center">
                        ⚠ Some answers require manual grading — your final score may be updated.
                    </p>
                @endif
            </div>
        </div>
    @else
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-[2px] p-5 mb-6 text-sm text-slate-500 dark:text-slate-400">
            @switch($exam->result_visibility)
                @case('manual_release')
                    <p>Your results will be released by your instructor. Check back later.</p>
                    @break
                @case('after_due_date')
                    <p>Results will be available after the exam period closes
                        @if($exam->ends_at) on <strong>{{ $exam->ends_at->format('M d, Y H:i') }}</strong> @endif.
                    </p>
                    @break
                @case('scheduled')
                    <p>Results will be available
                        @if($exam->results_release_datetime) on <strong>{{ $exam->results_release_datetime->format('M d, Y H:i') }}</strong> @endif.
                    </p>
                    @break
                @default
                    <p>Your responses have been recorded. Results will be available soon.</p>
            @endswitch
        </div>
    @endif

    {{-- Action --}}
    <a href="{{ route('mock-exams.take.join') }}"
       class="inline-flex items-center gap-2 px-6 py-2.5 border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 font-medium rounded-[2px] text-sm hover:bg-slate-50 dark:hover:bg-slate-800 transition-all">
        Return to Join Page
    </a>
</div>

</body>
</html>
