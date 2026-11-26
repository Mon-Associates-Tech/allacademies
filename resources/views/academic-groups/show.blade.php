<x-layouts.app title="Academic Group Details" :has-action="false">
    <x-exam.crumbs :items="[
    ['label' => 'My Learning', 'href' => route('dashboard')],
    ['label' => 'Academic Groups', 'href' => route('academic-groups.index')],
    ['label' => $academicGroup->name],
]" />

    <x-exam.page>
        {{-- ============ Header ============ --}}
        <div class="mb-6 flex flex-wrap items-start justify-between gap-4">
            <div>
                <p class="mb-2 flex items-center gap-2 font-mono text-[11px] tracking-[0.14em] text-ink-soft">
                    <span class="h-2 w-2 bg-accent"></span>REGISTRAR'S OFFICE · GROUP RECORD
                </p>
                <h1 class="font-display text-2xl font-bold text-ink md:text-3xl">{{ $academicGroup->name }}</h1>
                <div class="mt-2 flex flex-wrap items-center gap-3 font-mono text-[10.5px] tracking-wide text-muted">
                    <span>{{ $academicGroup->academic_levels_count }} LEVELS</span>
                    <span class="text-line-strong">|</span>
                    <span>{{ $academicGroup->teachers_count ?? 0 }} TEACHERS</span>
                    <span class="text-line-strong">|</span>
                    <x-exam.badge tone="ok">
                        <span class="mr-1 inline-block h-1.5 w-1.5 animate-pulse rounded-full bg-current"></span>
                        ACTIVE
                    </x-exam.badge>
                </div>
            </div>

            @can('administrate')
                <div class="relative" x-data="{ open: false }">
                    <button @click="open = !open" class="grid h-10 w-10 place-items-center rounded-md border border-line-strong bg-surface text-muted transition hover:border-ink-soft hover:text-ink">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"/>
                        </svg>
                    </button>

                    <div x-show="open" @click.away="open = false" x-transition
                         class="absolute right-0 z-10 mt-2 w-48 overflow-hidden rounded-md border border-line bg-surface shadow-lg">
                        <a href="{{ route('academic-groups.edit', ['academic_group' => $academicGroup]) }}"
                           class="flex items-center gap-2 px-4 py-2.5 text-sm text-ink-soft transition hover:bg-surface-2 hover:text-ink">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                            Edit Group
                        </a>
                        <button type="button"
                                x-data="{}"
                                x-on:click="$store.deleteForm.show('Danger', 'Are you sure you want to delete {{ $academicGroup->name }}?', '{{ route('academic-groups.destroy', ['academic_group' => $academicGroup]) }}')"
                                class="flex w-full items-center gap-2 border-t border-line px-4 py-2.5 text-sm text-accent transition hover:bg-accent/5">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                            Delete Group
                        </button>
                    </div>
                </div>
            @endcan
        </div>

        <x-exam.flash />

        <div class="grid gap-8 lg:grid-cols-[minmax(0,1fr)_320px]">

            {{-- ============ Main Column: Academic Levels ============ --}}
            <div>
                <div class="mb-5 flex items-center justify-between">
                    <div>
                        <p class="mb-1 font-mono text-[10.5px] tracking-[0.14em] text-muted">CURRICULUM STRUCTURE</p>
                        <h2 class="font-display text-xl font-bold text-ink">Academic Levels</h2>
                    </div>
                    @can('administrate')
                        <a href="{{ route('academic-levels.create', ['academic_group' => $academicGroup]) }}"
                           class="inline-flex items-center gap-1.5 whitespace-nowrap rounded bg-primary px-4 py-2 text-[12.5px] font-semibold tracking-wide text-on-primary transition hover:-translate-y-px hover:bg-primary-hi motion-reduce:transition-none">
                            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M12 4v16m8-8H4"/></svg>
                            Add Level
                        </a>
                    @endcan
                </div>

                @if($academicGroup->academicLevels && $academicGroup->academicLevels->count() > 0)
                    <div class="space-y-3">
                        @foreach($academicGroup->academicLevels as $level)
                            <article class="group rounded-md border border-line bg-surface p-4 shadow-[0_1px_2px_rgba(20,24,34,0.04)] transition hover:border-primary/40 hover:shadow-[0_10px_30px_-12px_rgba(20,24,34,0.30)] motion-reduce:transition-none">
                                <div class="flex flex-wrap items-center justify-between gap-4">
                                    <div class="min-w-0 flex-1">
                                        <div class="flex items-center gap-3">
                                            <span class="grid h-9 w-9 shrink-0 place-items-center rounded-md bg-primary/10 font-mono text-xs font-bold text-primary">
                                                {{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}
                                            </span>
                                            <div class="min-w-0">
                                                <h3 class="truncate font-display text-base font-bold text-ink transition group-hover:text-primary">
                                                    {{ $level->name }}
                                                </h3>
                                                @if($level->label && $level->label !== $level->name)
                                                    <p class="truncate font-mono text-[10.5px] tracking-wide text-muted">{{ $level->label }}</p>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    <div class="hidden items-center gap-6 sm:flex">
                                        <div class="flex items-baseline gap-2 font-mono text-[11px] tracking-wide text-muted">
                                            <span>SUBJECTS</span>
                                            <span class="flex-1 border-b border-dotted border-line-strong"></span>
                                            <span class="font-semibold text-ink">{{ $level->subjects->count() }}</span>
                                        </div>
                                        <div class="flex items-baseline gap-2 font-mono text-[11px] tracking-wide text-muted">
                                            <span>STUDENTS</span>
                                            <span class="flex-1 border-b border-dotted border-line-strong"></span>
                                            <span class="font-semibold text-ink">{{ $level->students->count() }}</span>
                                        </div>
                                    </div>

                                    <div class="flex items-center gap-2">
                                        <a href="{{ route('academic-levels.show', ['academic_level' => $level, 'academic_group' => getRouteParameter('academic_group')]) }}"
                                           class="inline-flex items-center justify-center rounded border border-line-strong px-3 py-1.5 font-mono text-[10.5px] font-semibold uppercase tracking-wider text-ink-soft transition hover:bg-surface-2 hover:text-ink">
                                            View
                                        </a>
                                        @can('administrate')
                                            <a href="{{ route('academic-levels.edit', ['academic_level' => $level, 'academic_group' => getRouteParameter('academic_group')]) }}"
                                               class="inline-flex items-center justify-center rounded border border-warn/40 px-3 py-1.5 font-mono text-[10.5px] font-semibold uppercase tracking-wider text-warn transition hover:bg-warn/10">
                                                Edit
                                            </a>
                                        @endcan
                                    </div>
                                </div>

                                {{-- Mobile stats --}}
                                <div class="mt-3 flex gap-4 border-t border-dashed border-line pt-3 sm:hidden">
                                    <div class="flex flex-1 items-baseline gap-2 font-mono text-[11px] tracking-wide text-muted">
                                        <span>SUBJECTS</span>
                                        <span class="flex-1 border-b border-dotted border-line-strong"></span>
                                        <span class="font-semibold text-ink">{{ $level->subjects->count() }}</span>
                                    </div>
                                    <div class="flex flex-1 items-baseline gap-2 font-mono text-[11px] tracking-wide text-muted">
                                        <span>STUDENTS</span>
                                        <span class="flex-1 border-b border-dotted border-line-strong"></span>
                                        <span class="font-semibold text-ink">{{ $level->students->count() }}</span>
                                    </div>
                                </div>
                            </article>
                        @endforeach

                        @if($academicGroup->academicLevels->count() >= 5)
                            <div class="pt-4 text-center">
                                <a href="{{ route('academic-levels.index', ['academic_group' => $academicGroup]) }}"
                                   class="font-mono text-[11px] font-semibold uppercase tracking-wider text-primary transition hover:text-primary-hi">
                                    VIEW ALL {{ $academicGroup->academicLevels->count() }} LEVELS →
                                </a>
                            </div>
                        @endif
                    </div>
                @else
                    <x-exam.empty compact title="No academic levels configured" description="Get started by creating an academic level for this group.">
                        @can('administrate')
                            <a href="{{ route('academic-levels.create', ['academic_group' => $academicGroup]) }}"
                               class="mt-5 inline-flex items-center gap-1.5 whitespace-nowrap rounded bg-primary px-5 py-2.5 text-[13.5px] font-semibold tracking-wide text-on-primary transition hover:-translate-y-px hover:bg-primary-hi motion-reduce:transition-none">
                                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M12 4v16m8-8H4"/></svg>
                                Create Academic Level
                            </a>
                        @endcan
                    </x-exam.empty>
                @endif
            </div>

            {{-- ============ Sidebar ============ --}}
            <aside class="space-y-6">

                {{-- Record Details --}}
                <x-exam.card :padded="false">
                    <x-slot:header>
                        <p class="font-mono text-[10.5px] tracking-[0.14em] text-muted">RECORD DETAILS</p>
                    </x-slot:header>
                    <div class="space-y-4 p-5 font-mono text-[11px] tracking-wide">
                        <div class="flex items-baseline justify-between gap-2 text-muted">
                            <span>NAME</span>
                            <span class="flex-1 border-b border-dotted border-line-strong"></span>
                            <span class="font-semibold text-ink">{{ $academicGroup->name }}</span>
                        </div>
                        @if($academicGroup->tag)
                            <div class="flex items-baseline justify-between gap-2 text-muted">
                                <span>TAG</span>
                                <span class="flex-1 border-b border-dotted border-line-strong"></span>
                                <span class="rounded-[3px] bg-primary/10 px-1.5 py-0.5 font-semibold tracking-[0.10em] text-primary">{{ strtoupper($academicGroup->tag) }}</span>
                            </div>
                        @endif
                        <div class="flex items-baseline justify-between gap-2 text-muted">
                            <span>CREATED</span>
                            <span class="flex-1 border-b border-dotted border-line-strong"></span>
                            <span class="font-semibold text-ink">{{ $academicGroup->created_at->format('M j, Y') }}</span>
                        </div>
                        <div class="flex items-baseline justify-between gap-2 text-muted">
                            <span>UPDATED</span>
                            <span class="flex-1 border-b border-dotted border-line-strong"></span>
                            <span class="font-semibold text-ink">{{ $academicGroup->updated_at->format('M j, Y') }}</span>
                        </div>
                    </div>
                </x-exam.card>

                {{-- Quick Actions --}}
                <x-exam.card :padded="false">
                    <x-slot:header>
                        <p class="font-mono text-[10.5px] tracking-[0.14em] text-muted">QUICK ACTIONS</p>
                    </x-slot:header>
                    <ul class="divide-y divide-line">
                        <li>
                            <a href="{{ route('academic-levels.index', ['academic_group' => $academicGroup]) }}"
                               class="flex items-center justify-between px-5 py-3 text-sm font-semibold text-ink-soft transition hover:bg-surface-2 hover:text-ink">
                                Manage Levels
                                <svg class="h-4 w-4 text-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                            </a>
                        </li>
                        @can('administrate')
                            <li>
                                <a href="{{ route('academic-levels.create', ['academic_group' => $academicGroup]) }}"
                                   class="flex items-center justify-between px-5 py-3 text-sm font-semibold text-ink-soft transition hover:bg-surface-2 hover:text-ink">
                                    Add New Level
                                    <svg class="h-4 w-4 text-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                </a>
                            </li>
                            <li>
                                <a href="{{ route('academic-groups.edit', ['academic_group' => $academicGroup]) }}"
                                   class="flex items-center justify-between px-5 py-3 text-sm font-semibold text-ink-soft transition hover:bg-surface-2 hover:text-ink">
                                    Edit Group
                                    <svg class="h-4 w-4 text-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                </a>
                            </li>
                        @endcan
                        <li>
                            <a href="{{ route('academic-groups.index') }}"
                               class="flex items-center justify-between px-5 py-3 text-sm font-semibold text-ink-soft transition hover:bg-surface-2 hover:text-ink">
                                All Groups
                                <svg class="h-4 w-4 text-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                            </a>
                        </li>
                    </ul>
                </x-exam.card>

                {{-- Registrar's Note --}}
                <x-exam.callout tone="primary">
                    <p class="mb-1.5 font-mono text-[10px] font-semibold uppercase tracking-[0.14em] text-primary">Registrar's Note</p>
                    <ul class="space-y-1.5 text-xs leading-relaxed text-ink-soft">
                        <li class="flex items-start gap-2">
                            <span class="mt-1 h-1 w-1 shrink-0 rounded-full bg-primary"></span>
                            Academic levels organize curriculum into distinct sections.
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="mt-1 h-1 w-1 shrink-0 rounded-full bg-primary"></span>
                            Use consistent naming conventions across groups.
                        </li>
                    </ul>
                </x-exam.callout>

            </aside>
        </div>
    </x-exam.page>
</x-layouts.app>
