<x-layouts.app title="Academic Level Details" page-name="Academic Details" :has-action="false">
    <x-exam.page>
        @php($academicGroup = $academicLevel->academicGroup)

        {{-- Breadcrumb --}}
        <x-exam.crumbs :items="[
            ['label' => 'Academic Groups', 'href' => route('academic-groups.index')],
            ['label' => $academicGroup->name, 'href' => route('academic-groups.show', ['academic_group' => $academicGroup])],
            ['label' => 'Academic Levels', 'href' => route('academic-levels.index', ['academic_group' => $academicGroup])],
            ['label' => $academicLevel->name],
        ]" />

        {{-- ============ Header ============ --}}
        <div class="mb-6 flex flex-wrap items-start justify-between gap-4">
            <div class="min-w-0">
                <h1 class="font-display text-2xl font-bold text-ink md:text-3xl">{{ $academicLevel->name }}</h1>
                <div class="mt-2 flex flex-wrap items-center gap-3 font-mono text-[10.5px] tracking-wide text-muted">
                    @if($academicLevel->label)
                        <span>{{ strtoupper($academicLevel->label) }}</span>
                        <span class="text-line-strong">|</span>
                    @endif
                    <a href="{{ route('academic-groups.show', ['academic_group' => $academicGroup]) }}"
                       class="transition hover:text-ink">
                        {{ strtoupper($academicGroup->name) }}
                    </a>
                    <span class="text-line-strong">|</span>
                    <x-exam.badge tone="ok">
                        <span class="mr-1 inline-block h-1.5 w-1.5 animate-pulse rounded-full bg-current"></span>
                        Active
                    </x-exam.badge>
                </div>
            </div>

            @can('administrate')
                <div class="flex flex-wrap items-center gap-2">
                    <a href="{{ route('academic-subjects.create', ['academic_level' => $academicLevel, 'academic_group' => $academicGroup]) }}"
                       class="inline-flex items-center gap-1.5 whitespace-nowrap rounded bg-primary px-4 py-2.5 text-[13px] font-semibold tracking-wide text-on-primary transition hover:-translate-y-px hover:bg-primary-hi motion-reduce:transition-none">
                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M12 4v16m8-8H4"/></svg>
                        Add Subject
                    </a>

                    <a href="{{ route('academic-levels.edit', ['academic_level' => $academicLevel, 'academic_group' => $academicGroup]) }}"
                       class="inline-flex items-center gap-1.5 whitespace-nowrap rounded border border-warn/40 px-4 py-2.5 font-mono text-[10.5px] font-semibold uppercase tracking-wider text-warn transition hover:bg-warn/10">
                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                        Edit
                    </a>

                    <button type="button"
                            x-data="{}"
                            x-on:click="$store.deleteForm.show('Delete Academic Level', 'Are you sure you want to delete {{ $academicLevel->name }}? This action cannot be undone.', '{{ route('academic-levels.destroy', ['academic_level' => $academicLevel, 'academic_group' => $academicGroup]) }}')"
                            class="inline-flex items-center gap-1.5 whitespace-nowrap rounded border border-accent/40 px-4 py-2.5 font-mono text-[10.5px] font-semibold uppercase tracking-wider text-accent transition hover:bg-accent/10">
                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        Delete
                    </button>
                </div>
            @endcan
        </div>

        <x-exam.flash />

        <div class="grid gap-8 lg:grid-cols-[minmax(0,1fr)_320px]">

            {{-- ============ Subjects ============ --}}
            <div>
                <div class="mb-5 flex flex-wrap items-end justify-between gap-3">
                    <div>
                        <p class="mb-1 font-mono text-[10.5px] tracking-[0.14em] text-muted">CURRICULUM REGISTER</p>
                        <h2 class="font-display text-xl font-bold text-ink">Academic Subjects</h2>
                    </div>
                    @if($academicLevel->academicSubjects && $academicLevel->academicSubjects->count())
                        <a href="{{ route('academic-subjects.index', ['academic_level' => $academicLevel, 'academic_group' => $academicGroup]) }}"
                           class="font-mono text-[10.5px] font-semibold uppercase tracking-wider text-primary transition hover:text-primary-hi">
                            View all →
                        </a>
                    @endif
                </div>

                @if($academicLevel->academicSubjects && $academicLevel->academicSubjects->count() > 0)
                    <div class="space-y-3">
                        @foreach($academicLevel->academicSubjects as $subject)
                            <article class="group rounded-md border border-line bg-surface p-5 shadow-[0_1px_2px_rgba(20,24,34,0.04)] transition hover:border-primary/40 hover:shadow-[0_10px_30px_-12px_rgba(20,24,34,0.30)] motion-reduce:transition-none">
                                <div class="flex flex-wrap items-start justify-between gap-4">
                                    {{-- Identity --}}
                                    <div class="flex min-w-0 flex-1 items-start gap-4">
                                        <span class="grid h-10 w-10 shrink-0 place-items-center rounded-md bg-primary/10 font-mono text-xs font-bold text-primary">
                                            {{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}
                                        </span>

                                        <div class="min-w-0 flex-1">
                                            <div class="flex flex-wrap items-center gap-2">
                                                <h3 class="truncate font-display text-base font-bold text-ink transition group-hover:text-primary">
                                                    {{ $subject->name }}
                                                </h3>
                                                @if($subject->code)
                                                    <span class="rounded-[3px] bg-accent/10 px-1.5 py-0.5 font-mono text-[10px] font-semibold tracking-[0.12em] text-accent">
                                                        {{ strtoupper($subject->code) }}
                                                    </span>
                                                @endif
                                                <x-exam.badge tone="ok">Active</x-exam.badge>
                                            </div>

                                            @if($subject->description)
                                                <p class="mt-2 line-clamp-2 text-sm leading-relaxed text-ink-soft">
                                                    {{ Str::limit($subject->description, 120) }}
                                                </p>
                                            @endif

                                            {{-- Dotted-leader meta --}}
                                            <div class="mt-3 max-w-xs space-y-1.5">
                                                @if(isset($subject->topics_count))
                                                    <div class="flex items-baseline gap-2 font-mono text-[11px] tracking-wide text-muted">
                                                        <span>TOPICS</span>
                                                        <span class="flex-1 -translate-y-[3px] border-b border-dotted border-line-strong"></span>
                                                        <span class="font-semibold text-ink">{{ $subject->topics_count }}</span>
                                                    </div>
                                                @endif
                                                <div class="flex items-baseline gap-2 font-mono text-[11px] tracking-wide text-muted">
                                                    <span>STATUS</span>
                                                    <span class="flex-1 -translate-y-[3px] border-b border-dotted border-line-strong"></span>
                                                    <span class="font-semibold text-ok">ACTIVE</span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Action --}}
                                    <div class="shrink-0">
                                        <a href="{{ route('academic-subjects.show', ['academic_subject' => $subject, 'academic_level' => getRouteParameter('academic_level'), 'academic_group' => getRouteParameter('academic_group')]) }}"
                                           class="inline-flex items-center gap-1.5 whitespace-nowrap rounded border border-line-strong px-3.5 py-2 font-mono text-[10.5px] font-semibold uppercase tracking-wider text-ink-soft transition hover:bg-surface-2 hover:text-ink">
                                            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                            View Details
                                        </a>
                                    </div>
                                </div>
                            </article>
                        @endforeach
                    </div>
                @else
                    <x-exam.empty
                        compact
                        title="No subjects yet"
                        description="This academic level doesn't have any subjects yet. Add the first subject to get started."
                    >
                        @can('administrate')
                            <a href="{{ route('academic-subjects.create', ['academic_level' => $academicLevel, 'academic_group' => $academicGroup]) }}"
                               class="mt-5 inline-flex items-center gap-1.5 whitespace-nowrap rounded bg-primary px-5 py-2.5 text-[13.5px] font-semibold tracking-wide text-on-primary transition hover:-translate-y-px hover:bg-primary-hi motion-reduce:transition-none">
                                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M12 4v16m8-8H4"/></svg>
                                Add First Subject
                            </a>
                        @endcan
                    </x-exam.empty>
                @endif
            </div>

            {{-- ============ Rail ============ --}}
            <aside class="space-y-6">

                {{-- Level information --}}
                <x-exam.card :padded="false">
                    <x-slot:header>
                        <p class="font-mono text-[10.5px] tracking-[0.14em] text-muted">LEVEL INFORMATION</p>
                    </x-slot:header>
                    <div class="space-y-4 p-5 font-mono text-[11px] tracking-wide">
                        <div class="flex items-baseline justify-between gap-2 text-muted">
                            <span>NAME</span>
                            <span class="flex-1 border-b border-dotted border-line-strong"></span>
                            <span class="text-right font-semibold text-ink">{{ $academicLevel->name }}</span>
                        </div>
                        <div class="flex items-baseline justify-between gap-2 text-muted">
                            <span>LABEL</span>
                            <span class="flex-1 border-b border-dotted border-line-strong"></span>
                            <span class="text-right font-semibold text-ink">{{ $academicLevel->label ?: '—' }}</span>
                        </div>
                        <div class="flex items-baseline justify-between gap-2 text-muted">
                            <span>GROUP</span>
                            <span class="flex-1 border-b border-dotted border-line-strong"></span>
                            <a href="{{ route('academic-groups.show', ['academic_group' => $academicGroup]) }}"
                               class="text-right font-semibold text-primary transition hover:text-primary-hi">
                                {{ $academicGroup->name }}
                            </a>
                        </div>
                        <div class="flex items-baseline justify-between gap-2 text-muted">
                            <span>CREATED</span>
                            <span class="flex-1 border-b border-dotted border-line-strong"></span>
                            <span class="text-right font-semibold text-ink">{{ $academicLevel->created_at->format('M d, Y') }}</span>
                        </div>
                        <div class="flex items-baseline justify-between gap-2 text-muted">
                            <span>UPDATED</span>
                            <span class="flex-1 border-b border-dotted border-line-strong"></span>
                            <span class="text-right font-semibold text-ink">{{ $academicLevel->updated_at->diffForHumans() }}</span>
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
                            <a href="{{ route('academic-groups.show', ['academic_group' => $academicGroup]) }}"
                               class="flex items-center justify-between px-5 py-3 text-sm font-semibold text-ink-soft transition hover:bg-surface-2 hover:text-ink">
                                View Academic Group
                                <svg class="h-4 w-4 text-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('academic-levels.index', ['academic_group' => $academicGroup]) }}"
                               class="flex items-center justify-between px-5 py-3 text-sm font-semibold text-ink-soft transition hover:bg-surface-2 hover:text-ink">
                                All Academic Levels
                                <svg class="h-4 w-4 text-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('academic-subjects.index', ['academic_level' => $academicLevel, 'academic_group' => $academicGroup]) }}"
                               class="flex items-center justify-between px-5 py-3 text-sm font-semibold text-ink-soft transition hover:bg-surface-2 hover:text-ink">
                                View All Subjects
                                <svg class="h-4 w-4 text-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                            </a>
                        </li>
                        @can('administrate')
                            <li>
                                <a href="{{ route('academic-subjects.create', ['academic_level' => $academicLevel, 'academic_group' => $academicGroup]) }}"
                                   class="flex items-center justify-between px-5 py-3 text-sm font-semibold text-primary transition hover:bg-surface-2 hover:text-primary-hi">
                                    Add New Subject
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                </a>
                            </li>
                        @endcan
                    </ul>
                </x-exam.card>

                {{-- Note --}}
                <x-exam.callout tone="primary">
                    <p class="mb-1.5 font-mono text-[10px] font-semibold uppercase tracking-[0.14em] text-primary">Note</p>
                    <p class="text-xs leading-relaxed text-ink-soft">
                        Subjects added to this level become available to its students and teachers, and appear in the
                        course registry for quiz and examination practice.
                    </p>
                </x-exam.callout>

            </aside>
        </div>
    </x-exam.page>
</x-layouts.app>
