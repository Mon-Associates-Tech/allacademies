<x-layouts.app title="New Multiple Choice Question" :has-action="false">
    <x-slot name="breadcrumb">
        <x-breadcrumb :paths="[
            'Academic Groups' => route('academic-groups.index'),
            $academicTopic->academicSubject->academicLevel->academicGroup->name => route('academic-groups.show', ['academic_group' => $academicTopic->academicSubject->academicLevel->academicGroup]),
            'Academic Levels' => route('academic-levels.index', ['academic_group' => $academicTopic->academicSubject->academicLevel->academicGroup]),
            $academicTopic->academicSubject->academicLevel->name => route('academic-levels.show', ['academic_level' => $academicTopic->academicSubject->academicLevel, 'academic_group' => $academicTopic->academicSubject->academicLevel->academicGroup]),
            'Academic Subjects' => route('academic-subjects.index', ['academic_level' => $academicTopic->academicSubject->academicLevel, 'academic_group' => $academicTopic->academicSubject->academicLevel->academicGroup]),
            $academicTopic->academicSubject->name => route('academic-subjects.show', ['academic_subject' => $academicTopic->academicSubject, 'academic_level' => $academicTopic->academicSubject->academicLevel, 'academic_group' => $academicTopic->academicSubject->academicLevel->academicGroup]),
            'Academic Topics' => route('academic-topics.index', ['academic_subject' => $academicTopic->academicSubject, 'academic_level' => $academicTopic->academicSubject->academicLevel, 'academic_group' => $academicTopic->academicSubject->academicLevel->academicGroup]),
            $academicTopic->name => route('academic-topics.show', ['academic_topic' => $academicTopic, 'academic_subject' => $academicTopic->academicSubject, 'academic_level' => $academicTopic->academicSubject->academicLevel, 'academic_group' => $academicTopic->academicSubject->academicLevel->academicGroup]),
            'Multiple Choice Questions' => route('multiple-choice-questions.index', ['academic_topic' => $academicTopic, 'academic_subject' => $academicTopic->academicSubject, 'academic_level' => $academicTopic->academicSubject->academicLevel, 'academic_group' => $academicTopic->academicSubject->academicLevel->academicGroup]),
            'New Question' => null,
        ]"/>
    </x-slot>

    @include('questions.multiple-choice-questions.form')
</x-layouts.app>
