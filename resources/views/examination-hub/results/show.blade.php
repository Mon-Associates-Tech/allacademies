<x-layouts.exam>
    {{-- ═══════════════════════════════════════════════════════════
         PAGE SHELL
    ═══════════════════════════════════════════════════════════ --}}
    <div class="min-h-screen py-8"
         style="font-family: 'system-ui', -apple-system, sans-serif; background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);">

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-7">

            {{-- ── PAGE HEADER ── --}}
            <div class="overflow-hidden"
                 style="border-radius: 2px; background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); box-shadow: 0 4px 24px rgba(0,0,0,0.15);">
                <div class="h-1 w-full" style="background: linear-gradient(90deg, #065f46, #059669, #10b981);"></div>
                <div class="px-7 py-6">
                    <a href="{{ route('examination-hub.results.index', ['email' => $email]) }}" 
                       class="inline-flex items-center gap-1.5 text-xs font-medium text-slate-400 hover:text-amber-400 transition-colors mb-3">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                        </svg>
                        Back to Results
                    </a>
                    <h1 class="text-2xl font-bold text-white leading-snug" style="letter-spacing: -0.02em; font-family: 'Georgia', serif;">
                        {{ $submission->assignment->title }}
                    </h1>
                    <p class="text-slate-400 mt-2 text-sm">
                        Submitted on {{ $submission->submitted_at?->format('F d, Y \a\t H:i') }}
                    </p>
                </div>
            </div>

            {{-- ── SCORE SUMMARY ── --}}
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                {{-- Score --}}
                <div class="bg-white dark:bg-slate-900 px-5 py-5 flex flex-col items-center justify-center text-center"
                     style="border-radius: 2px; border: 1px solid rgba(0,0,0,0.06); box-shadow: 0 1px 6px rgba(0,0,0,0.04);">
                    <p class="text-xs text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1" style="font-size: 10px; letter-spacing: 0.1em;">Score</p>
                    <p class="text-3xl font-bold text-slate-900 dark:text-white" style="letter-spacing: -0.04em;">{{ $submission->score }}<span class="text-lg font-medium text-slate-500">/{{ $submission->total_marks }}</span></p>
                </div>

                {{-- Percentage --}}
                <div class="bg-white dark:bg-slate-900 px-5 py-5 flex flex-col items-center justify-center text-center"
                     style="border-radius: 2px; border: 1px solid rgba(0,0,0,0.06); box-shadow: 0 1px 6px rgba(0,0,0,0.04);">
                    <p class="text-xs text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1" style="font-size: 10px; letter-spacing: 0.1em;">Percentage</p>
                    <p class="text-3xl font-bold text-slate-900 dark:text-white" style="letter-spacing: -0.04em;">{{ number_format($submission->percentage, 1) }}<span class="text-lg font-medium text-slate-500">%</span></p>
                </div>

                {{-- Grade --}}
                <div class="bg-white dark:bg-slate-900 px-5 py-5 flex flex-col items-center justify-center text-center"
                     style="border-radius: 2px; border: 1px solid rgba(0,0,0,0.06); box-shadow: 0 1px 6px rgba(0,0,0,0.04);">
                    <p class="text-xs text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1" style="font-size: 10px; letter-spacing: 0.1em;">Grade</p>
                    @php
                        $grade = $submission->grade;
                        $gradeColor = in_array($grade, ['A+', 'A'])
                            ? 'text-emerald-600 dark:text-emerald-400'
                            : ($grade === 'B'
                                ? 'text-blue-600 dark:text-blue-400'
                                : ($grade === 'C'
                                    ? 'text-amber-600 dark:text-amber-400'
                                    : 'text-red-600 dark:text-red-400'));
                    @endphp
                    <p class="text-3xl font-bold {{ $gradeColor }}" style="letter-spacing: -0.04em;">{{ $grade }}</p>
                </div>

                {{-- Time Taken --}}
                <div class="bg-white dark:bg-slate-900 px-5 py-5 flex flex-col items-center justify-center text-center"
                     style="border-radius: 2px; border: 1px solid rgba(0,0,0,0.06); box-shadow: 0 1px 6px rgba(0,0,0,0.04);">
                    <p class="text-xs text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1" style="font-size: 10px; letter-spacing: 0.1em;">Time Taken</p>
                    <p class="text-3xl font-bold text-slate-900 dark:text-white" style="letter-spacing: -0.04em;">{{ $submission->time_taken_minutes ?? 0 }}<span class="text-lg font-medium text-slate-500"> min</span></p>
                </div>
            </div>

            {{-- ── QUESTIONS & ANSWERS ── --}}
            <div class="bg-white dark:bg-slate-900 overflow-hidden"
                 style="border-radius: 2px; border: 1px solid rgba(0,0,0,0.06); box-shadow: 0 1px 6px rgba(0,0,0,0.04);">
                <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-800 flex items-center gap-2">
                    <div class="w-1 h-5" style="background: linear-gradient(180deg, #7c3aed, #a78bfa); border-radius: 1px;"></div>
                    <h2 class="font-bold text-slate-900 dark:text-white text-xs uppercase tracking-wider" style="letter-spacing: 0.1em;">Questions & Answers</h2>
                </div>

                <div class="p-5 space-y-6">
                    @php
                        $questionNumber = 1;
                        $responses = $submission->responses ?? [];
                    @endphp

                    @foreach($submission->assignment->sections as $section)
                        <div>
                            <h3 class="font-bold text-slate-900 dark:text-white text-sm mb-4 pb-2 border-b border-slate-100 dark:border-slate-800" style="letter-spacing: 0.05em;">
                                {{ $section->title }}
                            </h3>

                            @foreach($section->questions as $question)
                                @php
                                    $response = $responses[$question->id] ?? null;
                                    $isCorrect = $response['is_correct'] ?? null;
                                    $pointsEarned = $response['points_earned'] ?? 0;
                                    $cardBg = $isCorrect === true 
                                        ? 'background:#ecfdf5;border-color:#a7f3d0;' 
                                        : ($isCorrect === false 
                                            ? 'background:#fef2f2;border-color:#fecaca;' 
                                            : 'background:linear-gradient(135deg,#f8fafc,#f1f5f9);border-color:rgba(0,0,0,0.06);');
                                @endphp

                                <div class="p-4 border" style="border-radius: 2px; {{ $cardBg }}">
                                    <div class="flex items-start justify-between gap-3">
                                        <div class="flex-1 min-w-0">
                                            <p class="font-semibold text-slate-900 dark:text-white">Question {{ $questionNumber++ }} <span class="font-normal text-slate-500">({{ $question->marks }} marks)</span></p>
                                            <div class="text-slate-700 dark:text-slate-300 mt-2 text-sm">
                                                <x-form.markdown-with-math :content="$question->question_text" class="prose dark:prose-invert max-w-none" style="font-size:0.875rem;"/>
                                            </div>
                                        </div>
                                        <div class="flex-shrink-0 text-right">
                                            @php
                                                $scoreColor = $isCorrect === true 
                                                    ? 'text-emerald-600 dark:text-emerald-400' 
                                                    : ($isCorrect === false 
                                                        ? 'text-red-600 dark:text-red-400' 
                                                        : 'text-slate-600 dark:text-slate-400');
                                            @endphp
                                            <span class="text-sm font-mono font-semibold {{ $scoreColor }}">
                                                {{ $pointsEarned }}/{{ $question->marks }}
                                            </span>
                                        </div>
                                    </div>

                                    {{-- Multiple Choice --}}
                                    @if($question->question_type === 'multiple_choice')
                                        <div class="mt-4 space-y-2">
                                            @foreach($question->options as $option)
                                                @php
                                                    $isSelected = ($response['response'] ?? null) === $option;
                                                    $isCorrectOption = $question->correct_answer === $option;
                                                    $optBg = $isCorrectOption 
                                                        ? 'background:#ecfdf5;border-color:#a7f3d0;' 
                                                        : ($isSelected && !$isCorrectOption 
                                                            ? 'background:#fef2f2;border-color:#fecaca;' 
                                                            : 'background:linear-gradient(135deg,#f8fafc,#f1f5f9);border-color:rgba(0,0,0,0.06);');
                                                @endphp
                                                <div class="flex items-center gap-2 p-2.5 border text-sm" style="border-radius: 2px; {{ $optBg }}">
                                                    <span class="flex-shrink-0">
                                                        @if($isSelected)
                                                            @if($isCorrectOption)
                                                                <svg class="w-5 h-5 text-emerald-600" fill="currentColor" viewBox="0 0 20 20">
                                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                                                </svg>
                                                            @else
                                                                <svg class="w-5 h-5 text-red-600" fill="currentColor" viewBox="0 0 20 20">
                                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                                                </svg>
                                                            @endif
                                                        @elseif($isCorrectOption)
                                                            <svg class="w-5 h-5 text-emerald-600" fill="currentColor" viewBox="0 0 20 20">
                                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                                            </svg>
                                                        @else
                                                            <span class="inline-block w-5 h-5"></span>
                                                        @endif
                                                    </span>
                                                    <x-form.markdown-with-math :content="$option" class="text-slate-700 dark:text-slate-300" style="font-size:0.875rem;"/>
                                                </div>
                                            @endforeach
                                        </div>

                                    {{-- True/False --}}
                                    @elseif($question->question_type === 'true_false')
                                        <div class="mt-4 space-y-2">
                                            <div class="p-2.5 border text-sm" style="border-radius: 2px; border-color:rgba(0,0,0,0.06); background:linear-gradient(135deg,#f8fafc,#f1f5f9);">
                                                <p class="text-slate-600 dark:text-slate-400">Your answer: 
                                                    <span class="font-semibold {{ $isCorrect === true ? 'text-emerald-600 dark:text-emerald-400' : 'text-red-600 dark:text-red-400' }}">
                                                        {{ $response['response'] ?? 'Not answered' }}
                                                    </span>
                                                </p>
                                            </div>
                                            @if($isCorrect === false)
                                                <div class="p-2.5 border text-sm" style="border-radius: 2px; border-color:#a7f3d0; background:#ecfdf5;">
                                                    <p class="text-emerald-700 dark:text-emerald-300">Correct answer: 
                                                        <span class="font-semibold">{{ $question->correct_answer }}</span>
                                                    </p>
                                                </div>
                                            @endif
                                        </div>

                                    {{-- Essay --}}
                                    @elseif($question->question_type === 'essay')
                                        <div class="mt-4">
                                            <p class="text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2" style="letter-spacing: 0.08em;">Your Answer</p>
                                            <div class="p-3 border text-sm text-slate-700 dark:text-slate-300 whitespace-pre-wrap"
                                                 style="border-radius: 2px; border-color:rgba(0,0,0,0.06); background:linear-gradient(135deg,#f8fafc,#f1f5f9);">
                                                {{ $response['response'] ?? 'Not answered' }}
                                            </div>
                                        </div>
                                    @endif

                                    {{-- Explanation --}}
                                    @if($question->explanation && $isCorrect === false)
                                        <div class="mt-4 p-3 border text-sm"
                                             style="border-radius: 2px; color:#1d4ed8;background:#eff6ff;border-color:#bfdbfe;">
                                            <p class="font-semibold">Explanation:</p>
                                            <x-form.markdown-with-math :content="$question->explanation" class="mt-1 text-slate-700 dark:text-slate-300" style="font-size:0.875rem;"/>
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @endforeach
                </div>
            </div>

        </div>{{-- /container --}}
    </div>
</x-layouts.exam>