<x-layouts.app title="True Or False Questions">
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
        <x-link.primary class="text-nowrap" :to="route('academic-topics.true-or-false-questions.create', ['academic_topic' => $academicTopic])">Add Question</x-link.primary>
    </x-slot>
    @endcan

    @if ($trueOrFalseQuestions->count())
    <x-table>
        <x-slot name="head">
            <tr>
                <x-table.th class="font-black">Question</x-table.th>
                <x-table.th>Score</x-table.th>
                <x-table.th>Difficulty Level</x-table.th>
                <x-table.th><span class="sr-only">Actions</span>Actions</x-table.th>
            </tr>
        </x-slot>

        @foreach ($trueOrFalseQuestions as $trueOrFalseQuestion)
            <tr>
                <x-table.td bold>{{ $trueOrFalseQuestion->question->summary }}</x-table.td>
                <x-table.td>{{ $trueOrFalseQuestion->score }}</x-table.td>
                <x-table.td>{{ $trueOrFalseQuestion->difficulty_level }}</x-table.td>
                <x-table.td action class="text-nowrap">
                    <x-action name="view" :to="route('true-or-false-questions.show', ['true_or_false_question' => $trueOrFalseQuestion])" />
                    @can('moderate')
                    <x-action name="edit" :to="route('true-or-false-questions.edit', ['true_or_false_question' => $trueOrFalseQuestion])" />
                    <x-action class="text-red-500" name="delete" :to="route('true-or-false-questions.destroy', ['true_or_false_question' => $trueOrFalseQuestion])">
                        Are you sure you want to delete {{ $trueOrFalseQuestion->question->summary }}
                    </x-action>
                    @endcan
                </x-table.td>
            </tr>
        @endforeach
    </x-table>

    <div class="mt-3">
        {{ $trueOrFalseQuestions->links() }}
    </div>
    @else
    <x-blank />
    @endif
</x-layouts.app>
