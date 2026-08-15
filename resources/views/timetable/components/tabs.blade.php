{{-- resources/views/timetable/components/tabs.blade.php --}}
@props(['active'])
<div class="flex gap-1 mb-6 bg-white dark:bg-slate-900 p-1 overflow-x-auto"
     style="border-radius: 2px; border: 1px solid rgba(0,0,0,0.06);">
    @foreach([
        'timetable' => ['label' => 'Timetable', 'route' => 'timetable.index'],
        'rooms' => ['label' => 'Rooms', 'route' => 'timetable.rooms'],
        'time-slots' => ['label' => 'Time Slots', 'route' => 'timetable.time-slots'],
    ] as $key => $tab)
        <a href="{{ route($tab['route']) }}"
           class="px-4 py-2 text-xs font-semibold whitespace-nowrap transition-colors {{ $active === $key ? 'bg-slate-900 dark:bg-slate-700 text-white' : 'text-slate-500 dark:text-slate-400 hover:text-slate-700' }}"
           style="border-radius: 2px;">
            {{ $tab['label'] }}
        </a>
    @endforeach
</div>
