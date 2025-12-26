<div class="calendar-week-view">
    <!-- Days of week header -->
    <div class="grid grid-cols-7 border-b">
        @for($i = 0; $i < 7; $i++)
            @php
                $day = $currentDate->copy()->startOfWeek()->addDays($i);
                $isToday = $day->isToday();
            @endphp
            <div class="p-2 text-center {{ $isToday ? 'bg-blue-100 dark:bg-blue-900' : '' }}">
                <div class="font-medium text-gray-700 dark:text-gray-300">{{ $day->format('D') }}</div>
                <div class="text-lg {{ $isToday ? 'font-bold text-blue-600 dark:text-blue-400' : 'text-gray-900 dark:text-gray-100' }}">{{ $day->day }}</div>
            </div>
        @endfor
    </div>

    <!-- Time grid -->
    <div class="grid grid-cols-8">
        <!-- Time labels -->
        <div class="border-r">
            @for($hour = 0; $hour < 24; $hour++)
                <div class="h-16 border-b text-right pr-2 text-sm text-gray-500">{{ $hour == 0 ? '12 AM' : ($hour < 12 ? $hour . ' AM' : ($hour == 12 ? '12 PM' : ($hour - 12) . ' PM')) }}</div>
            @endfor
        </div>

        <!-- Day columns -->
        @for($i = 0; $i < 7; $i++)
            @php
                $day = $currentDate->copy()->startOfWeek()->addDays($i);
                $dayEvents = $events->filter(function($event) use ($day) {
                    return $event->start_date->startOfDay()->eq($day) ||
                           ($event->end_date && $event->end_date->endOfDay()->gte($day) && $event->start_date->lte($day->endOfDay()));
                });
            @endphp

            <div
                @click="$wire.createNoteFromCalendar('{{ $day->format('Y-m-d\\TH:i') }}').then(() => { $dispatch('open-modal', { name: 'create-note-modal' }) })"
                class="border-r relative cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700"
            >
                @foreach($dayEvents as $event)
                    @php
                        $startHour = $event->start_date->hour;
                        $startMinute = $event->start_date->minute;
                        $duration = $event->end_date ? $event->end_date->diffInMinutes($event->start_date) : 60;

                        $top = ($startHour * 60 + $startMinute) * 2; // 2px per minute
                        $height = $duration * 2; // 2px per minute
                    @endphp

                    <div
                        @click.stop="$dispatch('open-modal', { name: 'view-event-modal' }); $wire.selectEvent({{ $event->id }})"
                        class="absolute left-1 right-1 rounded p-1 text-xs cursor-pointer overflow-hidden"
                        style="top: {{ $top }}px; height: {{ $height }}px; background-color: {{ $event->color ?: '#3b82f6' }}; color: white;"
                        title="{{ $event->title }}"
                    >
                        <div class="font-medium truncate">{{ $event->title }}</div>
                        <div class="truncate">{{ $event->start_date->format('g:i A') }}</div>
                    </div>
                @endforeach
            </div>
        @endfor
    </div>
</div>
