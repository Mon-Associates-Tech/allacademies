<x-layouts.app title="Users">
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
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
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
                <option value="moderator" {{ request('role') === 'moderator' ? 'selected' : '' }}>Moderator</option>
                <option value="owner" {{ request('role') === 'owner' ? 'selected' : '' }}>Owner</option>
                <option value="author" {{ request('role') === 'author' ? 'selected' : '' }}>Author</option>
                <option value="subscriber" {{ request('role') === 'subscriber' ? 'selected' : '' }}>Subscriber</option>
                <option value="parent" {{ request('role') === 'parent' ? 'selected' : '' }}>Parent</option>
            </select>

            <!-- Online status toggle -->
            <a href="{{ route('users.index', array_merge(request()->all(), ['online' => !request('online')])) }}"
               class="px-3 py-2 text-sm font-medium rounded-md {{ request('online') ? 'bg-green-100 text-green-700' : 'text-gray-600 hover:text-gray-900' }}">
                {{ request('online') ? 'Show All' : 'Online Only' }}
            </a>

            <!-- Add New User Button -->
            <x-button.primary variant="primary" size="sm" type="button"
                              onclick="window.Modal.open('add-user-form')"
                              class="px-4 py-2">
                <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Add User
            </x-button.primary>

        </div>
    </div>

    @if ($users->count())
        <div class="bg-white shadow-sm ring-1 ring-gray-900/5 rounded-lg overflow-hidden">
            <x-table>
                <x-slot name="head">
                    <tr>
                        <x-table.th>User</x-table.th>
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
                                <div class="relative">
                                    <x-avatar avatar="{{$user->avatar}}" class="h-10 w-10" name="{{ $user->name }}"/>
                                    @if($user->is_online)
                                        <div
                                            class="absolute -bottom-0.5 -right-0.5 h-3 w-3 rounded-full bg-green-400 border-2 border-white"></div>
                                    @endif
                                </div>
                                <div class="min-w-0 flex-1">
                                    <p class="font-medium text-gray-900 truncate">{{ $user->name }}</p>
                                    <p class="text-sm text-gray-500 truncate">{{ $user->email }}</p>
                                    @if(Auth::user()->hasRole('owner'))
                                    <p class="text-sm text-gray-500 truncate">{{ $user->school?->name }}</p>
                                        @endif
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
                            <path fill-rule="evenodd"
                                  d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"
                                  clip-rule="evenodd"/>
                        </svg>
                        Verified
                    @else
                        <svg class="mr-1 h-3 w-3" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                  d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z"
                                  clip-rule="evenodd"/>
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
                            <span class="text-sm text-gray-500">{{ $user->created_at->format('M j, Y') }}</span>
                        </x-table.td>

                        <x-table.td action>
                            <div class="flex items-center space-x-2">
                                <x-action name="view" :to="route('users.show', ['user' => $user])"/>

                                <x-dropdown>
                                    <!-- Trigger -->
                                    <button class="p-1 text-gray-400 hover:text-gray-600">
                                        <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path
                                                d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z"/>
                                        </svg>
                                    </button>

                                    <!-- Dropdown Content -->
                                    <x-slot name="content">
                                        @can('own')
                                            @if($user->role !== 'owner')
                                                <x-dropdown.item
                                                    onclick="window.Modal.open('change-role-form', { userName:  '{{$user->name}}', email: '{{$user->email}}', role: '{{$user->role}}', id: '{{$user->id}}' })">
                                                    <x-slot name="icon">
                                                        <svg class="mr-3 h-4 w-4 text-gray-400" fill="none"
                                                             stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                  stroke-width="2"
                                                                  d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                        </svg>
                                                    </x-slot>
                                                    Change Role
                                                </x-dropdown.item>
                                            @endif
                                        @endcan
                                        {{--                                            onclick="window.Modal.open({name: 'change-role-form'}) $store.changeRole.show('{{ $user->name }}', '{{ $user->email }}', '{{ $user->role }}', {{ $user->id }}); $parent.open = false">--}}

                                        <x-dropdown.item :href="route('users.show', ['user' => $user])">
                                            <x-slot name="icon">
                                                <svg class="mr-3 h-4 w-4 text-gray-400" fill="none"
                                                     stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                          stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                          stroke-width="2"
                                                          d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                                </svg>
                                            </x-slot>
                                            View Details
                                        </x-dropdown.item>

                                            <x-dropdown.item click="$dispatch('open-delete-modal', {{ $user->id }})">
                                                <x-slot name="icon">
                                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                </x-slot>
                                                Delete User
                                            </x-dropdown.item>
                                    </x-slot>
                                </x-dropdown>
                            </div>
                        </x-table.td>
                    </tr>
                @endforeach
            </x-table>
        </div>

        <!-- Pagination -->
        <div class="mt-6">
            {{ $users->links() }}
        </div>

    @else
        <!-- Empty state -->
        <div class="text-center py-12">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">No users found</h3>
            <p class="mt-1 text-sm text-gray-500">
                Try adjusting your search or filters to find what you're looking for.
            </p>
        </div>
    @endif

    <x-modal-component name="change-role-form">
        <x-slot:header>
            <div class="pb-4">
                <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                    Change User Role
                </h3>
            </div>
        </x-slot:header>
        <form method="POST" action="{{ route('users.change-role') }}" id="change-role-form">
            @csrf
            <input type="hidden" name="user_id" x-model="modalData.id">

            <div>
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-blue-100">
                    <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor"
                         viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div class="mt-3 text-center sm:mt-5">
                    <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                        Change User Role
                    </h3>
                    <div class="mt-2">
                        <p class="text-sm text-gray-500">
                            You are about to change the role for the following user:
                        </p>
                    </div>
                </div>
            </div>

            <div class="mt-5 space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">User Name</label>
                    <input type="text"
                           x-model="modalData.userName"
                           readonly
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm bg-gray-50 text-gray-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700">Email</label>
                    <input type="email"
                           name="email"
                           x-model="modalData.email"
                           readonly
                           class="mt-1 block w-full border-gray-300 rounded-md shadow-sm bg-gray-50 text-gray-500">
                </div>

                <div>
                    <label for="role" class="block text-sm font-medium text-gray-700">New Role</label>
                    <select name="role"
                            id="role"
                            x-model="modalData.role"
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        <option value="subscriber">Subscriber</option>
{{--                        <option value="student">Student</option>--}}
{{--                        <option value="teacher">Teacher</option>--}}
{{--                        <option value="librarian">Librarian</option>--}}
                        <option value="author">Author</option>
                        <option value="parent">Parent</option>
                        <option value="moderator">Moderator</option>
                        <option value="admin">Admin</option>
                    </select>
                </div>
            </div>
        </form>

        <x-slot:footer>
            <div class="mt-5 sm:mt-6 sm:grid sm:grid-cols-2 sm:gap-3 sm:grid-flow-row-dense">
                <x-button.white type="button"
                                onclick="window.Modal.close('change-role-form')"
                                class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:mt-0 sm:col-start-1 sm:text-sm">
                    Cancel
                </x-button.white>
                <x-button.primary type="submit" form="change-role-form"
                                  class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:col-start-2 sm:text-sm">
                    Change Role
                </x-button.primary>

            </div>
        </x-slot:footer>
    </x-modal-component>
    <x-modal-component name="add-user-form" height="h-96">
        <x-slot:header>
            <div class="">
                <h3 class="text-lg font-medium pb-4 text-gray-900">Add New User</h3>
            </div>
        </x-slot:header>

        <form method="POST" id="user-add-form" action="{{ route('users.store') }}">
            @csrf

            <!-- Name Field -->
            <div class="mb-4">
                <label for="name" class="block text-sm font-medium text-gray-700 mb-2">Name</label>
                <input type="text"
                       id="name"
                       name="name"
                       required
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                       placeholder="Enter user's full name">
            </div>

            <!-- Email Field -->
            <div class="mb-4">
                <label for="email" class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                <input type="email"
                       id="email"
                       name="email"
                       required
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                       placeholder="Enter user's email address">
            </div>

            <!-- Password Field -->
            <div class="mb-4">
                <label for="password" class="block text-sm font-medium text-gray-700 mb-2">Password</label>
                <input type="password"
                       id="password"
                       name="password"
                       required
                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                       placeholder="Enter a secure password">
            </div>

            <!-- Role Field -->
            <div class="mb-6">
                <label for="role" class="block text-sm font-medium text-gray-700 mb-2">Role</label>
                <select id="role"
                        name="role"
                        required
                        class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                    <option value="">Select a role</option>
                    <option value="admin">Admin</option>
                    <option value="teacher">Teacher</option>
                    <option value="student">Student</option>
                    <option value="librarian">Librarian</option>
                    <option value="moderator">Moderator</option>
                    <option value="author">Author</option>
                    <option value="parent">Parent</option>
                    <option value="subscriber">Subscriber</option>
                </select>
            </div>
        </form>

        <x-slot:footer>
            <div class="flex items-center justify-end gap-3">
                <button type="button"
                        onclick="window.Modal.close('add-user-form')"
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 border border-gray-300 rounded-md hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-500">
                    Cancel
                </button>
                <x-button.primary size="md" type="submit" form="user-add-form"
                                  class="px-4  text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    Create User
                </x-button.primary>
            </div>
        </x-slot:footer>
    </x-modal-component>

    @livewire('users.delete-user-modal')

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            window.addEventListener('open-delete-modal', function(event) {
                // Use the correct Livewire dispatch method
                Livewire.dispatch('openDeleteModal', { userId: event.detail });
            });
        });
    </script>
</x-layouts.app>
