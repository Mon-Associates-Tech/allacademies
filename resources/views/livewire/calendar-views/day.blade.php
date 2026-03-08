<div class="calendar-day-view">
    <div class="text-center p-2 sm:p-4 border-b">
        <h3 class="text-base sm:text-xl font-bold text-gray-800 dark:text-white">{{ $currentDate->format('l, F j, Y') }}</h3>
    </div>

    <!-- Scrollable container for the time grid -->
    <div class="overflow-x-auto">
        <div class="min-w-[320px]">
            <div class="flex">
                <!-- Time labels -->
                <div class="border-r flex-shrink-0 w-16 sm:w-20">
                    @for($hour = 0; $hour < 24; $hour++)
                        <div class="h-12 sm:h-16 border-b text-right pr-1 sm:pr-2 text-xs sm:text-sm text-gray-500">{{ $hour == 0 ? '12 AM' : ($hour < 12 ? $hour . ' AM' : ($hour == 12 ? '12 PM' : ($hour - 12) . ' PM')) }}</div>
                    @endfor
                </div>

                <!-- Day column -->
                <div
                    @click="$wire.createNoteFromCalendar('{{ $currentDate->format('Y-m-d\\TH:i') }}').then(() => { $dispatch('open-modal', { name: 'create-note-modal' }) })"
                    class="relative cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-700 flex-1"
                >
                    @php
                        $dayEvents = $events->filter(function($event) {
                            return $event->start_date->startOfDay()->eq($currentDate) ||
                                   ($event->end_date && $event->end_date->endOfDay()->gte($currentDate) && $event->start_date->lte($currentDate->endOfDay()));
                        });
                    @endphp

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
                            class="absolute left-1 right-1 sm:left-2 sm:right-2 rounded p-1 sm:p-2 cursor-pointer overflow-hidden"
                            style="top: {{ $top }}px; height: {{ $height }}px; background-color: {{ $event->color ?: '#3b82f6' }}; color: white;"
                            title="{{ $event->title }}"
                        >
                            <div class="font-medium text-sm sm:text-base truncate">{{ $event->title }}</div>
                            <div class="text-xs sm:text-sm">{{ $event->start_date->format('g:i A') }} - {{ $event->end_date ? $event->end_date->format('g:i A') : '' }}</div>
                            <div class="text-xs mt-1 truncate hidden sm:block">{{ Str::limit(strip_tags($event->description), 50) }}</div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>
