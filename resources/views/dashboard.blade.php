<x-layouts.app :has-action="false" page-name="Dashboard">
    @php
        $user = Auth::user();
        $primaryRole = $user->role->value;
        $isImpersonating = session()->has('impersonated_by');
    @endphp

    <x-exam.page>

        {{-- Debug strip --}}
        @if(config('app.debug'))
            <div class="mb-5 flex flex-wrap items-center gap-x-5 gap-y-1 rounded-md border border-warn/40 bg-warn/10 px-4 py-2.5 font-mono text-[10.5px] tracking-wide text-ink-soft">
                <span class="font-semibold uppercase tracking-[0.18em] text-warn">Debug</span>
                <span>ROLE: <b class="text-ink">{{ $primaryRole }}</b></span>
                <span>ALL: <b class="text-ink">{{ implode(', ', $user->getRoleNames()) }}</b></span>
                <span>IMPERSONATING: <b class="text-ink">{{ $isImpersonating ? 'YES' : 'NO' }}</b></span>
            </div>
        @endif

        @auth
            <livewire:trial-expiration-banner/>
        @endauth

        {{-- ============ Role dispatch ============ --}}
        @if($primaryRole === 'student')
            @livewire('students.dashboard')
        @elseif($primaryRole === 'teacher')
            @livewire('teachers.dashboard')
        @elseif($primaryRole === 'librarian')
            @livewire('librarians.library-dashboard')
        @elseif(in_array($primaryRole, ['admin', 'owner']))
            <livewire:administrators.overview/>

            @if($primaryRole === 'owner')
                <x-exam.card class="mt-6" :padded="false">
                    <x-slot:header>
                        <p class="font-mono text-[10.5px] tracking-[0.14em] text-muted">PLATFORM CONFIGURATION · PRICING</p>
                    </x-slot:header>
                    <div class="flex flex-col gap-4 p-5 sm:flex-row sm:items-center sm:justify-between">
                        <div>
                            <h3 class="font-display text-lg font-bold text-ink">Manage Pricing</h3>
                            <p class="mt-1 text-sm text-ink-soft">Update subscription pricing used across the platform.</p>
                        </div>
                        <a href="{{ route('admin.pricing-settings.edit') }}"
                           class="inline-flex items-center justify-center whitespace-nowrap rounded bg-primary px-5 py-2.5 text-[13.5px] font-semibold tracking-wide text-on-primary transition hover:-translate-y-px hover:bg-primary-hi motion-reduce:transition-none">
                            Open Pricing Settings
                        </a>
                    </div>
                </x-exam.card>
            @endif
        @elseif($primaryRole === 'moderator')
            @livewire('moderator.dashboard')
        @elseif($primaryRole === 'author')
            @livewire('authors.dashboard')
        @elseif($primaryRole === 'parent')
            @livewire('parent.dashboard')
        @elseif($primaryRole === 'guest')
            @livewire('guests.guest-dashboard')
        @else
            <x-exam.callout tone="danger" class="mt-6">
                <p class="text-sm">
                    <b class="font-semibold text-ink">No dashboard found for role:</b> {{ $primaryRole }}.
                    Please contact your administrator.
                </p>
            </x-exam.callout>
        @endif

        {{-- Hidden token-usage components (kept for sidebar/widget use) --}}
        <div class="hidden"><livewire:chats.token-usage-monitor/></div>
        <div class="hidden w-64"><livewire:chats.token-usage-horizontal/></div>
        <div class="hidden w-16"><livewire:chats.token-usage-vertical/></div>
        <div class="hidden w-full max-w-xs"><livewire:chats.token-usage-circular/></div>
        <div class="hidden"><livewire:subscription-features-banner placement="dashboard"/></div>

        {{-- ============ COURSE REGISTRY ============ --}}
        @if(in_array(auth()->user()->role->value, ['admin', 'owner', 'moderator', 'guest', 'teacher']) && Route::is('dashboard'))
            @if($academicSubjects->count() || request()->hasAny(['search', 'academic_group', 'academic_level']))
                @php
                    $pageSubjects = $academicSubjects->getCollection();
                    $groupedSubjects = $pageSubjects->groupBy(fn ($s) => $s->academicLevel->academicGroup->name ?? 'General');
                    $activeFilterCount = collect(['search', 'academic_group', 'academic_level'])
                        ->filter(fn ($k) => filled(request($k)))->count();
                @endphp

                <section class="mt-12">
                    {{-- Section head --}}
                    <div class="mb-6 flex flex-wrap items-end justify-between gap-4">
                        <div>
                            <p class="mb-2 flex items-center gap-2 font-mono text-[11px] tracking-[0.14em] text-ink-soft">
                                <span class="h-2 w-2 bg-accent"></span>REGISTRAR'S DESK · SUBJECT CATALOG
                            </p>
                            <h3 class="font-display text-2xl font-bold text-ink md:text-3xl">Course Registry</h3>
                        </div>

                        <div class="flex items-center gap-1 rounded-md border border-line bg-surface p-1">
                            <button onclick="toggleView('grid')" id="grid-btn"
                                    class="rounded px-3 py-1.5 font-mono text-[10.5px] font-semibold uppercase tracking-[0.12em] transition">Grid</button>
                            <button onclick="toggleView('list')" id="list-btn"
                                    class="rounded px-3 py-1.5 font-mono text-[10.5px] font-semibold uppercase tracking-[0.12em] text-muted transition hover:text-ink">List</button>
                        </div>
                    </div>

                    {{-- Catalog command bar --}}
                    <form method="GET" action="{{ route('dashboard') }}" class="mb-6">
                        <div class="flex gap-2">
                            <div class="relative flex-1">
                                <svg class="pointer-events-none absolute left-4 top-1/2 h-4 w-4 -translate-y-1/2 text-muted" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd"/>
                                </svg>
                                <input type="text" name="search" value="{{ $filters['search'] ?? '' }}"
                                       placeholder="Search the registry — subject name, code, level…"
                                       class="w-full rounded-md border border-line-strong bg-surface py-3 pl-11 pr-4 text-sm text-ink placeholder:text-muted transition focus:border-primary focus:outline-none focus:ring-[3px] focus:ring-primary/20">
                            </div>
                            <button type="submit"
                                    class="inline-flex items-center justify-center whitespace-nowrap rounded bg-primary px-5 text-[13.5px] font-semibold tracking-wide text-on-primary transition hover:bg-primary-hi motion-reduce:transition-none">
                                Search
                            </button>
                        </div>

                        {{-- Compact filter rail --}}
                        <div class="mt-3 flex flex-wrap items-center gap-2.5">
                            <div class="relative">
                                <select name="academic_group" id="academic_group" onchange="updateLevels()"
                                        class="appearance-none rounded-md border border-line-strong bg-surface py-2 pl-3 pr-8 font-mono text-[11px] tracking-wide text-ink-soft transition hover:border-ink-soft focus:border-primary focus:outline-none">
                                    <option value="">ALL GROUPS</option>
                                    @foreach($academicGroups as $group)
                                        <option value="{{ $group->id }}" {{ ($filters['academic_group'] ?? '') == $group->id ? 'selected' : '' }}>
                                            {{ strtoupper($group->name) }}
                                        </option>
                                    @endforeach
                                </select>
                                <svg class="pointer-events-none absolute right-2.5 top-1/2 h-3 w-3 -translate-y-1/2 text-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            </div>

                            <div class="relative">
                                <select name="academic_level" id="academic_level"
                                        class="appearance-none rounded-md border border-line-strong bg-surface py-2 pl-3 pr-8 font-mono text-[11px] tracking-wide text-ink-soft transition hover:border-ink-soft focus:border-primary focus:outline-none">
                                    <option value="">ALL LEVELS</option>
                                    @foreach($academicLevels as $level)
                                        <option value="{{ $level->id }}" data-group="{{ $level->academic_group_id }}"
                                            {{ ($filters['academic_level'] ?? '') == $level->id ? 'selected' : '' }}>
                                            {{ strtoupper($level->name) }}
                                        </option>
                                    @endforeach
                                </select>
                                <svg class="pointer-events-none absolute right-2.5 top-1/2 h-3 w-3 -translate-y-1/2 text-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            </div>

                            <span class="mx-1 h-5 w-px bg-line-strong"></span>

                            <div class="relative">
                                <select name="sort_by" id="sort_by"
                                        class="appearance-none rounded-md border border-line-strong bg-surface py-2 pl-3 pr-8 font-mono text-[11px] tracking-wide text-ink-soft transition hover:border-ink-soft focus:border-primary focus:outline-none">
                                    <option value="name" {{ ($filters['sort_by'] ?? '') == 'name' ? 'selected' : '' }}>SORT: NAME</option>
                                    <option value="group" {{ ($filters['sort_by'] ?? '') == 'group' ? 'selected' : '' }}>SORT: GROUP</option>
                                    <option value="level" {{ ($filters['sort_by'] ?? '') == 'level' ? 'selected' : '' }}>SORT: LEVEL</option>
                                    <option value="quizzes_count" {{ ($filters['sort_by'] ?? '') == 'quizzes_count' ? 'selected' : '' }}>SORT: QUIZZES</option>
                                    <option value="examinations_count" {{ ($filters['sort_by'] ?? '') == 'examinations_count' ? 'selected' : '' }}>SORT: EXAMS</option>
                                </select>
                                <svg class="pointer-events-none absolute right-2.5 top-1/2 h-3 w-3 -translate-y-1/2 text-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            </div>

                            <div class="relative">
                                <select name="sort_order" id="sort_order"
                                        class="appearance-none rounded-md border border-line-strong bg-surface py-2 pl-3 pr-8 font-mono text-[11px] tracking-wide text-ink-soft transition hover:border-ink-soft focus:border-primary focus:outline-none">
                                    <option value="asc" {{ ($filters['sort_order'] ?? '') == 'asc' ? 'selected' : '' }}>↑ ASC</option>
                                    <option value="desc" {{ ($filters['sort_order'] ?? '') == 'desc' ? 'selected' : '' }}>↓ DESC</option>
                                </select>
                                <svg class="pointer-events-none absolute right-2.5 top-1/2 h-3 w-3 -translate-y-1/2 text-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            </div>

                            @if($activeFilterCount > 0)
                                <a href="{{ route('dashboard') }}"
                                   class="ml-1 font-mono text-[11px] font-semibold tracking-wider text-accent transition hover:opacity-75">
                                    CLEAR ✕
                                </a>
                            @endif
                        </div>

                        {{-- Active filter seals --}}
                        @if($activeFilterCount > 0)
                            <div class="mt-3 flex flex-wrap items-center gap-2">
                                <span class="font-mono text-[10px] tracking-[0.14em] text-muted">ACTIVE:</span>
                                @if($filters['search'])
                                    <a href="{{ request()->fullUrlWithQuery(['search' => null]) }}"
                                       class="inline-flex items-center gap-1.5 rounded-full border border-line bg-surface-2 px-2.5 py-1 font-mono text-[10px] tracking-wide text-ink-soft transition hover:border-accent/50 hover:text-accent">
                                        SEARCH: "{{ $filters['search'] }}" ✕
                                    </a>
                                @endif
                                @if($filters['academic_group'])
                                    <a href="{{ request()->fullUrlWithQuery(['academic_group' => null]) }}"
                                       class="inline-flex items-center gap-1.5 rounded-full border border-line bg-surface-2 px-2.5 py-1 font-mono text-[10px] tracking-wide text-ink-soft transition hover:border-accent/50 hover:text-accent">
                                        GROUP: {{ strtoupper($academicGroups->find($filters['academic_group'])->name) }} ✕
                                    </a>
                                @endif
                                @if($filters['academic_level'])
                                    <a href="{{ request()->fullUrlWithQuery(['academic_level' => null]) }}"
                                       class="inline-flex items-center gap-1.5 rounded-full border border-line bg-surface-2 px-2.5 py-1 font-mono text-[10px] tracking-wide text-ink-soft transition hover:border-accent/50 hover:text-accent">
                                        LEVEL: {{ strtoupper($academicLevels->find($filters['academic_level'])->name) }} ✕
                                    </a>
                                @endif
                            </div>
                        @endif
                    </form>

                    @if($academicSubjects->count())
                        {{-- Ledger summary strip --}}
                        <div class="mb-8 grid grid-cols-4 divide-x divide-line rounded-md border border-line bg-surface">
                            <div class="px-2 py-3.5 text-center sm:px-4">
                                <p class="font-display text-xl font-bold text-ink sm:text-2xl">{{ $academicSubjects->total() }}</p>
                                <p class="mt-0.5 font-mono text-[9px] tracking-[0.18em] text-muted sm:text-[9.5px]">TOTAL</p>
                            </div>
                            <div class="px-2 py-3.5 text-center sm:px-4">
                                <p class="font-display text-xl font-bold text-ink sm:text-2xl">{{ $academicSubjects->count() }}</p>
                                <p class="mt-0.5 font-mono text-[9px] tracking-[0.18em] text-muted sm:text-[9.5px]">SHOWING</p>
                            </div>
                            <div class="px-2 py-3.5 text-center sm:px-4">
                                <p class="font-display text-xl font-bold text-ink sm:text-2xl">{{ $groupedSubjects->count() }}</p>
                                <p class="mt-0.5 font-mono text-[9px] tracking-[0.18em] text-muted sm:text-[9.5px]">SECTIONS</p>
                            </div>
                            <div class="px-2 py-3.5 text-center sm:px-4">
                                <p class="font-display text-xl font-bold {{ $activeFilterCount ? 'text-accent' : 'text-ink' }} sm:text-2xl">{{ $activeFilterCount }}</p>
                                <p class="mt-0.5 font-mono text-[9px] tracking-[0.18em] text-muted sm:text-[9.5px]">FILTERS</p>
                            </div>
                        </div>

                        {{-- GRID VIEW: grouped syllabus sections --}}
                        <div id="grid-view" class="mb-8 space-y-10">
                            @foreach($groupedSubjects as $groupName => $subjects)
                                <section>
                                    <div class="mb-4 border-b border-line pb-2">
                                        <div class="flex flex-wrap items-baseline gap-3">
                                            <span class="rounded-[3px] bg-accent/10 px-1.5 py-0.5 font-mono text-[10px] font-semibold tracking-[0.12em] text-accent">
                                                SEC. {{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}
                                            </span>
                                            <h4 class="font-display text-lg font-bold text-ink">{{ $groupName }}</h4>
                                            <span class="ml-auto font-mono text-[10.5px] tracking-wider text-muted">
                                                {{ $subjects->count() }} {{ Str::plural('COURSE', $subjects->count()) }}
                                            </span>
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-1 gap-5 md:grid-cols-2 xl:grid-cols-3">
                                        @foreach($subjects as $academicSubject)
                                            <article class="group flex flex-col rounded-md border border-line bg-surface p-5 shadow-[0_1px_2px_rgba(20,24,34,0.04)] transition hover:-translate-y-0.5 hover:border-primary/40 hover:shadow-[0_10px_30px_-12px_rgba(20,24,34,0.30)] motion-reduce:transition-none">
                                                {{-- Code stamp + level --}}
                                                <div class="mb-3 flex items-start justify-between gap-2">
                                                    @if($academicSubject->code)
                                                        <span class="rounded-[3px] bg-accent/10 px-2 py-1 font-mono text-[10px] font-semibold tracking-[0.12em] text-accent">
                                                            {{ strtoupper($academicSubject->code) }}
                                                        </span>
                                                    @else
                                                        <span class="rounded-[3px] bg-surface-2 px-2 py-1 font-mono text-[10px] tracking-[0.12em] text-muted">NO CODE</span>
                                                    @endif
                                                    <span class="font-mono text-[10px] tracking-wider text-muted">
                                                        {{ strtoupper($academicSubject->academicLevel->name) }}
                                                    </span>
                                                </div>

                                                <h5 class="font-display text-base font-bold leading-snug text-ink transition group-hover:text-primary">
                                                    {{ $academicSubject->name }}
                                                </h5>

                                                {{-- Dotted-leader stats --}}
                                                <div class="mt-4 flex-1 space-y-2">
                                                    <div class="flex items-baseline gap-2 font-mono text-[11px] tracking-wide text-muted">
                                                        <span>QUIZZES</span>
                                                        <span class="flex-1 -translate-y-[3px] border-b border-dotted border-line-strong"></span>
                                                        <span class="font-semibold text-ink">{{ $academicSubject->quizzes_count ?? $academicSubject->quizzes()->count() }}</span>
                                                    </div>
                                                    @can('privileged', $currentTeam)
                                                        <div class="flex items-baseline gap-2 font-mono text-[11px] tracking-wide text-muted">
                                                            <span>EXAMS</span>
                                                            <span class="flex-1 -translate-y-[3px] border-b border-dotted border-line-strong"></span>
                                                            <span class="font-semibold text-ink">{{ $academicSubject->examinations_count ?? $academicSubject->examinations()->count() }}</span>
                                                        </div>
                                                    @endcan
                                                </div>

                                                {{-- Actions --}}
                                                <div class="mt-5 flex items-center justify-between gap-3 border-t border-dashed border-line pt-4">
                                                    <a href="{{ route('quizzes.index', ['academic_subject' => $academicSubject, 'academic_level' => $academicSubject->academicLevel, 'academic_group' => $academicSubject->academicLevel->academicGroup]) }}"
                                                       class="inline-flex items-center justify-center whitespace-nowrap rounded bg-primary px-3.5 py-2 font-mono text-[10.5px] font-semibold uppercase tracking-wider text-on-primary transition hover:bg-primary-hi motion-reduce:transition-none">
                                                        Quizzes →
                                                    </a>
                                                    @can('privileged', $currentTeam)
                                                        <a href="{{ route('examinations.index', ['academic_subject' => $academicSubject, 'academic_level' => $academicSubject->academicLevel, 'academic_group' => $academicSubject->academicLevel->academicGroup]) }}"
                                                           class="border-b border-dashed border-transparent font-mono text-[10.5px] font-semibold uppercase tracking-wider text-primary transition hover:border-current hover:text-primary-hi">
                                                            Exams
                                                        </a>
                                                    @endcan
                                                </div>
                                            </article>
                                        @endforeach
                                    </div>
                                </section>
                            @endforeach
                        </div>

                        {{-- LIST VIEW: ledger table --}}
                        <div id="list-view" class="mb-8 hidden">
                            <x-exam.table :headers="['Code', 'Course', 'Level', 'Quizzes', 'Exams', '']">
                                @foreach($academicSubjects as $academicSubject)
                                    <tr class="transition hover:bg-surface-2/60">
                                        <td class="px-4 py-3.5">
                                            @if($academicSubject->code)
                                                <span class="rounded-[3px] bg-accent/10 px-1.5 py-0.5 font-mono text-[10px] font-semibold tracking-[0.12em] text-accent">
                                                    {{ strtoupper($academicSubject->code) }}
                                                </span>
                                            @else
                                                <span class="font-mono text-[10px] text-muted">—</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3.5">
                                            <p class="font-display text-sm font-bold text-ink">{{ $academicSubject->name }}</p>
                                            <p class="mt-0.5 font-mono text-[10px] tracking-wider text-muted">
                                                {{ strtoupper($academicSubject->academicLevel->academicGroup->name) }}
                                            </p>
                                        </td>
                                        <td class="px-4 py-3.5 font-mono text-[11px] text-ink-soft">{{ strtoupper($academicSubject->academicLevel->name) }}</td>
                                        <td class="px-4 py-3.5 font-mono text-xs text-ink-soft">{{ $academicSubject->quizzes_count ?? $academicSubject->quizzes()->count() }}</td>
                                        <td class="px-4 py-3.5 font-mono text-xs text-ink-soft">
                                            @can('privileged', $currentTeam)
                                                {{ $academicSubject->examinations_count ?? $academicSubject->examinations()->count() }}
                                            @else
                                                —
                                            @endcan
                                        </td>
                                        <td class="px-4 py-3.5">
                                            <div class="flex items-center justify-end gap-3">
                                                <a href="{{ route('quizzes.index', ['academic_subject' => $academicSubject, 'academic_level' => $academicSubject->academicLevel, 'academic_group' => $academicSubject->academicLevel->academicGroup]) }}"
                                                   class="border-b border-dashed border-transparent font-mono text-[10.5px] font-semibold uppercase tracking-wider text-primary transition hover:border-current hover:text-primary-hi">
                                                    Quizzes
                                                </a>
                                                @can('privileged', $currentTeam)
                                                    <a href="{{ route('examinations.index', ['academic_subject' => $academicSubject, 'academic_level' => $academicSubject->academicLevel, 'academic_group' => $academicSubject->academicLevel->academicGroup]) }}"
                                                       class="border-b border-dashed border-transparent font-mono text-[10.5px] font-semibold uppercase tracking-wider text-muted transition hover:border-current hover:text-ink">
                                                        Exams
                                                    </a>
                                                @endcan
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </x-exam.table>
                        </div>

                        {{-- Pagination --}}
                        <div class="flex flex-wrap items-center justify-between gap-3">
                            <p class="font-mono text-[10.5px] tracking-wider text-muted">
                                SHOWING {{ $academicSubjects->firstItem() }}–{{ $academicSubjects->lastItem() }} OF {{ $academicSubjects->total() }}
                            </p>
                            {{ $academicSubjects->appends(request()->query())->links() }}
                        </div>
                    @else
                        {{-- No matches --}}
                        <x-exam.empty compact
                                      title="No courses found"
                                      description="No courses match the current filters. Adjust your search criteria."
                        >
                            <a href="{{ route('dashboard') }}"
                               class="mt-5 inline-flex items-center justify-center whitespace-nowrap rounded border border-line-strong px-5 py-2.5 text-[13.5px] font-semibold text-ink-soft transition hover:bg-surface-2 hover:text-ink">
                                Clear Filters
                            </a>
                        </x-exam.empty>
                    @endif
                </section>

                <script>
                    function toggleView(viewType) {
                        const gridView = document.getElementById('grid-view');
                        const listView = document.getElementById('list-view');
                        const gridBtn = document.getElementById('grid-btn');
                        const listBtn = document.getElementById('list-btn');
                        const active = ['bg-primary', 'text-on-primary'];
                        const inactive = ['text-muted', 'hover:text-ink'];

                        if (viewType === 'grid') {
                            gridView.classList.remove('hidden');
                            listView.classList.add('hidden');
                            gridBtn.classList.add(...active); gridBtn.classList.remove(...inactive);
                            listBtn.classList.remove(...active); listBtn.classList.add(...inactive);
                        } else {
                            listView.classList.remove('hidden');
                            gridView.classList.add('hidden');
                            listBtn.classList.add(...active); listBtn.classList.remove(...inactive);
                            gridBtn.classList.remove(...active); gridBtn.classList.add(...inactive);
                        }
                        localStorage.setItem('courseViewPreference', viewType);
                    }

                    function updateLevels() {
                        const groupSelect = document.getElementById('academic_group');
                        const levelSelect = document.getElementById('academic_level');
                        const selectedGroupId = groupSelect.value;

                        if (!selectedGroupId) {
                            Array.from(levelSelect.options).forEach(option => { option.style.display = 'block'; });
                            return;
                        }

                        Array.from(levelSelect.options).forEach(option => {
                            if (option.value === '') { option.style.display = 'block'; return; }
                            option.style.display = option.getAttribute('data-group') === selectedGroupId ? 'block' : 'none';
                        });

                        const current = levelSelect.options[levelSelect.selectedIndex];
                        if (current && current.style.display === 'none') levelSelect.value = '';
                    }

                    document.addEventListener('DOMContentLoaded', function () {
                        toggleView(localStorage.getItem('courseViewPreference') || 'grid');
                        updateLevels();
                    });
                </script>
            @else
                {{-- Registry completely empty (was the hidden marketing block) --}}
                <section class="mt-12">
                    <x-exam.empty
                        title="The registry is empty"
                        description="No courses have been catalogued for you yet. Subscribe to unlock the full subject catalog."
                    >
                        <a href="{{ route('subscriptions.create') }}"
                           class="mt-5 inline-flex items-center justify-center whitespace-nowrap rounded bg-primary px-5 py-2.5 text-[13.5px] font-semibold tracking-wide text-on-primary transition hover:-translate-y-px hover:bg-primary-hi motion-reduce:transition-none">
                            Subscribe Now
                        </a>
                    </x-exam.empty>
                </section>
            @endif
        @endif

    </x-exam.page>
</x-layouts.app>
