<div class="calendar-month-view">
    <!-- Days of week header -->
    <div class="grid grid-cols-7 border-b">
        @for($i = 0; $i < 7; $i++)
            <div class="p-2 text-center font-medium text-gray-700 dark:text-gray-300">
                {{ \Carbon\Carbon::now()->startOfWeek()->addDays($i)->format('D') }}
            </div>
        @endfor
    </div>

    <!-- Calendar grid -->
    <div class="grid grid-cols-7">
        @php
            $startDate = $currentDate->copy()->startOfMonth()->startOfWeek();
            $endDate = $currentDate->copy()->endOfMonth()->endOfWeek();
            $current = $startDate->copy();
        @endphp

        @while($current->lte($endDate))
            @php
                $isCurrentMonth = $current->month === $currentDate->month;
                $isToday = $current->isToday();
                $dayEvents = $events->filter(function($event) use ($current) {
                    return $event->start_date->startOfDay()->lte($current->endOfDay()) &&
                           ($event->end_date ? $event->end_date->endOfDay()->gte($current->startOfDay()) : $event->start_date->endOfDay()->gte($current->startOfDay()));
                });
            @endphp

            <div
                @click="$wire.createNoteFromCalendar('{{ $current->format('Y-m-d\\TH:i') }}').then(() => { $dispatch('open-modal', { name: 'create-note-modal' }) })"
                class="border min-h-32 {{ $isCurrentMonth ? 'bg-white dark:bg-gray-800' : 'bg-gray-50 dark:bg-gray-900' }} cursor-pointer hover:bg-gray-100 dark:hover:bg-gray-700"
            >
                <div class="p-1 text-right">
                    <span class="inline-block w-6 h-6 text-center leading-6 rounded-full {{ $isToday ? 'bg-blue-500 text-white' : '' }}">
                        {{ $current->day }}
                    </span>
                </div>

                <div class="p-1 space-y-1 max-h-24 overflow-y-auto">
                    @foreach($dayEvents as $event)
                        <div
                            @click.stop="$dispatch('open-modal', { name: 'view-event-modal' }); $wire.selectEvent({{ $event->id }})"
                            class="text-xs p-1 rounded cursor-pointer truncate"
                            style="background-color: {{ $event->color ?: '#3b82f6' }}; color: white;"
                            title="{{ $event->title }}"
                        >
                            {{ $event->title }}
                        </div>
                    @endforeach
                </div>
            </div>

            @php $current->addDay(); @endphp
        @endwhile
    </div>
</div>
