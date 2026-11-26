<x-layouts.app
    :main-only="true"
    title="Subtopics · {{ $academic_topic->name }}"
    page-name="Academic Subtopics"
    :has-action="false">
    <x-exam.page>
        @php
            $leafTopic   = $academic_topic;
            $leafSubject = $academicSubject;
            $leafLevel   = $academicSubject->academicLevel;
            $leafGroup   = $academicSubject->academicLevel->academicGroup;

            $routeParams = [
                'academic_topic'   => $academic_topic,
                'academic_subject' => getRouteParameter('academic_subject'),
                'academic_level'   => getRouteParameter('academic_level'),
                'academic_group'   => getRouteParameter('academic_group'),
            ];

            $topicUrl  = route('academic-topics.show', $routeParams);
            $createUrl = route('subtopics.create', $routeParams);

            $subtopicUrl = fn ($subtopic, $verb) => route("subtopics.{$verb}", array_merge($routeParams, ['subtopic' => $subtopic]));

            $seed = $subtopics->getCollection()->map(fn ($s, $i) => [
                'i'       => $i,
                'name'    => $s->name,
                'created' => $s->created_at->timestamp,
            ])->values();
        @endphp

        {{-- Breadcrumb --}}
        <x-exam.crumbs :items="[
            ['label' => 'Academic Groups', 'href' => route('academic-groups.index')],
            ['label' => $leafGroup->name, 'href' => route('academic-groups.show', ['academic_group' => $leafGroup])],
            ['label' => 'Academic Levels', 'href' => route('academic-levels.index', ['academic_group' => $leafGroup])],
            ['label' => $leafLevel->name, 'href' => route('academic-levels.show', ['academic_level' => $leafLevel, 'academic_group' => $leafGroup])],
            ['label' => 'Subjects', 'href' => route('academic-subjects.index', ['academic_level' => $leafLevel, 'academic_group' => $leafGroup])],
            ['label' => $leafSubject->name, 'href' => route('academic-subjects.show', ['academic_subject' => $leafSubject, 'academic_level' => $leafLevel, 'academic_group' => $leafGroup])],
            ['label' => 'Topics', 'href' => route('academic-topics.index', ['academic_subject' => $leafSubject, 'academic_level' => $leafLevel, 'academic_group' => $leafGroup])],
            ['label' => $leafTopic->name, 'href' => $topicUrl],
            ['label' => 'Subtopics'],
        ]" />

        <div x-data="{
                 q: '',
                 sort: 'order',
                 items: @js($seed),
                 get matches() {
                     const needle = this.q.trim().toLowerCase();
                     let rows = this.items.filter(r => !needle || r.name.toLowerCase().includes(needle));
                     if (this.sort === 'az') rows = [...rows].sort((a, b) => a.name.localeCompare(b.name));
                     if (this.sort === 'za') rows = [...rows].sort((a, b) => b.name.localeCompare(a.name));
                     if (this.sort === 'new') rows = [...rows].sort((a, b) => b.created - a.created);
                     if (this.sort === 'old') rows = [...rows].sort((a, b) => a.created - b.created);
                     return rows.map(r => r.i);
                 },
                 reorder() {
                     const box = this.$refs.register;
                     if (!box) return;
                     this.matches.forEach((idx, position) => {
                         const node = box.querySelector(`[data-idx='${idx}']`);
                         if (node) node.style.order = position;
                     });
                 }
             }"
             x-effect="reorder()">

            {{-- ============ Header ============ --}}
            <div class="mb-6 flex flex-wrap items-start justify-between gap-4">
                <div class="min-w-0">
                    <p class="mb-2 flex items-center gap-2 font-mono text-[11px] tracking-[0.14em] text-ink-soft">
                        <span class="h-2 w-2 bg-accent"></span>CURRICULUM REGISTER · LEAF RECORDS
                    </p>
                    <h1 class="font-display text-2xl font-bold leading-tight text-ink md:text-3xl">{{ $leafTopic->name }}</h1>
                    <div class="mt-2 flex flex-wrap items-center gap-3 font-mono text-[10.5px] tracking-wide text-muted">
                        <a href="{{ route('academic-subjects.show', ['academic_subject' => $leafSubject, 'academic_level' => $leafLevel, 'academic_group' => $leafGroup]) }}"
                           class="transition hover:text-ink">{{ strtoupper($leafSubject->name) }}</a>
                        <span class="text-line-strong">|</span>
                        <a href="{{ route('academic-levels.show', ['academic_level' => $leafLevel, 'academic_group' => $leafGroup]) }}"
                           class="transition hover:text-ink">{{ strtoupper($leafLevel->name) }}</a>
                        <span class="text-line-strong">|</span>
                        <span>{{ $subtopics->total() }} {{ Str::plural('SUBTOPIC', $subtopics->total()) }}</span>
                    </div>
                </div>

                <div class="flex flex-wrap items-center gap-2">
                    <a href="{{ $topicUrl }}"
                       class="inline-flex items-center gap-1.5 whitespace-nowrap rounded border border-line-strong px-4 py-2.5 font-mono text-[10.5px] font-semibold uppercase tracking-wider text-ink-soft transition hover:bg-surface-2 hover:text-ink">
                        Topic Details
                        <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </a>
                    @can('administrate')
                        <a href="{{ $createUrl }}"
                           class="inline-flex items-center gap-1.5 whitespace-nowrap rounded bg-primary px-5 py-2.5 text-[13.5px] font-semibold tracking-wide text-on-primary shadow-[0_1px_0_rgba(20,24,34,0.2),0_8px_18px_-10px_rgba(20,24,34,0.5)] transition hover:-translate-y-px hover:bg-primary-hi motion-reduce:transition-none">
                            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M12 4v16m8-8H4"/></svg>
                            Add Subtopic
                        </a>
                    @endcan
                </div>
            </div>

            <x-exam.flash />

            @if($subtopics->count())
                {{-- ============ Stats strip ============ --}}
                <div class="mb-5 grid grid-cols-2 divide-x divide-y divide-line rounded-md border border-line bg-surface sm:grid-cols-4 sm:divide-y-0">
                    <div class="px-4 py-3.5 text-center">
                        <p class="font-display text-xl font-bold text-ink sm:text-2xl">{{ $subtopics->total() }}</p>
                        <p class="mt-0.5 font-mono text-[9px] tracking-[0.18em] text-muted sm:text-[9.5px]">SUBTOPICS</p>
                    </div>
                    <div class="px-4 py-3.5 text-center">
                        <p class="font-display text-xl font-bold text-primary sm:text-2xl">{{ $subtopics->count() }}</p>
                        <p class="mt-0.5 font-mono text-[9px] tracking-[0.18em] text-muted sm:text-[9.5px]">ON THIS PAGE</p>
                    </div>
                    <div class="px-4 py-3.5 text-center">
                        <p class="font-display text-xl font-bold text-ok sm:text-2xl">{{ $subtopics->currentPage() }}</p>
                        <p class="mt-0.5 font-mono text-[9px] tracking-[0.18em] text-muted sm:text-[9.5px]">PAGE</p>
                    </div>
                    <div class="px-4 py-3.5 text-center">
                        <p class="font-display text-xl font-bold sm:text-2xl"
                           :class="matches.length === items.length ? 'text-ink' : 'text-accent'"
                           x-text="matches.length">{{ $subtopics->count() }}</p>
                        <p class="mt-0.5 font-mono text-[9px] tracking-[0.18em] text-muted sm:text-[9.5px]">MATCHING</p>
                    </div>
                </div>

                {{-- ============ Toolbar ============ --}}
                <div class="mb-5 flex flex-col gap-3 rounded-md border border-line bg-surface p-3 sm:flex-row sm:items-center">
                    <div class="relative flex-1">
                        <svg class="pointer-events-none absolute left-3.5 top-1/2 h-4 w-4 -translate-y-1/2 text-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        <input type="search" x-model="q" placeholder="Filter subtopics on this page…"
                               class="w-full rounded border border-line-strong bg-surface py-2.5 pl-10 pr-3 text-sm text-ink placeholder:text-muted transition hover:border-ink-soft focus:border-primary focus:outline-none focus:ring-[3px] focus:ring-primary/20">
                    </div>

                    <div class="relative shrink-0">
                        <select x-model="sort"
                                class="w-full appearance-none rounded border border-line-strong bg-surface py-2.5 pl-3 pr-9 font-mono text-[11px] tracking-wide text-ink-soft transition hover:border-ink-soft focus:border-primary focus:outline-none sm:w-auto">
                            <option value="order">REGISTER ORDER</option>
                            <option value="az">NAME A → Z</option>
                            <option value="za">NAME Z → A</option>
                            <option value="new">NEWEST FIRST</option>
                            <option value="old">OLDEST FIRST</option>
                        </select>
                        <svg class="pointer-events-none absolute right-3 top-1/2 h-3.5 w-3.5 -translate-y-1/2 text-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </div>

                    <button type="button" x-show="q.length > 0" x-cloak @click="q = ''"
                            class="shrink-0 whitespace-nowrap font-mono text-[10.5px] font-semibold uppercase tracking-wider text-accent transition hover:opacity-75">
                        Clear ✕
                    </button>
                </div>

                {{-- ============ Register ============ --}}
                <div x-ref="register" class="flex flex-col gap-3">
                    @foreach($subtopics as $subtopic)
                        <article data-idx="{{ $loop->index }}"
                                 x-show="matches.includes({{ $loop->index }})"
                                 class="group rounded-md border border-line bg-surface p-5 shadow-[0_1px_2px_rgba(20,24,34,0.04)] transition hover:border-primary/40 hover:shadow-[0_10px_30px_-12px_rgba(20,24,34,0.30)] motion-reduce:transition-none">

                            <div class="flex flex-wrap items-start justify-between gap-4">
                                {{-- Identity --}}
                                <div class="flex min-w-0 flex-1 items-start gap-4">
                                    <span class="grid h-11 w-11 shrink-0 place-items-center rounded-md bg-accent/10 font-mono text-sm font-bold text-accent">
                                        {{ str_pad($subtopics->firstItem() + $loop->index, 2, '0', STR_PAD_LEFT) }}
                                    </span>

                                    <div class="min-w-0 flex-1">
                                        <a href="{{ $subtopicUrl($subtopic, 'show') }}"
                                           class="block truncate font-display text-base font-bold text-ink transition hover:text-primary">
                                            {{ $subtopic->name }}
                                        </a>

                                        @if(!empty($subtopic->description))
                                            <p class="mt-1.5 line-clamp-2 text-sm leading-relaxed text-ink-soft">
                                                {{ Str::limit($subtopic->description, 150) }}
                                            </p>
                                        @endif

                                        <div class="mt-3 max-w-xs space-y-1.5">
                                            <div class="flex items-baseline gap-2 font-mono text-[11px] tracking-wide text-muted">
                                                <span>FILED</span>
                                                <span class="flex-1 -translate-y-[3px] border-b border-dotted border-line-strong"></span>
                                                <span class="font-semibold text-ink">{{ $subtopic->created_at->format('M d, Y') }}</span>
                                            </div>
                                            <div class="flex items-baseline gap-2 font-mono text-[11px] tracking-wide text-muted">
                                                <span>AGE</span>
                                                <span class="flex-1 -translate-y-[3px] border-b border-dotted border-line-strong"></span>
                                                <span class="font-semibold text-ink">{{ strtoupper($subtopic->created_at->diffForHumans()) }}</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Actions --}}
                                <div class="flex shrink-0 items-center gap-2">
                                    <a href="{{ $subtopicUrl($subtopic, 'show') }}"
                                       class="inline-flex items-center justify-center rounded border border-line-strong px-3 py-1.5 font-mono text-[10.5px] font-semibold uppercase tracking-wider text-ink-soft transition hover:bg-surface-2 hover:text-ink">
                                        View
                                    </a>
                                    @can('administrate')
                                        <a href="{{ $subtopicUrl($subtopic, 'edit') }}"
                                           class="inline-flex items-center justify-center rounded border border-warn/40 px-3 py-1.5 font-mono text-[10.5px] font-semibold uppercase tracking-wider text-warn transition hover:bg-warn/10">
                                            Edit
                                        </a>
                                        <button type="button"
                                                x-data="{}"
                                                x-on:click="$store.deleteForm.show('Delete Subtopic', 'Are you sure you want to delete {{ $subtopic->name }}? This action cannot be undone.', '{{ $subtopicUrl($subtopic, 'destroy') }}')"
                                                class="inline-flex items-center justify-center rounded border border-accent/40 px-3 py-1.5 font-mono text-[10.5px] font-semibold uppercase tracking-wider text-accent transition hover:bg-accent/10">
                                            Delete
                                        </button>
                                    @endcan
                                </div>
                            </div>

                            <div class="mt-3 flex items-center justify-between border-t border-dashed border-line pt-3 font-mono text-[10px] tracking-wider text-muted">
                                <span>ID #{{ $subtopic->id }}</span>
                                <span class="truncate pl-3">{{ strtoupper($leafTopic->name) }}</span>
                            </div>
                        </article>
                    @endforeach
                </div>

                {{-- No matches for the live filter --}}
                <div x-show="matches.length === 0" x-cloak class="mt-3">
                    <x-exam.empty compact
                                  title="No subtopics match that filter"
                                  description="Nothing on this page matches your search. Clear the filter or check another page of the register.">
                        <button type="button" @click="q = ''"
                                class="mt-5 inline-flex items-center justify-center whitespace-nowrap rounded border border-line-strong px-5 py-2.5 text-[13.5px] font-semibold text-ink-soft transition hover:bg-surface-2 hover:text-ink">
                            Clear Filter
                        </button>
                    </x-exam.empty>
                </div>

                {{-- Pagination --}}
                @if($subtopics->hasPages())
                    <div class="mt-6 flex flex-wrap items-center justify-between gap-3">
                        <p class="font-mono text-[10.5px] tracking-wider text-muted">
                            SHOWING {{ $subtopics->firstItem() }}–{{ $subtopics->lastItem() }} OF {{ $subtopics->total() }}
                        </p>
                        {{ $subtopics->appends(request()->query())->links() }}
                    </div>
                @endif
            @else
                {{-- ============ Empty register ============ --}}
                <x-exam.empty
                    title="No subtopics filed yet"
                    description="{{ $leafTopic->name }} has no subtopics. Subtopics are the smallest teachable units — the level at which questions and lesson notes are authored.">
                    <div class="mt-5 flex flex-col justify-center gap-2 sm:flex-row">
                        @can('administrate')
                            <a href="{{ $createUrl }}"
                               class="inline-flex items-center justify-center gap-1.5 whitespace-nowrap rounded bg-primary px-5 py-2.5 text-[13.5px] font-semibold tracking-wide text-on-primary transition hover:-translate-y-px hover:bg-primary-hi motion-reduce:transition-none">
                                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M12 4v16m8-8H4"/></svg>
                                Create First Subtopic
                            </a>
                        @endcan
                        <a href="{{ $topicUrl }}"
                           class="inline-flex items-center justify-center whitespace-nowrap rounded border border-line-strong px-5 py-2.5 text-[13.5px] font-semibold text-ink-soft transition hover:bg-surface-2 hover:text-ink">
                            Back to Topic
                        </a>
                    </div>
                </x-exam.empty>
            @endif
        </div>

        <style>
            [x-cloak] { display: none !important; }
        </style>
    </x-exam.page>
</x-layouts.app>
