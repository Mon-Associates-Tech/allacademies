<x-layouts.app title="Essay Question" :has-action="false">
    <x-slot name="breadcrumb">
        <x-breadcrumb :paths="[
            'Academic Groups' => route('academic-groups.index'),
            $essayQuestion->academicTopic->academicSubject->academicLevel->academicGroup->name => route('academic-groups.show', ['academic_group' => $essayQuestion->academicTopic->academicSubject->academicLevel->academicGroup]),
            'Academic Levels' => route('academic-groups.academic-levels.index', ['academic_group' => $essayQuestion->academicTopic->academicSubject->academicLevel->academicGroup]),
            $essayQuestion->academicTopic->academicSubject->academicLevel->name => route('academic-levels.show', ['academic_level' => $essayQuestion->academicTopic->academicSubject->academicLevel]),
            'Academic Subjects' => route('academic-levels.academic-subjects.index', ['academic_level' => $essayQuestion->academicTopic->academicSubject->academicLevel]),
            $essayQuestion->academicTopic->academicSubject->name => route('academic-subjects.show', ['academic_subject' => $essayQuestion->academicTopic->academicSubject]),
            'Academic Topics' => route('academic-subjects.academic-topics.index', ['academic_subject' => $essayQuestion->academicTopic->academicSubject]),
            $essayQuestion->academicTopic->name => route('academic-topics.show', ['academic_topic' => $essayQuestion->academicTopic]),
            'Essay Questions' => route('academic-topics.essay-questions.index', ['academic_topic' => $essayQuestion->academicTopic]),
        ]"/>
    </x-slot>

    <div class="bg-white p-4 grid grid-cols-5 rounded-md border-slate-300 border">
        <div class="max-w-4xl mt-1 pr-5 col-span-3">
            <form method="POST" action="{{ route('essay-questions.update', ['essay_question' => $essayQuestion]) }}">
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
                                       :value="$essayQuestion->difficulty_level"/>
                    </div>
                    <div class="sm:col-span-1">
                        <x-form.input name="score" type="number" :value="$essayQuestion->score"/>
                    </div>

                    @if(isset($essayQuestion->academicTopic->subtopic))
                        <div class="sm:col-span-2 my-3">
                            <x-form.input type="text" placeholder="Enter subtopic or leave blank"
                                          :value="$essayQuestion->subtopic->name" Label="Sub Topic" name="subtopic"/>
                        </div>
                    @endif


                    <div class="sm:col-span-2">
                        <div class="">
                            <x-form.rich-editor class="rich-editor" name="question" :value="$essayQuestion->question"/>
                        </div>

                        <div class="">
                            <x-form.rich-editor class="rich-editor" name="answer" :value="$essayQuestion->answer"/>
                        </div>

                        <div class="flex justify-end mt-3">
                            <x-button.primary class="ml-2">Update Essay Question</x-button.primary>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <div class="col-span-2 border mt-1 border-slate-200 p-3 rounded-md bg-gray-100">
            <x-plugins link="{{url()->current() . '/new'}}"/>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const editor = document.getElementById('question-editor');
            if (editor && window.marked) {
                const rawMarkdown = editor.value;
                const html = marked.parse(rawMarkdown);
                editor.value = html;
            }
        });
    </script>
</x-layouts.app>
