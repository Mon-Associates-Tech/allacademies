<x-layouts.app title="Edit Academic Topic" :has-action="false">
    <x-exam.page>
        @php
            $topicSubject = $academicTopic->academicSubject;
            $topicLevel   = $topicSubject->academicLevel;
            $topicGroup   = $topicLevel->academicGroup;
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
            ['label' => $academicTopic->name, 'href' => route('academic-topics.show', ['academic_topic' => $academicTopic, 'academic_subject' => $topicSubject, 'academic_level' => $topicLevel, 'academic_group' => $topicGroup])],
            ['label' => 'Edit'],
        ]" />

        {{-- ============ Header ============ --}}
        <div class="mb-6 max-w-2xl mx-auto">
            <h1 class="font-display text-2xl font-bold text-ink md:text-3xl">Edit Academic Topic</h1>
            <p class="mt-1 text-sm text-ink-soft">Update the details for <span class="font-semibold text-ink">{{ $academicTopic->name }}</span>.</p>
        </div>

        <x-exam.flash />

        {{-- ============ Context ============ --}}
        <x-exam.callout tone="primary" class="mb-6 max-w-2xl mx-auto">
            <div class="flex items-start gap-3">
                <svg class="mt-0.5 h-4 w-4 shrink-0 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <p class="text-sm leading-relaxed text-ink-soft">
                    This topic belongs to
                    <a href="{{ route('academic-subjects.show', ['academic_subject' => $topicSubject, 'academic_level' => $topicLevel, 'academic_group' => $topicGroup]) }}"
                       class="font-semibold text-primary transition hover:text-primary-hi">{{ $topicSubject->name }}</a>
                    under <b class="font-semibold text-ink">{{ $topicLevel->name }}</b>.
                </p>
            </div>
        </x-exam.callout>

        {{-- ============ Edit Form ============ --}}
        <div class="mx-auto max-w-2xl">
            <x-exam.card :padded="false">
                <x-slot:header>
                    <p class="font-mono text-[10.5px] tracking-[0.14em] text-muted">TOPIC INFORMATION</p>
                    <p class="font-mono text-[10px] tracking-wider text-muted">
                        LAST UPDATED {{ strtoupper($academicTopic->updated_at->diffForHumans()) }}
                    </p>
                </x-slot:header>

                <form method="POST" action="{{ route('academic-topics.update', ['academic_topic' => $academicTopic, 'academic_subject' => $topicSubject, 'academic_level' => $topicLevel, 'academic_group' => $topicGroup]) }}">
                    @csrf
                    @method('PATCH')

                    <div class="space-y-5 p-5">
                        {{-- Topic Name --}}
                        <x-exam.field label="Topic Name" required>
                            <x-exam.input type="text" name="name" :value="old('name', $academicTopic->name)"
                                          placeholder="e.g., Algebra Basics, Photosynthesis" required />
                            @error('name')
                            <p class="mt-2 font-mono text-[11.5px] text-accent">▲ {{ $message }}</p>
                            @enderror
                            <p class="mt-1.5 text-xs text-muted">The name of this topic as it appears in the subject's curriculum.</p>
                        </x-exam.field>
                    </div>

                    {{-- Actions --}}
                    <div class="flex flex-col gap-3 border-t border-line bg-surface-2 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
                        <div class="flex flex-wrap gap-2">
                            <a href="{{ route('academic-topics.show', ['academic_topic' => $academicTopic, 'academic_subject' => $topicSubject, 'academic_level' => $topicLevel, 'academic_group' => $topicGroup]) }}"
                               class="inline-flex items-center gap-1.5 whitespace-nowrap rounded border border-line-strong px-4 py-2.5 text-[13px] font-semibold text-ink-soft transition hover:bg-surface hover:text-ink">
                                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                                Cancel
                            </a>
                            <a href="{{ route('academic-topics.index', ['academic_subject' => $topicSubject, 'academic_level' => $topicLevel, 'academic_group' => $topicGroup]) }}"
                               class="inline-flex items-center gap-1.5 whitespace-nowrap rounded border border-line-strong px-4 py-2.5 text-[13px] font-semibold text-ink-soft transition hover:bg-surface hover:text-ink">
                                All Topics
                            </a>
                        </div>
                        <button type="submit"
                                class="inline-flex items-center justify-center gap-1.5 whitespace-nowrap rounded bg-primary px-5 py-2.5 text-[13.5px] font-semibold tracking-wide text-on-primary transition hover:-translate-y-px hover:bg-primary-hi motion-reduce:transition-none">
                            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M5 13l4 4L19 7"/></svg>
                            Update Topic
                        </button>
                    </div>
                </form>
            </x-exam.card>
        </div>

    </x-exam.page>
</x-layouts.app>
