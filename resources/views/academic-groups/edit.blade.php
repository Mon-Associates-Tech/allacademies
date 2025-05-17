<x-auth title="Edit Academic Group" :has-action="false">
    <x-slot name="breadcrumb">
        <x-breadcrumb :paths="[
            'Academic Groups' => route('academic-groups.index'),
        ]" />
    </x-slot>

    <div class="mx-auto bg-white px-4 py-8 rounded-md">
        <form method="POST" action="{{ route('academic-groups.update', ['academic_group' => $academicGroup]) }}">
            @csrf
            @method('PATCH')
            <div class="grid grid-cols-3 gap-4">
                <div class="col-span-2">
                    <x-form.input name="name" type="text" :value="$academicGroup->name" />
                </div>
            </div>
            <div class="flex justify-end mt-5">
                <x-button.primary class="ml-2">Update Academic Group</x-button.primary>
            </div>
        </form>
    </div>

</x-auth>
