<div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
    <!-- Header -->
    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
        <div class="flex items-center justify-between">
            <h3 class="text-lg font-semibold text-gray-900">User Impersonation</h3>
            @if(session()->has('impersonator'))
                <button wire:click="stopImpersonation"
                        class="inline-flex items-center px-3 py-2 border border-red-300 text-sm leading-4 font-medium rounded-md text-red-700 bg-red-50 hover:bg-red-100 transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                    Stop Impersonating
                </button>
            @endif
        </div>
        <p class="text-sm text-gray-600 mt-1">Select a user to impersonate and act as them throughout the system</p>
    </div>

    <!-- Search and Filter -->
    <div class="px-6 py-4 border-b border-gray-200">
        <div class="flex flex-col sm:flex-row gap-4">
            <div class="flex-1">
                <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Search Users</label>
                <input type="text"
                       id="search"
                       wire:model.live.debounce.300ms="search"
                       placeholder="Search by name or email..."
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            </div>
            <div class="sm:w-48">
                <label for="role" class="block text-sm font-medium text-gray-700 mb-1">Filter by Role</label>
                <select id="role"
                        wire:model.live="selectedRole"
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">All Roles</option>
                    @foreach($roles as $role)
                        <option value="{{ $role }}">{{ ucfirst($role) }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    <!-- Users List -->
    <div class="divide-y divide-gray-200">
        @forelse($users as $user)
            <div class="px-6 py-4 hover:bg-gray-50 transition-colors">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3">
                        <!-- Avatar -->
                        <div class="flex-shrink-0">
                            @if($user->avatar)
                                <img class="h-10 w-10 rounded-full object-cover"
                                     src="{{ $user->avatar }}"
                                     alt="{{ $user->name }}">
                            @else
                                <div class="h-10 w-10 rounded-full bg-gray-300 flex items-center justify-center">
                                    <span class="text-sm font-medium text-gray-700">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </span>
                                </div>
                            @endif
                        </div>

                        <!-- User Info -->
                        <div class="flex-1">
                            <div class="flex items-center space-x-2">
                                <p class="text-sm font-medium text-gray-900">{{ $user->name }}</p>
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                    {{ $user->role === 'student' ? 'bg-blue-100 text-blue-800' : '' }}
                                    {{ $user->role === 'teacher' ? 'bg-green-100 text-green-800' : '' }}
                                    {{ $user->role === 'librarian' ? 'bg-purple-100 text-purple-800' : '' }}
                                    {{ $user->role === 'author' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                    {{ $user->role === 'parent' ? 'bg-pink-100 text-pink-800' : '' }}
                                    {{ in_array($user->role, ['moderator', 'subscriber']) ? 'bg-gray-100 text-gray-800' : '' }}">
                                    {{ ucfirst($user->role) }}
                                </span>
                            </div>
                            <p class="text-sm text-gray-500">{{ $user->email }}</p>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="flex items-center space-x-2">
                        <!-- Online Status -->
                        @if($user->is_online)
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                <span class="w-2 h-2 bg-green-400 rounded-full mr-1"></span>
                                Online
                            </span>
                        @else
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                <span class="w-2 h-2 bg-gray-400 rounded-full mr-1"></span>
                                Offline
                            </span>
                        @endif

                        <!-- Impersonate Button -->
                        @if($user->canBeImpersonated())
                            <button wire:click="impersonateUser({{ $user->id }})"
                                    class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
                                </svg>
                                Impersonate
                            </button>
                        @else
                            <span class="inline-flex items-center px-3 py-2 text-sm leading-4 font-medium text-gray-400 cursor-not-allowed">
                                Cannot impersonate
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        @empty
            <div class="px-6 py-8 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-2.239"></path>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">No users found</h3>
                <p class="mt-1 text-sm text-gray-500">Try adjusting your search or filter criteria.</p>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($users->hasPages())
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $users->links() }}
        </div>
    @endif
</div>
