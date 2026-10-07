{{-- Question type comparison --}}
<x-exam.card class="mb-6" :padded="false">
    <x-slot:header>
        <p class="font-mono text-[10.5px] tracking-[0.14em] text-muted">QUESTION TYPE PERFORMANCE COMPARISON</p>
    </x-slot:header>
    <div class="p-5">
        @if(count($this->performanceByQuestionType) > 1)
            @if(!empty($typePieLabels))
                <livewire:charts.bar-chart
                    :labels="$typePieLabels"
                    :datasets="[['label' => 'Avg Score %', 'data' => collect($this->performanceByQuestionType)->pluck('average_score')->toArray(), 'backgroundColor' => ['#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6']]]"
                    :options="['plugins' => ['legend' => ['display' => false]], 'scales' => ['y' => ['beginAtZero' => true, 'max' => 100]]]"
                    height-class="h-64"
                />                <div class="min-h-[220px]"></div>
            @endif
        @else
            <x-exam.empty compact title="Not enough data yet" description="Complete more quizzes with different question types to see comparisons." />
        @endif
    </div>
</x-exam.card>

{{-- Difficulty comparison --}}
<x-exam.card class="mb-6" :padded="false">
    <x-slot:header>
        <p class="font-mono text-[10.5px] tracking-[0.14em] text-muted">DIFFICULTY LEVEL PERFORMANCE</p>
    </x-slot:header>
    <div class="p-5">
        @if(count($this->performanceByDifficulty) > 0)
            @if(!empty($difficultyPieLabels))
                <livewire:charts.bar-chart
                    :labels="$difficultyPieLabels"
                    :datasets="[
                            ['label' => 'Avg Score %', 'data' => collect($this->performanceByDifficulty)->pluck('average_score')->toArray(), 'backgroundColor' => '#3b82f6'],
                            ['label' => 'Pass Rate %', 'data' => collect($this->performanceByDifficulty)->pluck('pass_rate')->toArray(), 'backgroundColor' => '#10b981']
                        ]"
                    :options="['plugins' => ['legend' => ['display' => true, 'position' => 'bottom']], 'scales' => ['y' => ['beginAtZero' => true, 'max' => 100]]]"
                    height-class="h-64"
                />                <div class="mb-6 min-h-[200px]"></div>
            @endif

            <div class="space-y-4">
                @foreach($this->performanceByDifficulty as $index => $diff)
                    <div>
                        <div class="flex items-baseline justify-between font-mono text-[10.5px] tracking-wider text-muted">
                            <span class="uppercase text-ink-soft">{{ $diff['difficulty'] }}</span>
                            <span class="font-semibold text-ink">{{ number_format($diff['average_score'], 1) }}%</span>
                        </div>
                        <div class="mt-1.5 h-1.5 w-full overflow-hidden rounded-full bg-line">
                            <div class="h-full rounded-full bg-primary transition-all" style="width: {{ min(100, $diff['average_score']) }}%"></div>
                        </div>
                        <p class="mt-1 font-mono text-[10px] tracking-wider text-muted">
                            {{ $diff['quiz_count'] }} QUIZZES · PASS RATE {{ number_format($diff['pass_rate'], 1) }}%
                        </p>
                    </div>
                @endforeach
            </div>
        @else
            <x-exam.empty compact title="No difficulty comparison data available" />
        @endif
    </div>
</x-exam.card>

{{-- Insights --}}
<x-exam.card :padded="false">
    <x-slot:header>
        <p class="flex items-center gap-2 font-mono text-[10.5px] tracking-[0.14em] text-muted"><span class="h-2 w-2 bg-primary"></span>PERFORMANCE INSIGHTS</p>
    </x-slot:header>
    <ul class="space-y-2.5 p-5">
        @if($this->performanceData['improvement_trend']['trend'] === 'improving')
            <li class="flex items-start gap-2.5 text-sm text-ink-soft">
                <svg class="mt-0.5 h-4 w-4 shrink-0 text-ok" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                <span>Your performance is improving over time ({{ $this->performanceData['improvement_trend']['change'] > 0 ? '+' : '' }}{{ number_format($this->performanceData['improvement_trend']['change'], 1) }}%).</span>
            </li>
        @endif
        @if($this->performanceData['average_score'] >= 80)
            <li class="flex items-start gap-2.5 text-sm text-ink-soft">
                <svg class="mt-0.5 h-4 w-4 shrink-0 text-ok" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                <span>Excellent average score of {{ number_format($this->performanceData['average_score'], 1) }}% — keep up the great work!</span>
            </li>
        @endif
        @if(count($this->strengthsAndWeaknesses['strengths']) > 0)
            <li class="flex items-start gap-2.5 text-sm text-ink-soft">
                <svg class="mt-0.5 h-4 w-4 shrink-0 text-ok" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                <span>You excel at <b class="text-ink">{{ $this->strengthsAndWeaknesses['strengths'][0]['type'] }}</b> questions.</span>
            </li>
        @endif
        @if(count($this->strengthsAndWeaknesses['weaknesses']) > 0)
            <li class="flex items-start gap-2.5 text-sm text-ink-soft">
                <svg class="mt-0.5 h-4 w-4 shrink-0 text-warn" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>
                <span>Focus on improving <b class="text-ink">{{ $this->strengthsAndWeaknesses['weaknesses'][0]['type'] }}</b> questions.</span>
            </li>
        @endif
        @if($this->performanceData['total_quizzes'] >= 10)
            <li class="flex items-start gap-2.5 text-sm text-ink-soft">
                <svg class="mt-0.5 h-4 w-4 shrink-0 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span>You've completed {{ $this->performanceData['total_quizzes'] }} quizzes — great dedication!</span>
            </li>
        @endif
        @if($this->performanceData['total_quizzes'] == 0)
            <li class="flex items-start gap-2.5 text-sm text-muted">
                <svg class="mt-0.5 h-4 w-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span>Start taking quizzes to see your performance insights!</span>
            </li>
        @endif
    </ul>
</x-exam.card>
