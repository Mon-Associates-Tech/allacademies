<x-auth title="New Member">
    <x-slot name="breadcrumb">
        <x-breadcrumb :paths="[
            'Teams' => route('teams.index'),
            'Members' => route('teams.members.index', ['team' => $team])
        ]" />
    </x-slot>

    <form method="POST" action="{{ route('teams.members.store', ['team' => $team]) }}">
        @csrf
        <div class="grid sm:grid-cols-3 gap-4">
            <div class="sm:col-span-2">
                <x-form.input name="email" type="email" />
            </div>
        </div>
        <div class="flex justify-end mt-3">
            <x-button.primary class="ml-2">Add Member</x-button.primary>
        </div>
    </form>
</x-auth>
