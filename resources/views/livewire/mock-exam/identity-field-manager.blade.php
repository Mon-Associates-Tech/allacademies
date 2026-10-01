{{-- resources/views/livewire/mock-exam/identity-fields.blade.php --}}

<x-exam.page>
    <x-exam.header
        eyebrow="ADMIN · EXAM IDENTITY SCHEMA"
        title="Identity Fields"
        sub="Define the fields every user fills in for their exam identity section — shown at the top of every generated exam, above the front page."
        :lines="['FIELD', 'SCHEMA']"
    />

    <x-exam.flash />

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-[340px_1fr]">

        {{-- ============ Form ============ --}}
        <x-exam.card class="h-fit animate-rise lg:sticky lg:top-7" :padded="false">
            <x-slot:header>
                <p class="font-mono text-[10.5px] tracking-[0.14em] text-muted">
                    {{ $editingId ? 'EDIT FIELD · #' . $editingId : 'NEW FIELD' }}
                </p>
            </x-slot:header>

            <form wire:submit.prevent="save" class="space-y-4 p-5">
                <x-exam.field bare label="Key" required>
                    <x-exam.input wire:model="key" placeholder="school_name" />
                </x-exam.field>

                <x-exam.field bare label="Label" required>
                    <x-exam.input wire:model="label" placeholder="School Name" />
                </x-exam.field>

                <x-exam.field bare label="Type">
                    <x-exam.select wire:model="type">
                        <option value="text">Text</option>
                        <option value="image">Image</option>
                    </x-exam.select>
                </x-exam.field>

                <x-exam.field bare label="Pretext (optional)">
                    <x-exam.input wire:model="pretext" placeholder="AllAcademies Examination Conducted For" />
                    <p class="mt-1.5 font-mono text-[10.5px] tracking-wide text-muted">
                        PRINTS BEFORE THE VALUE ON THE SHEET
                    </p>
                </x-exam.field>

                <x-exam.field bare label="Font Size (pt) — text fields only">
                    <x-exam.input type="number" min="6" max="72" wire:model="fontSize" />
                </x-exam.field>

                <div class="space-y-2.5 border-t border-dashed border-line pt-4">
                    <label class="flex cursor-pointer items-center gap-2.5 text-sm text-ink-soft">
                        <input type="checkbox" wire:model="sameRow" class="h-4 w-4 rounded border-line-strong accent-primary focus:ring-primary/20" />
                        Same row as previous field
                    </label>
                    <label class="flex cursor-pointer items-center gap-2.5 text-sm text-ink-soft">
                        <input type="checkbox" wire:model="isRequired" class="h-4 w-4 rounded border-line-strong accent-primary focus:ring-primary/20" />
                        Required <span class="font-bold text-accent">*</span>
                    </label>
                </div>
            </form>

            <x-slot:footer>
                <div class="flex gap-2">
                    <x-exam.button type="submit" wire:click="save">
                        {{ $editingId ? 'Update' : 'Add Field' }}
                    </x-exam.button>
                    @if($editingId)
                        <x-exam.button type="button" variant="ghost" wire:click="resetForm">Cancel</x-exam.button>
                    @endif
                </div>
            </x-slot:footer>
        </x-exam.card>

        {{-- ============ List ============ --}}
        <div class="animate-rise [animation-delay:60ms]">
            <p class="mb-3 font-mono text-[10.5px] tracking-[0.14em] text-muted">
                REGISTERED FIELDS · {{ count($fields) }}
            </p>

            @if(empty($fields))
                <x-exam.empty compact title="No fields yet" description="Add one on the left to start building the exam header." />
            @else
                <x-exam.table :headers="['Order', 'Key', 'Label', 'Type', 'Row', 'Pretext', '']">
                    @foreach($fields as $field)
                        <tr wire:key="field-{{ $field->id }}" class="transition hover:bg-surface-2/60">
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-1.5">
                                    <button type="button" wire:click="moveUp({{ $field->id }})"
                                            aria-label="Move up"
                                            class="grid h-7 w-7 place-items-center rounded border border-line bg-surface text-muted transition hover:border-primary hover:text-primary focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-accent">↑</button>
                                    <button type="button" wire:click="moveDown({{ $field->id }})"
                                            aria-label="Move down"
                                            class="grid h-7 w-7 place-items-center rounded border border-line bg-surface text-muted transition hover:border-primary hover:text-primary focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-accent">↓</button>
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <code class="rounded bg-surface-2 px-1.5 py-0.5 font-mono text-[11px] text-ink-soft">{{ $field->key }}</code>
                            </td>
                            <td class="px-4 py-3 font-semibold text-ink">{{ $field->label }}</td>
                            <td class="px-4 py-3">
                                <x-exam.badge :tone="$field->type === 'image' ? 'warn' : 'primary'">
                                    {{ ucfirst($field->type) }}{{ $field->type === 'text' && $field->font_size ? ' · ' . $field->font_size . 'pt' : '' }}
                                </x-exam.badge>
                            </td>
                            <td class="px-4 py-3 font-mono text-[10.5px] tracking-wide text-muted">
                                {{ $field->same_row ? '↳ SAME ROW' : 'NEW ROW' }}
                            </td>
                            <td class="px-4 py-3 max-w-[180px] truncate font-mono text-[11px] text-muted">
                                {{ $field->pretext ?: '—' }}
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center justify-end gap-3">
                                    <x-exam.link-button wire:click="edit({{ $field->id }})">Edit</x-exam.link-button>
                                    <x-exam.link-button tone="danger" wire:click="delete({{ $field->id }})"
                                                        wire:confirm="Delete this field? Existing saved values for it stay in the database but won't display.">
                                        Delete
                                    </x-exam.link-button>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </x-exam.table>

                <p class="mt-3 font-mono text-[10.5px] tracking-wide text-muted">
                    TIP · DRAG-ORDER IS MANUAL. USE ↑ / ↓ TO REARRANGE THE HEADER BLOCK ON THE GENERATED PDF.
                </p>
            @endif
        </div>

    </div>
</x-exam.page>