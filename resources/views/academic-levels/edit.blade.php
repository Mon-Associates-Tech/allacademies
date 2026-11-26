<x-layouts.app title="Edit Academic Level" :has-action="false">
    <x-exam.page>

        {{-- Breadcrumb --}}
        <x-exam.crumbs :items="[
            ['label' => 'Academic Groups', 'href' => route('academic-groups.index')],
            ['label' => $academicLevel->academicGroup->name, 'href' => route('academic-groups.show', ['academic_group' => $academicLevel->academicGroup])],
            ['label' => 'Academic Levels', 'href' => route('academic-levels.index', ['academic_group' => $academicLevel->academicGroup])],
            ['label' => $academicLevel->name, 'href' => route('academic-levels.show', ['academic_level' => $academicLevel, 'academic_group' => $academicLevel->academicGroup])],
            ['label' => 'Edit'],
        ]" />

        {{-- ============ Header ============ --}}
        <div class="mb-6">
            <h1 class="font-display text-2xl font-bold text-ink md:text-3xl">Edit Academic Level</h1>
            <p class="mt-1 text-sm text-ink-soft">Update the details for <span class="font-semibold text-ink">{{ $academicLevel->name }}</span>.</p>
        </div>

        <x-exam.flash />

        {{-- ============ Context ============ --}}
        <x-exam.callout tone="primary" class="mb-6 max-w-2xl mx-auto">
            <div class="flex items-start gap-3">
                <svg class="mt-0.5 h-4 w-4 shrink-0 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <p class="text-sm leading-relaxed text-ink-soft">
                    This academic level belongs to <b class="font-semibold text-ink">{{ $academicLevel->academicGroup->name }}</b>
                    @if($academicLevel->academic_subjects_count > 0)
                        and currently has <b class="font-semibold text-ink">{{ $academicLevel->academic_subjects_count }}</b>
                        {{ Str::plural('subject', $academicLevel->academic_subjects_count) }} associated with it.
                    @endif
                </p>
            </div>
        </x-exam.callout>

        {{-- ============ Edit Form ============ --}}
        <div class="mx-auto max-w-2xl">
            <x-exam.card :padded="false">
                <x-slot:header>
                    <p class="font-mono text-[10.5px] tracking-[0.14em] text-muted">LEVEL INFORMATION</p>
                    <p class="font-mono text-[10px] tracking-wider text-muted"><span class="font-bold text-accent">*</span> REQUIRED</p>
                </x-slot:header>

                <form method="POST" action="{{ route('academic-levels.update', ['academic_level' => $academicLevel, 'academic_group' => $academicLevel->academicGroup]) }}">
                    @csrf
                    @method('PATCH')

                    <div class="space-y-5 p-5">
                        {{-- Level Name --}}
                        <x-exam.field label="Level Name" required>
                            <x-exam.input type="text" name="name" :value="old('name', $academicLevel->name)"
                                          placeholder="e.g., Form 1, Grade 10, Year 7" required />
                            @error('name')
                            <p class="mt-2 font-mono text-[11.5px] text-accent">▲ {{ $message }}</p>
                            @enderror
                            <p class="mt-1.5 text-xs text-muted">The internal name used to identify this academic level.</p>
                        </x-exam.field>

                        {{-- Display Label --}}
                        <x-exam.field label="Display Label" required>
                            <x-exam.input type="text" name="label" :value="old('label', $academicLevel->label)"
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
                                <span class="text-sm font-semibold text-ink">{{ $academicLevel->academicGroup->name }}</span>
                                <span class="ml-auto font-mono text-[10px] tracking-wider text-muted">CANNOT BE CHANGED</span>
                            </div>
                            <p class="mt-1.5 text-xs text-muted">To move this level to a different group, contact your administrator.</p>
                        </x-exam.field>
                    </div>

                    {{-- Actions --}}
                    <div class="flex items-center justify-between gap-2 border-t border-line bg-surface-2 px-5 py-4">
                        <div class="flex gap-2">
                            <a href="{{ route('academic-levels.show', ['academic_level' => $academicLevel, 'academic_group' => $academicLevel->academicGroup]) }}"
                               class="inline-flex items-center gap-1.5 whitespace-nowrap rounded border border-line-strong px-4 py-2.5 text-[13px] font-semibold text-ink-soft transition hover:bg-surface hover:text-ink">
                                <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                                Cancel
                            </a>
                            <a href="{{ route('academic-levels.index', ['academic_group' => $academicLevel->academicGroup]) }}"
                               class="hidden items-center gap-1.5 whitespace-nowrap rounded border border-line-strong px-4 py-2.5 text-[13px] font-semibold text-ink-soft transition hover:bg-surface hover:text-ink sm:inline-flex">
                                All Levels
                            </a>
                        </div>
                        <button type="submit"
                                class="inline-flex items-center gap-1.5 whitespace-nowrap rounded bg-primary px-5 py-2.5 text-[13.5px] font-semibold tracking-wide text-on-primary transition hover:-translate-y-px hover:bg-primary-hi motion-reduce:transition-none">
                            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.2" d="M5 13l4 4L19 7"/></svg>
                            Update Academic Level
                        </button>
                    </div>
                </form>
            </x-exam.card>

            {{-- ============ Warning: affects subjects ============ --}}
            @if($academicLevel->academic_subjects_count > 0)
                <x-exam.callout tone="warn" class="mt-6">
                    <div class="flex items-start gap-3">
                        <svg class="mt-0.5 h-4 w-4 shrink-0 text-warn" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 15.5c-.77.833.192 2.5 1.732 2.5z"/>
                        </svg>
                        <div class="min-w-0 flex-1">
                            <p class="mb-1.5 font-mono text-[10px] font-semibold uppercase tracking-[0.14em] text-warn">Important Note</p>
                            <p class="text-xs leading-relaxed text-ink-soft">
                                Changing the name or label will affect how this level appears across the system, including:
                            </p>
                            <ul class="mt-2 space-y-1 text-xs leading-relaxed text-ink-soft">
                                <li class="flex items-start gap-2">
                                    <span class="mt-1 h-1 w-1 shrink-0 rounded-full bg-warn"></span>
                                    Subject listings and navigation
                                </li>
                                <li class="flex items-start gap-2">
                                    <span class="mt-1 h-1 w-1 shrink-0 rounded-full bg-warn"></span>
                                    Student and teacher dashboards
                                </li>
                                <li class="flex items-start gap-2">
                                    <span class="mt-1 h-1 w-1 shrink-0 rounded-full bg-warn"></span>
                                    Reports and analytics
                                </li>
                                <li class="flex items-start gap-2">
                                    <span class="mt-1 h-1 w-1 shrink-0 rounded-full bg-warn"></span>
                                    Academic transcripts
                                </li>
                            </ul>
                        </div>
                    </div>
                </x-exam.callout>
            @endif

            {{-- ============ Quick Actions ============ --}}
            <x-exam.card class="mt-6" :padded="false">
                <x-slot:header>
                    <p class="font-mono text-[10.5px] tracking-[0.14em] text-muted">QUICK ACTIONS</p>
                </x-slot:header>
                <ul class="divide-y divide-line">
                    <li>
                        <a href="{{ route('academic-subjects.index', ['academic_level' => $academicLevel, 'academic_group' => $academicLevel->academicGroup]) }}"
                           class="flex items-center justify-between px-5 py-3 transition hover:bg-surface-2">
                            <div class="flex items-center gap-3">
                                <svg class="h-4 w-4 text-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                                <div>
                                    <p class="text-sm font-semibold text-ink">Manage Subjects</p>
                                    <p class="font-mono text-[10.5px] tracking-wide text-muted">{{ $academicLevel->academic_subjects_count }} {{ Str::plural('SUBJECT', $academicLevel->academic_subjects_count) }}</p>
                                </div>
                            </div>
                            <svg class="h-4 w-4 text-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                        </a>
                    </li>
                    @can('administrate')
                        <li>
                            <a href="{{ route('academic-subjects.create', ['academic_level' => $academicLevel, 'academic_group' => $academicLevel->academicGroup]) }}"
                               class="flex items-center justify-between px-5 py-3 transition hover:bg-surface-2">
                                <div class="flex items-center gap-3">
                                    <svg class="h-4 w-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                                    <div>
                                        <p class="text-sm font-semibold text-primary">Add Subject</p>
                                        <p class="font-mono text-[10.5px] tracking-wide text-muted">CREATE NEW SUBJECT</p>
                                    </div>
                                </div>
                                <svg class="h-4 w-4 text-muted" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                            </a>
                        </li>
                    @endcan
                </ul>
            </x-exam.card>
        </div>

    </x-exam.page>
</x-layouts.app>
