<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $exam->title }} – Completed</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full bg-slate-50 dark:bg-slate-950 flex items-center justify-center p-4">

<div class="w-full max-w-lg">

    {{-- Logo / brand mark --}}
    <div class="text-center mb-8">
        <div class="inline-flex items-center justify-center w-20 h-20 bg-gradient-to-br from-emerald-500 to-emerald-400 rounded-[2px] shadow-lg shadow-emerald-500/30 mb-4">
            <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
        </div>
        <h1 class="text-2xl font-bold text-slate-900 dark:text-white">Examination Submitted!</h1>
        <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">
            Your responses have been recorded for <strong class="text-slate-700 dark:text-slate-300">{{ $exam->title }}</strong>
        </p>
    </div>

    {{-- Errors --}}
    @if($errors->any())
        <div class="mb-4 px-4 py-3 rounded-[2px] bg-red-50 border border-red-200 text-red-700 text-sm dark:bg-red-900/20 dark:border-red-800 dark:text-red-400">
            @foreach($errors->all() as $e) <p>{{ $e }}</p> @endforeach
        </div>
    @endif

    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-[2px] shadow-sm overflow-hidden mb-6">
        
        @if($submission)
            @if($exam->canShowResults())
                {{-- Results Available --}}
                <div class="px-5 py-4 bg-gradient-to-br from-violet-50 to-violet-100 dark:from-violet-900/30 dark:to-violet-900/20 border-b border-violet-200 dark:border-violet-800">
                    <h2 class="text-xs font-bold text-violet-700 dark:text-violet-400 uppercase tracking-widest">Your Results</h2>
                </div>
                <div class="p-6 space-y-4">
                    <div class="grid grid-cols-3 gap-4 text-center">
                        <div>
                            <p class="text-2xl font-bold text-violet-600 dark:text-violet-400">{{ $submission->grade ?? '—' }}</p>
                            <p class="text-xs text-slate-400 mt-1 uppercase tracking-wider">Grade</p>
                        </div>
                        <div>
                            <p class="text-2xl font-bold text-slate-800 dark:text-slate-200">{{ number_format($submission->percentage ?? 0, 1) }}%</p>
                            <p class="text-xs text-slate-400 mt-1 uppercase tracking-wider">Score</p>
                        </div>
                        <div>
                            <p class="text-2xl font-bold text-slate-800 dark:text-slate-200">{{ number_format($submission->score ?? 0, 1) }}</p>
                            <p class="text-xs text-slate-400 mt-1 uppercase tracking-wider">of {{ number_format($submission->total_marks ?? 0, 1) }}</p>
                        </div>
                    </div>

                    @if($submission->teacher_feedback)
                        <div class="p-3 bg-slate-50 dark:bg-slate-800 rounded-[2px] text-sm text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-slate-700">
                            <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider mb-1">Instructor Feedback</p>
                            {{ $submission->teacher_feedback }}
                        </div>
                    @endif

                    @if($submission->requires_manual_review)
                        <p class="text-xs text-amber-600 dark:text-amber-400 text-center">
                            ⚠ Some answers require manual grading — your final score may be updated.
                        </p>
                    @endif

                    <div class="pt-2">
                        <a href="{{ route('mock-exams.take.results', $exam) }}"
                           class="block w-full px-5 py-3 bg-gradient-to-r from-violet-600 to-violet-500 hover:from-violet-700 hover:to-violet-600 text-white font-semibold rounded-[2px] text-sm shadow-[0_2px_10px_rgba(124,58,237,0.3)] transition-all text-center">
                            View Detailed Results →
                        </a>
                    </div>
                </div>
            @else
                {{-- Submission exists but results not yet available --}}
                <div class="p-6 text-center space-y-4">
                    <div class="w-16 h-16 mx-auto bg-slate-100 dark:bg-slate-800 rounded-full flex items-center justify-center">
                        <svg class="w-8 h-8 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div>
                        <p class="text-sm text-slate-600 dark:text-slate-400">
                            Your exam has been submitted successfully.
                        </p>
                        @switch($exam->result_visibility)
                            @case('manual_release')
                                <p class="text-sm text-slate-500 dark:text-slate-400 mt-2">Results will be released by your instructor.</p>
                                @break
                            @case('after_due_date')
                                <p class="text-sm text-slate-500 dark:text-slate-400 mt-2">
                                    Results will be available after the exam period closes
                                    @if($exam->ends_at) on <strong>{{ $exam->ends_at->format('M d, Y H:i') }}</strong>@endif.
                                </p>
                                @break
                            @case('scheduled')
                                <p class="text-sm text-slate-500 dark:text-slate-400 mt-2">
                                    Results will be available
                                    @if($exam->results_release_datetime) on <strong>{{ $exam->results_release_datetime->format('M d, Y H:i') }}</strong>@endif.
                                </p>
                                @break
                            @default
                                <p class="text-sm text-slate-500 dark:text-slate-400 mt-2">Results will be available soon.</p>
                        @endswitch
                    </div>
                    
                    <a href="{{ route('mock-exams.take.results', $exam) }}"
                       class="block w-full px-5 py-3 border border-violet-600 text-violet-600 dark:text-violet-400 hover:bg-violet-50 dark:hover:bg-violet-900/20 font-semibold rounded-[2px] text-sm transition-all text-center">
                        Check Results Status
                    </a>
                </div>
            @endif
        @else
            {{-- No submission found --}}
            <div class="p-6 text-center">
                <p class="text-sm text-slate-500 dark:text-slate-400">No submission record found.</p>
            </div>
        @endif
    </div>

    {{-- Action --}}
    <a href="{{ route('mock-exams.take.join') }}"
       class="block w-full px-5 py-3 border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 font-medium rounded-[2px] text-sm hover:bg-slate-50 dark:hover:bg-slate-800 transition-all text-center">
        Return to Join Page
    </a>
</div>

</body>
</html>
