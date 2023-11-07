<x-auth title="Edit Mutiple Choice Question">
    <x-slot name="breadcrumb">
        <x-breadcrumb :paths="[
            'Academic Groups' => route('academic-groups.index'),
            $multipleChoiceQuestion->academicTopic->academicSubject->academicLevel->academicGroup->name => route(
                'academic-groups.show',
                [
                    'academic_group' =>
                        $multipleChoiceQuestion->academicTopic->academicSubject->academicLevel->academicGroup,
                ],
            ),
            'Academic Levels' => route('academic-groups.academic-levels.index', [
                'academic_group' =>
                    $multipleChoiceQuestion->academicTopic->academicSubject->academicLevel->academicGroup,
            ]),
            $multipleChoiceQuestion->academicTopic->academicSubject->academicLevel->name => route(
                'academic-levels.show',
                ['academic_level' => $multipleChoiceQuestion->academicTopic->academicSubject->academicLevel],
            ),
            'Academic Subjects' => route('academic-levels.academic-subjects.index', [
                'academic_level' => $multipleChoiceQuestion->academicTopic->academicSubject->academicLevel,
            ]),
            $multipleChoiceQuestion->academicTopic->academicSubject->name => route('academic-subjects.show', [
                'academic_subject' => $multipleChoiceQuestion->academicTopic->academicSubject,
            ]),
            'Academic Topics' => route('academic-subjects.academic-topics.index', [
                'academic_subject' => $multipleChoiceQuestion->academicTopic->academicSubject,
            ]),
            $multipleChoiceQuestion->academicTopic->name => route('academic-topics.show', [
                'academic_topic' => $multipleChoiceQuestion->academicTopic,
            ]),
            'Multiple Choice Questions' => route('academic-topics.multiple-choice-questions.index', [
                'academic_topic' => $multipleChoiceQuestion->academicTopic,
            ]),
        ]" />
    </x-slot>

    <div class="grid sm:grid-cols-3 gap-12">
        <div class="sm:col-span-2">
            <form method="POST"
                action="{{ route('multiple-choice-questions.update', ['multiple_choice_question' => $multipleChoiceQuestion]) }}">
                @csrf
                @method('PATCH')
                <div class="grid grid-cols-2 gap-6">
                    <x-question-attributes :score="$multipleChoiceQuestion->score" :difficulty_level="$multipleChoiceQuestion->difficulty_level" />
                </div>
                <x-form.editor full name="question" :value="$multipleChoiceQuestion->question" />
                <x-form.editor full name="option_a" label="Option A" :value="$multipleChoiceQuestion->option_a" />
                <x-form.editor full name="option_b" label="Option B" :value="$multipleChoiceQuestion->option_b" />
                <x-form.editor full name="option_c" label="Option C" :value="$multipleChoiceQuestion->option_c" />
                <x-form.editor full name="option_d" label="Option D" :value="$multipleChoiceQuestion->option_d" />
                <x-form.editor full name="option_e" label="Option E" :value="$multipleChoiceQuestion->option_e" />
                <x-form.select full name="answer" :options="[
                    'a' => 'Option A',
                    'b' => 'Option B',
                    'c' => 'Option C',
                    'd' => 'Option D',
                    'e' => 'Option E',
                ]" :value="$multipleChoiceQuestion->answer" />
                <div class="flex justify-end mt-3">
                    <x-button.primary class="ml-2">Update Multiple Choice Question</x-button.primary>
                </div>
            </form>
        </div>
        <div class="sm:col-span-1 space-y-2">
            <x-plugins />
        </div>
    </div>
</x-auth>
