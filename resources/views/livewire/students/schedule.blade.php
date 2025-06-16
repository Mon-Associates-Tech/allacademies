<div x-data="{
    currentView: 'calendar'
}">
    <!-- File: resources/views/livewire/students/schedule.blade.php -->

    <div class="mb-4 flex justify-between items-center">
        <div>
            <label for="statusFilter" class="block text-sm font-medium text-gray-700">Filter by Status</label>
            <select id="statusFilter" wire:model.live="selectedStatus"
                    wire:change="$refresh"
                    class="mt-1 block pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md">
                <option value="">All</option>
                <option value="completed">Completed</option>
                <option value="in_progress">In Progress</option>
                <option value="needs_grading">Needs Grading</option>
            </select>
        </div>

        <!-- Optional: Date Navigation -->
        <div class="flex space-x-2">
            <button @click="$wire.previousPeriod()" class="bg-gray-200 px-3 py-2 rounded">Previous</button>
            <button @click="$wire.nextPeriod()" class="bg-gray-200 px-3 py-2 rounded">Next</button>
        </div>
    </div>
    <!-- Tabs -->
    <div class="mb-4 border-b border-gray-200">
        <ul class="flex space-x-6" role="tablist">
            <li role="presentation">
                <button type="button"
                        @click="currentView = 'calendar'; $wire.set('selectedEvent', null)"
                        class="py-4 px-1 font-medium text-sm border-b-2"
                        :class="{
                            'border-indigo-500 text-indigo-600': currentView === 'calendar',
                            'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': currentView !== 'calendar'
                        }">
                    Calendar View
                </button>
            </li>
            <li role="presentation">
                <button type="button"
                        @click="currentView = 'list'; $wire.set('selectedEvent', null)"
                        class="py-4 px-1 font-medium text-sm border-b-2"
                        :class="{
                            'border-indigo-500 text-indigo-600': currentView === 'list',
                            'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': currentView !== 'list'
                        }">
                    List View
                </button>
            </li>
            <li role="presentation">
                <button type="button"
                        @click="currentView = 'grid'; $wire.set('selectedEvent', null)"
                        class="py-4 px-1 font-medium text-sm border-b-2"
                        :class="{
                            'border-indigo-500 text-indigo-600': currentView === 'grid',
                            'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': currentView !== 'grid'
                        }">
                    Grid View
                </button>
            </li>
        </ul>
    </div>

    <!-- Calendar View -->
    <div x-show="currentView === 'calendar'" class="mt-4 bg-white p-4 rounded-lg shadow">
        <div id="calendar"></div>
    </div>

