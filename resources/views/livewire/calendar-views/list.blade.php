<div class="calendar-list-view">
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
            <thead class="bg-gray-50 dark:bg-gray-700">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Event</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Date</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Time</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Type</th>
                </tr>
            </thead>
            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                @forelse($events as $event)
                    <tr
                        @click="$dispatch('open-modal', { name: 'view-event-modal' }); $wire.selectEvent({{ $event->id }})"
                        class="hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer"
                    >
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div
                                    class="w-3 h-3 rounded-full mr-3"
                                    style="background-color: {{ $event->color ?: '#3b82f6' }}"
                                ></div>
                                <div class="text-sm font-medium text-gray-900 dark:text-white">{{ $event->title }}</div>
                            </div>
                            <div class="text-sm text-gray-500 dark:text-gray-400 mt-1">{{ Str::limit(strip_tags($event->description), 100) }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                            {{ $event->start_date->format('M j, Y') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                            {{ $event->all_day ? 'All Day' : $event->start_date->format('g:i A') }}
                            @if($event->end_date && !$event->all_day)
                                - {{ $event->end_date->format('g:i A') }}
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                            {{ class_basename($event->event_type) }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-6 py-4 text-center text-sm text-gray-500 dark:text-gray-400">
                            No events found
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4 text-center">
        <button
            @click="$wire.createNoteFromCalendar().then(() => { $dispatch('open-modal', { name: 'create-note-modal' }) })"
            class="px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600"
        >
            Create New Note
        </button>
    </div>
</div>
