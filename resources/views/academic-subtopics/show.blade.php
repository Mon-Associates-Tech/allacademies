<x-layouts.app title="{{ $academic_subtopic->name }} · Subtopic" :has-action="false" :show-title-area="false">
    <x-exam.page>
        @php
            $leafSubtopic = $academic_subtopic;
            $leafTopic    = $academicTopic;
            $leafSubject  = $academicTopic->academicSubject;
            $leafLevel    = $leafSubject->academicLevel;
            $leafGroup    = $leafLevel->academicGroup;

            $topicUrl = route('academic-topics.show', [
                'academic_topic'   => getRouteParameter('academic_topic'),
                'academic_subject' => getRouteParameter('academic_subject'),
                'academic_level'   => $leafLevel,
                'academic_group'   => getRouteParameter('academic_group'),
            ]);

            $subjectUrl = route('academic-subjects.show', [
                'academic_subject' => getRouteParameter('academic_subject'),
                'academic_level'   => getRouteParameter('academic_level'),
                'academic_group'   => getRouteParameter('academic_group'),
            ]);

            $siblingsUrl = route('subtopics.index', [
                'academic_topic'   => $academicTopic,
                'academic_subject' => getRouteParameter('academic_subject'),
                'academic_level'   => getRouteParameter('academic_level'),
                'academic_group'   => getRouteParameter('academic_group'),
            ]);

            $editUrl = route('subtopics.edit', [
                'academic_topic'   => $academicTopic,
                'subtopic'         => $academic_subtopic,
                'academic_subject' => getRouteParameter('academic_subject'),
                'academic_level'   => getRouteParameter('academic_level'),
                'academic_group'   => getRouteParameter('academic_group'),
            ]);

            $deleteUrl = route('subtopics.destroy', [
                'subtopic'         => $academic_subtopic,
                'academic_topic'   => getRouteParameter('academic_topic'),
                'academic_subject' => getRouteParameter('academic_subject'),
                'academic_level'   => getRouteParameter('academic_level'),
                'academic_group'   => getRouteParameter('academic_group'),
            ]);

            $bankParams = [
                'academic_topic'   => getRouteParameter('academic_topic'),
                'academic_subject' => getRouteParameter('academic_subject'),
                'academic_level'   => $leafLevel,
                'academic_group'   => getRouteParameter('academic_group'),
            ];

            $mcqCount   = (int) ($academicTopic->multiple_choice_questions_count ?? 0);
            $tfCount    = (int) ($academicTopic->true_or_false_questions_count ?? 0);
            $essayCount = (int) ($academicTopic->essay_questions_count ?? 0);
            $bankTotal  = $mcqCount + $tfCount + $essayCount;
            $share      = fn ($n) => $bankTotal > 0 ? round($n / $bankTotal * 100, 1) : 0;

            $bankRows = [
                [
                    'stamp' => 'MCQ',
                    'label' => 'Multiple Choice',
                    'note'  => 'Single answer from an option set',
                    'count' => $mcqCount,
                    'tone'  => 'primary',
                    'href'  => route('multiple-choice-questions.index', $bankParams),
                ],
                [
                    'stamp' => 'T/F',
                    'label' => 'True or False',
                    'note'  => 'Binary statement judgement',
                    'count' => $tfCount,
                    'tone'  => 'ok',
                    'href'  => route('true-or-false-questions.index', $bankParams),
                ],
                [
                    'stamp' => 'ESY',
                    'label' => 'Essay',
                    'note'  => 'Extended written response',
                    'count' => $essayCount,
                    'tone'  => 'warn',
                    'href'  => route('essay-questions.index', $bankParams),
                ],
            ];

            $toneText = ['primary' => 'text-primary', 'ok' => 'text-ok', 'warn' => 'text-warn'];
            $toneFill = ['primary' => 'bg-primary', 'ok' => 'bg-ok', 'warn' => 'bg-warn'];
            $toneChip = ['primary' => 'bg-primary/10 text-primary', 'ok' => 'bg-ok/10 text-ok', 'warn' => 'bg-warn/10 text-warn'];

            $lineage = [
                ['kind' => 'Group',   'name' => $leafGroup->name,   'href' => route('academic-groups.show', ['academic_group' => $leafGroup])],
                ['kind' => 'Level',   'name' => $leafLevel->name,   'href' => route('academic-levels.show', ['academic_level' => $leafLevel, 'academic_group' => getRouteParameter('academic_group')])],
                ['kind' => 'Subject', 'name' => $leafSubject->name, 'href' => $subjectUrl],
                ['kind' => 'Topic',   'name' => $leafTopic->name,   'href' => $topicUrl],
            ];
        @endphp

        {{-- Breadcrumb --}}
        <x-exam.crumbs :items="[
            ['label' => 'Academic Groups', 'href' => route('academic-groups.index')],
            ['label' => $leafGroup->name, 'href' => route('academic-groups.show', ['academic_group' => $leafGroup])],
            ['label' => 'Academic Levels', 'href' => route('academic-levels.index', ['academic_group' => $leafGroup])],
            ['label' => $leafLevel->name, 'href' => route('academic-levels.show', ['academic_level' => $leafLevel, 'academic_group' => getRouteParameter('academic_group')])],
            ['label' => 'Subjects', 'href' => route('academic-subjects.index', ['academic_level' => $leafLevel, 'academic_group' => getRouteParameter('academic_group')])],
            ['label' => $leafSubject->name, 'href' => $subjectUrl],
            ['label' => 'Topics', 'href' => route('academic-topics.index', ['academic_subject' => $leafSubject, 'academic_level' => getRouteParameter('academic_level'), 'academic_group' => getRouteParameter('academic_group')])],
            ['label' => $leafTopic->name, 'href' => $topicUrl],
            ['label' => 'Subtopics', 'href' => $siblingsUrl],
            ['label' => $leafSubtopic->name],
        ]" />

        {{-- ============ Header ============ --}}
        <div class="mb-6 flex flex-wrap items-start justify-between gap-4">
            <div class="min-w-0">
                <div class="flex flex-wrap items-center gap-2.5">
                    <span class="grid h-11 w-11 shrink-0 place-items-center rounded-md bg-accent/10 font-mono text-[11px] font-bold text-accent">
                        {{ strtoupper(substr($leafSubtopic->name, 0, 3)) }}
                    </span>
                    <h1 class="font-display text-2xl font-bold leading-tight text-ink md:text-3xl">{{ $leafSubtopic->name }}</h1>
                    <x-exam.badge tone="ok">
                        <span class="mr-1 inline-block h-1.5 w-1.5 animate-pulse rounded-full bg-current"></span>
                        Active
                    </x-exam.badge>
                </div>
                <div class="mt-2 flex flex-wrap items-center gap-3 font-mono text-[10.5px] tracking-wide text-muted">
                    <span>LEAF RECORD</span>
                    <span class="text-line-strong">|</span>
                    <a href="{{ $topicUrl }}" class="transition hover:text-ink">{{ strtoupper($leafTopic->name) }}</a>
                    <span class="text-line-strong">|</span>
                    <a href="{{ $subjectUrl }}" class="transition hover:text-ink">{{ strtoupper($leafSubject->name) }}</a>
                </div>
            </div>

            @can('administrate')
                <div class="flex flex-wrap items-center gap-2">
                    <a href="{{ $editUrl }}"
                       class="inline-flex items-center gap-1.5 whitespace-nowrap rounded border border-warn/40 px-4 py-2.5 font-mono text-[10.5px] font-semibold uppercase tracking-wider text-warn transition hover:bg-warn/10">
                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        Edit Subtopic
                    </a>
                    <button type="button"
                            x-data="{}"
                            x-on:click="$store.deleteForm.show('Delete Subtopic', 'Are you sure you want to delete {{ $leafSubtopic->name }}? This action cannot be undone.', '{{ $deleteUrl }}')"
                            class="inline-flex items-center gap-1.5 whitespace-nowrap rounded border border-accent/40 px-4 py-2.5 font-mono text-[10.5px] font-semibold uppercase tracking-wider text-accent transition hover:bg-accent/10">
                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        Delete
                    </button>
                </div>
            @endcan
        </div>

        <x-exam.flash />

        {{-- ============ Bank census ============ --}}
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

            {{-- ============ Main column ============ --}}
            <div class="space-y-6">

                {{-- Description --}}
                @if(!empty($leafSubtopic->description))
                    <x-exam.card class="animate-rise" :padded="false">
                        <x-slot:header>
                            <p class="flex items-center gap-2 font-mono text-[10.5px] tracking-[0.14em] text-muted">
                                <span class="h-2 w-2 bg-accent"></span>TEACHING SCOPE
                            </p>
                        </x-slot:header>
                        <div class="p-5">
                            <p class="text-sm leading-relaxed text-ink-soft">{{ $leafSubtopic->description }}</p>
                        </div>
                    </x-exam.card>
                @endif

                {{-- Question bank --}}
                <x-exam.card class="animate-rise" style="animation-delay:60ms" :padded="false">
                    <x-slot:header>
                        <p class="flex items-center gap-2 font-mono text-[10.5px] tracking-[0.14em] text-muted">
                            <span class="h-2 w-2 bg-primary"></span>QUESTION BANK
                        </p>
                        <p class="font-mono text-[10px] tracking-wider text-muted">HELD AT TOPIC LEVEL</p>
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
                                <p class="font-display text-base font-bold text-ink">No questions filed yet</p>
                                <p class="mx-auto mt-1 max-w-sm text-xs leading-relaxed text-ink-soft">
                                    {{ $leafTopic->name }} has an empty bank. Author questions in any of the three registers above
                                    to make this subtopic assessable.
                                </p>
                            </div>
                        @endif

                        <div class="rounded-md border border-line bg-surface-2 px-4 py-3">
                            <p class="font-mono text-[10px] leading-relaxed tracking-wide text-muted">
                                QUESTIONS ARE FILED AGAINST THE PARENT TOPIC. THIS SUBTOPIC SHARES THAT POOL WHEN QUIZZES
                                AND EXAMINATIONS ARE GENERATED FOR {{ strtoupper($leafSubject->name) }}.
                            </p>
                        </div>
                    </div>
                </x-exam.card>
            </div>

            {{-- ============ Rail ============ --}}
            <aside class="space-y-6 lg:sticky lg:top-7 lg:self-start">

                {{-- Curriculum placement --}}
                <x-exam.card class="animate-rise" style="animation-delay:120ms" :padded="false">
                    <x-slot:header>
                        <p class="font-mono text-[10.5px] tracking-[0.14em] text-muted">CURRICULUM PLACEMENT</p>
                    </x-slot:header>
                    <div class="p-5">
                        <div class="relative pl-6">
                            <span class="absolute bottom-7 left-[5px] top-2 w-px bg-line-strong"></span>

                            @foreach($lineage as $node)
                                <div class="relative pb-4">
                                    <span class="absolute -left-6 top-1 h-2.5 w-2.5 rounded-full border-2 border-line-strong bg-surface"></span>
                                    <p class="font-mono text-[9px] uppercase tracking-[0.18em] text-muted">{{ $node['kind'] }}</p>
                                    <a href="{{ $node['href'] }}"
                                       class="mt-0.5 block truncate text-sm font-semibold text-ink-soft transition hover:text-primary">
                                        {{ $node['name'] }}
                                    </a>
                                </div>
                            @endforeach

                            <div class="relative">
                                <span class="absolute -left-6 top-1 h-2.5 w-2.5 rounded-full border-2 border-accent bg-accent"></span>
                                <p class="font-mono text-[9px] uppercase tracking-[0.18em] text-accent">Subtopic · You are here</p>
                                <p class="mt-0.5 truncate text-sm font-bold text-ink">{{ $leafSubtopic->name }}</p>
                            </div>
                        </div>
                    </div>
                </x-exam.card>

                {{-- Record details --}}
                <x-exam.card class="animate-rise" style="animation-delay:180ms" :padded="false">
                    <x-slot:header>
                        <p class="font-mono text-[10.5px] tracking-[0.14em] text-muted">SUBTOPIC RECORD</p>
                        <p class="font-mono text-[10px] tracking-wider text-muted">#{{ $leafSubtopic->id }}</p>
                    </x-slot:header>
                    <div class="space-y-4 p-5 font-mono text-[11px] tracking-wide">
                        <div class="flex items-baseline justify-between gap-2 text-muted">
                            <span>NAME</span>
                            <span class="flex-1 border-b border-dotted border-line-strong"></span>
                            <span class="text-right font-semibold text-ink">{{ $leafSubtopic->name }}</span>
                        </div>
                        <div class="flex items-baseline justify-between gap-2 text-muted">
                            <span>PARENT TOPIC</span>
                            <span class="flex-1 border-b border-dotted border-line-strong"></span>
                            <a href="{{ $topicUrl }}" class="text-right font-semibold text-primary transition hover:text-primary-hi">{{ $leafTopic->name }}</a>
                        </div>
                        <div class="flex items-baseline justify-between gap-2 text-muted">
                            <span>SUBJECT</span>
                            <span class="flex-1 border-b border-dotted border-line-strong"></span>
                            <a href="{{ $subjectUrl }}" class="text-right font-semibold text-primary transition hover:text-primary-hi">{{ $leafSubject->name }}</a>
                        </div>
                        <div class="flex items-baseline justify-between gap-2 text-muted">
                            <span>SIBLINGS</span>
                            <span class="flex-1 border-b border-dotted border-line-strong"></span>
                            <span class="font-semibold {{ ($leafTopic->subtopics_count ?? 0) > 1 ? 'text-ink' : 'text-muted' }}">
                                {{ max(0, (int) ($leafTopic->subtopics_count ?? 1) - 1) }}
                            </span>
                        </div>
                        <div class="flex items-baseline justify-between gap-2 text-muted">
                            <span>CREATED</span>
                            <span class="flex-1 border-b border-dotted border-line-strong"></span>
                            <span class="font-semibold text-ink">{{ $leafSubtopic->created_at->format('M d, Y') }}</span>
                        </div>
                        <div class="flex items-baseline justify-between gap-2 text-muted">
                            <span>FILED</span>
                            <span class="flex-1 border-b border-dotted border-line-strong"></span>
                            <span class="font-semibold text-ink">{{ strtoupper($leafSubtopic->created_at->diffForHumans()) }}</span>
                        </div>
                        @if($leafSubtopic->updated_at && $leafSubtopic->updated_at->ne($leafSubtopic->created_at))
                            <div class="flex items-baseline justify-between gap-2 text-muted">
                                <span>UPDATED</span>
                                <span class="flex-1 border-b border-dotted border-line-strong"></span>
                                <span class="font-semibold text-ink">{{ strtoupper($leafSubtopic->updated_at->diffForHumans()) }}</span>
                            </div>
                        @endif
                    </div>
                </x-exam.card>

                {{-- Quick navigation --}}
                <x-exam.card class="animate-rise" style="animation-delay:240ms" :padded="false">
                    <x-slot:header>
                        <p class="font-mono text-[10.5px] tracking-[0.14em] text-muted">QUICK NAVIGATION</p>
                    </x-slot:header>
                    <ul class="divide-y divide-line">
                        <li>
                            <a href="{{ $siblingsUrl }}"
                               class="flex items-center justify-between px-5 py-3 text-sm font-semibold text-ink-soft transition hover:bg-surface-2 hover:text-ink">
                                All Subtopics
                                <svg class="h-4 w-4 text-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                            </a>
                        </li>
                        <li>
                            <a href="{{ $topicUrl }}"
                               class="flex items-center justify-between px-5 py-3 text-sm font-semibold text-ink-soft transition hover:bg-surface-2 hover:text-ink">
                                Parent Topic
                                <svg class="h-4 w-4 text-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                            </a>
                        </li>
                        <li>
                            <a href="{{ $subjectUrl }}"
                               class="flex items-center justify-between px-5 py-3 text-sm font-semibold text-ink-soft transition hover:bg-surface-2 hover:text-ink">
                                Subject Record
                                <svg class="h-4 w-4 text-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                            </a>
                        </li>
                        @can('administrate')
                            <li>
                                <a href="{{ $editUrl }}"
                                   class="flex items-center justify-between px-5 py-3 text-sm font-semibold text-warn transition hover:bg-surface-2">
                                    Edit This Subtopic
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                </a>
                            </li>
                        @endcan
                    </ul>
                </x-exam.card>
            </aside>
        </div>

        <style>
            [x-cloak] { display: none !important; }
        </style>
    </x-exam.page>
</x-layouts.app>
