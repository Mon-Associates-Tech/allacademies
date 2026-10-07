<x-exam.page>
    <x-exam.header
        eyebrow="STUDENT RECORDS · PERFORMANCE ANALYTICS"
        title="Performance Dashboard"
        :sub="isset($userId) && $userId !== auth()->id()
            ? 'Performance analytics for ' . $this->targetUser->name . '.'
            : 'Track your performance and progress across every quiz you have taken.'"
        :lines="['GRADE', 'BOOK']"
    />

    <x-exam.flash />

    {{-- Toolbar --}}
    <div class="mb-6 flex flex-wrap items-center justify-between gap-3">
        <x-exam.badge tone="primary">{{ $this->getGradingSystemName() }}</x-exam.badge>
        <div class="flex gap-2">
            {{-- VERIFY: adjust handler names to your component --}}
            <x-exam.button type="button" variant="ghost" wire:click="resetFilters">Reset Filters</x-exam.button>
            <x-exam.button type="button" wire:click="exportReport">Export Report</x-exam.button>
        </div>
    </div>

    {{-- Filters --}}
    <x-exam.card class="mb-6" :padded="false">
        <x-slot:header>
            <p class="font-mono text-[10.5px] tracking-[0.14em] text-muted">FILTERS · REFINE THE RECORD SET</p>
        </x-slot:header>

        <div class="grid grid-cols-1 gap-4 p-5 sm:grid-cols-2 lg:grid-cols-4">
            {{-- VERIFY: wire:model names below — source markup was stripped --}}
            <x-exam.field bare label="Time Period">
                <x-exam.select wire:model.live="selectedPeriod">
                    @foreach($periods as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </x-exam.select>
            </x-exam.field>

            <x-exam.field bare label="Book">
                <x-exam.select wire:model.live="selectedBookId" placeholder="All Books">
                    @foreach($availableBooks as $book)
                        <option value="{{ $book->id }}">{{ $book->title }}</option>
                    @endforeach
                </x-exam.select>
            </x-exam.field>

            <x-exam.field bare label="Difficulty">
                <x-exam.select wire:model.live="selectedDifficulty">
                    @foreach($difficulties as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </x-exam.select>
            </x-exam.field>

            <x-exam.field bare label="Question Type">
                <x-exam.select wire:model.live="selectedQuestionType">
                    @foreach($questionTypes as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </x-exam.select>
            </x-exam.field>

            <x-exam.field bare label="Min Score (%)">
                <x-exam.input type="number" min="0" max="100" wire:model.live="minScore" />
            </x-exam.field>

            <x-exam.field bare label="Max Score (%)">
                <x-exam.input type="number" min="0" max="100" wire:model.live="maxScore" />
            </x-exam.field>

            @if($selectedPeriod === 'custom')
                <x-exam.field bare label="Start Date">
                    <x-exam.input type="date" wire:model.live="startDate" />
                </x-exam.field>
                <x-exam.field bare label="End Date">
                    <x-exam.input type="date" wire:model.live="endDate" />
                </x-exam.field>
            @endif
        </div>
    </x-exam.card>

    {{-- Overview stats --}}
    @php($trend = $this->performanceData['improvement_trend'])
    <div class="mb-4 grid grid-cols-2 gap-4 lg:grid-cols-4">
        <x-exam.stat label="Total Quizzes" :value="$this->performanceData['total_quizzes']" />
        <div>
            <x-exam.stat label="Average Score" :value="number_format($this->performanceData['average_score'], 1) . '%'" />
            @php($avgGradeInfo = $this->getGrade($this->performanceData['average_score']))
            <p class="mt-1.5 text-center font-mono text-[10px] tracking-wider text-muted">
                GRADE {{ $avgGradeInfo['grade'] }} · {{ strtoupper($avgGradeInfo['interpretation']) }}
            </p>
        </div>
        <x-exam.stat label="Best Score" :value="number_format($this->performanceData['highest_score'], 1) . '%'" tone="ok" />

        <div class="rounded-md border border-line bg-surface p-4 text-center shadow-[0_1px_2px_rgba(20,24,34,0.04)]">
            <p class="font-display text-2xl font-bold {{ $trend['trend'] === 'improving' ? 'text-ok' : ($trend['trend'] === 'declining' ? 'text-accent' : 'text-ink') }}">
                {{ $trend['trend'] === 'improving' ? '▲' : ($trend['trend'] === 'declining' ? '▼' : '→') }}
            </p>
            <p class="mt-1 font-mono text-[10px] uppercase tracking-[0.14em] text-muted">Trend</p>
            @if($trend['change'] != 0)
                <p class="mt-1 font-mono text-[10.5px] tracking-wide {{ $trend['change'] > 0 ? 'text-ok' : 'text-accent' }}">
                    {{ $trend['change'] > 0 ? '+' : '' }}{{ number_format($trend['change'], 1) }}%
                </p>
            @endif
        </div>
    </div>

    {{-- Secondary stats --}}
    <div class="mb-8 grid grid-cols-1 gap-4 sm:grid-cols-3">
        <x-exam.stat label="Questions Answered" :value="number_format($this->performanceData['total_questions_answered'])" />
        <x-exam.stat label="Average Time / Quiz" :value="gmdate('i:s', $this->performanceData['average_time_taken'] ?? 0)" />
        <x-exam.stat label="Completion Rate" :value="number_format($this->performanceData['completion_rate'], 1) . '%'" />
    </div>

    {{-- Analysis views --}}
    <x-exam.tabs
        :tabs="['overview' => 'Overview', 'detailed' => 'Detailed Analysis', 'trends' => 'Trends', 'comparisons' => 'Comparisons']"
        :active="$activeView"
        property="activeView"
    />

    <div class="animate-rise">
        @if($activeView === 'overview')
            @include('livewire.learning.partials.performance-overview')
        @elseif($activeView === 'detailed')
            @include('livewire.learning.partials.performance-detailed')
        @elseif($activeView === 'trends')
            @include('livewire.learning.partials.performance-trends')
        @elseif($activeView === 'comparisons')
            @include('livewire.learning.partials.performance-comparisons')
        @endif
    </div>
</x-exam.page>
