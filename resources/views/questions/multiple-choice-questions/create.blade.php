<x-layouts.app title="New Multiple Choice Question" :has-action="false">
                    <x-slot name="breadcrumb">
                        <x-breadcrumb :paths="[
                            'Academic Groups' => route('academic-groups.index'),
                            $academicTopic->academicSubject->academicLevel->academicGroup->name => route('academic-groups.show', ['academic_group' => $academicTopic->academicSubject->academicLevel->academicGroup]),
                            'Academic Levels' => route('academic-levels.index', ['academic_group' => $academicTopic->academicSubject->academicLevel->academicGroup]),
                            $academicTopic->academicSubject->academicLevel->name => route('academic-levels.show', ['academic_level' => $academicTopic->academicSubject->academicLevel, 'academic_group' => $academicTopic->academicSubject->academicLevel->academicGroup]),
                            'Academic Subjects' => route('academic-subjects.index', ['academic_level' => $academicTopic->academicSubject->academicLevel, 'academic_group' => $academicTopic->academicSubject->academicLevel->academicGroup]),
                            $academicTopic->academicSubject->name => route('academic-subjects.show', ['academic_subject' => $academicTopic->academicSubject, 'academic_level' => $academicTopic->academicSubject->academicLevel, 'academic_group' => $academicTopic->academicSubject->academicLevel->academicGroup]),
                            'Academic Topics' => route('academic-topics.index', ['academic_subject' => $academicTopic->academicSubject, 'academic_level' => $academicTopic->academicSubject->academicLevel, 'academic_group' => $academicTopic->academicSubject->academicLevel->academicGroup]),
                            $academicTopic->name => route('academic-topics.show', ['academic_topic' => $academicTopic, 'academic_subject' => $academicTopic->academicSubject, 'academic_level' => $academicTopic->academicSubject->academicLevel, 'academic_group' => $academicTopic->academicSubject->academicLevel->academicGroup]),
                            'Multiple Choice Questions' => route('multiple-choice-questions.index', ['academic_topic' => $academicTopic, 'academic_subject' => $academicTopic->academicSubject, 'academic_level' => $academicTopic->academicSubject->academicLevel, 'academic_group' => $academicTopic->academicSubject->academicLevel->academicGroup]),
                        ]" />
                    </x-slot>

                    <div class="bg-white p-4 grid grid-cols-5 rounded-md border-slate-300 border">
                        <!-- Question Form -->
                        <div class="max-w-4xl mt-1 pr-5 col-span-3">
                            <form method="POST" action="{{ route('multiple-choice-questions.store', [
                                'academic_topic' => $academicTopic,
                                'academic_subject' => getRouteParameter('academic_subject'),
                                'academic_level' => getRouteParameter('academic_level'),
                                'academic_group' => getRouteParameter('academic_group')
                            ]) }}">
                                @csrf

                                <div class="grid sm:grid-cols-2 gap-x-3 border border-slate-200 p-3 rounded-md bg-gray-100">
                                    <!-- Question Settings -->
                                    <div class="sm:col-span-1">
                                        <x-form.select name="difficulty_level" label="Difficulty Level" :options="[
                                            'unspecified' => 'None',
                                            'easy' => 'Easy',
                                            'medium' => 'Medium',
                                            'difficult' => 'Difficult',
                                        ]" />
                                    </div>

                                    <div class="sm:col-span-1">
                                        <x-form.input name="score" type="number" label="Score (Marks)" value="1" />
                                    </div>

                                    <!-- Question Content -->
                                    <div class="sm:col-span-2">
                                        <div class="space-y-2">
                                            <div class="pt-2">
                                                <x-form.input type="text" name="subtopic" label="Sub Topic"
                                                    placeholder="Enter subtopic or leave blank" />
                                            </div>

                                            <div class="py-2">
                                                <x-form.rich-editor class="rich-editor" full name="question" />
                                            </div>

                                            <!-- Answer Options -->
                                            @foreach(['a', 'b', 'c', 'd', 'e'] as $option)
                                                <div class="py-2">
                                                    <x-form.rich-editor class="rich-editor" full
                                                        name="option_{{$option}}"
                                                        label="Option {{strtoupper($option)}}" />
                                                </div>
                                            @endforeach

                                            <!-- Answer Selection -->
                                            <div class="py-2">
                                                <x-form.select class="mt-2" full name="answer" :options="[
                                                    'a' => 'Option A',
                                                    'b' => 'Option B',
                                                    'c' => 'Option C',
                                                    'd' => 'Option D',
                                                    'e' => 'Option E',
                                                ]" />
                                            </div>

                                            <!-- Submit Button -->
                                            <div class="flex justify-end mt-3">
                                                <x-button.primary class="ml-2">Create Multiple Choice Question</x-button.primary>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </form>
                        </div>

                        <!-- Plugins Section -->
                        <div class="col-span-2 border mt-1 border-slate-200 p-3 rounded-md bg-gray-100">
                            <x-plugins link="{{ url()->current() . '/new' }}"/>
                        </div>
                    </div>
                </x-layouts.app>
