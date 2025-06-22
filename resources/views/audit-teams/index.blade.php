<x-layouts.app title="Teams (Auditing)">
    <x-slot name="breadcrumb">
        <x-breadcrumb />
    </x-slot>

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-6">
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="text-2xl font-bold text-gray-900">{{ $stats['total'] }}</div>
                    </div>
                    <div class="ml-4">
                        <div class="text-sm font-medium text-gray-500">Total Pending</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="text-2xl font-bold text-gray-900">{{ $stats['this_week'] }}</div>
                    </div>
                    <div class="ml-4">
                        <div class="text-sm font-medium text-gray-500">This Week</div>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="text-2xl font-bold text-gray-900">{{ $stats['this_month'] }}</div>
                    </div>
                    <div class="ml-4">
                        <div class="text-sm font-medium text-gray-500">This Month</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters and Search -->
    <div class="bg-white shadow-sm sm:rounded-lg mb-6">
        <div class="p-6 border-b">
            <form method="GET" action="{{ route('audit-teams.index') }}" class="space-y-4">
                <div class="flex flex-col sm:flex-row gap-4">
                    <!-- Search -->
                    <div class="flex-1">
                        <label for="search" class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                        <input type="text"
                               name="search"
                               id="search"
                               value="{{ request('search') }}"
                               placeholder="Search teams or owners..."
                               class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                    </div>

                    <!-- Sort -->
                    <div class="sm:w-48">
                        <label for="sort" class="block text-sm font-medium text-gray-700 mb-1">Sort by</label>
                        <select name="sort"
                                id="sort"
                                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                            <option value="oldest_updated" {{ request('sort') == 'oldest_updated' ? 'selected' : '' }}>Oldest Updated</option>
                            <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Newest</option>
                            <option value="name_asc" {{ request('sort') == 'name_asc' ? 'selected' : '' }}>Name A-Z</option>
                            <option value="name_desc" {{ request('sort') == 'name_desc' ? 'selected' : '' }}>Name Z-A</option>
                            <option value="owner_asc" {{ request('sort') == 'owner_asc' ? 'selected' : '' }}>Owner A-Z</option>
                            <option value="owner_desc" {{ request('sort') == 'owner_desc' ? 'selected' : '' }}>Owner Z-A</option>
                            <option value="members_desc" {{ request('sort') == 'members_desc' ? 'selected' : '' }}>Most Members</option>
                            <option value="members_asc" {{ request('sort') == 'members_asc' ? 'selected' : '' }}>Least Members</option>
                        </select>
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row gap-2">
                    <button type="submit" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        <svg class="-ml-1 mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                        Filter
                    </button>

                    @if(request()->hasAny(['search', 'sort']))
                        <a href="{{ route('audit-teams.index') }}"
                           class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Clear Filters
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    @if ($auditTeams->count())
        <!-- Bulk Actions -->
        <div class="bg-white shadow-sm sm:rounded-lg mb-6">
            <div class="p-4 border-b">
                <form id="bulk-actions-form" method="POST" action="{{ route('audit-teams.bulk-approve') }}">
                    @csrf
                    <div class="flex items-center gap-4">
                        <label class="flex items-center">
                            <input type="checkbox" id="select-all" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <span class="ml-2 text-sm text-gray-700">Select All</span>
                        </label>

                        <button type="submit"
                                id="bulk-approve-btn"
                                disabled
                                class="inline-flex items-center px-3 py-2 border border-transparent text-xs font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 disabled:opacity-50 disabled:cursor-not-allowed">
                            <svg class="-ml-1 mr-1 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            Bulk Approve
                        </button>

                        <span id="selected-count" class="text-sm text-gray-500">0 selected</span>
                    </div>
                </form>
            </div>
        </div>

        <!-- Teams Table -->
        <x-table>
            <x-slot name="head">
                <tr>
                    <x-table.th>
                        <span class="sr-only">Select</span>
                    </x-table.th>
                    <x-table.th>Team</x-table.th>
                    <x-table.th>Owner</x-table.th>
                    <x-table.th>Members</x-table.th>
                    <x-table.th>Status</x-table.th>
                    <x-table.th>Created</x-table.th>
                    <x-table.th>Last Updated</x-table.th>
                    <x-table.th><span class="sr-only">Actions</span></x-table.th>
                </tr>
            </x-slot>

            @foreach ($auditTeams as $team)
                <tr class="hover:bg-gray-50">
                    <x-table.td>
                        <input type="checkbox"
                               name="team_ids[]"
                               value="{{ $team->id }}"
                               form="bulk-actions-form"
                               class="team-checkbox rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </x-table.td>

                    <x-table.td bold>
                        {{ $team->name }}
                        @if($team->is_personal)
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800 ml-2">
                                Personal
                            </span>
                        @endif
                    </x-table.td>

                    <x-table.td>
                        <div class="flex items-center">
                            <div class="flex-shrink-0 h-8 w-8">
                                <div class="h-8 w-8 rounded-full bg-gray-300 flex items-center justify-center">
                                    <span class="text-sm font-medium text-gray-700">
                                        {{ substr($team->owner->name, 0, 1) }}
                                    </span>
                                </div>
                            </div>
                            <div class="ml-3">
                                <div class="text-sm font-medium text-gray-900">{{ $team->owner->name }}</div>
                                <div class="text-sm text-gray-500">{{ $team->owner->email }}</div>
                            </div>
                        </div>
                    </x-table.td>

                    <x-table.td>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                            {{ $team->members_count }} {{ Str::plural('member', $team->members_count) }}
                        </span>
                    </x-table.td>

                    <x-table.td>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                            </svg>
                            Pending
                        </span>
                    </x-table.td>

                    <x-table.td>
                        <div class="text-sm text-gray-900">{{ $team->created_at->format('M j, Y') }}</div>
                        <div class="text-sm text-gray-500">{{ $team->created_at->format('g:i A') }}</div>
                    </x-table.td>

                    <x-table.td>
                        <div class="text-sm text-gray-900">{{ $team->updated_at->format('M j, Y') }}</div>
                        <div class="text-sm text-gray-500">{{ $team->updated_at->diffForHumans() }}</div>
                    </x-table.td>

                    <x-table.td action>
                        <div class="flex items-center gap-2">
                            <x-action name="view" :to="route('audit-teams.show', ['audit_team' => $team])" />

                            <form method="POST" action="{{ route('audit-teams.approve', ['audit_team' => $team]) }}" class="inline">
                                @csrf
                                <button type="submit"
                                        onclick="return confirm('Are you sure you want to approve this team?')"
                                        class="inline-flex items-center px-2 py-1 border border-transparent text-xs font-medium rounded text-green-700 bg-green-100 hover:bg-green-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                    Approve
                                </button>
                            </form>

                            <a href="{{ route('audit-teams.reason', ['audit_team' => $team]) }}"
                               class="inline-flex items-center px-2 py-1 border border-transparent text-xs font-medium rounded text-red-700 bg-red-100 hover:bg-red-200 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">
                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                </svg>
                                Decline
                            </a>
                        </div>
                    </x-table.td>
                </tr>
            @endforeach
        </x-table>

        <div class="mt-6">
            {{ $auditTeams->links() }}
        </div>
    @else
        <x-blank>
            <div class="text-center">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">No pending teams</h3>
                <p class="mt-1 text-sm text-gray-500">
                    @if(request()->hasAny(['search', 'sort']))
                        No teams match your current filters.
                    @else
                        There are no teams pending approval at this time.
                    @endif
                </p>
                @if(request()->hasAny(['search', 'sort']))
                    <div class="mt-6">
                        <a href="{{ route('audit-teams.index') }}"
                           class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                            Clear Filters
                        </a>
                    </div>
                @endif
            </div>
        </x-blank>
    @endif

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const selectAllCheckbox = document.getElementById('select-all');
                const teamCheckboxes = document.querySelectorAll('.team-checkbox');
                const bulkApproveBtn = document.getElementById('bulk-approve-btn');
                const selectedCount = document.getElementById('selected-count');

                function updateBulkActions() {
                    const checkedBoxes = document.querySelectorAll('.team-checkbox:checked');
                    const count = checkedBoxes.length;

                    selectedCount.textContent = `${count} selected`;
                    bulkApproveBtn.disabled = count === 0;

                    // Update select all checkbox state
                    if (count === 0) {
                        selectAllCheckbox.indeterminate = false;
                        selectAllCheckbox.checked = false;
                    } else if (count === teamCheckboxes.length) {
                        selectAllCheckbox.indeterminate = false;
                        selectAllCheckbox.checked = true;
                    } else {
                        selectAllCheckbox.indeterminate = true;
                        selectAllCheckbox.checked = false;
                    }
                }

                selectAllCheckbox.addEventListener('change', function() {
                    teamCheckboxes.forEach(checkbox => {
                        checkbox.checked = this.checked;
                    });
                    updateBulkActions();
                });

                teamCheckboxes.forEach(checkbox => {
                    checkbox.addEventListener('change', updateBulkActions);
                });

                // Confirmation for bulk approve
                document.getElementById('bulk-actions-form').addEventListener('submit', function(e) {
                    const checkedBoxes = document.querySelectorAll('.team-checkbox:checked');
                    const count = checkedBoxes.length;

                    if (count > 0) {
                        if (!confirm(`Are you sure you want to approve ${count} team(s)?`)) {
                            e.preventDefault();
                        }
                    }
                });
            });
        </script>
    @endpush
</x-layouts.app>
