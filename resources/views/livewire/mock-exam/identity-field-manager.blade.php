{{-- resources/views/livewire/mock-exam/identity-field-manager.blade.php --}}
<div class="max-w-5xl mx-auto px-4 py-8">
    <div class="mb-6">
        <h1 class="text-lg font-bold text-slate-900 dark:text-white">Exam Identity Fields</h1>
        <p class="text-sm text-slate-500 dark:text-slate-400">
            Define the fields every user fills in for their exam identity section — shown at the top of every generated
            exam, above the front page.
        </p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-[320px_1fr] gap-6">
        {{-- Form --}}
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 p-4"
             style="border-radius: 2px;">
            <h2 class="text-sm font-semibold text-slate-700 dark:text-slate-300 mb-3">
                {{ $editingId ? 'Edit Field' : 'Add Field' }}
            </h2>
            <form wire:submit.prevent="save" class="space-y-3">
                <div>
                    <label class="block text-xs font-medium text-slate-500 mb-1">Key</label>
                    <input type="text" wire:model="key" placeholder="school_name"
                           class="w-full px-2.5 py-1.5 text-sm border border-slate-300 dark:border-slate-600 dark:bg-slate-800 dark:text-white"
                           style="border-radius: 2px;">
                    @error('key') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-500 mb-1">Label</label>
                    <input type="text" wire:model="label" placeholder="School Name"
                           class="w-full px-2.5 py-1.5 text-sm border border-slate-300 dark:border-slate-600 dark:bg-slate-800 dark:text-white"
                           style="border-radius: 2px;">
                    @error('label') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-500 mb-1">Type</label>
                    <select wire:model="type"
                            class="w-full px-2.5 py-1.5 text-sm border border-slate-300 dark:border-slate-600 dark:bg-slate-800 dark:text-white"
                            style="border-radius: 2px;">
                        <option value="text">Text</option>
                        <option value="image">Image</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-500 mb-1">Pretext (optional)</label>
                    <input type="text" wire:model="pretext" placeholder="AllAcademies Examination Conducted For"
                           class="w-full px-2.5 py-1.5 text-sm border border-slate-300 dark:border-slate-600 dark:bg-slate-800 dark:text-white"
                           style="border-radius: 2px;">
                </div>

                <div>
                    <label class="block text-xs font-medium text-slate-500 mb-1">Font Size (pt) — text fields
                        only</label>
                    <input type="number" wire:model="fontSize" min="6" max="72"
                           class="w-full px-2.5 py-1.5 text-sm border border-slate-300 dark:border-slate-600 dark:bg-slate-800 dark:text-white"
                           style="border-radius: 2px;">
                </div>
                <label class="flex items-center gap-2 text-xs text-slate-600 dark:text-slate-400">
                    <input type="checkbox" wire:model="sameRow"> Same row as previous field
                </label>
                <label class="flex items-center gap-2 text-xs text-slate-600 dark:text-slate-400">
                    <input type="checkbox" wire:model="isRequired"> Required
                </label>
                <div class="flex gap-2 pt-1">
                    <button type="submit"
                            class="flex-1 px-3 py-2 text-xs font-semibold text-white bg-violet-600 hover:bg-violet-700"
                            style="border-radius: 2px;">
                        {{ $editingId ? 'Update' : 'Add Field' }}
                    </button>
                    @if($editingId)
                        <button type="button" wire:click="resetForm"
                                class="px-3 py-2 text-xs font-medium text-slate-600 bg-slate-100 dark:bg-slate-800 dark:text-slate-300"
                                style="border-radius: 2px;">
                            Cancel
                        </button>
                    @endif
                </div>
            </form>
        </div>

        {{-- List --}}
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 overflow-hidden"
             style="border-radius: 2px;">
            @if(empty($fields))
                <div class="p-6 text-sm text-slate-500 text-center">No fields yet — add one on the left.</div>
            @else
                <table class="w-full text-sm">
                    <thead>
                    <tr class="border-b border-slate-200 dark:border-slate-700 text-left text-xs text-slate-500 uppercase">
                        <th class="px-4 py-2">Order</th>
                        <th class="px-4 py-2">Key</th>
                        <th class="px-4 py-2">Label</th>
                        <th class="px-4 py-2">Type</th>
                        <th class="px-4 py-2">Row</th>
                        <th class="px-4 py-2">Pretext</th>
                        <th class="px-4 py-2"></th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($fields as $field)
                        <tr class="border-b border-slate-100 dark:border-slate-800">
                            <td class="px-4 py-2 whitespace-nowrap">
                                <button type="button" wire:click="moveUp({{ $field->id }})"
                                        class="text-slate-400 hover:text-slate-700">↑
                                </button>
                                <button type="button" wire:click="moveDown({{ $field->id }})"
                                        class="text-slate-400 hover:text-slate-700">↓
                                </button>
                            </td>
                            <td class="px-4 py-2 font-mono text-xs text-slate-600 dark:text-slate-400">{{ $field->key }}</td>
                            <td class="px-4 py-2 text-slate-700 dark:text-slate-300">{{ $field->label }}</td>
                            <td class="px-4 py-2 text-slate-500">
                                {{ ucfirst($field->type) }}{{ $field->type === 'text' && $field->font_size ? ' · '.$field->font_size.'pt' : '' }}
                            </td>
                            <td class="px-4 py-2 text-xs text-slate-400">{{ $field->same_row ? '↳ same row' : 'New row' }}</td>
                            <td class="px-4 py-2 text-slate-400 text-xs">{{ $field->pretext ?: '—' }}</td>
                            <td class="px-4 py-2 text-right whitespace-nowrap">
                                <button type="button" wire:click="edit({{ $field->id }})"
                                        class="text-xs text-violet-600 hover:text-violet-800 mr-3">Edit
                                </button>
                                <button type="button" wire:click="delete({{ $field->id }})"
                                        wire:confirm="Delete this field? Existing saved values for it stay in the database but won't display."
                                        class="text-xs text-red-600 hover:text-red-800">Delete
                                </button>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            @endif
        </div>
    </div>
</div>
