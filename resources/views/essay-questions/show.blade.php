<x-auth title="Essay Question Details">
    <x-slot name="breadcrumb">
        <x-breadcrumb :paths="[
            'Academic Groups' => route('academic-groups.index'),
            $essayQuestion->academicTopic->academicSubject->academicLevel->academicGroup->name => route('academic-groups.show', ['academic_group' => $essayQuestion->academicTopic->academicSubject->academicLevel->academicGroup]),
            'Academic Levels' => route('academic-groups.academic-levels.index', ['academic_group' => $essayQuestion->academicTopic->academicSubject->academicLevel->academicGroup]),
            $essayQuestion->academicTopic->academicSubject->academicLevel->name => route('academic-levels.show', ['academic_level' => $essayQuestion->academicTopic->academicSubject->academicLevel]),
            'Academic Subjects' => route('academic-levels.academic-subjects.index', ['academic_level' => $essayQuestion->academicTopic->academicSubject->academicLevel]),
            $essayQuestion->academicTopic->academicSubject->name => route('academic-subjects.show', ['academic_subject' => $essayQuestion->academicTopic->academicSubject]),
            'Academic Topics' => route('academic-subjects.academic-topics.index', ['academic_subject' => $essayQuestion->academicTopic->academicSubject]),
            $essayQuestion->academicTopic->name => route('academic-topics.show', ['academic_topic' => $essayQuestion->academicTopic]),
            'Essay Questions' => route('academic-topics.essay-questions.index', ['academic_topic' => $essayQuestion->academicTopic]),
        ]" />
    </x-slot>

    <x-detail>
        <x-detail.data expand label="Question">{{ $essayQuestion->question->html }}</x-detail.data>

        <x-detail.data label="Academic Topic">
            <x-anchor to="{{ route('academic-topics.show', ['academic_topic' => $essayQuestion->academicTopic]) }}">
                {{ $essayQuestion->academicTopic->name }}
            </x-anchor>
        </x-detail.data>

        @can('moderate')
        <x-slot name="action">
            <x-button.secondary type="buttton" x-data="{}" x-on:click="$store.deleteForm.show('Danger', 'Are you sure you want to delete {{ $essayQuestion->question->summary }}', '{{ route('essay-questions.destroy', ['essay_question' => $essayQuestion]) }}')">Delete Essay Question</x-button.secondary>
            <x-link.primary :to="route('essay-questions.edit', ['essay_question' => $essayQuestion])">Edit Essay Question</x-link.primary>
        </x-slot>
        @endcan
    </x-detail>
</x-auth>