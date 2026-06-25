<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $exam->title }} – Start</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-full bg-slate-50 dark:bg-slate-950">

<div class="max-w-2xl mx-auto px-4 py-12">

    {{-- Exam header --}}
    <div class="text-center mb-8">
        <div class="inline-flex items-center justify-center w-12 h-12 bg-gradient-to-br from-violet-600 to-violet-400 rounded-[2px] shadow-lg shadow-violet-500/30 mb-4">
            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2"/>
            </svg>
        </div>
        <h1 class="text-2xl font-bold text-slate-900 dark:text-white">{{ $exam->title }}</h1>
        <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">
            Welcome, <strong class="text-slate-700 dark:text-slate-300">{{ session('mock_exam_participant_name', 'Candidate') }}</strong>
        </p>
    </div>

    {{-- Exam info card --}}
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 rounded-[2px] shadow-sm overflow-hidden mb-6">
        <div class="px-5 py-4 bg-gradient-to-br from-slate-50 to-slate-100 dark:from-slate-800 dark:to-slate-800 border-b border-slate-200 dark:border-slate-700">
            <h2 class="text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-widest">Exam Overview</h2>
        </div>
        <div class="p-5 grid grid-cols-2 gap-4 text-sm">
            <div>
                <p class="text-slate-400 text-xs">Total Sections</p>
                <p class="font-semibold text-slate-800 dark:text-slate-200">{{ $totalSections }}</p>
            </div>
            @if($exam->ends_at)
                <div>
                    <p class="text-slate-400 text-xs">Exam Closes</p>
                    <p class="font-semibold text-slate-800 dark:text-slate-200">{{ $exam->ends_at->format('M d, Y H:i') }}</p>
                </div>
            @endif
            <div>
                <p class="text-slate-400 text-xs">Result Visibility</p>
                <p class="font-semibold text-slate-800 dark:text-slate-200">{{ str_replace('_', ' ', ucfirst($exam->result_visibility)) }}</p>
            </div>
            <div>
                <p class="text-slate-400 text-xs">Max Attempts</p>
                <p class="font-semibold text-slate-800 dark:text-slate-200">{{ $exam->max_attempts }}</p>
            </div>
        </div>
    </div>

    {{-- Instructions --}}
    @if($exam->instructions)
        <div class="bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-[2px] p-5 mb-6">
            <p class="text-xs font-bold text-amber-700 dark:text-amber-400 uppercase tracking-widest mb-2">Instructions</p>
            <div class="text-sm text-amber-800 dark:text-amber-300 whitespace-pre-wrap">{{ $exam->instructions }}</div>
        </div>
    @endif

    {{-- Important notices --}}
    <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-[2px] p-4 mb-6 text-sm text-blue-700 dark:text-blue-300 space-y-1">
        <p class="font-semibold">Before you begin:</p>
        <ul class="list-disc list-inside space-y-1 text-xs">
            <li>Ensure you have a stable internet connection.</li>
            <li>Do not close or refresh this tab once you start — your answers auto-save.</li>
            <li>Navigate through sections using the buttons at the bottom of each page.</li>
            <li>Submit once you have completed all sections.</li>
        </ul>
    </div>

    {{-- Start button --}}
    <div class="text-center">
        <a href="{{ route('mock-exams.take.section', [$exam, 0]) }}"
           class="inline-flex items-center gap-2 px-8 py-3 bg-gradient-to-r from-violet-600 to-violet-500 hover:from-violet-700 hover:to-violet-600 text-white font-semibold rounded-[2px] text-sm shadow-[0_2px_10px_rgba(124,58,237,0.3)] transition-all">
            Start Examination
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"/>
            </svg>
        </a>
    </div>

</div>
</body>
</html>
