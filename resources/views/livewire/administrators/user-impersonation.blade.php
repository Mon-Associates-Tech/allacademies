<div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
    <!-- Header -->
    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
        <div class="flex items-center justify-between">
            <h3 class="text-lg font-semibold text-gray-900">User Impersonation</h3>
            @if(session()->has('impersonator'))
                <button wire:click="stopImpersonation"
                        class="inline-flex items-center px-3 py-2 border border-red-300 text-sm leading-4 font-medium rounded-md text-red-700 bg-red-50 hover:bg-red-100 transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M6 18L18 6M6 6l12 12"></path>
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
    <div class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 rounded-lg">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                <thead class="bg-gray-50 dark:bg-gray-800">
                <tr>
                    <th scope="col"
                        class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 dark:text-gray-200 sm:pl-6">
                        User
                    </th>
                    <th scope="col"
                        class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 dark:text-gray-200 hidden sm:table-cell">
                        Role
                    </th>
                    <th scope="col"
                        class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 dark:text-gray-200 hidden md:table-cell">
                        Email
                    </th>
                    <th scope="col"
                        class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900 dark:text-gray-200">Status
                    </th>
                    <th scope="col" class="relative py-3.5 pl-3 pr-4 sm:pr-6">
                        <span class="sr-only">Actions</span>
                    </th>
                </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 dark:divide-gray-700 bg-white dark:bg-gray-900">
                @forelse($users as $user)
                    <tr class="hover:bg-gray-50 dark:hover:bg-gray-800/50 transition-colors">
                        <td class="whitespace-nowrap py-4 pl-4 pr-3 sm:pl-6">
                            <div class="flex items-center gap-x-3">
                                <x-avatar
                                    name="{{$user->name}}"
                                    class="w-8 h-8"
                                    text-size="text-xs"
                                    avatar="{{$user->avatar}}"
                                />
                                <!-- Mobile: Show name and role together -->
                                <div class="sm:hidden">
                                    <div class="font-medium text-gray-900 dark:text-gray-200">{{ $user->name }}</div>
                                    <div class="mt-1">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                            {{ $user->role === 'student' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200' : '' }}
                                            {{ $user->role === 'teacher' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : '' }}
                                            {{ $user->role === 'librarian' ? 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200' : '' }}
                                            {{ $user->role === 'author' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200' : '' }}
                                            {{ $user->role === 'parent' ? 'bg-pink-100 text-pink-800 dark:bg-pink-900 dark:text-pink-200' : '' }}
                                            {{ in_array($user->role, ['moderator', 'subscriber']) ? 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200' : '' }}">
                                            {{ ucfirst($user->role) }}
                                        </span>
                                    </div>
                                </div>
                                <!-- Desktop: Show only name -->
                                <div class="hidden sm:block font-medium text-gray-900 dark:text-gray-200">
                                    {{ $user->name }}
                                </div>
                            </div>
                        </td>

                        <!-- Role (hidden on mobile) -->
                        <td class="hidden sm:table-cell whitespace-nowrap px-3 py-4 text-sm text-gray-500 dark:text-gray-400">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                {{ $user->role === 'student' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200' : '' }}
                                {{ $user->role === 'teacher' ? 'bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200' : '' }}
                                {{ $user->role === 'librarian' ? 'bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200' : '' }}
                                {{ $user->role === 'author' ? 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200' : '' }}
                                {{ $user->role === 'parent' ? 'bg-pink-100 text-pink-800 dark:bg-pink-900 dark:text-pink-200' : '' }}
                                {{ in_array($user->role, ['moderator', 'subscriber']) ? 'bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200' : '' }}">
                                {{ ucfirst($user->role) }}
                            </span>
                        </td>

                        <!-- Email (hidden on mobile) -->
                        <td class="hidden md:table-cell whitespace-nowrap px-3 py-4 text-sm text-gray-500 dark:text-gray-400">
                            {{ $user->email }}
                        </td>

                        <!-- Status -->
                        <td class="whitespace-nowrap px-3 py-4 text-sm">
                            @if($user->is_online)
                                <span
                                    class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                    <span class="w-2 h-2 bg-green-400 dark:bg-green-500 rounded-full mr-1"></span>
                                    Online
                                </span>
                            @else
                                <span
                                    class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200">
                                    <span class="w-2 h-2 bg-gray-400 dark:bg-gray-500 rounded-full mr-1"></span>
                                    Offline
                                </span>
                            @endif
                        </td>

                        <!-- Actions -->
                        <td class="whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-6">
                            @if($user->canBeImpersonated())
                                <x-button.primary
                                    variant="subtle"
                                    :with-shadow="false"
                                    wire:click="impersonateUser({{ $user->id }})"
                                    class="inline-flex items-center px-3 py-2"
                                >
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
                                    </svg>
                                    <span class="hidden sm:inline">Impersonate</span>
                                </x-button.primary>
                            @else
                                <span class="text-sm !text-start text-gray-400 dark:text-gray-500">Not Applicable</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-8 text-center">
                            <svg class="mx-auto h-12 w-12 text-gray-400 dark:text-gray-600" fill="none"
                                 viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-2.239"></path>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-gray-200">No users found</h3>
                            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Try adjusting your search or filter
                                criteria.</p>
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    @if($users->hasPages())
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $users->links() }}
        </div>
    @endif
</div>
