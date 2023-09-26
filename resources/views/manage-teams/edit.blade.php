<x-auth title="Decline Team">
    <x-slot name="breadcrumb">
        <x-breadcrumb :paths="[
            'Pending Teams' => route('manage-teams.index'),
        ]" />
    </x-slot>
    <form method="POST" action="{{ route('manage-teams.decline', ['team' => $team]) }}" enctype="multipart/form-data">
        @csrf
        <div class="grid grid-cols-3 gap-4">
            <div class="col-span-2">
                <x-form.input name="name" type="text" :value="$team->name" />
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