<x-auth title="Edit Team">
    <x-slot name="breadcrumb">
        <x-breadcrumb :paths="[
            'Teams' => route('teams.index'),
        ]" />
    </x-slot>
    <div class="grid grid-cols-3 gap-4">
        @if (!$team->is_personal)
            @if ($team->metaData)
                @if ($team->status->value == 'approved')
                    <x-alert.info name="Approved"
                        message="You are all set to create examinations. Institution details will be used for examination heading."
                        :svg="['M4.5 12.75l6 6 9-13.5']" />
                @elseif($team->status->value == 'pending')
                    <x-alert.info name="Pending"
                        message="Institution details pending approval. Details must be approved before you can create examinations because they will be used for examination heading."
                        :svg="[
                            'M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99',
                        ]" />
                @elseif($team->status->value == 'declined')
                    <x-alert.info name="Declined"
                        message="Institution details have been declined. Please make the necessary changes and update team for review."
                        :svg="['M9.75 9.75l4.5 4.5m0-4.5l-4.5 4.5M21 12a9 9 0 11-18 0 9 9 0 0118 0z']" />
                @endif
            @else
                <x-alert.info name="Note"
                    message="Institution details must be provided. These details will be used for examination heading and they must be approved before you can create examinations."
                    :svg="[
                        'M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z',
                    ]" />
            @endif
        @endif
    </div>

    <form method="POST" action="{{ route('teams.update', ['team' => $team]) }}" enctype="multipart/form-data">
        @csrf
        @method('PATCH')
        <div class="grid grid-cols-3 gap-4">
            <div class="col-span-2">
                <x-form.input name="name" type="text" :value="$team->name" />
            </div>
            @if (!$team->is_personal)
                <div class="col-span-2 space-y-2">
                    @livewire('institution-details', ['team' => $team])
                </div>
            @endif
        </div>
        <div class="flex justify-end mt-3">
            <x-button.primary class="ml-2">Update Team</x-button.primary>
        </div>
    </form>
</x-auth>
