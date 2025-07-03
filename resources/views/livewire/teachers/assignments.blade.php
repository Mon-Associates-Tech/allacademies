<div class="assignments-index rounded-lg shadow-sm">
    <!-- Header Section -->
    <div class="bg-white dark:bg-gray-900 shadow-sm border-b border-gray-200 dark:border-gray-700 px-4 sm:px-6 py-6">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between space-y-4 lg:space-y-0">
            <div class="flex-1 min-w-0">
                <h1 class="text-2xl sm:text-3xl font-bold text-gray-900 dark:text-white">My Assignments</h1>
                <p class="text-sm text-gray-600 dark:text-gray-300 mt-1">
                    Manage and track your assignments
                </p>
            </div>
            <div class="flex flex-col sm:flex-row items-stretch sm:items-center space-y-2 sm:space-y-0 sm:space-x-3">
                <a href="{{ route('teachers.assignments.create') }}"
                   class="flex items-center justify-center bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Create Assignment
                </a>
                <button wire:click="resetFilters"
                        class="flex items-center justify-center bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg text-sm font-medium transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                    Reset Filters
                </button>
            </div>
        </div>
    </div>

    <!-- Filters Section -->
    <div class="bg-white dark:bg-gray-900 border-b border-gray-200 dark:border-gray-700 px-4 sm:px-6 py-4">
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-5 gap-4">
            <!-- Search -->
            <div class="xl:col-span-2">
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                    </div>
                    <input type="text"
                           wire:model.live.debounce.300ms="search"
                           placeholder="Search assignments..."
                           class="block w-full pl-10 pr-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg leading-5 bg-white dark:bg-gray-800 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent sm:text-sm">
                </div>
            </div>

            <!-- Status Filter -->
            <div>
                <select wire:model.live="statusFilter"
                        class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent sm:text-sm">
                    <option value="all">All Status</option>
                    <option value="draft">Draft</option>
                    <option value="published">Published</option>
                    <option value="completed">Completed</option>
                </select>
            </div>

            <!-- Subject Filter -->
            <div>
                <select wire:model.live="subjectFilter"
                        class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent sm:text-sm">
                    <option value="all">All Subjects</option>
                    @foreach($subjects as $subject)
                        <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Type Filter -->
            <div>
                <select wire:model.live="typeFilter"
                        class="block w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent sm:text-sm">
                    <option value="all">All Types</option>
                    <option value="quiz">Quiz</option>
                    <option value="examination">Examination</option>
                </select>
            </div>
        </div>
    </div>

    <!-- Sort Options -->
    <div class="bg-gray-50 dark:bg-gray-800 border-b border-gray-200 dark:border-gray-700 px-4 sm:px-6 py-3">
        <div class="flex flex-wrap items-center gap-2">
            <span class="text-sm text-gray-600 dark:text-gray-400">Sort by:</span>
            <button wire:click="sortBy('title')"
                    class="text-sm px-3 py-1 rounded-md transition-colors {{ $sortBy === 'title' ? 'bg-blue-100 dark:bg-blue-900 text-blue-700 dark:text-blue-300' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-200 dark:hover:bg-gray-700' }}">
                Title
                @if($sortBy === 'title')
                    <svg class="w-4 h-4 inline ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="{{ $sortDirection === 'asc' ? 'M5 15l7-7 7 7' : 'M19 9l-7 7-7-7' }}"></path>
                    </svg>
                @endif
            </button>
            <button wire:click="sortBy('created_at')"
                    class="text-sm px-3 py-1 rounded-md transition-colors {{ $sortBy === 'created_at' ? 'bg-blue-100 dark:bg-blue-900 text-blue-700 dark:text-blue-300' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-200 dark:hover:bg-gray-700' }}">
                Created
                @if($sortBy === 'created_at')
                    <svg class="w-4 h-4 inline ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="{{ $sortDirection === 'asc' ? 'M5 15l7-7 7 7' : 'M19 9l-7 7-7-7' }}"></path>
                    </svg>
                @endif
            </button>
            <button wire:click="sortBy('starts_at')"
                    class="text-sm px-3 py-1 rounded-md transition-colors {{ $sortBy === 'starts_at' ? 'bg-blue-100 dark:bg-blue-900 text-blue-700 dark:text-blue-300' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-200 dark:hover:bg-gray-700' }}">
                Start Date
                @if($sortBy === 'starts_at')
                    <svg class="w-4 h-4 inline ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="{{ $sortDirection === 'asc' ? 'M5 15l7-7 7 7' : 'M19 9l-7 7-7-7' }}"></path>
                    </svg>
                @endif
            </button>
            <button wire:click="sortBy('ends_at')"
                    class="text-sm px-3 py-1 rounded-md transition-colors {{ $sortBy === 'ends_at' ? 'bg-blue-100 dark:bg-blue-900 text-blue-700 dark:text-blue-300' : 'text-gray-600 dark:text-gray-400 hover:bg-gray-200 dark:hover:bg-gray-700' }}">
                Due Date
                @if($sortBy === 'ends_at')
                    <svg class="w-4 h-4 inline ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="{{ $sortDirection === 'asc' ? 'M5 15l7-7 7 7' : 'M19 9l-7 7-7-7' }}"></path>
                    </svg>
                @endif
            </button>
        </div>
    </div>

    <!-- Assignments Grid -->
    <div class="bg-gray-50 dark:bg-gray-800 px-4 sm:px-6 py-6">
        @if($assignments->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-2 xl:grid-cols-3 gap-6">
                @foreach($assignments as $assignment)
                    <div
                        class="bg-white dark:bg-gray-900 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 hover:shadow-md transition-shadow duration-200">
                        <!-- Assignment Header -->
                        <div class="p-4 border-b border-gray-200 dark:border-gray-700">
                            <!-- Title and Subject (full width) -->
                            <div class="mb-3">
                                <h3 class="text-lg font-medium text-gray-900 dark:text-white line-clamp-2 leading-6">
                                    {{ $assignment->title }}
                                </h3>
                                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1 truncate">
                                    {{ $assignment->academicSubject->name }}
                                </p>
                            </div>

                            <!-- Badges Row -->
                            <div class="flex items-center justify-between">
                                <div class="flex items-center space-x-2">
                                    <!-- Status Badge -->
                                    @php
                                        $statusColors = [
                                            'draft' => 'bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200',
                                            'published' => 'bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200',
                                            'completed' => 'bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200'
                                        ];
                                    @endphp
                                    <span
                                        class="px-2 py-1 text-xs font-medium rounded-full {{ $statusColors[$assignment->status] ?? 'bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200' }}">
                    {{ ucfirst($assignment->status) }}
                </span>

                                    <!-- Type Badge -->
                                    <span
                                        class="px-2 py-1 text-xs font-medium rounded-full {{ $assignment->type === 'quiz' ? 'bg-purple-100 dark:bg-purple-900 text-purple-800 dark:text-purple-200' : 'bg-orange-100 dark:bg-orange-900 text-orange-800 dark:text-orange-200' }}">
                    {{ ucfirst($assignment->type) }}
                </span>
                                </div>

                                <!-- Quick Actions (Mobile Friendly) -->
                                <div class="flex items-center space-x-1">
                                    <button wire:click="duplicateAssignment({{ $assignment->id }})"
                                            class="p-1.5 text-gray-400 hover:text-gray-600 dark:hover:text-gray-200 transition-colors rounded"
                                            title="Duplicate">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                        </svg>
                                    </button>

                                    <button wire:click="deleteAssignment({{ $assignment->id }})"
                                            wire:confirm="Are you sure you want to delete this assignment?"
                                            class="p-1.5 text-red-400 hover:text-red-600 dark:hover:text-red-300 transition-colors rounded"
                                            title="Delete">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>

                        <!-- Assignment Content -->
                        <div class="p-4">
                            <!-- Description -->
                            <p class="text-sm text-gray-600 dark:text-gray-400 mb-4 line-clamp-2 min-h-[2.5rem]">
                                {{ $assignment->description ?: 'No description available' }}
                            </p>

                            <!-- Assignment Stats -->
                            <div class="grid grid-cols-2 gap-3 mb-4">
                                <div class="text-center p-2 bg-gray-50 dark:bg-gray-800 rounded-lg">
                                    <div class="text-lg font-semibold text-gray-900 dark:text-white">
                                        {{ $assignment->students->count() }}
                                    </div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">Students</div>
                                </div>
                                <div class="text-center p-2 bg-gray-50 dark:bg-gray-800 rounded-lg">
                                    <div class="text-lg font-semibold text-gray-900 dark:text-white">
                                        {{ $assignment->submissions->count() }}
                                    </div>
                                    <div class="text-xs text-gray-500 dark:text-gray-400">Submissions</div>
                                </div>
                            </div>

                            <!-- Dates & Duration in Compact Format -->
                            <div class="space-y-2">
                                @if($assignment->starts_at)
                                    <div class="flex items-center text-xs text-gray-500 dark:text-gray-400">
                                        <svg class="w-3.5 h-3.5 mr-1.5 flex-shrink-0" fill="none" stroke="currentColor"
                                             viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                        <span
                                            class="truncate">Starts: {{ $assignment->starts_at->format('M d, Y g:i A') }}</span>
                                    </div>
                                @endif

                                @if($assignment->ends_at)
                                    <div
                                        class="flex items-center text-xs {{ $assignment->ends_at->isPast() ? 'text-red-500 dark:text-red-400' : 'text-gray-500 dark:text-gray-400' }}">
                                        <svg class="w-3.5 h-3.5 mr-1.5 flex-shrink-0" fill="none" stroke="currentColor"
                                             viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        <span
                                            class="truncate">Due: {{ $assignment->ends_at->format('M d, Y g:i A') }}</span>
                                    </div>
                                @endif

                                @if($assignment->duration_in_minutes)
                                    <div class="flex items-center text-xs text-gray-500 dark:text-gray-400">
                                        <svg class="w-3.5 h-3.5 mr-1.5 flex-shrink-0" fill="none" stroke="currentColor"
                                             viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        <span
                                            class="truncate">Duration: {{ $assignment->duration_in_minutes }} min</span>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <!-- Assignment Actions -->
                        <div
                            class="px-4 py-3 bg-gray-50 dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700 rounded-b-lg">
                            <div class="flex items-center justify-between">
                                <a href="{{ route('teacher.assignments.show', $assignment) }}"
                                   class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-200 text-sm font-medium hover:underline transition-colors">
                                    View Details
                                </a>

                                <!-- Progress/Completion Indicator -->
                                @if($assignment->students->count() > 0)
                                    <div class="text-xs text-gray-500 dark:text-gray-400">
                                        {{ round(($assignment->submissions->count() / $assignment->students->count()) * 100) }}
                                        % complete
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="mt-6">
                {{ $assignments->links() }}
            </div>
        @else
            <!-- Empty State -->
            <div class="text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor"
                     viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No assignments found</h3>
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                    @if($search || $statusFilter !== 'all' || $subjectFilter !== 'all' || $typeFilter !== 'all')
                        Try adjusting your search or filter criteria.
                    @else
                        Get started by creating your first assignment.
                    @endif
                </p>
                <div class="mt-6">
                    @if($search || $statusFilter !== 'all' || $subjectFilter !== 'all' || $typeFilter !== 'all')
                        <button wire:click="resetFilters"
                                class="inline-flex items-center px-4 py-2 border border-gray-300 dark:border-gray-600 shadow-sm text-sm font-medium rounded-md text-gray-700 dark:text-gray-200 bg-white dark:bg-gray-800 hover:bg-gray-50 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            Clear Filters
                        </button>
                    @else
                        <a href="{{ route('teachers.assignments.create') }}"
                           class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            Create Assignment
                        </a>
                    @endif
                </div>
            </div>
        @endif
    </div>
    <style>
        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .scrollbar-hide {
            -ms-overflow-style: none;
            scrollbar-width: none;
        }

        .scrollbar-hide::-webkit-scrollbar {
            display: none;
        }
    </style>
</div>



