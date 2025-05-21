<x-auth title="Multiple Choice Question Details" :has-action="false">
    <x-slot name="breadcrumb">
        <x-breadcrumb :paths="[
            'Academic Groups' => route('academic-groups.index'),
            $multipleChoiceQuestion->academicTopic->academicSubject->academicLevel->academicGroup->name => route('academic-groups.show', ['academic_group' => $multipleChoiceQuestion->academicTopic->academicSubject->academicLevel->academicGroup]),
            'Academic Levels' => route('academic-groups.academic-levels.index', ['academic_group' => $multipleChoiceQuestion->academicTopic->academicSubject->academicLevel->academicGroup]),
            $multipleChoiceQuestion->academicTopic->academicSubject->academicLevel->name => route('academic-levels.show', ['academic_level' => $multipleChoiceQuestion->academicTopic->academicSubject->academicLevel]),
            'Academic Subjects' => route('academic-levels.academic-subjects.index', ['academic_level' => $multipleChoiceQuestion->academicTopic->academicSubject->academicLevel]),
            $multipleChoiceQuestion->academicTopic->academicSubject->name => route('academic-subjects.show', ['academic_subject' => $multipleChoiceQuestion->academicTopic->academicSubject]),
            'Academic Topics' => route('academic-subjects.academic-topics.index', ['academic_subject' => $multipleChoiceQuestion->academicTopic->academicSubject]),
            $multipleChoiceQuestion->academicTopic->name => route('academic-topics.show', ['academic_topic' => $multipleChoiceQuestion->academicTopic]),
            'Multiple Choice Questions' => route('academic-topics.multiple-choice-questions.index', ['academic_topic' => $multipleChoiceQuestion->academicTopic]),
        ]" />
    </x-slot>

    <x-detail>
        <x-detail.data expand label="Question">{{ $multipleChoiceQuestion->question->html }}</x-detail.data>

        <x-detail.data label="Option A">{{ $multipleChoiceQuestion->option_a->html }}</x-detail.data>
        <x-detail.data label="Option B">{{ $multipleChoiceQuestion->option_b->html }}</x-detail.data>
        <x-detail.data label="Option C">{{ $multipleChoiceQuestion->option_c->html }}</x-detail.data>
        <x-detail.data label="Option D">{{ $multipleChoiceQuestion->option_d->html }}</x-detail.data>
        <x-detail.data label="Option E">{{ $multipleChoiceQuestion->option_e->html }}</x-detail.data>

        <x-detail.data label="Academic Topic">
            <x-anchor to="{{ route('academic-topics.show', ['academic_topic' => $multipleChoiceQuestion->academicTopic]) }}">
                {{ $multipleChoiceQuestion->academicTopic->name }}
            </x-anchor>
        </x-detail.data>

        @if(isset($multipleChoiceQuestion->subtopic))
            <x-detail.data label="Sub Topic" disabled>
                <x-anchor to="#">
                    {{ $multipleChoiceQuestion?->subtopic?->name }}
                </x-anchor>
            </x-detail.data>
        @endif

        @can('moderate')
        <x-slot name="action">
            <x-button.secondary type="button" x-data="{}" x-on:click="$store.deleteForm.show('Danger', 'Are you sure you want to delete {{ $multipleChoiceQuestion->question->summary }}', '{{ route('multiple-choice-questions.destroy', ['multiple_choice_question' => $multipleChoiceQuestion]) }}')">Delete Multiple Choice Question</x-button.secondary>
            <x-link.primary :to="route('multiple-choice-questions.edit', ['multiple_choice_question' => $multipleChoiceQuestion])">Edit Multiple Choice Question</x-link.primary>
        </x-slot>
        @endcan
    </x-detail>
</x-auth>
