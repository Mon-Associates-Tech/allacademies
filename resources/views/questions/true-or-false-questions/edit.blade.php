<x-layouts.app title="Edit True Or False Question" :has-action="false">
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

                    <div class="sm:col-span-2 my-3">
                        <x-form.input type="text" placeholder="Enter subtopic or leave blank" :value="$trueOrFalseQuestion?->subtopic?->name" Label="Sub Topic" name="subtopic" />
                    </div>

                    <div class="sm:col-span-2">
                        <div class="mb-3">
                            <x-form.rich-editor name="question" :value="$trueOrFalseQuestion->question" />
                        </div>

                        <x-form.checkbox name="answer" description="Check if answer is true, Leave otherwise."
                                         :value="$trueOrFalseQuestion->answer" />
                        <div class="flex justify-end mt-3">
                            <x-button.primary class="ml-2">Update True or False Question</x-button.primary>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <div class="mt-4 col-span-2 border border-slate-300 p-3 rounded-md bg-gray-100">
            <x-plugins link="{{url()->current() . '/new'}}"/>
        </div>
    </div>

</x-layouts.app>
