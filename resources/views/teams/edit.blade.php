<x-auth title="Edit Team">
    <x-slot name="breadcrumb">
        <x-breadcrumb :paths="[
            'Teams' => route('teams.index'),
        ]" />
    </x-slot>
    <form method="POST" action="{{ route('teams.update', ['team' => $team]) }}" enctype="multipart/form-data">
        @csrf
        @method('PATCH')
        <div class="grid grid-cols-3 gap-4">
            <div class="col-span-2">
                <x-form.input name="name" type="text" :value="$team->name" />
            </div>
            @if(!$team->is_personal)
                <div class="col-span-2">
                    <x-form.input name="school" type="text" :value="is_null($team->metaData) ? null : $team->metaData->meta['school'] ?? '' "  />
                </div>
                <div class="col-span-2">
                    <x-form.input name="department" type="text" :value="is_null($team->metaData) ? null : $team->metaData->meta['department'] ?? '' "  />
                </div>
                <div class="col-span-2">
                    <x-form.file-upload name="logo"/>
                </div>
            @endif
        </div>
        <div class="flex justify-end mt-3">
            <x-button.primary class="ml-2">Update Team</x-button.primary>
        </div>
    </form>
</x-auth>