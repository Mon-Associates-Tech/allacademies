<div class="container mx-auto px-4 py-6">
    <!-- Header -->
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 dark:text-gray-100 mb-2">My Assignments</h1>
            <p class="text-gray-600 dark:text-gray-400">Complete your assignments and track your progress</p>
        </div>
        <div class="mt-4 lg:mt-0 flex items-center space-x-4">
            <div class="text-sm text-gray-500 dark:text-gray-400">
                {{ $assignments->total() }} assignments available
            </div>
            <!-- Quick Stats -->
            <div class="flex items-center space-x-4">
                <div class="text-xs">
                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300">
                        {{ $assignments->where(fn($a) => $this->getAssignmentStatus($a) === 'not_started')->count() }} Available
                    </span>
                </div>
                <div class="text-xs">
                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300">
                        {{ $assignments->where(fn($a) => $this->getAssignmentStatus($a) === 'completed')->count() }} Completed
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters and Search -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4">
            <!-- Search -->
            <div class="relative lg:col-span-2">
                <input
                    type="text"
                    wire:model.live.debounce.300ms="search"
                    placeholder="Search assignments..."
                    class="w-full pl-10 pr-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-violet-500 focus:border-transparent dark:bg-gray-700 dark:text-gray-300"
                >
                <svg class="absolute left-3 top-2.5 h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
            </div>

            <!-- Status Filter -->
            <div>
                <select
                    wire:model.live="statusFilter"
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-violet-500 focus:border-transparent dark:bg-gray-700 dark:text-gray-300"
                >
                    <option value="all">All Status</option>
                    <option value="pending">Pending</option>
                    <option value="completed">Completed</option>
                </select>
            </div>

            <!-- Subject Filter -->
            <div>
                <select
                    wire:model.live="subjectFilter"
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-violet-500 focus:border-transparent dark:bg-gray-700 dark:text-gray-300"
                >
                    <option value="all">All Subjects</option>
                    @foreach ($subjects as $subject)
                        <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Sort -->
            <div>
                <select
                    wire:model.live="sortBy"
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-violet-500 focus:border-transparent dark:bg-gray-700 dark:text-gray-300"
                >
                    <option value="created_at">Sort by Date</option>
                    <option value="title">Sort by Title</option>
                    <option value="ends_at">Sort by Deadline</option>
                </select>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="mt-4 flex flex-wrap gap-2">
            <button
                wire:click="resetFilters"
                class="inline-flex items-center px-3 py-1 text-sm bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-full hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors"
            >
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                </svg>
                Reset Filters
            </button>
        </div>
    </div>

    <!-- Assignments Table -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <!-- Table Header -->
        <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700">
            <div class="grid grid-cols-12 gap-4 text-xs font-semibold text-gray-500 dark:text-gray-400 uppercase tracking-wider">
                <div class="col-span-4">
                    <button
                        wire:click="sortBy('title')"
                        class="flex items-center space-x-1 hover:text-gray-700 dark:hover:text-gray-300 transition-colors"
                    >
                        <span>Assignment</span>
                        @if ($sortBy === 'title')
                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                @if ($sortDirection === 'asc')
                                    <path d="M3 3a1 1 0 000 2h11a1 1 0 100-2H3zM3 7a1 1 0 000 2h7a1 1 0 100-2H3zM3 11a1 1 0 100 2h4a1 1 0 100-2H3zM15 8a1 1 0 10-2 0v5.586l-1.293-1.293a1 1 0 00-1.414 1.414l3 3a1 1 0 001.414 0l3-3a1 1 0 00-1.414-1.414L15 13.586V8z"/>
                                @else
                                    <path d="M3 3a1 1 0 000 2h11a1 1 0 100-2H3zM3 7a1 1 0 000 2h7a1 1 0 100-2H3zM3 11a1 1 0 100 2h4a1 1 0 100-2H3zM13 16a1 1 0 102 0v-5.586l1.293 1.293a1 1 0 001.414-1.414l-3-3a1 1 0 00-1.414 0l-3 3a1 1 0 101.414 1.414L13 10.414V16z"/>
                                @endif
                            </svg>
                        @endif
                    </button>
                </div>
                <div class="col-span-2">Subject</div>
                <div class="col-span-1">Status</div>
                <div class="col-span-1">Progress</div>
                <div class="col-span-2">
                    <button
                        wire:click="sortBy('ends_at')"
                        class="flex items-center space-x-1 hover:text-gray-700 dark:hover:text-gray-300 transition-colors"
                    >
                        <span>Deadline</span>
                        @if ($sortBy === 'ends_at')
                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                @if ($sortDirection === 'asc')
                                    <path d="M3 3a1 1 0 000 2h11a1 1 0 100-2H3zM3 7a1 1 0 000 2h7a1 1 0 100-2H3zM3 11a1 1 0 100 2h4a1 1 0 100-2H3zM15 8a1 1 0 10-2 0v5.586l-1.293-1.293a1 1 0 00-1.414 1.414l3 3a1 1 0 001.414 0l3-3a1 1 0 00-1.414-1.414L15 13.586V8z"/>
                                @else
                                    <path d="M3 3a1 1 0 000 2h11a1 1 0 100-2H3zM3 7a1 1 0 000 2h7a1 1 0 100-2H3zM3 11a1 1 0 100 2h4a1 1 0 100-2H3zM13 16a1 1 0 102 0v-5.586l1.293 1.293a1 1 0 001.414-1.414l-3-3a1 1 0 00-1.414 0l-3 3a1 1 0 101.414 1.414L13 10.414V16z"/>
                                @endif
                            </svg>
                        @endif
                    </button>
                </div>
                <div class="col-span-2">Actions</div>
            </div>
        </div>

        <!-- Table Body -->
        <div class="divide-y divide-gray-200 dark:divide-gray-700">
            @forelse ($assignments as $assignment)
                @php
                    $status = $this->getAssignmentStatus($assignment);
                    $progress = $this->getAssignmentProgress($assignment);
                    $isOverdue = $assignment->ends_at < now();
                    $timeLeft = now()->diffInHours($assignment->ends_at, true);
                @endphp

                <div class="px-6 py-4 hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                    <div class="grid grid-cols-12 gap-4 items-center">
                        <!-- Assignment Info -->
                        <div class="col-span-4">
                            <div class="flex items-start space-x-3">
                                <!-- Assignment Icon -->
                                <div class="flex-shrink-0">
                                    <div class="w-10 h-10 bg-gradient-to-br from-violet-500 to-purple-600 rounded-lg flex items-center justify-center">
                                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                                        </svg>
                                    </div>
                                </div>

                                <!-- Assignment Details -->
                                <div class="flex-1 min-w-0">
                                    <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100 truncate">
                                        {{ $assignment->title }}
                                    </h3>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                                        {{ $assignment->duration_in_minutes }} min
                                        @if ($assignment->total_marks)
                                            • {{ $assignment->total_marks }} marks
                                        @endif
                                    </p>
                                    @if ($assignment->description)
                                        <p class="text-xs text-gray-600 dark:text-gray-400 mt-1 line-clamp-1">
                                            {{ Str::limit($assignment->description, 60) }}
                                        </p>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Subject -->
                        <div class="col-span-2">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300">
                                {{ $assignment->academicSubject->name ?? 'Unknown' }}
                            </span>
                        </div>

                        <!-- Status -->
                        <div class="col-span-1">
                            @if ($status === 'completed')
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300">
                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                    Done
                                </span>
                            @elseif ($status === 'in_progress')
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-300">
                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                                    </svg>
                                    Active
                                </span>
                            @elseif ($isOverdue)
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300">
                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                    </svg>
                                    Late
                                </span>
                            @else
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300">
                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1zm0 5a1 1 0 000 2h8a1 1 0 100-2H6z" clip-rule="evenodd"></path>
                                    </svg>
                                    Open
                                </span>
                            @endif
                        </div>

                        <!-- Progress -->
                        <div class="col-span-1">
                            @if ($progress > 0)
                                <div class="flex items-center space-x-2">
                                    <div class="flex-1 bg-gray-200 dark:bg-gray-700 rounded-full h-2">
                                        <div
                                            class="bg-gradient-to-r from-violet-500 to-purple-600 h-2 rounded-full transition-all duration-300"
                                            style="width: {{ $progress }}%"
                                        ></div>
                                    </div>
                                    <span class="text-xs text-gray-500 dark:text-gray-400 font-medium">{{ $progress }}%</span>
                                </div>
                            @else
                                <span class="text-xs text-gray-400 dark:text-gray-500">—</span>
                            @endif
                        </div>

                        <!-- Deadline -->
                        <div class="col-span-2">
                            <div class="text-sm text-gray-900 dark:text-gray-100">
                                {{ $assignment->ends_at->format('M j, Y') }}
                            </div>
                            <div class="text-xs {{ $isOverdue ? 'text-red-600 dark:text-red-400' : ($timeLeft < 24 ? 'text-orange-600 dark:text-orange-400' : 'text-gray-500 dark:text-gray-400') }}">
                                @if ($isOverdue)
                                    Overdue
                                @elseif ($timeLeft < 1)
                                    < 1 hour left
                                @elseif ($timeLeft < 24)
                                    {{ getTimeRemaining($assignment->ends_at) }}
                                @else
                                    {{ ceil($timeLeft / 24) }} days left
                                @endif
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="col-span-2">
                            <div class="flex items-center space-x-2">
                                @if ($status === 'completed')
                                    <button
                                        wire:click="viewAssignment({{ $assignment->id }})"
                                        class="inline-flex items-center px-3 py-1 text-xs font-medium text-violet-600 dark:text-violet-400 border border-violet-300 dark:border-violet-600 rounded-md hover:bg-violet-50 dark:hover:bg-violet-900/20 transition-colors"
                                    >
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                        </svg>
                                        Results
                                    </button>
                                @elseif ($status === 'in_progress')
                                    <button
                                        wire:click="startAssignment({{ $assignment->id }})"
                                        class="inline-flex items-center px-3 py-1 text-xs font-medium text-white bg-gradient-to-r from-violet-500 to-purple-600 rounded-md hover:from-violet-600 hover:to-purple-700 transition-all duration-200 shadow-sm"
                                    >
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h1m4 0h1m-6 4h8m-5-4h.01M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"></path>
                                        </svg>
                                        Continue
                                    </button>
                                @elseif (!$isOverdue)
                                    <button
                                        wire:click="startAssignment({{ $assignment }})"
                                        class="inline-flex items-center px-3 py-1 text-xs font-medium text-white bg-gradient-to-r from-violet-500 to-purple-600 rounded-md hover:from-violet-600 hover:to-purple-700 transition-all duration-200 shadow-sm"
                                    >
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h1m4 0h1m-6 4h8m-5-4h.01M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"></path>
                                        </svg>
                                        Start
                                    </button>
                                @else
                                    <button
                                        disabled
                                        class="inline-flex items-center px-3 py-1 text-xs font-medium text-gray-500 dark:text-gray-400 bg-gray-100 dark:bg-gray-700 rounded-md cursor-not-allowed"
                                    >
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        Expired
                                    </button>
                                @endif

                                <button
                                    wire:click="viewAssignment({{ $assignment->id }})"
                                    class="inline-flex items-center px-3 py-1 text-xs font-medium text-gray-600 dark:text-gray-400 border border-gray-300 dark:border-gray-600 rounded-md hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors"
                                >
                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                    View
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="px-6 py-12 text-center">
                    <svg class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-100">No assignments found</h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        @if ($search || $statusFilter !== 'all' || $subjectFilter !== 'all')
                            Try adjusting your search or filters.
                        @else
                            No assignments have been assigned to you yet.
                        @endif
                    </p>
                </div>
            @endforelse
        </div>
    </div>

    <!-- Pagination -->
    @if ($assignments->hasPages())
        <div class="mt-6">
            {{ $assignments->links() }}
        </div>
    @endif

    <!-- Assignment Details Modal -->
    @if ($showDetails && $selectedAssignment)
        <div class="fixed inset-0 z-50 overflow-y-auto" wire:click="closeDetails" x-data="{ activeTab: 'overview' }">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 transition-opacity bg-gray-900 bg-opacity-50 backdrop-blur-sm" aria-hidden="true"></div>

                <div class="inline-block w-full max-w-5xl my-8 overflow-hidden text-left align-middle transition-all transform bg-white dark:bg-gray-800 shadow-2xl rounded-xl" wire:click.stop>
                    @php
                        $status = $this->getAssignmentStatus($selectedAssignment);
                        $progress = $this->getAssignmentProgress($selectedAssignment);
                        $isOverdue = $selectedAssignment->ends_at < now();
                        $isUpcoming = $selectedAssignment->starts_at > now();
                        $timeLeft = now()->diffInHours($selectedAssignment->ends_at, false);
                        $timeUntilStart = $isUpcoming ? now()->diffInHours($selectedAssignment->starts_at, false) : 0;
                        $questionStats = $selectedAssignment->getQuestionStatistics();
                    @endphp

                        <!-- Enhanced Modal Header -->
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700 bg-gradient-to-r from-violet-50 to-purple-50 dark:from-gray-700 dark:to-gray-700">
                        <div class="flex items-start justify-between">
                            <div class="flex items-start space-x-4">
                                <!-- Dynamic Status Icon -->
                                <div class="flex-shrink-0">
                                    @if ($status === 'completed')
                                        <div class="w-12 h-12 bg-gradient-to-br from-green-500 to-emerald-600 rounded-xl flex items-center justify-center shadow-lg">
                                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                        </div>
                                    @elseif ($status === 'in_progress')
                                        <div class="w-12 h-12 bg-gradient-to-br from-orange-500 to-yellow-600 rounded-xl flex items-center justify-center shadow-lg">
                                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                        </div>
                                    @elseif ($isOverdue)
                                        <div class="w-12 h-12 bg-gradient-to-br from-red-500 to-pink-600 rounded-xl flex items-center justify-center shadow-lg">
                                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                        </div>
                                    @else
                                        <div class="w-12 h-12 bg-gradient-to-br from-violet-500 to-purple-600 rounded-xl flex items-center justify-center shadow-lg">
                                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                            </svg>
                                        </div>
                                    @endif
                                </div>

                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center space-x-3 mb-2">
                                        <h3 class="text-xl font-bold text-gray-900 dark:text-gray-100 truncate">
                                            {{ $selectedAssignment->title }}
                                        </h3>
                                        <!-- Assignment Type Badge -->
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $selectedAssignment->type === 'quiz' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300' : 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-300' }}">
                                        {{ ucfirst($selectedAssignment->type) }}
                                    </span>
                                    </div>

                                    <div class="flex flex-wrap items-center gap-4 text-sm text-gray-600 dark:text-gray-400">
                                        <div class="flex items-center space-x-1">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                            </svg>
                                            <span>{{ $selectedAssignment->academicSubject->name ?? 'Unknown Subject' }}</span>
                                        </div>
                                        <div class="flex items-center space-x-1">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                            </svg>
                                            <span>{{ $selectedAssignment->user->name ?? 'Unknown Teacher' }}</span>
                                        </div>
                                        @if ($questionStats['total_questions'] > 0)
                                            <div class="flex items-center space-x-1">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                                <span>{{ $questionStats['total_questions'] }} Questions</span>
                                            </div>
                                        @endif
                                    </div>

                                    <!-- Status Badge -->
                                    <div class="mt-3">
                                        @if ($status === 'completed')
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300">
                                            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                            </svg>
                                            Completed
                                        </span>
                                        @elseif ($status === 'in_progress')
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-300">
                                            <svg class="w-4 h-4 mr-2 animate-pulse" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                                            </svg>
                                            In Progress
                                        </span>
                                        @elseif ($isOverdue)
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300">
                                            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92z" clip-rule="evenodd"></path>
                                            </svg>
                                            Overdue
                                        </span>
                                        @elseif ($isUpcoming)
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300">
                                            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M6 2a1 1 0 00-1 1v1H4a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V6a2 2 0 00-2-2h-1V3a1 1 0 10-2 0v1H7V3a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                            </svg>
                                            Starts {{ $timeUntilStart < 1 ? 'soon' : 'in ' . ceil($timeUntilStart / 24) . ' day(s)' }}
                                        </span>
                                        @else
                                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-violet-100 text-violet-800 dark:bg-violet-900 dark:text-violet-300">
                                            <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                            </svg>
                                            Available
                                        </span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <button
                                wire:click="closeDetails"
                                class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors"
                            >
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>

                        <!-- Progress Bar -->
                        @if ($progress > 0)
                            <div class="mt-4">
                                <div class="flex items-center justify-between text-sm mb-2">
                                    <span class="text-gray-600 dark:text-gray-300">Progress</span>
                                    <span class="font-semibold text-gray-900 dark:text-gray-100">{{ $progress }}%</span>
                                </div>
                                <div class="w-full bg-gray-200 dark:bg-gray-600 rounded-full h-2.5">
                                    <div class="bg-gradient-to-r from-violet-500 to-purple-600 h-2.5 rounded-full transition-all duration-500" style="width: {{ $progress }}%"></div>
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- Tab Navigation -->
                    <div class="border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-750">
                        <nav class="flex px-6" aria-label="Tabs">
                            <button
                                @click="activeTab = 'overview'"
                                :class="activeTab === 'overview' ? 'border-violet-500 text-violet-600 dark:text-violet-400' : 'border-transparent text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-500 dark:text-gray-400 dark:hover:text-gray-300'"
                                class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm mr-8 transition-colors duration-200"
                            >
                                <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                                Overview
                            </button>
                            @if ($questionStats['total_questions'] > 0)
                                <button
                                    @click="activeTab = 'questions'"
                                    :class="activeTab === 'questions' ? 'border-violet-500 text-violet-600 dark:text-violet-400' : 'border-transparent text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-500 dark:text-gray-400 dark:hover:text-gray-300'"
                                    class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm mr-8 transition-colors duration-200"
                                >
                                    <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    Questions ({{ $questionStats['total_questions'] }})
                                </button>
                            @endif
                            <button
                                @click="activeTab = 'timeline'"
                                :class="activeTab === 'timeline' ? 'border-violet-500 text-violet-600 dark:text-violet-400' : 'border-transparent text-gray-500 hover:text-gray-700 dark:hover:text-gray-300 hover:border-gray-300 dark:hover:border-gray-500 dark:text-gray-400 dark:hover:text-gray-300'"
                                class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm mr-8 transition-colors duration-200"
                            >
                                <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Timeline
                            </button>
                        </nav>
                    </div>

                    <!-- Modal Content -->
                    <div class="max-h-96 overflow-y-auto">
                        <!-- Overview Tab -->
                        <div x-show="activeTab === 'overview'" class="p-6">
                            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                                <!-- Main Content -->
                                <div class="lg:col-span-2 space-y-6">
                                    <!-- Description -->
                                    @if ($selectedAssignment->description)
                                        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                                            <h4 class="text-sm font-semibold text-gray-900 dark:text-gray-100 mb-3 flex items-center">
                                                <svg class="w-4 h-4 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7"></path>
                                                </svg>
                                                Description
                                            </h4>
                                            <p class="text-sm text-gray-600 dark:text-gray-300 leading-relaxed">
                                                {{ $selectedAssignment->description }}
                                            </p>
                                        </div>
                                    @endif

                                    <!-- Instructions -->
                                    @if ($selectedAssignment->instructions)
                                        <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-700 rounded-lg p-4">
                                            <h4 class="text-sm font-semibold text-blue-900 dark:text-blue-300 mb-3 flex items-center">
                                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                                Instructions
                                            </h4>
                                            <div class="text-sm text-blue-800 dark:text-blue-200 prose prose-sm prose-blue max-w-none dark:prose-invert">
                                                {!! $selectedAssignment->instructions !!}
                                            </div>
                                        </div>
                                    @endif

                                    <!-- Quick Stats -->
                                    <div class="grid grid-cols-2 gap-4">
                                        <div class="bg-violet-50 dark:bg-violet-900/20 rounded-lg p-4 text-center">
                                            <div class="text-2xl font-bold text-violet-600 dark:text-violet-400">{{ $selectedAssignment->duration_in_minutes }}</div>
                                            <div class="text-sm text-violet-500 dark:text-violet-300">Minutes</div>
                                        </div>
                                        @if ($selectedAssignment->total_marks)
                                            <div class="bg-purple-50 dark:bg-purple-900/20 rounded-lg p-4 text-center">
                                                <div class="text-2xl font-bold text-purple-600 dark:text-purple-400">{{ $selectedAssignment->total_marks }}</div>
                                                <div class="text-sm text-purple-500 dark:text-purple-300">Total Marks</div>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                <!-- Sidebar -->
                                <div class="space-y-4">
                                    <!-- Assignment Details -->
                                    <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4">
                                        <h4 class="text-sm font-semibold text-gray-900 dark:text-gray-100 mb-3">Assignment Details</h4>
                                        <div class="space-y-3 text-sm">
                                            <div class="flex justify-between items-center">
                                                <span class="text-gray-500 dark:text-gray-400">Type</span>
                                                <span class="font-medium text-gray-900 dark:text-gray-100 capitalize">{{ $selectedAssignment->type }}</span>
                                            </div>
                                            <div class="flex justify-between items-center">
                                                <span class="text-gray-500 dark:text-gray-400">Randomized</span>
                                                <span class="font-medium text-gray-900 dark:text-gray-100">
                                                {{ $selectedAssignment->is_randomized ? 'Yes' : 'No' }}
                                            </span>
                                            </div>
                                            @if ($selectedAssignment->is_randomized)
                                                <div class="text-xs text-amber-600 dark:text-amber-400 bg-amber-50 dark:bg-amber-900/20 p-2 rounded">
                                                    Each student will receive different questions
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Questions Tab -->
                        @if ($questionStats['total_questions'] > 0)
                            <div x-show="activeTab === 'questions'" class="p-6">
                                <div class="space-y-6">
                                    <!-- Question Summary -->
                                    <div class="bg-gradient-to-r from-violet-50 to-purple-50 dark:from-violet-900/20 dark:to-purple-900/20 rounded-lg p-4">
                                        <h4 class="text-lg font-semibold text-gray-900 dark:text-gray-100 mb-4">Question Breakdown</h4>
                                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                            @foreach ($questionStats['by_type'] as $type => $count)
                                                <div class="bg-white dark:bg-gray-700 rounded-lg p-4 text-center">
                                                    <div class="text-2xl font-bold text-violet-600 dark:text-violet-400">{{ $count }}</div>
                                                    <div class="text-sm text-gray-600 dark:text-gray-300 capitalize">
                                                        {{ str_replace('_', ' ', $type) }}
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>

                                    <!-- Estimated Duration -->
                                    @if ($questionStats['estimated_duration'] > 0)
                                        <div class="bg-blue-50 dark:bg-blue-900/20 rounded-lg p-4">
                                            <h5 class="font-medium text-blue-900 dark:text-blue-300 mb-2">Estimated Time</h5>
                                            <div class="flex items-center space-x-2">
                                                <svg class="w-4 h-4 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                                <span class="text-blue-800 dark:text-blue-200">
                                                Approximately {{ $questionStats['estimated_duration'] }} minutes for questions
                                            </span>
                                            </div>
                                            <div class="text-xs text-blue-600 dark:text-blue-300 mt-1">
                                                Total assignment time: {{ $selectedAssignment->duration_in_minutes }} minutes
                                            </div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endif

                        <!-- Timeline Tab -->
                        <div x-show="activeTab === 'timeline'" class="p-6">
                            <div class="space-y-6">
                                <!-- Timeline -->
                                <div class="relative">
                                    <div class="absolute left-4 top-6 bottom-6 w-0.5 bg-gray-300 dark:bg-gray-600"></div>

                                    <!-- Start Time -->
                                    <div class="relative flex items-start space-x-4 pb-6">
                                        <div class="flex-shrink-0 w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center">
                                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <div class="text-sm font-medium text-gray-900 dark:text-gray-100">Assignment Opens</div>
                                            <div class="text-sm text-gray-600 dark:text-gray-400">{{ $selectedAssignment->starts_at->format('M j, Y \a\t g:i A') }}</div>
                                            @if ($isUpcoming)
                                                <div class="text-xs text-blue-600 dark:text-blue-400 mt-1">
                                                    {{ $timeUntilStart < 1 ? 'Opens in less than an hour' : 'Opens in ' . ceil($timeUntilStart) . ' hour(s)' }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>

                                    <!-- Current Time Indicator -->
                                    @if (!$isUpcoming && !$isOverdue)
                                        <div class="relative flex items-start space-x-4 pb-6">
                                            <div class="flex-shrink-0 w-8 h-8 bg-green-500 rounded-full flex items-center justify-center animate-pulse">
                                                <div class="w-2 h-2 bg-white rounded-full"></div>
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <div class="text-sm font-medium text-green-600 dark:text-green-400">Currently Active</div>
                                                <div class="text-sm text-gray-600 dark:text-gray-400">{{ now()->format('M j, Y \a\t g:i A') }}</div>
                                            </div>
                                        </div>
                                    @endif

                                    <!-- End Time -->
                                    <div class="relative flex items-start space-x-4">
                                        <div class="flex-shrink-0 w-8 h-8 {{ $isOverdue ? 'bg-red-500' : 'bg-orange-500' }} rounded-full flex items-center justify-center">
                                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <div class="text-sm font-medium text-gray-900 dark:text-gray-100">Assignment Closes</div>
                                            <div class="text-sm text-gray-600 dark:text-gray-400">{{ $selectedAssignment->ends_at->format('M j, Y \a\t g:i A') }}</div>
                                            <div class="text-xs {{ $isOverdue ? 'text-red-600 dark:text-red-400' : ($timeLeft < 24 ? 'text-orange-600 dark:text-orange-400' : 'text-gray-500 dark:text-gray-400') }} mt-1">
                                                @if ($isOverdue)
                                                    Assignment ended
                                                @elseif ($timeLeft < 1)
                                                    Less than 1 hour remaining
                                                @elseif ($timeLeft < 24)
                                                    {{ ceil($timeLeft) }} hour(s) remaining
                                                @else
                                                    {{ ceil($timeLeft / 24) }} day(s) remaining
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Enhanced Modal Footer -->
                    <div class="px-6 py-4 bg-gray-50 dark:bg-gray-700 border-t border-gray-200 dark:border-gray-600">
                        <div class="flex flex-col sm:flex-row justify-between items-center space-y-3 sm:space-y-0 sm:space-x-3">
                            <!-- Left side - Additional info -->
                            <div class="flex items-center space-x-4 text-sm text-gray-500 dark:text-gray-400">
                                @if ($status === 'completed')
                                    <span class="flex items-center">
                                    <svg class="w-4 h-4 mr-1 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                    Assignment completed
                                </span>
                                @elseif ($selectedAssignment->is_randomized)
                                    <span class="flex items-center">
                                    <svg class="w-4 h-4 mr-1 text-amber-500" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M4 2a1 1 0 011 1v2.101a7.002 7.002 0 0111.601 2.566 1 1 0 11-1.885.666A5.002 5.002 0 005.999 7H9a1 1 0 010 2H4a1 1 0 01-1-1V3a1 1 0 011-1zm.008 9.057a1 1 0 011.276.61A5.002 5.002 0 0014.001 13H11a1 1 0 110-2h5a1 1 0 011 1v5a1 1 0 11-2 0v-2.101a7.002 7.002 0 01-11.601-2.566 1 1 0 01.61-1.276z" clip-rule="evenodd"></path>
                                    </svg>
                                    Randomized questions
                                </span>
                                @endif
                            </div>

                            <!-- Right side - Action buttons -->
                            <div class="flex items-center space-x-3">
                                <button
                                    wire:click="closeDetails"
                                    class="px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-600 border border-gray-300 dark:border-gray-500 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-500 transition-colors duration-200"
                                >
                                    Close
                                </button>

                                @if ($status === 'completed')
                                    <button
                                        wire:click="viewAssignment({{ $selectedAssignment->id }})"
                                        class="px-4 py-2 text-sm font-medium text-violet-600 dark:text-violet-400 bg-violet-50 dark:bg-violet-900/20 border border-violet-200 dark:border-violet-600 rounded-lg hover:bg-violet-100 dark:hover:bg-violet-900/30 transition-colors duration-200"
                                    >
                                        View Results
                                    </button>
                                @elseif (!$isOverdue)
                                    <button
                                        wire:click="startAssignment({{ $selectedAssignment->id }})"
                                        class="px-6 py-2 text-sm font-medium text-white bg-gradient-to-r from-violet-500 to-purple-600 rounded-lg hover:from-violet-600 hover:to-purple-700 transform hover:scale-105 transition-all duration-200 shadow-lg hover:shadow-xl"
                                        @if ($isUpcoming) disabled title="Assignment hasn't started yet" @endif
                                    >
                                        @if ($status === 'in_progress')
                                            <svg class="w-4 h-4 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h1m4 0h1m-6 4h8m-5-4h.01M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"></path>
                                            </svg>
                                            Continue Assignment
                                        @elseif ($isUpcoming)
                                            <svg class="w-4 h-4 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            Not Available Yet
                                        @else
                                            <svg class="w-4 h-4 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path>
                                            </svg>
                                            Start Assignment
                                        @endif
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
