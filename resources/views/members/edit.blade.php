<x-auth title="Edit Member">
    <x-slot name="breadcrumb">
        <x-breadcrumb :paths="[
            'Teams' => route('teams.index'),
            'Members' => route('teams.members.index', ['team' => $team])
        ]" />
    </x-slot>

    <form method="POST" action="{{ route('members.update',  ['team' => $team, 'member' => $member]) }}">
        @csrf
        <div class="grid sm:grid-cols-3 gap-4">
            <div class="sm:col-span-2">
                <x-form.input name="email" type="email" :value="$member->email" readonly/>
            </div>
            <div class="sm:col-span-2">
                <x-form.select name="role" :options="[
                    'member' => 'Member',
                    'admin' => 'Admin',
                ]"
                    :value="$member->pivot->role" />
            </div>
        </div>
        <div class="flex justify-end mt-3">
            <x-button.primary class="ml-2">Update Member</x-button.primary>
        </div>
    </form>
</x-auth>
