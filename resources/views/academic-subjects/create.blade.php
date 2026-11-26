<x-layouts.app title="New Academic Subject" :has-action="false">
    <x-exam.page>
        @php
            $subjectLevel = $academicLevel;
            $subjectGroup = $academicLevel->academicGroup;
        @endphp

        {{-- Breadcrumb --}}
        <x-exam.crumbs :items="[
            ['label' => 'Academic Groups', 'href' => route('academic-groups.index')],
            ['label' => $subjectGroup->name, 'href' => route('academic-groups.show', ['academic_group' => $subjectGroup])],
            ['label' => 'Academic Levels', 'href' => route('academic-levels.index', ['academic_group' => $subjectGroup])],
            ['label' => $subjectLevel->name, 'href' => route('academic-levels.show', ['academic_level' => $subjectLevel, 'academic_group' => $subjectGroup])],
            ['label' => 'Subjects', 'href' => route('academic-subjects.index', ['academic_level' => $subjectLevel, 'academic_group' => $subjectGroup])],
            ['label' => 'New Subject'],
        ]" />

        {{-- ============ Header ============ --}}
        <div class="mb-6 mx-auto max-w-2xl">
            <h1 class="font-display text-2xl font-bold text-ink md:text-3xl">New Academic Subject</h1>
            <p class="mt-1 text-sm text-ink-soft">
                Create a new subject for <span class="font-semibold text-ink">{{ $subjectLevel->name }}</span>.
            </p>
        </div>

        <x-exam.flash />

        {{-- ============ Context ============ --}}
        <x-exam.callout tone="primary" class="mb-6 mx-auto max-w-2xl">
            <div class="flex items-start gap-3">
                <svg class="mt-0.5 h-4 w-4 shrink-0 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <p class="text-sm leading-relaxed text-ink-soft">
                    This subject will be added to
                    <a href="{{ route('academic-levels.show', ['academic_level' => $subjectLevel, 'academic_group' => $subjectGroup]) }}"
                       class="font-semibold text-primary transition hover:text-primary-hi">{{ $subjectLevel->name }}</a>
                    in <b class="font-semibold text-ink">{{ $subjectGroup->name }}</b>.
                </p>
            </div>
        </x-exam.callout>

        {{-- ============ Form ============ --}}
        <div class="mx-auto max-w-2xl">
            <x-exam.card :padded="false">
                <x-slot:header>
                    <p class="font-mono text-[10.5px] tracking-[0.14em] text-muted">SUBJECT INFORMATION</p>
                    <p class="font-mono text-[10px] tracking-wider text-muted"><span class="font-bold text-accent">*</span> REQUIRED</p>
                </x-slot:header>

                <form method="POST" action="{{ route('academic-subjects.store', ['academic_level' => $subjectLevel, 'academic_group' => $subjectGroup]) }}">
                    @csrf

                    <div class="space-y-5 p-5">
                        {{-- Subject Name --}}
                        <x-exam.field label="Subject Name" required>
                            <x-exam.input type="text" name="name" :value="old('name')"
                                          placeholder="e.g., Mathematics, English Literature" required />
                            @error('name')
                            <p class="mt-2 font-mono text-[11.5px] text-accent">▲ {{ $message }}</p>
                            @enderror
                            <p class="mt-1.5 text-xs text-muted">The full name of the subject as it will appear across the system.</p>
                        </x-exam.field>

                        {{-- Subject Code --}}
                        <x-exam.field label="Subject Code" required>
                            <x-exam.input type="text" name="code" :value="old('code')"
                                          placeholder="e.g., MATH101, ENG201" required />
                            @error('code')
                            <p class="mt-2 font-mono text-[11.5px] text-accent">▲ {{ $message }}</p>
                            @enderror
                            <p class="mt-1.5 text-xs text-muted">A unique identifier for this subject. Usually a short code.</p>
                        </x-exam.field>
                    </div>

                    {{-- Actions --}}
                    <div class="flex items-center justify-between gap-2 border-t border-line bg-surface-2 px-5 py-4">
                        <a href="{{ route('academic-subjects.index', ['academic_level' => $subjectLevel, 'academic_group' => $subjectGroup]) }}"
                           class="inline-flex items-center gap-1.5 whitespace-nowrap rounded border border-line-strong px-4 py-2.5 text-[13px] font-semibold text-ink-soft transition hover:bg-surface hover:text-ink">
                            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                            Cancel
                        </a>
                        <button type="submit"
                                class="inline-flex items-center gap-1.5 whitespace-nowrap rounded bg-primary px-5 py-2.5 text-[13.5px] font-semibold tracking-wide text-on-primary transition hover:-translate-y-px hover:bg-primary-hi motion-reduce:transition-none">
                            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M12 4v16m8-8H4"/></svg>
                            Create Subject
                        </button>
                    </div>
                </form>
            </x-exam.card>
        </div>

    </x-exam.page>
</x-layouts.app>
