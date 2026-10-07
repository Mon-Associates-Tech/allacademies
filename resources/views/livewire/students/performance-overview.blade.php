{{-- Partial — include inside a page that already provides x-exam.page --}}

@php
    $percentage = $overallStats['average_percentage'] ?? 0;
    $scoreColor = $percentage >= 80 ? 'text-ok' : ($percentage >= 60 ? 'text-warn' : 'text-accent');
    $gradeTone = fn ($grade) => match (true) {
        str_starts_with((string) $grade, 'A') => 'ok',
        str_starts_with((string) $grade, 'B') => 'primary',
        str_starts_with((string) $grade, 'C') => 'warn',
        default => 'danger',
    };
@endphp

<section>
{{-- ============ Section head ============ --}}
<div class="mb-6">
    <p class="mb-2 flex items-center gap-2 font-mono text-[11px] tracking-[0.14em] text-ink-soft">
        <span class="h-2 w-2 bg-accent"></span>ASSIGNMENT RECORDS · PERFORMANCE REVIEW
    </p>
    <h2 class="font-display text-2xl font-bold text-ink md:text-3xl">Assignment Performance</h2>
    <p class="mt-1 text-sm text-ink-soft">Track your academic progress and achievements.</p>
</div>

{{-- ============ Report card hero ============ --}}
<x-exam.card class="mb-6" :padded="false">
    <x-slot:header>
        <p class="font-mono text-[10.5px] tracking-[0.14em] text-muted">REPORT CARD · CURRENT STANDING</p>
    </x-slot:header>

    <div class="flex flex-col gap-6 p-5 lg:flex-row lg:items-center lg:justify-between">
        {{-- Grade seal + overall score --}}
        <div class="flex items-center gap-5">
            <x-exam.stamp :lines="[$overallStats['overall_grade'] ?? '—']" />
            <div>
                <p class="font-mono text-[10px] uppercase tracking-[0.14em] text-muted">Overall Score</p>
                <p class="mt-1 font-display text-4xl font-bold {{ $scoreColor }}">{{ number_format($percentage, 1) }}%</p>
                <p class="mt-1 font-mono text-[10.5px] tracking-wide text-muted">
                    CURRENT GRADE · {{ $overallStats['overall_grade'] ?? 'N/A' }}
                </p>
            </div>
        </div>

        {{-- Filters --}}
        {{-- VERIFY: wire:model names — source markup was stripped --}}
        <div class="grid w-full max-w-md grid-cols-1 gap-4 sm:grid-cols-2">
            <x-exam.field bare label="Time Period">
                <x-exam.select wire:model.live="selectedPeriod">
                    <option value="all">All Time</option>
                    <option value="week">This Week</option>
                    <option value="month">This Month</option>
                    <option value="quarter">This Quarter</option>
                    <option value="year">This Year</option>
                </x-exam.select>
            </x-exam.field>

            <x-exam.field bare label="Subject">
                <x-exam.select wire:model.live="selectedSubject" placeholder="All Subjects">
                    @foreach($subjects as $id => $name)
                        <option value="{{ $id }}">{{ $name }}</option>
                    @endforeach
                </x-exam.select>
            </x-exam.field>
        </div>
    </div>
</x-exam.card>

@if(!empty($overallStats))
    {{-- ============ Scorecard ============ --}}
    <div class="mb-4 grid grid-cols-2 gap-4 lg:grid-cols-4">
        <div>
            <x-exam.stat label="Average Score" :value="($overallStats['average_percentage'] ?? 0) . '%'" />
            <p class="mt-1.5 text-center font-mono text-[10px] tracking-wider text-muted">GRADE {{ $overallStats['overall_grade'] ?? 'N/A' }}</p>
        </div>
        <div>
            <x-exam.stat label="Completed" :value="$overallStats['completed_assignments'] ?? 0" tone="ok" />
            <p class="mt-1.5 text-center font-mono text-[10px] tracking-wider text-muted">OF {{ $overallStats['total_assignments'] ?? 0 }} ASSIGNED</p>
        </div>
        <div>
            <x-exam.stat label="Completion Rate" :value="($overallStats['completion_rate'] ?? 0) . '%'" tone="primary" />
            <p class="mt-1.5 text-center font-mono text-[10px] tracking-wider text-muted">{{ $overallStats['total_subjects'] ?? 0 }} SUBJECTS</p>
        </div>
        <div>
            <x-exam.stat label="Study Streak" :value="($overallStats['study_streak'] ?? 0) . ' days'" tone="warn" />
            <p class="mt-1.5 text-center font-mono text-[10px] tracking-wider text-muted">KEEP IT UP</p>
        </div>
    </div>

    {{-- ============ Ledger counters ============ --}}
    <div class="mb-6 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:max-w-2xl">
        <div>
            <x-exam.stat label="Total Assignments" :value="$overallStats['total_assignments'] ?? 0" />
            <p class="mt-1.5 text-center font-mono text-[10px] tracking-wider text-muted">{{ $overallStats['available_assignments'] ?? 0 }} AVAILABLE NOW</p>
        </div>
        <div>
            <x-exam.stat label="Pending" :value="$overallStats['pending_assignments'] ?? 0" tone="danger" />
            <p class="mt-1.5 text-center font-mono text-[10px] tracking-wider text-muted">{{ $overallStats['submitted_assignments'] ?? 0 }} SUBMITTED</p>
        </div>
    </div>
