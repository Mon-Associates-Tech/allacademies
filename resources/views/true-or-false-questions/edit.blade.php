<x-layouts.app title="Edit True Or False Question">
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

    <div class="bg-white p-4 grid grid-cols-5 rounded-md border-slate-300 border">
        <div class="max-w-4xl mx-auto px-4 py-4 sm:px-6 lg:px-8 col-span-3">
            <form method="POST"
                  action="{{ route('true-or-false-questions.update', ['true_or_false_question' => $trueOrFalseQuestion]) }}">
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
                                       :value="$trueOrFalseQuestion->difficulty_level" />
                    </div>
                    <div class="sm:col-span-1">
                        <x-form.input name="score" type="number" :value="$trueOrFalseQuestion->score" />
                    </div>

                    <div class="sm:col-span-1 my-3">
                        <x-form.input type="text" placeholder="Enter subtopic or leave blank" :value="$trueOrFalseQuestion?->subtopic?->name" Label="Sub Topic" name="subtopic" />
                    </div>

                    <div class="sm:col-span-2">
                        <x-form.editor name="question" :value="$trueOrFalseQuestion->question" />
                        <x-form.checkbox name="answer" description="Check if answer is true, Leave otherwise."
                                         :value="$trueOrFalseQuestion->answer" />
                        <div class="flex justify-end mt-3">
                            <x-button.primary class="ml-2">Update Multiple Choice Question</x-button.primary>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <div class="mt-4 col-span-2 border border-slate-300 p-3 rounded-md bg-gray-100">
            <x-plugins link="{{url()->current() . '/new'}}"/>
        </div>
    </div>

    <div class="bg-white p-4 grid grid-cols-5 rounded-md border-slate-300 border">
        <div class="max-w-4xl mt-1 pr-5 col-span-3">
            <form method="POST"
                  action="{{ route('academic-topics.multiple-choice-questions.store', ['academic_topic' => $academicTopic]) }}">
                @csrf
                <div class="grid sm:grid-cols-2 gap-x-3 border border-slate-200 p-3 rounded-md bg-gray-100">
                    <div class="sm:col-span-1">
                        <x-form.select class="" name="difficulty_level" label="Difficulty Level" :options="[
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
                        <div class="py-2">
                            <x-form.editor class="rich-editor" full name="question" />
                        </div>
                        <div class="py-2"><x-form.editor class="rich-editor" full name="option_a" label="Option A" /></div>
                        <div class="py-2"><x-form.editor class="rich-editor" full name="option_b" label="Option B" /></div>
                        <div class="py-2"> <x-form.editor class="rich-editor" full name="option_c" label="Option C" /></div>
                        <div class="py-2"><x-form.editor class="rich-editor" full name="option_d" label="Option D" /></div>
                        <div class="py-2"><x-form.editor class="rich-editor" full name="option_e" label="Option E" /></div>
                        <div class="py-2">
                            <x-form.select class="mt-2" full name="answer" :options="[
                            'a' => 'Option A',
                            'b' => 'Option B',
                            'c' => 'Option C',
                            'd' => 'Option D',
                            'e' => 'Option E',
                        ]" />
                        </div>

                        <div class="pt-2">
                            <x-form.input type="text" placeholder="Enter subtopic or leave blank" Label="Sub Topic" name="subtopic" ></x-form.input>
                        </div>

                        <div class="flex justify-end mt-3">
                            <x-button.primary class="ml-2">Create Multiple Choice Question</x-button.primary>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <div class="col-span-2 border mt-1 border-slate-200 p-3 rounded-md bg-gray-100">
            <x-plugins link="{{url()->current() . '/new'}}"/>
        </div>
    </div>

</x-layouts.app>
