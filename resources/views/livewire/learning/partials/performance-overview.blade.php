
{{-- Grade distribution --}}
<x-exam.card class="mb-6" :padded="false">
    <x-slot:header>
        <p class="font-mono text-[10.5px] tracking-[0.14em] text-muted">GRADE DISTRIBUTION</p>
    </x-slot:header>

    <div class="p-5">
        @if(!empty($gradeBarLabels))
            <livewire:charts.bar-chart :labels="$gradeBarLabels" :datasets="$gradeBarDatasets" :options="$gradeBarOptions" height-class="h-64" />
            <div class="min-h-[220px]"></div>
        @else
            <x-exam.empty compact title="No grade data available yet" />
        @endif

        @php
            $allGrades = \App\Support\GradingSystemResolver::getAllGrades($this->targetUser);
            $gradeInterpretations = collect($allGrades)->keyBy('grade')->map(fn($g) => $g['interpretation'])->toArray();
        @endphp

        @if(count($this->performanceData['grade_distribution']) > 0)
            <div class="mt-5 grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-6">
                @foreach($this->performanceData['grade_distribution'] as $grade => $count)
                    <div class="rounded-md border border-line bg-surface-2 p-3 text-center transition hover:border-primary/40">
                        <p class="font-display text-2xl font-bold text-primary">{{ $grade }}</p>
                        <p class="mt-0.5 truncate text-[11px] text-ink-soft">{{ $gradeInterpretations[$grade] ?? '' }}</p>
                        <p class="mt-1 font-mono text-[10px] tracking-wider text-muted">{{ $count }} {{ strtoupper(Str::plural('quiz', $count)) }}</p>
                    </div>
                @endforeach
            </div>
        @endif
    </div>
</x-exam.card>

{{-- Completion rate gauge --}}
<x-exam.card class="mb-6" :padded="false">
    <x-slot:header>
        <p class="font-mono text-[10.5px] tracking-[0.14em] text-muted">COMPLETION RATE</p>
    </x-slot:header>

    <div class="p-5">
        @if(!empty($completionGaugeThresholds))
            <div class="flex flex-col items-center gap-3">
                <livewire:charts.gauge-chart
                    :value="$completionGaugeValue"
                    :min="$completionGaugeMin"
                    :max="$completionGaugeMax"
                    :thresholds="$completionGaugeThresholds"
                    center-label="Complete"
                    height-class="h-48"
                />
                <div class="min-h-[160px] w-full"></div>
                <p class="text-center font-mono text-[11px] tracking-wide text-muted">
                    <span class="font-display text-2xl font-bold text-primary">{{ number_format($completionGaugeValue, 1) }}%</span>
                    OF STARTED QUIZZES COMPLETED
                </p>
            </div>
        @else
            <x-exam.empty compact title="No completion data available yet" />
        @endif
    </div>
</x-exam.card>

