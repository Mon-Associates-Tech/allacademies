<x-layouts.app title="New Academic Group" :has-action="false">
    <x-exam.page>

        {{-- Breadcrumb --}}
        <x-exam.crumbs :items="[
            ['label' => 'Academic Groups', 'href' => route('academic-groups.index')],
            ['label' => 'Create'],
        ]" />

        {{-- ============ Header ============ --}}
        <div class="mb-6">
            <h1 class="font-display text-2xl font-bold text-ink md:text-3xl">Create Academic Group</h1>
            <p class="mt-1 text-sm text-ink-soft">Set up a new academic group to organize your educational content.</p>
        </div>

        <x-exam.flash />

        {{-- ============ Main Form ============ --}}
        <form method="POST" action="{{ route('academic-groups.store')}}"
              x-data="{
                  name: '',
                  isValid: false,
                  updateValidation() {
                      this.isValid = this.name.trim().length >= 3;
                  }
              }"
              x-init="updateValidation()">
            @csrf

            <x-exam.card :padded="false">
                <x-slot:header>
                    <p class="font-mono text-[10.5px] tracking-[0.14em] text-muted">NEW RECORD</p>
                </x-slot:header>

                <div class="space-y-6 p-5">
                    {{-- Academic Group Name --}}
                    <x-exam.field label="Academic Group Name" required>
        <x-exam.input type="text" name="name" x-model="name" x-on:input="updateValidation()"
                      placeholder="e.g., Primary School, Secondary School, University Level" required />
        @error('name')
        <p class="mt-2 font-mono text-[11.5px] text-accent">▲ {{ $message }}</p>
        @enderror
        <p class="mt-1.5 text-xs text-muted">
            Choose a descriptive name that clearly identifies this academic group.
        </p>
        </x-exam.field>

        {{-- Information Panel --}}
        <x-exam.callout tone="primary">
            <p class="mb-1.5 font-mono text-[10px] font-semibold uppercase tracking-[0.14em] text-primary">What is an Academic Group?</p>
            <p class="mb-2 text-xs leading-relaxed text-ink-soft">
                Academic Groups help you organize your educational content by academic level or institution type.
            </p>
            <ul class="space-y-1 text-xs leading-relaxed text-ink-soft">
                <li class="flex items-start gap-2">
                    <span class="mt-1 h-1 w-1 shrink-0 rounded-full bg-primary"></span>
                    Group academic levels together (e.g., Primary, Secondary)
                </li>
                <li class="flex items-start gap-2">
                    <span class="mt-1 h-1 w-1 shrink-0 rounded-full bg-primary"></span>
                    Organize subjects and courses by educational stage
                </li>
                <li class="flex items-start gap-2">
                    <span class="mt-1 h-1 w-1 shrink-0 rounded-full bg-primary"></span>
                    Manage teachers and content across different academic levels
                </li>
                <li class="flex items-start gap-2">
                    <span class="mt-1 h-1 w-1 shrink-0 rounded-full bg-primary"></span>
                    Set up subscription packages for different academic groups
                </li>
            </ul>
        </x-exam.callout>

        {{-- Preview Section --}}
        <div x-show="name.trim().length > 0" x-transition>
            <p class="mb-2 font-mono text-[10.5px] tracking-[0.14em] text-muted">PREVIEW</p>
            <div class="flex items-center gap-4 rounded-md border border-line bg-surface-2 p-4">
                            <span class="grid h-12 w-12 shrink-0 place-items-center rounded-md bg-primary/10 font-mono text-sm font-bold text-primary">
                                <span x-text="name.trim().substring(0, 3).toUpperCase() || 'GRP'"></span>
                            </span>
                <div class="min-w-0">
                    <p class="truncate font-display text-base font-bold text-ink" x-text="name || 'Academic Group Name'"></p>
                    <p class="font-mono text-[10.5px] tracking-wide text-muted">0 LEVELS · 0 TEACHERS</p>
                </div>
            </div>
        </div>
        </div>

        {{-- Form Actions --}}
        <div class="flex flex-col gap-4 border-t border-line bg-surface-2 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
            {{-- Validation Message --}}
            <div class="flex items-center gap-2 font-mono text-[11px] tracking-wide"
                 :class="isValid ? 'text-ok' : 'text-warn'">
                <svg x-show="!isValid" class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 15.5c-.77.833.192 2.5 1.732 2.5z"/>
                </svg>
                <svg x-show="isValid" x-cloak class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span x-show="!isValid">NAME MUST BE AT LEAST 3 CHARACTERS</span>
                <span x-show="isValid" x-cloak>READY TO CREATE ACADEMIC GROUP</span>
            </div>

            {{-- Buttons --}}
            <div class="flex gap-2">
                <a href="{{ route('academic-groups.index') }}"
                   class="inline-flex items-center justify-center whitespace-nowrap rounded border border-line-strong px-4 py-2.5 text-[13px] font-semibold text-ink-soft transition hover:bg-surface hover:text-ink">
                    Cancel
                </a>
                <button type="submit" :disabled="!isValid"
                        class="inline-flex items-center justify-center whitespace-nowrap rounded bg-primary px-5 py-2.5 text-[13.5px] font-semibold tracking-wide text-on-primary transition hover:-translate-y-px hover:bg-primary-hi disabled:cursor-not-allowed disabled:opacity-50 disabled:hover:translate-y-0 motion-reduce:transition-none">
                    <span x-show="!isValid">Complete Form First</span>
                    <span x-show="isValid" x-cloak>Create Academic Group</span>
                </button>
            </div>
        </div>
        </x-exam.card>

        {{-- Next Steps --}}
        <x-exam.callout tone="ok" class="mt-6" x-show="isValid" x-transition>
            <p class="mb-1 font-mono text-[10px] font-semibold uppercase tracking-[0.14em] text-ok">Next Steps</p>
            <p class="text-xs leading-relaxed text-ink-soft">
                After creating this academic group, you'll be able to add academic levels, assign teachers, and organize your educational content.
            </p>
        </x-exam.callout>
        </form>

    </x-exam.page>
</x-layouts.app>
