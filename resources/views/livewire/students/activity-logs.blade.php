<div class="max-w-7xl mx-auto p-6 bg-white dark:bg-gray-800 rounded-lg shadow-lg">
    <!-- Page Header -->
    <div class="mb-8">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">Activity Timeline</h1>
                <p class="text-gray-600 dark:text-gray-400">Track your learning journey and progress over time</p>
            </div>
            <div class="flex items-center space-x-3">
                <!-- View Mode Toggle -->
                <div class="flex bg-gray-100 dark:bg-gray-700 rounded-lg p-1">
                    <button wire:click="$set('viewMode', 'timeline')"
                            class="px-3 py-1.5 text-sm font-medium rounded-md transition-colors {{ $viewMode === 'timeline' ? 'bg-white dark:bg-gray-600 text-gray-900 dark:text-white shadow-sm' : 'text-gray-500 dark:text-gray-400' }}">
                        <svg class="w-4 h-4 mr-1.5 inline" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M3 4a1 1 0 011-1h12a1 1 0 011 1v2a1 1 0 01-1 1H4a1 1 0 01-1-1V4zM3 10a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H4a1 1 0 01-1-1v-6zM14 9a1 1 0 00-1 1v6a1 1 0 001 1h2a1 1 0 001-1v-6a1 1 0 00-1-1h-2z"></path>
                        </svg>
                        Timeline
                    </button>
                    <button wire:click="$set('viewMode', 'list')"
                            class="px-3 py-1.5 text-sm font-medium rounded-md transition-colors {{ $viewMode === 'list' ? 'bg-white dark:bg-gray-600 text-gray-900 dark:text-white shadow-sm' : 'text-gray-500 dark:text-gray-400' }}">
                        <svg class="w-4 h-4 mr-1.5 inline" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M3 4a1 1 0 000 2h14a1 1 0 100-2H3zM3 10a1 1 0 000 2h14a1 1 0 100-2H3zM3 16a1 1 0 000 2h14a1 1 0 100-2H3z"></path>
                        </svg>
                        List
                    </button>
                </div>

                <!-- Export Button -->
                <button wire:click="exportActivities"
                        class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-200 dark:hover:bg-gray-600">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    Export
                </button>
            </div>
        </div>
    </div>

    <!-- Enhanced Activity Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-6 gap-4 mb-8">
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
                    <p class="text-sm opacity-80">Reading</p>
                    <p class="text-2xl font-bold">{{ $activityStats['reading'] ?? 0 }}</p>
                </div>
                <div class="bg-white bg-opacity-20 rounded-full p-2">
                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M10 12a2 2 0 100-4 2 2 0 000 4z"></path>
                        <path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-r from-indigo-500 to-indigo-600 rounded-lg p-4 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm opacity-80">This Week</p>
                    <p class="text-2xl font-bold">{{ $activityStats['this_week'] ?? 0 }}</p>
                </div>
                <div class="bg-white bg-opacity-20 rounded-full p-2">
                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-gradient-to-r from-pink-500 to-pink-600 rounded-lg p-4 text-white">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm opacity-80">Streak</p>
                    <p class="text-2xl font-bold">{{ $activityStreak ?? 0 }}</p>
                    <p class="text-xs opacity-70">days</p>
                </div>
                <div class="bg-white bg-opacity-20 rounded-full p-2">
                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Enhanced Filters -->
    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg mb-8 {{ $showFilters ? 'p-6' : 'p-4' }} transition-all duration-200">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-medium text-gray-900 dark:text-white">Filters & Search</h3>
            <div class="flex items-center space-x-2">
                <button wire:click="clearFilters"
                        class="text-sm text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                    Clear All
                </button>
                <button wire:click="toggleFilters"
                        class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                    <svg class="w-5 h-5 transform transition-transform {{ $showFilters ? 'rotate-180' : '' }}" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                    </svg>
                </button>
            </div>
        </div>

        @if($showFilters)
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-4">
                <!-- Search -->
                <div>
                    <label for="search" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Search Activities</label>
                    <div class="relative">
                        <input type="text"
                               wire:model.live.debounce.300ms="search"
                               id="search"
                               placeholder="Search activities..."
                               class="w-full pl-10 pr-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-600 dark:border-gray-500 dark:text-white">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd"></path>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Activity Type Filter -->
                <div>
                    <label for="activityType" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Activity Type</label>
                    <select wire:model.live="activityType"
                            id="activityType"
                            class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md dark:bg-gray-700 dark:text-white shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-600 dark:border-gray-500 dark:text-white">
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
                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md dark:bg-gray-700 dark:text-white shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-600 dark:border-gray-500 dark:text-white">
                </div>

                <!-- Date To -->
                <div>
                    <label for="dateTo" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">To Date</label>
                    <input type="date"
                           wire:model.live="dateTo"
                           id="dateTo"
                           class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md dark:bg-gray-700 dark:text-white shadow-sm focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 dark:bg-gray-600 dark:border-gray-500 dark:text-white">
                </div>
            </div>

            <!-- Quick Date Range Buttons -->
            <div class="flex flex-wrap gap-2 mb-4">
                <span class="text-sm font-medium text-gray-700 dark:text-gray-300 mr-2">Quick ranges:</span>
                <button wire:click="setDateRange('today')"
                        class="px-3 py-1 text-xs bg-gray-200 dark:bg-gray-600 text-gray-700 dark:text-gray-300 rounded-full hover:bg-gray-300 dark:hover:bg-gray-500 transition-colors">
                    Today
                </button>
                <button wire:click="setDateRange('week')"
                        class="px-3 py-1 text-xs bg-gray-200 dark:bg-gray-600 text-gray-700 dark:text-gray-300 rounded-full hover:bg-gray-300 dark:hover:bg-gray-500 transition-colors">
                    This Week
                </button>
                <button wire:click="setDateRange('month')"
                        class="px-3 py-1 text-xs bg-gray-200 dark:bg-gray-600 text-gray-700 dark:text-gray-300 rounded-full hover:bg-gray-300 dark:hover:bg-gray-500 transition-colors">
                    This Month
                </button>
                <button wire:click="setDateRange('quarter')"
                        class="px-3 py-1 text-xs bg-gray-200 dark:bg-gray-600 text-gray-700 dark:text-gray-300 rounded-full hover:bg-gray-300 dark:hover:bg-gray-500 transition-colors">
                    Last 3 Months
                </button>
                <button wire:click="setDateRange('year')"
                        class="px-3 py-1 text-xs bg-gray-200 dark:bg-gray-600 text-gray-700 dark:text-gray-300 rounded-full hover:bg-gray-300 dark:hover:bg-gray-500 transition-colors">
                    This Year
                </button>
            </div>

            <!-- Per Page and Sort Options -->
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <div class="flex items-center space-x-2">
                        <label for="perPage" class="text-sm font-medium text-gray-700 dark:text-gray-300">Show:</label>
                        <select wire:model.live="perPage" id="perPage" class="text-sm border-gray-300 rounded-md dark:bg-gray-600 dark:border-gray-500 dark:text-white">
                            <option value="10">10</option>
                            <option value="15">15</option>
                            <option value="25">25</option>
                            <option value="50">50</option>
                        </select>
                        <span class="text-sm text-gray-500 dark:text-gray-400">per page</span>
                    </div>
                </div>

                @if($viewMode === 'list')
                    <div class="flex items-center space-x-2">
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Sort by:</span>
                        <button wire:click="sortBy('created_at')"
                                class="text-sm px-2 py-1 rounded {{ $sortBy === 'created_at' ? 'bg-indigo-100 text-indigo-700 dark:bg-indigo-900 dark:text-indigo-300' : 'text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200' }}">
                            Date
                            @if($sortBy === 'created_at')
                                <svg class="w-3 h-3 inline ml-1 {{ $sortDirection === 'asc' ? 'transform rotate-180' : '' }}" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"></path>
                                </svg>
                            @endif
                        </button>
                        <button wire:click="sortBy('description')"
                                class="text-sm px-2 py-1 rounded {{ $sortBy === 'description' ? 'bg-indigo-100 text-indigo-700 dark:bg-indigo-900 dark:text-indigo-300' : 'text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200' }}">
                            Activity
                            @if($sortBy === 'description')
                                <svg class="w-3 h-3 inline ml-1 {{ $sortDirection === 'asc' ? 'transform rotate-180' : '' }}" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z"></path>
                                </svg>
                            @endif
                        </button>
                    </div>
                @endif
            </div>
        @endif
    </div>

    <!-- Activities Display -->
    @if($viewMode === 'timeline')
        @include('livewire.students.partials.activity-timeline')
    @else
        @include('livewire.students.partials.activity-list')
    @endif

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
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" wire:click="closeModal"></div>

                <div class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full">
                    <div class="bg-white dark:bg-gray-800 px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="flex items-start justify-between">
                            <div class="flex">
                                <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-{{ $this->getActivityTypeColor($selectedActivity->description) }}-100 dark:bg-{{ $this->getActivityTypeColor($selectedActivity->description) }}-900 sm:mx-0 sm:h-10 sm:w-10">
                                    <svg class="h-6 w-6 text-{{ $this->getActivityTypeColor($selectedActivity->description) }}-600 dark:text-{{ $this->getActivityTypeColor($selectedActivity->description) }}-400" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <div class="ml-4">
                                    <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-white" id="modal-title">
                                        Activity Details
                                    </h3>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">
                                        {{ $selectedActivity->created_at->format('F j, Y \a\t g:i A') }}
                                    </p>
                                </div>
                            </div>
                            <button wire:click="closeModal" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-200">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>

                        <div class="mt-6">
                            <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 mb-4">
                                <h4 class="text-sm font-medium text-gray-900 dark:text-white mb-2">Description</h4>
                                <p class="text-gray-700 dark:text-gray-300">{{ $selectedActivity->description }}</p>
                            </div>

                            @if($selectedActivity->log_name)
                                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 mb-4">
                                    <h4 class="text-sm font-medium text-gray-900 dark:text-white mb-2">Log Name</h4>
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-{{ $this->getActivityTypeColor($selectedActivity->description) }}-100 text-{{ $this->getActivityTypeColor($selectedActivity->description) }}-800 dark:bg-{{ $this->getActivityTypeColor($selectedActivity->description) }}-900 dark:text-{{ $this->getActivityTypeColor($selectedActivity->description) }}-200">
                                        {{ ucfirst($selectedActivity->log_name) }}
                                    </span>
                                </div>
                            @endif

                            @if($selectedActivity->properties && count($selectedActivity->properties) > 0)
                                <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                                    <h4 class="text-sm font-medium text-gray-900 dark:text-white mb-3">Additional Information</h4>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                        @foreach($selectedActivity->properties as $key => $value)
                                            @if(is_string($value) || is_numeric($value))
                                                <div class="bg-white dark:bg-gray-600 rounded-md p-3">
                                                    <div class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">
                                                        {{ str_replace('_', ' ', $key) }}
                                                    </div>
                                                    <div class="text-sm font-medium text-gray-900 dark:text-white mt-1">
                                                        {{ $value }}
                                                    </div>
                                                </div>
                                            @elseif(is_array($value))
                                                <div class="bg-white dark:bg-gray-600 rounded-md p-3">
                                                    <div class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">
                                                        {{ str_replace('_', ' ', $key) }}
                                                    </div>
                                                    <div class="text-sm font-medium text-gray-900 dark:text-white mt-1">
                                                        <pre class="text-xs bg-gray-100 dark:bg-gray-700 p-2 rounded mt-1 overflow-x-auto">{{ json_encode($value, JSON_PRETTY_PRINT) }}</pre>
                                                    </div>
                                                </div>
                                            @endif
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div class="bg-gray-50 dark:bg-gray-700 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button wire:click="closeModal"
                                type="button"
                                class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:ml-3 sm:w-auto sm:text-sm">
                            Close
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
