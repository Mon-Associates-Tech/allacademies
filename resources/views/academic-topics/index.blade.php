<x-layouts.app title="Academic Topics" :show-title-area="false" :page-name="$academicSubject->name">
    <x-exam.page>
        @php
            $topicSubject = $academicSubject;
            $topicLevel   = $academicSubject->academicLevel;
            $topicGroup   = $academicSubject->academicLevel->academicGroup;
        @endphp

        {{-- Breadcrumb --}}
        <x-exam.crumbs :items="[
            ['label' => 'Academic Groups', 'href' => route('academic-groups.index')],
            ['label' => $topicGroup->name, 'href' => route('academic-groups.show', ['academic_group' => $topicGroup])],
            ['label' => 'Academic Levels', 'href' => route('academic-levels.index', ['academic_group' => $topicGroup])],
            ['label' => $topicLevel->name, 'href' => route('academic-levels.show', ['academic_level' => $topicLevel, 'academic_group' => getRouteParameter('academic_group')])],
            ['label' => 'Subjects', 'href' => route('academic-subjects.index', ['academic_level' => $topicLevel, 'academic_group' => getRouteParameter('academic_group')])],
            ['label' => $topicSubject->name, 'href' => route('academic-subjects.show', ['academic_subject' => $topicSubject, 'academic_level' => getRouteParameter('academic_level'), 'academic_group' => getRouteParameter('academic_group')])],
            ['label' => 'Topics'],
        ]" />

        {{-- ============ Header ============ --}}
        <div class="mb-6 flex flex-wrap items-start justify-between gap-4">
            <div class="min-w-0">
                <h1 class="font-display text-2xl font-bold text-ink md:text-3xl">
                    Topics for {{ $topicSubject->name }}
                </h1>
                <div class="mt-2 flex flex-wrap items-center gap-3 font-mono text-[10.5px] tracking-wide text-muted">
                    @if($topicSubject->code)
                        <span class="rounded-[3px] bg-accent/10 px-1.5 py-0.5 font-semibold tracking-[0.12em] text-accent">
                            {{ strtoupper($topicSubject->code) }}
                        </span>
                        <span class="text-line-strong">|</span>
                    @endif
                    <a href="{{ route('academic-levels.show', ['academic_level' => $topicLevel, 'academic_group' => getRouteParameter('academic_group')]) }}"
                       class="transition hover:text-ink">
                        {{ strtoupper($topicLevel->name) }}
                    </a>
                    <span class="text-line-strong">|</span>
                    <a href="{{ route('academic-groups.show', ['academic_group' => $topicGroup]) }}"
                       class="transition hover:text-ink">
                        {{ strtoupper($topicGroup->name) }}
                    </a>
                </div>
                @if($topicSubject->description)
                    <p class="mt-2 max-w-2xl text-sm leading-relaxed text-ink-soft">
                        {{ Str::limit($topicSubject->description, 140) }}
                    </p>
                @endif
            </div>

            @can('administrate')
                <a href="{{ route('academic-topics.create', ['academic_subject' => $topicSubject, 'academic_level' => $topicLevel, 'academic_group' => $topicGroup]) }}"
                   class="inline-flex items-center gap-1.5 whitespace-nowrap rounded bg-primary px-5 py-2.5 text-[13.5px] font-semibold tracking-wide text-on-primary transition hover:-translate-y-px hover:bg-primary-hi motion-reduce:transition-none">
                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M12 4v16m8-8H4"/></svg>
                    New Topic
                </a>
            @endcan
        </div>

        <x-exam.flash />

        @if($academicTopics->count())
            {{-- ============ Stats strip ============ --}}
            <div class="mb-8 grid grid-cols-3 divide-x divide-line rounded-md border border-line bg-surface">
                <div class="px-4 py-3.5 text-center">
                    <p class="font-display text-xl font-bold text-ink sm:text-2xl">{{ $academicTopics->total() }}</p>
                    <p class="mt-0.5 font-mono text-[9px] tracking-[0.18em] text-muted sm:text-[9.5px]">TOTAL TOPICS</p>
                </div>
                <div class="px-4 py-3.5 text-center">
                    <a href="{{ route('academic-subjects.show', ['academic_subject' => $topicSubject, 'academic_level' => getRouteParameter('academic_level'), 'academic_group' => getRouteParameter('academic_group')]) }}"
                       class="inline-block max-w-full truncate font-display text-sm font-bold text-primary transition hover:text-primary-hi sm:text-base">
                        {{ $topicSubject->name }}
                    </a>
                    <p class="mt-0.5 font-mono text-[9px] tracking-[0.18em] text-muted sm:text-[9.5px]">SUBJECT</p>
                </div>
                <div class="px-4 py-3.5 text-center">
                    <a href="{{ route('academic-levels.show', ['academic_level' => $topicLevel, 'academic_group' => getRouteParameter('academic_group')]) }}"
                       class="inline-block max-w-full truncate font-display text-sm font-bold text-ok transition hover:text-primary sm:text-base">
                        {{ $topicLevel->name }}
                    </a>
                    <p class="mt-0.5 font-mono text-[9px] tracking-[0.18em] text-muted sm:text-[9.5px]">LEVEL</p>
                </div>
            </div>

            {{-- ============ Topics register ============ --}}
            <div class="space-y-3">
                @foreach($academicTopics as $academicTopic)
                    @php($topicNumber = str_pad($academicTopics->firstItem() + $loop->index, 2, '0', STR_PAD_LEFT))
                    <article class="group rounded-md border border-line bg-surface p-5 shadow-[0_1px_2px_rgba(20,24,34,0.04)] transition hover:border-primary/40 hover:shadow-[0_10px_30px_-12px_rgba(20,24,34,0.30)] motion-reduce:transition-none">

                        <div class="flex flex-wrap items-center justify-between gap-4">
                            {{-- Left: Identity --}}
                            <div class="flex min-w-0 flex-1 items-center gap-4">
                                <span class="grid h-11 w-11 shrink-0 place-items-center rounded-md bg-primary/10 font-mono text-sm font-bold text-primary">
                                    {{ $topicNumber }}
                                </span>
                                <div class="min-w-0">
                                    <h3 class="truncate font-display text-base font-bold text-ink transition group-hover:text-primary">
                                        {{ $academicTopic->name }}
                                    </h3>

                                    {{-- Subtopics (desktop) --}}
                                    <div class="mt-2 hidden max-w-xs md:block">
                                        <div class="flex items-baseline gap-2 font-mono text-[11px] tracking-wide text-muted">
                                            <span>SUBTOPICS</span>
                                            <span class="flex-1 -translate-y-[3px] border-b border-dotted border-line-strong"></span>
                                            <span class="font-semibold {{ $academicTopic->subtopics_count > 0 ? 'text-ink' : 'text-muted' }}">
                                                {{ $academicTopic->subtopics_count > 0 ? $academicTopic->subtopics_count : 'NONE' }}
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Right: Actions --}}
                            <div class="flex items-center gap-2">
                                <a href="{{ route('academic-topics.show', ['academic_topic' => $academicTopic, 'academic_subject' => getRouteParameter('academic_subject'), 'academic_level' => getRouteParameter('academic_level'), 'academic_group' => getRouteParameter('academic_group')]) }}"
                                   class="inline-flex items-center justify-center rounded border border-line-strong px-3 py-1.5 font-mono text-[10.5px] font-semibold uppercase tracking-wider text-ink-soft transition hover:bg-surface-2 hover:text-ink">
                                    View
                                </a>
                                @can('administrate')
                                    <a href="{{ route('academic-topics.edit', ['academic_topic' => $academicTopic, 'academic_subject' => getRouteParameter('academic_subject'), 'academic_level' => getRouteParameter('academic_level'), 'academic_group' => getRouteParameter('academic_group')]) }}"
                                       class="inline-flex items-center justify-center rounded border border-warn/40 px-3 py-1.5 font-mono text-[10.5px] font-semibold uppercase tracking-wider text-warn transition hover:bg-warn/10">
                                        Edit
                                    </a>
                                    <button type="button"
                                            x-data="{}"
                                            x-on:click="$store.deleteForm.show('Delete Topic', 'Are you sure you want to delete {{ $academicTopic->name }}? This action cannot be undone.', '{{ route('academic-topics.destroy', ['academic_topic' => $academicTopic, 'academic_subject' => getRouteParameter('academic_subject'), 'academic_level' => getRouteParameter('academic_level'), 'academic_group' => getRouteParameter('academic_group')]) }}')"
                                            class="inline-flex items-center justify-center rounded border border-accent/40 px-3 py-1.5 font-mono text-[10.5px] font-semibold uppercase tracking-wider text-accent transition hover:bg-accent/10">
                                        Delete
                                    </button>
                                @endcan
                            </div>
                        </div>

                        {{-- Mobile subtopics --}}
                        <div class="mt-3 border-t border-dashed border-line pt-3 md:hidden">
                            <div class="flex items-baseline gap-2 font-mono text-[11px] tracking-wide text-muted">
                                <span>SUBTOPICS</span>
                                <span class="flex-1 -translate-y-[3px] border-b border-dotted border-line-strong"></span>
                                <span class="font-semibold {{ $academicTopic->subtopics_count > 0 ? 'text-ink' : 'text-muted' }}">
                                    {{ $academicTopic->subtopics_count > 0 ? $academicTopic->subtopics_count : 'NONE' }}
                                </span>
                            </div>
                        </div>

                        {{-- Footer: ID + updated --}}
                        <div class="mt-3 flex items-center justify-between border-t border-dashed border-line pt-3 font-mono text-[10px] tracking-wider text-muted">
                            <span>ID #{{ $academicTopic->id }}</span>
                            <span>UPDATED {{ strtoupper($academicTopic->updated_at->diffForHumans()) }}</span>
                        </div>
                    </article>
                @endforeach
            </div>

            {{-- Pagination --}}
            @if($academicTopics->hasPages())
                <div class="mt-6 flex flex-wrap items-center justify-between gap-3">
                    <p class="font-mono text-[10.5px] tracking-wider text-muted">
                        SHOWING {{ $academicTopics->firstItem() }}–{{ $academicTopics->lastItem() }} OF {{ $academicTopics->total() }}
                    </p>
                    {{ $academicTopics->appends(request()->query())->links() }}
                </div>
            @endif
        @else
            {{-- ============ Empty state ============ --}}
            <x-exam.empty
                title="No topics found"
                description="Get started by creating your first topic for {{ $topicSubject->name }}. Topics organize the curriculum and make content easier for students to navigate."
            >
                <div class="mt-5 flex flex-col justify-center gap-2 sm:flex-row">
                    @can('administrate')
                        <a href="{{ route('academic-topics.create', ['academic_subject' => $topicSubject, 'academic_level' => $topicLevel, 'academic_group' => $topicGroup]) }}"
                           class="inline-flex items-center justify-center gap-1.5 whitespace-nowrap rounded bg-primary px-5 py-2.5 text-[13.5px] font-semibold tracking-wide text-on-primary transition hover:-translate-y-px hover:bg-primary-hi motion-reduce:transition-none">
                            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M12 4v16m8-8H4"/></svg>
                            Create First Topic
                        </a>
                    @endcan
                    <a href="{{ route('academic-subjects.show', ['academic_subject' => $topicSubject, 'academic_level' => getRouteParameter('academic_level'), 'academic_group' => getRouteParameter('academic_group')]) }}"
                       class="inline-flex items-center justify-center whitespace-nowrap rounded border border-line-strong px-5 py-2.5 text-[13.5px] font-semibold text-ink-soft transition hover:bg-surface-2 hover:text-ink">
                        Back to Subject
                    </a>
                </div>
            </x-exam.empty>
        @endif

    </x-exam.page>
</x-layouts.app>
