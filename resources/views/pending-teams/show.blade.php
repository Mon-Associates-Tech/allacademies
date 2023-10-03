<x-auth title="Institution Details">
    <x-slot name="breadcrumb">
        <x-breadcrumb :paths="[
            'Pending Teams' => route('pending-teams.index'),
        ]" />
    </x-slot>
    <div>
        @livewire('show-institution-details', ['team' => $team, 'institutionDetails' => $institutionDetails])
    </div>

    <div class="flex justify-end space-x-2">
        <form class="inline" method="POST" action="{{ route('pending-teams.approve', ['team' => $team]) }}">
            @csrf
            <x-button.secondary>Approve</x-button.secondary>
        </form>
        <x-link.primary :to="route('pending-teams.decline', ['team' => $team])"> Decline </x-link.primary>
    </div>
</x-auth>
