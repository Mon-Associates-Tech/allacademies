<x-auth title="New Academic Group" :has-action="false">
    <x-slot name="breadcrumb">
        <x-breadcrumb :paths="[
            'Academic Groups' => route('academic-groups.index'),
        ]" />
    </x-slot>

    <div class="mx-auto bg-white px-4 py-8 rounded-md">
        <form method="POST" action="{{ route('academic-groups.store') }}">
            @csrf
            <div class="grid sm:grid-col gap-4">
                <div class="sm:col-span-2">
                    <x-form.input name="name" type="text" />
                </div>
            </div>
            <div class="flex justify-end mt-5">
                <x-button.primary class="ml-2">Create Academic Group</x-button.primary>
            </div>
        </form>
    </div>

</x-auth>
