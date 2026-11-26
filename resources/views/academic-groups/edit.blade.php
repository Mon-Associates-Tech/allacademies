<x-layouts.app title="Edit Academic Group" :has-action="false">
    <x-exam.page>

        {{-- Breadcrumb --}}
        <x-exam.crumbs :items="[
            ['label' => 'Academic Groups', 'href' => route('academic-groups.index')],
            ['label' => $academicGroup->name, 'href' => route('academic-groups.show', $academicGroup)],
            ['label' => 'Edit'],
        ]" />

        {{-- ============ Header ============ --}}
        <div class="mb-6">
            <p class="mb-2 flex items-center gap-2 font-mono text-[11px] tracking-[0.14em] text-ink-soft">
                <span class="h-2 w-2 bg-accent"></span>REGISTRAR'S OFFICE · RECORD AMENDMENT
            </p>
            <h1 class="font-display text-2xl font-bold text-ink md:text-3xl">Edit Academic Group</h1>
            <p class="mt-1 text-sm text-ink-soft">Update academic group information for <span class="font-semibold text-ink">{{ $academicGroup->name }}</span></p>
        </div>

        <x-exam.flash />

        {{-- ============ Current Record Info ============ --}}
        <x-exam.card class="mb-6" :padded="false">
            <x-slot:header>
                <p class="font-mono text-[10.5px] tracking-[0.14em] text-muted">CURRENT RECORD DETAILS</p>
                <p class="font-mono text-[10px] tracking-wider text-muted">RECORD #{{ $academicGroup->id }}</p>
            </x-slot:header>
            <div class="space-y-3 p-5 font-mono text-[11px] tracking-wide">
                <div class="flex items-baseline justify-between gap-2 text-muted">
                    <span>NAME</span>
                    <span class="flex-1 border-b border-dotted border-line-strong"></span>
                    <span class="font-semibold text-ink">{{ $academicGroup->name }}</span>
                </div>
                <div class="flex items-baseline justify-between gap-2 text-muted">
                    <span>CREATED</span>
                    <span class="flex-1 border-b border-dotted border-line-strong"></span>
                    <span class="font-semibold text-ink">{{ $academicGroup->created_at->format('M j, Y \a\t g:i A') }}</span>
                </div>
                <div class="flex items-baseline justify-between gap-2 text-muted">
                    <span>UPDATED</span>
                    <span class="flex-1 border-b border-dotted border-line-strong"></span>
                    <span class="font-semibold text-ink">{{ $academicGroup->updated_at->diffForHumans() }}</span>
                </div>
            </div>
        </x-exam.card>

        {{-- ============ Edit Form ============ --}}
        <x-exam.card :padded="false">
            <x-slot:header>
                <p class="font-mono text-[10.5px] tracking-[0.14em] text-muted">AMENDMENT FORM</p>
            </x-slot:header>

            <form method="POST" action="{{ route('academic-groups.update', ['academic_group' => $academicGroup]) }}">
                @csrf
                @method('PATCH')

                <div class="space-y-5 p-5">
                    {{-- Academic Group Name --}}
                    <x-exam.field label="Academic Group Name" required>
                        <x-exam.input type="text" name="name" :value="old('name', $academicGroup->name)"
                                      placeholder="Enter academic group name (e.g., Primary, Secondary, etc.)" required />
                        @error('name')
                        <p class="mt-2 font-mono text-[11.5px] text-accent">▲ {{ $message }}</p>
                        @enderror
                        <p class="mt-1.5 text-xs text-muted">
                            Choose a descriptive name that clearly identifies this academic group within your institution.
                        </p>
                    </x-exam.field>

                    {{-- Coming Soon Notice --}}
                    <x-exam.callout tone="primary">
                        <p class="mb-1.5 font-mono text-[10px] font-semibold uppercase tracking-[0.14em] text-primary">Coming Soon</p>
                        <p class="text-xs leading-relaxed text-ink-soft">
                            Additional configuration options like description, settings, and advanced features will be available in future updates.
                        </p>
                    </x-exam.callout>

                    {{-- Current Statistics --}}
                    <div class="border-t border-dashed border-line pt-5">
                        <p class="mb-3 font-mono text-[10.5px] tracking-[0.14em] text-muted">CURRENT STATISTICS</p>
                        <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                            <div class="rounded-md border border-line bg-surface-2 p-4 text-center">
                                <p class="font-display text-xl font-bold text-primary sm:text-2xl">{{ $academicGroup->academicLevels()->count() }}</p>
                                <p class="mt-0.5 font-mono text-[9px] tracking-[0.18em] text-muted sm:text-[9.5px]">ACADEMIC LEVELS</p>
                            </div>
                            <div class="rounded-md border border-line bg-surface-2 p-4 text-center">
                                <p class="font-display text-xl font-bold text-ok sm:text-2xl">{{ $academicGroup->teachers()->count() }}</p>
                                <p class="mt-0.5 font-mono text-[9px] tracking-[0.18em] text-muted sm:text-[9.5px]">TEACHERS</p>
                            </div>
                            <div class="rounded-md border border-line bg-surface-2 p-4 text-center">
                                <p class="font-display text-xl font-bold text-ink sm:text-2xl">Active</p>
                                <p class="mt-0.5 font-mono text-[9px] tracking-[0.18em] text-muted sm:text-[9.5px]">STATUS</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Form Actions --}}
                <div class="flex flex-wrap items-center justify-between gap-3 border-t border-line bg-surface-2 px-5 py-4">
                    <p class="font-mono text-[10.5px] tracking-wide text-muted">
                        LAST UPDATED {{ strtoupper($academicGroup->updated_at->diffForHumans()) }}
                    </p>
                    <div class="flex gap-2">
                        <a href="{{ route('academic-groups.show', $academicGroup) }}"
                           class="inline-flex items-center justify-center whitespace-nowrap rounded border border-line-strong px-4 py-2.5 text-[13px] font-semibold text-ink-soft transition hover:bg-surface hover:text-ink">
                            Cancel
                        </a>
                        <button type="submit"
                                class="inline-flex items-center justify-center whitespace-nowrap rounded bg-primary px-5 py-2.5 text-[13.5px] font-semibold tracking-wide text-on-primary transition hover:-translate-y-px hover:bg-primary-hi motion-reduce:transition-none">
                            Update Academic Group
                        </button>
                    </div>
                </div>
            </form>
        </x-exam.card>

        {{-- ============ Help Section ============ --}}
        <x-exam.callout tone="primary" class="mt-6">
            <p class="mb-2 font-mono text-[10px] font-semibold uppercase tracking-[0.14em] text-primary">Need Help?</p>
            <ul class="space-y-1.5 text-xs leading-relaxed text-ink-soft">
                <li class="flex items-start gap-2">
                    <span class="mt-1 h-1 w-1 shrink-0 rounded-full bg-primary"></span>
                    Academic groups help organize your institution's educational structure
                </li>
                <li class="flex items-start gap-2">
                    <span class="mt-1 h-1 w-1 shrink-0 rounded-full bg-primary"></span>
                    Common examples: Primary, Secondary, Pre-University, etc.
                </li>
                <li class="flex items-start gap-2">
                    <span class="mt-1 h-1 w-1 shrink-0 rounded-full bg-primary"></span>
                    Each group can contain multiple academic levels and associated teachers
                </li>
                <li class="flex items-start gap-2">
                    <span class="mt-1 h-1 w-1 shrink-0 rounded-full bg-primary"></span>
                    Changes will be reflected immediately across the system
                </li>
            </ul>
        </x-exam.callout>

    </x-exam.page>
</x-layouts.app>