@endif

{{-- ============ Charts ============ --}}
<div class="mb-6 grid grid-cols-1 gap-6 lg:grid-cols-2">
    <x-exam.card :padded="false">
        <x-slot:header>
            <p class="font-mono text-[10.5px] tracking-[0.14em] text-muted">PERFORMANCE TREND</p>
        </x-slot:header>
        <div class="p-5">
            @if(!empty($trendData))
                <div class="h-64">
                <canvas id="performanceTrendChart"></canvas>
        </div>
                <div class="grid min-h-[220px] place-items-center rounded-md border border-dashed border-line text-muted">
                    <p class="font-mono text-[10px] tracking-[0.14em]">CHART PLACEHOLDER</p>
                </div>
            @else
                <x-exam.empty compact title="No trend data yet" description="Complete a few assignments to start building your trend line." />
            @endif
        </div>
    </x-exam.card>

    <x-exam.card :padded="false">
        <x-slot:header>
            <p class="font-mono text-[10.5px] tracking-[0.14em] text-muted">PERFORMANCE BY SUBJECT</p>
        </x-slot:header>
        <div class="p-5">
            @if(!empty($performanceData))
                {{-- CHART: paste your subject-performance chart component here --}}
                <div class="grid min-h-[220px] place-items-center rounded-md border border-dashed border-line text-muted">
                    <p class="font-mono text-[10px] tracking-[0.14em]">CHART PLACEHOLDER</p>
                </div>
            @else
                <x-exam.empty compact title="No subject data yet" />
            @endif
        </div>
    </x-exam.card>
</div>

{{-- ============ Pending assignments ============ --}}
@if(!empty($pendingAssignments) && count($pendingAssignments) > 0)
    <x-exam.callout tone="warn" class="mb-6">
        <div class="flex items-start gap-3">
            <svg class="mt-0.5 h-4 w-4 shrink-0 text-warn" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"/></svg>
            <div class="min-w-0 flex-1">
                <p class="text-sm font-semibold text-ink">You have {{ count($pendingAssignments) }} pending assignment{{ count($pendingAssignments) === 1 ? '' : 's' }}</p>

                <ul class="mt-3 space-y-2">
                    @foreach($pendingAssignments as $pending)
                        <li class="flex flex-wrap items-center justify-between gap-2 rounded-md border border-line bg-surface px-3.5 py-2.5">
                            <div class="min-w-0">
                                <p class="truncate text-sm font-semibold text-ink">{{ $pending['assignment']->title }}</p>
                                <p class="font-mono text-[10.5px] tracking-wide text-muted">DUE IN {{ $pending['time_remaining'] }} HOURS</p>
                            </div>
                            <x-exam.badge :tone="$pending['status'] === 'in_progress' ? 'primary' : 'warn'">
                                {{ $pending['status'] === 'in_progress' ? 'In Progress' : 'Not Started' }}
                            </x-exam.badge>
                        </li>
                    @endforeach
                </ul>

                <div class="mt-3">
                    <a href="{{ route('students.assignments') }}"
                       class="font-mono text-[11px] font-semibold uppercase tracking-wider text-primary transition hover:text-primary-hi">
                        VIEW ALL ASSIGNMENTS →
                    </a>
                </div>
            </div>
        </div>
    </x-exam.callout>
@endif

{{-- ============ Upcoming assignments ============ --}}
@if(!empty($upcomingAssignments) && count($upcomingAssignments) > 0)
    <x-exam.card class="mb-6" :padded="false">
        <x-slot:header>
            <p class="flex items-center gap-2 font-mono text-[10.5px] tracking-[0.14em] text-muted">
                <span class="h-2 w-2 bg-primary"></span>UPCOMING ASSIGNMENTS · {{ count($upcomingAssignments) }}
            </p>
        </x-slot:header>
        <ul class="divide-y divide-line">
            @foreach($upcomingAssignments as $upcoming)
                <li class="flex items-center justify-between gap-3 px-5 py-3 transition hover:bg-surface-2/60">
                    <p class="truncate text-sm font-semibold text-ink">{{ $upcoming->title }}</p>
                    <p class="shrink-0 font-mono text-[10.5px] tracking-wide text-muted">STARTS {{ strtoupper($upcoming->starts_at->diffForHumans()) }}</p>
                </li>
            @endforeach
        </ul>
    </x-exam.card>
