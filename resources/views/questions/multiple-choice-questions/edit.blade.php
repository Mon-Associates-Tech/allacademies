<x-layouts.app title="Edit Multiple Choice Question" :has-action="false">
    <x-slot name="breadcrumb">
        <x-breadcrumb :paths="[
            'Academic Groups' => route('academic-groups.index'),
            $multipleChoiceQuestion->academicTopic->academicSubject->academicLevel->academicGroup->name => route('academic-groups.show', ['academic_group' => $multipleChoiceQuestion->academicTopic->academicSubject->academicLevel->academicGroup]),
            'Academic Levels' => route('academic-levels.index', ['academic_group' => $multipleChoiceQuestion->academicTopic->academicSubject->academicLevel->academicGroup]),
            $multipleChoiceQuestion->academicTopic->academicSubject->academicLevel->name => route('academic-levels.show', ['academic_level' => $multipleChoiceQuestion->academicTopic->academicSubject->academicLevel, 'academic_group' => $multipleChoiceQuestion->academicTopic->academicSubject->academicLevel->academicGroup]),
            'Academic Subjects' => route('academic-subjects.index', ['academic_level' => $multipleChoiceQuestion->academicTopic->academicSubject->academicLevel, 'academic_group' => $multipleChoiceQuestion->academicTopic->academicSubject->academicLevel->academicGroup]),
            $multipleChoiceQuestion->academicTopic->academicSubject->name => route('academic-subjects.show', ['academic_subject' => $multipleChoiceQuestion->academicTopic->academicSubject, 'academic_level' => $multipleChoiceQuestion->academicTopic->academicSubject->academicLevel, 'academic_group' => $multipleChoiceQuestion->academicTopic->academicSubject->academicLevel->academicGroup]),
            'Academic Topics' => route('academic-topics.index', ['academic_subject' => $multipleChoiceQuestion->academicTopic->academicSubject, 'academic_level' => $multipleChoiceQuestion->academicTopic->academicSubject->academicLevel, 'academic_group' => $multipleChoiceQuestion->academicTopic->academicSubject->academicLevel->academicGroup]),
            $multipleChoiceQuestion->academicTopic->name => route('academic-topics.show', ['academic_topic' => $multipleChoiceQuestion->academicTopic, 'academic_subject' => $multipleChoiceQuestion->academicTopic->academicSubject, 'academic_level' => $multipleChoiceQuestion->academicTopic->academicSubject->academicLevel, 'academic_group' => $multipleChoiceQuestion->academicTopic->academicSubject->academicLevel->academicGroup]),
            'Multiple Choice Questions' => route('multiple-choice-questions.index', ['academic_topic' => $multipleChoiceQuestion->academicTopic, 'academic_subject' => $multipleChoiceQuestion->academicTopic->academicSubject, 'academic_level' => $multipleChoiceQuestion->academicTopic->academicSubject->academicLevel, 'academic_group' => $multipleChoiceQuestion->academicTopic->academicSubject->academicLevel->academicGroup]),
            'Edit Question' => null,
        ]"/>
    </x-slot>

    @include('questions.multiple-choice-questions.form', ['academicTopic' => $multipleChoiceQuestion->academicTopic])
</x-layouts.app>
