<div class="space-y-6">
    <!-- Header -->
    <div class="sm:flex sm:items-center sm:justify-between">
        <div>
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Activity Logs</h2>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Track your academic activities and achievements</p>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white dark:bg-gray-800 p-4 rounded-lg border border-gray-200 dark:border-gray-700">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label for="search" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Search</label>
                <input wire:model.debounce.300ms="search" type="text" id="search" 
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white sm:text-sm" 
                       placeholder="Search activities...">
            </div>
            <div>
                <label for="activity_type" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Activity Type</label>
                <select wire:model="activityType" id="activity_type" 
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white sm:text-sm">
                    <option value="all">All Activities</option>
                    <option value="academic">Academic Activities</option>
                    <option value="assessment">Assessments</option>
                    <option value="borrowing">Book Borrowings</option>
                    <option value="subscription">Book Subscriptions</option>
                </select>
            </div>
            <div>
                <label for="date_from" class="block text-sm font-medium text-gray-700 dark:text-gray-300">From Date</label>
                <input wire:model="dateFrom" type="date" id="date_from" 
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white sm:text-sm">
            </div>
            <div>
                <label for="date_to" class="block text-sm font-medium text-gray-700 dark:text-gray-300">To Date</label>
                <input wire:model="dateTo" type="date" id="date_to" 
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 dark:bg-gray-700 dark:border-gray-600 dark:text-white sm:text-sm">
            </div>
        </div>
    </div>

    <!-- Activity Logs -->
    <div class="bg-white dark:bg-gray-800 shadow rounded-lg border border-gray-200 dark:border-gray-700">
        <div class="px-4 py-5 sm:p-6">
            <div class="flow-root">
                <ul role="list" class="-mb-8">
                    @forelse($activityLogs as $index => $log)
                        <li>
                            <div class="relative pb-8">
                                @if(!$loop->last)
                                    <span class="absolute top-5 left-5 -ml-px h-full w-0.5 bg-gray-200 dark:bg-gray-700" aria-hidden="true"></span>
                                @endif
                                
                                <div class="relative flex items-start space-x-3">
                                    <!-- Icon -->
                                    <div class="relative">
                                        <div class="h-10 w-10 rounded-full flex items-center justify-center
                                            @if($log['type'] === 'academic_activity') bg-blue-100 dark:bg-blue-900
                                            @elseif($log['type'] === 'assessment') bg-green-100 dark:bg-green-900
                                            @elseif($log['type'] === 'book_borrowing') bg-yellow-100 dark:bg-yellow-900
                                            @elseif($log['type'] === 'book_subscription') bg-purple-100 dark:bg-purple-900
                                            @else bg-gray-100 dark:bg-gray-700 @endif">
                                            
                                            @if($log['type'] === 'academic_activity')
                                                <svg class="h-5 w-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                </svg>
                                            @elseif($log['type'] === 'assessment')
                                                <svg class="h-5 w-5 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                            @elseif($log['type'] === 'book_borrowing')
                                                <svg class="h-5 w-5 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                                </svg>
                                            @elseif($log['type'] === 'book_subscription')
                                                <svg class="h-5 w-5 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 4V2a1 1 0 011-1h8a1 1 0 011 1v2h3a1 1 0 011 1v1a1 1 0 01-1 1v9a2 2 0 01-2 2H6a2 2 0 01-2-2V7a1 1 0 01-1-1V5a1 1 0 011-1h3z"></path>
                                                </svg>
                                            @endif
                                        </div>
                                    </div>
                                    
                                    <!-- Content -->
                                    <div class="min-w-0 flex-1">
                                        <div class="text-sm">
                                            <div class="flex items-center justify-between">
                                                <p class="font-medium text-gray-900 dark:text-white">{{ $log['title'] }}</p>
                                                <div class="flex items-center space-x-2">
                                                    <!-- Status Badge -->
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                                        @if($log['status'] === 'completed') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                                                        @elseif($log['status'] === 'in_progress') bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200
                                                        @elseif($log['status'] === 'scheduled') bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200
                                                        @elseif($log['status'] === 'pending') bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200
                                                        @elseif($log['status'] === 'approved') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                                                        @elseif($log['status'] === 'active') bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200
                                                        @elseif($log['status'] === 'cancelled') bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200
                                                        @else bg-gray-100 text-gray-800 dark:bg-gray-900 dark:text-gray-200 @endif">
                                                        {{ ucfirst($log['status']) }}
                                                    </span>
                                                    
                                                    <time class="text-xs text-gray-500 dark:text-gray-400">
                                                        {{ $log['date']->format('M d, Y H:i') }}
                                                    </time>
                                                </div>
                                            </div>
                                            
                                            <p class="mt-1 text-gray-600 dark:text-gray-400">{{ $log['description'] }}</p>
                                            
                                            <!-- Metadata -->
                                            @if(!empty($log['metadata']))
                                                <div class="mt-2 flex flex-wrap gap-2">
                                                    @foreach($log['metadata'] as $key => $value)
                                                        @if($value && !in_array($key, ['activity_type', 'book_title', 'author']) )
                                                            <span class="inline-flex items-center px-2 py-1 rounded text-xs bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300">
                                                                <span class="font-medium">{{ ucfirst(str_replace('_', ' ', $key)) }}:</span>
                                                                <span class="ml-1">
                                                                    @if($key === 'percentage')
                                                                        {{ $value }}%
                                                                    @elseif($key === 'score')
                                                                        {{ $value }}/{{ $log['metadata']['max_score'] ?? 0 }}
                                                                    @elseif($key === 'due_date' || $key === 'expires_at' || $key === 'returned_at')
                                                                        {{ $value ? \Carbon\Carbon::parse($value)->format('M d, Y') : 'N/A' }}
                                                                    @else
                                                                        {{ $value }}
                                                                    @endif
                                                                </span>
                                                            </span>
                                                        @endif
                                                    @endforeach
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </li>
                    @empty
                        <li>
                            <div class="text-center py-12">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                </svg>
                                <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No activities found</h3>
                                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Try adjusting your search criteria or date range.</p>
                            </div>
                        </li>
                    @endforelse
                </ul>
            </div>
            
            <!-- Load More / Pagination Info -->
            @if($activityLogs->count() > 0)
                <div class="mt-6 text-center">
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        Showing {{ $activityLogs->count() }} of {{ $totalLogs }} activities
                    </p>
                    @if($activityLogs->count() < $totalLogs)
                        <button wire:click="$set('limit', {{ ($activityLogs->count() + 15) }})" 
                                class="mt-2 inline-flex items-center px-4 py-2 border border-gray-300 shadow-sm text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 dark:bg-gray-700 dark:border-gray-600 dark:text-gray-300 dark:hover:bg-gray-600">
                            Load More
                        </button>
                    @endif
                </div>
            @endif
        </div>
    </div>
</div>