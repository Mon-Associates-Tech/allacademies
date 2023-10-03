<x-auth title="Decline Team">
    <x-slot name="breadcrumb">
        <x-breadcrumb :paths="[
            'Pending Teams' => route('pending-teams.index'),
            'Team' => route('pending-teams.show', [
                'pending_team' => $team,
            ]),
        ]" />

    </x-slot>
    <form method="POST" action="{{ route('pending-teams.decline_team', ['pending_team' => $team]) }}"
        enctype="multipart/form-data">
        @csrf
        <label class="block text-gray-800 text-sm mt-4 mb-4 font-medium">Decline <span class="font-bold">
                {{ $team->name }} </span></label>
        <div class="grid grid-cols-3 gap-4">
            <div class="col-span-2">
                <input name="status" type="text" value="declined" hidden />
            </div>
            <div class="col-span-2 space-y-2">
                <x-form.textarea name="reason" type="text" />
            </div>
        </div>
        <div class="flex justify-end mt-3">
            <x-button.primary class="ml-2">Decline Team</x-button.primary>
        </div>
    </form>
</x-auth>
