<x-layouts.app page-name="Teams">
    <x-slot name="breadcrumb">
        <x-breadcrumb />
    </x-slot>

    <section>
        <!-- Search and filter controls -->
        <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div class="flex-1 max-w-md">
                <form method="GET" action="{{ route('teams.index') }}">
                    <div class="relative">
                        <input type="text"
                               name="search"
                               value="{{ request('search') }}"
                               placeholder="Search teams by name..."
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
                    <a href="{{ route('teams.index') }}"
                       class="px-3 py-2 text-sm font-medium rounded-md {{ !request()->has(['owned', 'joined', 'personal']) ? 'bg-blue-100 text-blue-700' : 'text-gray-600 hover:text-gray-900' }}">
                        All Teams
                    </a>
                    <a href="{{ route('teams.index', ['owned' => 1]) }}"
                       class="px-3 py-2 text-sm font-medium rounded-md {{ request('owned') ? 'bg-purple-100 text-purple-700' : 'text-gray-600 hover:text-gray-900' }}">
                        Owned
                    </a>
                    <a href="{{ route('teams.index', ['joined' => 1]) }}"
                       class="px-3 py-2 text-sm font-medium rounded-md {{ request('joined') ? 'bg-green-100 text-green-700' : 'text-gray-600 hover:text-gray-900' }}">
                        Joined
                    </a>
                    <a href="{{ route('teams.index', ['personal' => 1]) }}"
                       class="px-3 py-2 text-sm font-medium rounded-md {{ request('personal') ? 'bg-yellow-100 text-yellow-700' : 'text-gray-600 hover:text-gray-900' }}">
                        Personal
                    </a>
                </div>

                <!-- Action buttons -->
                <div class="flex items-center gap-2">
                    <x-link.secondary :to="route('teams.joining')" class="text-nowrap">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                        Join Team
                    </x-link.secondary>
                    <x-link.primary :to="route('teams.create')" class="text-nowrap">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                        New Team
                    </x-link.primary>
                </div>
            </div>
        </div>

        @if ($teams->count())
            <div class="bg-white shadow-sm ring-1 ring-gray-900/5 rounded-lg">
                <x-table class="min-w-full divide-y divide-gray-200" style="z-index: 1">
                    <x-slot name="head">
                        <tr>
                            <x-table.th>
                                <div class="flex items-center space-x-2">
                                    <span>Team</span>
                                </div>
                            </x-table.th>
                            <x-table.th>Owner</x-table.th>
                            <x-table.th>Status</x-table.th>
                            <x-table.th>Members</x-table.th>
                            <x-table.th>Subscriptions</x-table.th>
                            <x-table.th>Created</x-table.th>
                            <x-table.th><span class="sr-only">Actions</span></x-table.th>
                        </tr>
                    </x-slot>

                    @foreach ($teams as $team)
                        <tr class="hover:bg-gray-50">
                            <x-table.td>
                                <div class="flex items-center space-x-3">
                                    <!-- Team Avatar/Icon -->
                                    <div class="relative">
                                        @if(isset($team->meta['logo']) && $team->meta['logo'])
                                            <img class="h-10 w-10 rounded-lg object-cover border border-gray-200"
                                                 src="{{ Storage::disk('s3')->url($team->meta['logo']) }}"
                                                 alt="{{ $team->name }}">
                                        @else
                                            <div class="h-10 w-10 rounded-lg bg-gradient-to-r from-indigo-500 to-purple-600 flex items-center justify-center">
                                                <svg class="h-5 w-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3z"/>
                                                </svg>
                                            </div>
                                        @endif

                                        <!-- Current team indicator -->
                                        @if ($user->currentTeam?->is($team))
                                            <div class="absolute -top-1 -right-1 h-4 w-4 rounded-full bg-orange-400 border-2 border-white flex items-center justify-center">
                                                <svg class="h-2 w-2 text-white" fill="currentColor" viewBox="0 0 8 8">
                                                    <circle cx="4" cy="4" r="3"/>
                                                </svg>
                                            </div>
                                        @endif
                                    </div>

                                    <div class="min-w-0 flex-1">
                                        <div class="flex items-center space-x-2">
                                            <p class="font-medium text-gray-900 truncate">{{ $team->name }}</p>
                                            <div class="flex items-center space-x-1">
                                                @if ($team->owner->is($user) && $team->is_personal)
                                                    <span class="inline-flex items-center rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-800">
                                                    Personal
                                                </span>
                                                @endif
                                                @if ($user->currentTeam?->is($team))
                                                    <span class="inline-flex items-center rounded-full bg-orange-100 px-2.5 py-0.5 text-xs font-medium text-orange-800">
                                                    Current
                                                </span>
                                                @endif
                                            </div>
                                        </div>
                                        @if(isset($team->meta['future']['institution']))
                                            <p class="text-sm text-gray-500 truncate">{{ $team->meta['future']['institution'] }}</p>
                                        @endif
                                    </div>
                                </div>
                            </x-table.td>

                            <x-table.td>
                                <div class="flex items-center space-x-2">
                                <span class="font-medium text-gray-900">
                                    {{ $team->owner->is($user) ? 'You' : $team->owner->name }}
                                </span>
                                    @if($team->owner->is($user))
                                        <span class="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium bg-blue-100 text-blue-700">
                                        Owner
                                    </span>
                                    @else
                                        <span class="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium bg-gray-100 text-gray-700">
                                        Member
                                    </span>
                                    @endif
                                </div>
                            </x-table.td>

                            <x-table.td>
                                @if(isset($team->status))
                                    <span @class([
                                    "inline-flex items-center rounded-md px-2 py-1 text-xs font-medium",
                                    'bg-green-100 text-green-700' => $team->status->value === 'approved',
                                    'bg-yellow-100 text-yellow-700' => $team->status->value === 'pending',
                                    'bg-red-100 text-red-700' => $team->status->value === 'declined'
                                ])>
                                    @if($team->status->value === 'approved')
                                            <svg class="mr-1 h-3 w-3" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                        </svg>
                                            Approved
                                        @elseif($team->status->value === 'pending')
                                            <svg class="mr-1 h-3 w-3" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"/>
                                        </svg>
                                            Pending
                                        @else
                                            <svg class="mr-1 h-3 w-3" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                        </svg>
                                            Declined
                                        @endif
                                </span>
                                @else
                                    <span class="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium bg-gray-100 text-gray-700">
                                    Active
                                </span>
                                @endif
                            </x-table.td>

                            <x-table.td>
                                <div class="flex items-center space-x-1">
                                    <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/>
                                    </svg>
                                    <span class="text-sm text-gray-600">
                                    {{ $team->members->count() + 1 }} <!-- +1 for owner -->
                                </span>
                                </div>
                            </x-table.td>

                            <x-table.td>
                                <div class="flex items-center space-x-1">
                                    <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                                    </svg>
                                    <span class="text-sm text-gray-600">
                                    {{ $team->subscriptions_count ?? 0 }}
                                </span>
                                </div>
                            </x-table.td>

                            <x-table.td>
                            <span class="text-sm text-gray-500">
                                {{ $team->created_at->format('M j, Y') }}
                            </span>
                            </x-table.td>

                            <x-table.td action>
                                <div class="flex items-center space-x-1">
                                    <!-- Quick actions -->
                                    @if ($user->currentTeam?->isNot($team))
                                        <form class="inline" method="POST" action="{{ route('teams.activate', ['team' => $team]) }}">
                                            @csrf
                                            <button class="text-indigo-600 hover:text-indigo-900 text-sm font-medium">
                                                Set Current
                                            </button>
                                        </form>
                                    @endif

                                    <div x-data="{
            open: false,
            toggle() {
                this.open = !this.open;
                if (this.open) {
                    this.$nextTick(() => {
                        this.positionDropdown();
                    });
                }
            },
            positionDropdown() {
                const button = this.$refs.button;
                const dropdown = this.$refs.dropdown;
                const rect = button.getBoundingClientRect();

                dropdown.style.position = 'fixed';
                dropdown.style.top = (rect.bottom + 4) + 'px';
                dropdown.style.left = (rect.right - dropdown.offsetWidth) + 'px';
            }
        }"
                                         @click.away="open = false"
                                         @resize.window="open && positionDropdown()">
                                        <button @click="toggle()"
                                                x-ref="button"
                                                type="button"
                                                class="p-1 text-gray-400 hover:text-gray-600 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-1 rounded"
                                                :aria-expanded="open"
                                                aria-haspopup="true">
                                            <span class="sr-only">Open options</span>
                                            <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z"/>
                                            </svg>
                                        </button>

                                        <!-- Dropdown positioned fixed to body -->
                                        <div x-show="open"
                                             x-ref="dropdown"
                                             x-transition:enter="transition ease-out duration-100"
                                             x-transition:enter-start="transform opacity-0 scale-95"
                                             x-transition:enter-end="transform opacity-100 scale-100"
                                             x-transition:leave="transition ease-in duration-75"
                                             x-transition:leave-start="transform opacity-100 scale-100"
                                             x-transition:leave-end="transform opacity-0 scale-95"
                                             class="w-56 bg-white rounded-md shadow-lg border border-gray-200"
                                             style="position: fixed; z-index: 9999;"
                                             role="menu"
                                             aria-orientation="vertical">
                                            <div class="py-1 text-left">
                                                <!-- Your dropdown content here -->
                                                <a href="{{ route('teams.members.index', ['team' => $team]) }}"
                                                   class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors duration-150"
                                                   role="menuitem"
                                                   @click="open = false">
                                                    <svg class="inline w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/>
                                                    </svg>
                                                    View Members
                                                </a>


                                                @if ($team->owner->is($user))
                                                    <a href="{{ route('teams.edit', ['team' => $team]) }}"
                                                       class="block text-left px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 transition-colors duration-150"
                                                       role="menuitem"
                                                       @click="open = false">
                                                        <svg class="inline w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                        </svg>
                                                        Edit Team
                                                    </a>
                                                @endif

                                                @if ($team->owner->isNot($user))
                                                    <button type="button"
                                                            @click="$store.deleteForm.show('Leave Team', 'Are you sure you want to leave {{ $team->name }}?', '{{ route('teams.members.destroy', ['team' => $team, 'member' => $user]) }}', 'Leave'); open = false"
                                                            class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition-colors duration-150"
                                                            role="menuitem">
                                                        <svg class="inline w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                                        </svg>
                                                        Leave Team
                                                    </button>
                                                @endif

                                                @if ($team->owner->is($user) && ! $team->is_personal && $team->subscriptions_count === 0)
                                                    <button type="button"
                                                            @click="$store.deleteForm.show('Delete Team', 'Are you sure you want to delete {{ $team->name }}? This action cannot be undone.', '{{ route('teams.destroy', ['team' => $team]) }}', 'Delete'); open = false"
                                                            class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition-colors duration-150"
                                                            role="menuitem">
                                                        <svg class="inline w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                        </svg>
                                                        Delete Team
                                                    </button>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </x-table.td>
                        </tr>
                    @endforeach
                </x-table>
            </div>
        @else
            <div class="text-center py-12">
                <div class="mx-auto h-12 w-12 text-gray-400 mb-4">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" class="w-full h-full">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/>
                    </svg>
                </div>
                <h3 class="mt-4 text-lg font-medium text-gray-900">No teams found</h3>
                <p class="mt-2 text-sm text-gray-500">
                    @if(request()->hasAny(['search', 'owned', 'joined', 'personal']))
                        Try adjusting your search or filter criteria.
                    @else
                        Get started by creating your first team or joining an existing one.
                    @endif
                </p>
                @if(request()->hasAny(['search', 'owned', 'joined', 'personal']))
                    <div class="mt-4">
                        <a href="{{ route('teams.index') }}" class="text-blue-600 hover:text-blue-500 text-sm font-medium">
                            Clear all filters
                        </a>
                    </div>
                @else
                    <div class="mt-6 flex items-center justify-center space-x-4">
                        <x-link.primary :to="route('teams.create')">
                            Create Team
                        </x-link.primary>
                        <x-link.secondary :to="route('teams.joining')">
                            Join Team
                        </x-link.secondary>
                    </div>
                @endif
            </div>
        @endif
    </section>

</x-layouts.app>
