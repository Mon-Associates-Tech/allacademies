<x-layouts.app title="Edit Academic Level" :has-action="false">
    <x-slot name="breadcrumb">
        <x-breadcrumb :paths="[
            'Academic Groups' => route('academic-groups.index'),
            $academicLevel->academicGroup->name => route('academic-groups.show', ['academic_group' => $academicLevel->academicGroup]),
            'Academic Levels' => route('academic-groups.academic-levels.index', ['academic_group' => $academicLevel->academicGroup]),
        ]" />
    </x-slot>

    <div class="mx-auto max-w-lg">
        <form method="POST" action="{{ route('academic-levels.update', ['academic_level' => $academicLevel]) }}">
            @csrf
            @method('PATCH')
            <div class="grid grid-cols-2 gap-4">
                <div class="sm:col-span-2 max-w-md">
                    <x-form.input name="name" type="text" :value="$academicLevel->name" />
                </div>
                <div class="sm:col-span-2 max-w-md">
                    <x-form.input name="label" type="text" :value="$academicLevel->label"  />
                </div>
            </div>
            <div class="flex justify-end mt-3 max-w-md">
                <x-button.primary class="ml-2">Update Academic Level</x-button.primary>
            </div>
        </form>
    </div>

</x-layouts.app>
