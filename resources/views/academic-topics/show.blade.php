<x-auth title="{{$academicTopic->name}} Topic Details" action-link-text="Add Subtopic"
:action_link="route('academic-topics.subtopics.create', ['academic_topic' => $academicTopic])"
>
    <x-slot name="breadcrumb">
        <x-breadcrumb :paths="[
            'Academic Groups' => route('academic-groups.index'),
            $academicTopic->academicSubject->academicLevel->academicGroup->name => route('academic-groups.show', ['academic_group' => $academicTopic->academicSubject->academicLevel->academicGroup]),
            'Academic Levels' => route('academic-groups.academic-levels.index', ['academic_group' => $academicTopic->academicSubject->academicLevel->academicGroup]),
            $academicTopic->academicSubject->academicLevel->name => route('academic-levels.show', ['academic_level' => $academicTopic->academicSubject->academicLevel]),
            'Academic Subjects' => route('academic-levels.academic-subjects.index', ['academic_level' => $academicTopic->academicSubject->academicLevel]),
            $academicTopic->academicSubject->name => route('academic-subjects.show', ['academic_subject' => $academicTopic->academicSubject]),
            'Academic Topics' => route('academic-subjects.academic-topics.index', ['academic_subject' => $academicTopic->academicSubject]),
        ]"/>
    </x-slot>

    <x-detail>
        <x-detail.data label="Name">{{ $academicTopic->name }}</x-detail.data>

        <x-detail.data label="Academic Subject">
            <x-anchor
                to="{{ route('academic-subjects.show', ['academic_subject' => $academicTopic->academicSubject]) }}">
                {{ $academicTopic->academicSubject->name }}
            </x-anchor>
        </x-detail.data>

        <x-detail.data label="Multiple Choice Questions">
            <x-anchor
                to="{{ route('academic-topics.multiple-choice-questions.index', ['academic_topic' => $academicTopic]) }}">
                {{ $academicTopic->multiple_choice_questions_count }} multiple
                choice {{ Str::plural('question', $academicTopic->multiple_choice_questions_count) }}
            </x-anchor>
        </x-detail.data>

        <x-detail.data label="True Or False Questions">
            <x-anchor
                to="{{ route('academic-topics.true-or-false-questions.index', ['academic_topic' => $academicTopic]) }}">
                {{ $academicTopic->true_or_false_questions_count }} true or
                false {{ Str::plural('question', $academicTopic->true_or_false_questions_count) }}
            </x-anchor>
        </x-detail.data>

        <x-detail.data label="Essay Questions">
            <x-anchor to="{{ route('academic-topics.essay-questions.index', ['academic_topic' => $academicTopic]) }}">
                {{ $academicTopic->essay_questions_count }}
                essay {{ Str::plural('question', $academicTopic->essay_questions_count) }}
            </x-anchor>
        </x-detail.data>

        @can('administrate')
            <x-slot name="action">
                <x-button.secondary type="button" x-data="{}"
                                    x-on:click="$store.deleteForm.show('Danger', 'Are you sure you want to delete {{ $academicTopic->name }}', '{{ route('academic-topics.destroy', ['academic_topic' => $academicTopic]) }}')">
                    Delete Academic Topic
                </x-button.secondary>
                <x-link.primary :to="route('academic-topics.edit', ['academic_topic' => $academicTopic])">Edit Academic
                    Topic
                </x-link.primary>
            </x-slot>
        @endcan
    </x-detail>

</x-auth>



