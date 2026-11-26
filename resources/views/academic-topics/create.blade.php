<x-layouts.app title="New Academic Topic" :has-action="false" :show-title-area="false">
    <x-exam.page>
        @php
            $topicSubject = $academicSubject;
            $topicLevel   = $academicSubject->academicLevel;
            $topicGroup   = $academicSubject->academicLevel->academicGroup;

            $storeUrl = route('academic-topics.store', [
                'academic_topic'   => getRouteParameter('academic_topic'),
                'academic_subject' => getRouteParameter('academic_subject'),
                'academic_level'   => getRouteParameter('academic_level'),
                'academic_group'   => getRouteParameter('academic_group'),
            ]);

            $indexUrl = route('academic-topics.index', [
                'academic_topic'   => getRouteParameter('academic_topic'),
                'academic_subject' => getRouteParameter('academic_subject'),
                'academic_level'   => getRouteParameter('academic_level'),
                'academic_group'   => getRouteParameter('academic_group'),
            ]);
        @endphp

        {{-- Breadcrumb --}}
        <x-exam.crumbs :items="[
            ['label' => 'Academic Groups', 'href' => route('academic-groups.index')],
            ['label' => $topicGroup->name, 'href' => route('academic-groups.show', ['academic_group' => $topicGroup])],
            ['label' => 'Academic Levels', 'href' => route('academic-levels.index', ['academic_group' => $topicGroup])],
            ['label' => $topicLevel->name, 'href' => route('academic-levels.show', ['academic_level' => $topicLevel, 'academic_group' => $topicGroup])],
            ['label' => 'Subjects', 'href' => route('academic-subjects.index', ['academic_level' => $topicLevel, 'academic_group' => $topicGroup])],
            ['label' => $topicSubject->name, 'href' => route('academic-subjects.show', ['academic_subject' => $topicSubject, 'academic_level' => $topicLevel, 'academic_group' => $topicGroup])],
            ['label' => 'Topics', 'href' => route('academic-topics.index', ['academic_subject' => $topicSubject, 'academic_level' => $topicLevel, 'academic_group' => $topicGroup])],
            ['label' => 'New Topic'],
        ]" />

        {{-- ============ Header ============ --}}
        <div class="mb-6 max-w-2xl mx-auto">
            <h1 class="font-display text-2xl font-bold text-ink md:text-3xl">Create New Topic</h1>
            <p class="mt-1 text-sm text-ink-soft">
                Add a new topic to <span class="font-semibold text-ink">{{ $topicSubject->name }}</span>.
            </p>
        </div>

        <x-exam.flash />

        {{-- ============ Context ============ --}}
        <x-exam.callout tone="primary" class="mb-6 max-w-2xl mx-auto">
            <div class="flex items-start gap-3">
                <svg class="mt-0.5 h-4 w-4 shrink-0 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <p class="text-sm leading-relaxed text-ink-soft">
                    This topic will be created under
                    <a href="{{ route('academic-subjects.show', ['academic_subject' => $topicSubject, 'academic_level' => $topicLevel, 'academic_group' => $topicGroup]) }}"
                       class="font-semibold text-primary transition hover:text-primary-hi">{{ $topicSubject->name }}</a>
                    in <b class="font-semibold text-ink">{{ $topicLevel->name }}</b>.
                    Subtopics, lesson notes and question banks attach to it after creation.
                </p>
            </div>
        </x-exam.callout>

        {{-- ============ Form ============ --}}
        <div class="mx-auto max-w-2xl">
            <form method="POST" action="{{ $storeUrl }}"
                  x-data="{
                      name: @js(old('name', '')),
                      max: 255,
                      get trimmed() { return this.name.trim() },
                      get isValid() { return this.trimmed.length >= 2 && this.trimmed.length <= this.max },
                      get initials() { return this.trimmed.substring(0, 3).toUpperCase() || 'TOP' }
                  }">
                @csrf

                <x-exam.card :padded="false">
                    <x-slot:header>
                        <p class="font-mono text-[10.5px] tracking-[0.14em] text-muted">TOPIC INFORMATION</p>
                        <p class="font-mono text-[10px] tracking-wider text-muted"><span class="font-bold text-accent">*</span> REQUIRED</p>
                    </x-slot:header>

                    <div class="space-y-5 p-5">
                        {{-- Topic Name --}}
                        <x-exam.field label="Topic Name" required>
                            <x-exam.input
                                type="text"
                                name="name"
                                x-model="name"
                                maxlength="255"
                                autocomplete="off"
                                placeholder="e.g., Algebra Basics, Photosynthesis, World War II"
                                required />

                            @error('name')
                            <p class="mt-2 font-mono text-[11.5px] text-accent">▲ {{ $message }}</p>
                            @enderror

                            <div class="mt-1.5 flex items-start justify-between gap-3">
                                <p class="text-xs text-muted">Choose a clear and descriptive name for this topic.</p>
                                <p class="shrink-0 font-mono text-[10px] tracking-wider"
                                   :class="name.length > max - 20 ? 'text-warn' : 'text-muted'">
                                    <span x-text="name.length">0</span>/255
                                </p>
                            </div>
                        </x-exam.field>

                        {{-- Live record preview --}}
                        <div x-show="trimmed.length > 0" x-transition x-cloak>
                            <p class="mb-2 font-mono text-[10.5px] tracking-[0.14em] text-muted">RECORD PREVIEW</p>
                            <div class="flex items-center gap-4 rounded-md border border-line bg-surface-2 p-4">
                                <span class="grid h-12 w-12 shrink-0 place-items-center rounded-md bg-primary/10 font-mono text-[11px] font-bold text-primary"
                                      x-text="initials">TOP</span>
                                <div class="min-w-0">
                                    <p class="truncate font-display text-base font-bold text-ink" x-text="trimmed"></p>
                                    <p class="mt-0.5 font-mono text-[10px] tracking-wider text-muted">
                                        {{ strtoupper($topicSubject->name) }} · 0 SUBTOPICS · 0 QUESTIONS
                                    </p>
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
                            <span x-show="isValid" x-cloak>READY TO FILE TOPIC</span>
                        </div>

                        <div class="flex gap-2">
                            <a href="{{ $indexUrl }}"
                               class="inline-flex items-center gap-1.5 whitespace-nowrap rounded border border-line-strong px-4 py-2.5 text-[13px] font-semibold text-ink-soft transition hover:bg-surface hover:text-ink">
                                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                                Cancel
                            </a>
                            <button type="submit" :disabled="!isValid"
                                    class="inline-flex items-center justify-center gap-1.5 whitespace-nowrap rounded bg-primary px-5 py-2.5 text-[13.5px] font-semibold tracking-wide text-on-primary transition hover:-translate-y-px hover:bg-primary-hi disabled:cursor-not-allowed disabled:opacity-50 disabled:hover:translate-y-0 motion-reduce:transition-none">
                                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M12 4v16m8-8H4"/></svg>
                                Create Topic
                            </button>
                        </div>
                    </div>
                </x-exam.card>
            </form>

            {{-- Next steps --}}
            <x-exam.callout tone="ok" class="mt-6">
                <p class="mb-1 font-mono text-[10px] font-semibold uppercase tracking-[0.14em] text-ok">After creation</p>
                <p class="text-xs leading-relaxed text-ink-soft">
                    You'll land on the topic record, where you can break it into subtopics, attach lesson notes,
                    and build its multiple-choice, true/false and essay question banks — the pool quizzes and
                    examinations draw from.
                </p>
            </x-exam.callout>
        </div>

        <style>[x-cloak] { display: none !important; }</style>
    </x-exam.page>
</x-layouts.app>
