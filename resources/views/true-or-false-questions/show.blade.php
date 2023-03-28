<x-auth title="True Or False Question Details">
    <x-slot name="breadcrumb">
        <x-breadcrumb :paths="[
            'Academic Groups' => route('academic-groups.index'),
            $trueOrFalseQuestion->academicTopic->academicSubject->academicLevel->academicGroup->name => route('academic-groups.show', ['academic_group' => $trueOrFalseQuestion->academicTopic->academicSubject->academicLevel->academicGroup]),
            'Academic Levels' => route('academic-groups.academic-levels.index', ['academic_group' => $trueOrFalseQuestion->academicTopic->academicSubject->academicLevel->academicGroup]),
            $trueOrFalseQuestion->academicTopic->academicSubject->academicLevel->name => route('academic-levels.show', ['academic_level' => $trueOrFalseQuestion->academicTopic->academicSubject->academicLevel]),
            'Academic Subjects' => route('academic-levels.academic-subjects.index', ['academic_level' => $trueOrFalseQuestion->academicTopic->academicSubject->academicLevel]),
            $trueOrFalseQuestion->academicTopic->academicSubject->name => route('academic-subjects.show', ['academic_subject' => $trueOrFalseQuestion->academicTopic->academicSubject]),
            'Academic Topics' => route('academic-subjects.academic-topics.index', ['academic_subject' => $trueOrFalseQuestion->academicTopic->academicSubject]),
            $trueOrFalseQuestion->academicTopic->name => route('academic-topics.show', ['academic_topic' => $trueOrFalseQuestion->academicTopic]),
            'True Or False Questions' => route('academic-topics.true-or-false-questions.index', ['academic_topic' => $trueOrFalseQuestion->academicTopic]),
        ]" />
    </x-slot>

    <x-detail>
        <x-detail.data expand label="Question">{{ $trueOrFalseQuestion->question->html }}</x-detail.data>

        <x-detail.data label="Academic Topic">
            <x-anchor to="{{ route('academic-topics.show', ['academic_topic' => $trueOrFalseQuestion->academicTopic]) }}">
                {{ $trueOrFalseQuestion->academicTopic->name }}
            </x-anchor>
        </x-detail.data>

        @can('moderate')
        <x-slot name="action">
            <x-button.secondary type="buttton" x-data="{}" x-on:click="$store.deleteForm.show('Danger', 'Are you sure you want to delete {{ $trueOrFalseQuestion->question->summary }}', '{{ route('true-or-false-questions.destroy', ['true_or_false_question' => $trueOrFalseQuestion]) }}')">Delete True Or False Question</x-button.secondary>
            <x-link.primary :to="route('true-or-false-questions.edit', ['true_or_false_question' => $trueOrFalseQuestion])">Edit True Or False Question</x-link.primary>
        </x-slot>
        @endcan
    </x-detail>
</x-auth>