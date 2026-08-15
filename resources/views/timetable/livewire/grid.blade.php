{{-- resources/views/timetable/livewire/grid.blade.php --}}

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 space-y-6"
     style="font-family: 'system-ui', -apple-system, sans-serif;">

    {{-- ── PAGE HEADER ── --}}
    <div class="overflow-hidden"
         style="border-radius: 2px; background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); box-shadow: 0 4px 24px rgba(0,0,0,0.15);">
        <div class="h-1 w-full" style="background: linear-gradient(90deg, #7c3aed, #a78bfa, #c4b5fd);"></div>
        <div class="px-7 py-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-white leading-snug"
                    style="letter-spacing: -0.02em; font-family: 'Georgia', serif;">
                    Timetable
                </h1>
                <p class="text-slate-400 mt-2 text-sm">Weekly class schedule</p>
            </div>
            @can('create', \App\Timetable\Models\TimetableEntry::class)
                <x-ui.button variant="secondary" size="md" icon="plus"
                             wire:click="openCreate('monday', {{ $this->timeSlots->first()?->id ?? 'null' }})">
                    Add Period
                </x-ui.button>
            @endcan
        </div>
    </div>

    {{-- ── FILTERS ── --}}
    <div class="bg-white dark:bg-slate-900 overflow-hidden"
         style="border-radius: 2px; border: 1px solid rgba(0,0,0,0.06); box-shadow: 0 1px 6px rgba(0,0,0,0.04);">
        <div class="p-5 grid sm:grid-cols-2 gap-4">
            <div>
                <label
                    class="block text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2"
                    style="letter-spacing: 0.08em;">Academic Period</label>
                <select wire:model.live="academicPeriodId"
                        class="w-full px-4 py-2.5 text-sm border border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500"
                        style="border-radius: 2px;">
                    @foreach($this->academicPeriods as $period)
                        <option value="{{ $period->id }}">
                            {{ $period->getDisplayName() }} — {{ $period->academic_year }}
                            @if($period->is_current)
                                (Current)
                            @endif
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label
                    class="block text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2"
                    style="letter-spacing: 0.08em;">Class</label>
                <select wire:model.live="academicLevelFilter"
                        class="w-full px-4 py-2.5 text-sm border border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500"
                        style="border-radius: 2px;">
                    <option value="">All Classes</option>
                    @foreach($this->academicLevels as $level)
                        <option value="{{ $level->id }}">{{ $level->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label
                    class="block text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2"
                    style="letter-spacing: 0.08em;">Teacher</label>
                <select wire:model.live="teacherFilter"
                        class="w-full px-4 py-2.5 text-sm border border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-amber-500/20 focus:border-amber-500"
                        style="border-radius: 2px;">
                    <option value="">All Teachers</option>
                    @foreach($this->teachers as $teacher)
                        <option value="{{ $teacher->id }}">{{ $teacher->user->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    @php
        $dayLabels = ['monday' => 'Mon', 'tuesday' => 'Tue', 'wednesday' => 'Wed', 'thursday' => 'Thu', 'friday' => 'Fri'];
        $days = array_keys($dayLabels);
    @endphp

    {{-- ═══════════════════════════════════════════════
         DESKTOP GRID (md and up)
    ═══════════════════════════════════════════════ --}}
    <div class="hidden md:block bg-white dark:bg-slate-900 overflow-hidden"
         style="border-radius: 2px; border: 1px solid rgba(0,0,0,0.06); box-shadow: 0 1px 6px rgba(0,0,0,0.04);">
        <div class="overflow-x-auto">
            <table class="w-full text-sm border-collapse">
                <thead>
                <tr class="bg-slate-50 dark:bg-slate-800/60">
                    <th class="px-3 py-3 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider w-32">
                        Period
                    </th>
                    @foreach($dayLabels as $day => $label)
                        <th class="px-3 py-3 text-left text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                            {{ $label }}
                        </th>
                    @endforeach
                </tr>
                </thead>
                <tbody>
                @forelse($this->timeSlots as $slot)
                    <tr class="border-t border-slate-100 dark:border-slate-800 {{ $slot->is_break ? 'bg-slate-50/50 dark:bg-slate-800/30' : '' }}">
                        <td class="px-3 py-3 align-top">
                            <div class="font-semibold text-slate-900 dark:text-white text-xs">{{ $slot->label }}</div>
                            <div class="text-slate-500 dark:text-slate-400 text-[11px] font-mono">
                                {{ \Carbon\Carbon::parse($slot->starts_at)->format('g:i A') }}
                                –{{ \Carbon\Carbon::parse($slot->ends_at)->format('g:i A') }}
                            </div>
                        </td>
                        @foreach($days as $day)
                            @php $entry = $this->entries->get($day)?->get($slot->id)?->first(); @endphp
                            <td class="px-2 py-2 align-top">
                                @if($slot->is_break)
                                    <div class="text-center text-[11px] text-slate-400 italic py-2">Break</div>
                                @elseif($entry)
                                    <button
                                        @can('update', $entry) wire:click="openEdit({{ $entry->id }})" @endcan
                                    class="w-full text-left px-3 py-2 hover:opacity-90 transition-opacity"
                                        style="border-radius: 2px; background: linear-gradient(135deg, #eef2ff, #e0e7ff);"
                                    >
                                        <div
                                            class="font-semibold text-indigo-900 dark:text-indigo-200 text-xs truncate">{{ $entry->academicSubject->name }}</div>
                                        <div
                                            class="text-indigo-700/70 dark:text-indigo-300/70 text-[11px] truncate">{{ $entry->teacher->user->name }}</div>
                                        <div
                                            class="text-indigo-700/50 dark:text-indigo-300/50 text-[10px] truncate">{{ $entry->room->name }}</div>
                                    </button>
                                @else
                                    @can('create', \App\Timetable\Models\TimetableEntry::class)
                                        <button
                                            wire:click="openCreate('{{ $day }}', {{ $slot->id }})"
                                            class="w-full h-16 flex items-center justify-center text-slate-300 dark:text-slate-700 hover:text-slate-400 dark:hover:text-slate-600 border border-dashed border-slate-200 dark:border-slate-800 transition-colors"
                                            style="border-radius: 2px;"
                                        >
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M12 4v16m8-8H4"/>
                                            </svg>
                                        </button>
                                    @endcan
                                @endif
                            </td>
                        @endforeach
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-3 py-8 text-center text-slate-400 text-sm">No time slots configured
                            yet.
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════
         MOBILE: DAY TABS + STACKED CARDS (below md)
    ═══════════════════════════════════════════════ --}}
    <div class="md:hidden" x-data="{ activeDay: 'monday' }">
        {{-- Day tab strip --}}
        <div class="flex gap-1 bg-white dark:bg-slate-900 p-1 overflow-x-auto"
             style="border-radius: 2px; border: 1px solid rgba(0,0,0,0.06);">
            @foreach($dayLabels as $day => $label)
                <button
                    @click="activeDay = '{{ $day }}'"
                    :class="activeDay === '{{ $day }}' ? 'bg-slate-900 dark:bg-slate-700 text-white' : 'text-slate-500 dark:text-slate-400'"
                    class="flex-1 min-w-[56px] px-3 py-2.5 text-xs font-semibold transition-colors"
                    style="border-radius: 2px;"
                >
                    {{ $label }}
                </button>
            @endforeach
        </div>

        {{-- Stacked period cards, one <div> per day, toggled via x-show --}}
        @foreach($days as $day)
            <div x-show="activeDay === '{{ $day }}'" x-cloak class="mt-3 space-y-2">
                @forelse($this->timeSlots as $slot)
                    @php $entry = $this->entries->get($day)?->get($slot->id)?->first(); @endphp

                    @if($slot->is_break)
                        <div class="px-4 py-3 text-center text-xs text-slate-400 italic bg-slate-50 dark:bg-slate-900"
                             style="border-radius: 2px; border: 1px solid rgba(0,0,0,0.04);">
                            {{ $slot->label }} — Break
                        </div>
                    @elseif($entry)
                        <button
                            @can('update', $entry) wire:click="openEdit({{ $entry->id }})" @endcan
                        class="w-full text-left bg-white dark:bg-slate-900 p-4 flex items-start gap-3"
                            style="border-radius: 2px; border: 1px solid rgba(0,0,0,0.06); box-shadow: 0 1px 6px rgba(0,0,0,0.04);"
                        >
                            <div class="w-14 flex-shrink-0 text-center">
                                <div class="text-[10px] font-semibold text-slate-400 uppercase">{{ $slot->label }}</div>
                                <div
                                    class="text-[10px] text-slate-400 font-mono">{{ \Carbon\Carbon::parse($slot->starts_at)->format('g:i A') }}</div>
                            </div>
                            <div class="flex-1 min-w-0">
                                <div
                                    class="font-semibold text-slate-900 dark:text-white text-sm truncate">{{ $entry->academicSubject->name }}</div>
                                <div
                                    class="text-slate-500 dark:text-slate-400 text-xs truncate">{{ $entry->teacher->user->name }}
                                    · {{ $entry->room->name }}</div>
                            </div>
                        </button>
                    @else
                        @can('create', \App\Timetable\Models\TimetableEntry::class)
                            <button
                                wire:click="openCreate('{{ $day }}', {{ $slot->id }})"
                                class="w-full flex items-center gap-3 px-4 py-3 border border-dashed border-slate-200 dark:border-slate-800 text-slate-400 dark:text-slate-600"
                                style="border-radius: 2px;"
                            >
                                <div
                                    class="w-14 flex-shrink-0 text-center text-[10px] font-semibold uppercase">{{ $slot->label }}</div>
                                <div class="text-xs">Add period</div>
                            </button>
                        @endcan
                    @endif
                @empty
                    <p class="text-center text-slate-400 text-sm py-8">No time slots configured yet.</p>
                @endforelse
            </div>
        @endforeach
    </div>

    {{-- ── CREATE/EDIT MODAL ── --}}
    @if($showModal)
        <div class="fixed inset-0 z-50 flex items-end sm:items-center justify-center bg-black/50 p-0 sm:p-4"
             wire:click.self="$set('showModal', false)">
            <div class="w-full sm:max-w-lg bg-white dark:bg-slate-900 max-h-[90vh] overflow-y-auto"
                 style="border-radius: 2px; box-shadow: 0 8px 40px rgba(0,0,0,0.25);">
                <div
                    class="px-5 py-4 border-b border-slate-100 dark:border-slate-800 flex items-center justify-between">
                    <h3 class="font-bold text-slate-900 dark:text-white text-sm">
                        {{ $editingEntryId ? 'Edit Period' : 'Add Period' }}
                    </h3>
                    <button wire:click="$set('showModal', false)" class="text-slate-400 hover:text-slate-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <div class="p-5 space-y-4">
                    @if(!empty($conflictWarnings))
                        <div
                            class="px-4 py-3 text-xs text-amber-800 bg-amber-50 border border-amber-200 dark:text-amber-200 dark:bg-amber-900/30 dark:border-amber-800"
                            style="border-radius: 2px;">
                            <p class="font-semibold mb-1">Scheduling conflict detected:</p>
                            <ul class="list-disc list-inside space-y-0.5">
                                @foreach($conflictWarnings as $type)
                                    <li>
                                        @if($type === 'teacher')
                                            This teacher is already booked in this slot.
                                        @elseif($type === 'room')
                                            This room is already booked in this slot.
                                        @else
                                            This class already has a subject in this slot.
                                        @endif
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div>
                        <label
                            class="block text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">Class</label>
                        <select wire:model="form.academic_level_id"
                                class="w-full px-4 py-2.5 text-sm border border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-white"
                                style="border-radius: 2px;">
                            <option value="">Select class</option>
                            @foreach($this->academicLevels as $level)
                                <option value="{{ $level->id }}">{{ $level->name }}</option>
                            @endforeach
                        </select>
                        @error('form.academic_level_id') <p
                            class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label
                            class="block text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">Subject</label>
                        <select wire:model="form.academic_subject_id"
                                class="w-full px-4 py-2.5 text-sm border border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-white"
                                style="border-radius: 2px;">
                            <option value="">Select subject</option>
                            @foreach($this->academicSubjects as $subject)
                                <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                            @endforeach
                        </select>
                        @error('form.academic_subject_id') <p
                            class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div>
                        <label
                            class="block text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">Teacher</label>
                        <select wire:model="form.teacher_id"
                                class="w-full px-4 py-2.5 text-sm border border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-white"
                                style="border-radius: 2px;">
                            <option value="">Select teacher</option>
                            @foreach($this->teachers as $teacher)
                                <option value="{{ $teacher->id }}">{{ $teacher->user->name }}</option>
                            @endforeach
                        </select>
                        @error('form.teacher_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label
                                class="block text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">Room</label>
                            <select wire:model="form.room_id"
                                    class="w-full px-4 py-2.5 text-sm border border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-white"
                                    style="border-radius: 2px;">
                                <option value="">Select</option>
                                @foreach($this->rooms as $room)
                                    <option value="{{ $room->id }}">{{ $room->name }}</option>
                                @endforeach
                            </select>
                            @error('form.room_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label
                                class="block text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">Day</label>
                            <select wire:model="form.day_of_week"
                                    class="w-full px-4 py-2.5 text-sm border border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-white"
                                    style="border-radius: 2px;">
                                @foreach($dayLabels as $day => $label)
                                    <option value="{{ $day }}">{{ ucfirst($day) }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div>
                        <label
                            class="block text-xs font-medium text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">Time
                            Slot</label>
                        <select wire:model="form.time_slot_id"
                                class="w-full px-4 py-2.5 text-sm border border-slate-200 dark:border-slate-700 dark:bg-slate-800 dark:text-white"
                                style="border-radius: 2px;">
                            <option value="">Select</option>
                            @foreach($this->timeSlots as $slot)
                                <option value="{{ $slot->id }}">{{ $slot->label }}
                                    ({{ \Carbon\Carbon::parse($slot->starts_at)->format('g:i A') }}
                                    –{{ \Carbon\Carbon::parse($slot->ends_at)->format('g:i A') }})
                                </option>
                            @endforeach
                        </select>
                        @error('form.time_slot_id') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div
                    class="px-5 py-4 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between gap-3">
                    @if($editingEntryId)
                        <button wire:click="delete({{ $editingEntryId }})"
                                wire:confirm="Remove this period from the timetable?"
                                class="px-4 py-2.5 text-sm font-semibold text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20"
                                style="border-radius: 2px;">
                            Delete
                        </button>
                    @else
                        <span></span>
                    @endif
                    <div class="flex gap-2">
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
        </div>
    @endif

</div>