@endif

{{-- ============ Subject performance analysis ============ --}}
@if(!empty($performanceData))
    <div class="mb-2 flex flex-wrap items-baseline justify-between gap-2">
        <p class="font-mono text-[10.5px] tracking-[0.14em] text-muted">SUBJECT PERFORMANCE ANALYSIS</p>
        <p class="hidden font-mono text-[10px] tracking-wider text-muted sm:block">DETAILED BREAKDOWN ACROSS ALL SUBJECTS</p>
    </div>

    <x-exam.table class="mb-8" :headers="['Subject', 'Assignments', 'Average', 'Score', 'Grade', 'Progress', 'Trend']">
        @foreach($performanceData as $subject)
            @php($g = $subject['grade'] ?? '—')
            <tr class="transition hover:bg-surface-2/60">
                <td class="px-4 py-3 font-semibold text-ink">{{ $subject['subject'] }}</td>
                <td class="px-4 py-3 font-mono text-xs text-ink-soft">{{ $subject['total_assignments'] }}</td>
                <td class="px-4 py-3 font-mono text-xs text-ink-soft">{{ number_format($subject['average_score'], 1) }}</td>
                <td class="px-4 py-3 font-semibold text-ink">{{ number_format($subject['percentage'], 1) }}%</td>
                <td class="px-4 py-3"><x-exam.badge :tone="$gradeTone($g)">{{ $g }}</x-exam.badge></td>
                <td class="px-4 py-3"><div class="w-24"><x-exam.progress :value="$subject['percentage']" /></div></td>
                <td class="px-4 py-3">
                    @if($subject['trend'] === 'up')
                        <span class="font-mono text-[11px] font-semibold text-ok">▲ +{{ number_format($subject['improvement'], 1) }}%</span>
                    @elseif($subject['trend'] === 'down')
                        <span class="font-mono text-[11px] font-semibold text-accent">▼ {{ number_format($subject['improvement'], 1) }}%</span>
                    @else
                        <span class="font-mono text-[11px] text-muted">STABLE</span>
                    @endif
                </td>
            </tr>
        @endforeach
    </x-exam.table>
@else
    <x-exam.empty class="mb-8" title="No performance data" description="Start taking assignments to see your performance analytics here.">
        <a href="{{ route('students.assignments') }}"
           class="mt-5 inline-flex items-center justify-center rounded bg-primary px-5 py-2.5 text-[13.5px] font-semibold tracking-wide text-on-primary transition hover:-translate-y-px hover:bg-primary-hi">
            View Assignments
        </a>
    </x-exam.empty>
@endif

{{-- ============ AI performance insights ============ --}}
@if(!empty($insights))
    <x-exam.card :padded="false">
        <x-slot:header>
            <p class="flex items-center gap-2 font-mono text-[10.5px] tracking-[0.14em] text-muted">
                <span class="h-2 w-2 bg-accent"></span>AI PERFORMANCE INSIGHTS
            </p>
        </x-slot:header>

        <ul class="space-y-3 p-5">
            @foreach($insights as $insight)
                @php
                    $tone = match ($insight['type'] ?? 'info') {
                        'strength'    => ['wrap' => 'bg-ok/10 ring-ok/30', 'icon' => 'text-ok', 'path' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'],
                        'improvement' => ['wrap' => 'bg-warn/10 ring-warn/30', 'icon' => 'text-warn', 'path' => 'M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z'],
                        default       => ['wrap' => 'bg-primary/10 ring-primary/30', 'icon' => 'text-primary', 'path' => 'M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z'],
                    };
                @endphp
                <li class="flex items-start gap-3 rounded-md border border-line bg-surface-2 p-4">
                    <span class="grid h-8 w-8 shrink-0 place-items-center rounded-full ring-1 ring-inset {{ $tone['wrap'] }}">
                        <svg class="h-4 w-4 {{ $tone['icon'] }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $tone['path'] }}"/></svg>
                    </span>
                    <div class="min-w-0 flex-1">
                        <p class="text-sm font-semibold text-ink">{{ $insight['title'] }}</p>
                        <p class="mt-0.5 text-sm leading-relaxed text-ink-soft">{{ $insight['message'] }}</p>
                        @if(!empty($insight['action']))
                            <p class="mt-2 font-mono text-[10.5px] tracking-wide text-primary">TIP · {{ strtoupper($insight['action']) }}</p>
                        @endif
                    </div>
                </li>
            @endforeach
        </ul>
    </x-exam.card>
@endif
</section>
