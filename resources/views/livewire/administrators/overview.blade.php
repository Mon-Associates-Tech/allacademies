<div class="p-6 m-0 rounded-lg min-h-screen bg-gray-50 dark:bg-gray-900 transition-colors duration-200">

    @if(Auth::user()->hasAnyRole(['admin', 'owner']))
        <!-- Enhanced Header with Quick Actions -->
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-800 dark:text-white">Admin Dashboard</h1>
                <p class="text-gray-600 dark:text-gray-400 mt-1">Welcome back, {{ auth()->user()->name }}</p>
                <div class="flex items-center gap-4 mt-2">
                    <span class="text-sm text-gray-500 dark:text-gray-400">System Status:</span>
                    <div class="flex items-center gap-2">
                        @foreach($systemHealth as $service => $health)
                            <div class="flex items-center gap-1">
                                <div class="w-2 h-2 rounded-full
                                {{ $health['status'] === 'healthy' ? 'bg-green-400' : ($health['status'] === 'warning' ? 'bg-yellow-400' : 'bg-red-400') }}">
                                </div>
                                <span class="text-xs text-gray-600 dark:text-gray-400 capitalize">{{ $service }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
            <div class="flex gap-3">
                <button
                    class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    Add New User
                </button>
                <button
                    class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Generate Report
                </button>
                <button
                    class="px-4 py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition-colors flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    </svg>
                    System Settings
                </button>
            </div>
        </div>

        <!-- Enhanced Statistics Cards with Change Indicators -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            @foreach($cards as $card)
                <div
                    class="relative overflow-hidden bg-white dark:bg-gray-800 p-6 rounded-xl shadow-sm hover:shadow-md transition-all duration-300 dark:shadow-gray-700 group">
                    <div class="flex items-center justify-between mb-4">
                        <div>
                            <h3 class="text-sm font-medium text-gray-600 dark:text-gray-300">{{ $card['label'] }}</h3>
                            <p class="text-2xl font-bold text-gray-800 dark:text-white">{{ number_format($card['value']) }}</p>
                        </div>
                        <div
                            class="p-3 rounded-full {{ $card['textColor'] }} {{ $card['darkTextColor'] }} opacity-80 group-hover:opacity-100 transition-opacity">
                            {!! $card['icon'] !!}
                        </div>
                    </div>
                    <div class="flex items-center">
                  <span class="text-xs
                      {{ $card['changeType'] === 'positive' ? 'text-green-600 dark:text-green-400' :
                         ($card['changeType'] === 'negative' ? 'text-red-600 dark:text-red-400' : 'text-gray-600 dark:text-gray-400') }}">
                      {{ $card['change'] }}
                  </span>
                    </div>
                    <div
                        class="absolute bottom-0 left-0 w-full h-1 bg-gradient-to-r from-transparent via-{{ explode('-', $card['textColor'])[1] }}-500/50 to-transparent transform scale-x-0 group-hover:scale-x-100 transition-transform duration-300">
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Enhanced Grid Layout -->
        <div class="grid grid-cols-1 lg:grid-cols-1 gap-8">
            <!-- Enhanced Users Management Section -->
            <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-sm dark:shadow-gray-800">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
                    <h2 class="text-xl font-bold text-gray-800 dark:text-white">New Accounts</h2>
                    <div class="flex gap-4 w-full sm:w-auto">
                        <div class="relative flex-1 sm:flex-initial">
                            <input type="text" placeholder="Search users..." wire:model.debounce.300ms="search"
                                   class="w-full px-4 py-2 bg-gray-50 dark:bg-gray-700 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 dark:text-gray-300">
                            <svg class="w-4 h-4 absolute right-3 top-3 text-gray-400" fill="none" stroke="currentColor"
                                 viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </div>
                        <button wire:click="toggleShowAllUsers"
                                class="text-sm px-3 py-2 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors whitespace-nowrap">
                            {{ $showAllUsers ? 'Show Recent' : 'Show More' }}
                        </button>
                    </div>
                </div>

                @if($recentUsers->isEmpty())
                    <div class="text-center py-12">
                        <svg class="w-12 h-12 mx-auto text-gray-400" fill="none" stroke="currentColor"
                             viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                        <p class="mt-4 text-gray-500 dark:text-gray-400">No users found</p>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">
                                    User
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">
                                    Role
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">
                                    Status
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider dark:text-gray-300">
                                    Actions
                                </th>
                            </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700">
                            @foreach($recentUsers as $user)
                                <tr class="hover:bg-gray-50 transition-colors dark:hover:bg-gray-700">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="h-8 w-8 rounded-full flex items-center justify-center font-medium
                                           {{ ['bg-blue-100 text-blue-600 dark:bg-blue-900 dark:text-blue-300',
                                               'bg-green-100 text-green-600 dark:bg-green-900 dark:text-green-300',
                                               'bg-purple-100 text-purple-600 dark:bg-purple-900 dark:text-purple-300',
                                               'bg-pink-100 text-pink-600 dark:bg-pink-900 dark:text-pink-300',
                                               'bg-yellow-100 text-yellow-600 dark:bg-yellow-900 dark:text-yellow-300',
                                               'bg-indigo-100 text-indigo-600 dark:bg-indigo-900 dark:text-indigo-300'][array_rand([0,1,2,3,4,5])] }}">
                                                {{ collect(explode(' ', $user->name))->map(function($name) { return $name[0] ?? ''; })->take(2)->join('') }}
                                            </div>
                                            <div class="ml-4">
                                                <div
                                                    class="font-medium text-gray-900 dark:text-white">{{ $user->name }}</div>
                                                <div
                                                    class="text-sm text-gray-500 dark:text-gray-400">{{ $user->email }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex px-2 py-1 text-xs font-medium rounded-full
                                        {{ $user->getRoleName() === 'admin' ? 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300' :
                                           ($user->getRoleName() === 'teacher' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-300' :
                                           'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300') }}">
                                        {{ ucfirst($user->getRoleName() ?? 'User') }}
                                    </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="w-2 h-2 rounded-full mr-2
                                            {{ $user->is_online ? 'bg-green-400' :
                                               ($user->last_seen_at && $user->last_seen_at->isToday() ? 'bg-yellow-400' : 'bg-gray-400') }}">
                                            </div>
                                            <span class="text-sm text-gray-600 dark:text-gray-400">
                                            {{ $user->is_online ? 'Online' :
                                               ($user->last_seen_at ? $user->last_seen_at->diffForHumans() : 'Never') }}
                                        </span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex space-x-2">
                                            <!-- Impersonate User Button -->
                                            @if($user->id !== auth()->id())
                                                <button wire:click="impersonateUser({{ $user->id }})"
                                                        class="px-3 py-1 text-xs bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 transition-colors flex items-center gap-1"
                                                        title="Act as this user">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor"
                                                         viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                              stroke-width="2"
                                                              d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"/>
                                                    </svg>
                                                    Act As
                                                </button>
                                            @endif

                                            <!-- Edit User Button -->
                                            <button
                                                class="px-3 py-1 text-xs bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                                                Edit
                                            </button>

                                            <!-- Toggle User Status -->
                                            @if($user->id !== auth()->id())
                                                <button wire:click="toggleUserStatus({{ $user->id }})"
                                                        class="px-3 py-1 text-xs {{ isset($user->is_active) && $user->is_active ? 'bg-red-600 hover:bg-red-700' : 'bg-green-600 hover:bg-green-700' }} text-white rounded-lg transition-colors">
                                                    {{ isset($user->is_active) && $user->is_active ? 'Deactivate' : 'Activate' }}
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

            <!-- Enhanced Pending Approvals Section -->
            <div class="bg-white hidden dark:bg-gray-800 p-6 rounded-xl shadow-sm dark:shadow-gray-800">
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
                    <h2 class="text-xl font-bold text-gray-800 dark:text-white">Pending Book Approvals</h2>
                    <div class="flex gap-4">
                        @if($pendingApprovals->isNotEmpty())
                            <button wire:click="approveAll"
                                    class="text-sm px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                                Approve All
                            </button>
                        @endif
                        <a href="#"
                           class="text-sm text-blue-600 hover:text-blue-800 transition-colors dark:text-blue-400 dark:hover:text-blue-300">View
                            All</a>
                    </div>
                </div>

                @if($pendingApprovals->isEmpty())
                    <div class="text-center py-12">
                        <svg class="w-12 h-12 mx-auto text-gray-400" fill="none" stroke="currentColor"
                             viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <p class="mt-4 text-gray-500 dark:text-gray-400">No pending approvals</p>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-100 dark:bg-gray-700">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider dark:text-gray-300">
                                    Book
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider dark:text-gray-300">
                                    Author
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider dark:text-gray-300">
                                    Submitted
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-600 uppercase tracking-wider dark:text-gray-300">
                                    Actions
                                </th>
                            </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200 dark:bg-gray-800 dark:divide-gray-700">
                            @foreach($pendingApprovals as $bookApproval)
                                <tr class="hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="h-10 w-10 flex-shrink-0 overflow-hidden rounded-md">
                                                @if($bookApproval->book->cover_image)
                                                    <img src="{{ $bookApproval->book->cover_image }}"
                                                         alt="{{ $bookApproval->book->title }}"
                                                         class="h-full w-full object-cover">
                                                @else
                                                    <div
                                                        class="h-full w-full bg-gray-200 dark:bg-gray-700 flex items-center justify-center">
                                                        <svg class="w-6 h-6 text-gray-400" fill="none"
                                                             stroke="currentColor"
                                                             viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                  stroke-width="2"
                                                                  d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                                        </svg>
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="ml-4">
                                                <div
                                                    class="text-sm font-medium text-gray-900 dark:text-white">{{ $bookApproval->book->title }}</div>
                                                <div class="text-sm text-gray-500 dark:text-gray-400">
                                                    ISBN: {{ $bookApproval->book->isbn }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div
                                            class="text-sm text-gray-900 dark:text-white">{{ $bookApproval->author }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                        {{ $bookApproval->created_at->diffForHumans() }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                                        <div class="flex space-x-2">
                                            <button wire:click="approveBook({{ $bookApproval->id }})"
                                                    class="px-3 py-1 text-sm bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors">
                                                Approve
                                            </button>
                                            <button wire:click="rejectBook({{ $bookApproval->id }})"
                                                    class="px-3 py-1 text-sm bg-red-600 text-white rounded-lg hover:bg-red-700 transition-colors">
                                                Reject
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    @endif
    <!-- Flash Messages -->
    @if(session()->has('message'))
        <div class="fixed bottom-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50">
            {{ session('message') }}
        </div>
    @endif

    @if(session()->has('error'))
        <div class="fixed bottom-4 right-4 bg-red-500 text-white px-6 py-3 rounded-lg shadow-lg z-50">
            {{ session('error') }}
        </div>
    @endif
</div>
