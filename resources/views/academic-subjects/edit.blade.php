<x-layouts.app title="Edit Academic Subject" :has-action="false">
    <x-exam.page>

        {{-- Breadcrumb --}}
        <x-exam.crumbs :items="[
            ['label' => 'Academic Groups', 'href' => route('academic-groups.index')],
            ['label' => $academicSubject->academicLevel->academicGroup->name, 'href' => route('academic-groups.show', ['academic_group' => $academicSubject->academicLevel->academicGroup])],
            ['label' => 'Academic Levels', 'href' => route('academic-levels.index', ['academic_group' => $academicSubject->academicLevel->academicGroup])],
            ['label' => $academicSubject->academicLevel->name, 'href' => route('academic-levels.show', ['academic_level' => $academicSubject->academicLevel, 'academic_group' => $academicSubject->academicLevel->academicGroup])],
            ['label' => 'Subjects', 'href' => route('academic-subjects.index', ['academic_level' => $academicSubject->academicLevel, 'academic_group' => $academicSubject->academicLevel->academicGroup])],
            ['label' => $academicSubject->name, 'href' => route('academic-subjects.show', ['academic_subject' => $academicSubject, 'academic_level' => $academicSubject->academicLevel, 'academic_group' => $academicSubject->academicLevel->academicGroup])],
            ['label' => 'Edit'],
        ]" />

        {{-- ============ Header ============ --}}
        <div class="mb-6 max-w-2xl mx-auto">
            <h1 class="font-display text-2xl font-bold text-ink md:text-3xl">Edit Academic Subject</h1>
            <p class="mt-1 text-sm text-ink-soft">Update the details for <span class="font-semibold text-ink">{{ $academicSubject->name }}</span>.</p>
        </div>

        <x-exam.flash />

        {{-- ============ Context ============ --}}
        <x-exam.callout tone="primary" class="mb-6 max-w-2xl mx-auto">
            <div class="flex items-start gap-3">
                <svg class="mt-0.5 h-4 w-4 shrink-0 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <p class="text-sm leading-relaxed text-ink-soft">
                    This subject belongs to
                    <a href="{{ route('academic-levels.show', ['academic_level' => $academicSubject->academicLevel, 'academic_group' => $academicSubject->academicLevel->academicGroup]) }}"
                       class="font-semibold text-primary transition hover:text-primary-hi">{{ $academicSubject->academicLevel->name }}</a>
                    in
                    <a href="{{ route('academic-groups.show', ['academic_group' => $academicSubject->academicLevel->academicGroup]) }}"
                       class="font-semibold text-primary transition hover:text-primary-hi">{{ $academicSubject->academicLevel->academicGroup->name }}</a>.
                </p>
            </div>
        </x-exam.callout>

        {{-- ============ Edit Form ============ --}}
        <div class="mx-auto max-w-2xl">
            <x-exam.card :padded="false">
                <x-slot:header>
                    <p class="font-mono text-[10.5px] tracking-[0.14em] text-muted">SUBJECT INFORMATION</p>
                    <p class="font-mono text-[10px] tracking-wider text-muted">
                        LAST UPDATED {{ strtoupper($academicSubject->updated_at->diffForHumans()) }}
                    </p>
                </x-slot:header>

                <form method="POST" action="{{ route('academic-subjects.update', ['academic_subject' => $academicSubject, 'academic_level' => $academicSubject->academicLevel, 'academic_group' => $academicSubject->academicLevel->academicGroup]) }}">
                    @csrf
                    @method('PATCH')

                    <div class="space-y-5 p-5">
                        {{-- Subject Name --}}
                        <x-exam.field label="Subject Name" required>
                            <x-exam.input type="text" name="name" :value="old('name', $academicSubject->name)"
                                          placeholder="e.g., Mathematics, English, Science" required />
                            @error('name')
                            <p class="mt-2 font-mono text-[11.5px] text-accent">▲ {{ $message }}</p>
                            @enderror
                            <p class="mt-1.5 text-xs text-muted">The full name of the subject as it will appear across the system.</p>
                        </x-exam.field>

                        {{-- Subject Code --}}
                        <x-exam.field label="Subject Code" required>
                            <x-exam.input type="text" name="code" :value="old('code', $academicSubject->code)"
                                          placeholder="e.g., MATH101, ENG201" required />
                            @error('code')
                            <p class="mt-2 font-mono text-[11.5px] text-accent">▲ {{ $message }}</p>
                            @enderror
                            <p class="mt-1.5 text-xs text-muted">A unique identifier code for this subject.</p>
                        </x-exam.field>
                    </div>

                    {{-- Actions --}}
                    <div class="flex flex-col gap-3 border-t border-line bg-surface-2 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
                        <div class="flex flex-wrap gap-2">
                            <a href="{{ route('academic-subjects.show', ['academic_subject' => $academicSubject, 'academic_level' => $academicSubject->academicLevel, 'academic_group' => $academicSubject->academicLevel->academicGroup]) }}"
                               class="inline-flex items-center gap-1.5 whitespace-nowrap rounded border border-line-strong px-4 py-2.5 text-[13px] font-semibold text-ink-soft transition hover:bg-surface hover:text-ink">
                                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                                Cancel
                            </a>
                            <a href="{{ route('academic-subjects.index', ['academic_level' => $academicSubject->academicLevel, 'academic_group' => $academicSubject->academicLevel->academicGroup]) }}"
                               class="inline-flex items-center gap-1.5 whitespace-nowrap rounded border border-line-strong px-4 py-2.5 text-[13px] font-semibold text-ink-soft transition hover:bg-surface hover:text-ink">
                                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/></svg>
                                All Subjects
                            </a>
                        </div>
                        <button type="submit"
                                class="inline-flex items-center justify-center gap-1.5 whitespace-nowrap rounded bg-primary px-5 py-2.5 text-[13.5px] font-semibold tracking-wide text-on-primary transition hover:-translate-y-px hover:bg-primary-hi motion-reduce:transition-none">
                            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M5 13l4 4L19 7"/></svg>
                            Update Subject
                        </button>
                    </div>
                </form>
            </x-exam.card>
        </div>

    </x-exam.page>
</x-layouts.app>
