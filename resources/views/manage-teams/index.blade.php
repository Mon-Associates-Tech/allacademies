<x-auth title="Pending Teams">
    <x-slot name="breadcrumb">
        <x-breadcrumb />
    </x-slot>

    @if ($pendingTeams->count())
    <x-table>
        <x-slot name="head">
            <tr>
                <x-table.th>Name</x-table.th>
                <x-table.th><span class="sr-only">Actions</span></x-table.th>
            </tr>
        </x-slot>

        @foreach ($pendingTeams as $team)
            <tr>
                <x-table.td bold>{{ $team->name }}</x-table.td>
                <x-table.td action>
                    <form class="inline" method="POST" action="{{ route('manage-teams.approve', ['team' => $team]) }}">
                        @csrf
                        <button class="text-primary-600 hover:text-primary-900">Approve</button>
                    </form>
                    <a class="text-primary-600 hover:text-primary-900" href="{{ route('manage-teams.edit', ['team' => $team]) }}">Decline</a>
                </x-table.td>
            </tr>
        @endforeach
    </x-table>

    <div class="mt-3">
        {{ $pendingTeams->links() }}
    </div>
    @else
    <x-blank />
    @endif
</x-auth>
