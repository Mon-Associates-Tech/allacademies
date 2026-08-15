{{-- resources/views/timetable/livewire/rooms.blade.php --}}
<div>

    <div class="overflow-hidden mb-6"
         style="border-radius: 2px; background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); box-shadow: 0 4px 24px rgba(0,0,0,0.15);">
        <div class="h-1 w-full" style="background: linear-gradient(90deg, #7c3aed, #a78bfa, #c4b5fd);"></div>
        <div class="px-7 py-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-white leading-snug" style="letter-spacing: -0.02em; font-family: 'Georgia', serif;">
                    Rooms
                </h1>
                <p class="text-slate-400 mt-2 text-sm">Manage classrooms, labs, and halls used in scheduling</p>
            </div>
            @can('create', \App\Timetable\Models\Room::class)
                <x-ui.button variant="secondary" size="md" icon="plus" wire:click="openCreate">
                    Add Room
                </x-ui.button>
            @endcan
        </div>
    </div>

    <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-4">
        @forelse($this->rooms as $room)
            <div class="bg-white dark:bg-slate-900 p-5"
                 style="border-radius: 2px; border: 1px solid rgba(0,0,0,0.06); box-shadow: 0 1px 6px rgba(0,0,0,0.04);">
                <div class="flex items-start justify-between gap-3 mb-3">
                    <div>
                        <h3 class="font-bold text-slate-900 dark:text-white text-sm">{{ $room->name }}</h3>
                        <span class="inline-block mt-1 text-[10px] font-semibold uppercase tracking-wider px-2 py-0.5 text-slate-600 bg-slate-100 dark:text-slate-300 dark:bg-slate-800"
                              style="border-radius: 2px;">
                            {{ ucfirst($room->type) }}
                        </span>
                    </div>
                    @if(!$room->is_active)
                        <span class="text-[10px] font-semibold text-red-700 bg-red-50 dark:text-red-300 dark:bg-red-900/30 px-2 py-0.5"
                              style="border-radius: 2px;">Inactive</span>
                    @endif
                </div>

                @if($room->capacity)
                    <p class="text-xs text-slate-500 dark:text-slate-400 mb-4">Capacity: {{ $room->capacity }}</p>
                @endif

                @can('update', $room)
                    <div class="flex gap-2 pt-3 border-t border-slate-100 dark:border-slate-800">
                        <button wire:click="openEdit({{ $room->id }})"
                                class="text-xs font-semibold text-slate-600 dark:text-slate-300 hover:text-slate-900 dark:hover:text-white">
                            Edit
                        </button>
                        <button wire:click="delete({{ $room->id }})"
                                wire:confirm="Delete this room?"
                                class="text-xs font-semibold text-red-600 hover:text-red-700">
                            Delete
                        </button>
                    </div>
                @endcan
            </div>
        @empty
            <div class="col-span-full text-center py-16 bg-white dark:bg-slate-900"
                 style="border-radius: 2px; border: 1px solid rgba(0,0,0,0.06);">
                <p class="text-slate-400 text-sm">No rooms configured yet.</p>
            </div>
        @endforelse
    </div>

    @if($showModal)
        <div class="fixed inset-0 z-50 flex items-end sm:items-center justify-center bg-black/50 p-0 sm:p-4"
             wire:click.self="$set('showModal', false)">
            <div class="w-full sm:max-w-md bg-white dark:bg-slate-900"
                 style="border-radius: 2px; box-shadow: 0 8px 40px rgba(0,0,0,0.25);">
                <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
                    <h3 class="font-bold text-slate-900 dark:text-white text-sm">
                        {{ $editingRoomId ? 'Edit Room' : 'Add Room' }}
                    </h3>
                    <button wire:click="$set('showModal', false)" class="text-slate-400 hover:text-slate-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <div class="p-5 space-y-4">
                    <div>
                        <label class="block text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">Name</label>
                        <input type="text" wire:model="form.name"
                               class="w-full px-4 py-2.5 text-sm border border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-white"
                               style="border-radius: 2px;" placeholder="e.g. Room 204">
                        @error('form.name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">Type</label>
                            <select wire:model="form.type"
                                    class="w-full px-4 py-2.5 text-sm border border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-white"
                                    style="border-radius: 2px;">
                                <option value="classroom">Classroom</option>
                                <option value="lab">Lab</option>
                                <option value="hall">Hall</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">Capacity</label>
                            <input type="number" wire:model="form.capacity" min="1"
                                   class="w-full px-4 py-2.5 text-sm border border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-white"
                                   style="border-radius: 2px;">
                        </div>
                    </div>

                    <label class="flex items-center gap-2 text-sm text-slate-700 dark:text-slate-300">
                        <input type="checkbox" wire:model="form.is_active">
                        Active
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
