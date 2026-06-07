<x-layouts.app >
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-7 font-sans">

        {{-- ── PAGE HEADER ── --}}
        <div class="overflow-hidden rounded-[2px] bg-gradient-to-br from-slate-900 to-slate-800 shadow-xl">
            <div class="h-1 w-full bg-gradient-to-r from-violet-600 via-violet-400 to-indigo-300"></div>
            <div class="px-7 py-6 flex items-start justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-bold text-white leading-snug tracking-tight font-serif">
                        Submission – {{ $submission->participant_name }}
                    </h1>
                    <p class="text-slate-400 mt-2 text-sm">{{ $submission->participant_email }}</p>
                </div>
                <div class="flex items-center gap-2 mt-1">
                    <x-ui.button href="{{ route('mock-exams.results.index', $mockExam) }}" variant="ghost" size="sm" icon="arrow-left">
                        Results
                    </x-ui.button>
                </div>
            </div>
        </div>

        {{-- ── FLASH MESSAGES ── --}}
        @if(session('success'))
            <x-ui.card variant="accent" accent="success" shadow="true">
                <div class="px-5 py-3 flex items-center gap-2">
                    <x-heroicon-o-check-circle class="w-4 h-4 text-emerald-500 shrink-0"/>
                    <p class="text-sm text-emerald-800 dark:text-emerald-200">{!! session('success') !!}</p>
                </div>
            </x-ui.card>
        @endif

        {{-- ── SCORE SUMMARY ── --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            @foreach([
                ['label' => 'Score',  'value' => number_format($submission->score ?? 0, 1) . ' / ' . number_format($submission->total_marks ?? 0, 1), 'color' => 'violet'],
                ['label' => '%',      'value' => number_format($submission->percentage ?? 0, 1) . '%', 'color' => 'blue'],
                ['label' => 'Grade',  'value' => $submission->grade ?? '—', 'color' => 'emerald'],
                ['label' => 'Status', 'value' => str_replace('_', ' ', ucfirst($submission->status)), 'color' => 'slate'],
            ] as $stat)
                <x-ui.card variant="default" shadow="true">
                    <div class="p-4 text-center">
                        <p class="text-xl font-bold text-{{ $stat['color'] }}-600 dark:text-{{ $stat['color'] }}-400">
                            {{ $stat['value'] }}
                        </p>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5 uppercase tracking-widest" style="font-size:10px; letter-spacing:0.1em;">
                            {{ $stat['label'] }}
                        </p>
                    </div>
                </x-ui.card>
            @endforeach
        </div>

        {{-- ── DETERMINE WHETHER GRADING INPUTS ARE SHOWN ── --}}
        @php
            // Show grading UI if:
            //   - submission is not finalised, AND
            //   - the exam contains at least one essay question
            // We do NOT require requires_manual_review === true, because that flag
            // may be false even when essays are present (e.g. keyword threshold met).
            $canGrade = $submission->status !== 'final';

            $hasEssayQuestions = $mockExam->subjectExams
                ->flatMap(fn($se) => $se->sections)
                ->flatMap(fn($s) => $s->questions)
                ->contains(fn($q) => $q->isEssay());

            $showGradingForm = $canGrade && $hasEssayQuestions;
        @endphp

        {{-- ── QUESTIONS WITH RESPONSES ── --}}
        <form method="POST"
              action="{{ route('mock-exams.results.grade', [$mockExam, $submission]) }}"
              id="grading-form">
            @csrf

            @php $responses = $submission->responses ?? []; @endphp

            @foreach($mockExam->subjectExams as $se)
                <x-ui.card variant="default" shadow="true" class="mb-4">
                    <x-ui.card-header :title="$se->getDisplayTitle()" accent="info" />

                    @foreach($se->sections as $section)
                        <div class="px-5 pt-4 pb-2">
                            <p class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-widest mb-3"
                               style="font-size:10px; letter-spacing:0.1em;">
                                {{ $section->title }} — {{ ucfirst(str_replace('_', ' ', $section->question_type)) }}
                            </p>

                            @foreach($section->questions as $q)
                                @php
                                    $resp           = $responses[$q->id] ?? null;
                                    $participantAnswer = $resp['response'] ?? null;
                                    $isCorrect      = $resp['is_correct'] ?? null;
                                    $pointsEarned   = $resp['points_earned'] ?? 0;
                                @endphp

                                <div class="mb-4 p-4 rounded-[2px] border
                                    {{ $isCorrect === true
                                        ? 'border-emerald-200 bg-emerald-50/50 dark:border-emerald-800 dark:bg-emerald-900/10'
                                        : ($isCorrect === false
                                            ? 'border-red-200 bg-red-50/50 dark:border-red-800 dark:bg-red-900/10'
                                            : 'border-slate-200 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800/20') }}">

                                    {{-- Question text --}}
                                    <div class="flex items-start justify-between gap-4">
                                        <p class="text-sm font-medium text-slate-800 dark:text-slate-200 flex-1">
                                            <span class="text-slate-400 dark:text-slate-500 mr-1">Q{{ $loop->iteration }}.</span>
                                            {!! nl2br(e($q->question_text)) !!}
                                        </p>
                                        <span class="text-xs text-slate-400 dark:text-slate-500 shrink-0">
                                            {{ $q->marks }} mk{{ $q->marks != 1 ? 's' : '' }}
                                        </span>
                                    </div>

                                    {{-- MCQ options with correct highlighted --}}
                                    @if($q->isMultipleChoice() && !empty($q->options))
                                        <div class="mt-2 grid grid-cols-2 gap-1">
                                            @foreach($q->getOptionsForDisplay() as $letter => $text)
                                                <p class="text-xs {{ $letter === $q->correct_answer ? 'text-emerald-700 dark:text-emerald-400 font-medium' : 'text-slate-500 dark:text-slate-400' }}">
                                                    <span class="font-mono mr-1">{{ $letter }}.</span>{{ $text }}
                                                    @if($letter === $q->correct_answer)
                                                        <x-heroicon-o-check class="inline w-3 h-3 text-emerald-500"/>
                                                    @endif
                                                </p>
                                            @endforeach
                                        </div>
                                    @endif

                                    @if($q->isTrueFalse())
                                        <p class="mt-1 text-xs text-emerald-700 dark:text-emerald-400 font-medium">
                                            Correct: {{ ucfirst($q->correct_answer ?? '') }}
                                        </p>
                                    @endif

                                    {{-- Participant's answer + current points --}}
                                    <div class="mt-3 pt-3 border-t border-slate-200 dark:border-slate-700 space-y-1">
                                        <p class="text-xs text-slate-500 dark:text-slate-400">
                                            <span class="font-medium text-slate-700 dark:text-slate-300">Answer:</span>
                                            {{ $participantAnswer ?? 'No answer provided' }}
                                        </p>
                                        <p class="text-xs text-slate-500 dark:text-slate-400">
                                            <span class="font-medium text-slate-700 dark:text-slate-300">Points earned:</span>
                                            {{ $pointsEarned }} / {{ $q->marks }}
                                        </p>
                                        @if(isset($resp['feedback']))
                                            <p class="text-xs text-amber-600 dark:text-amber-400">{{ $resp['feedback'] }}</p>
                                        @endif
                                        @if(isset($resp['manually_graded']) && $resp['manually_graded'])
                                            <p class="text-xs text-violet-600 dark:text-violet-400 font-medium">
                                                ✓ Manually graded
                                            </p>
                                        @endif
                                    </div>

                                    {{-- ── ESSAY MANUAL GRADING PANEL ── --}}
                                    @if($q->isEssay())
                                        @if($showGradingForm)
                                            {{-- Instructor can allocate marks --}}
                                            <div class="mt-3 pt-3 border-t border-violet-200 dark:border-violet-800 space-y-3">
                                                <p class="text-xs font-semibold text-violet-700 dark:text-violet-400 uppercase tracking-wider"
                                                   style="font-size:10px; letter-spacing:0.1em;">
                                                    Manual Grading
                                                </p>

                                                @if($q->answer_explanation)
                                                    <div class="p-3 bg-slate-50 dark:bg-slate-800 rounded-[2px] border border-slate-200 dark:border-slate-700">
                                                        <p class="text-xs font-medium text-slate-500 uppercase tracking-wider mb-1" style="font-size:9px;">Model Answer</p>
                                                        <p class="text-xs text-slate-600 dark:text-slate-300">{{ Str::limit($q->answer_explanation, 300) }}</p>
                                                    </div>
                                                @endif

                                                @if(!empty($q->answer_keywords))
                                                    <p class="text-xs text-blue-600 dark:text-blue-400 italic">
                                                        Key terms: {{ implode(' · ', $q->answer_keywords) }}
                                                    </p>
                                                @endif

                                                <div class="flex flex-wrap items-center gap-4">
                                                    <div class="flex items-center gap-2">
                                                        <label class="text-xs font-medium text-slate-600 dark:text-slate-400 whitespace-nowrap">
                                                            Points (max {{ $q->marks }}):
                                                        </label>
                                                        <input type="number"
                                                               name="grades[{{ $q->id }}][points]"
                                                               value="{{ old('grades.' . $q->id . '.points', $pointsEarned) }}"
                                                               min="0"
                                                               max="{{ $q->marks }}"
                                                               step="0.5"
                                                               class="w-24 px-3 py-1.5 text-sm border border-slate-300 dark:border-slate-600 rounded-[2px] focus:outline-none focus:ring-2 focus:ring-violet-500/20 focus:border-violet-500 dark:bg-slate-800 dark:text-white transition-all">
                                                    </div>
                                                    <div class="flex items-center gap-2 flex-1 min-w-[200px]">
                                                        <label class="text-xs font-medium text-slate-600 dark:text-slate-400 whitespace-nowrap">
                                                            Feedback:
                                                        </label>
                                                        <input type="text"
                                                               name="grades[{{ $q->id }}][feedback]"
                                                               value="{{ old('grades.' . $q->id . '.feedback', $resp['manual_feedback'] ?? '') }}"
                                                               placeholder="Optional feedback for the participant"
                                                               class="flex-1 px-3 py-1.5 text-sm border border-slate-300 dark:border-slate-600 rounded-[2px] focus:outline-none focus:ring-2 focus:ring-violet-500/20 focus:border-violet-500 dark:bg-slate-800 dark:text-white transition-all">
                                                    </div>
                                                </div>
                                            </div>
                                        @else
                                            {{-- Submission is finalised – show read-only feedback --}}
                                            @if($q->answer_explanation)
                                                <div class="mt-3 pt-3 border-t border-slate-200 dark:border-slate-700">
                                                    <p class="text-xs font-medium text-slate-500 uppercase tracking-wider mb-1" style="font-size:9px;">Model Answer</p>
                                                    <p class="text-xs text-slate-600 dark:text-slate-300">{{ $q->answer_explanation }}</p>
                                                </div>
                                            @endif
                                            @if(isset($resp['manual_feedback']))
                                                <p class="mt-2 text-xs text-slate-500 dark:text-slate-400 italic">
                                                    Feedback: {{ $resp['manual_feedback'] }}
                                                </p>
                                            @endif
                                        @endif
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @endforeach
                </x-ui.card>
            @endforeach

            {{-- ── BOTTOM SAVE ROW (always shown when grading is possible) ── --}}
            @if($showGradingForm)
                <div class="flex items-center justify-between gap-4 mt-2">
                    <x-ui.button type="submit" variant="primary" size="md" icon="check">
                        Save Grades
                    </x-ui.button>

                    @if($submission->status !== 'final')
                        <form method="POST"
                              action="{{ route('mock-exams.results.finalize', [$mockExam, $submission]) }}"
                              onsubmit="return confirm('Finalise grading? This locks the submission and cannot be undone.')">
                            @csrf
                            <x-ui.button type="submit" variant="success" size="md" icon="check-badge">
                                Save &amp; Finalise
                            </x-ui.button>
                        </form>
                    @endif
                </div>
            @endif
        </form>

        {{-- ── FINALISE-ONLY BUTTON (no essays, but review still flagged) ── --}}
        @if(!$showGradingForm && $submission->requires_manual_review && $submission->status !== 'final')
            <form method="POST"
                  action="{{ route('mock-exams.results.finalize', [$mockExam, $submission]) }}"
                  onsubmit="return confirm('Finalise grading?')">
                @csrf
                <x-ui.button type="submit" variant="success" size="md" icon="check-badge">
                    Finalise Grading
                </x-ui.button>
            </form>
        @endif

        {{-- ── TEACHER FEEDBACK (read-only, shown after finalisation) ── --}}
        @if($submission->teacher_feedback)
            <x-ui.card variant="accent" accent="info" shadow="true">
                <x-ui.card-header title="Instructor Feedback" accent="info" />
                <div class="p-5 text-sm text-slate-700 dark:text-slate-300">
                    {{ $submission->teacher_feedback }}
                </div>
            </x-ui.card>
        @endif

    </div>

    {{-- ── STICKY SAVE BAR (appears when grading form is present) ── --}}
    @if($showGradingForm)
        <div class="fixed bottom-0 left-0 right-0 z-40 border-t border-slate-200 dark:border-slate-700 bg-white/95 dark:bg-slate-900/95 backdrop-blur-sm shadow-lg">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-3 flex items-center justify-between gap-4">
                <p class="text-sm text-slate-500 dark:text-slate-400">
                    Grading <strong class="text-slate-800 dark:text-slate-200">{{ $submission->participant_name }}</strong>
                    — fill in points above then save.
                </p>
                <div class="flex items-center gap-2">
                    <button type="submit"
                            form="grading-form"
                            class="inline-flex items-center gap-2 px-5 py-2 text-sm font-semibold text-white transition-all"
                            style="border-radius: 2px; background: linear-gradient(135deg, #7c3aed, #a78bfa); box-shadow: 0 2px 10px rgba(124,58,237,0.3);">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Save Grades
                    </button>
                </div>
            </div>
        </div>
        {{-- Spacer so sticky bar doesn't cover content --}}
        <div class="h-16"></div>
    @endif

</x-layouts.app>