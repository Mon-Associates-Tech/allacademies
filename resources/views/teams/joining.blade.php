<x-auth title="Join Team">
    <x-slot name="breadcrumb">
        <x-breadcrumb :paths="[
            'Teams' => route('teams.index'),
        ]" />
    </x-slot>

    <form method="POST" action="{{ route('teams.add-member') }}">
        @csrf
        <div class="grid sm:grid-cols-3 gap-4">
            <div class="sm:col-span-2">
                <x-form.input name="code" type="text" />
            </div>
        </div>
        <div class="flex justify-end mt-3">
            <x-button.primary class="ml-2">Join Team</x-button.primary>
        </div>
    </form>
</x-auth>