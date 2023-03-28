<x-auth title="Teams">
    <x-slot name="breadcrumb">
        <x-breadcrumb />
    </x-slot>
    <x-slot name="action">
        <x-link.primary :to="route('teams.create')">New Team</x-link.primary>
    </x-slot>

    <x-table>
        <x-slot name="head">
            <tr>
                <x-table.th>Name</x-table.th>
                <x-table.th>Owner</x-table.th>
                <x-table.th><span class="sr-only">Actions</span></x-table.th>
            </tr>
        </x-slot>

        @foreach ($teams as $team)
            <tr>
                <x-table.td bold>
                    {{ $team->name }}
                    @if ($team->owner->is($user) && $team->is_personal)
                    <span class="inline-flex items-center rounded-full bg-primary-100 px-2.5 py-0.5 text-xs font-medium text-primary-800">Personal</span>
                    @endif
                    @if ($user->currentTeam->is($team))
                    <span class="inline-flex items-center rounded-full bg-gray-100 px-2.5 py-0.5 text-xs font-medium text-gray-800 capitalize">current</span>
                    @endif
                </x-table.td>
                <x-table.td>{{ $team->owner->is($user) ? 'You' : $team->owner->name }}</x-table.td>
                <x-table.td action>
                    @if ($team->owner->isNot($user))
                        <button x-data="{}" x-on:click="$store.deleteForm.show('Danger', 'Are you sure you want to leave {{ $team->name }}', '{{ route('teams.members.destroy', ['team' => $team, 'member' => $user]) }}', 'Leave')" class="text-primary-600 hover:text-primary-900">Leave Team</button>
                    @endif
                    @if ($team->owner->is($user) && ! $team->is_personal && $team->subscriptions_count === 0)
                        <button x-data="{}" x-on:click="$store.deleteForm.show('Danger', 'Are you sure you want to delete {{ $team->name }}', '{{ route('teams.destroy', ['team' => $team]) }}')" class="text-primary-600 hover:text-primary-900">Delete Team</button>
                    @endif
                    @if ($user->currentTeam->isNot($team))
                    <form class="inline" method="POST" action="{{ route('teams.activate', ['team' => $team]) }}">
                        @csrf
                        <button class="text-primary-600 hover:text-primary-900">Set as current</button>
                    </form>
                    @endif
                    @if ($team->owner->is($user))
                    <a class="text-primary-600 hover:text-primary-900" href="{{ route('teams.edit', ['team' => $team]) }}">Edit Team</a>
                    @endif
                    <a class="text-primary-600 hover:text-primary-900" href="{{ route('teams.members.index', ['team' => $team]) }}">List members</a>
                </x-table.td>
            </tr>
        @endforeach
    </x-table>
</x-auth>

{{-- <x-dashboard title="Team members" summary="All team members">
    <table class="w-full divide-y divide-gray-300">
        <caption>
            <div class="flex items-center justify-between px-2 py-3">
                <div class="font-medium text-gray-500 tracking-wide">
                    Owned Team
                </div>
                <div>
                </div>
            </div>
        </caption>
        <thead>
            <tr>
                <x-table.th>ID</x-table.th>
                <x-table.th>Name</x-table.th>
                <x-table.th><span class="sr-only">Actions</span></x-table.th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
            @foreach ($ownedTeams as $team)
            <tr>
                <td class="p-2 text-sm text-gray-500">#{{ $team->id }}</td>
                <td class="p-2 text-sm text-gray-900 font-medium">{{ $team->name }}</td>
                <td class="p-2 text-sm text-primary-600 space-x-3 flex">
                    <a href="{{ route('teams.members.create', ['team' => $team]) }}">Add member</a>
                    @if (! $team->is_personal)
                    <form method="POST" action="{{ route('teams.destroy', ['team' => $team]) }}">
                        @csrf
                        @method('DELETE')
                        <button>Remove</button>
                    </form>
                    @endif
                    @if ($user->currentTeam->isNot($team))
                    <form method="POST" action="{{ route('teams.activate', ['team' => $team]) }}">
                        @csrf
                        <button>Set as current</button>
                    </form>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <table class="w-full divide-y divide-gray-300 mt-10">
        <caption>
            <div class="flex items-center justify-between px-2 py-3">
                <div class="font-medium text-gray-500 tracking-wide">
                    Joined Team
                </div>
                <div>
                </div>
            </div>
        </caption>
        <thead>
            <tr>
                <x-table.th>ID</x-table.th>
                <x-table.th>Name</x-table.th>
                <x-table.th>Owner</x-table.th>
                <x-table.th><span class="sr-only">Actions</span></x-table.th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
            @foreach ($joinedTeams as $team)
            <tr>
                <td class="p-2 text-sm text-gray-500">#{{ $team->id }}</td>
                <td class="p-2 text-sm text-gray-900 font-medium">{{ $team->name }}</td>
                <td class="p-2 text-sm text-gray-500">{{ $team->owner->name }}</td>
                <td class="p-2 text-sm text-primary-600 space-x-3 flex">
                    <form method="POST" action="{{ route('teams.members.destroy', ['team' => $team, 'member' => $user]) }}">
                        @csrf
                        @method('DELETE')
                        <button>leave</button>
                    </form>
                    @if ($user->currentTeam->isNot($team))
                    <form method="POST" action="{{ route('teams.activate', ['team' => $team]) }}">
                        @csrf
                        <button>Set as current</button>
                    </form>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <table class="w-full divide-y divide-gray-300 mt-10">
        <caption>
            <div class="flex items-center justify-between px-2 py-3">
                <div class="font-medium text-gray-500 tracking-wide">
                    {{ $user->currentTeam->name }} members (Current)
                </div>
                <div>
                </div>
            </div>
        </caption>
        <thead>
            <tr>
                <x-table.th>ID</x-table.th>
                <x-table.th>Name</x-table.th>
                <x-table.th>Email</x-table.th>
                <x-table.th><span class="sr-only">Actions</span></x-table.th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-200">
            @foreach ($user->currentTeam->members as $member)
            <tr>
                <td class="p-2 text-sm text-gray-500">#{{ $member->id }}</td>
                <td class="p-2 text-sm text-gray-900 font-medium">{{ $member->name }}</td>
                <td class="p-2 text-sm text-gray-500">{{ $member->email }}</td>
                <td class="p-2 text-sm text-primary-600 space-x-3 flex">
                    @if($user->currentTeam->owner->is($user) && $user->currentTeam->owner->isNot($member))
                    <form method="POST" action="{{ route('teams.members.destroy', ['team' => $team, 'member' => $member]) }}">
                        @csrf
                        @method('DELETE')
                        <button>Remove</button>
                    </form>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</x-dashboard> --}}