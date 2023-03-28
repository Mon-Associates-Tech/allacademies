<x-auth title="New Academic Topic">
    <x-slot name="breadcrumb">
        <x-breadcrumb :paths="[
            'Academic Groups' => route('academic-groups.index'),
            $academicSubject->academicLevel->academicGroup->name => route('academic-groups.show', ['academic_group' => $academicSubject->academicLevel->academicGroup]),
            'Academic Levels' => route('academic-groups.academic-levels.index', ['academic_group' => $academicSubject->academicLevel->academicGroup]),
            $academicSubject->academicLevel->name => route('academic-levels.show', ['academic_level' => $academicSubject->academicLevel]),
            'Academic Subjects' => route('academic-levels.academic-subjects.index', ['academic_level' => $academicSubject->academicLevel]),
            $academicSubject->name => route('academic-subjects.show', ['academic_subject' => $academicSubject]),
            'Academic Topics' => route('academic-subjects.academic-topics.index', ['academic_subject' => $academicSubject]),
        ]" />
    </x-slot>

    <form method="POST" action="{{ route('academic-subjects.academic-topics.store', ['academic_subject' => $academicSubject]) }}">
        @csrf
        <div class="grid sm:grid-cols-3 gap-4">
            <div class="sm:col-span-2">
                <x-form.input name="name" type="text" />
            </div>
        </div>
        <div class="flex justify-end mt-3">
            <x-button.primary class="ml-2">Create Academic Topic</x-button.primary>
        </div>
    </form>
</x-auth>
