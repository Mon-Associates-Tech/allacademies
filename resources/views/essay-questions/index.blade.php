<x-layouts.app title="Essay Questions">
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
        <x-link.primary :to="route('academic-topics.essay-questions.create', ['academic_topic' => $academicTopic])">New Essay Question</x-link.primary>
    </x-slot>
    @endcan

    @if ($essayQuestions->count())
    <x-table>
        <x-slot name="head">
            <tr>
                <x-table.th>Question</x-table.th>
                <x-table.th>Score</x-table.th>
                <x-table.th>Difficulty Level</x-table.th>
                <x-table.th><span class="sr-only">Actions</span></x-table.th>
            </tr>
        </x-slot>

        @foreach ($essayQuestions as $essayQuestion)
            <tr>
                <x-table.td class="truncate-four-lines" >{{ $essayQuestion->question->summary }}</x-table.td>
                <x-table.td>{{ $essayQuestion->score }}</x-table.td>
                <x-table.td>{{ $essayQuestion->difficulty_level }}</x-table.td>
                <x-table.td action class="text-nowrap">
                    <x-action name="view" :to="route('essay-questions.show', ['essay_question' => $essayQuestion])" />
                    @can('moderate')
                    <x-action name="edit" :to="route('essay-questions.edit', ['essay_question' => $essayQuestion])" />
                    <x-action name="delete" :to="route('essay-questions.destroy', ['essay_question' => $essayQuestion])">
                        Are you sure you want to delete {{ $essayQuestion->question->summary }}
                    </x-action>
                    @endcan
                </x-table.td>
            </tr>
        @endforeach
    </x-table>

    <div class="mt-3">
        {{ $essayQuestions->links() }}
    </div>
    @else
    <x-blank />
    @endif
</x-layouts.app>
