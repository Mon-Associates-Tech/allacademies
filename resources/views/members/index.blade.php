<x-layouts.app :title="'Members of ' . $team->name">
    <x-slot name="breadcrumb">
        <x-breadcrumb :paths="[
            'Teams' => route('teams.index'),
        ]" />
    </x-slot>

    @if ($team->owner->is($user) && !$team->is_personal)
        <x-slot name="action">
            <x-link.primary :to="route('teams.members.create', ['team' => $team])">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                Add Member
            </x-link.primary>
        </x-slot>
    @endif

    <!-- Team Info Header -->
    <div class="bg-white shadow-sm ring-1 ring-gray-900/5 rounded-lg p-6 mb-6">
        <div class="flex items-start justify-between">
            <div class="flex items-center space-x-4">
                <div class="h-16 w-16 rounded-lg bg-gradient-to-r from-blue-500 to-purple-600 flex items-center justify-center">
                    <span class="text-white font-bold text-xl">
                        {{ strtoupper(substr($team->name, 0, 2)) }}
                    </span>
                </div>
                <div>
                    <h2 class="text-2xl font-bold text-gray-900">{{ $team->name }}</h2>
                    <div class="flex items-center space-x-4 mt-2">
                        <span class="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium {{ $team->is_personal ? 'bg-blue-100 text-blue-700' : 'bg-green-100 text-green-700' }}">
                            {{ $team->is_personal ? 'Personal' : 'Shared' }} Team
                        </span>
                        <span class="text-sm text-gray-500">
                            {{ $team->members->count() }} {{ Str::plural('member', $team->members->count()) }}
                        </span>
                        <span class="text-sm text-gray-500">
                            Created {{ $team->created_at->diffForHumans() }}
                        </span>
                    </div>
                </div>
            </div>

            @if($team->owner->is($user))
                <div class="flex items-center space-x-2">
                    <span class="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium bg-yellow-100 text-yellow-700">
                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M9.383 3.076A1 1 0 0110 4v12a1 1 0 01-1.707.707L4.586 13H2a1 1 0 01-1-1V8a1 1 0 011-1h2.586l3.707-3.707a1 1 0 011.09-.217zM15.657 6.343a1 1 0 011.414 0A9.972 9.972 0 0119 12a9.972 9.972 0 01-1.929 5.657 1 1 0 01-1.414-1.414A7.971 7.971 0 0017 12a7.971 7.971 0 00-1.343-4.243 1 1 0 010-1.414z" clip-rule="evenodd"/>
                        </svg>
                        Team Owner
                    </span>
                </div>
            @endif
        </div>
    </div>

    <!-- Search and Filter Controls -->
    <div class="mb-6 bg-white p-6 shadow-sm ring-1 ring-gray-900/5 rounded-lg">
        <form method="GET" action="{{ route('teams.members.index', ['team' => $team]) }}" class="space-y-4">
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                <!-- Search Input -->
                <div>
                    <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Search Members</label>
                    <div class="relative">
                        <input type="text"
                               id="search"
                               name="search"
                               value="{{ request('search') }}"
                               placeholder="Search by name or email..."
                               class="w-full rounded-md border-gray-300 pl-10 pr-4 py-2 text-sm focus:border-blue-500 focus:ring-blue-500">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <!-- Role Filter -->
                <div>
                    <label for="role" class="block text-sm font-medium text-gray-700 mb-1">Filter by Role</label>
                    <select name="role" id="role" class="w-full rounded-md border-gray-300 text-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">All Roles</option>
                        <option value="owner" {{ request('role') === 'owner' ? 'selected' : '' }}>Owner</option>
                        <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>Admin</option>
                        <option value="member" {{ request('role') === 'member' ? 'selected' : '' }}>Member</option>
                    </select>
                </div>

                <!-- Actions -->
                <div class="flex items-end space-x-2">
                    <button type="submit" class="flex-1 bg-blue-600 text-white px-4 py-2 rounded-md text-sm font-medium hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500">
                        Apply Filters
                    </button>
                    <a href="{{ route('teams.members.index', ['team' => $team]) }}" class="flex-1 text-center bg-gray-100 text-gray-700 px-4 py-2 rounded-md text-sm font-medium hover:bg-gray-200">
                        Clear
                    </a>
                </div>
            </div>
        </form>
    </div>

    @if ($team->members->count())
        <div class="bg-white shadow-sm ring-1 ring-gray-900/5 rounded-lg overflow-hidden">
            <x-table>
                <x-slot name="head">
                    <tr>
                        <x-table.th>
                            <div class="flex items-center space-x-2">
                                <span>Member</span>
                            </div>
                        </x-table.th>
                        <x-table.th>Role</x-table.th>
                        <x-table.th>Status</x-table.th>
                        <x-table.th>Joined</x-table.th>
                        <x-table.th><span class="sr-only">Actions</span></x-table.th>
                    </tr>
                </x-slot>

                @foreach ($team->members as $member)
                    <tr class="hover:bg-gray-50">
                        <x-table.td>
                            <div class="flex items-center space-x-3">
                                <!-- Avatar -->
                                <div class="relative">
                                    @if($member->avatar)
                                        <img class="h-10 w-10 rounded-full object-cover"
                                             src="{{ $member->avatar }}"
                                             alt="{{ $member->name }}">
                                    @else
                                        <div class="h-10 w-10 rounded-full bg-gradient-to-r from-blue-500 to-purple-600 flex items-center justify-center">
                                            <span class="text-white font-medium text-sm">
                                                {{ strtoupper(substr($member->name, 0, 2)) }}
                                            </span>
                                        </div>
                                    @endif

                                    <!-- Online indicator -->
                                    @if($member->is_online ?? false)
                                        <div class="absolute -bottom-0.5 -right-0.5 h-3 w-3 rounded-full bg-green-400 border-2 border-white"></div>
                                    @endif
                                </div>

                                <div class="min-w-0 flex-1">
                                    <div class="flex items-center space-x-2">
                                        <p class="font-medium text-gray-900 truncate">{{ $member->name }}</p>
                                        @if($team->owner->is($member))
                                            <svg class="w-4 h-4 text-yellow-500" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M9.383 3.076A1 1 0 0110 4v12a1 1 0 01-1.707.707L4.586 13H2a1 1 0 01-1-1V8a1 1 0 011-1h2.586l3.707-3.707a1 1 0 011.09-.217z" clip-rule="evenodd"/>
                                            </svg>
                                        @endif
                                    </div>
                                    <p class="text-sm text-gray-500 truncate">{{ $member->email }}</p>
                                </div>
                            </div>
                        </x-table.td>

                        <x-table.td>
                            @if($team->owner->is($member))
                                <span class="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium bg-yellow-100 text-yellow-700">
                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M9.383 3.076A1 1 0 0110 4v12a1 1 0 01-1.707.707L4.586 13H2a1 1 0 01-1-1V8a1 1 0 011-1h2.586l3.707-3.707a1 1 0 011.09-.217z" clip-rule="evenodd"/>
                                    </svg>
                                    Owner
                                </span>
                            @else
                                <span class="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium capitalize
                                    {{ ($member->pivot->role ?? 'member') === 'admin' ? 'bg-blue-100 text-blue-700' : 'bg-gray-100 text-gray-700' }}">
                                    @if(($member->pivot->role ?? 'member') === 'admin')
                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M18 8a6 6 0 01-7.743 5.743L10 14l-1 1-1 1H6v2H2v-4l4.257-4.257A6 6 0 1118 8zm-6-4a1 1 0 100 2 2 2 0 012 2 1 1 0 102 0 4 4 0 00-4-4z" clip-rule="evenodd"/>
                                        </svg>
                                    @else
                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"/>
                                        </svg>
                                    @endif
                                    {{ $member->pivot->role ?? 'member' }}
                                </span>
                            @endif
                        </x-table.td>

                        <x-table.td>
                            @if($member->email_verified_at)
                                <span class="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium bg-green-100 text-green-700">
                                    <svg class="mr-1 h-3 w-3" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                    </svg>
                                    Active
                                </span>
                            @else
                                <span class="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium bg-yellow-100 text-yellow-700">
                                    <svg class="mr-1 h-3 w-3" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                    </svg>
                                    Pending
                                </span>
                            @endif
                        </x-table.td>

                        <x-table.td>
                            <span class="text-sm text-gray-500">
                                @if($team->owner->is($member))
                                    {{ $team->created_at->format('M j, Y') }}
                                @else
                                    {{ $member->pivot->created_at ? $member->pivot->created_at->format('M j, Y') : 'N/A' }}
                                @endif
                            </span>
                        </x-table.td>

                        <x-table.td action>
                            <div class="flex items-center space-x-2">
                                @if ($team->owner->is($user) && $member->isNot($user) && !$team->owner->is($member))
                                    <!-- Edit Role Button -->
                                    <a href="{{ route('members.edit', ['team' => $team, 'member' => $member]) }}"
                                       class="text-blue-600 hover:text-blue-900 text-sm font-medium">
                                        Edit Role
                                    </a>

                                    <!-- Remove Member Button -->
                                    <button x-data="{}"
                                            x-on:click="$store.deleteForm.show('Remove Member', 'Are you sure you want to remove {{ $member->name }} from {{ $team->name }}? This action cannot be undone.', '{{ route('teams.members.destroy', ['team' => $team, 'member' => $member]) }}', 'Remove Member')"
                                            class="text-red-600 hover:text-red-900 text-sm font-medium">
                                        Remove
                                    </button>
                                @elseif($team->owner->is($member))
                                    <span class="text-gray-400 text-sm">Team Owner</span>
                                @elseif($member->is($user))
                                    <span class="text-gray-400 text-sm">You</span>
                                @else
                                    <span class="text-gray-400 text-sm">—</span>
                                @endif
                            </div>
                        </x-table.td>
                    </tr>
                @endforeach
            </x-table>
        </div>

        <!-- Team Statistics -->
        <div class="mt-6 grid grid-cols-1 gap-5 sm:grid-cols-3">
            <div class="bg-white overflow-hidden shadow-sm ring-1 ring-gray-900/5 rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-blue-500 rounded-md flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Total Members</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ $team->members->count() }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm ring-1 ring-gray-900/5 rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-green-500 rounded-md flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Active Members</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ $team->members->where('email_verified_at', '!=', null)->count() }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm ring-1 ring-gray-900/5 rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <div class="w-8 h-8 bg-purple-500 rounded-md flex items-center justify-center">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"/>
                                </svg>
                            </div>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-gray-500 truncate">Admins</dt>
                                <dd class="text-lg font-medium text-gray-900">{{ $team->members->where('pivot.role', 'admin')->count() + 1 }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @else
        <div class="text-center py-12">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">No team members</h3>
            <p class="mt-1 text-sm text-gray-500">Get started by adding your first team member.</p>
            @if ($team->owner->is($user) && !$team->is_personal)
                <div class="mt-6">
                    <x-link.primary :to="route('teams.members.create', ['team' => $team])">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                        Add Member
                    </x-link.primary>
                </div>
            @endif
        </div>
    @endif
</x-layouts.app>
