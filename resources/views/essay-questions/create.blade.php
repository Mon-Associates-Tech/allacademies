<x-layouts.app title="New Essay Question" :main-only="false">
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
            'Essay Questions' => route('academic-topics.essay-questions.index', ['academic_topic' => $academicTopic]),
        ]"/>
    </x-slot>

    <div class="bg-white p-4 grid grid-cols-5 rounded-md border-slate-300 border">
        <div class="max-w-4xl mt-1 pr-5 col-span-3">
            <form method="POST"
                  action="{{ route('academic-topics.essay-questions.store', ['academic_topic' => $academicTopic]) }}">
                @csrf
                <div class="grid sm:grid-cols-2 gap-x-3 border border-slate-200 p-3 rounded-md bg-gray-100">
                    <div class="sm:col-span-1">
                        <x-form.select name="difficulty_level" label="Difficulty Level" :options="[
                            'unspecified' => 'Unspecified',
                            'easy' => 'Easy',
                            'medium' => 'Medium',
                            'difficult' => 'Difficult',
                        ]"/>
                    </div>
                    <div class="sm:col-span-1">
                        <x-form.input name="score" type="number" value="15"/>
                    </div>

                    <div class="sm:col-span-2 my-3">
                        <x-form.input type="text" placeholder="Enter subtopic or leave blank" Label="Sub Topic"
                                      name="subtopic"/>
                    </div>

                    <div class="sm:col-span-2">
                        <x-form.editor class="rich-editor" full name="question"/>
                        <div class="my-3">
                            <x-form.editor class="rich-editor" full name="answer"/>
                        </div>

                        <div class="flex justify-end mt-4">
                            <x-button.primary class="ml-2">Create Essay Question</x-button.primary>
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
