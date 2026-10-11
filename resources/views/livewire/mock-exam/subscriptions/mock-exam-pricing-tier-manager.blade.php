<div>
    <x-exam.flash />

    {{-- Toolbar --}}
    <div class="mb-5 flex flex-wrap items-center justify-between gap-4">
        <p class="font-mono text-[10.5px] tracking-[0.14em] text-muted">CONFIGURED TIERS · {{ count($tiers) }}</p>
        <x-exam.button type="button" wire:click="openCreate">+ Add Tier</x-exam.button>
    </div>

    {{-- ============ Tier form ============ --}}
    @if($showForm)
        <x-exam.card class="mb-6 animate-rise" :padded="false">
            <x-slot:header>
                <p class="font-mono text-[10.5px] tracking-[0.14em] text-muted">{{ $editingId ? 'EDIT TIER' : 'NEW TIER' }}</p>
                @if($editingId)
                    <p class="font-mono text-[10px] tracking-wider text-muted">RECORD #{{ $editingId }}</p>
                @endif
            </x-slot:header>

            <form wire:submit.prevent="save">
                <div class="grid grid-cols-1 gap-4 p-5 sm:grid-cols-2 lg:grid-cols-3">
                    <x-exam.field bare label="Tier Name" required>
                        <x-exam.input wire:model="name" placeholder="e.g. School – 3 Subjects" />
                    </x-exam.field>

                    <x-exam.field bare label="User Group" required>
                        <x-exam.select wire:model="userGroup">
                            @foreach($this->userGroupOptions as $value => $label)
                                <option value="{{ $value }}">{{ $label }}</option>
                            @endforeach
                        </x-exam.select>
                    </x-exam.field>

                    <x-exam.field bare label="Subject Count" required>
                        <x-exam.input type="number" min="1" max="20" wire:model="subjectCount" />
                        <p class="mt-1.5 text-xs text-muted">Between 1 and 20 subjects per subscription.</p>
                    </x-exam.field>

                    <x-exam.field bare label="Price Per Student (GHS) — Online" required>
                        <x-exam.input type="number" step="0.01" min="0" wire:model="pricePerStudent" placeholder="0.00" />
                    </x-exam.field>

                    <x-exam.field bare label="Print Flat Rate (GHS) — Per Subject" required>
                        <x-exam.input type="number" step="0.01" min="0" wire:model="printFlatRate" placeholder="0.00" />
                    </x-exam.field>

                    <x-exam.field bare label="Availability">
                        <label class="flex cursor-pointer items-center gap-2.5 rounded border border-line bg-surface-2 px-3.5 py-2.5 text-sm text-ink-soft transition hover:border-primary/40">
                            <input type="checkbox" wire:model="isActive" class="h-4 w-4 rounded border-line-strong accent-primary focus:ring-primary/20" />
                            Tier is active
                            <span class="ml-auto font-mono text-[10px] tracking-wider {{ $isActive ? 'text-ok' : 'text-muted' }}">
                                {{ $isActive ? 'VISIBLE' : 'HIDDEN' }}
                            </span>
                        </label>
                    </x-exam.field>
                </div>

                <div class="flex justify-end gap-2 border-t border-line bg-surface-2 px-5 py-4">
                    <x-exam.button type="button" variant="ghost" wire:click="$set('showForm', false)">Cancel</x-exam.button>
                    <x-exam.button type="submit" target="save" loading="Saving…">
                        {{ $editingId ? 'Update Tier' : 'Add Tier' }}
                    </x-exam.button>
                </div>
            </form>
        </x-exam.card>
    @endif

    {{-- ============ Tiers table ============ --}}
    @if(count($tiers) > 0)
        <x-exam.table :headers="['Name', 'User Group', 'Subjects', 'Online (per student)', 'Print (flat/subject)', 'Status', '']">
            @foreach($tiers as $tier)
                <tr wire:key="tier-{{ $tier['id'] }}" class="transition hover:bg-surface-2/60">
                    <td class="px-4 py-3 font-semibold text-ink">{{ $tier['name'] ?? '—' }}</td>
                    <td class="px-4 py-3">
                        <x-exam.badge tone="primary">
                            {{ \App\MockExam\Models\MockExamUserGroup::from($tier['user_group'])->label() }}
                        </x-exam.badge>
                    </td>
                    <td class="px-4 py-3 text-ink-soft">{{ $tier['subject_count'] }} subject{{ $tier['subject_count'] === 1 ? '' : 's' }}</td>
                    <td class="px-4 py-3 font-mono text-xs text-ink-soft">GHS {{ number_format((float) $tier['price_per_student'], 2) }}</td>
                    <td class="px-4 py-3 font-mono text-xs text-ink-soft">GHS {{ number_format((float) $tier['print_flat_rate'], 2) }}</td>
                    <td class="px-4 py-3">
                        <x-exam.badge :tone="$tier['is_active'] ? 'ok' : 'muted'">
                            {{ $tier['is_active'] ? 'Active' : 'Inactive' }}
                        </x-exam.badge>
                    </td>
                    <td class="px-4 py-3">
                        <div class="flex items-center justify-end gap-3">
                            <x-exam.link-button wire:click="openEdit({{ $tier['id'] }})">Edit</x-exam.link-button>
                            <x-exam.link-button tone="muted" wire:click="toggleActive({{ $tier['id'] }})">
                                {{ $tier['is_active'] ? 'Disable' : 'Enable' }}
                            </x-exam.link-button>
                            <x-exam.link-button tone="danger" wire:click="delete({{ $tier['id'] }})"
                                                wire:confirm="Delete this tier? Existing subscriptions are not affected.">
                                Delete
                            </x-exam.link-button>
                        </div>
                    </td>
                </tr>
            @endforeach
        </x-exam.table>

        <p class="mt-3 font-mono text-[10px] tracking-wider text-muted">ORDERED BY SUBJECT COUNT · THEN NAME</p>
    @else
        <x-exam.empty title="No pricing tiers configured yet" description="Add your first tier to start pricing mock exam subscriptions.">
            <x-exam.button type="button" class="mt-5" wire:click="openCreate">+ Add Tier</x-exam.button>
        </x-exam.empty>
    @endif
</div>