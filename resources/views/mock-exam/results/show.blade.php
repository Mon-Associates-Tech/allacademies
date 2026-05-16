<x-layouts.app :title="'Submission – ' . $submission->participant_name">

    <div class="flex items-center gap-3 mb-6">
        <x-ui.button href="{{ route('mock-exams.results.index', $mockExam) }}" variant="ghost" size="sm" icon="arrow-left">Results</x-ui.button>
        <div>
            <h1 class="text-xl font-bold text-slate-900 dark:text-white">{{ $submission->participant_name }}</h1>
            <p class="text-sm text-slate-500 mt-0.5">{{ $submission->participant_email }}</p>
        </div>
        <div class="ml-auto flex items-center gap-2">
            @if($submission->requires_manual_review && $submission->status !== 'final')
                <form method="POST" action="{{ route('mock-exams.results.finalize', [$mockExam, $submission]) }}">
                    @csrf
                    <x-ui.button type="submit" variant="success" icon="check-badge">Finalise Grading</x-ui.button>
                </form>
            @endif
        </div>
    </div>

    @if(session('success'))
        <div class="mb-4 px-4 py-3 rounded-[2px] bg-emerald-50 border border-emerald-200 text-emerald-700 text-sm">{{ session('success') }}</div>
    @endif

    {{-- Score summary --}}
    <div class="grid grid-cols-4 gap-4 mb-6">
        @foreach([
            ['Score',   number_format($submission->score ?? 0, 1) . ' / ' . number_format($submission->total_marks ?? 0, 1), 'violet'],
            ['%',       number_format($submission->percentage ?? 0, 1).'%', 'blue'],
            ['Grade',   $submission->grade ?? '—', 'emerald'],
            ['Status',  str_replace('_', ' ', ucfirst($submission->status)), 'slate'],
        ] as [$label, $value, $color])
        <x-ui.card>
            <div class="p-4 text-center">
                <p class="text-xl font-bold text-{{ $color }}-600 dark:text-{{ $color }}-400">{{ $value }}</p>
                <p class="text-xs text-slate-500 mt-0.5">{{ $label }}</p>
            </div>
        </x-ui.card>
        @endforeach
    </div>

    {{-- Questions with responses --}}
    <form method="POST" action="{{ route('mock-exams.results.grade', [$mockExam, $submission]) }}">
        @csrf

        @php $responses = $submission->responses ?? []; @endphp

        @foreach($mockExam->subjectExams as $se)
            <x-ui.card class="mb-4">
                <x-ui.card-header :title="$se->getDisplayTitle()" accent="info" />
                @foreach($se->sections as $section)
                    <div class="px-5 pt-4 pb-2">
                        <p class="text-xs font-semibold text-slate-500 uppercase tracking-wider mb-3">
                            {{ $section->title }} — {{ ucfirst(str_replace('_', ' ', $section->question_type)) }}
                        </p>

                        @foreach($section->questions as $q)
                            @php
                                $resp = $responses[$q->id] ?? null;
                                $participantAnswer = $resp['response'] ?? null;
                                $isCorrect = $resp['is_correct'] ?? null;
                                $pointsEarned = $resp['points_earned'] ?? 0;
                            @endphp

                            <div class="mb-4 p-4 rounded-[2px] border {{ $isCorrect === true ? 'border-emerald-200 bg-emerald-50/50 dark:border-emerald-800 dark:bg-emerald-900/10' : ($isCorrect === false ? 'border-red-200 bg-red-50/50 dark:border-red-800 dark:bg-red-900/10' : 'border-slate-200 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800/20') }}">

                                <div class="flex items-start justify-between gap-4">
                                    <p class="text-sm font-medium text-slate-800 dark:text-slate-200 flex-1">
                                        <span class="text-slate-400 mr-1">Q{{ $loop->iteration }}.</span>
                                        {!! nl2br(e($q->question_text)) !!}
                                    </p>
                                    <span class="text-xs text-slate-400 shrink-0">{{ $q->marks }} mk{{ $q->marks != 1 ? 's' : '' }}</span>
                                </div>

                                {{-- MCQ options --}}
                                @if($q->isMultipleChoice() && !empty($q->options))
                                    <div class="mt-2 grid grid-cols-2 gap-1">
                                        @foreach($q->getOptionsForDisplay() as $letter => $text)
                                            <p class="text-xs {{ $letter === $q->correct_answer ? 'text-emerald-700 font-medium' : 'text-slate-500' }}">
                                                <span class="font-mono mr-1">{{ $letter }}.</span>{{ $text }}
                                                @if($letter === $q->correct_answer) ✓ @endif
                                            </p>
                                        @endforeach
                                    </div>
                                @endif

                                @if($q->isTrueFalse())
                                    <p class="mt-1 text-xs text-emerald-700 font-medium">Correct: {{ ucfirst($q->correct_answer ?? '') }}</p>
                                @endif

                                {{-- Participant answer --}}
                                <div class="mt-3 pt-3 border-t border-slate-200 dark:border-slate-700 space-y-1">
                                    <p class="text-xs text-slate-500">
                                        <span class="font-medium">Answer:</span>
                                        {{ $participantAnswer ?? 'No answer' }}
                                    </p>
                                    <p class="text-xs text-slate-500">
                                        <span class="font-medium">Points earned:</span> {{ $pointsEarned }} / {{ $q->marks }}
                                    </p>
                                    @if(isset($resp['feedback']))
                                        <p class="text-xs text-amber-600">{{ $resp['feedback'] }}</p>
                                    @endif
                                </div>

                                {{-- Manual grading for essays --}}
                                @if($q->isEssay() && $submission->status !== 'final')
                                    <div class="mt-3 pt-3 border-t border-slate-200 dark:border-slate-700 space-y-2">
                                        <p class="text-xs font-medium text-slate-500 uppercase tracking-wider">Manual Grade</p>
                                        @if($q->answer_explanation)
                                            <p class="text-xs text-slate-500 italic">Model answer: {{ Str::limit($q->answer_explanation, 200) }}</p>
                                        @endif
                                        <div class="flex items-center gap-3">
                                            <label class="text-xs text-slate-500">Points:</label>
                                            <input type="number" name="grades[{{ $q->id }}][points]"
                                                   value="{{ $pointsEarned }}" min="0" max="{{ $q->marks }}" step="0.5"
                                                   class="w-24 px-2 py-1 text-sm border border-slate-200 dark:border-slate-700 rounded-none focus:outline-none focus:ring-2 focus:ring-violet-500/20 dark:bg-slate-800 dark:text-white">
                                            <label class="text-xs text-slate-500">Feedback:</label>
                                            <input type="text" name="grades[{{ $q->id }}][feedback]"
                                                   value="{{ $resp['manual_feedback'] ?? '' }}" placeholder="Optional feedback"
                                                   class="flex-1 px-2 py-1 text-sm border border-slate-200 dark:border-slate-700 rounded-none focus:outline-none focus:ring-2 focus:ring-violet-500/20 dark:bg-slate-800 dark:text-white">
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endforeach
            </x-ui.card>
        @endforeach

        @if($submission->requires_manual_review && $submission->status !== 'final')
            <div class="flex gap-3 mt-4">
                <x-ui.button type="submit" variant="primary" icon="check">Save Grades</x-ui.button>
            </div>
        @endif

        {{-- Teacher feedback --}}
        @if($submission->teacher_feedback)
            <x-ui.card class="mt-4">
                <x-ui.card-header title="Instructor Feedback" accent="info" />
                <div class="p-5 text-sm text-slate-700 dark:text-slate-300">{{ $submission->teacher_feedback }}</div>
            </x-ui.card>
        @endif
    </form>

</x-layouts.app>
