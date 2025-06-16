<div class="max-w-7xl mx-auto p-6 bg-white dark:bg-gray-800 rounded-lg shadow-lg">
    <!-- Page Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">Activity Timeline</h1>
        <p class="text-gray-600 dark:text-gray-400">Track your learning journey and progress over time</p>
    </div>

    <!-- Activity Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-8">
        <div class="bg-gradient-to-r from-blue-500 to-blue-600 rounded-lg p-4 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm opacity-80">Total Activities</p>
                    <p class="text-2xl font-bold">{{ $activityStats['total'] ?? 0 }}</p>
                </div>
                <div class="bg-white bg-opacity-20 rounded-full p-2">
                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-r from-green-500 to-green-600 rounded-lg p-4 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm opacity-80">Assessments</p>
                    <p class="text-2xl font-bold">{{ $activityStats['assessments'] ?? 0 }}</p>
                </div>
                <div class="bg-white bg-opacity-20 rounded-full p-2">
                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"></path>
                        <path fill-rule="evenodd" d="M4 5a2 2 0 012-2v1a1 1 0 001 1h6a1 1 0 001-1V3a2 2 0 012 2v6a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3z" clip-rule="evenodd"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-r from-purple-500 to-purple-600 rounded-lg p-4 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm opacity-80">Books</p>
                    <p class="text-2xl font-bold">{{ $activityStats['books'] ?? 0 }}</p>
                </div>
                <div class="bg-white bg-opacity-20 rounded-full p-2">
                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9 4.804A7.968 7.968 0 005.5 4c-1.255 0-2.443.29-3.5.804v10A7.969 7.969 0 015.5 14c1.669 0 3.218.51 4.5 1.385A7.962 7.962 0 0114.5 14c1.255 0 2.443.29 3.5.804v-10A7.968 7.968 0 0014.5 4c-1.255 0-2.443.29-3.5.804V12a1 1 0 11-2 0V4.804z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-r from-orange-500 to-orange-600 rounded-lg p-4 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm opacity-80">Schedule</p>
                    <p class="text-2xl font-bold">{{ $activityStats['schedule'] ?? 0 }}</p>
                </div>
                <div class="bg-white bg-opacity-20 rounded-full p-2">
                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-r from-indigo-500 to-indigo-600 rounded-lg p-4 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm opacity-80">Today</p>
                    <p class="text-2xl font-bold">{{ $activityStats['today'] ?? 0 }}</p>
                </div>
                <div class="bg-white bg-opacity-20 rounded-full p-2">
                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-6 mb-8">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <!-- Search -->
            <div>
                <label for="search" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Search Activities</label>
                <input type="text"
                       wire:model.live.debounce.300ms="search"
                       id="search"
                       placeholder="Search activities..."
                       class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-600 dark:border-gray-500 dark:text-white">
            </div>

            <!-- Activity Type Filter -->
            <div>
                <label for="activityType" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Activity Type</label>
                <select wire:model.live="activityType"
                        id="activityType"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-600 dark:border-gray-500 dark:text-white">
                    @foreach($activityTypeOptions as $value => $label)
                        <option value="{{ $value }}">{{ $label }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Date From -->
            <div>
                <label for="dateFrom" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">From Date</label>
                <input type="date"
                       wire:model.live="dateFrom"
                       id="dateFrom"
                       class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-600 dark:border-gray-500 dark:text-white">
            </div>

            <!-- Date To -->
            <div>
                <label for="dateTo" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">To Date</label>
                <input type="date"
                       wire:model.live="dateTo"
                       id="dateTo"
                       class="w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-600 dark:border-gray-500 dark:text-white">
            </div>
        </div>
    </div>

    <!-- Timeline Container -->
    <div class="relative">
        @forelse($activityLogs as $index => $activity)
            @php
                $isToday = $activity->created_at->isToday();
                $isYesterday = $activity->created_at->isYesterday();
                $showDateLabel = $index === 0 ||
                    !$activityLogs[$index - 1]->created_at->isSameDay($activity->created_at);

                $description = strtolower($activity->description);
                $activityColor = match(true) {
                    str_contains($description, 'assessment') => 'green',
                    str_contains($description, 'book') => 'purple',
                    str_contains($description, 'schedule') => 'orange',
                    str_contains($description, 'reading') => 'blue',
                    str_contains($description, 'subscription') => 'indigo',
                    default => 'gray'
                };
            @endphp

            <!-- Date Label -->
            @if($showDateLabel)
                <div class="relative flex items-center mb-6 mt-8 first:mt-0">
                    <div class="flex-grow border-t border-gray-300 dark:border-gray-600"></div>
                    <div class="mx-4 px-4 py-2 bg-gray-100 dark:bg-gray-700 rounded-full">
                        <span class="text-sm font-semibold text-gray-700 dark:text-gray-300">
                            @if($isToday)
                                Today - {{ $activity->created_at->format('M j, Y') }}
                            @elseif($isYesterday)
                                Yesterday - {{ $activity->created_at->format('M j, Y') }}
                            @else
                                {{ $activity->created_at->format('l, M j, Y') }}
                            @endif
                        </span>
                    </div>
                    <div class="flex-grow border-t border-gray-300 dark:border-gray-600"></div>
                </div>
            @endif

            <!-- Timeline Item -->
            <div class="relative flex items-start mb-8 group">
                <!-- Timeline Line -->
                @if(!$loop->last)
                    <div class="absolute left-6 top-12 w-0.5 h-full bg-gray-200 dark:bg-gray-600 group-hover:bg-{{ $activityColor }}-300 transition-colors duration-200"></div>
                @endif

                <!-- Timeline Icon -->
                <div class="relative z-10 flex-shrink-0">
                    <div class="w-12 h-12 rounded-full bg-{{ $activityColor }}-500 flex items-center justify-center text-white shadow-lg group-hover:scale-110 transition-all duration-200">
                        @if(str_contains($description, 'assessment'))
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9 2a1 1 0 000 2h2a1 1 0 100-2H9z"></path>
                                <path fill-rule="evenodd" d="M4 5a2 2 0 012-2v1a1 1 0 001 1h6a1 1 0 001-1V3a2 2 0 012 2v6a2 2 0 01-2 2H6a2 2 0 01-2-2V5zm3 4a1 1 0 000 2h.01a1 1 0 100-2H7zm3 0a1 1 0 000 2h3a1 1 0 100-2h-3z" clip-rule="evenodd"></path>
                            </svg>
                        @elseif(str_contains($description, 'book'))
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9 4.804A7.968 7.968 0 005.5 4c-1.255 0-2.443.29-3.5.804v10A7.969 7.969 0 015.5 14c1.669 0 3.218.51 4.5 1.385A7.962 7.962 0 0114.5 14c1.255 0 2.443.29 3.5.804v-10A7.968 7.968 0 0014.5 4c-1.255 0-2.443.29-3.5.804V12a1 1 0 11-2 0V4.804z"></path>
                            </svg>
                        @elseif(str_contains($description, 'schedule'))
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"></path>
                            </svg>
                        @elseif(str_contains($description, 'reading'))
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"></path>
                                <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"></path>
                            </svg>
                        @else
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        @endif
                    </div>

                    <!-- Pulse Animation for Recent Activities -->
                    @if($activity->created_at->diffInHours() < 2)
                        <div class="absolute inset-0 rounded-full bg-{{ $activityColor }}-400 animate-ping opacity-25"></div>
                    @endif
                </div>

                <!-- Timeline Content -->
                <div class="ml-6 flex-1">
                    <div class="bg-white dark:bg-gray-700 rounded-lg border border-gray-200 dark:border-gray-600 p-6 shadow-sm hover:shadow-md transition-all duration-200 group-hover:border-{{ $activityColor }}-300">
                        <div class="flex items-start justify-between mb-3">
                            <div class="flex-1">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-1">
                                    {{ $activity->description }}
                                </h3>
                                <div class="flex items-center space-x-3 text-sm text-gray-500 dark:text-gray-400">
                                    <span class="flex items-center">
                                        <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                                        </svg>
                                        {{ $activity->created_at->format('g:i A') }}
                                    </span>
                                    <span>•</span>
                                    <span>{{ $activity->created_at->diffForHumans() }}</span>
                                    @if($activity->log_name)
                                        <span>•</span>
                                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-{{ $activityColor }}-100 text-{{ $activityColor }}-800 dark:bg-{{ $activityColor }}-900 dark:text-{{ $activityColor }}-200">
                                            {{ ucfirst($activity->log_name) }}
                                        </span>
                                    @endif
                                </div>
                            </div>

                            <button
                                wire:click="viewActivityDetails({{ $activity->id }})"
                                class="ml-4 inline-flex items-center px-3 py-1.5 border border-gray-300 shadow-sm text-xs font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-{{ $activityColor }}-500 dark:bg-gray-600 dark:border-gray-500 dark:text-gray-200 dark:hover:bg-gray-500">
                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"></path>
                                    <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"></path>
                                </svg>
                                Details
                            </button>
                        </div>

                        <!-- Activity Properties Preview -->
                        @if($activity->properties && count($activity->properties) > 0)
                            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                                @foreach(collect($activity->properties)->take(6) as $key => $value)
                                    @if(is_string($value) || is_numeric($value))
                                        <div class="bg-gray-50 dark:bg-gray-600 rounded-md p-3">
                                            <div class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">
                                                {{ str_replace('_', ' ', $key) }}
                                            </div>
                                            <div class="text-sm font-medium text-gray-900 dark:text-white mt-1 truncate">
                                                {{ $value }}
                                            </div>
                                        </div>
                                    @endif
                                @endforeach
                                @if(count($activity->properties) > 6)
                                    <div class="bg-gray-50 dark:bg-gray-600 rounded-md p-3 flex items-center justify-center">
                                        <span class="text-xs text-gray-500 dark:text-gray-400">
                                            +{{ count($activity->properties) - 6 }} more properties
                                        </span>
                                    </div>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="text-center py-16">
                <div class="w-24 h-24 mx-auto bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mb-4">
                    <svg class="w-12 h-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-medium text-gray-900 dark:text-white mb-2">No activities found</h3>
                <p class="text-gray-500 dark:text-gray-400 mb-6">Start learning to see your activity timeline here!</p>
                <div class="text-sm text-gray-400 dark:text-gray-500">
                    Try adjusting your filters or date range to see more activities.
                </div>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($activityLogs->hasPages())
        <div class="mt-12 border-t border-gray-200 dark:border-gray-600 pt-6">
            {{ $activityLogs->links() }}
        </div>
    @endif

    <!-- Activity Details Modal -->
    @if($showModal && $selectedActivity)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <!-- Background overlay -->
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"
                     wire:click="closeModal"></div>

                <!-- Modal panel -->
                <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-3xl sm:w-full sm:p-6">
                    <div class="sm:flex sm:items-start">
                        <div class="mt-3 text-center sm:mt-0 sm:text-left w-full">
                            <div class="flex items-center justify-between mb-6">
                                <h3 class="text-xl leading-6 font-semibold text-gray-900 dark:text-white" id="modal-title">
                                    Activity Details
                                </h3>
                                <button
                                    wire:click="closeModal"
                                    class="rounded-md text-gray-400 hover:text-gray-600 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                            </div>

                            <div class="space-y-6">
                                <!-- Basic Info -->
                                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-6">
                                    <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-3">Description</h4>
                                    <p class="text-gray-700 dark:text-gray-300 text-base">{{ $selectedActivity->description }}</p>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-6">
                                        <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-3">Timestamp</h4>
                                        <p class="text-gray-700 dark:text-gray-300 text-base mb-2">{{ $selectedActivity->created_at->format('l, F j, Y') }}</p>
                                        <p class="text-gray-700 dark:text-gray-300 text-base mb-2">{{ $selectedActivity->created_at->format('g:i:s A') }}</p>
                                        <p class="text-sm text-gray-500 dark:text-gray-400">{{ $selectedActivity->created_at->diffForHumans() }}</p>
                                    </div>

                                    @if($selectedActivity->log_name)
                                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-6">
                                        <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-3">Category</h4>
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                                            {{ ucfirst($selectedActivity->log_name) }}
                                        </span>
                                    </div>
                                    @endif
                                </div>

                                <!-- Properties -->
                                @if($selectedActivity->properties && count($selectedActivity->properties) > 0)
                                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-6">
                                        <h4 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Activity Properties</h4>
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                            @foreach($selectedActivity->properties as $key => $value)
                                                <div class="bg-white dark:bg-gray-600 rounded-md p-4">
                                                    <div class="text-sm font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide mb-1">
                                                        {{ str_replace('_', ' ', $key) }}
                                                    </div>
                                                    <div class="text-gray-900 dark:text-white">
                                                        @if(is_array($value))
                                                            {{ implode(', ', $value) }}
                                                        @elseif(is_bool($value))
                                                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $value ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200' }}">
                                                                {{ $value ? 'Yes' : 'No' }}
                                                            </span>
                                                        @else
                                                            {{ $value }}
                                                        @endif
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
