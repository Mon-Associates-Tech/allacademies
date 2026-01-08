<div>
    {{-- Statistics Cards --}}
    @if($showStats && $stats)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            {{-- Total Sessions --}}
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-blue-100 rounded-md p-3">
                        <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                    </div>
                    <div class="ml-5">
                        <p class="text-sm font-medium text-gray-500">Total Sessions</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $stats['total_sessions'] }}</p>
                    </div>
                </div>
            </div>

            {{-- Active Sessions --}}
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-green-100 rounded-md p-3">
                        <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div class="ml-5">
                        <p class="text-sm font-medium text-gray-500">Active Sessions</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $stats['active_sessions'] }}</p>
                    </div>
                </div>
            </div>

            {{-- Average Duration --}}
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-purple-100 rounded-md p-3">
                        <svg class="h-6 w-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div class="ml-5">
                        <p class="text-sm font-medium text-gray-500">Avg Duration</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $stats['average_duration_minutes'] }}m</p>
                    </div>
                </div>
            </div>

            {{-- Unique Devices --}}
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0 bg-orange-100 rounded-md p-3">
                        <svg class="h-6 w-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <div class="ml-5">
                        <p class="text-sm font-medium text-gray-500">Unique Devices</p>
                        <p class="text-2xl font-bold text-gray-900">{{ $stats['unique_devices'] }}</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Last Login Info --}}
        @if($stats['last_login'])
            <div class="bg-blue-50 border-l-4 border-blue-400 p-4 mb-6">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-blue-700">
                            <strong>Last Login:</strong> {{ $stats['last_login']->login_at->format('M d, Y h:i A') }}
                            from {{ $stats['last_login']->ip_address }} ({{ $stats['last_login']->country }})
                            using {{ $stats['last_login']->browser }} on {{ $stats['last_login']->platform }}
                        </p>
                    </div>
                </div>
            </div>
        @endif
    @endif

    {{-- Filters --}}
    <div class="bg-white rounded-lg shadow mb-6 p-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            {{-- Search --}}
            <div class="md:col-span-2">
                <label for="search" class="block text-sm font-medium text-gray-700 mb-2">Search</label>
                <input type="text" id="search"
                       wire:model.live.debounce.300ms="searchTerm"
                       class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                       placeholder="Search by IP, browser, device, country...">
            </div>

            {{-- Status Filter --}}
            <div>
                <label for="statusFilter" class="block text-sm font-medium text-gray-700 mb-2">Status</label>
                <select id="statusFilter" wire:model.live="statusFilter"
                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                    <option value="all">All Sessions</option>
                    <option value="active">Active Only</option>
                    <option value="completed">Completed Only</option>
                </select>
            </div>

            {{-- Date Filter --}}
            <div>
                <label for="dateFilter" class="block text-sm font-medium text-gray-700 mb-2">Period</label>
                <select id="dateFilter" wire:model.live="dateFilter"
                        class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                    <option value="all">All Time</option>
                    <option value="today">Today</option>
                    <option value="week">This Week</option>
                    <option value="month">This Month</option>
                </select>
            </div>
        </div>
    </div>

    {{-- Activity Table --}}
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Session Info
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Device & Location
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Login Time
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Duration
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Status
                    </th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Actions
                    </th>
                </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                @forelse($activities as $activity)
                    <tr class="hover:bg-gray-50">
                        {{-- Session Info --}}
                        <td class="px-6 py-4">
                            <div class="text-sm font-medium text-gray-900">
                                {{ $activity->browser }}
                            </div>
                            <div class="text-sm text-gray-500">
                                {{ $activity->platform }} - {{ $activity->device_type }}
                            </div>
                        </td>

                        {{-- Device & Location --}}
                        <td class="px-6 py-4">
                            <div class="text-sm text-gray-900">
                                {{ $activity->ip_address }}
                            </div>
                            <div class="text-sm text-gray-500">
                                {{ $activity->country }}
                                @if($activity->location)
                                    - {{ $activity->location }}
                                @endif
                            </div>
                        </td>

                        {{-- Login Time --}}
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">
                                {{ $activity->login_at->format('M d, Y') }}
                            </div>
                            <div class="text-sm text-gray-500">
                                {{ $activity->login_at->format('h:i A') }}
                            </div>
                        </td>

                        {{-- Duration --}}
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">
                                {{ $activity->duration }}
                            </div>
                            @if($activity->logout_at)
                                <div class="text-xs text-gray-500">
                                    {{ $activity->logout_type_display }}
                                </div>
                            @endif
                        </td>

                        {{-- Status --}}
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($activity->status === 'active')
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                        Active
                                    </span>
                            @else
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                        Ended
                                    </span>
                            @endif
                        </td>

                        {{-- Actions --}}
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            @if($activity->status === 'active' && auth()->user()->hasAnyRole(['administrator', 'super_admin']))
                                <button wire:click="forceLogout({{ $activity->id }})"
                                        wire:confirm="Are you sure you want to terminate this session?"
                                        class="text-red-600 hover:text-red-900">
                                    Terminate
                                </button>
                            @else
                                <span class="text-gray-400">—</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-6 py-12 text-center">
                            <div class="text-gray-500">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                <p class="mt-2 text-sm">No login activities found</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $activities->links() }}
        </div>
    </div>
</div>
