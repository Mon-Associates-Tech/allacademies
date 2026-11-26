<x-layouts.app title="Academic Levels" :page-name="'Academic Levels'">
    <x-exam.page>

        {{-- Breadcrumb --}}
        <x-exam.crumbs :items="[
            ['label' => 'Academic Groups', 'href' => route('academic-groups.index')],
            ['label' => $academicGroup->name, 'href' => route('academic-groups.show', ['academic_group' => $academicGroup])],
            ['label' => 'Academic Levels'],
        ]" />

        {{-- ============ Header ============ --}}
        <div class="mb-6 flex flex-wrap items-start justify-between gap-4">
            <div>
                <p class="mb-2 flex items-center gap-2 font-mono text-[11px] tracking-[0.14em] text-ink-soft">
                    <span class="h-2 w-2 bg-accent"></span>REGISTRAR'S OFFICE · LEVEL REGISTRY
                </p>
                <h1 class="font-display text-2xl font-bold text-ink md:text-3xl">Academic Levels</h1>
                <div class="mt-2 flex flex-wrap items-center gap-3 font-mono text-[10.5px] tracking-wide text-muted">
                    <span>{{ $academicGroup->name }}</span>
                    <span class="text-line-strong">|</span>
                    <span>{{ $academicLevels->count() }} {{ Str::plural('LEVEL', $academicLevels->count()) }}</span>
                </div>
            </div>

            @can('administrate')
                <a href="{{ route('academic-levels.create', ['academic_group' => $academicGroup]) }}"
                   class="inline-flex items-center gap-1.5 whitespace-nowrap rounded bg-primary px-5 py-2.5 text-[13.5px] font-semibold tracking-wide text-on-primary transition hover:-translate-y-px hover:bg-primary-hi motion-reduce:transition-none">
                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M12 4v16m8-8H4"/></svg>
                    New Level
                </a>
            @endcan
        </div>

        <x-exam.flash />

        {{-- ============ Stats Strip ============ --}}
        <div class="mb-8 grid grid-cols-3 divide-x divide-line rounded-md border border-line bg-surface">
            <div class="px-4 py-3.5 text-center">
                <p class="font-display text-xl font-bold text-ink sm:text-2xl">{{ $academicLevels->count() }}</p>
                <p class="mt-0.5 font-mono text-[9px] tracking-[0.18em] text-muted sm:text-[9.5px]">ACADEMIC LEVELS</p>
            </div>
            <div class="px-4 py-3.5 text-center">
                <p class="font-display text-xl font-bold text-primary sm:text-2xl">{{ $academicLevels->sum('academic_subjects_count') ?? 0 }}</p>
                <p class="mt-0.5 font-mono text-[9px] tracking-[0.18em] text-muted sm:text-[9.5px]">TOTAL SUBJECTS</p>
            </div>
            <div class="px-4 py-3.5 text-center">
                <a href="{{ route('academic-groups.show', ['academic_group' => $academicGroup]) }}"
                   class="inline-block max-w-full truncate font-display text-sm font-bold text-ok transition hover:text-primary sm:text-base">
                    {{ $academicGroup->name }}
                </a>
                <p class="mt-0.5 font-mono text-[9px] tracking-[0.18em] text-muted sm:text-[9.5px]">PARENT GROUP</p>
            </div>
        </div>

        {{-- ============ Levels Registry ============ --}}
        @if($academicLevels->count())
            <div class="space-y-3">
                @foreach($academicLevels as $academicLevel)
                    @php($levelNumber = str_pad($academicLevels->firstItem() + $loop->index, 2, '0', STR_PAD_LEFT))
                    <article class="group rounded-md border border-line bg-surface p-5 shadow-[0_1px_2px_rgba(20,24,34,0.04)] transition hover:border-primary/40 hover:shadow-[0_10px_30px_-12px_rgba(20,24,34,0.30)] motion-reduce:transition-none">

                        <div class="flex flex-wrap items-center justify-between gap-4">
                            {{-- Left: Identity --}}
                            <div class="flex min-w-0 flex-1 items-center gap-4">
                                <span class="grid h-11 w-11 shrink-0 place-items-center rounded-md bg-primary/10 font-mono text-sm font-bold text-primary">
                                    {{ $levelNumber }}
                                </span>
                                <div class="min-w-0">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <h3 class="truncate font-display text-base font-bold text-ink transition group-hover:text-primary">
                                            {{ $academicLevel->name }}
                                        </h3>
                                        <x-exam.badge tone="ok">
                                            <span class="mr-1 inline-block h-1.5 w-1.5 animate-pulse rounded-full bg-current"></span>
                                            Active
                                        </x-exam.badge>
                                    </div>
                                    @if($academicLevel->label && $academicLevel->label !== $academicLevel->name)
                                        <p class="mt-0.5 truncate font-mono text-[10.5px] tracking-wide text-muted">{{ $academicLevel->label }}</p>
                                    @endif
                                </div>
                            </div>

                            {{-- Center: Stats (desktop) --}}
                            <div class="hidden items-center gap-6 md:flex">
                                <div class="flex items-baseline gap-2 font-mono text-[11px] tracking-wide text-muted">
                                    <span>SUBJECTS</span>
                                    <span class="flex-1 border-b border-dotted border-line-strong"></span>
                                    <span class="font-semibold text-ink">{{ $academicLevel->academic_subjects_count ?? 0 }}</span>
                                </div>
                                <div class="flex items-baseline gap-2 font-mono text-[11px] tracking-wide text-muted">
                                    <span>CREATED</span>
                                    <span class="flex-1 border-b border-dotted border-line-strong"></span>
                                    <span class="font-semibold text-ink">{{ $academicLevel->created_at->format('M Y') }}</span>
                                </div>
                            </div>

                            {{-- Right: Actions --}}
                            <div class="flex items-center gap-2">
                                <a href="{{ route('academic-levels.show', ['academic_level' => $academicLevel, 'academic_group' => $academicGroup]) }}"
                                   class="inline-flex items-center justify-center rounded border border-line-strong px-3 py-1.5 font-mono text-[10.5px] font-semibold uppercase tracking-wider text-ink-soft transition hover:bg-surface-2 hover:text-ink">
                                    View
                                </a>
                                @can('administrate')
                                    <a href="{{ route('academic-levels.edit', ['academic_level' => $academicLevel, 'academic_group' => $academicGroup]) }}"
                                       class="inline-flex items-center justify-center rounded border border-warn/40 px-3 py-1.5 font-mono text-[10.5px] font-semibold uppercase tracking-wider text-warn transition hover:bg-warn/10">
                                        Edit
                                    </a>
                                    <button type="button"
                                            x-data="{}"
                                            x-on:click="$store.deleteForm.show('Danger', 'Are you sure you want to delete {{ $academicLevel->name }}?', '{{ route('academic-levels.destroy', ['academic_level' => $academicLevel, 'academic_group' => $academicGroup]) }}')"
                                            class="inline-flex items-center justify-center rounded border border-accent/40 px-3 py-1.5 font-mono text-[10.5px] font-semibold uppercase tracking-wider text-accent transition hover:bg-accent/10">
                                        Delete
                                    </button>
                                @endcan
                            </div>
                        </div>

                        {{-- Description --}}
                        @if($academicLevel->description)
                            <p class="mt-3 line-clamp-2 border-t border-dashed border-line pt-3 text-sm text-ink-soft">
                                {{ Str::limit($academicLevel->description, 140) }}
                            </p>
                        @endif

                        {{-- Footer: ID + updated --}}
                        <div class="mt-3 flex items-center justify-between border-t border-dashed border-line pt-3 font-mono text-[10px] tracking-wider text-muted">
                            <span>ID #{{ $academicLevel->id }}</span>
                            <span>UPDATED {{ strtoupper($academicLevel->updated_at->diffForHumans()) }}</span>
                        </div>
                    </article>
                @endforeach
            </div>

            {{-- Pagination --}}
            @if($academicLevels->hasPages())
                <div class="mt-6 flex flex-wrap items-center justify-between gap-3">
                    <p class="font-mono text-[10.5px] tracking-wider text-muted">
                        SHOWING {{ $academicLevels->firstItem() }}–{{ $academicLevels->lastItem() }} OF {{ $academicLevels->total() }}
                    </p>
                    {{ $academicLevels->appends(request()->query())->links() }}
                </div>
            @endif

            {{-- Back to group --}}
            <div class="mt-8 text-center">
                <a href="{{ route('academic-groups.show', ['academic_group' => $academicGroup]) }}"
                   class="font-mono text-[11px] font-semibold uppercase tracking-wider text-primary transition hover:text-primary-hi">
                    ← Back to {{ $academicGroup->name }}
                </a>
            </div>
        @else
            {{-- ============ Empty State ============ --}}
            <x-exam.empty
                title="No academic levels found"
                description="The {{ $academicGroup->name }} group doesn't have any academic levels yet. Create your first level to start organizing your educational content."
            >
                <div class="mt-5 flex flex-col justify-center gap-2 sm:flex-row">
                    @can('administrate')
                        <a href="{{ route('academic-levels.create', ['academic_group' => $academicGroup]) }}"
                           class="inline-flex items-center justify-center gap-1.5 whitespace-nowrap rounded bg-primary px-5 py-2.5 text-[13.5px] font-semibold tracking-wide text-on-primary transition hover:-translate-y-px hover:bg-primary-hi motion-reduce:transition-none">
                            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M12 4v16m8-8H4"/></svg>
                            Create First Level
                        </a>
                    @endcan
                    <a href="{{ route('academic-groups.show', ['academic_group' => $academicGroup]) }}"
                       class="inline-flex items-center justify-center whitespace-nowrap rounded border border-line-strong px-5 py-2.5 text-[13.5px] font-semibold text-ink-soft transition hover:bg-surface-2 hover:text-ink">
                        Back to Group
                    </a>
                </div>
            </x-exam.empty>
        @endif

    </x-exam.page>
</x-layouts.app>
