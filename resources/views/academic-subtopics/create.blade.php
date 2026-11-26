<x-layouts.app title="Create New Subtopic" :has-action="false" :show-title-area="false">
    <x-exam.page>
        @php
            $parentTopic = $academic_topic;
            $subSubject  = $academicSubject;
            $subLevel    = $academicSubject->academicLevel;
            $subGroup    = $academicSubject->academicLevel->academicGroup;

            $routeParams = [
                'academic_topic'   => $academic_topic,
                'academic_subject' => getRouteParameter('academic_subject'),
                'academic_level'   => getRouteParameter('academic_level'),
                'academic_group'   => getRouteParameter('academic_group'),
            ];

            $storeUrl  = route('subtopics.store', $routeParams);
            $cancelUrl = route('academic-topics.show', $routeParams);

            $existingSubtopics = (int) ($parentTopic->subtopics_count ?? 0);

            $lineage = [
                ['kind' => 'Group',   'name' => $subGroup->name,   'href' => route('academic-groups.show', ['academic_group' => $subGroup])],
                ['kind' => 'Level',   'name' => $subLevel->name,   'href' => route('academic-levels.show', ['academic_level' => $subLevel, 'academic_group' => $subGroup])],
                ['kind' => 'Subject', 'name' => $subSubject->name, 'href' => route('academic-subjects.show', ['academic_subject' => $subSubject, 'academic_level' => $subLevel, 'academic_group' => $subGroup])],
                ['kind' => 'Topic',   'name' => $parentTopic->name, 'href' => $cancelUrl],
            ];
        @endphp

        {{-- Breadcrumb --}}
        <x-exam.crumbs :items="[
            ['label' => 'Academic Groups', 'href' => route('academic-groups.index')],
            ['label' => $subGroup->name, 'href' => route('academic-groups.show', ['academic_group' => $subGroup])],
            ['label' => 'Academic Levels', 'href' => route('academic-levels.index', ['academic_group' => $subGroup, 'academic_subject' => $subSubject])],
            ['label' => $subLevel->name, 'href' => route('academic-levels.show', ['academic_level' => $subLevel, 'academic_group' => $subGroup])],
            ['label' => 'Subjects', 'href' => route('academic-subjects.index', ['academic_level' => $subLevel, 'academic_group' => $subGroup])],
            ['label' => $subSubject->name, 'href' => route('academic-subjects.show', ['academic_subject' => $subSubject, 'academic_level' => $subLevel, 'academic_group' => $subGroup])],
            ['label' => 'Topics', 'href' => route('academic-topics.index', ['academic_subject' => $subSubject, 'academic_level' => $subLevel, 'academic_group' => $subGroup])],
            ['label' => $parentTopic->name, 'href' => $cancelUrl],
            ['label' => 'New Subtopic'],
        ]" />

        {{-- ============ Header ============ --}}
        <div class="mb-6">
            <h1 class="font-display text-2xl font-bold text-ink md:text-3xl">Create New Subtopic</h1>
            <p class="mt-1 text-sm text-ink-soft">
                Break <span class="font-semibold text-ink">{{ $parentTopic->name }}</span> into a focused study unit.
            </p>
        </div>

        <x-exam.flash />

        {{-- ============ Context ============ --}}
        <x-exam.callout tone="primary" class="mb-6">
            <div class="flex items-start gap-3">
                <svg class="mt-0.5 h-4 w-4 shrink-0 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <p class="text-sm leading-relaxed text-ink-soft">
                    Filing under
                    <a href="{{ $cancelUrl }}" class="font-semibold text-primary transition hover:text-primary-hi">{{ $parentTopic->name }}</a>
                    in <b class="font-semibold text-ink">{{ $subSubject->name }}</b> · {{ $subLevel->name }}.
                    @if($existingSubtopics > 0)
                        This topic already holds <b class="font-semibold text-ink">{{ $existingSubtopics }}</b>
                        {{ Str::plural('subtopic', $existingSubtopics) }} — the new one will be filed alongside them.
                    @else
                        This will be the topic's first subtopic.
                    @endif
                </p>
            </div>
        </x-exam.callout>

        {{-- ============ Form + rail ============ --}}
        <div class="grid gap-8 lg:grid-cols-[minmax(0,1fr)_320px]"
             x-data="{
                 name: @js(old('name', '')),
                 description: @js(old('description', '')),
                 max: 255,
                 get trimmed() { return this.name.trim() },
                 get isValid() { return this.trimmed.length >= 2 && this.trimmed.length <= this.max },
                 get initials() { return this.trimmed.substring(0, 3).toUpperCase() || 'SUB' },
                 get words() { return this.description.trim() ? this.description.trim().split(/\s+/).length : 0 }
             }">

            {{-- -------- Form -------- --}}
            <div>
                <form method="POST" action="{{ $storeUrl }}">
                    @csrf

                    <x-exam.card class="animate-rise" :padded="false">
                        <x-slot:header>
                            <p class="font-mono text-[10.5px] tracking-[0.14em] text-muted">SUBTOPIC INFORMATION</p>
                            <p class="font-mono text-[10px] tracking-wider text-muted"><span class="font-bold text-accent">*</span> REQUIRED</p>
                        </x-slot:header>

                        <div class="space-y-5 p-5">
                            {{-- Parent topic (read-only) --}}
                            <x-exam.field bare label="Parent Topic">
                                <div class="flex items-center gap-3 rounded-md border border-line bg-surface-2 px-3.5 py-2.5">
                                    <svg class="h-4 w-4 shrink-0 text-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                    <span class="min-w-0 flex-1 truncate text-sm font-semibold text-ink">{{ $parentTopic->name }}</span>
                                    <span class="shrink-0 font-mono text-[10px] tracking-wider text-muted">
                                        {{ $existingSubtopics }} EXISTING
                                    </span>
                                </div>
                            </x-exam.field>

                            {{-- Subtopic name --}}
                            <x-exam.field label="Subtopic Name" required>
                                <x-exam.input
                                    type="text"
                                    id="name"
                                    name="name"
                                    x-model="name"
                                    maxlength="255"
                                    autocomplete="off"
                                    autofocus
                                    placeholder="e.g., Linear Equations, Cell Structure, Causes of the War"
                                    required />

                                @error('name')
                                <p class="mt-2 font-mono text-[11.5px] text-accent">▲ {{ $message }}</p>
                                @enderror

                                <div class="mt-1.5 flex items-start justify-between gap-3">
                                    <p class="text-xs text-muted">Name one teachable idea — narrow enough to assess on its own.</p>
                                    <p class="shrink-0 font-mono text-[10px] tracking-wider"
                                       :class="name.length > max - 20 ? 'text-warn' : 'text-muted'">
                                        <span x-text="name.length">0</span>/255
                                    </p>
                                </div>
                            </x-exam.field>

                            {{-- Description --}}
                            <x-exam.field label="Description">
                                <textarea
                                    id="description"
                                    name="description"
                                    rows="4"
                                    x-model="description"
                                    placeholder="What should a student be able to do after this subtopic? (optional)"
                                    class="ei-scroll w-full rounded border border-line-strong bg-surface px-3 py-2.5 text-sm leading-relaxed text-ink placeholder:text-muted transition hover:border-ink-soft focus:border-primary focus:outline-none focus:ring-[3px] focus:ring-primary/20">{{ old('description') }}</textarea>

                                @error('description')
                                <p class="mt-2 font-mono text-[11.5px] text-accent">▲ {{ $message }}</p>
                                @enderror

                                <div class="mt-1.5 flex items-start justify-between gap-3">
                                    <p class="text-xs text-muted">Shown to teachers when organising lesson notes and question banks.</p>
                                    <p class="shrink-0 font-mono text-[10px] tracking-wider text-muted">
                                        <span x-text="words">0</span> <span x-text="words === 1 ? 'WORD' : 'WORDS'">WORDS</span>
                                    </p>
                                </div>
                            </x-exam.field>

                            {{-- Live record preview --}}
                            <div x-show="trimmed.length > 0" x-transition x-cloak class="border-t border-dashed border-line pt-5">
                                <p class="mb-2 font-mono text-[10.5px] tracking-[0.14em] text-muted">RECORD PREVIEW</p>
                                <div class="rounded-md border border-line bg-surface-2 p-4">
                                    <div class="flex items-start gap-4">
                                        <span class="grid h-12 w-12 shrink-0 place-items-center rounded-md bg-accent/10 font-mono text-[11px] font-bold text-accent"
                                              x-text="initials">SUB</span>
                                        <div class="min-w-0 flex-1">
                                            <p class="truncate font-display text-base font-bold text-ink" x-text="trimmed"></p>
                                            <p class="mt-0.5 font-mono text-[10px] tracking-wider text-muted">
                                                {{ strtoupper($parentTopic->name) }} · 0 QUESTIONS
                                            </p>
                                            <p class="mt-2 text-xs leading-relaxed text-ink-soft"
                                               x-show="description.trim().length > 0"
                                               x-text="description.trim().length > 160 ? description.trim().slice(0, 160) + '…' : description.trim()"></p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Actions --}}
                        <div class="flex flex-col gap-4 border-t border-line bg-surface-2 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
                            <div class="flex items-center gap-2 font-mono text-[10.5px] tracking-wide"
                                 :class="isValid ? 'text-ok' : 'text-warn'">
                                <svg x-show="!isValid" class="h-4 w-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 15.5c-.77.833.192 2.5 1.732 2.5z"/>
                                </svg>
                                <svg x-show="isValid" x-cloak class="h-4 w-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <span x-show="!isValid">ENTER AT LEAST 2 CHARACTERS</span>
                                <span x-show="isValid" x-cloak>READY TO FILE SUBTOPIC</span>
                            </div>

                            <div class="flex gap-2">
                                <a href="{{ $cancelUrl }}"
                                   class="inline-flex items-center gap-1.5 whitespace-nowrap rounded border border-line-strong px-4 py-2.5 text-[13px] font-semibold text-ink-soft transition hover:bg-surface hover:text-ink">
                                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                                    Cancel
                                </a>
                                <button type="submit" :disabled="!isValid"
                                        class="inline-flex items-center justify-center gap-1.5 whitespace-nowrap rounded bg-primary px-5 py-2.5 text-[13.5px] font-semibold tracking-wide text-on-primary transition hover:-translate-y-px hover:bg-primary-hi disabled:cursor-not-allowed disabled:opacity-50 disabled:hover:translate-y-0 motion-reduce:transition-none">
                                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M12 4v16m8-8H4"/></svg>
                                    Create Subtopic
                                </button>
                            </div>
                        </div>
                    </x-exam.card>
                </form>

                {{-- After creation --}}
                <x-exam.callout tone="ok" class="mt-6" x-show="isValid" x-transition x-cloak>
                    <p class="mb-1 font-mono text-[10px] font-semibold uppercase tracking-[0.14em] text-ok">After creation</p>
                    <p class="text-xs leading-relaxed text-ink-soft">
                        The subtopic appears under {{ $parentTopic->name }} straight away. Lesson notes and questions
                        authored against it feed the same pool that quizzes and examinations draw from for
                        {{ $subSubject->name }}.
                    </p>
                </x-exam.callout>
            </div>

            {{-- -------- Rail -------- --}}
            <aside class="space-y-6 lg:sticky lg:top-7 lg:self-start">

                {{-- Curriculum placement --}}
                <x-exam.card class="animate-rise" style="animation-delay:60ms" :padded="false">
                    <x-slot:header>
                        <p class="font-mono text-[10.5px] tracking-[0.14em] text-muted">CURRICULUM PLACEMENT</p>
                    </x-slot:header>

                    <div class="p-5">
                        <div class="relative pl-6">
                            <span class="absolute bottom-7 left-[5px] top-2 w-px bg-line-strong"></span>

                            @foreach($lineage as $node)
                                <div class="relative pb-4">
                                    <span class="absolute -left-6 top-1 grid h-2.5 w-2.5 place-items-center rounded-full border-2 border-line-strong bg-surface"></span>
                                    <p class="font-mono text-[9px] uppercase tracking-[0.18em] text-muted">{{ $node['kind'] }}</p>
                                    <a href="{{ $node['href'] }}"
                                       class="mt-0.5 block truncate text-sm font-semibold text-ink-soft transition hover:text-primary">
                                        {{ $node['name'] }}
                                    </a>
                                </div>
                            @endforeach

                            {{-- Pending leaf --}}
                            <div class="relative">
                                <span class="absolute -left-6 top-1 h-2.5 w-2.5 rounded-full border-2 border-accent transition-colors duration-300"
                                      :class="isValid ? 'bg-accent' : 'bg-accent/20 animate-pulse'"></span>
                                <p class="font-mono text-[9px] uppercase tracking-[0.18em] text-accent">New Subtopic</p>
                                <p class="mt-0.5 truncate text-sm font-bold"
                                   :class="trimmed.length ? 'text-ink' : 'text-muted'"
                                   x-text="trimmed || 'Awaiting name…'"></p>
                                <p class="mt-1 font-mono text-[9.5px] tracking-wider text-muted"
                                   x-show="trimmed.length > 0" x-cloak>
                                    POSITION {{ $existingSubtopics + 1 }} OF {{ $existingSubtopics + 1 }}
                                </p>
                            </div>
                        </div>
                    </div>
                </x-exam.card>

                {{-- Naming guide --}}
                <x-exam.card class="animate-rise" style="animation-delay:120ms" :padded="false">
                    <x-slot:header>
                        <p class="font-mono text-[10.5px] tracking-[0.14em] text-muted">NAMING GUIDE</p>
                    </x-slot:header>
                    <div class="space-y-3 p-5">
                        <p class="text-xs leading-relaxed text-ink-soft">
                            A subtopic is the smallest unit you would set a question on. Keep it to one idea.
                        </p>

                        <div class="rounded-md border border-ok/30 bg-ok/10 p-3">
                            <p class="mb-1.5 font-mono text-[9px] uppercase tracking-[0.18em] text-ok">Focused</p>
                            <p class="text-xs font-semibold text-ink">{{ $parentTopic->name }} → Solving for one unknown</p>
                        </div>

                        <div class="rounded-md border border-accent/30 bg-accent/10 p-3">
                            <p class="mb-1.5 font-mono text-[9px] uppercase tracking-[0.18em] text-accent">Too broad</p>
                            <p class="text-xs font-semibold text-ink">{{ $parentTopic->name }} → Everything about {{ $parentTopic->name }}</p>
                        </div>

                        <ul class="space-y-1.5 pt-1 text-xs leading-relaxed text-ink-soft">
                            <li class="flex items-start gap-2">
                                <span class="mt-1.5 h-1 w-1 shrink-0 rounded-full bg-primary"></span>
                                Use the wording students will see on the paper.
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="mt-1.5 h-1 w-1 shrink-0 rounded-full bg-primary"></span>
                                Avoid numbering — order is managed in the topic register.
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="mt-1.5 h-1 w-1 shrink-0 rounded-full bg-primary"></span>
                                Duplicate names under one topic are hard to tell apart in reports.
                            </li>
                        </ul>
                    </div>
                </x-exam.card>

            </aside>
        </div>

        <style>
            [x-cloak] { display: none !important; }

            .ei-scroll { scrollbar-width: thin; scrollbar-color: rgb(var(--c-line-strong)) rgb(var(--c-surface-2)); }
            .ei-scroll::-webkit-scrollbar { width: 8px; }
            .ei-scroll::-webkit-scrollbar-track { background: rgb(var(--c-surface-2)); border-radius: 4px; }
            .ei-scroll::-webkit-scrollbar-thumb { background: rgb(var(--c-line-strong)); border-radius: 4px; }
            .ei-scroll::-webkit-scrollbar-thumb:hover { background: rgb(var(--c-primary)); }
        </style>
    </x-exam.page>
</x-layouts.app>
