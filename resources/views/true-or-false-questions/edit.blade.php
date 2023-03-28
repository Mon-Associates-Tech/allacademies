<x-auth title="Edit True Or False Question">
    <x-slot name="breadcrumb">
        <x-breadcrumb :paths="[
            'Academic Groups' => route('academic-groups.index'),
            $trueOrFalseQuestion->academicTopic->academicSubject->academicLevel->academicGroup->name => route('academic-groups.show', ['academic_group' => $trueOrFalseQuestion->academicTopic->academicSubject->academicLevel->academicGroup]),
            'Academic Levels' => route('academic-groups.academic-levels.index', ['academic_group' => $trueOrFalseQuestion->academicTopic->academicSubject->academicLevel->academicGroup]),
            $trueOrFalseQuestion->academicTopic->academicSubject->academicLevel->name => route('academic-levels.show', ['academic_level' => $trueOrFalseQuestion->academicTopic->academicSubject->academicLevel]),
            'Academic Subjects' => route('academic-levels.academic-subjects.index', ['academic_level' => $trueOrFalseQuestion->academicTopic->academicSubject->academicLevel]),
            $trueOrFalseQuestion->academicTopic->academicSubject->name => route('academic-subjects.show', ['academic_subject' => $trueOrFalseQuestion->academicTopic->academicSubject]),
            'Academic Topics' => route('academic-subjects.academic-topics.index', ['academic_subject' => $trueOrFalseQuestion->academicTopic->academicSubject]),
            $trueOrFalseQuestion->academicTopic->name => route('academic-topics.show', ['academic_topic' => $trueOrFalseQuestion->academicTopic]),
            'True Or False Questions' => route('academic-topics.true-or-false-questions.index', ['academic_topic' => $trueOrFalseQuestion->academicTopic]),
        ]" />
    </x-slot>

    <form method="POST" action="{{ route('true-or-false-questions.update', ['true_or_false_question' => $trueOrFalseQuestion]) }}">
        @csrf
        @method('PATCH')
        <div class="grid grid-cols-3 gap-4">
            <div class="sm:col-span-2">
                <x-form.editor name="question" :value="$trueOrFalseQuestion->question" />
            </div>
            <div class="sm:col-span-2">
                <x-form.checkbox name="answer" description="Check if answer is true, Leave otherwise." :value="$trueOrFalseQuestion->answer"  />
            </div>
        </div>
        <div class="flex justify-end mt-3">
            <x-button.primary class="ml-2">Update Multiple Choice Question</x-button.primary>
        </div>
    </form>
</x-auth>