<x-layouts.app title="Edit Multiple Choice Question">
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
            <div class="bg-white p-4 rounded-md border-slate-300 border">
                <form method="POST"
                      action="{{ route('multiple-choice-questions.update', ['multiple_choice_question' => $multipleChoiceQuestion]) }}">
                    @csrf
                    @method('PATCH')
                    <div class="grid sm:grid-cols-2 gap-x-3">
                        <div class="sm:col-span-1">
                            <x-form.select name="difficulty_level" label="Difficulty Level" :options="[
                            'unspecified' => 'Unspecified',
                            'easy' => 'Easy',
                            'medium' => 'Medium',
                            'difficult' => 'Difficult',
                        ]"
                                           :value="$multipleChoiceQuestion->difficulty_level" />
                        </div>
                        <div class="sm:col-span-1">
                            <x-form.input name="score" type="number" :value="$multipleChoiceQuestion->score" />
                        </div>
                        <div class="sm:col-span-2">
                            <div class="py-3"> <x-form.editor full name="question" :value="$multipleChoiceQuestion->question" /></div>
                            <div class="py-3"><x-form.editor full name="option_a" label="Option A" :value="$multipleChoiceQuestion->option_a" /></div>
                            <div class="py-3"><x-form.editor full name="option_b" label="Option B" :value="$multipleChoiceQuestion->option_b" /></div>
                            <div class="py-3"><x-form.editor full name="option_c" label="Option C" :value="$multipleChoiceQuestion->option_c" /></div>
                            <div class="py-3"><x-form.editor full name="option_d" label="Option D" :value="$multipleChoiceQuestion->option_d" /></div>
                            <div class="py-3"><x-form.editor full name="option_e" label="Option E" :value="$multipleChoiceQuestion->option_e" /></div>
                            <div class="py-3">
                                <x-form.select full name="answer" :options="[
                            'a' => 'Option A',
                            'b' => 'Option B',
                            'c' => 'Option C',
                            'd' => 'Option D',
                            'e' => 'Option E',
                        ]" :value="$multipleChoiceQuestion->answer" />
                            </div>

                            <div class="mt-3">
                                <x-form.input type="text" Label="Sub Topic" :value="$multipleChoiceQuestion?->subtopic?->name" name="subtopic" ></x-form.input>
                                <hr class="my-6">
                            </div>
                            <div class="flex justify-end mt-3">
                                <x-button.primary class="ml-2">Update Multiple Choice Question</x-button.primary>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

        <x-slot name="right">
            <div class="mt-5">
                <x-plugins />
            </div>
        </x-slot>
</x-layouts.app>
