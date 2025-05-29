<x-layouts.app title="Teams">


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
                    <span class="inline-flex items-center rounded-full bg-vert px-2.5 py-0.5 text-xs font-medium text-white capitalize">personal</span>
                    @endif
                    @if ($user->currentTeam->is($team))
                    <span class="inline-flex items-center rounded-full bg-range px-2.5 py-0.5 text-xs font-medium text-white capitalize">current</span>
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

    <x-slot name="action">
        <div class="flex">
            <x-link.primary class="text-nowrap" :to="route('teams.create')">New Team</x-link.primary>
            <x-link.secondary :to="route('teams.joining')" class="ml-6 text-nowrap">
                Join Team
                <span class="ml-1" aria-hidden="true"> &rarr;</span>
            </x-link.secondary>
        </div>
    </x-slot>


</x-layouts.app>
