<x-layouts.app title="Edit Academic Subject" :has-action="false">
    <x-slot name="breadcrumb">
        <x-breadcrumb :paths="[
            'Academic Groups' => route('academic-groups.index'),
            $academicSubject->academicLevel->academicGroup->name => route('academic-groups.show', ['academic_group' => $academicSubject->academicLevel->academicGroup]),
            'Academic Levels' => route('academic-groups.academic-levels.index', ['academic_group' => $academicSubject->academicLevel->academicGroup]),
            $academicSubject->academicLevel->name => route('academic-levels.show', ['academic_level' => $academicSubject->academicLevel]),
            'Academic Subjects' => route('academic-levels.academic-subjects.index', ['academic_level' => $academicSubject->academicLevel]),
        ]" />
    </x-slot>

    <div class="mx-auto max-w-lg">
    <form method="POST" action="{{ route('academic-subjects.update', ['academic_subject' => $academicSubject]) }}">
        @csrf
        @method('PATCH')
        <div class="grid grid-cols-2 gap-4">
            <div class="sm:col-span-2 max-w-md">
                <x-form.input name="name" type="text" :value="$academicSubject->name" />
            </div>
            <div class="sm:col-span-2 max-w-md">
                <x-form.input name="code" type="text" :value="$academicSubject->code"  />
            </div>
        </div>
        <div class="flex justify-end mt-3 max-w-md">
            <x-button.primary class="ml-2">Update Academic Subject</x-button.primary>
        </div>
    </form>
    </div>
</x-layouts.app>
