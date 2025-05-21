<x-auth title="New Multiple Choice Question" :has-action="false">
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
            'Multiple Choice Questions' => route('academic-topics.multiple-choice-questions.index', ['academic_topic' => $academicTopic]),
        ]" />
    </x-slot>

            <div class="bg-white p-4 rounded-md border-slate-300 border">
                <form method="POST"
                      action="{{ route('academic-topics.multiple-choice-questions.store', ['academic_topic' => $academicTopic]) }}">
                    @csrf
                    <div class="grid sm:grid-cols-2 gap-x-3">
                        <div class="sm:col-span-1">
                            <x-form.select class="bg-gray-100" name="difficulty_level" label="Difficulty Level" :options="[
                            'unspecified' => 'None',
                            'easy' => 'Easy',
                            'medium' => 'Medium',
                            'difficult' => 'Difficult',
                        ]" />
                        </div>
                        <div class="sm:col-span-1 ">
                            <x-form.input name="score" type="number" label="Score (Marks)" value="1" />
                        </div>
                        <div class="sm:col-span-2">
                            <div class="py-3">
                                <x-form.editor class="rich-editor" full name="question" />
                            </div>
                            <div class="py-3"><x-form.editor class="rich-editor" full name="option_a" label="Option A" /></div>
                            <div class="py-3"><x-form.editor class="rich-editor" full name="option_b" label="Option B" /></div>
                            <div class="py-3"> <x-form.editor class="rich-editor" full name="option_c" label="Option C" /></div>
                            <div class="py-3"><x-form.editor class="rich-editor" full name="option_d" label="Option D" /></div>
                            <div class="py-3"><x-form.editor class="rich-editor" full name="option_e" label="Option E" /></div>
                            <div class="py-3">
                                <x-form.select class="mt-3" full name="answer" :options="[
                            'a' => 'Option A',
                            'b' => 'Option B',
                            'c' => 'Option C',
                            'd' => 'Option D',
                            'e' => 'Option E',
                        ]" />
                            </div>

                            <div class="pt-3">
                                <x-form.input type="text" placeholder="Enter subtopic or leave blank" Label="Sub Topic" name="subtopic" ></x-form.input>
                                <hr class="my-6">
                            </div>

                            <div class="flex justify-end mt-3">
                                <x-button.primary class="ml-2">Create Multiple Choice Question</x-button.primary>
                            </div>
                        </div>
                    </div>
                </form>
            </div>

        <x-slot name="right" >
            <x-plugins />
        </x-slot>

</x-auth>
