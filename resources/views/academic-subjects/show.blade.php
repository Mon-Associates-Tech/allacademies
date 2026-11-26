<x-layouts.app title="Academic Subject Details" :has-action="false">
    <x-exam.page>
        @php
            $subjectLevel = $academicSubject->academicLevel;
            $subjectGroup = $academicSubject->academicLevel->academicGroup;
        @endphp

        {{-- Breadcrumb --}}
        <x-exam.crumbs :items="[
            ['label' => 'Academic Groups', 'href' => route('academic-groups.index')],
            ['label' => $subjectGroup->name, 'href' => route('academic-groups.show', ['academic_group' => $subjectGroup])],
            ['label' => 'Academic Levels', 'href' => route('academic-levels.index', ['academic_group' => $subjectGroup])],
            ['label' => $subjectLevel->name, 'href' => route('academic-levels.show', ['academic_level' => $subjectLevel, 'academic_group' => $subjectGroup])],
            ['label' => 'Subjects', 'href' => route('academic-subjects.index', ['academic_level' => $subjectLevel, 'academic_group' => $subjectGroup])],
            ['label' => $academicSubject->name],
        ]" />

        {{-- ============ Header ============ --}}
        <div class="mb-6 flex flex-wrap items-start justify-between gap-4">
            <div class="min-w-0">
                <div class="flex flex-wrap items-center gap-2.5">
                    <h1 class="font-display text-2xl font-bold text-ink md:text-3xl">{{ $academicSubject->name }}</h1>
                    @if($academicSubject->code)
                        <span class="rounded-[3px] bg-accent/10 px-2 py-1 font-mono text-[10.5px] font-semibold tracking-[0.12em] text-accent">
                            {{ strtoupper($academicSubject->code) }}
                        </span>
                    @endif
                    <x-exam.badge tone="ok">
                        <span class="mr-1 inline-block h-1.5 w-1.5 animate-pulse rounded-full bg-current"></span>
                        Active
                    </x-exam.badge>
                </div>
                <div class="mt-2 flex flex-wrap items-center gap-3 font-mono text-[10.5px] tracking-wide text-muted">
                    <a href="{{ route('academic-levels.show', ['academic_level' => $subjectLevel, 'academic_group' => $subjectGroup]) }}"
                       class="transition hover:text-ink">
                        {{ strtoupper($subjectLevel->name) }}
                    </a>
                    <span class="text-line-strong">|</span>
                    <a href="{{ route('academic-groups.show', ['academic_group' => $subjectGroup]) }}"
                       class="transition hover:text-ink">
                        {{ strtoupper($subjectGroup->name) }}
                    </a>
                </div>
            </div>

            @can('administrate')
                <div class="flex flex-wrap items-center gap-2">
                    <a href="{{ route('academic-topics.create', ['academic_subject' => $academicSubject, 'academic_level' => $subjectLevel, 'academic_group' => $subjectGroup]) }}"
                       class="inline-flex items-center gap-1.5 whitespace-nowrap rounded bg-primary px-4 py-2.5 text-[13px] font-semibold tracking-wide text-on-primary transition hover:-translate-y-px hover:bg-primary-hi motion-reduce:transition-none">
                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M12 4v16m8-8H4"/></svg>
                        Add Topic
                    </a>

                    <a href="{{ route('questions.subject.import.form', ['academic_subject' => $academicSubject, 'academic_level' => $subjectLevel, 'academic_group' => $subjectGroup]) }}"
                       class="inline-flex items-center gap-1.5 whitespace-nowrap rounded border border-ok/40 px-4 py-2.5 font-mono text-[10.5px] font-semibold uppercase tracking-wider text-ok transition hover:bg-ok/10">
                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 0115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                        Import Questions
                    </a>

                    <a href="{{ route('academic-subjects.edit', ['academic_subject' => $academicSubject, 'academic_level' => $subjectLevel, 'academic_group' => $subjectGroup]) }}"
                       class="inline-flex items-center gap-1.5 whitespace-nowrap rounded border border-warn/40 px-4 py-2.5 font-mono text-[10.5px] font-semibold uppercase tracking-wider text-warn transition hover:bg-warn/10">
                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        Edit
                    </a>

                    <button type="button"
                            x-data="{}"
                            x-on:click="$store.deleteForm.show('Delete Subject', 'Are you sure you want to delete {{ $academicSubject->name }}? This action cannot be undone.', '{{ route('academic-subjects.destroy', ['academic_subject' => $academicSubject, 'academic_level' => $subjectLevel, 'academic_group' => $subjectGroup]) }}')"
                            class="inline-flex items-center gap-1.5 whitespace-nowrap rounded border border-accent/40 px-4 py-2.5 font-mono text-[10.5px] font-semibold uppercase tracking-wider text-accent transition hover:bg-accent/10">
                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        Delete
                    </button>
                </div>
            @endcan
        </div>

        <x-exam.flash />

        <div class="grid gap-8 lg:grid-cols-[minmax(0,1fr)_320px]">

            {{-- ============ Topic register ============ --}}
            <div>
                <div class="mb-5 flex flex-wrap items-end justify-between gap-3">
                    <div>
                        <p class="mb-1 font-mono text-[10.5px] tracking-[0.14em] text-muted">CURRICULUM REGISTER</p>
                        <h2 class="font-display text-xl font-bold text-ink">Topics</h2>
                    </div>
                    <p class="font-mono text-[10.5px] tracking-wider text-muted">
                        {{ $topics->total() }} {{ Str::plural('TOPIC', $topics->total()) }}
                    </p>
                </div>

                @if($topics->count() > 0)
                    <div class="space-y-3">
                        @foreach($topics as $topic)
                            <article class="group rounded-md border border-line bg-surface p-5 shadow-[0_1px_2px_rgba(20,24,34,0.04)] transition hover:border-primary/40 hover:shadow-[0_10px_30px_-12px_rgba(20,24,34,0.30)] motion-reduce:transition-none">
                                <div class="flex flex-wrap items-center justify-between gap-4">
                                    {{-- Identity --}}
                                    <div class="flex min-w-0 flex-1 items-center gap-4">
                                        <span class="grid h-10 w-10 shrink-0 place-items-center rounded-md bg-primary/10 font-mono text-xs font-bold text-primary">
                                            {{ str_pad($topics->firstItem() + $loop->index, 2, '0', STR_PAD_LEFT) }}
                                        </span>
                                        <div class="min-w-0 flex-1">
                                            <h3 class="truncate font-display text-base font-bold text-ink transition group-hover:text-primary">
                                                {{ $topic->name }}
                                            </h3>

                                            <div class="mt-2 max-w-xs">
                                                <div class="flex items-baseline gap-2 font-mono text-[11px] tracking-wide text-muted">
                                                    <span>SUBTOPICS</span>
                                                    <span class="flex-1 -translate-y-[3px] border-b border-dotted border-line-strong"></span>
                                                    <span class="font-semibold {{ $topic->subtopics_count > 0 ? 'text-ink' : 'text-muted' }}">
                                                        {{ $topic->subtopics_count > 0 ? $topic->subtopics_count : 'NONE' }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Actions --}}
                                    <div class="flex shrink-0 items-center gap-2">
                                        <a href="{{ route('academic-topics.show', ['academic_topic' => $topic, 'academic_subject' => $academicSubject, 'academic_level' => $subjectLevel, 'academic_group' => $subjectGroup]) }}"
                                           class="inline-flex items-center justify-center rounded border border-line-strong px-3 py-1.5 font-mono text-[10.5px] font-semibold uppercase tracking-wider text-ink-soft transition hover:bg-surface-2 hover:text-ink">
                                            View
                                        </a>
                                        @can('administrate')
                                            <a href="{{ route('academic-topics.edit', ['academic_topic' => $topic, 'academic_subject' => $academicSubject, 'academic_level' => $subjectLevel, 'academic_group' => $subjectGroup]) }}"
                                               title="Edit topic"
                                               class="inline-flex items-center justify-center rounded border border-warn/40 px-2.5 py-1.5 text-warn transition hover:bg-warn/10">
                                                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                            </a>
                                            <button type="button"
                                                    title="Delete topic"
                                                    x-data="{}"
                                                    x-on:click="$store.deleteForm.show('Delete Topic', 'Are you sure you want to delete {{ $topic->name }}? This action cannot be undone.', '{{ route('academic-topics.destroy', ['academic_topic' => $topic, 'academic_subject' => $academicSubject, 'academic_level' => $subjectLevel, 'academic_group' => $subjectGroup]) }}')"
                                                    class="inline-flex items-center justify-center rounded border border-accent/40 px-2.5 py-1.5 text-accent transition hover:bg-accent/10">
                                                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                            </button>
                                        @endcan
                                    </div>
                                </div>
                            </article>
                        @endforeach
                    </div>

                    {{-- Pagination --}}
                    @if($topics->hasPages())
                        <div class="mt-6 flex flex-wrap items-center justify-between gap-3">
                            <p class="font-mono text-[10.5px] tracking-wider text-muted">
                                SHOWING {{ $topics->firstItem() }}–{{ $topics->lastItem() }} OF {{ $topics->total() }}
                            </p>
                            {{ $topics->appends(request()->query())->links() }}
                        </div>
                    @endif
                @else
                    <x-exam.empty
                        compact
                        title="No topics yet"
                        description="Start building your curriculum by adding topics to this subject. Topics help organize your content and make it easier for students to learn."
                    >
                        @can('administrate')
                            <a href="{{ route('academic-topics.create', ['academic_subject' => $academicSubject, 'academic_level' => $subjectLevel, 'academic_group' => $subjectGroup]) }}"
                               class="mt-5 inline-flex items-center gap-1.5 whitespace-nowrap rounded bg-primary px-5 py-2.5 text-[13.5px] font-semibold tracking-wide text-on-primary transition hover:-translate-y-px hover:bg-primary-hi motion-reduce:transition-none">
                                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M12 4v16m8-8H4"/></svg>
                                Add Your First Topic
                            </a>
                        @endcan
                    </x-exam.empty>
                @endif
            </div>

            {{-- ============ Rail ============ --}}
            <aside class="space-y-6">

                {{-- Subject information --}}
                <x-exam.card :padded="false">
                    <x-slot:header>
                        <p class="font-mono text-[10.5px] tracking-[0.14em] text-muted">SUBJECT INFORMATION</p>
                    </x-slot:header>
                    <div class="space-y-4 p-5 font-mono text-[11px] tracking-wide">
                        <div class="flex items-baseline justify-between gap-2 text-muted">
                            <span>NAME</span>
                            <span class="flex-1 border-b border-dotted border-line-strong"></span>
                            <span class="text-right font-semibold text-ink">{{ $academicSubject->name }}</span>
                        </div>
                        <div class="flex items-baseline justify-between gap-2 text-muted">
                            <span>CODE</span>
                            <span class="flex-1 border-b border-dotted border-line-strong"></span>
                            <span class="text-right font-semibold text-ink">{{ $academicSubject->code ?: '—' }}</span>
                        </div>
                        <div class="flex items-baseline justify-between gap-2 text-muted">
                            <span>LEVEL</span>
                            <span class="flex-1 border-b border-dotted border-line-strong"></span>
                            <a href="{{ route('academic-levels.show', ['academic_level' => $subjectLevel, 'academic_group' => $subjectGroup]) }}"
                               class="text-right font-semibold text-primary transition hover:text-primary-hi">
                                {{ $subjectLevel->name }}
                            </a>
                        </div>
                        <div class="flex items-baseline justify-between gap-2 text-muted">
                            <span>GROUP</span>
                            <span class="flex-1 border-b border-dotted border-line-strong"></span>
                            <a href="{{ route('academic-groups.show', ['academic_group' => $subjectGroup]) }}"
                               class="text-right font-semibold text-primary transition hover:text-primary-hi">
                                {{ $subjectGroup->name }}
                            </a>
                        </div>
                        <div class="flex items-baseline justify-between gap-2 text-muted">
                            <span>CREATED</span>
                            <span class="flex-1 border-b border-dotted border-line-strong"></span>
                            <span class="text-right font-semibold text-ink">{{ $academicSubject->created_at->format('M d, Y') }}</span>
                        </div>
                    </div>
                </x-exam.card>

                {{-- Quick navigation --}}
                <x-exam.card :padded="false">
                    <x-slot:header>
                        <p class="font-mono text-[10.5px] tracking-[0.14em] text-muted">QUICK NAVIGATION</p>
                    </x-slot:header>
                    <ul class="divide-y divide-line">
                        <li>
                            <a href="{{ route('academic-subjects.index', ['academic_level' => $subjectLevel, 'academic_group' => $subjectGroup]) }}"
                               class="flex items-center justify-between px-5 py-3 text-sm font-semibold text-ink-soft transition hover:bg-surface-2 hover:text-ink">
                                All Subjects
                                <svg class="h-4 w-4 text-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('academic-levels.show', ['academic_level' => $subjectLevel, 'academic_group' => $subjectGroup]) }}"
                               class="flex items-center justify-between px-5 py-3 text-sm font-semibold text-ink-soft transition hover:bg-surface-2 hover:text-ink">
                                Back to Level
                                <svg class="h-4 w-4 text-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                            </a>
                        </li>
                        @can('administrate')
                            <li>
                                <a href="{{ route('academic-topics.create', ['academic_subject' => $academicSubject, 'academic_level' => $subjectLevel, 'academic_group' => $subjectGroup]) }}"
                                   class="flex items-center justify-between px-5 py-3 text-sm font-semibold text-primary transition hover:bg-surface-2 hover:text-primary-hi">
                                    Add New Topic
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('questions.subject.import.form', ['academic_subject' => $academicSubject, 'academic_level' => $subjectLevel, 'academic_group' => $subjectGroup]) }}"
                                   class="flex items-center justify-between px-5 py-3 text-sm font-semibold text-ok transition hover:bg-surface-2">
                                    Import Questions
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 0115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                                </a>
                            </li>
                        @endcan
                    </ul>
                </x-exam.card>

                {{-- Note --}}
                <x-exam.callout tone="primary">
                    <p class="mb-1.5 font-mono text-[10px] font-semibold uppercase tracking-[0.14em] text-primary">Note</p>
                    <p class="text-xs leading-relaxed text-ink-soft">
                        Topics organize this subject's content into study units. Each topic can hold subtopics,
                        and imported or authored questions attach at the topic level.
                    </p>
                </x-exam.callout>

            </aside>
        </div>
    </x-exam.page>
</x-layouts.app>
