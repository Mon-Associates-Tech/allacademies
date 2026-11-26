<x-layouts.app title="Edit Subtopic" :has-action="false" :show-title-area="false">
    <x-exam.page>
        @php
            $editSubtopic = $subtopic;
            $editTopic    = $academic_topic;
            $editSubject  = $academicSubject;
            $editLevel    = $academicSubject->academicLevel;
            $editGroup    = $academicSubject->academicLevel->academicGroup;

            $updateUrl = route('subtopics.update', [
                'subtopic'         => $subtopic,
                'academic_topic'   => $academic_topic,
                'academic_subject' => $academicSubject,
                'academic_level'   => $academicSubject->academicLevel,
                'academic_group'   => $academicSubject->academicLevel->academicGroup,
            ]);

            $topicUrl = route('academic-topics.show', [
                'academic_topic'   => $academic_topic,
                'academic_subject' => $academicSubject,
                'academic_level'   => $academicSubject->academicLevel,
                'academic_group'   => $academicSubject->academicLevel->academicGroup,
            ]);

            $subjectUrl = route('academic-subjects.show', [
                'academic_subject' => $academicSubject,
                'academic_level'   => $editLevel,
                'academic_group'   => $editGroup,
            ]);

            $siblingsUrl = route('subtopics.index', [
                'academic_topic'   => $academic_topic,
                'academic_subject' => $academicSubject,
                'academic_level'   => $editLevel,
                'academic_group'   => $editGroup,
            ]);

            $showUrl = route('subtopics.show', [
                'subtopic'         => $subtopic,
                'academic_topic'   => $academic_topic,
                'academic_subject' => $academicSubject,
                'academic_level'   => $editLevel,
                'academic_group'   => $editGroup,
            ]);

            $lineage = [
                ['kind' => 'Group',   'name' => $editGroup->name,   'href' => route('academic-groups.show', ['academic_group' => $editGroup])],
                ['kind' => 'Level',   'name' => $editLevel->name,   'href' => route('academic-levels.show', ['academic_level' => $editLevel, 'academic_group' => $editGroup])],
                ['kind' => 'Subject', 'name' => $editSubject->name, 'href' => $subjectUrl],
                ['kind' => 'Topic',   'name' => $editTopic->name,   'href' => $topicUrl],
            ];
        @endphp

        {{-- Breadcrumb --}}
        <x-exam.crumbs :items="[
            ['label' => 'Academic Groups', 'href' => route('academic-groups.index')],
            ['label' => $editGroup->name, 'href' => route('academic-groups.show', ['academic_group' => $editGroup])],
            ['label' => 'Academic Levels', 'href' => route('academic-levels.index', ['academic_group' => $editGroup])],
            ['label' => $editLevel->name, 'href' => route('academic-levels.show', ['academic_level' => $editLevel, 'academic_group' => $editGroup])],
            ['label' => 'Subjects', 'href' => route('academic-subjects.index', ['academic_level' => $editLevel, 'academic_group' => $editGroup])],
            ['label' => $editSubject->name, 'href' => $subjectUrl],
            ['label' => 'Topics', 'href' => route('academic-topics.index', ['academic_subject' => $editSubject, 'academic_level' => $editLevel, 'academic_group' => $editGroup])],
            ['label' => $editTopic->name, 'href' => $topicUrl],
            ['label' => 'Subtopics', 'href' => $siblingsUrl],
            ['label' => $editSubtopic->name, 'href' => $showUrl],
            ['label' => 'Edit'],
        ]" />

        {{-- ============ Header ============ --}}
        <div class="mb-6">
            <h1 class="font-display text-2xl font-bold text-ink md:text-3xl">Edit Subtopic</h1>
            <p class="mt-1 text-sm text-ink-soft">
                Update the details of <span class="font-semibold text-ink">{{ $editSubtopic->name }}</span>.
            </p>
        </div>

        <x-exam.flash />
        <x-exam.flash type="error" />

        {{-- ============ Context ============ --}}
        <x-exam.callout tone="primary" class="mb-6">
            <div class="flex items-start gap-3">
                <svg class="mt-0.5 h-4 w-4 shrink-0 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <p class="text-sm leading-relaxed text-ink-soft">
                    Filed under
                    <a href="{{ $topicUrl }}" class="font-semibold text-primary transition hover:text-primary-hi">{{ $editTopic->name }}</a>
                    in <b class="font-semibold text-ink">{{ $editSubject->name }}</b> · {{ $editLevel->name }}.
                    Renaming affects how this subtopic appears in navigation, reports and transcripts.
                </p>
            </div>
        </x-exam.callout>

        {{-- ============ Form + rail ============ --}}
        <div class="grid gap-8 lg:grid-cols-[minmax(0,1fr)_320px]"
             x-data="{
                 name: @js(old('name', $editSubtopic->name ?? '')),
                 slug: @js(old('slug', $editSubtopic->slug ?? '')),
                 description: @js(old('description', $editSubtopic->description ?? '')),
                 max: 255,
                 get trimmed() { return this.name.trim() },
                 get isValid() { return this.trimmed.length >= 2 && this.trimmed.length <= this.max },
                 get initials() { return this.trimmed.substring(0, 3).toUpperCase() || 'SUB' },
                 get words() { return this.description.trim() ? this.description.trim().split(/\s+/).length : 0 },
                 slugify(value) {
                     return value.toLowerCase()
                         .replace(/[^a-z0-9\s-]/g, '')
                         .replace(/\s+/g, '-')
                         .replace(/-+/g, '-')
                         .replace(/^-+|-+$/g, '');
                 },
                 autoSlug() { if (!this.slug.trim()) this.slug = this.slugify(this.name) },
                 forceSlug() { this.slug = this.slugify(this.name) }
             }">

            {{-- -------- Form -------- --}}
            <div>
                <form method="POST" action="{{ $updateUrl }}">
                    @csrf
                    @method('PUT')

                    <x-exam.card class="animate-rise" :padded="false">
                        <x-slot:header>
                            <p class="font-mono text-[10.5px] tracking-[0.14em] text-muted">SUBTOPIC RECORD · #{{ $editSubtopic->id }}</p>
                            <p class="font-mono text-[10px] tracking-wider text-muted">
                                UPDATED {{ strtoupper($editSubtopic->updated_at->format('M j, Y · g:i A')) }}
                            </p>
                        </x-slot:header>

                        <div class="space-y-5 p-5">
                            {{-- Parent topic (read-only) --}}
                            <x-exam.field bare label="Parent Topic">
                                <div class="flex items-center gap-3 rounded-md border border-line bg-surface-2 px-3.5 py-2.5">
                                    <svg class="h-4 w-4 shrink-0 text-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                    </svg>
                                    <span class="min-w-0 flex-1 truncate text-sm font-semibold text-ink">{{ $editTopic->name }}</span>
                                    <span class="shrink-0 font-mono text-[10px] tracking-wider text-muted">CANNOT BE CHANGED</span>
                                </div>
                            </x-exam.field>

                            {{-- Name --}}
                            <x-exam.field label="Subtopic Name" required>
                                <x-exam.input
                                    type="text"
                                    id="name"
                                    name="name"
                                    x-model="name"
                                    x-on:input="autoSlug()"
                                    maxlength="255"
                                    autocomplete="off"
                                    autofocus
                                    placeholder="Enter subtopic name"
                                    required />

                                @error('name')
                                <p class="mt-2 font-mono text-[11.5px] text-accent">▲ {{ $message }}</p>
                                @enderror

                                <div class="mt-1.5 flex items-start justify-between gap-3">
                                    <p class="text-xs text-muted">Enter a unique name for this subtopic within the parent topic.</p>
                                    <p class="shrink-0 font-mono text-[10px] tracking-wider"
                                       :class="name.length > max - 20 ? 'text-warn' : 'text-muted'">
                                        <span x-text="name.length">0</span>/255
                                    </p>
                                </div>
                            </x-exam.field>

                            {{-- Slug --}}
                            <x-exam.field label="Slug">
                                <div class="flex gap-2">
                                    <div class="relative flex-1">
                                        <span class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 font-mono text-[11px] text-muted">/</span>
                                        <input type="text"
                                               id="slug"
                                               name="slug"
                                               x-model="slug"
                                               placeholder="Auto-generated from name if empty"
                                               autocomplete="off"
                                               class="w-full rounded border border-line-strong bg-surface py-2.5 pl-6 pr-3 font-mono text-[12.5px] text-ink placeholder:text-muted transition hover:border-ink-soft focus:border-primary focus:outline-none focus:ring-[3px] focus:ring-primary/20">
                                    </div>
                                    <button type="button" @click="forceSlug()" :disabled="!trimmed.length"
                                            class="shrink-0 whitespace-nowrap rounded border border-line-strong px-3.5 py-2.5 font-mono text-[10.5px] font-semibold uppercase tracking-wider text-ink-soft transition hover:bg-surface-2 hover:text-ink disabled:cursor-not-allowed disabled:opacity-40">
                                        Regenerate
                                    </button>
                                </div>

                                @error('slug')
                                <p class="mt-2 font-mono text-[11.5px] text-accent">▲ {{ $message }}</p>
                                @enderror

                                <div class="mt-1.5 flex flex-wrap items-baseline justify-between gap-2">
                                    <p class="text-xs text-muted">URL-friendly version of the name. Leave empty to auto-generate.</p>
                                    <p class="font-mono text-[10px] tracking-wide"
                                       :class="slug.trim() ? 'text-primary' : 'text-muted'">
                                        <span x-show="slug.trim()" x-cloak>→ <span x-text="slugify(slug)"></span></span>
                                        <span x-show="!slug.trim()">WILL AUTO-GENERATE</span>
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
                                    placeholder="Brief description of the subtopic (optional)"
                                    class="ei-scroll w-full rounded border border-line-strong bg-surface px-3 py-2.5 text-sm leading-relaxed text-ink placeholder:text-muted transition hover:border-ink-soft focus:border-primary focus:outline-none focus:ring-[3px] focus:ring-primary/20">{{ old('description', $editSubtopic->description) }}</textarea>

                                @error('description')
                                <p class="mt-2 font-mono text-[11.5px] text-accent">▲ {{ $message }}</p>
                                @enderror

                                <div class="mt-1.5 flex items-start justify-between gap-3">
                                    <p class="text-xs text-muted">Provide additional context about this subtopic.</p>
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
                                            <p class="mt-0.5 truncate font-mono text-[10px] tracking-wider text-muted">
                                                {{ strtoupper($editTopic->name) }} · <span x-text="slug.trim() ? slugify(slug) : slugify(name)"></span>
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
                                <span x-show="isValid" x-cloak>READY TO SAVE AMENDMENTS</span>
                            </div>

                            <div class="flex gap-2">
                                <a href="{{ $topicUrl }}"
                                   class="inline-flex items-center gap-1.5 whitespace-nowrap rounded border border-line-strong px-4 py-2.5 text-[13px] font-semibold text-ink-soft transition hover:bg-surface hover:text-ink">
                                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                                    Cancel
                                </a>
                                <button type="submit" :disabled="!isValid"
                                        class="inline-flex items-center justify-center gap-1.5 whitespace-nowrap rounded bg-primary px-5 py-2.5 text-[13.5px] font-semibold tracking-wide text-on-primary transition hover:-translate-y-px hover:bg-primary-hi disabled:cursor-not-allowed disabled:opacity-50 disabled:hover:translate-y-0 motion-reduce:transition-none">
                                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M5 13l4 4L19 7"/></svg>
                                    Update Subtopic
                                </button>
                            </div>
                        </div>
                    </x-exam.card>
                </form>

                {{-- Impact note --}}
                <x-exam.callout tone="warn" class="mt-6">
                    <p class="mb-1.5 font-mono text-[10px] font-semibold uppercase tracking-[0.14em] text-warn">Before you save</p>
                    <ul class="space-y-1.5 text-xs leading-relaxed text-ink-soft">
                        <li class="flex items-start gap-2">
                            <span class="mt-1.5 h-1 w-1 shrink-0 rounded-full bg-warn"></span>
                            Changing the slug breaks any bookmarked or shared links to this subtopic.
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="mt-1.5 h-1 w-1 shrink-0 rounded-full bg-warn"></span>
                            The new name propagates to subject navigation, dashboards, reports and transcripts.
                        </li>
                        <li class="flex items-start gap-2">
                            <span class="mt-1.5 h-1 w-1 shrink-0 rounded-full bg-warn"></span>
                            Questions and lesson notes stay attached — nothing is unfiled by renaming.
                        </li>
                    </ul>
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
                                    <span class="absolute -left-6 top-1 h-2.5 w-2.5 rounded-full border-2 border-line-strong bg-surface"></span>
                                    <p class="font-mono text-[9px] uppercase tracking-[0.18em] text-muted">{{ $node['kind'] }}</p>
                                    <a href="{{ $node['href'] }}"
                                       class="mt-0.5 block truncate text-sm font-semibold text-ink-soft transition hover:text-primary">
                                        {{ $node['name'] }}
                                    </a>
                                </div>
                            @endforeach

                            <div class="relative">
                                <span class="absolute -left-6 top-1 h-2.5 w-2.5 rounded-full border-2 border-accent transition-colors duration-300"
                                      :class="isValid ? 'bg-accent' : 'bg-accent/20 animate-pulse'"></span>
                                <p class="font-mono text-[9px] uppercase tracking-[0.18em] text-accent">Editing</p>
                                <p class="mt-0.5 truncate text-sm font-bold"
                                   :class="trimmed.length ? 'text-ink' : 'text-muted'"
                                   x-text="trimmed || 'Awaiting name…'"></p>
                                <p class="mt-1 truncate font-mono text-[9.5px] tracking-wider text-muted"
                                   x-show="trimmed.length > 0" x-cloak
                                   x-text="slug.trim() ? slugify(slug) : slugify(name)"></p>
                            </div>
                        </div>
                    </div>
                </x-exam.card>

                {{-- Record details --}}
                <x-exam.card class="animate-rise" style="animation-delay:120ms" :padded="false">
                    <x-slot:header>
                        <p class="font-mono text-[10.5px] tracking-[0.14em] text-muted">RECORD DETAILS</p>
                    </x-slot:header>
                    <div class="space-y-4 p-5 font-mono text-[11px] tracking-wide">
                        <div class="flex items-baseline justify-between gap-2 text-muted">
                            <span>ID</span>
                            <span class="flex-1 border-b border-dotted border-line-strong"></span>
                            <span class="font-semibold text-ink">#{{ $editSubtopic->id }}</span>
                        </div>
                        <div class="flex items-baseline justify-between gap-2 text-muted">
                            <span>CURRENT SLUG</span>
                            <span class="flex-1 border-b border-dotted border-line-strong"></span>
                            <span class="max-w-[55%] truncate text-right font-semibold text-ink">{{ $editSubtopic->slug ?: '—' }}</span>
                        </div>
                        <div class="flex items-baseline justify-between gap-2 text-muted">
                            <span>CREATED</span>
                            <span class="flex-1 border-b border-dotted border-line-strong"></span>
                            <span class="font-semibold text-ink">{{ $editSubtopic->created_at->format('M d, Y') }}</span>
                        </div>
                        <div class="flex items-baseline justify-between gap-2 text-muted">
                            <span>UPDATED</span>
                            <span class="flex-1 border-b border-dotted border-line-strong"></span>
                            <span class="font-semibold text-ink">{{ strtoupper($editSubtopic->updated_at->diffForHumans()) }}</span>
                        </div>
                        <div class="border-t border-dashed border-line pt-3">
                            <a href="{{ $showUrl }}"
                               class="flex items-center justify-between text-[11px] font-semibold text-primary transition hover:text-primary-hi">
                                VIEW PUBLIC RECORD
                                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                            </a>
                        </div>
                    </div>
                </x-exam.card>

                {{-- Slug guide --}}
                <x-exam.card class="animate-rise" style="animation-delay:180ms" :padded="false">
                    <x-slot:header>
                        <p class="font-mono text-[10.5px] tracking-[0.14em] text-muted">SLUG RULES</p>
                    </x-slot:header>
                    <div class="space-y-3 p-5">
                        <p class="text-xs leading-relaxed text-ink-soft">
                            Slugs appear in URLs. Lowercase letters, numbers and dashes only.
                        </p>

                        <div class="rounded-md border border-ok/30 bg-ok/10 p-3">
                            <p class="mb-1.5 font-mono text-[9px] uppercase tracking-[0.18em] text-ok">Valid</p>
                            <p class="font-mono text-[11.5px] font-semibold text-ink">solving-linear-equations</p>
                        </div>

                        <div class="rounded-md border border-accent/30 bg-accent/10 p-3">
                            <p class="mb-1.5 font-mono text-[9px] uppercase tracking-[0.18em] text-accent">Invalid</p>
                            <p class="font-mono text-[11.5px] font-semibold text-ink">Solving Linear Equations!</p>
                        </div>

                        <ul class="space-y-1.5 pt-1 text-xs leading-relaxed text-ink-soft">
                            <li class="flex items-start gap-2">
                                <span class="mt-1.5 h-1 w-1 shrink-0 rounded-full bg-primary"></span>
                                Leave the field empty and it fills from the name as you type.
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="mt-1.5 h-1 w-1 shrink-0 rounded-full bg-primary"></span>
                                Use <b class="font-semibold text-ink">Regenerate</b> to overwrite an existing slug.
                            </li>
                            <li class="flex items-start gap-2">
                                <span class="mt-1.5 h-1 w-1 shrink-0 rounded-full bg-primary"></span>
                                Keep names concise and descriptive.
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
