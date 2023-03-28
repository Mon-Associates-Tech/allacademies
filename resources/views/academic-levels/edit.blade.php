<x-auth title="Edit Academic Level">
    <x-slot name="breadcrumb">
        <x-breadcrumb :paths="[
            'Academic Groups' => route('academic-groups.index'),
            $academicLevel->academicGroup->name => route('academic-groups.show', ['academic_group' => $academicLevel->academicGroup]),
            'Academic Levels' => route('academic-groups.academic-levels.index', ['academic_group' => $academicLevel->academicGroup]),
        ]" />
    </x-slot>

    <form method="POST" action="{{ route('academic-levels.update', ['academic_level' => $academicLevel]) }}">
        @csrf
        @method('PATCH')
        <div class="grid grid-cols-3 gap-4">
            <div class="col-span-2">
                <x-form.input name="name" type="text" :value="$academicLevel->name" />
            </div>
            <div>
                <x-form.input name="label" type="text" :value="$academicLevel->label"  />
            </div>
        </div>
        <div class="flex justify-end mt-3">
            <x-button.primary class="ml-2">Update Academic Level</x-button.primary>
        </div>
    </form>
</x-auth>