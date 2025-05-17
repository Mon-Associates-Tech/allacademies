<x-auth title="Multiple Choice Questions">
    <x-slot name="breadcrumb">
        <x-breadcrumb :paths="[
            'Academic Groups' => route('academic-groups.index'),
            $academicTopic->academicSubject->academicLevel->academicGroup->name => route('academic-groups.show', ['academic_group' => $academicTopic->academicSubject->academicLevel->academicGroup]),
            'Academic Levels' => route('academic-groups.academic-levels.index', ['academic_group' => $academicTopic->academicSubject->academicLevel->academicGroup]),
            $academicTopic->academicSubject->academicLevel->name => route('academic-levels.show', ['academic_level' => $academicTopic->academicSubject->academicLevel]),
            'Academic Subjects' => route('academic-levels.academic-subjects.index', ['academic_level' => $academicTopic->academicSubject->academicLevel]),
            $academicTopic->academicSubject->name => route('academic-subjects.show', ['academic_subject' => $academicTopic->academicSubject]),
            'Academic Topics' => route('academic-subjects.academic-topics.index', ['academic_subject' => $academicTopic->academicSubject]),
            $academicTopic->name => route('academic-topics.show', ['academic_topic' => $academicTopic]),
        ]" />
    </x-slot>
    @can('moderate')
    <x-slot name="action">
        <x-link.primary :to="route('academic-topics.multiple-choice-questions.create', ['academic_topic' => $academicTopic])">New Multiple Choice Question</x-link.primary>
    </x-slot>
    @endcan

    @if ($multipleChoiceQuestions->count())
    <x-table>
        <x-slot name="head">
            <tr>
                <x-table.th>Question</x-table.th>
                <x-table.th>Score</x-table.th>
                <x-table.th>Difficulty Level</x-table.th>
                <x-table.th><span class="sr-only">Actions</span></x-table.th>
            </tr>
        </x-slot>

        @foreach ($multipleChoiceQuestions as $multipleChoiceQuestion)
            <tr>
                <x-table.td bold>{{ $multipleChoiceQuestion->question->summary }}</x-table.td>
                <x-table.td>{{ $multipleChoiceQuestion->score }}</x-table.td>
                <x-table.td>{{ $multipleChoiceQuestion->difficulty_level }}</x-table.td>
                <x-table.td action>
                    <x-action name="view" :to="route('multiple-choice-questions.show', ['multiple_choice_question' => $multipleChoiceQuestion])" />
                    @can('moderate')
                    <x-action name="edit" :to="route('multiple-choice-questions.edit', ['multiple_choice_question' => $multipleChoiceQuestion])" />
                    <x-action name="delete" :to="route('multiple-choice-questions.destroy', ['multiple_choice_question' => $multipleChoiceQuestion])">
                        Are you sure you want to delete {{ $multipleChoiceQuestion->question->summary }}
                    </x-action>
                    @endcan
                </x-table.td>
            </tr>
        @endforeach
    </x-table>

    <div class="mt-3">
        {{ $multipleChoiceQuestions->links() }}
    </div>
    @else
    <x-blank text="Add Questions" />
    @endif
</x-auth>
