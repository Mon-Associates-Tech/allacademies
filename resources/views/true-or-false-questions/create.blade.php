<x-layouts.app title="New True Or False Question">
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
            'True or False Questions' => route('academic-topics.true-or-false-questions.index', ['academic_topic' => $academicTopic]),
        ]"/>
    </x-slot>

    <div class="bg-white p-4 grid grid-cols-5 rounded-md border-slate-300 border">
        <div class="max-w-4xl mx-auto px-4 py-4 sm:px-6 lg:px-8 col-span-3">
            <form method="POST"
                  action="{{ route('academic-topics.true-or-false-questions.store', ['academic_topic' => $academicTopic]) }}">
                @csrf
                <div class="grid sm:grid-cols-2 gap-x-3 border border-slate-300 p-3 rounded-md bg-gray-100">
                    <div class="sm:col-span-1">
                        <x-form.select name="difficulty_level" label="Difficulty Level" :options="[
                            'unspecified' => 'Unspecified',
                            'easy' => 'Easy',
                            'medium' => 'Medium',
                            'difficult' => 'Difficult',
                        ]"/>
                    </div>
                    <div class="sm:col-span-1">
                        <x-form.input name="score" type="number" value="1"/>
                    </div>

                    <div class="sm:col-span-2 my-3">
                        <x-form.input type="text" placeholder="Enter subtopic or leave blank" Label="Sub Topic"
                                      name="subtopic"/>
                    </div>

                    <div class="sm:col-span-2">
                        <div class="">
                            <x-form.editor class="rich-editor" name="question"/>
                        </div>
                        <div class="my-4">
                            <x-form.checkbox name="answer" description="Check if answer is true, Leave otherwise."/>

                        </div>
                        <div class="flex justify-end mt-3">
                            <x-button.primary class="ml-2">Create True Or False Question</x-button.primary>
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
