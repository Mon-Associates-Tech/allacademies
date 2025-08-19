<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
    <!-- Header Section -->
    <div class="bg-white dark:bg-gray-900 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 mb-6">
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center space-y-4 sm:space-y-0">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">My Schedule</h1>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                        Manage your teaching activities, assessments, and assignments
                    </p>
                </div>

                <!-- View Mode Toggle -->
                <div class="flex space-x-2">
                    <button wire:click="setViewMode('calendar')"
                            class="px-4 py-2 text-sm font-medium rounded-lg transition-all duration-200 {{ $this->viewMode === 'calendar' ? 'bg-blue-600 text-white shadow-sm' : 'bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700' }}">
                        <i class="fas fa-calendar mr-2"></i>Calendar
                    </button>
                    <button wire:click="setViewMode('list')"
                            class="px-4 py-2 text-sm font-medium rounded-lg transition-all duration-200 {{ $this->viewMode === 'list' ? 'bg-blue-600 text-white shadow-sm' : 'bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700' }}">
                        <i class="fas fa-list mr-2"></i>List
                    </button>
                    <button wire:click="setViewMode('week')"
                            class="px-4 py-2 text-sm font-medium rounded-lg transition-all duration-200 {{ $this->viewMode === 'week' ? 'bg-blue-600 text-white shadow-sm' : 'bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300 hover:bg-gray-200 dark:hover:bg-gray-700' }}">
                        <i class="fas fa-calendar-week mr-2"></i>Week
                    </button>
                </div>
            </div>
        </div>

        <!-- Filter Section -->
        <div class="px-6 py-4">
            <div class="flex flex-wrap items-center gap-3">
                <label class="text-sm font-medium text-gray-700 dark:text-gray-300">Filter by:</label>

                <button wire:click="setFilterType('all')"
                        class="px-3 py-1.5 text-xs font-medium rounded-full transition-all duration-200 {{ $filterType === 'all' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200' : 'bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400 hover:bg-gray-200 dark:hover:bg-gray-700' }}">
                    All Activities
                </button>

                <button wire:click="setFilterType('assignments')"
                        class="px-3 py-1.5 text-xs font-medium rounded-full transition-all duration-200 {{ $filterType === 'assignments' ? 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200' : 'bg-gray-100 dark:bg-gray-800 text-gray-600 dark:text-gray-400 hover:bg-gray-200 dark:hover:bg-gray-700' }}">
                    <i class="fas fa-tasks mr-1"></i>Assignments
                </button>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    @php
        $weeklyStats = $this->getWeeklyStats();
        $monthlyStats = $this->getMonthlyStats();
    @endphp

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
        <div class="bg-white dark:bg-gray-900 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex items-center">
                <div class="p-3 bg-blue-100 dark:bg-blue-900/30 rounded-xl">
                    <i class="fas fa-file-alt text-blue-600 dark:text-blue-400 text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Assessments This Week</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $weeklyStats['assessments_created'] ?? 0 }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-900 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex items-center">
                <div class="p-3 bg-purple-100 dark:bg-purple-900/30 rounded-xl">
                    <i class="fas fa-tasks text-purple-600 dark:text-purple-400 text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Assignments This Week</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $weeklyStats['assignments_created'] ?? 0 }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-900 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex items-center">
                <div class="p-3 bg-green-100 dark:bg-green-900/30 rounded-xl">
                    <i class="fas fa-percentage text-green-600 dark:text-green-400 text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Avg. Assessment Score</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ number_format($weeklyStats['average_score'] ?? 0, 1) }}%</p>
                </div>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-900 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex items-center">
                <div class="p-3 bg-orange-100 dark:bg-orange-900/30 rounded-xl">
                    <i class="fas fa-book text-orange-600 dark:text-orange-400 text-xl"></i>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Subjects Taught</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $monthlyStats['subjects_taught'] ?? 0 }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="bg-white dark:bg-gray-900 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
        @if($viewMode === 'calendar')
            <!-- Calendar View -->
            <div class="p-6">
                <!-- Calendar Header -->
                <div class="flex items-center justify-between mb-6">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">{{ $monthName }}</h2>
                    <div class="flex space-x-2">
                        <button wire:click="changeMonth('prev')"
                                class="p-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-lg transition-colors duration-200">
                            <i class="fas fa-chevron-left"></i>
                        </button>
                        <button wire:click="changeMonth('next')"
                                class="p-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-lg transition-colors duration-200">
                            <i class="fas fa-chevron-right"></i>
                        </button>
                    </div>
                </div>

                <!-- Calendar Grid -->
                <div class="grid grid-cols-7 gap-px bg-gray-200 dark:bg-gray-700 rounded-lg overflow-hidden">
                    <!-- Week Day Headers -->
                    @foreach($weekDays as $day)
                        <div class="bg-gray-50 dark:bg-gray-800 p-3 text-center">
                            <span class="text-sm font-medium text-gray-700 dark:text-gray-300">{{ $day }}</span>
                        </div>
                    @endforeach

                    <!-- Calendar Days -->
                    @foreach($calendarData as $week)
                        @foreach($week as $day)
                            <div class="bg-white dark:bg-gray-900 min-h-[120px] p-2 {{ !$day['isCurrentMonth'] ? 'opacity-50' : '' }}">
                                <div class="flex justify-between items-start mb-2">
                                    <button wire:click="selectDate('{{ $day['date']->format('Y-m-d') }}')"
                                            class="text-sm font-medium {{ $day['isToday'] ? 'bg-blue-600 text-white' : ($day['isCurrentMonth'] ? 'text-gray-900 dark:text-white hover:bg-blue-100 dark:hover:bg-blue-900' : 'text-gray-400') }} w-7 h-7 rounded-full flex items-center justify-center transition-colors duration-200">
                                        {{ $day['date']->day }}
                                    </button>
                                    @if($day['activityCount'] > 0)
                                        <span class="bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200 text-xs px-2 py-1 rounded-full">
                                            {{ $day['activityCount'] }}
                                        </span>
                                    @endif
                                </div>

                                <!-- Activity indicators -->
                                <div class="space-y-1">
                                    @foreach($day['activities']->take(3) as $activity)
                                        <div class="text-xs p-1 rounded bg-{{ $activity['color'] }}-100 text-{{ $activity['color'] }}-800 dark:bg-{{ $activity['color'] }}-900 dark:text-{{ $activity['color'] }}-200 truncate cursor-pointer hover:bg-{{ $activity['color'] }}-200 dark:hover:bg-{{ $activity['color'] }}-800 transition-colors duration-200"
                                             wire:click="showActivityDetails({{ $activity['id'] }}, '{{ $activity['type'] }}')">
                                            {{ Str::limit($activity['title'], 20) }}
                                        </div>
                                    @endforeach
                                    @if($day['activityCount'] > 3)
                                        <div class="text-xs text-gray-500 dark:text-gray-400 text-center">
                                            +{{ $day['activityCount'] - 3 }} more
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    @endforeach
                </div>
            </div>

        @elseif($viewMode === 'list')
            <!-- List View -->
            <div class="p-6">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-6">
                    Activities for {{ $selectedDate->format('F j, Y') }}
                </h2>

                @if($this->activities->isEmpty())
                    <div class="text-center py-12">
                        <div class="w-24 h-24 mx-auto mb-4">
                            <i class="fas fa-calendar-day text-gray-300 dark:text-gray-600 text-6xl"></i>
                        </div>
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">No activities found</h3>
                        <p class="text-gray-500 dark:text-gray-400">There are no activities for the selected date and filter.</p>
                    </div>
                @else
                    <div class="space-y-4">
                        @foreach($this->activities as $activity)
                            <div class="border border-gray-200 dark:border-gray-700 rounded-xl p-4 hover:shadow-md transition-shadow duration-200 cursor-pointer"
                                 wire:click="showActivityDetails({{ $activity['id'] }}, '{{ $activity['type'] }}')">
                                <div class="flex items-start justify-between">
                                    <div class="flex items-start space-x-4">
                                        <div class="p-2 rounded-lg bg-{{ $activity['color'] }}-100 text-{{ $activity['color'] }}-600 dark:bg-{{ $activity['color'] }}-900/30 dark:text-{{ $activity['color'] }}-400">
                                            <i class="{{ $activity['icon'] }} text-lg"></i>
                                        </div>
                                        <div class="flex-1">
                                            <h3 class="font-semibold text-gray-900 dark:text-white">{{ $activity['title'] }}</h3>
                                            @if(isset($activity['subject']))
                                                <p class="text-sm text-gray-600 dark:text-gray-400">{{ $activity['subject'] }}</p>
                                            @endif
                                            @if(isset($activity['topic']))
                                                <p class="text-xs text-gray-500 dark:text-gray-500">{{ $activity['topic'] }}</p>
                                            @endif
                                            <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                                                {{ $activity['date']->format('M j, Y \a\t g:i A') }}
                                            </p>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        @if($activity['type'] === 'assessment' && isset($activity['percentage']))
                                            <div class="text-lg font-bold text-gray-900 dark:text-white">
                                                {{ number_format($activity['percentage'], 1) }}%
                                            </div>
                                            <div class="text-sm text-gray-500 dark:text-gray-400">
                                                {{ $activity['score'] }}/{{ $activity['max_score'] }}
                                            </div>
                                        @endif
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-{{ $activity['color'] }}-100 text-{{ $activity['color'] }}-800 dark:bg-{{ $activity['color'] }}-900 dark:text-{{ $activity['color'] }}-200 mt-2">
                                            {{ ucfirst(str_replace('_', ' ', $activity['status'])) }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

        @elseif($this->viewMode === 'week')
            <!-- Week View -->
            <div class="p-6">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white mb-6">
                    Week of {{ $selectedDate->startOfWeek()->format('M j') }} - {{ $selectedDate->endOfWeek()->format('M j, Y') }}
                </h2>

                <div class="grid grid-cols-7 gap-4">
                    @php
                        $weekStart = $selectedDate->copy()->startOfWeek();
                    @endphp
                    @for($i = 0; $i < 7; $i++)
                        @php
                            $currentDay = $weekStart->copy()->addDays($i);
                            $dayActivities = $this->activities->filter(function($activity) use ($currentDay) {
                                return $activity['date']->isSameDay($currentDay);
                            });
                        @endphp
                        <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4 min-h-[200px]">
                            <div class="text-center mb-3">
                                <div class="text-sm font-medium text-gray-600 dark:text-gray-400">
                                    {{ $currentDay->format('D') }}
                                </div>
                                <div class="text-lg font-bold text-gray-900 dark:text-white {{ $currentDay->isToday() ? 'bg-blue-600 text-white rounded-full w-8 h-8 flex items-center justify-center mx-auto' : '' }}">
                                    {{ $currentDay->day }}
                                </div>
                            </div>
                            <div class="space-y-2">
                                @foreach($dayActivities->take(5) as $activity)
                                    <div class="text-xs p-2 rounded bg-{{ $activity['color'] }}-100 text-{{ $activity['color'] }}-800 dark:bg-{{ $activity['color'] }}-900 dark:text-{{ $activity['color'] }}-200 cursor-pointer hover:bg-{{ $activity['color'] }}-200 dark:hover:bg-{{ $activity['color'] }}-800 transition-colors duration-200"
                                         wire:click="showActivityDetails({{ $activity['id'] }}, '{{ $activity['type'] }}')">
                                        <div class="font-medium truncate">{{ Str::limit($activity['title'], 15) }}</div>
                                        @if(isset($activity['percentage']))
                                            <div class="text-xs opacity-75">{{ number_format($activity['percentage'], 0) }}%</div>
                                        @endif
                                    </div>
                                @endforeach
                                @if($dayActivities->count() > 5)
                                    <div class="text-xs text-gray-500 dark:text-gray-400 text-center">
                                        +{{ $dayActivities->count() - 5 }} more
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endfor
                </div>
            </div>
        @endif
    </div>

    <!-- Activity Detail Modal -->
    @if($showActivityModal && $selectedActivity)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" wire:click="closeActivityModal"></div>

                <div class="inline-block align-bottom bg-white dark:bg-gray-900 rounded-xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <div class="bg-white dark:bg-gray-900 px-6 pt-6 pb-4">
                        <div class="flex items-start justify-between">
                            <div class="flex items-center space-x-3">
                                <div class="p-3 rounded-xl bg-blue-100 text-blue-600 dark:bg-blue-900/30 dark:text-blue-400">
                                    @if($selectedActivity instanceof \App\Models\Assessment)
                                        <i class="fas fa-clipboard-check text-xl"></i>
                                    @elseif($selectedActivity instanceof \App\Models\Assignment)
                                        <i class="fas fa-tasks text-xl"></i>
                                    @else
                                        <i class="fas fa-chalkboard-teacher text-xl"></i>
                                    @endif
                                </div>
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                                        @if($selectedActivity instanceof \App\Models\Assessment)
                                            {{ $selectedActivity->title }}
                                        @elseif($selectedActivity instanceof \App\Models\Assignment)
                                            {{ $selectedActivity->title }}
                                        @endif
                                    </h3>
                                    <p class="text-sm text-gray-600 dark:text-gray-400">
                                        @if($selectedActivity instanceof \App\Models\Assessment)
                                            {{ $selectedActivity->subject?->name }}
                                        @elseif($selectedActivity instanceof \App\Models\Assignment)
                                            {{ $selectedActivity->subject?->name }}
                                        @endif
                                    </p>
                                </div>
                            </div>
                            <button wire:click="closeActivityModal"
                                    class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                                <i class="fas fa-times text-xl"></i>
                            </button>
                        </div>
                    </div>

                    <div class="bg-gray-50 dark:bg-gray-800 px-6 py-4 space-y-4">
                        @if($selectedActivity instanceof \App\Models\Assessment)
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Score</label>
                                    <p class="text-lg font-bold text-gray-900 dark:text-white">
                                        {{ $selectedActivity->score ?? 'N/A' }}/{{ $selectedActivity->max_score ?? 'N/A' }}
                                    </p>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Percentage</label>
                                    <p class="text-lg font-bold text-gray-900 dark:text-white">
                                        {{ $selectedActivity->percentage_score ? number_format($selectedActivity->percentage_score, 1) . '%' : 'N/A' }}
                                    </p>
                                </div>
                            </div>

                            @if($selectedActivity->scheduled_date)
                                <div>
                                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Scheduled Date</label>
                                    <p class="text-sm text-gray-900 dark:text-white">
                                        {{ $selectedActivity->scheduled_date->format('M j, Y') }}
                                    </p>
                                </div>
                            @endif

                        @elseif($selectedActivity instanceof \App\Models\Assignment)
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Due Date</label>
                                <p class="text-sm text-gray-900 dark:text-white">
                                    {{ $selectedActivity->ends_at?->format('M j, Y \a\t g:i A') }}
                                </p>
                            </div>
                        @endif

                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Status</label>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                {{ ucfirst(str_replace('_', ' ', $selectedActivity->status ?? 'Unknown')) }}
                            </span>
                        </div>

                        @if($selectedActivity instanceof \App\Models\Assessment && $selectedActivity->topic)
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">Topic</label>
                                <p class="text-sm text-gray-900 dark:text-white">{{ $selectedActivity->topic->name }}</p>
                                @if($selectedActivity->subtopic)
                                    <p class="text-xs text-gray-600 dark:text-gray-400">{{ $selectedActivity->subtopic->name }}</p>
                                @endif
                            </div>
                        @endif
                    </div>

                    <div class="bg-gray-50 dark:bg-gray-800 px-6 py-3">
                        <button wire:click="closeActivityModal"
                                class="w-full px-4 py-2 bg-gray-200 dark:bg-gray-700 text-gray-800 dark:text-gray-200 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors duration-200">
                            Close
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

@push('styles')
    <style>
        .scrollbar-thin::-webkit-scrollbar {
            width: 6px;
        }
        .scrollbar-thumb-gray-300::-webkit-scrollbar-thumb {
            background-color: rgb(209 213 219);
            border-radius: 3px;
        }
        .scrollbar-track-transparent::-webkit-scrollbar-track {
            background-color: transparent;
        }
    </style>
@endpush

