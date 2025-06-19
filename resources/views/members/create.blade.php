<x-layouts.app title="New Member" title-align-center="true">
    <x-slot name="breadcrumb">
        <x-breadcrumb :paths="[
            'Teams' => route('teams.index'),
            'Members' => route('teams.members.index', ['team' => $team])
        ]" />
    </x-slot>

    <div class="max-w-xl mx-auto">
        <form method="POST" action="{{ route('teams.members.store', ['team' => $team]) }}">
            @csrf
            <div class="grid sm:grid-cols-3 gap-4">
                <div class="sm:col-span-2">
                    <x-form.input name="email" type="email" />
                </div>
            </div>
            <div class="flex justify-center grid-cols-1 mt-3">
                <x-button.primary class="ml-2">Add Member</x-button.primary>
            </div>
        </form>
    </div>

</x-layouts.app>
