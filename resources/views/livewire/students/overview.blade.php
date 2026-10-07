<x-exam.page>
    <x-exam.header
        eyebrow="STUDENT RECORDS · ACADEMIC OVERVIEW"
        title="My Dashboard"
        sub="Track your academic progress across assignments, quizzes, and reading."
        :lines="['REPORT', 'CARD']"
    />

    <x-exam.flash/>

    @if(!$student)
        <x-exam.callout tone="danger">
            <p class="text-sm">Student profile not found. Please contact your administrator.</p>
        </x-exam.callout>
    @else
        @php
            $scoreTone = fn ($p) => $p >= 80 ? 'ok' : ($p >= 60 ? 'primary' : ($p >= 40 ? 'warn' : 'danger'));
            $barColor  = fn ($p) => ['ok' => 'bg-ok', 'primary' => 'bg-primary', 'warn' => 'bg-warn', 'danger' => 'bg-accent'][$scoreTone($p)];
        @endphp

        {{-- Toolbar: range switcher + average --}}
        <div class="mb-6 flex flex-wrap items-center justify-between gap-4">
            {{-- VERIFY: swap the anchors for wire:click="$set('range', '…')" if this view is a Livewire component --}}
            <nav class="flex w-fit flex-wrap items-center gap-1 rounded-md border border-line bg-surface p-1"
                 aria-label="Date range">
                @foreach(['all' => 'All', '7d' => '7d', '30d' => '30d', '90d' => '90d', 'term' => 'Term'] as $key => $label)
                    <button wire:click="$set('range', '{{ $key }}')" type="button"
                            class="rounded px-3 py-1.5 font-mono text-[11px] font-semibold uppercase tracking-[0.12em] transition
                              {{ $range === $key ? 'bg-primary text-on-primary' : 'text-muted hover:bg-surface-2 hover:text-ink' }}">
                        {{ $label }}
                    </button>
                @endforeach
            </nav>
            <p class="font-mono text-[10.5px] tracking-[0.14em] text-muted">
                AVG SCORE <span
                    class="ml-1 font-display text-lg font-bold text-primary">{{ $averageAssignmentScore }}%</span>
            </p>
        </div>

        {{-- ============ Snapshot ============ --}}
        @if(!empty($studentSnapshot))
            <div class="mb-6 grid grid-cols-1 gap-4 md:grid-cols-3">
                {{-- Assignments --}}
                <x-exam.card :padded="false">
                    <x-slot:header>
                        <p class="flex items-center gap-2 font-mono text-[10.5px] tracking-[0.14em] text-muted"><span
                                class="h-2 w-2 bg-primary"></span>ASSIGNMENTS SNAPSHOT</p>
                    </x-slot:header>
                    <div class="p-5">
                        <p class="font-display text-3xl font-bold text-primary">{{ $studentSnapshot['assignments']['completion_rate'] ?? 0 }}
                            %</p>
                        <p class="mt-1 font-mono text-[10.5px] tracking-wide text-muted">
                            {{ $studentSnapshot['assignments']['completed'] ?? 0 }} COMPLETED
                            · {{ $studentSnapshot['assignments']['upcoming'] ?? 0 }} DUE SOON
                        </p>
                        <div class="mt-3 h-1.5 overflow-hidden rounded-full bg-line">
                            <div class="h-full rounded-full bg-primary transition-all"
                                 style="width: {{ min(100, $studentSnapshot['assignments']['completion_rate'] ?? 0) }}%"></div>
                        </div>
                    </div>
                </x-exam.card>

                {{-- Self assessments --}}
                <x-exam.card :padded="false">
                    <x-slot:header>
                        <p class="flex items-center gap-2 font-mono text-[10.5px] tracking-[0.14em] text-muted"><span
                                class="h-2 w-2 bg-ok"></span>SELF ASSESSMENTS</p>
                    </x-slot:header>
                    <div class="p-5">
                        <p class="font-display text-3xl font-bold text-ok">{{ $studentSnapshot['quizzes']['average_score'] ?? 0 }}
                            %</p>
                        <p class="mt-1 font-mono text-[10.5px] tracking-wide text-muted">
                            {{ $studentSnapshot['quizzes']['total'] ?? 0 }} ATTEMPTS ·
                            BEST {{ $studentSnapshot['quizzes']['best_score'] ?? 0 }}%
                        </p>
                        <div class="mt-3 h-1.5 overflow-hidden rounded-full bg-line">
                            <div class="h-full rounded-full bg-ok transition-all"
                                 style="width: {{ min(100, $studentSnapshot['quizzes']['average_score'] ?? 0) }}%"></div>
                        </div>
                    </div>
                </x-exam.card>

                {{-- Reading --}}
                <x-exam.card :padded="false">
                    <x-slot:header>
                        <p class="flex items-center gap-2 font-mono text-[10.5px] tracking-[0.14em] text-muted"><span
                                class="h-2 w-2 bg-warn"></span>READING PROGRESS</p>
                    </x-slot:header>
                    <div class="p-5">
                        <p class="font-display text-3xl font-bold text-warn">{{ $studentSnapshot['reading']['books_in_progress'] ?? 0 }}</p>
                        <p class="mt-1 font-mono text-[10.5px] tracking-wide text-muted">
                            {{ $studentSnapshot['reading']['books_completed'] ?? 0 }} COMPLETED BOOKS
                            · {{ number_format($studentSnapshot['reading']['total_pages_read'] ?? 0) }} PAGES READ
                        </p>
                    </div>
                </x-exam.card>
            </div>
        @endif

        {{-- ============ Assignment ledger ============ --}}
        <div class="mb-4 grid grid-cols-2 gap-4 lg:grid-cols-4">
            <x-exam.stat label="Total" :value="$totalAssignments"/>
            <x-exam.stat label="Completed" :value="$completedAssignments" tone="ok"/>
            <x-exam.stat label="Ongoing" :value="$ongoingAssignments" tone="primary"/>
            <x-exam.stat label="Overdue" :value="$overdueAssignments" tone="danger"/>
        </div>

        <x-exam.card class="mb-8" :padded="false">
            <x-slot:header>
                <p class="font-mono text-[10.5px] tracking-[0.14em] text-muted">SUBMISSION PIPELINE</p>
            </x-slot:header>
            <div class="grid grid-cols-1 gap-8 p-5 md:grid-cols-2">
                {{-- Submitted → graded --}}
                @php($gradePct = $submittedAssignments > 0 ? min(100, round($gradedAssignments / $submittedAssignments * 100)) : 0)
                <div>
                    <div class="flex items-center gap-3">
                        <div class="flex-1 rounded-md border border-line bg-surface-2 p-3 text-center">
                            <p class="font-display text-2xl font-bold text-primary">{{ $submittedAssignments }}</p>
                            <p class="mt-0.5 font-mono text-[10px] tracking-wider text-muted">SUBMITTED</p>
                        </div>
                        <svg class="h-4 w-4 shrink-0 text-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M13 7l5 5m0 0l-5 5m5-5H6"/>
                        </svg>
                        <div class="flex-1 rounded-md border border-line bg-surface-2 p-3 text-center">
                            <p class="font-display text-2xl font-bold text-ok">{{ $gradedAssignments }}</p>
                            <p class="mt-0.5 font-mono text-[10px] tracking-wider text-muted">GRADED</p>
                        </div>
                    </div>
                    <div class="mt-3 h-1.5 overflow-hidden rounded-full bg-line">
                        <div class="h-full rounded-full bg-ok transition-all" style="width: {{ $gradePct }}%"></div>
                    </div>
                    <p class="mt-1.5 font-mono text-[10px] tracking-wider text-muted">{{ $gradePct }}% OF SUBMISSIONS
                        GRADED</p>
                </div>

                {{-- Assigned scope --}}
                <div>
                    <div class="grid grid-cols-2 gap-3">
                        <div class="rounded-md border border-line bg-surface-2 p-3 text-center">
                            <p class="font-display text-2xl font-bold text-ink">{{ $totalAssignments }}</p>
                            <p class="mt-0.5 font-mono text-[10px] tracking-wider text-muted">TOTAL ASSIGNED</p>
                        </div>
                        <div class="rounded-md border border-line bg-surface-2 p-3 text-center">
                            <p class="font-display text-2xl font-bold text-ink">{{ $studentSnapshot['assignments']['total_available_now'] ?? 0 }}</p>
                            <p class="mt-0.5 font-mono text-[10px] tracking-wider text-muted">ACTIVE NOW</p>
                        </div>
                    </div>
                </div>
            </div>
        </x-exam.card>

        {{-- ============ Quick actions ============ --}}
        <x-exam.card class="mb-8" :padded="false">
            <x-slot:header>
                <p class="font-mono text-[10.5px] tracking-[0.14em] text-muted">QUICK ACTIONS</p>
                <p class="font-mono text-[10.5px] tracking-[0.14em] text-muted">{{ now()->format('l, F j, Y') }}</p>
            </x-slot:header>

            <div class="grid grid-cols-2 gap-3 p-5 sm:grid-cols-3 lg:grid-cols-6">
                {{-- Assignments --}}
                <a href="{{ route('students.assignments') }}"
                   class="group relative rounded-md border border-line bg-surface-2 px-3 py-4 text-center transition hover:-translate-y-0.5 hover:border-primary/50 motion-reduce:transition-none">
                    <svg class="mx-auto h-5 w-5 text-muted transition group-hover:text-primary" fill="none"
                         stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                              d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                    </svg>
                    <span
                        class="mt-2 block font-mono text-[10px] font-semibold uppercase tracking-[0.12em] text-ink-soft transition group-hover:text-ink">Assignments</span>
                    @if($upcomingAssignments > 0)
                        <span
                            class="absolute right-1.5 top-1.5 grid h-5 min-w-[20px] place-items-center rounded-full bg-accent px-1 font-mono text-[9.5px] font-bold text-white">
                            {{ $upcomingAssignments > 9 ? '9+' : $upcomingAssignments }}
                        </span>
                    @endif
                </a>

                {{-- VERIFY: point each action at its real route --}}
                <a href="{{ route('students.assessments') }}"
                   class="group rounded-md border border-line bg-surface-2 px-3 py-4 text-center transition hover:-translate-y-0.5 hover:border-primary/50 motion-reduce:transition-none">
                    <svg class="mx-auto h-5 w-5 text-muted transition group-hover:text-primary" fill="none"
                         stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                              d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                    <span
                        class="mt-2 block font-mono text-[10px] font-semibold uppercase tracking-[0.12em] text-ink-soft transition group-hover:text-ink">Take Quiz</span>
                </a>

                <a href="{{ route('students.courses') }}"
                   class="group rounded-md border border-line bg-surface-2 px-3 py-4 text-center transition hover:-translate-y-0.5 hover:border-primary/50 motion-reduce:transition-none">
                    <svg class="mx-auto h-5 w-5 text-muted transition group-hover:text-primary" fill="none"
                         stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                              d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                    <span
                        class="mt-2 block font-mono text-[10px] font-semibold uppercase tracking-[0.12em] text-ink-soft transition group-hover:text-ink">Courses</span>
                </a>

                <a href="{{ route('students.schedules') }}"
                   class="group rounded-md border border-line bg-surface-2 px-3 py-4 text-center transition hover:-translate-y-0.5 hover:border-primary/50 motion-reduce:transition-none">
                    <svg class="mx-auto h-5 w-5 text-muted transition group-hover:text-primary" fill="none"
                         stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                              d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <span
                        class="mt-2 block font-mono text-[10px] font-semibold uppercase tracking-[0.12em] text-ink-soft transition group-hover:text-ink">Schedule</span>
                </a>

                <a href="{{ route('students.messages.index') }}"
                   class="group rounded-md border border-line bg-surface-2 px-3 py-4 text-center transition hover:-translate-y-0.5 hover:border-primary/50 motion-reduce:transition-none">
                    <svg class="mx-auto h-5 w-5 text-muted transition group-hover:text-primary" fill="none"
                         stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                              d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                    </svg>
                    <span
                        class="mt-2 block font-mono text-[10px] font-semibold uppercase tracking-[0.12em] text-ink-soft transition group-hover:text-ink">Messages</span>
                </a>

                <a href="{{ route('students.classroom.sessions') }}"
                   class="group rounded-md border border-line bg-surface-2 px-3 py-4 text-center transition hover:-translate-y-0.5 hover:border-primary/50 motion-reduce:transition-none">
                    <svg class="mx-auto h-5 w-5 text-muted transition group-hover:text-primary" fill="none"
                         stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                              d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/>
                    </svg>
                    <span
                        class="mt-2 block font-mono text-[10px] font-semibold uppercase tracking-[0.12em] text-ink-soft transition group-hover:text-ink">Virtual Class</span>
                </a>
            </div>
        </x-exam.card>

        {{-- ============ Charts ============ --}}
        <div class="mb-8 grid grid-cols-1 gap-6 lg:grid-cols-2">
            <x-exam.card :padded="false">
                <x-slot:header>
                    <p class="font-mono text-[10.5px] tracking-[0.14em] text-muted">PERFORMANCE BY SUBJECT ·
                        RANGE: {{ strtoupper($range) }}</p>
                </x-slot:header>
                <div class="p-5">
                    <livewire:charts.bar-chart :labels="$barLabels" :datasets="$barDatasets" :options="$barOptions"
                                               height-class="h-72"/>
                    <div
                        class="grid min-h-[220px] place-items-center rounded-md border border-dashed border-line text-muted">
                        <p class="font-mono text-[10px] tracking-[0.14em]">CHART PLACEHOLDER</p>
                    </div>
                </div>
            </x-exam.card>

            <x-exam.card :padded="false">
                <x-slot:header>
                    <p class="font-mono text-[10.5px] tracking-[0.14em] text-muted">ASSIGNMENTS STATUS ·
                        RANGE: {{ strtoupper($range) }}</p>
                </x-slot:header>
                <div class="p-5">
                    <livewire:charts.pie-chart :labels="$pieLabels" :values="$pieValues" :options="$pieOptions"
                                               height-class="h-72"/>
                    <div
                        class="grid min-h-[220px] place-items-center rounded-md border border-dashed border-line text-muted">
                        <p class="font-mono text-[10px] tracking-[0.14em]">CHART PLACEHOLDER</p>
                    </div>
                </div>
            </x-exam.card>
        </div>

        {{-- ============ Recent & upcoming assignments ============ --}}
        <div class="mb-8 grid grid-cols-1 gap-6 lg:grid-cols-2">
            {{-- Recent --}}
            <x-exam.card :padded="false">
                <x-slot:header>
                    <p class="font-mono text-[10.5px] tracking-[0.14em] text-muted">RECENT ASSIGNMENTS</p>
                    <a href="{{ route('students.assignments') }}"
                       class="font-mono text-[10.5px] font-semibold tracking-[0.14em] text-primary transition hover:text-primary-hi">VIEW
                        ALL →</a>
                </x-slot:header>
                <ul class="divide-y divide-line">
                    @forelse($recentAssignments as $assignment)
                        <li class="flex items-center justify-between gap-3 px-5 py-3.5 transition hover:bg-surface-2/60">
                            <div class="min-w-0">
                                <p class="truncate text-sm font-semibold text-ink">{{ $assignment['title'] }}</p>
                                <p class="mt-0.5 font-mono text-[10.5px] tracking-wide text-muted">{{ $assignment['subject'] }}
                                    · {{ $assignment['submitted_at']->diffForHumans() }}</p>
                            </div>
                            <x-exam.badge :tone="$scoreTone($assignment['percentage'])">{{ $assignment['percentage'] }}
                                %
                            </x-exam.badge>
                        </li>
                    @empty
                        <li class="px-5 py-8 text-center font-mono text-xs tracking-wider text-muted">NO COMPLETED
                            ASSIGNMENTS YET
                        </li>
                    @endforelse
                </ul>
            </x-exam.card>

            {{-- Upcoming --}}
            <x-exam.card :padded="false">
                <x-slot:header>
                    <p class="font-mono text-[10.5px] tracking-[0.14em] text-muted">UPCOMING ASSIGNMENTS</p>
                    <a href="{{ route('students.assignments') }}"
                       class="font-mono text-[10.5px] font-semibold tracking-[0.14em] text-primary transition hover:text-primary-hi">VIEW
                        ALL →</a>
                </x-slot:header>
                <ul class="divide-y divide-line">
                    @forelse($upcomingDueAssignments as $assignment)
                        @php($days = abs($assignment['days_until_due']))
                        <li class="flex items-center justify-between gap-3 px-5 py-3.5 transition hover:bg-surface-2/60">
                            <div class="min-w-0">
                                <p class="truncate text-sm font-semibold text-ink">{{ $assignment['title'] }}</p>
                                <p class="mt-0.5 font-mono text-[10.5px] tracking-wide text-muted">{{ $assignment['subject'] }}
                                    · DUE {{ $assignment['due_date']->format('M d, Y') }}</p>
                            </div>
                            <x-exam.badge :tone="$days <= 3 ? 'danger' : ($days <= 7 ? 'warn' : 'muted')">
                                {{ $days }} {{ $days === 1 ? 'DAY' : 'DAYS' }}
                            </x-exam.badge>
                        </li>
                    @empty
                        <li class="px-5 py-8 text-center font-mono text-xs tracking-wider text-muted">NO UPCOMING
                            ASSIGNMENTS
                        </li>
                    @endforelse
                </ul>
            </x-exam.card>
        </div>

        {{-- ============ Subject details ============ --}}
        @if(count($subjectPerformance) > 0)
            <x-exam.card class="mb-8" :padded="false">
                <x-slot:header>
                    <p class="font-mono text-[10.5px] tracking-[0.14em] text-muted">SUBJECT DETAILS</p>
                </x-slot:header>
                <div class="space-y-4 p-5">
                    @foreach($subjectPerformance as $subject)
                        <div>
                            <div class="flex items-baseline justify-between gap-3">
                                <p class="text-sm font-semibold text-ink">{{ $subject['subject'] }}</p>
                                <p class="font-mono text-[10.5px] tracking-wide text-muted">
                                    {{ $subject['assignments_count'] }} {{ strtoupper(Str::plural('assignment', $subject['assignments_count'])) }}
                                    ·
                                    <span class="font-semibold text-ink">{{ $subject['average_score'] }}%</span>
                                </p>
                            </div>
                            <div class="mt-1.5 h-1.5 overflow-hidden rounded-full bg-line">
                                <div
                                    class="h-full rounded-full {{ $barColor($subject['average_score']) }} transition-all"
                                    style="width: {{ min(100, $subject['average_score']) }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </x-exam.card>
        @endif

        {{-- ============ Book quiz performance ============ --}}
        @if($totalQuizAttempts > 0)
            <div class="mb-4">
                <h2 class="font-display text-xl font-bold text-ink">Book Quiz Performance</h2>
                <p class="mt-0.5 text-sm text-ink-soft">Self-assessment quizzes from books & uploads</p>
            </div>

            <div class="mb-4 grid grid-cols-2 gap-4 sm:max-w-md">
                <x-exam.stat label="Quizzes" :value="$totalQuizAttempts"/>
                <x-exam.stat label="Avg Score" :value="$averageSelfAssessmentScore . '%'" tone="primary"/>
            </div>

            <div class="mb-6 grid grid-cols-1 gap-6 lg:grid-cols-2">
                <x-exam.card :padded="false">
                    <x-slot:header>
                        <p class="font-mono text-[10.5px] tracking-[0.14em] text-muted">QUIZ SCORES BY SOURCE ·
                            RANGE: {{ strtoupper($range) }}</p>
                    </x-slot:header>
                    <div class="p-5">
                        <livewire:charts.bar-chart :labels="$quizBarLabels" :datasets="$quizBarDatasets"
                                                   :options="$quizBarOptions" height-class="h-72"/>
                        <div
                            class="grid min-h-[200px] place-items-center rounded-md border border-dashed border-line text-muted">
                            <p class="font-mono text-[10px] tracking-[0.14em]">CHART PLACEHOLDER</p>
                        </div>
                    </div>
                </x-exam.card>

                <x-exam.card :padded="false">
                    <x-slot:header>
                        <p class="font-mono text-[10.5px] tracking-[0.14em] text-muted">BY DIFFICULTY ·
                            RANGE: {{ strtoupper($range) }}</p>
                    </x-slot:header>
                    <div class="p-5">
                        <livewire:charts.pie-chart :labels="$quizPieLabels" :values="$quizPieValues"
                                                   :options="$quizPieOptions" height-class="h-72"/>
                        <div
                            class="grid min-h-[200px] place-items-center rounded-md border border-dashed border-line text-muted">
                            <p class="font-mono text-[10px] tracking-[0.14em]">CHART PLACEHOLDER</p>
                        </div>
                    </div>
                </x-exam.card>
            </div>

            <div class="mb-8 grid grid-cols-1 gap-6 lg:grid-cols-2">
                @if(count($quizzesByDifficulty) > 0)
                    <x-exam.card :padded="false">
                        <x-slot:header>
                            <p class="font-mono text-[10.5px] tracking-[0.14em] text-muted">PERFORMANCE BY
                                DIFFICULTY</p>
                        </x-slot:header>
                        <div class="space-y-4 p-5">
                            @foreach($quizzesByDifficulty as $diff)
                                <div>
                                    <div class="flex items-baseline justify-between gap-3">
                                        <p class="font-mono text-[11px] font-semibold uppercase tracking-[0.12em] text-ink-soft">{{ $diff['difficulty'] }}</p>
                                        <p class="font-mono text-[10.5px] tracking-wide text-muted">
                                            {{ $diff['count'] }} {{ strtoupper(Str::plural('quiz', $diff['count'])) }} ·
                                            <span class="font-semibold text-ink">{{ $diff['average_score'] }}%</span>
                                        </p>
                                    </div>
                                    <div class="mt-1.5 h-1.5 overflow-hidden rounded-full bg-line">
                                        <div
                                            class="h-full rounded-full {{ $barColor($diff['average_score']) }} transition-all"
                                            style="width: {{ min(100, $diff['average_score']) }}%"></div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </x-exam.card>
                @endif

                @if(count($quizzesByType) > 0)
                    <x-exam.card :padded="false">
                        <x-slot:header>
                            <p class="font-mono text-[10.5px] tracking-[0.14em] text-muted">PERFORMANCE BY QUESTION
                                TYPE</p>
                        </x-slot:header>
                        <div class="space-y-4 p-5">
                            @foreach($quizzesByType as $type)
                                <div>
                                    <div class="flex items-baseline justify-between gap-3">
                                        <p class="font-mono text-[11px] font-semibold uppercase tracking-[0.12em] text-ink-soft">{{ $type['type'] }}</p>
                                        <p class="font-mono text-[10.5px] tracking-wide text-muted">
                                            {{ $type['count'] }} {{ strtoupper(Str::plural('quiz', $type['count'])) }} ·
                                            <span class="font-semibold text-ink">{{ $type['average_score'] }}%</span>
                                        </p>
                                    </div>
                                    <div class="mt-1.5 h-1.5 overflow-hidden rounded-full bg-line">
                                        <div
                                            class="h-full rounded-full {{ $barColor($type['average_score']) }} transition-all"
                                            style="width: {{ min(100, $type['average_score']) }}%"></div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </x-exam.card>
                @endif
            </div>
        @endif

        {{-- ============ Recent book quizzes ============ --}}
        @if(count($recentSelfAssessments) > 0)
            <x-exam.card :padded="false">
                <x-slot:header>
                    <p class="font-mono text-[10.5px] tracking-[0.14em] text-muted">RECENT BOOK QUIZZES</p>
                    <a href="{{ route('students.assessments') }}"
                       class="font-mono text-[10.5px] font-semibold tracking-[0.14em] text-primary transition hover:text-primary-hi">VIEW
                        ALL →</a>
                </x-slot:header>
                <ul class="divide-y divide-line">
                    @foreach($recentSelfAssessments as $assessment)
                        <li class="flex items-center justify-between gap-3 px-5 py-3.5 transition hover:bg-surface-2/60">
                            <div class="min-w-0">
                                <div class="flex items-center gap-2">
                                    <p class="truncate text-sm font-semibold text-ink">{{ $assessment['book_title'] }}</p>
                                    @if(($assessment['type'] ?? null) === 'uploaded')
                                        <x-exam.badge tone="warn">UPLOAD</x-exam.badge>
                                    @endif
                                </div>
                                <p class="mt-0.5 font-mono text-[10.5px] tracking-wide text-muted">
                                    @if(isset($assessment['difficulty']))
                                        {{ strtoupper($assessment['difficulty']) }} ·
                                    @endif
                                    {{ $assessment['completed_at']->diffForHumans() }}
                                </p>
                            </div>
                            <x-exam.badge :tone="$scoreTone($assessment['score'])">SCORE {{ $assessment['score'] }}%
                            </x-exam.badge>
                        </li>
                    @endforeach
                </ul>
            </x-exam.card>
        @endif
    @endif
</x-exam.page>
