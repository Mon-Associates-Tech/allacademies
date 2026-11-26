<x-layouts.app title="Academic Groups" page-name="Academic Groups">
    <x-slot name="breadcrumb">
        <x-breadcrumb :paths="['Academic Groups' => null]" />
    </x-slot>

    <x-exam.page>
        {{-- ============ Header ============ --}}
        <x-exam.header
            eyebrow="ACADEMIC STRUCTURE"
            title="Academic Groups"
        />

        {{-- Header Actions --}}
        <div class="mb-6 flex flex-wrap items-center justify-between gap-4">
            <p class="font-mono text-[10.5px] tracking-[0.14em] text-muted">
                ACADEMIC STRUCTURE · {{ $academicGroups->total() }} GROUP{{ $academicGroups->total() === 1 ? '' : 'S' }}
            </p>
            <div class="flex gap-2">
                <a href="{{ route('academic.structure') }}"
                   class="inline-flex items-center gap-1.5 whitespace-nowrap rounded border border-line-strong px-4 py-2.5 text-[13px] font-semibold text-ink-soft transition hover:bg-surface-2 hover:text-ink">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                    View Structure
                </a>
                @can('administrate')
                    <a href="{{ route('academic-groups.create') }}"
                       class="inline-flex items-center gap-1.5 whitespace-nowrap rounded bg-primary px-5 py-2.5 text-[13.5px] font-semibold tracking-wide text-on-primary transition hover:-translate-y-px hover:bg-primary-hi motion-reduce:transition-none">
                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M12 4v16m8-8H4"/></svg>
                        New Group
                    </a>
                @endcan
            </div>
        </div>

        <x-exam.flash />

        @if($academicGroups->count())

            {{-- ============ Search & Filters ============ --}}
            <x-exam.card class="mb-6" :padded="false">
                <x-slot:header>
                    <p class="font-mono text-[10.5px] tracking-[0.14em] text-muted">FILTERS · SEARCH THE REGISTRY</p>
                </x-slot:header>
                <div class="flex flex-col gap-4 p-5 sm:flex-row sm:items-center">
                    <div class="relative flex-1">
                        <svg class="pointer-events-none absolute left-3.5 top-1/2 h-4 w-4 -translate-y-1/2 text-muted" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd"/>
                        </svg>
                        <input type="text" id="search-input" placeholder="Search groups by name…"
                               class="w-full rounded-md border border-line-strong bg-surface py-2.5 pl-10 pr-4 text-sm text-ink placeholder:text-muted transition focus:border-primary focus:outline-none focus:ring-[3px] focus:ring-primary/20">
                    </div>
                    <div class="flex items-center gap-2">
                        <select class="appearance-none rounded-md border border-line-strong bg-surface py-2.5 pl-3 pr-8 font-mono text-[11px] tracking-wide text-ink-soft transition hover:border-ink-soft focus:border-primary focus:outline-none">
                            <option value="">ALL GROUPS</option>
                            <option value="active">ACTIVE</option>
                            <option value="inactive">INACTIVE</option>
                        </select>
                    </div>
                </div>
            </x-exam.card>

            {{-- ============ Groups Registry ============ --}}
            <div id="groups-container" class="space-y-3">
                @foreach($academicGroups as $academicGroup)
                    <article class="group-item animate-rise rounded-md border border-line bg-surface p-5 shadow-[0_1px_2px_rgba(20,24,34,0.04)] transition hover:border-primary/40 hover:shadow-[0_10px_30px_-12px_rgba(20,24,34,0.30)] motion-reduce:transition-none"
                             data-name="{{ strtolower($academicGroup->name) }}"
                             style="animation-delay: {{ $loop->index * 40 }}ms">
                        <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                            {{-- Left: Identity --}}
                            <div class="flex items-center gap-4">
                                <x-avatar
                                    max-initials="3"
                                    :name="$academicGroup->name"
                                    class="h-14 w-14 shrink-0 text-sm font-bold"
                                    text-size="text-sm"
                                />
                                <div class="min-w-0">
                                    <div class="flex flex-wrap items-center gap-2">
                                        <h3 class="truncate font-display text-lg font-bold text-ink transition group-hover:text-primary">
                                            {{ $academicGroup->name }}
                                        </h3>
                                        <x-exam.badge tone="ok">
                                            <span class="mr-1 inline-block h-1.5 w-1.5 animate-pulse rounded-full bg-current"></span>
                                            Active
                                        </x-exam.badge>
                                    </div>
                                    <div class="mt-1 flex flex-wrap items-center gap-3 font-mono text-[10.5px] tracking-wide text-muted">
                                        <span class="rounded-[3px] bg-primary/10 px-1.5 py-0.5 font-semibold tracking-[0.10em] text-primary">ACADEMIC GROUP</span>
                                        <span>
                                            <svg class="mr-0.5 inline h-3 w-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                            CREATED {{ strtoupper($academicGroup->created_at->diffForHumans()) }}
                                        </span>
                                    </div>
                                </div>
                            </div>

                            {{-- Center: Stats --}}
                            <div class="flex items-center gap-6">
                                <div class="text-center">
                                    <p class="font-display text-xl font-bold text-primary">{{ $academicGroup->academicLevels()->count() }}</p>
                                    <p class="font-mono text-[9px] tracking-[0.14em] text-muted">LEVELS</p>
                                </div>
                                <div class="text-center">
                                    <p class="font-display text-xl font-bold text-ok">{{ $academicGroup->teachers()->count() }}</p>
                                    <p class="font-mono text-[9px] tracking-[0.14em] text-muted">TEACHERS</p>
                                </div>
                            </div>

                            {{-- Right: Actions --}}
                            <div class="flex items-center gap-2">
                                <a href="{{ route('academic-groups.show', ['academic_group' => $academicGroup]) }}"
                                   class="inline-flex items-center gap-1.5 rounded border border-line-strong px-3 py-2 font-mono text-[10.5px] font-semibold uppercase tracking-wider text-ink-soft transition hover:bg-surface-2 hover:text-ink">
                                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                                    <span class="hidden sm:inline">View</span>
                                </a>
                                @can('administrate')
                                    <a href="{{ route('academic-groups.edit', ['academic_group' => $academicGroup]) }}"
                                       class="inline-flex items-center gap-1.5 rounded border border-warn/40 px-3 py-2 font-mono text-[10.5px] font-semibold uppercase tracking-wider text-warn transition hover:bg-warn/10">
                                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                                        <span class="hidden sm:inline">Edit</span>
                                    </a>
                                    <button type="button"
                                            x-data="{}"
                                            x-on:click="$store.deleteForm.show(
                                                'Delete Academic Group',
                                                'Are you sure you want to delete this academic group?\n\nThis action cannot be undone and will affect all associated levels and teachers.',
                                                '{{ route('academic-groups.destroy', ['academic_group' => $academicGroup]) }}'
                                            )"
                                            class="inline-flex items-center gap-1.5 rounded border border-accent/40 px-3 py-2 font-mono text-[10.5px] font-semibold uppercase tracking-wider text-accent transition hover:bg-accent/10">
                                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        <span class="hidden sm:inline">Delete</span>
                                    </button>
                                @endcan
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>

            {{-- Pagination --}}
            @if($academicGroups->hasPages())
                <div class="mt-6 flex flex-wrap items-center justify-between gap-3">
                    <p class="font-mono text-[10.5px] tracking-wider text-muted">
                        SHOWING {{ $academicGroups->firstItem() }}–{{ $academicGroups->lastItem() }} OF {{ $academicGroups->total() }}
                    </p>
                    {{ $academicGroups->appends(request()->query())->links() }}
                </div>
            @endif
        @else
            {{-- ============ Empty State ============ --}}
            <x-exam.empty
                title="No Academic Groups Yet"
                description="Get started by creating your first academic group to organize your educational structure."
            >
                @can('administrate')
                    <a href="{{ route('academic-groups.create') }}"
                       class="mt-5 inline-flex items-center gap-1.5 whitespace-nowrap rounded bg-primary px-5 py-2.5 text-[13.5px] font-semibold tracking-wide text-on-primary transition hover:-translate-y-px hover:bg-primary-hi motion-reduce:transition-none">
                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M12 4v16m8-8H4"/></svg>
                        Create Academic Group
                    </a>
                @endcan
            </x-exam.empty>
        @endif
    </x-exam.page>

    {{-- Search Filtering --}}
    <script>
        document.getElementById('search-input').addEventListener('input', function(e) {
            const searchTerm = e.target.value.toLowerCase();
            const groups = document.querySelectorAll('.group-item');
            groups.forEach(group => {
                const name = group.dataset.name;
                group.style.display = name.includes(searchTerm) ? '' : 'none';
            });
        });
    </script>
</x-layouts.app>
