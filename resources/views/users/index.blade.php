<x-layouts.app title="Users">
    <x-slot name="breadcrumb">
        <x-breadcrumb />
    </x-slot>

    <!-- Add search and filter controls -->
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div class="flex-1 max-w-md">
            <form method="GET" action="{{ route('users.index') }}">
                <div class="relative">
                    <input type="text"
                           name="search"
                           value="{{ request('search') }}"
                           placeholder="Search users by name or email..."
                           class="w-full rounded-md border-gray-300 pl-10 pr-4 py-2 text-sm focus:border-blue-500 focus:ring-blue-500">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                </div>
            </form>
        </div>

        <div class="flex items-center gap-3">
            <!-- Filter buttons -->
            <div class="flex items-center gap-2">
                <a href="{{ route('users.index') }}"
                   class="px-3 py-2 text-sm font-medium rounded-md {{ !request()->has(['verified', 'unverified', 'role']) ? 'bg-blue-100 text-blue-700' : 'text-gray-600 hover:text-gray-900' }}">
                    All
                </a>
                <a href="{{ route('users.index', ['verified' => 1]) }}"
                   class="px-3 py-2 text-sm font-medium rounded-md {{ request('verified') ? 'bg-green-100 text-green-700' : 'text-gray-600 hover:text-gray-900' }}">
                    Verified
                </a>
                <a href="{{ route('users.index', ['unverified' => 1]) }}"
                   class="px-3 py-2 text-sm font-medium rounded-md {{ request('unverified') ? 'bg-yellow-100 text-yellow-700' : 'text-gray-600 hover:text-gray-900' }}">
                    Unverified
                </a>
            </div>

            <!-- Role filter dropdown -->
            <select name="role"
                    onchange="window.location.href = '{{ route('users.index') }}?role=' + this.value"
                    class="rounded-md border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                <option value="">All Roles</option>
                <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                <option value="teacher" {{ request('role') === 'teacher' ? 'selected' : '' }}>Teacher</option>
                <option value="student" {{ request('role') === 'student' ? 'selected' : '' }}>Student</option>
                <option value="librarian" {{ request('role') === 'librarian' ? 'selected' : '' }}>Librarian</option>
            </select>

            <!-- Online status toggle -->
            <a href="{{ route('users.index', array_merge(request()->all(), ['online' => !request('online')])) }}"
               class="px-3 py-2 text-sm font-medium rounded-md {{ request('online') ? 'bg-green-100 text-green-700' : 'text-gray-600 hover:text-gray-900' }}">
                {{ request('online') ? 'Show All' : 'Online Only' }}
            </a>
        </div>
    </div>

    @if ($users->count())
        <div class="bg-white shadow-sm ring-1 ring-gray-900/5 rounded-lg overflow-hidden">
            <x-table>
                <x-slot name="head">
                    <tr>
                        <x-table.th>
                            <div class="flex items-center space-x-2">
                                <span>User</span>
                            </div>
                        </x-table.th>
                        <x-table.th>Role</x-table.th>
                        <x-table.th>Status</x-table.th>
                        <x-table.th>Activity</x-table.th>
                        <x-table.th>Joined</x-table.th>
                        <x-table.th><span class="sr-only">Actions</span></x-table.th>
                    </tr>
                </x-slot>

                @foreach ($users as $user)
                    <tr class="hover:bg-gray-50">
                        <x-table.td>
                            <div class="flex items-center space-x-3">
                                <!-- Avatar -->
                                <div class="relative">
                                    @if($user->avatar)
                                        <img class="h-10 w-10 rounded-full object-cover"
                                             src="{{ $user->avatar }}"
                                             alt="{{ $user->name }}">
                                    @else
                                        <div class="h-10 w-10 rounded-full bg-gradient-to-r from-blue-500 to-purple-600 flex items-center justify-center">
                                            <span class="text-white font-medium text-sm">
                                                {{ strtoupper(substr($user->name, 0, 2)) }}
                                            </span>
                                        </div>
                                    @endif

                                    <!-- Online indicator -->
                                    @if($user->is_online)
                                        <div class="absolute -bottom-0.5 -right-0.5 h-3 w-3 rounded-full bg-green-400 border-2 border-white"></div>
                                    @endif
                                </div>

                                <div class="min-w-0 flex-1">
                                    <p class="font-medium text-gray-900 truncate">{{ $user->name }}</p>
                                    <p class="text-sm text-gray-500 truncate">{{ $user->email }}</p>
                                </div>
                            </div>
                        </x-table.td>

                        <x-table.td>
                            <span class="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium capitalize
                                {{ $user->role === 'admin' ? 'bg-red-100 text-red-700' : '' }}
                                {{ $user->role === 'teacher' ? 'bg-blue-100 text-blue-700' : '' }}
                                {{ $user->role === 'student' ? 'bg-green-100 text-green-700' : '' }}
                                {{ $user->role === 'librarian' ? 'bg-purple-100 text-purple-700' : '' }}
                                {{ !in_array($user->role, ['admin', 'teacher', 'student', 'librarian']) ? 'bg-gray-100 text-gray-700' : '' }}">
                                {{ $user->role ?? 'User' }}
                            </span>
                        </x-table.td>

                        <x-table.td>
                            <span @class([
                                "inline-flex items-center rounded-md px-2 py-1 text-xs font-medium",
                                'bg-green-100 text-green-700' => $user->email_verified_at,
                                'bg-yellow-100 text-yellow-700' => !$user->email_verified_at
                            ])>
                                @if($user->email_verified_at)
                                    <svg class="mr-1 h-3 w-3" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                    Verified
                                @else
                                    <svg class="mr-1 h-3 w-3" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                    </svg>
                                    Unverified
                                @endif
                            </span>
                        </x-table.td>

                        <x-table.td>
                            @if($user->is_online)
                                <span class="inline-flex items-center text-xs text-green-600">
                                    <div class="h-2 w-2 rounded-full bg-green-400 mr-1"></div>
                                    Online
                                </span>
                            @else
                                <span class="text-xs text-gray-500">
                                    {{ $user->last_seen_at ? $user->last_seen_at->diffForHumans() : 'Never' }}
                                </span>
                            @endif
                        </x-table.td>

                        <x-table.td>
                            <span class="text-sm text-gray-500">
                                {{ $user->created_at->format('M j, Y') }}
                            </span>
                        </x-table.td>

                        <x-table.td action>
                            <div class="flex items-center space-x-2">
                                <x-action name="view" :to="route('users.show', ['user' => $user])" />

                                <!-- Quick actions dropdown -->
                                <div class="relative" x-data="{ open: false }">
                                    <button @click="open = !open"
                                            class="p-1 text-gray-400 hover:text-gray-600">
                                        <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z"/>
                                        </svg>
                                    </button>

                                    <div x-show="open"
                                         @click.away="open = false"
                                         x-transition
                                         class="absolute right-0 mt-1 w-48 bg-white rounded-md shadow-lg border border-gray-200 z-10">
                                        <div class="py-1">
                                            @if(!$user->email_verified_at)
                                                <button class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                                    Send Verification Email
                                                </button>
                                            @endif
                                            <button class="block w-full text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                                Reset Password
                                            </button>
                                            <button class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50">
                                                Suspend User
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </x-table.td>
                    </tr>
                @endforeach
            </x-table>
        </div>

        <!-- Enhanced pagination with info -->
        <div class="mt-6 flex items-center justify-between">
            <div class="text-sm text-gray-700">
                Showing {{ $users->firstItem() }} to {{ $users->lastItem() }} of {{ $users->total() }} users
            </div>
            {{ $users->links() }}
        </div>
    @else
        <div class="text-center py-12">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/>
            </svg>
            <h3 class="mt-4 text-lg font-medium text-gray-900">No users found</h3>
            <p class="mt-2 text-sm text-gray-500">
                @if(request()->hasAny(['search', 'verified', 'unverified', 'role', 'online']))
                    Try adjusting your search or filter criteria.
                @else
                    Get started by inviting users to your application.
                @endif
            </p>
            @if(request()->hasAny(['search', 'verified', 'unverified', 'role', 'online']))
                <div class="mt-4">
                    <a href="{{ route('users.index') }}" class="text-blue-600 hover:text-blue-500 text-sm font-medium">
                        Clear all filters
                    </a>
                </div>
            @endif
        </div>
    @endif
</x-layouts.app>
