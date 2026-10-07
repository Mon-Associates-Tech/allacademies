{{-- Performance by book --}}
<x-exam.card class="mb-6" :padded="false">
    <x-slot:header>
        <p class="font-mono text-[10.5px] tracking-[0.14em] text-muted">PERFORMANCE BY BOOK</p>
    </x-slot:header>

    <div class="p-5">
        @if(count($this->performanceByBook) > 0)
            @if(!empty($bookBarLabels))
                <livewire:charts.bar-chart :labels="$bookBarLabels" :datasets="$bookBarDatasets" :options="$bookBarOptions" height-class="h-72" />
                <div class="mb-5 min-h-[220px]"></div>
            @endif

            <div class="space-y-3">
                @foreach($this->performanceByBook as $bookPerf)
                    <div class="flex flex-wrap items-center justify-between gap-3 rounded-md border border-line bg-surface-2 px-4 py-3 transition hover:border-primary/40">
                        <div class="min-w-0">
                            <p class="truncate text-sm font-semibold text-ink">{{ Str::limit($bookPerf['book_title'], 50) }}</p>
                            <p class="font-mono text-[10.5px] tracking-wide text-muted">{{ $bookPerf['author'] }}</p>
                        </div>
                        <div class="flex flex-wrap items-center gap-4">
                            @if($bookPerf['improvement'] != 0)
                                <span class="font-mono text-[10.5px] tracking-wide {{ $bookPerf['improvement'] > 0 ? 'text-ok' : 'text-accent' }}">
                                    {{ $bookPerf['improvement'] > 0 ? '▲' : '▼' }} {{ abs($bookPerf['improvement']) }}% FROM FIRST ATTEMPT
                                </span>
                            @endif
                            <div class="text-right">
                                <p class="font-display text-lg font-bold text-primary">{{ number_format($bookPerf['average_score'], 1) }}%</p>
                                <p class="font-mono text-[10px] tracking-wider text-muted">
                                    {{ $bookPerf['quiz_count'] }} {{ strtoupper(Str::plural('quiz', $bookPerf['quiz_count'])) }} · BEST {{ number_format($bookPerf['best_score'], 1) }}%
                                </p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <x-exam.empty compact title="No book data available" />
        @endif
    </div>
</x-exam.card>

{{-- Difficulty + question type --}}
<div class="grid grid-cols-1 gap-6 lg:grid-cols-2">

    {{-- By difficulty --}}
    <x-exam.card :padded="false">
        <x-slot:header>
            <p class="font-mono text-[10.5px] tracking-[0.14em] text-muted">QUIZZES BY DIFFICULTY</p>
        </x-slot:header>
        <div class="p-5">
            @if(!empty($difficultyPieLabels))
                <livewire:charts.pie-chart :labels="$difficultyPieLabels" :values="$difficultyPieValues" :options="$difficultyPieOptions" height-class="h-64" />
                <div class="mb-5 min-h-[200px]"></div>
            @else
                <p class="mb-4 text-sm text-muted">No difficulty data available.</p>
            @endif

            @if(count($this->performanceByDifficulty) > 0)
                <div class="grid grid-cols-1 gap-3 sm:grid-cols-3">
                    @foreach($this->performanceByDifficulty as $diffPerf)
                        <div class="rounded-md border border-line bg-surface-2 p-3 text-center">
                            <p class="font-mono text-[10px] uppercase tracking-[0.14em] text-muted">{{ $diffPerf['difficulty'] }}</p>
                            <p class="mt-1 font-display text-xl font-bold text-primary">{{ number_format($diffPerf['average_score'], 1) }}%</p>
                            <p class="mt-0.5 font-mono text-[10px] tracking-wider text-muted">{{ $diffPerf['quiz_count'] }} {{ strtoupper(Str::plural('QUIZ', $diffPerf['quiz_count'])) }}</p>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </x-exam.card>

    {{-- By question type --}}
    <x-exam.card :padded="false">
        <x-slot:header>
            <p class="font-mono text-[10.5px] tracking-[0.14em] text-muted">QUIZZES BY QUESTION TYPE</p>
        </x-slot:header>
        <div class="p-5">
            @if(!empty($typePieLabels))
                <livewire:charts.pie-chart :labels="$typePieLabels" :values="$typePieValues" :options="$typePieOptions" height-class="h-64" />
                <div class="mb-5 min-h-[200px]"></div>
            @else
                <p class="mb-4 text-sm text-muted">No question type data available.</p>
            @endif

            @if(count($this->performanceByQuestionType) > 0)
                <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                    @foreach($this->performanceByQuestionType as $typePerf)
                        <div class="rounded-md border border-line bg-surface-2 p-3 text-center">
                            <p class="font-mono text-[10px] uppercase tracking-[0.14em] text-muted">{{ $typePerf['type'] }}</p>
                            <p class="mt-1 font-display text-xl font-bold text-primary">{{ number_format($typePerf['average_score'], 1) }}%</p>
                            <p class="mt-0.5 font-mono text-[10px] tracking-wider text-muted">
                                {{ $typePerf['quiz_count'] }} {{ strtoupper(Str::plural('QUIZ', $typePerf['quiz_count'])) }}
                                @if($typePerf['average_time'] > 0)
                                    · {{ gmdate('i:s', $typePerf['average_time']) }} AVG
                                @endif
                            </p>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </x-exam.card>
</div>
