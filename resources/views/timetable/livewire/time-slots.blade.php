{{-- resources/views/timetable/livewire/time-slots.blade.php --}}
<div>

    <div class="overflow-hidden mb-6"
         style="border-radius: 2px; background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); box-shadow: 0 4px 24px rgba(0,0,0,0.15);">
        <div class="h-1 w-full" style="background: linear-gradient(90deg, #7c3aed, #a78bfa, #c4b5fd);"></div>
        <div class="px-7 py-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-white leading-snug" style="letter-spacing: -0.02em; font-family: 'Georgia', serif;">
                    Time Slots
                </h1>
                <p class="text-slate-400 mt-2 text-sm">Define the daily periods used across the timetable</p>
            </div>
            @can('create', \App\Timetable\Models\TimeSlot::class)
                <x-ui.button variant="secondary" size="md" icon="plus" wire:click="openCreate">
                    Add Time Slot
                </x-ui.button>
            @endcan
        </div>
    </div>

    <div class="bg-white dark:bg-slate-900 overflow-hidden"
         style="border-radius: 2px; border: 1px solid rgba(0,0,0,0.06); box-shadow: 0 1px 6px rgba(0,0,0,0.04);">
        @forelse($this->timeSlots as $slot)
            <div class="flex items-center justify-between gap-4 px-5 py-4 {{ !$loop->last ? 'border-b border-slate-100 dark:border-slate-800' : '' }} {{ $slot->is_break ? 'bg-slate-50/50 dark:bg-slate-800/30' : '' }}">
                <div class="flex items-center gap-4">
                    <span class="text-xs font-mono text-slate-400 w-6">{{ $slot->order }}</span>
                    <div>
                        <div class="font-semibold text-slate-900 dark:text-white text-sm">
                            {{ $slot->label }}
                            @if($slot->is_break)
                                <span class="ml-2 text-[10px] font-semibold uppercase text-slate-500 bg-slate-100 dark:bg-slate-800 px-2 py-0.5" style="border-radius: 2px;">Break</span>
                            @endif
                        </div>
                        <div class="text-xs text-slate-500 dark:text-slate-400 font-mono">
                            {{ \Carbon\Carbon::parse($slot->starts_at)->format('g:i A') }} – {{ \Carbon\Carbon::parse($slot->ends_at)->format('g:i A') }}
                        </div>
                    </div>
                </div>

                @can('update', $slot)
                    <div class="flex gap-3">
                        <button wire:click="openEdit({{ $slot->id }})" class="text-xs font-semibold text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white">Edit</button>
                        <button wire:click="delete({{ $slot->id }})" wire:confirm="Delete this time slot?" class="text-xs font-semibold text-red-600 hover:text-red-700">Delete</button>
                    </div>
                @endcan
            </div>
        @empty
            <p class="text-center py-16 text-slate-400 text-sm">No time slots configured yet.</p>
        @endforelse
    </div>

    @if($showModal)
        <div class="fixed inset-0 z-50 flex items-end sm:items-center justify-center bg-black/50 p-0 sm:p-4"
             wire:click.self="$set('showModal', false)">
            <div class="w-full sm:max-w-md bg-white dark:bg-slate-900"
                 style="border-radius: 2px; box-shadow: 0 8px 40px rgba(0,0,0,0.25);">
                <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
                    <h3 class="font-bold text-slate-900 dark:text-white text-sm">
                        {{ $editingSlotId ? 'Edit Time Slot' : 'Add Time Slot' }}
                    </h3>
                    <button wire:click="$set('showModal', false)" class="text-slate-400 hover:text-slate-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <div class="p-5 space-y-4">
                    <div>
                        <label class="block text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">Label</label>
                        <input type="text" wire:model="form.label"
                               class="w-full px-4 py-2.5 text-sm border border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-white"
                               style="border-radius: 2px;" placeholder="e.g. Period 1">
                        @error('form.label') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">Starts</label>
                            <input type="time" wire:model="form.starts_at"
                                   class="w-full px-4 py-2.5 text-sm border border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-white"
                                   style="border-radius: 2px;">
                            @error('form.starts_at') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">Ends</label>
                            <input type="time" wire:model="form.ends_at"
                                   class="w-full px-4 py-2.5 text-sm border border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-white"
                                   style="border-radius: 2px;">
                            @error('form.ends_at') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">Display Order</label>
                        <input type="number" wire:model="form.order" min="0"
                               class="w-full px-4 py-2.5 text-sm border border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-white"
                               style="border-radius: 2px;">
                    </div>

                    <label class="flex items-center gap-2 text-sm text-slate-700 dark:text-slate-300">
                        <input type="checkbox" wire:model="form.is_break">
                        This is a break/lunch period (excluded from subject scheduling)
                    </label>
                </div>

                <div class="px-5 py-4 border-t border-slate-100 dark:border-slate-800 flex justify-end gap-2">
                    <button wire:click="$set('showModal', false)"
                            class="px-4 py-2.5 text-sm font-semibold text-slate-600 dark:text-slate-300"
                            style="border-radius: 2px;">
                        Cancel
                    </button>
                    <button wire:click="save"
                            class="px-5 py-2.5 text-sm font-semibold text-white"
                            style="border-radius: 2px; background: linear-gradient(135deg, #1e293b, #334155);">
                        Save
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
