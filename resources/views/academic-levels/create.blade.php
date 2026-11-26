<x-layouts.app title="New Academic Level" :has-action="false">
    <x-exam.page>

        {{-- Breadcrumb --}}
        <x-exam.crumbs :items="[
            ['label' => 'Academic Groups', 'href' => route('academic-groups.index')],
            ['label' => $academicGroup->name, 'href' => route('academic-groups.show', ['academic_group' => $academicGroup])],
            ['label' => 'Academic Levels', 'href' => route('academic-levels.index', ['academic_group' => $academicGroup])],
            ['label' => 'New Level'],
        ]" />

        {{-- ============ Header ============ --}}
        <div class="mb-6">
            <h1 class="font-display text-2xl font-bold text-ink md:text-3xl">New Academic Level</h1>
            <p class="mt-1 text-sm text-ink-soft">
                Create a new level in <span class="font-semibold text-ink">{{ $academicGroup->name }}</span>.
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
                    This level will be created in <b class="font-semibold text-ink">{{ $academicGroup->name }}</b>.
                    You can add subjects to it after creation.
                </p>
            </div>
        </x-exam.callout>

        {{-- ============ Form ============ --}}
        <div class="mx-auto max-w-2xl">
            <x-exam.card :padded="false">
                <x-slot:header>
                    <p class="font-mono text-[10.5px] tracking-[0.14em] text-muted">LEVEL INFORMATION</p>
                    <p class="font-mono text-[10px] tracking-wider text-muted"><span class="font-bold text-accent">*</span> REQUIRED</p>
                </x-slot:header>

                <form method="POST" action="{{ route('academic-levels.store', ['academic_group' => $academicGroup]) }}">
                    @csrf

                    <div class="space-y-5 p-5">
                        {{-- Level Name --}}
                        <x-exam.field label="Level Name" required>
                            <x-exam.input type="text" name="name" :value="old('name')"
                                          placeholder="e.g., Form 1, Grade 10, Year 7" required />
                            @error('name')
                            <p class="mt-2 font-mono text-[11.5px] text-accent">▲ {{ $message }}</p>
                            @enderror
                            <p class="mt-1.5 text-xs text-muted">The internal name used to identify this academic level.</p>
                        </x-exam.field>

                        {{-- Display Label --}}
                        <x-exam.field label="Display Label" required>
                            <x-exam.input type="text" name="label" :value="old('label')"
                                          placeholder="e.g., Form One, Tenth Grade, Year Seven" required />
                            @error('label')
                            <p class="mt-2 font-mono text-[11.5px] text-accent">▲ {{ $message }}</p>
                            @enderror
                            <p class="mt-1.5 text-xs text-muted">The user-friendly name displayed to students and teachers.</p>
                        </x-exam.field>

                        {{-- Academic Group (read-only) --}}
                        <x-exam.field label="Academic Group">
                            <div class="flex items-center gap-3 rounded-md border border-line bg-surface-2 px-3.5 py-2.5">
                                <svg class="h-4 w-4 shrink-0 text-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                </svg>
                                <span class="text-sm font-semibold text-ink">{{ $academicGroup->name }}</span>
                                <span class="ml-auto font-mono text-[10px] tracking-wider text-muted">READ-ONLY</span>
                            </div>
                        </x-exam.field>
                    </div>

                    {{-- Actions --}}
                    <div class="flex items-center justify-between gap-2 border-t border-line bg-surface-2 px-5 py-4">
                        <a href="{{ route('academic-levels.index', ['academic_group' => $academicGroup]) }}"
                           class="inline-flex items-center gap-1.5 whitespace-nowrap rounded border border-line-strong px-4 py-2.5 text-[13px] font-semibold text-ink-soft transition hover:bg-surface hover:text-ink">
                            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                            Cancel
                        </a>
                        <button type="submit"
                                class="inline-flex items-center gap-1.5 whitespace-nowrap rounded bg-primary px-5 py-2.5 text-[13.5px] font-semibold tracking-wide text-on-primary transition hover:-translate-y-px hover:bg-primary-hi motion-reduce:transition-none">
                            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M12 4v16m8-8H4"/></svg>
                            Create Academic Level
                        </button>
                    </div>
                </form>
            </x-exam.card>
        </div>

    </x-exam.page>
</x-layouts.app>
