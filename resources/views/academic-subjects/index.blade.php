<x-layouts.app title="Academic Subjects" page-name="Academic Subjects">
    <x-exam.page>

        {{-- Breadcrumb --}}
        <x-exam.crumbs :items="[
            ['label' => 'Academic Groups', 'href' => route('academic-groups.index')],
            ['label' => $academicLevel->academicGroup->name, 'href' => route('academic-groups.show', ['academic_group' => $academicLevel->academicGroup])],
            ['label' => 'Academic Levels', 'href' => route('academic-levels.index', ['academic_group' => $academicLevel->academicGroup])],
            ['label' => $academicLevel->name, 'href' => route('academic-levels.show', ['academic_level' => $academicLevel, 'academic_group' => $academicLevel->academicGroup])],
            ['label' => 'Subjects'],
        ]" />

        {{-- ============ Header ============ --}}
        <div class="mb-6 flex flex-wrap items-start justify-between gap-4">
            <div>
                <h1 class="font-display text-2xl font-bold text-ink md:text-3xl">Academic Subjects</h1>
                <div class="mt-2 flex flex-wrap items-center gap-3 font-mono text-[10.5px] tracking-wide text-muted">
                    <a href="{{ route('academic-levels.show', ['academic_level' => $academicLevel, 'academic_group' => $academicLevel->academicGroup]) }}"
                       class="transition hover:text-ink">
                        {{ strtoupper($academicLevel->name) }}
                    </a>
                    <span class="text-line-strong">|</span>
                    <a href="{{ route('academic-groups.show', ['academic_group' => $academicLevel->academicGroup]) }}"
                       class="transition hover:text-ink">
                        {{ strtoupper($academicLevel->academicGroup->name) }}
                    </a>
                    <span class="text-line-strong">|</span>
                    <span>{{ $academicSubjects->count() }} {{ Str::plural('SUBJECT', $academicSubjects->count()) }}</span>
                </div>
            </div>

            @can('administrate')
                <a href="{{ route('academic-subjects.create', ['academic_level' => $academicLevel, 'academic_group' => $academicLevel->academicGroup]) }}"
                   class="inline-flex items-center gap-1.5 whitespace-nowrap rounded bg-primary px-5 py-2.5 text-[13.5px] font-semibold tracking-wide text-on-primary transition hover:-translate-y-px hover:bg-primary-hi motion-reduce:transition-none">
                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M12 4v16m8-8H4"/></svg>
                    New Subject
                </a>
            @endcan
        </div>

        <x-exam.flash />

        {{-- ============ Subjects Registry ============ --}}
        @if($academicSubjects->count())
            <div class="space-y-3">
                @foreach($academicSubjects as $academicSubject)
                    @php($subjectNumber = str_pad($academicSubjects->firstItem() + $loop->index, 2, '0', STR_PAD_LEFT))
                    <article class="group rounded-md border border-line bg-surface p-5 shadow-[0_1px_2px_rgba(20,24,34,0.04)] transition hover:border-primary/40 hover:shadow-[0_10px_30px_-12px_rgba(20,24,34,0.30)] motion-reduce:transition-none">

                        <div class="flex flex-wrap items-center justify-between gap-4">
                            {{-- Left: Identity --}}
                            <div class="flex min-w-0 flex-1 items-center gap-4">
                                <span class="grid h-11 w-11 shrink-0 place-items-center rounded-md bg-primary/10 font-mono text-sm font-bold text-primary">
                                    {{ $subjectNumber }}
                                </span>
                                <div class="min-w-0">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <h3 class="truncate font-display text-base font-bold text-ink transition group-hover:text-primary">
                                            {{ $academicSubject->name }}
                                        </h3>
                                        @if($academicSubject->code)
                                            <span class="rounded-[3px] bg-accent/10 px-1.5 py-0.5 font-mono text-[10px] font-semibold tracking-[0.12em] text-accent">
                                                {{ strtoupper($academicSubject->code) }}
                                            </span>
                                        @endif
                                        <x-exam.badge tone="ok">
                                            <span class="mr-1 inline-block h-1.5 w-1.5 animate-pulse rounded-full bg-current"></span>
                                            Active
                                        </x-exam.badge>
                                    </div>
                                </div>
                            </div>

                            {{-- Center: Stats (desktop) --}}
                            <div class="hidden items-center gap-6 md:flex">
                                <div class="flex items-baseline gap-2 font-mono text-[11px] tracking-wide text-muted">
                                    <span>TOPICS</span>
                                    <span class="flex-1 border-b border-dotted border-line-strong"></span>
                                    <span class="font-semibold text-ink">{{ $academicSubject->academicTopics()->count() }}</span>
                                </div>
                                <div class="flex items-baseline gap-2 font-mono text-[11px] tracking-wide text-muted">
                                    <span>QUIZZES</span>
                                    <span class="flex-1 border-b border-dotted border-line-strong"></span>
                                    <span class="font-semibold text-ink">{{ $academicSubject->quizzes()->count() }}</span>
                                </div>
                            </div>

                            {{-- Right: Actions --}}
                            <div class="flex items-center gap-2">
                                <a href="{{ route('academic-subjects.show', ['academic_subject' => $academicSubject, 'academic_level' => getRouteParameter('academic_level'), 'academic_group' => getRouteParameter('academic_group')]) }}"
                                   class="inline-flex items-center justify-center rounded border border-line-strong px-3 py-1.5 font-mono text-[10.5px] font-semibold uppercase tracking-wider text-ink-soft transition hover:bg-surface-2 hover:text-ink">
                                    View
                                </a>
                                @can('administrate')
                                    <a href="{{ route('academic-subjects.edit', ['academic_subject' => $academicSubject, 'academic_level' => $academicLevel, 'academic_group' => getRouteParameter('academic_group')]) }}"
                                       class="inline-flex items-center justify-center rounded border border-warn/40 px-3 py-1.5 font-mono text-[10.5px] font-semibold uppercase tracking-wider text-warn transition hover:bg-warn/10">
                                        Edit
                                    </a>
                                    <button type="button"
                                            x-data="{}"
                                            x-on:click="$store.deleteForm.show('Delete Subject', 'Are you sure you want to delete {{ $academicSubject->name }}?', '{{ route('academic-subjects.destroy', ['academic_subject' => $academicSubject, 'academic_level' => $academicLevel, 'academic_group' => getRouteParameter('academic_group')]) }}')"
                                            class="inline-flex items-center justify-center rounded border border-accent/40 px-3 py-1.5 font-mono text-[10.5px] font-semibold uppercase tracking-wider text-accent transition hover:bg-accent/10">
                                        Delete
                                    </button>
                                @endcan
                            </div>
                        </div>

                        {{-- Mobile stats --}}
                        <div class="mt-3 flex gap-4 border-t border-dashed border-line pt-3 md:hidden">
                            <div class="flex flex-1 items-baseline gap-2 font-mono text-[11px] tracking-wide text-muted">
                                <span>TOPICS</span>
                                <span class="flex-1 border-b border-dotted border-line-strong"></span>
                                <span class="font-semibold text-ink">{{ $academicSubject->academicTopics()->count() }}</span>
                            </div>
                            <div class="flex flex-1 items-baseline gap-2 font-mono text-[11px] tracking-wide text-muted">
                                <span>QUIZZES</span>
                                <span class="flex-1 border-b border-dotted border-line-strong"></span>
                                <span class="font-semibold text-ink">{{ $academicSubject->quizzes()->count() }}</span>
                            </div>
                        </div>

                        {{-- Footer: ID + updated --}}
                        <div class="mt-3 flex items-center justify-between border-t border-dashed border-line pt-3 font-mono text-[10px] tracking-wider text-muted">
                            <span>ID #{{ $academicSubject->id }}</span>
                            <span>UPDATED {{ strtoupper($academicSubject->updated_at->diffForHumans()) }}</span>
                        </div>
                    </article>
                @endforeach
            </div>

            {{-- Pagination --}}
            @if($academicSubjects->hasPages())
                <div class="mt-6 flex flex-wrap items-center justify-between gap-3">
                    <p class="font-mono text-[10.5px] tracking-wider text-muted">
                        SHOWING {{ $academicSubjects->firstItem() }}–{{ $academicSubjects->lastItem() }} OF {{ $academicSubjects->total() }}
                    </p>
                    {{ $academicSubjects->appends(request()->query())->links() }}
                </div>
            @endif
        @else
            {{-- ============ Empty State ============ --}}
            <x-exam.empty
                title="No subjects found"
                description="The {{ $academicLevel->name }} level doesn't have any subjects yet. Create your first subject to start building the curriculum."
            >
                <div class="mt-5 flex flex-col justify-center gap-2 sm:flex-row">
                    @can('administrate')
                        <a href="{{ route('academic-subjects.create', ['academic_level' => $academicLevel, 'academic_group' => $academicLevel->academicGroup]) }}"
                           class="inline-flex items-center justify-center gap-1.5 whitespace-nowrap rounded bg-primary px-5 py-2.5 text-[13.5px] font-semibold tracking-wide text-on-primary transition hover:-translate-y-px hover:bg-primary-hi motion-reduce:transition-none">
                            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M12 4v16m8-8H4"/></svg>
                            Create First Subject
                        </a>
                    @endcan
                    <a href="{{ route('academic-levels.show', ['academic_level' => $academicLevel, 'academic_group' => $academicLevel->academicGroup]) }}"
                       class="inline-flex items-center justify-center whitespace-nowrap rounded border border-line-strong px-5 py-2.5 text-[13.5px] font-semibold text-ink-soft transition hover:bg-surface-2 hover:text-ink">
                        Back to Level
                    </a>
                </div>
            </x-exam.empty>
        @endif

    </x-exam.page>
</x-layouts.app>