<!-- List View -->
<div x-show="currentView === 'list'" class="mt-4">
    <div class="overflow-x-auto">
        <div class="min-w-full inline-block align-middle">
            <div class="overflow-hidden border-b border-gray-200 dark:border-gray-700 shadow-sm sm:rounded-lg">
                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                    <thead class="bg-gray-50 dark:bg-gray-700">
                        <tr>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Assessment
                            </th>
                            <th scope="col" class="hidden md:table-cell px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Date & Time
                            </th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Status
                            </th>
                            <th scope="col" class="hidden md:table-cell px-4 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                Score
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                        <template x-for="event in {{ json_encode($assessments) }}" :key="event.id">
                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700/50 cursor-pointer" @click="$wire.openEventDetails(event)">
                                <td class="px-4 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900 dark:text-gray-100" x-text="event.title"></div>
                                    <div class="md:hidden text-xs text-gray-500 dark:text-gray-400 mt-1">
                                        <span x-text="event.formatted_date"></span>
                                        <span x-text="event.formatted_time"></span>
                                    </div>
                                </td>
                                <td class="hidden md:table-cell px-4 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900 dark:text-gray-100" x-text="event.formatted_date"></div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400" x-text="event.formatted_time"></div>
                                    <div class="text-xs text-gray-400" x-text="event.relative_date"></div>
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap">
                                    <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full"
                                        :class="{
                                            'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200': event.status === 'completed',
                                            'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200': event.status === 'in_progress',
                                            'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200': event.status === 'needs_grading',
                                            'bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200': !event.status
                                        }"
                                        x-text="event.status ? event.status.replace('_', ' ') : 'pending'">
                                    </span>
                                </td>
                                <td class="hidden md:table-cell px-4 py-4 whitespace-nowrap text-sm">
                                    <span x-show="event.status === 'completed'"
                                        :class="{'text-green-600 dark:text-green-400': event.score >= 70, 'text-red-600 dark:text-red-400': event.score < 70}"
                                        x-text="event.score + '%'">
                                    </span>
                                    <span x-show="event.status === 'needs_grading'" class="text-yellow-600 dark:text-yellow-400">
                                        Pending
                                    </span>
                                    <span x-show="event.status === 'in_progress'" class="text-gray-400">
                                        -
                                    </span>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

    <!-- Grid View -->
    <div x-show="currentView === 'grid'" class="mt-4">
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            <template x-for="event in {{ json_encode($assessments) }}" :key="event.id">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm hover:shadow-md transition-all duration-200 border border-gray-200 dark:border-gray-700 overflow-hidden cursor-pointer group"
                     @click="$wire.openEventDetails(event)">
                    <div class="p-4">
                        <!-- Title with subject icon -->
                        <div class="flex items-start space-x-3 mb-3">
                            <div class="rounded-full p-2 bg-indigo-100 dark:bg-indigo-900/50">
                                <svg class="w-5 h-5 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                            </div>
                            <h4 x-text="event.title" class="font-medium text-gray-900 dark:text-gray-100 group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors"></h4>
                        </div>

                        <!-- Status and Score -->
                        <div class="flex items-center justify-between mb-3">
                        <span x-text="event.status"
                              class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium capitalize"
                              :class="{
                                  'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200': event.status === 'completed',
                                  'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200': event.status === 'in_progress',
                                  'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200': event.status === 'needs_grading'
                              }">
                        </span>
                            <span x-show="event.status === 'completed'"
                                  x-text="event.percentage_score + '%'"
                                  :class="{
                                  'text-green-600 dark:text-green-400': event.percentage_score >= 70,
                                  'text-red-600 dark:text-red-400': event.percentage_score < 70
                              }"
                                  class="text-sm font-semibold">
                        </span>
                        </div>

                        <!-- Date and Time -->
                        <div class="text-sm text-gray-500 dark:text-gray-400 flex items-center space-x-2">
                            <svg class="w-4 h-4 opacity-70" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <div>
                                <span x-text="event.formatted_date"></span>
                                <span x-text="event.formatted_time"></span>
                                <span class="text-xs opacity-75" x-text="'(' + event.relative_date + ')'"></span>
                            </div>
                        </div>
                    </div>
                </div>
            </template>
        </div>
    </div>

    <!-- Modal -->
    <!-- Modal -->
    <div x-show="$wire.selectedEvent !== null"
         x-cloak
         class="fixed inset-0 z-50 overflow-auto bg-black bg-opacity-75 flex items-center justify-center"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-300"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">

        <div class="bg-white w-full max-w-2xl mx-auto rounded-xl ring-slate-600 inset-3 ring-4 shadow-lg z-50 overflow-y-auto"
             x-data="{ questions: [] }"
             x-init="questions = $wire.selectedEvent && $wire.selectedEvent.questions ? $wire.selectedEvent.questions : []">

            <!-- Modal content wrapper with transition -->
            <div class="bg-white w-full max-w-2xl mx-auto rounded shadow-lg z-50 overflow-y-auto"
                 x-show="$wire.selectedEvent !== null"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="transform opacity-0 scale-95"
                 x-transition:enter-end="transform opacity-100 scale-100"
                 x-transition:leave="transition ease-in duration-300"
                 x-transition:leave-start="transform opacity-100 scale-100"
                 x-transition:leave-end="transform opacity-0 scale-95">

                <header class="px-4 py-4 border-b border-b-gray-300 flex justify-between items-center">
                    <h2 class="text-lg font-bold" x-text="$wire.selectedEvent?.title"></h2>
                    <p class="text-sm text-gray-500">
                        <span x-text="$wire.selectedEvent?.formatted_date"></span>,
                        <span x-text="$wire.selectedEvent?.formatted_time"></span>
                        (<span x-text="$wire.selectedEvent?.relative_date"></span>)
                    </p>

                    <button @click="$wire.set('selectedEvent', null)"
                            class="text-gray-500 rounded-lg bg-white border border-gray-200 p-1 hover:text-gray-800"
                            x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0 transform scale-90"
                            x-transition:enter-end="opacity-100 transform scale-100">
                        <svg class="w-6 h-6 opacity-75" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M18 6L6 18" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M6 6L18 18" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </button>
                </header>
            <main class="p-4">
                <div class="space-y-4">
                    <div class="flex justify-between">
                        <p><strong>Subject:</strong> <span x-text="$wire.selectedEvent?.subject || 'N/A'"></span></p>
                        <p><strong>Book:</strong> <span x-text="$wire.selectedEvent?.book || 'N/A'"></span></p>
                    </div>
                    <div class="flex justify-between">
                        <p><strong>Status:</strong>
                            <span :class="{
        'inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800': $wire.selectedEvent?.status === 'completed',
        'inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800': $wire.selectedEvent?.status === 'in_progress',
        'inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800': $wire.selectedEvent?.status === 'needs_grading',
        'inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800': !$wire.selectedEvent?.status || $wire.selectedEvent?.status === 'N/A'
    }">
        <span x-text="{
            'completed': 'Completed',
            'in_progress': 'In Progress',
            'needs_grading': 'Needs Grading',
            'N/A': 'N/A'
        }[$wire.selectedEvent?.status || 'N/A']"></span>
    </span>
                        </p>

                        <p><strong>Score:</strong>
                            <span x-text="$wire.selectedEvent?.score + '/' + $wire.selectedEvent?.max_score"
                                  :class="{
                                      'text-green-600': $wire.selectedEvent?.percentage >= 70,
                                      'text-yellow-600': $wire.selectedEvent?.percentage < 70 && $wire.selectedEvent?.percentage >= 50,
                                      'text-red-600': $wire.selectedEvent?.percentage < 50
                                  }"></span>
                        </p>
                    </div>
                    <hr class="opacity-50">
                    <h4 class="font-semibold">Questions & Answers</h4>
                    <template x-if="$wire.selectedEventQuestions.length > 0 && $wire.selectedEventQuestions">
                        <ul class="space-y-4">
                            <template x-for="question in $wire.selectedEventQuestions" key="question.id">
                                <li class="border border-gray-200 p-3 rounded-md bg-gray-50">
                                    <p x-text="question.question"></p>
                                    <div class="mt-2 pl-4 border-l-2"
                                         :class="{
                                             'border-green-500': question.isCorrect,
                                             'border-red-500': !question.isCorrect
                                         }">
                                        <p><strong>Your Answer:</strong> <span x-text="question.studentAnswer || 'Not answered'"></span></p>
                                        <p><strong>Correct Answer:</strong> <span x-text="question.correctAnswer || 'N/A'"></span></p>
                                        <div class="flex items-center mt-1">
                                            <template x-if="question.isCorrect">
                                                <!-- Green checkmark SVG -->
                                                <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                </svg>
                                            </template>
                                            <template x-if="!question.isCorrect && question.studentAnswer">
                                                <!-- Red cross SVG -->
                                                <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                </svg>
                                            </template>
                                        </div>

                                    </div>
                                </li>
                            </template>
                        </ul>
                    </template>

                    <template x-if="$wire.selectedEventQuestions.length === 0">
                        <p class="text-gray-500 italic">No questions or results available.</p>
                    </template>
                </div>
            </main>
            <footer class="px-4 py-2 border-t border-t-gray-400 flex justify-end">
                <button @click="$wire.set('selectedEvent', null)" class="bg-indigo-600 text-white px-4 py-2 rounded">Close</button>
            </footer>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        var calendarEl = document.getElementById('calendar');
        var events = @json($assessments);

        var calendar = new FullCalendar.Calendar(calendarEl, {
            initialView: 'dayGridMonth',
            headerToolbar: {
                left: 'prev,next today',
                center: 'title',
                right: 'dayGridMonth,timeGridWeek,timeGridDay'
            },
            events: events.map(event => ({
                title: event.title,
                start: event.start,
                end: event.end,
                extendedProps: event
            })),
            eventClick: function(info) {
                @this.call('openEventDetails', info.event.extendedProps);
            }
        });

        calendar.render();
    });
</script>
@endpush
