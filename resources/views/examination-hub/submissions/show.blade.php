<x-layouts.app>
    {{-- ═══════════════════════════════════════════════════════════
         PAGE SHELL
    ═══════════════════════════════════════════════════════════ --}}
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-7"
         style="font-family: 'system-ui', -apple-system, sans-serif;">

        {{-- ── PAGE HEADER ── --}}
        <div class="overflow-hidden"
             style="border-radius: 2px; background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); box-shadow: 0 4px 24px rgba(0,0,0,0.15);">
            <div class="h-1 w-full" style="background: linear-gradient(90deg, #0369a1, #38bdf8, #7dd3fc);"></div>
            <div class="px-7 py-6">
                <a href="{{ route('examination-hub.submissions.index', $exam) }}" 
                   class="inline-flex items-center gap-1.5 text-xs font-medium text-slate-400 hover:text-amber-400 transition-colors mb-3">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                    Back to Submissions
                </a>
                <h1 class="text-2xl font-bold text-white leading-snug" style="letter-spacing: -0.02em; font-family: 'Georgia', serif;">
                    Submission Details
                </h1>
                <p class="text-slate-400 mt-2 text-sm">{{ $exam->title }}</p>
            </div>
        </div>

        {{-- ── PARTICIPANT & PERFORMANCE GRID ── --}}
        <div class="grid md:grid-cols-2 gap-6">
            {{-- Participant Information --}}
            <div class="bg-white dark:bg-slate-900 overflow-hidden"
                 style="border-radius: 2px; border: 1px solid rgba(0,0,0,0.06); box-shadow: 0 1px 6px rgba(0,0,0,0.04);">
                <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-800 flex items-center gap-2">
                    <div class="w-1 h-5" style="background: linear-gradient(180deg, #2563eb, #60a5fa); border-radius: 1px;"></div>
                    <h2 class="font-bold text-slate-900 dark:text-white text-xs uppercase tracking-wider" style="letter-spacing: 0.1em;">Participant Information</h2>
                </div>
                <div class="p-5 space-y-4">
                    <div>
                        <p class="text-xs text-slate-500 dark:text-slate-400 uppercase tracking-wider" style="letter-spacing: 0.08em;">Name</p>
                        <p class="text-base font-semibold text-slate-900 dark:text-white mt-1">{{ $submission->participant_name ?? $submission->getParticipantName() }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-slate-500 dark:text-slate-400 uppercase tracking-wider" style="letter-spacing: 0.08em;">Email</p>
                        <p class="text-base text-slate-700 dark:text-slate-300 mt-1">{{ $submission->participant_email ?? $submission->getParticipantEmail() }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-slate-500 dark:text-slate-400 uppercase tracking-wider" style="letter-spacing: 0.08em;">Participant Type</p>
                        <p class="text-base text-slate-700 dark:text-slate-300 mt-1">{{ ucfirst($submission->participant_type) }}</p>
                    </div>
                </div>
            </div>

            {{-- Performance Summary --}}
            <div class="bg-white dark:bg-slate-900 overflow-hidden"
                 style="border-radius: 2px; border: 1px solid rgba(0,0,0,0.06); box-shadow: 0 1px 6px rgba(0,0,0,0.04);">
                <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-800 flex items-center gap-2">
                    <div class="w-1 h-5" style="background: linear-gradient(180deg, #7c3aed, #a78bfa); border-radius: 1px;"></div>
                    <h2 class="font-bold text-slate-900 dark:text-white text-xs uppercase tracking-wider" style="letter-spacing: 0.1em;">Performance Summary</h2>
                </div>
                <div class="p-5 space-y-4">
                    <div>
                        <p class="text-xs text-slate-500 dark:text-slate-400 uppercase tracking-wider" style="letter-spacing: 0.08em;">Score</p>
                        <p class="text-2xl font-bold text-slate-900 dark:text-white mt-1" style="letter-spacing: -0.04em;">{{ $submission->score ?? 0 }}<span class="text-lg font-medium text-slate-500">/{{ $submission->total_marks ?? 0 }}</span></p>
                    </div>
                    <div>
                        <p class="text-xs text-slate-500 dark:text-slate-400 uppercase tracking-wider" style="letter-spacing: 0.08em;">Percentage</p>
                        <p class="text-2xl font-bold text-amber-600 dark:text-amber-400 mt-1" style="letter-spacing: -0.04em;">{{ number_format($submission->percentage ?? 0, 1) }}%</p>
                    </div>
                    <div>
                        <p class="text-xs text-slate-500 dark:text-slate-400 uppercase tracking-wider" style="letter-spacing: 0.08em;">Grade</p>
                        @php
                            $grade = $submission->grade ?? 'N/A';
                            $gradeStyle = in_array($grade, ['A+', 'A'])
                                ? 'color:#065f46;background:#ecfdf5;border-color:#a7f3d0;'
                                : (in_array($grade, ['B', 'C'])
                                    ? 'color:#1d4ed8;background:#eff6ff;border-color:#bfdbfe;'
                                    : (in_array($grade, ['D', 'F'])
                                        ? 'color:#991b1b;background:#fef2f2;border-color:#fecaca;'
                                        : 'color:#475569;background:#f1f5f9;border-color:#e2e8f0;'));
                        @endphp
                        <span class="inline-flex items-center justify-center text-lg font-semibold px-4 py-1.5 border mt-1"
                              style="border-radius: 2px; {{ $gradeStyle }}">
                            {{ $grade }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        {{-- ── METRICS STRIP ── --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            {{-- Time Taken --}}
            <div class="bg-white dark:bg-slate-900 px-5 py-5 flex items-center gap-4"
                 style="border-radius: 2px; border: 1px solid rgba(0,0,0,0.06); box-shadow: 0 1px 6px rgba(0,0,0,0.04);">
                <div class="w-11 h-11 flex-shrink-0 flex items-center justify-center"
                     style="border-radius: 2px; background: linear-gradient(135deg, #1d4ed8, #3b82f6);">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-xs text-slate-500 dark:text-slate-400 uppercase tracking-wider" style="font-size: 10px; letter-spacing: 0.1em;">Time Taken</p>
                    <p class="text-2xl font-bold text-slate-900 dark:text-white mt-0.5" style="letter-spacing: -0.04em;">{{ $submission->time_taken_minutes ?? 0 }}<span class="text-base font-medium text-slate-500"> min</span></p>
                </div>
            </div>

            {{-- Status --}}
            <div class="bg-white dark:bg-slate-900 px-5 py-5 flex items-center gap-4"
                 style="border-radius: 2px; border: 1px solid rgba(0,0,0,0.06); box-shadow: 0 1px 6px rgba(0,0,0,0.04);">
                <div class="w-11 h-11 flex-shrink-0 flex items-center justify-center"
                     style="border-radius: 2px; background: linear-gradient(135deg, #b45309, #d97706);">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-xs text-slate-500 dark:text-slate-400 uppercase tracking-wider" style="font-size: 10px; letter-spacing: 0.1em;">Status</p>
                    @php
                        $status = $submission->status ?? 'unknown';
                        $statusStyle = $status === 'completed'
                            ? 'color:#065f46;background:#ecfdf5;border-color:#a7f3d0;'
                            : ($status === 'in_progress'
                                ? 'color:#92400e;background:#fffbeb;border-color:#fde68a;'
                                : 'color:#475569;background:#f1f5f9;border-color:#e2e8f0;');
                    @endphp
                    <span class="inline-flex items-center justify-center text-lg font-semibold px-3 py-1 border mt-0.5"
                          style="border-radius: 2px; {{ $statusStyle }}">
                        {{ ucfirst($status) }}
                    </span>
                </div>
            </div>

            {{-- Submitted At --}}
            <div class="bg-white dark:bg-slate-900 px-5 py-5 flex items-center gap-4"
                 style="border-radius: 2px; border: 1px solid rgba(0,0,0,0.06); box-shadow: 0 1px 6px rgba(0,0,0,0.04);">
                <div class="w-11 h-11 flex-shrink-0 flex items-center justify-center"
                     style="border-radius: 2px; background: linear-gradient(135deg, #065f46, #059669);">
                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
                <div>
                    <p class="text-xs text-slate-500 dark:text-slate-400 uppercase tracking-wider" style="font-size: 10px; letter-spacing: 0.1em;">Submitted At</p>
                    <p class="text-lg font-semibold text-slate-900 dark:text-white mt-0.5">{{ optional($submission->submitted_at)?->format('M d, H:i') ?? 'N/A' }}</p>
                </div>
            </div>
        </div>

        {{-- ── DETAILED RESPONSES ── --}}
        @php
            $exam->load('questions.section');
            $responses = $submission->responses ?? [];
            $questionsBySection = $exam->questions->groupBy(fn($q) => $q->section?->title ?? 'Unsectioned');
        @endphp

        <div class="bg-white dark:bg-slate-900 overflow-hidden"
             style="border-radius: 2px; border: 1px solid rgba(0,0,0,0.06); box-shadow: 0 1px 6px rgba(0,0,0,0.04);">
            <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-800 flex items-center gap-2">
                <div class="w-1 h-5" style="background: linear-gradient(180deg, #7c3aed, #a78bfa); border-radius: 1px;"></div>
                <h2 class="font-bold text-slate-900 dark:text-white text-xs uppercase tracking-wider" style="letter-spacing: 0.1em;">Detailed Responses</h2>
            </div>
            
            <div class="p-5 space-y-8">
                @foreach($questionsBySection as $sectionTitle => $questions)
                    <div>
                        <h3 class="font-bold text-slate-900 dark:text-white text-sm mb-4 pb-2 border-b border-slate-100 dark:border-slate-800" style="letter-spacing: 0.05em;">
                            {{ $sectionTitle }}
                        </h3>
                        
                        <div class="space-y-5">
                            @foreach($questions as $index => $question)
                                @php
                                    $response = $responses[$question->id] ?? null;
                                    $isCorrect = $response['is_correct'] ?? null;
                                    $studentAnswer = $response['response'] ?? null;
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
                                            <p class="font-semibold text-slate-900 dark:text-white">Question {{ $loop->iteration }}</p>
                                            <div class="text-slate-700 dark:text-slate-300 mt-2 text-sm">
                                                <x-form.markdown-with-math :content="$question->getFormattedQuestion()" class="prose dark:prose-invert max-w-none" style="font-size:0.875rem;"/>
                                            </div>
                                        </div>
                                        <div class="flex items-center gap-2 flex-shrink-0">
                                            @if($isCorrect === true)
                                                <span class="inline-flex items-center px-2.5 py-1 text-xs font-semibold border"
                                                      style="border-radius: 2px; color:#065f46;background:#ecfdf5;border-color:#a7f3d0;">
                                                    ✓ Correct
                                                </span>
                                            @elseif($isCorrect === false)
                                                <span class="inline-flex items-center px-2.5 py-1 text-xs font-semibold border"
                                                      style="border-radius: 2px; color:#991b1b;background:#fef2f2;border-color:#fecaca;">
                                                    ✗ Incorrect
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2.5 py-1 text-xs font-semibold border"
                                                      style="border-radius: 2px; color:#475569;background:#f1f5f9;border-color:#e2e8f0;">
                                                    Pending Review
                                                </span>
                                            @endif
                                            <span class="text-sm font-mono font-semibold text-slate-700 dark:text-slate-300 px-2 py-1"
                                                  style="border-radius: 2px; background:rgba(0,0,0,0.04);">
                                                {{ $pointsEarned }}/{{ $question->marks }}
                                            </span>
                                        </div>
                                    </div>

                                    {{-- Multiple Choice / TrueFalse Options --}}
                                    @if($question->isMultipleChoice() || $question->isTrueFalse())
                                        <div class="mt-4 space-y-2">
                                            @if($question->isMultipleChoice())
                                                @foreach($question->getOptionsForDisplay() as $optionKey => $optionText)
                                                    @php
                                                        $isStudentAnswer = $studentAnswer === $optionKey;
                                                        $isCorrectAnswer = $question->correct_answer === $optionKey;
                                                        $optBg = $isStudentAnswer && $isCorrectAnswer 
                                                            ? 'background:#ecfdf5;border-color:#a7f3d0;' 
                                                            : ($isStudentAnswer && !$isCorrectAnswer 
                                                                ? 'background:#fef2f2;border-color:#fecaca;' 
                                                                : (!$isStudentAnswer && $isCorrectAnswer 
                                                                    ? 'background:#eff6ff;border-color:#bfdbfe;' 
                                                                    : 'background:linear-gradient(135deg,#f8fafc,#f1f5f9);border-color:rgba(0,0,0,0.06);'));
                                                    @endphp
                                                    <div class="flex items-center gap-2 p-2.5 border text-sm" style="border-radius: 2px; {{ $optBg }}">
                                                        <span class="font-medium text-slate-700 dark:text-slate-300">{{ $optionKey }}.</span>
                                                        <x-form.markdown-with-math :content="$optionText" class="inline text-slate-700 dark:text-slate-300" style="font-size:0.875rem;"/>
                                                        @if($isStudentAnswer)
                                                            <span class="ml-auto text-xs text-slate-500 dark:text-slate-400">(Your answer)</span>
                                                        @endif
                                                        @if($isCorrectAnswer)
                                                            <span class="ml-auto text-xs font-medium" style="color:#065f46;">✓ Correct</span>
                                                        @endif
                                                    </div>
                                                @endforeach
                                            @else
                                                @php
                                                    $trueCorrect = $question->correct_answer === 'True' || $question->correct_answer === '1';
                                                    $falseCorrect = !$trueCorrect;
                                                @endphp
                                                <div class="flex items-center gap-2 p-2.5 border text-sm" 
                                                     style="border-radius: 2px; {{ $studentAnswer === 'True' ? 'background:#eff6ff;border-color:#bfdbfe;' : 'background:linear-gradient(135deg,#f8fafc,#f1f5f9);border-color:rgba(0,0,0,0.06);' }}">
                                                    <span class="font-medium text-slate-700 dark:text-slate-300">True</span>
                                                    @if($studentAnswer === 'True')
                                                        <span class="ml-auto text-xs text-slate-500 dark:text-slate-400">(Your answer)</span>
                                                    @endif
                                                    @if($trueCorrect)
                                                        <span class="ml-auto text-xs font-medium" style="color:#065f46;">✓ Correct</span>
                                                    @endif
                                                </div>
                                                <div class="flex items-center gap-2 p-2.5 border text-sm" 
                                                     style="border-radius: 2px; {{ $studentAnswer === 'False' ? 'background:#eff6ff;border-color:#bfdbfe;' : 'background:linear-gradient(135deg,#f8fafc,#f1f5f9);border-color:rgba(0,0,0,0.06);' }}">
                                                    <span class="font-medium text-slate-700 dark:text-slate-300">False</span>
                                                    @if($studentAnswer === 'False')
                                                        <span class="ml-auto text-xs text-slate-500 dark:text-slate-400">(Your answer)</span>
                                                    @endif
                                                    @if($falseCorrect)
                                                        <span class="ml-auto text-xs font-medium" style="color:#065f46;">✓ Correct</span>
                                                    @endif
                                                </div>
                                            @endif
                                        </div>
                                    @else
                                        {{-- Text/Essay Answer --}}
                                        <div class="mt-4">
                                            <p class="text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2" style="letter-spacing: 0.08em;">Student Answer</p>
                                            <div class="p-3 border text-sm text-slate-700 dark:text-slate-300 whitespace-pre-wrap"
                                                 style="border-radius: 2px; border-color:rgba(0,0,0,0.06); background:linear-gradient(135deg,#f8fafc,#f1f5f9);">
                                                {{ $studentAnswer ?? 'No answer provided' }}
                                            </div>
                                        </div>
                                    @endif

                                    {{-- Feedback --}}
                                    @if(!empty($response['feedback']))
                                        <div class="mt-4 p-3 border text-sm"
                                             style="border-radius: 2px; color:#1d4ed8;background:#eff6ff;border-color:#bfdbfe;">
                                            <p class="font-semibold">Feedback:</p>
                                            <p class="mt-1 text-slate-700 dark:text-slate-300">{{ $response['feedback'] }}</p>
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

    </div>{{-- /container --}}
</x-layouts.app>