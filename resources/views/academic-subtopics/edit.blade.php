<x-auth title="Edit Academic Topic" :main-only="true">
    <x-slot name="breadcrumb">
        <x-breadcrumb :paths="[
            'Academic Groups' => route('academic-groups.index'),
            $academicTopic->academicSubject->academicLevel->academicGroup->name => route('academic-groups.show', ['academic_group' => $academicTopic->academicSubject->academicLevel->academicGroup]),
            'Academic Levels' => route('academic-groups.academic-levels.index', ['academic_group' => $academicTopic->academicSubject->academicLevel->academicGroup]),
            $academicTopic->academicSubject->academicLevel->name => route('academic-levels.show', ['academic_level' => $academicTopic->academicSubject->academicLevel]),
            'Academic Subjects' => route('academic-levels.academic-subjects.index', ['academic_level' => $academicTopic->academicSubject->academicLevel]),
            $academicTopic->academicSubject->name => route('academic-subjects.show', ['academic_subject' => $academicTopic->academicSubject]),
            'Academic Topics' => route('academic-subjects.academic-topics.index', ['academic_subject' => $academicTopic->academicSubject]),
        ]" />
    </x-slot>
    <div class="mx-auto bg-white px-4 py-8 rounded-md">
        <form method="POST" action="{{ route('academic-topics.update', ['academic_topic' => $academicTopic]) }}">
            @csrf
            @method('PATCH')
            <div class="">
                <div class="max-w-">
                    <x-form.input  name="name" type="text" :value="$academicTopic->name" />
                </div>
            </div>
            <div class="flex justify-end mt-3">
                <x-button.primary class="ml-2">Update Academic Topic</x-button.primary>
            </div>
        </form>
    </div>
</x-auth>
