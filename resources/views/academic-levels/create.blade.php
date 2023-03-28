<x-auth title="New Academic Level">
    <x-slot name="breadcrumb">
        <x-breadcrumb :paths="[
            'Academic Groups' => route('academic-groups.index'),
            $academicGroup->name => route('academic-groups.show', ['academic_group' => $academicGroup]),
            'Academic Levels' => route('academic-groups.academic-levels.index', ['academic_group' => $academicGroup]),
        ]" />
    </x-slot>

    <form method="POST" action="{{ route('academic-groups.academic-levels.store', ['academic_group' => $academicGroup]) }}">
        @csrf
        <div class="grid sm:grid-cols-3 gap-4">
            <div class="sm:col-span-2">
                <x-form.input name="name" type="text" />
            </div>
            <div>
                <x-form.input name="label" type="text"  />
            </div>
        </div>
        <div class="flex justify-end mt-3">
            <x-button.primary class="ml-2">Create Academic Level</x-button.primary>
        </div>
    </form>
</x-auth>
