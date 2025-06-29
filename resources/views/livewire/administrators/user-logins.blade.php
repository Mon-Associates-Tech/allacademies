<div class="p-6">
    <!-- Header -->
    <div class="mb-6">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-2xl font-bold text-gray-900">User Activity Log</h2>
            <div class="flex space-x-2">
                <button wire:click="toggleViewMode"
                        class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
                    {{ $viewMode === 'sessions' ? 'View Activities' : 'View Sessions' }}
                </button>
                <button wire:click="toggleFilters"
                        class="px-4 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition">
                    {{ $showFilters ? 'Hide Filters' : 'Show Filters' }}
                </button>
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
            <div class="bg-white rounded-lg shadow p-4">
                <div class="text-sm font-medium text-gray-500">Total Sessions</div>
                <div class="text-2xl font-bold text-gray-900">{{ number_format($stats['total_sessions']) }}</div>
            </div>
            <div class="bg-white rounded-lg shadow p-4">
                <div class="text-sm font-medium text-gray-500">Active Sessions</div>
                <div class="text-2xl font-bold text-green-600">{{ number_format($stats['active_sessions']) }}</div>
            </div>
            <div class="bg-white rounded-lg shadow p-4">
                <div class="text-sm font-medium text-gray-500">Unique Users Today</div>
                <div class="text-2xl font-bold text-blue-600">{{ number_format($stats['unique_users_today']) }}</div>
            </div>
            <div class="bg-white rounded-lg shadow p-4">
                <div class="text-sm font-medium text-gray-500">Users Online</div>
                <div class="text-2xl font-bold text-indigo-600">{{ number_format($stats['total_users_online']) }}</div>
            </div>
        </div>

        <!-- Filters -->
        @if($showFilters)
        <div class="bg-gray-50 rounded-lg p-4 mb-6">
            <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                    <input type="text" wire:model.debounce.300ms="searchTerm"
                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                           placeholder="Name, email, IP...">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">From Date</label>
                    <input type="date" wire:model="dateFrom"
                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">To Date</label>
                    <input type="date" wire:model="dateTo"
                           class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                    <select wire:model="statusFilter"
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                        <option value="all">All Sessions</option>
                        <option value="active">Active</option>
                        <option value="completed">Completed</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Logout Type</label>
                    <select wire:model="logoutTypeFilter"
                            class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                        <option value="all">All Types</option>
                        <option value="manual">Manual</option>
                        <option value="session_timeout">Timeout</option>
                        <option value="forced">Forced</option>
                        <option value="browser_close">Browser Close</option>
                    </select>
                </div>
            </div>
        </div>
        @endif
    </div>

    <!-- Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden" wire:poll.30s>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <button wire:click="sortBy('user_id')" class="flex items-center space-x-1 hover:text-gray-700">
                                <span>User</span>
                                @if($sortBy === 'user_id')
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                    </svg>
                                @endif
                            </button>
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            <button wire:click="sortBy('login_at')" class="flex items-center space-x-1 hover:text-gray-700">
                                <span>Login Time</span>
                                @if($sortBy === 'login_at')
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd"/>
                                    </svg>
                                @endif
                            </button>
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Logout Time</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Duration</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Device Info</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">IP Address</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($activities as $activity)
                        <tr class="hover:bg-gray-50 {{ $activity->status === 'active' ? 'bg-green-50' : '' }}">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-8 w-8">
                                        <div class="h-8 w-8 rounded-full bg-gray-300 flex items-center justify-center">
                                            <span class="text-sm font-medium text-gray-700">
                                                {{ strtoupper(substr($activity->user->name, 0, 1)) }}
                                            </span>
                                        </div>
                                    </div>
                                    <div class="ml-3">
                                        <div class="text-sm font-medium text-gray-900">{{ $activity->user->name }}</div>
                                        <div class="text-sm text-gray-500">{{ $activity->user->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($activity->status === 'active')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        <svg class="w-2 h-2 mr-1 fill-current" viewBox="0 0 8 8">
                                            <circle cx="4" cy="4" r="3"/>
                                        </svg>
                                        Active
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                        Ended
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <div>{{ $activity->login_at->format('M d, Y') }}</div>
                                <div class="text-xs text-gray-400">{{ $activity->login_at->format('H:i:s') }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                @if($activity->logout_at)
                                    <div>{{ $activity->logout_at->format('M d, Y') }}</div>
                                    <div class="text-xs text-gray-400">{{ $activity->logout_at->format('H:i:s') }}</div>
                                    @if($activity->logout_type)
                                        <div class="text-xs text-gray-400">({{ $activity->logout_type_display }})</div>
                                    @endif
                                @else
                                    <span class="text-green-600">Active</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                @if($activity->status === 'active')
                                    <span data-active-session data-login-time="{{ $activity->login_at->toISOString() }}" class="text-green-600">
            {{ $activity->formatted_duration }}
        </span>
                                @else
                                    <span class="text-gray-600">
            {{ $activity->formatted_duration }}
        </span>
                                @endif
                            </td>

                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $activity->device_type }}</div>
                                <div class="text-sm text-gray-500">{{ $activity->browser }} on {{ $activity->platform }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <div>{{ $activity->ip_address }}</div>
                                @if($activity->location)
                                    <div class="text-xs text-gray-400">{{ $activity->location }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                @if($activity->status === 'active')
                                    <button wire:click="forceLogout({{ $activity->id }})"
                                            class="text-red-600 hover:text-red-900"
                                            onclick="return confirm('Are you sure you want to force logout this user?')">
                                        Force Logout
                                    </button>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-6 py-12 text-center text-gray-500">
                                <div class="text-center">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                                    </svg>
                                    <h3 class="mt-2 text-sm font-medium text-gray-900">No activity found</h3>
                                    <p class="mt-1 text-sm text-gray-500">No user login activities match your current filters.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    <div class="mt-6">
        {{ $activities->links() }}
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Update active session durations every minute
            setInterval(function() {
                document.querySelectorAll('[data-active-session]').forEach(function(element) {
                    const loginTime = new Date(element.dataset.loginTime);
                    const now = new Date();
                    const diffMinutes = Math.floor((now - loginTime) / (1000 * 60));

                    let displayText;
                    if (diffMinutes < 1) {
                        displayText = 'Just now';
                    } else if (diffMinutes < 60) {
                        displayText = diffMinutes + ' minute' + (diffMinutes > 1 ? 's' : '') + ' ago';
                    } else {
                        const hours = Math.floor(diffMinutes / 60);
                        const minutes = diffMinutes % 60;
                        displayText = hours + ' hour' + (hours > 1 ? 's' : '');
                        if (minutes > 0) {
                            displayText += ' ' + minutes + ' minute' + (minutes > 1 ? 's' : '');
                        }
                        displayText += ' ago';
                    }

                    element.textContent = displayText;
                });
            }, 60000); // Update every minute
        });
    </script>

</div>