{{-- Strengths & weaknesses --}}
<div class="mb-6 grid grid-cols-1 gap-6 lg:grid-cols-2">
    <x-exam.card :padded="false">
        <x-slot:header>
            <p class="flex items-center gap-2 font-mono text-[10.5px] tracking-[0.14em] text-muted"><span class="h-2 w-2 bg-ok"></span>STRENGTHS</p>
        </x-slot:header>
        <div class="p-5">
            @if(count($this->strengthsAndWeaknesses['strengths']) > 0)
                <ul class="space-y-2.5">
                    @foreach($this->strengthsAndWeaknesses['strengths'] as $strength)
                        <li class="flex items-center justify-between gap-3 rounded-md border border-line bg-surface-2 px-3.5 py-2.5">
                            <div class="min-w-0">
                                <p class="truncate text-sm font-semibold text-ink">{{ $strength['type'] }}</p>
                                <p class="font-mono text-[10px] tracking-wider text-muted">{{ $strength['correct_answers'] }}/{{ $strength['total_questions'] }} CORRECT</p>
                            </div>
                            <span class="shrink-0 font-display text-lg font-bold text-ok">{{ number_format($strength['accuracy'], 1) }}%</span>
                        </li>
                    @endforeach
                </ul>
            @else
                <p class="text-sm text-muted">Complete more quizzes to identify your strengths.</p>
            @endif
        </div>
    </x-exam.card>

    <x-exam.card :padded="false">
        <x-slot:header>
            <p class="flex items-center gap-2 font-mono text-[10.5px] tracking-[0.14em] text-muted"><span class="h-2 w-2 bg-warn"></span>AREAS FOR IMPROVEMENT</p>
        </x-slot:header>
        <div class="p-5">
            @if(count($this->strengthsAndWeaknesses['weaknesses']) > 0)
                <ul class="space-y-2.5">
                    @foreach($this->strengthsAndWeaknesses['weaknesses'] as $weakness)
                        <li class="flex items-center justify-between gap-3 rounded-md border border-line bg-surface-2 px-3.5 py-2.5">
                            <div class="min-w-0">
                                <p class="truncate text-sm font-semibold text-ink">{{ $weakness['type'] }}</p>
                                <p class="font-mono text-[10px] tracking-wider text-muted">{{ $weakness['correct_answers'] }}/{{ $weakness['total_questions'] }} CORRECT</p>
                            </div>
                            <span class="shrink-0 font-display text-lg font-bold text-warn">{{ number_format($weakness['accuracy'], 1) }}%</span>
                        </li>
                    @endforeach
                </ul>
            @else
                <p class="text-sm text-ok">Great job! No significant weaknesses detected.</p>
            @endif
        </div>
    </x-exam.card>
</div>

{{-- Recent quizzes --}}
<div class="mb-2 flex items-center justify-between">
    <p class="font-mono text-[10.5px] tracking-[0.14em] text-muted">RECENT QUIZZES</p>
</div>

<x-exam.table :headers="['Book', 'Difficulty', 'Type', 'Score', 'Questions', 'Date']">
    @forelse($this->recentQuizzes as $quiz)
        @php
            $quizGradeInfo = $this->getGrade($quiz['score']);
            $scoreTone = $quiz['score'] >= 80 ? 'ok' : ($quiz['score'] >= 60 ? 'primary' : ($quiz['score'] >= 40 ? 'warn' : 'danger'));
        @endphp
        <tr wire:key="recent-{{ $loop->index }}" class="transition hover:bg-surface-2/60">
            <td class="px-4 py-3">
                <p class="font-semibold text-ink">{{ Str::limit($quiz['book_title'], 40) }}</p>
                <p class="font-mono text-[10.5px] text-muted">{{ $quiz['author'] }}</p>
            </td>
            <td class="px-4 py-3"><x-exam.badge tone="muted">{{ $quiz['difficulty'] }}</x-exam.badge></td>
            <td class="px-4 py-3 text-xs text-ink-soft">{{ $quiz['question_type'] }}</td>
            <td class="px-4 py-3">
                <div class="flex items-center gap-2">
                    <span class="font-semibold text-ink">{{ number_format($quiz['score'], 1) }}%</span>
                    <x-exam.badge :tone="$scoreTone">{{ $quizGradeInfo['grade'] }}</x-exam.badge>
                </div>
                <span class="mt-0.5 block text-[10.5px] text-muted">{{ $quizGradeInfo['interpretation'] }}</span>
            </td>
            <td class="px-4 py-3 font-mono text-xs text-ink-soft">{{ $quiz['correct'] }}/{{ $quiz['questions'] }}</td>
            <td class="px-4 py-3 font-mono text-[11px] text-muted">{{ $quiz['completed_at']->format('M d, Y') }}</td>
        </tr>
    @empty
        <tr>
            <td colspan="6" class="px-4 py-10 text-center font-mono text-xs tracking-wider text-muted">NO QUIZZES FOUND WITH THE CURRENT FILTERS</td>
        </tr>
    @endforelse
</x-exam.table>
