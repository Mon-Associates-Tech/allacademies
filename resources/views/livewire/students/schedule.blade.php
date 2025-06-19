<div x-data="{
    currentView: 'calendar',
    showDetailsModal: false,
    selectedAssessment: null,
    showFilters: false
}" class="max-w-7xl mx-auto space-y-6">

    <!-- Enhanced Header Section -->
    <div class="bg-gradient-to-r from-blue-600 via-indigo-600 to-purple-700 rounded-xl p-6 text-white shadow-lg">
        <div class="flex items-center justify-between">
            <div class="flex items-center space-x-4">
                <div class="p-3 bg-white/20 backdrop-blur-sm rounded-full">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
                <div>
                    <h1 class="text-3xl font-bold">Academic Schedule</h1>
                    <p class="text-blue-100 mt-1">Track your assessments, deadlines, and academic progress</p>
                </div>
            </div>

            <!-- Enhanced Assessment Stats -->
            <div class="hidden lg:grid grid-cols-3 gap-4">
                <div class="text-center bg-white/10 backdrop-blur-sm rounded-lg p-4">
                    <div class="text-2xl font-bold text-white">{{ count($assessments) }}</div>
                    <div class="text-sm text-blue-200">Total</div>
                    <div class="text-xs text-blue-300 mt-1">Assessments</div>
                </div>
                <div class="text-center bg-white/10 backdrop-blur-sm rounded-lg p-4">
                    <div class="text-2xl font-bold text-green-300">{{ collect($assessments)->where('status', 'completed')->count() }}</div>
                    <div class="text-sm text-blue-200">Completed</div>
                    <div class="text-xs text-blue-300 mt-1">✓ Done</div>
                </div>
                <div class="text-center bg-white/10 backdrop-blur-sm rounded-lg p-4">
                    <div class="text-2xl font-bold text-yellow-300">{{ collect($assessments)->where('status', 'in_progress')->count() }}</div>
                    <div class="text-sm text-blue-200">In Progress</div>
                    <div class="text-xs text-blue-300 mt-1">⏳ Active</div>
                </div>
            </div>
        </div>

        <!-- Mobile Stats -->
        <div class="lg:hidden mt-6 grid grid-cols-3 gap-3">
            <div class="text-center bg-white/10 backdrop-blur-sm rounded-lg p-3">
                <div class="text-lg font-bold">{{ count($assessments) }}</div>
                <div class="text-xs text-blue-200">Total</div>
            </div>
            <div class="text-center bg-white/10 backdrop-blur-sm rounded-lg p-3">
                <div class="text-lg font-bold text-green-300">{{ collect($assessments)->where('status', 'completed')->count() }}</div>
                <div class="text-xs text-blue-200">Completed</div>
            </div>
            <div class="text-center bg-white/10 backdrop-blur-sm rounded-lg p-3">
                <div class="text-lg font-bold text-yellow-300">{{ collect($assessments)->where('status', 'in_progress')->count() }}</div>
                <div class="text-xs text-blue-200">In Progress</div>
            </div>
        </div>
    </div>

    <!-- Enhanced Filter and Navigation Controls -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
        <!-- Header with toggle -->
        <div class="flex items-center justify-between p-4 border-b border-gray-200 dark:border-gray-700">
            <h3 class="text-lg font-semibold text-gray-900 dark:text-gray-100 flex items-center">
                <svg class="w-5 h-5 mr-2 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4"/>
                </svg>
                Filters & Navigation
            </h3>
            <button @click="showFilters = !showFilters"
                    class="lg:hidden inline-flex items-center px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                <span x-text="showFilters ? 'Hide' : 'Show'"></span>
                <svg class="ml-2 h-4 w-4 transition-transform" :class="{'rotate-180': showFilters}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                </svg>
            </button>
        </div>

        <div class="p-6" :class="{'hidden lg:block': !showFilters}">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between space-y-4 lg:space-y-0">
                <!-- Status Filter -->
                <div class="flex flex-col sm:flex-row sm:items-center space-y-2 sm:space-y-0 sm:space-x-4">
                    <div class="flex items-center space-x-2">
                        <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.207A1 1 0 013 6.5V4z"/>
                        </svg>
                        <label for="statusFilter" class="text-sm font-medium text-gray-700 dark:text-gray-300">Status:</label>
                    </div>
                    <select id="statusFilter" wire:model.live="selectedStatus"
                            class="rounded-lg border-gray-300 dark:border-gray-600 dark:bg-gray-700 bg-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm min-w-48">
                        <option value="">🔍 All Statuses</option>
                        <option value="completed">✅ Completed</option>
                        <option value="in_progress">🔄 In Progress</option>
                        <option value="needs_grading">📝 Needs Grading</option>
                        <option value="pending">⏳ Pending</option>
                    </select>
                </div>

                <!-- Date Navigation -->
                <div class="flex items-center justify-center space-x-2">
                    <button wire:click="previousPeriod"
                            class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                        </svg>
                        Previous
                    </button>

                    <div class="px-6 py-2 bg-indigo-50 dark:bg-indigo-900/30 border border-indigo-200 dark:border-indigo-700 rounded-lg">
                        <div class="text-sm font-medium text-indigo-900 dark:text-indigo-100">
                            {{ $currentDate->format('F Y') }}
                        </div>
                        <div class="text-xs text-indigo-600 dark:text-indigo-300">
                            {{ $currentDate->format('l, jS') }}
                        </div>
                    </div>

                    <button wire:click="nextPeriod"
                            class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-colors">
                        Next
                        <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                        </svg>
                    </button>
                </div>

                <!-- Quick Actions -->
                <div class="flex items-center space-x-2">
                    <button wire:click="refreshCalendar"
                            class="inline-flex items-center px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                    </button>
                    <button onclick="document.querySelector('[x-data]').__x.$data.activeTab = 'self-assessment'"
                            class="inline-flex items-center px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-colors">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        New Assessment
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Calendar Container -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <div class="p-6">
            <div wire:ignore>
                <div id="calendar" class="w-full"></div>
            </div>
        </div>
    </div>

    <!-- Assessment Details Modal -->
    <div x-show="showDetailsModal"
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 overflow-y-auto"
         style="display: none;">
        <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div x-show="showDetailsModal"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="fixed inset-0 transition-opacity"
                 @click="showDetailsModal = false">
                <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
            </div>

            <div x-show="showDetailsModal"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 class="inline-block align-bottom bg-white dark:bg-gray-800 rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">

                <div class="sm:flex sm:items-start">
                    <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-indigo-100 dark:bg-indigo-900 sm:mx-0 sm:h-10 sm:w-10">
                        <svg class="h-6 w-6 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left flex-1">
                        <h3 class="text-lg leading-6 font-medium text-gray-900 dark:text-gray-100" x-text="selectedAssessment?.title || 'Assessment Details'"></h3>
                        <div class="mt-4 space-y-3">
                            <div class="flex items-center text-sm text-gray-600 dark:text-gray-400">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <span x-text="selectedAssessment?.start ? new Date(selectedAssessment.start).toLocaleString() : 'No date specified'"></span>
                            </div>
                            <div class="flex items-center text-sm text-gray-600 dark:text-gray-400">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <span x-text="selectedAssessment?.status || 'Unknown'"></span>
                            </div>
                            <div x-show="selectedAssessment?.subject" class="flex items-center text-sm text-gray-600 dark:text-gray-400">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                </svg>
                                <span x-text="selectedAssessment?.subject"></span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">
                    <button @click="showDetailsModal = false"
                            type="button"
                            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:ml-3 sm:w-auto sm:text-sm transition-colors">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('schedule', () => ({
                calendar: null,

                init() {
                    this.initCalendar();
                },

                initCalendar() {
                    // FullCalendar initialization logic
                }
            }));
                            }
                        },
                        eventDidMount: function(info) {
                            // Add custom styling based on status
                            const event = info.event;
                            const el = info.el;

                            // Add custom classes for better styling
                            el.classList.add('assessment-event');
                            el.style.borderRadius = '6px';
                            el.style.border = 'none';
                            el.style.fontSize = '12px';
                            el.style.fontWeight = '500';
                        }
                    });

                    calendar.render();
                }

                // Listen for assessment updates
                Livewire.on('assessmentsUpdated', () => {
                    if (calendar) {
                        calendar.refetchEvents();
                    }
                });

                // Listen for custom event to show assessment details
                window.addEventListener('show-assessment-details', (event) => {
                    // Use Alpine.js to set the data and show modal
                    Alpine.store('assessmentModal', {
                        selectedAssessment: event.detail,
                        showDetailsModal: true
                    });
                });

                // Initialize calendar
                initializeCalendar();
        });
    </script>
    @endpush
</div>
