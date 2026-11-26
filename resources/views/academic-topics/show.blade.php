<x-layouts.app title="{{ $academicTopic->name }} · Topic Details" :show-title-area="false" :page-name="$academicTopic->name">
    <x-exam.page>
        @php
            $topicSubject = $academicTopic->academicSubject;
            $topicLevel   = $topicSubject->academicLevel;
            $topicGroup   = $topicLevel->academicGroup;

            $mcqCount   = (int) ($academicTopic->multiple_choice_questions_count ?? 0);
            $tfCount    = (int) ($academicTopic->true_or_false_questions_count ?? 0);
            $essayCount = (int) ($academicTopic->essay_questions_count ?? 0);
            $bankTotal  = $mcqCount + $tfCount + $essayCount;

            $share = fn ($n) => $bankTotal > 0 ? round($n / $bankTotal * 100, 1) : 0;

            $bankRows = [
                [
                    'key'   => 'mcq',
                    'stamp' => 'MCQ',
                    'label' => 'Multiple Choice',
                    'note'  => 'Single answer from option set',
                    'count' => $mcqCount,
                    'tone'  => 'primary',
                    'href'  => route('multiple-choice-questions.index', [
                        'academic_topic'   => $academicTopic,
                        'academic_subject' => getRouteParameter('academic_subject'),
                        'academic_level'   => getRouteParameter('academic_level'),
                        'academic_group'   => getRouteParameter('academic_group'),
                    ]),
                ],
                [
                    'key'   => 'tf',
                    'stamp' => 'T/F',
                    'label' => 'True / False',
                    'note'  => 'Binary statement judgement',
                    'count' => $tfCount,
                    'tone'  => 'ok',
                    'href'  => route('true-or-false-questions.index', [
                        'academic_topic'   => $academicTopic,
                        'academic_subject' => getRouteParameter('academic_subject'),
                        'academic_level'   => getRouteParameter('academic_level'),
                        'academic_group'   => getRouteParameter('academic_group'),
                    ]),
                ],
                [
                    'key'   => 'essay',
                    'stamp' => 'ESY',
                    'label' => 'Essay Questions',
                    'note'  => 'Extended written response',
                    'count' => $essayCount,
                    'tone'  => 'warn',
                    'href'  => route('essay-questions.index', [
                        'academic_topic'   => $academicTopic,
                        'academic_subject' => getRouteParameter('academic_subject'),
                        'academic_level'   => getRouteParameter('academic_level'),
                        'academic_group'   => getRouteParameter('academic_group'),
                    ]),
                ],
            ];

            $toneText = ['primary' => 'text-primary', 'ok' => 'text-ok', 'warn' => 'text-warn'];
            $toneFill = ['primary' => 'bg-primary', 'ok' => 'bg-ok', 'warn' => 'bg-warn'];
            $toneChip = ['primary' => 'bg-primary/10 text-primary', 'ok' => 'bg-ok/10 text-ok', 'warn' => 'bg-warn/10 text-warn'];
        @endphp

        {{-- Breadcrumb --}}
        <x-exam.crumbs :items="[
            ['label' => 'Academic Groups', 'href' => route('academic-groups.index')],
            ['label' => $topicGroup->name, 'href' => route('academic-groups.show', ['academic_group' => $topicGroup])],
            ['label' => 'Academic Levels', 'href' => route('academic-levels.index', ['academic_group' => $topicGroup])],
            ['label' => $topicLevel->name, 'href' => route('academic-levels.show', ['academic_level' => $topicLevel, 'academic_group' => $topicGroup])],
            ['label' => 'Subjects', 'href' => route('academic-subjects.index', ['academic_level' => $topicLevel, 'academic_group' => $topicGroup])],
            ['label' => $topicSubject->name, 'href' => route('academic-subjects.show', ['academic_subject' => $topicSubject, 'academic_level' => $topicLevel, 'academic_group' => $topicGroup])],
            ['label' => 'Topics', 'href' => route('academic-topics.index', ['academic_subject' => $topicSubject, 'academic_level' => $topicLevel, 'academic_group' => $topicGroup])],
            ['label' => $academicTopic->name],
        ]" />

        {{-- ============ Header ============ --}}
        <div class="mb-6 flex flex-wrap items-start justify-between gap-4">
            <div class="min-w-0">
                <div class="flex flex-wrap items-center gap-2.5">
                    <h1 class="font-display text-2xl font-bold leading-tight text-ink md:text-3xl">{{ $academicTopic->name }}</h1>
                    <x-exam.badge tone="ok">
                        <span class="mr-1 inline-block h-1.5 w-1.5 animate-pulse rounded-full bg-current"></span>
                        Active
                    </x-exam.badge>
                </div>
                <div class="mt-2 flex flex-wrap items-center gap-3 font-mono text-[10.5px] tracking-wide text-muted">
                    <a href="{{ route('academic-subjects.show', ['academic_subject' => $topicSubject, 'academic_level' => $topicLevel, 'academic_group' => $topicGroup]) }}"
                       class="transition hover:text-ink">{{ strtoupper($topicSubject->name) }}</a>
                    <span class="text-line-strong">|</span>
                    <a href="{{ route('academic-levels.show', ['academic_level' => $topicLevel, 'academic_group' => $topicGroup]) }}"
                       class="transition hover:text-ink">{{ strtoupper($topicLevel->name) }}</a>
                    <span class="text-line-strong">|</span>
                    <a href="{{ route('academic-groups.show', ['academic_group' => $topicGroup]) }}"
                       class="transition hover:text-ink">{{ strtoupper($topicGroup->name) }}</a>
                </div>
            </div>

            @can('administrate')
                <div class="flex flex-wrap items-center gap-2">
                    <a href="{{ route('subtopics.create', ['academic_topic' => $academicTopic, 'academic_subject' => getRouteParameter('academic_subject'), 'academic_level' => getRouteParameter('academic_level'), 'academic_group' => getRouteParameter('academic_group')]) }}"
                       class="inline-flex items-center gap-1.5 whitespace-nowrap rounded bg-primary px-4 py-2.5 text-[13px] font-semibold tracking-wide text-on-primary transition hover:-translate-y-px hover:bg-primary-hi motion-reduce:transition-none">
                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M12 4v16m8-8H4"/></svg>
                        Add Subtopic
                    </a>

                    <a href="{{ route('questions.import.form', ['academic_topic' => $academicTopic, 'academic_subject' => $topicSubject, 'academic_level' => $topicLevel, 'academic_group' => $topicGroup]) }}"
                       class="inline-flex items-center gap-1.5 whitespace-nowrap rounded border border-ok/40 px-4 py-2.5 font-mono text-[10.5px] font-semibold uppercase tracking-wider text-ok transition hover:bg-ok/10">
                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                        Import
                    </a>

                    {{-- Preserved from source: subject-wide import is intentionally not surfaced on this page --}}
                    <a href="{{ route('questions.subject.import.form', ['academic_subject' => $topicSubject, 'academic_level' => $topicLevel, 'academic_group' => $topicGroup]) }}"
                       class="hidden">Import Subject Questions</a>

                    <a href="{{ route('academic-topics.edit', ['academic_topic' => $academicTopic, 'academic_subject' => getRouteParameter('academic_subject'), 'academic_level' => getRouteParameter('academic_level'), 'academic_group' => getRouteParameter('academic_group')]) }}"
                       class="inline-flex items-center gap-1.5 whitespace-nowrap rounded border border-warn/40 px-4 py-2.5 font-mono text-[10.5px] font-semibold uppercase tracking-wider text-warn transition hover:bg-warn/10">
                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        Edit
                    </a>

                    <button type="button"
                            x-data="{}"
                            x-on:click="$store.deleteForm.show('Delete Topic', 'Are you sure you want to delete {{ $academicTopic->name }}? This action cannot be undone.', '{{ route('academic-topics.destroy', ['academic_topic' => $academicTopic, 'academic_subject' => getRouteParameter('academic_subject'), 'academic_level' => getRouteParameter('academic_level'), 'academic_group' => getRouteParameter('academic_group')]) }}')"
                            class="inline-flex items-center gap-1.5 whitespace-nowrap rounded border border-accent/40 px-4 py-2.5 font-mono text-[10.5px] font-semibold uppercase tracking-wider text-accent transition hover:bg-accent/10">
                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        Delete
                    </button>
                </div>
            @endcan
        </div>

        <x-exam.flash />

        {{-- ============ Question bank census ============ --}}
        <div class="mb-4 grid grid-cols-2 divide-x divide-y divide-line rounded-md border border-line bg-surface sm:grid-cols-4 sm:divide-y-0">
            <div class="px-4 py-3.5 text-center">
                <p class="font-display text-2xl font-bold text-ink">{{ number_format($bankTotal) }}</p>
                <p class="mt-0.5 font-mono text-[9px] tracking-[0.18em] text-muted sm:text-[9.5px]">TOTAL QUESTIONS</p>
            </div>
            <div class="px-4 py-3.5 text-center">
                <p class="font-display text-2xl font-bold text-primary">{{ number_format($mcqCount) }}</p>
                <p class="mt-0.5 font-mono text-[9px] tracking-[0.18em] text-muted sm:text-[9.5px]">MULTIPLE CHOICE</p>
            </div>
            <div class="px-4 py-3.5 text-center">
                <p class="font-display text-2xl font-bold text-ok">{{ number_format($tfCount) }}</p>
                <p class="mt-0.5 font-mono text-[9px] tracking-[0.18em] text-muted sm:text-[9.5px]">TRUE / FALSE</p>
            </div>
            <div class="px-4 py-3.5 text-center">
                <p class="font-display text-2xl font-bold text-warn">{{ number_format($essayCount) }}</p>
                <p class="mt-0.5 font-mono text-[9px] tracking-[0.18em] text-muted sm:text-[9.5px]">ESSAY</p>
            </div>
        </div>

        {{-- Composition --}}
        @if($bankTotal > 0)
            <div class="mb-8 rounded-md border border-line bg-surface p-4">
                <div class="mb-2 flex items-baseline justify-between font-mono text-[10px] tracking-[0.14em] text-muted">
                    <span>BANK COMPOSITION</span>
                    <span>{{ number_format($bankTotal) }} ITEM{{ $bankTotal === 1 ? '' : 'S' }}</span>
                </div>
                <div class="flex h-2.5 w-full overflow-hidden rounded-full bg-line">
                    @foreach($bankRows as $row)
                        @if($row['count'] > 0)
                            <div class="{{ $toneFill[$row['tone']] }} h-full transition-all duration-500 motion-reduce:transition-none"
                                 style="width: {{ $share($row['count']) }}%"
                                 title="{{ $row['label'] }} — {{ $row['count'] }}"></div>
                        @endif
                    @endforeach
                </div>
                <div class="mt-2.5 flex flex-wrap items-center gap-x-5 gap-y-1.5">
                    @foreach($bankRows as $row)
                        <span class="flex items-center gap-1.5 font-mono text-[10.5px] tracking-wide text-muted">
                            <span class="h-2 w-2 rounded-full {{ $toneFill[$row['tone']] }}"></span>
                            {{ strtoupper($row['label']) }}
                            <b class="font-semibold text-ink">{{ $share($row['count']) }}%</b>
                        </span>
                    @endforeach
                </div>
            </div>
        @else
            <div class="mb-8"></div>
        @endif

        <div class="grid gap-8 lg:grid-cols-[minmax(0,1fr)_320px]">

            {{-- ============ Registers ============ --}}
            <div class="space-y-6">
                @can('moderate')
                    {{-- Question bank --}}
                    <x-exam.card :padded="false">
                        <x-slot:header>
                            <p class="flex items-center gap-2 font-mono text-[10.5px] tracking-[0.14em] text-muted">
                                <span class="h-2 w-2 bg-primary"></span>QUESTION BANK
                            </p>
                            <p class="font-mono text-[10px] tracking-wider text-muted">{{ number_format($bankTotal) }} TOTAL</p>
                        </x-slot:header>

                        <div class="space-y-2.5 p-5">
                            @foreach($bankRows as $row)
                                <a href="{{ $row['href'] }}"
                                   class="group flex items-center gap-4 rounded-md border border-line bg-surface-2 p-4 transition hover:-translate-y-px hover:border-primary/40 hover:bg-surface motion-reduce:transition-none">
                                    <span class="grid h-11 w-11 shrink-0 place-items-center rounded-md font-mono text-[10.5px] font-bold {{ $toneChip[$row['tone']] }}">
                                        {{ $row['stamp'] }}
                                    </span>
                                    <div class="min-w-0 flex-1">
                                        <p class="truncate font-display text-base font-bold text-ink transition group-hover:text-primary">{{ $row['label'] }}</p>
                                        <p class="mt-0.5 font-mono text-[10px] tracking-wide text-muted">{{ strtoupper($row['note']) }}</p>
                                    </div>
                                    <div class="shrink-0 text-right">
                                        <p class="font-display text-2xl font-bold {{ $row['count'] > 0 ? $toneText[$row['tone']] : 'text-muted' }}">
                                            {{ number_format($row['count']) }}
                                        </p>
                                        <p class="font-mono text-[9px] tracking-[0.16em] text-muted">{{ $row['count'] === 1 ? 'QUESTION' : 'QUESTIONS' }}</p>
                                    </div>
                                    <svg class="h-4 w-4 shrink-0 text-muted transition group-hover:translate-x-0.5 group-hover:text-primary motion-reduce:transition-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                </a>
                            @endforeach

                            @if($bankTotal === 0)
                                <div class="rounded-md border border-dashed border-line-strong bg-surface-2 px-4 py-5 text-center">
                                    <p class="font-display text-base font-bold text-ink">This bank is empty</p>
                                    <p class="mx-auto mt-1 max-w-sm text-xs leading-relaxed text-ink-soft">
                                        Author questions in any of the three registers above, or bulk-import an existing paper.
                                    </p>
                                    <a href="{{ route('questions.import.form', ['academic_topic' => $academicTopic, 'academic_subject' => $topicSubject, 'academic_level' => $topicLevel, 'academic_group' => $topicGroup]) }}"
                                       class="mt-4 inline-flex items-center gap-1.5 whitespace-nowrap rounded bg-primary px-4 py-2 font-mono text-[10.5px] font-semibold uppercase tracking-wider text-on-primary transition hover:bg-primary-hi">
                                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                                        Import Questions
                                    </a>
                                </div>
                            @endif
                        </div>
                    </x-exam.card>

                    {{-- Content & materials --}}
                    <x-exam.card :padded="false">
                        <x-slot:header>
                            <p class="flex items-center gap-2 font-mono text-[10.5px] tracking-[0.14em] text-muted">
                                <span class="h-2 w-2 bg-warn"></span>CONTENT &amp; MATERIALS
                            </p>
                        </x-slot:header>

                        <ul class="divide-y divide-line">
                            <li>
                                <a href="{{ route('subtopics.index', ['academic_topic' => $academicTopic, 'academic_subject' => getRouteParameter('academic_subject'), 'academic_level' => getRouteParameter('academic_level'), 'academic_group' => getRouteParameter('academic_group')]) }}"
                                   class="group flex items-center gap-4 px-5 py-4 transition hover:bg-surface-2/60">
                                    <span class="grid h-10 w-10 shrink-0 place-items-center rounded-md bg-warn/10 text-warn">
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4 6h16M4 10h16M4 14h10M4 18h10"/></svg>
                                    </span>
                                    <div class="min-w-0 flex-1">
                                        <p class="font-display text-base font-bold text-ink transition group-hover:text-primary">Subtopics</p>
                                        <p class="mt-0.5 font-mono text-[10px] tracking-wide text-muted">BREAK THIS TOPIC INTO STUDY UNITS</p>
                                    </div>
                                    @if(($academicTopic->subtopics_count ?? 0) > 0)
                                        <span class="shrink-0 font-display text-xl font-bold text-warn">{{ $academicTopic->subtopics_count }}</span>
                                    @endif
                                    <svg class="h-4 w-4 shrink-0 text-muted transition group-hover:translate-x-0.5 group-hover:text-primary motion-reduce:transition-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('lesson-notes.index', ['academic_topic' => $academicTopic, 'academic_subject' => getRouteParameter('academic_subject'), 'academic_level' => getRouteParameter('academic_level'), 'academic_group' => getRouteParameter('academic_group')]) }}"
                                   class="group flex items-center gap-4 px-5 py-4 transition hover:bg-surface-2/60">
                                    <span class="grid h-10 w-10 shrink-0 place-items-center rounded-md bg-primary/10 text-primary">
                                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                                    </span>
                                    <div class="min-w-0 flex-1">
                                        <p class="font-display text-base font-bold text-ink transition group-hover:text-primary">Lesson Notes</p>
                                        <p class="mt-0.5 font-mono text-[10px] tracking-wide text-muted">STUDY MATERIALS &amp; TEACHING NOTES</p>
                                    </div>
                                    <svg class="h-4 w-4 shrink-0 text-muted transition group-hover:translate-x-0.5 group-hover:text-primary motion-reduce:transition-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                </a>
                            </li>
                        </ul>
                    </x-exam.card>

                    {{-- Import --}}
                    <x-exam.card :padded="false">
                        <x-slot:header>
                            <p class="flex items-center gap-2 font-mono text-[10.5px] tracking-[0.14em] text-muted">
                                <span class="h-2 w-2 bg-ok"></span>BULK IMPORT
                            </p>
                        </x-slot:header>
                        <a href="{{ route('questions.import.form', ['academic_topic' => $academicTopic, 'academic_subject' => $topicSubject, 'academic_level' => $topicLevel, 'academic_group' => $topicGroup]) }}"
                           class="group flex items-center gap-4 px-5 py-4 transition hover:bg-surface-2/60">
                            <span class="grid h-10 w-10 shrink-0 place-items-center rounded-md bg-ok/10 text-ok">
                                <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                            </span>
                            <div class="min-w-0 flex-1">
                                <p class="font-display text-base font-bold text-ink transition group-hover:text-primary">Import Questions</p>
                                <p class="mt-0.5 font-mono text-[10px] tracking-wide text-muted">EXCEL · WORD · PDF</p>
                            </div>
                            <svg class="h-4 w-4 shrink-0 text-muted transition group-hover:translate-x-0.5 group-hover:text-primary motion-reduce:transition-none" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </a>
                    </x-exam.card>
                @endcan

                @cannot('moderate')
                    <x-exam.callout tone="primary">
                        <p class="mb-1.5 font-mono text-[10px] font-semibold uppercase tracking-[0.14em] text-primary">Read-only access</p>
                        <p class="text-xs leading-relaxed text-ink-soft">
                            You can review this topic's census and position in the curriculum. Authoring question banks,
                            subtopics and lesson notes requires moderator permission.
                        </p>
                    </x-exam.callout>
                @endcannot
            </div>

            {{-- ============ Rail ============ --}}
            <aside class="space-y-6">
                <x-exam.card :padded="false">
                    <x-slot:header>
                        <p class="font-mono text-[10.5px] tracking-[0.14em] text-muted">TOPIC RECORD</p>
                        <p class="font-mono text-[10px] tracking-wider text-muted">#{{ $academicTopic->id }}</p>
                    </x-slot:header>
                    <div class="space-y-4 p-5 font-mono text-[11px] tracking-wide">
                        <div class="flex items-baseline justify-between gap-2 text-muted">
                            <span>NAME</span>
                            <span class="flex-1 border-b border-dotted border-line-strong"></span>
                            <span class="text-right font-semibold text-ink">{{ $academicTopic->name }}</span>
                        </div>
                        <div class="flex items-baseline justify-between gap-2 text-muted">
                            <span>SUBJECT</span>
                            <span class="flex-1 border-b border-dotted border-line-strong"></span>
                            <a href="{{ route('academic-subjects.show', ['academic_subject' => $topicSubject, 'academic_level' => $topicLevel, 'academic_group' => $topicGroup]) }}"
                               class="text-right font-semibold text-primary transition hover:text-primary-hi">{{ $topicSubject->name }}</a>
                        </div>
                        <div class="flex items-baseline justify-between gap-2 text-muted">
                            <span>LEVEL</span>
                            <span class="flex-1 border-b border-dotted border-line-strong"></span>
                            <a href="{{ route('academic-levels.show', ['academic_level' => $topicLevel, 'academic_group' => $topicGroup]) }}"
                               class="text-right font-semibold text-primary transition hover:text-primary-hi">{{ $topicLevel->name }}</a>
                        </div>
                        <div class="flex items-baseline justify-between gap-2 text-muted">
                            <span>GROUP</span>
                            <span class="flex-1 border-b border-dotted border-line-strong"></span>
                            <a href="{{ route('academic-groups.show', ['academic_group' => $topicGroup]) }}"
                               class="text-right font-semibold text-primary transition hover:text-primary-hi">{{ $topicGroup->name }}</a>
                        </div>
                        <div class="flex items-baseline justify-between gap-2 text-muted">
                            <span>SUBTOPICS</span>
                            <span class="flex-1 border-b border-dotted border-line-strong"></span>
                            <span class="font-semibold {{ ($academicTopic->subtopics_count ?? 0) > 0 ? 'text-ink' : 'text-muted' }}">
                                {{ ($academicTopic->subtopics_count ?? 0) > 0 ? $academicTopic->subtopics_count : 'NONE' }}
                            </span>
                        </div>
                        <div class="flex items-baseline justify-between gap-2 text-muted">
                            <span>CREATED</span>
                            <span class="flex-1 border-b border-dotted border-line-strong"></span>
                            <span class="font-semibold text-ink">{{ $academicTopic->created_at->format('M d, Y') }}</span>
                        </div>
                        <div class="flex items-baseline justify-between gap-2 text-muted">
                            <span>UPDATED</span>
                            <span class="flex-1 border-b border-dotted border-line-strong"></span>
                            <span class="font-semibold text-ink">{{ strtoupper($academicTopic->updated_at->diffForHumans()) }}</span>
                        </div>
                    </div>
                </x-exam.card>

                <x-exam.card :padded="false">
                    <x-slot:header>
                        <p class="font-mono text-[10.5px] tracking-[0.14em] text-muted">QUICK NAVIGATION</p>
                    </x-slot:header>
                    <ul class="divide-y divide-line">
                        <li>
                            <a href="{{ route('academic-topics.index', ['academic_subject' => $topicSubject, 'academic_level' => $topicLevel, 'academic_group' => $topicGroup]) }}"
                               class="flex items-center justify-between px-5 py-3 text-sm font-semibold text-ink-soft transition hover:bg-surface-2 hover:text-ink">
                                All Topics
                                <svg class="h-4 w-4 text-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('academic-subjects.show', ['academic_subject' => $topicSubject, 'academic_level' => $topicLevel, 'academic_group' => $topicGroup]) }}"
                               class="flex items-center justify-between px-5 py-3 text-sm font-semibold text-ink-soft transition hover:bg-surface-2 hover:text-ink">
                                Back to Subject
                                <svg class="h-4 w-4 text-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                            </a>
                        </li>
                        @can('moderate')
                            <li>
                                <a href="{{ route('subtopics.index', ['academic_topic' => $academicTopic, 'academic_subject' => getRouteParameter('academic_subject'), 'academic_level' => getRouteParameter('academic_level'), 'academic_group' => getRouteParameter('academic_group')]) }}"
                                   class="flex items-center justify-between px-5 py-3 text-sm font-semibold text-ink-soft transition hover:bg-surface-2 hover:text-ink">
                                    Manage Subtopics
                                    <svg class="h-4 w-4 text-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                </a>
                            </li>
                        @endcan
                        @can('administrate')
                            <li>
                                <a href="{{ route('subtopics.create', ['academic_topic' => $academicTopic, 'academic_subject' => getRouteParameter('academic_subject'), 'academic_level' => getRouteParameter('academic_level'), 'academic_group' => getRouteParameter('academic_group')]) }}"
                                   class="flex items-center justify-between px-5 py-3 text-sm font-semibold text-primary transition hover:bg-surface-2 hover:text-primary-hi">
                                    Add Subtopic
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                </a>
                            </li>
                        @endcan
                    </ul>
                </x-exam.card>

                <x-exam.callout tone="primary">
                    <p class="mb-1.5 font-mono text-[10px] font-semibold uppercase tracking-[0.14em] text-primary">How this feeds assessment</p>
                    <p class="text-xs leading-relaxed text-ink-soft">
                        Questions held in this topic's three registers are the pool drawn on when quizzes and examinations
                        are generated for {{ $topicSubject->name }}. A balanced mix across choice, judgement and extended
                        response produces the most reliable assessment.
                    </p>
                </x-exam.callout>
            </aside>
        </div>
    </x-exam.page>
</x-layouts.app>
