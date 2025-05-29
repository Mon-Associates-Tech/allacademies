<x-layouts.app title="New Academic Subject" :has-action="false">
    <x-slot name="breadcrumb">
        <x-breadcrumb :paths="[
            'Academic Groups' => route('academic-groups.index'),
            $academicLevel->academicGroup->name => route('academic-groups.show', ['academic_group' => $academicLevel->academicGroup]),
            'Academic Levels' => route('academic-groups.academic-levels.index', ['academic_group' => $academicLevel->academicGroup]),
            $academicLevel->name => route('academic-levels.show', ['academic_level' => $academicLevel]),
            'Academic Subjects' => route('academic-levels.academic-subjects.index', ['academic_level' => $academicLevel]),
        ]" />
    </x-slot>

    <div class="mx-auto max-w-lg">
        <form method="POST" action="{{ route('academic-levels.academic-subjects.store', ['academic_level' => $academicLevel]) }}">
            @csrf
            <div class="grid sm:grid-cols-2 gap-4">
                <div class="sm:col-span-2 max-w-md">
                    <x-form.input name="name" type="text" />
                </div>
                <div class="sm:col-span-2 max-w-md">
                    <x-form.input name="code" type="text" label="Subject Code" />
                </div>
            </div>
            <div class="flex justify-end mt-3 max-w-md">
                <x-button.primary class="ml-2">Create Academic Subject</x-button.primary>
            </div>
        </form>
    </div>

</x-layouts.app>
