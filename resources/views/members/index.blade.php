<x-auth :title="'Members of ' . $team->name">
    <x-slot name="breadcrumb">
        <x-breadcrumb :paths="[
            'Teams' => route('teams.index'),
        ]" />
    </x-slot>
    <x-slot name="action">
        @if ($team->owner->is($user) && !$team->is_personal)
            <x-link.primary :to="route('teams.members.create', ['team' => $team])">New Member</x-link.primary>
        @endif
    </x-slot>

    <x-table>
        <x-slot name="head">
            <tr>
                <x-table.th>Name</x-table.th>
                <x-table.th>Email</x-table.th>
                <x-table.th>Role</x-table.th>
                <x-table.th><span class="sr-only">Actions</span></x-table.th>
            </tr>
        </x-slot>

        @foreach ($team->members as $member)
            <tr>
                <x-table.td bold>{{ $member->name }}</x-table.td>
                <x-table.td>{{ $member->email }}</x-table.td>
                <x-table.td>{{ $team->owner->is($member) ? 'owner' : $member->pivot->role }}</x-table.td>
                <x-table.td>
                @if ($team->owner->is($user) && $member->isNot($user))
                    <button x-data="{}" x-on:click="$store.deleteForm.show('Danger', 'Are you sure you want to remove {{ $member->name }} from {{ $team->name }}', '{{ route('teams.members.destroy', ['team' => $team, 'member' => $member]) }}', 'Remove')" class="text-primary-600 hover:text-primary-900 mr-2">Remove</button>
                    <a class="text-primary-600 hover:text-primary-900" href="{{ route('members.edit', ['team' => $team, 'member' => $member]) }}">Edit Member</a>
                @endif
                </x-table.td>
            </tr>
        @endforeach
    </x-table>
</x-auth>
