<x-auth title="Essay Question">
    <x-slot name="breadcrumb">
        <x-breadcrumb :paths="[
            'Academic Groups' => route('academic-groups.index'),
            $essayQuestion->academicTopic->academicSubject->academicLevel->academicGroup->name => route(
                'academic-groups.show',
                ['academic_group' => $essayQuestion->academicTopic->academicSubject->academicLevel->academicGroup],
            ),
            'Academic Levels' => route('academic-groups.academic-levels.index', [
                'academic_group' => $essayQuestion->academicTopic->academicSubject->academicLevel->academicGroup,
            ]),
            $essayQuestion->academicTopic->academicSubject->academicLevel->name => route('academic-levels.show', [
                'academic_level' => $essayQuestion->academicTopic->academicSubject->academicLevel,
            ]),
            'Academic Subjects' => route('academic-levels.academic-subjects.index', [
                'academic_level' => $essayQuestion->academicTopic->academicSubject->academicLevel,
            ]),
            $essayQuestion->academicTopic->academicSubject->name => route('academic-subjects.show', [
                'academic_subject' => $essayQuestion->academicTopic->academicSubject,
            ]),
            'Academic Topics' => route('academic-subjects.academic-topics.index', [
                'academic_subject' => $essayQuestion->academicTopic->academicSubject,
            ]),
            $essayQuestion->academicTopic->name => route('academic-topics.show', [
                'academic_topic' => $essayQuestion->academicTopic,
            ]),
            'Essay Questions' => route('academic-topics.essay-questions.index', [
                'academic_topic' => $essayQuestion->academicTopic,
            ]),
        ]" />
    </x-slot>

    <div class="grid sm:grid-cols-3 gap-12">
        <div class="sm:col-span-2">
            <form method="POST" action="{{ route('essay-questions.update', ['essay_question' => $essayQuestion]) }}">
                @csrf
                @method('PATCH')
                <div class="flex gap-x-3">
                    <div class="w-1/2">
                        <x-form.select name="difficulty_level" label="Difficulty Level" :options="[
                            'unspecified' => 'Unspecified',
                            'easy' => 'Easy',
                            'medium' => 'Medium',
                            'difficult' => 'Difficult',
                        ]"
                            :value="$essayQuestion->difficulty_level" />
                    </div>
                    <div class="w-1/2">
                        <x-form.input name="score" type="number" :value="$essayQuestion->score" />
                    </div>
                </div>
                <x-form.editor name="question" :value="$essayQuestion->question" />
                <x-form.editor name="answer" :value="$essayQuestion->answer" />
                <div class="flex justify-end mt-3">
                    <x-button.primary class="ml-2">Update Essay Question</x-button.primary>
                </div>
            </form>
        </div>
        <div class="sm:col-span-1 space-y-2">
            <x-plugins />
        </div>
    </div>
</x-auth>
