<x-auth title="New True Or False Question">
    <x-slot name="breadcrumb">
        <x-breadcrumb :paths="[
            'Academic Groups' => route('academic-groups.index'),
            $academicTopic->academicSubject->academicLevel->academicGroup->name => route('academic-groups.show', [
                'academic_group' => $academicTopic->academicSubject->academicLevel->academicGroup,
            ]),
            'Academic Levels' => route('academic-groups.academic-levels.index', [
                'academic_group' => $academicTopic->academicSubject->academicLevel->academicGroup,
            ]),
            $academicTopic->academicSubject->academicLevel->name => route('academic-levels.show', [
                'academic_level' => $academicTopic->academicSubject->academicLevel,
            ]),
            'Academic Subjects' => route('academic-levels.academic-subjects.index', [
                'academic_level' => $academicTopic->academicSubject->academicLevel,
            ]),
            $academicTopic->academicSubject->name => route('academic-subjects.show', [
                'academic_subject' => $academicTopic->academicSubject,
            ]),
            'Academic Topics' => route('academic-subjects.academic-topics.index', [
                'academic_subject' => $academicTopic->academicSubject,
            ]),
            $academicTopic->name => route('academic-topics.show', ['academic_topic' => $academicTopic]),
            'True or False Questions' => route('academic-topics.true-or-false-questions.index', [
                'academic_topic' => $academicTopic,
            ]),
        ]" />
    </x-slot>

    <div class="grid sm:grid-cols-3 gap-12">
        <div class="sm:col-span-2">
            <form method="POST"
                action="{{ route('academic-topics.true-or-false-questions.store', ['academic_topic' => $academicTopic]) }}">
                @csrf
                <div class="flex gap-x-3">
                    <div class="w-1/2">
                        <x-form.select name="difficulty_level" label="Difficulty Level" :options="[
                            'unspecified' => 'Unspecified',
                            'easy' => 'Easy',
                            'medium' => 'Medium',
                            'difficult' => 'Difficult',
                        ]" />
                    </div>
                    <div class="w-1/2">
                        <x-form.input name="score" type="number" value="1" />
                    </div>
                </div>
                <x-form.editor name="question" />
                <x-form.checkbox name="answer" description="Check if answer is true, Leave otherwise." />
                <div class="flex justify-end mt-3">
                    <x-button.primary class="ml-2">Create True Or False Question</x-button.primary>
                </div>
            </form>
        </div>
        <div class="sm:col-span-1 space-y-2">
            <x-plugins />
        </div>
    </div>
</x-auth>
