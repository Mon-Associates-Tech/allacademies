<x-auth title="Teams (Auditing)">
    <x-slot name="breadcrumb">
        <x-breadcrumb />
    </x-slot>

    @if ($auditTeams->count())
        <x-table>
            <x-slot name="head">
                <tr>
                    <x-table.th>Team</x-table.th>
                    <x-table.th>Owner</x-table.th>
                    <x-table.th><span class="sr-only">Actions</span></x-table.th>
                </tr>
            </x-slot>

            @foreach ($auditTeams as $team)
                <tr>
                    <x-table.td bold>{{ $team->name }}</x-table.td>
                    <x-table.td>{{ $team->owner->name }}</x-table.td>
                    <x-table.td action>
                        <x-action name="view" :to="route('audit-teams.show', [
                            'audit_team' => $team,
                        ])" />
                    </x-table.td>
                </tr>
            @endforeach
        </x-table>

        <div class="mt-3">
            {{ $auditTeams->links() }}
        </div>
    @else
        <x-blank />
    @endif
</x-auth>
