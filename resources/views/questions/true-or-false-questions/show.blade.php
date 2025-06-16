<x-layouts.app title="True Or False Question Details" :has-action="false">
                <x-slot name="breadcrumb">
                    <x-breadcrumb :paths="[
                        'Academic Groups' => route('academic-groups.index'),
                        $trueOrFalseQuestion->academicTopic->academicSubject->academicLevel->academicGroup->name => route('academic-groups.show', ['academic_group' => $trueOrFalseQuestion->academicTopic->academicSubject->academicLevel->academicGroup]),
                        'Academic Levels' => route('academic-levels.index', ['academic_group' => $trueOrFalseQuestion->academicTopic->academicSubject->academicLevel->academicGroup]),
                        $trueOrFalseQuestion->academicTopic->academicSubject->academicLevel->name => route('academic-levels.show', ['academic_level' => $trueOrFalseQuestion->academicTopic->academicSubject->academicLevel, 'academic_group' => getRouteParameter('academic_group')]),
                        'Academic Subjects' => route('academic-subjects.index', ['academic_level' => $trueOrFalseQuestion->academicTopic->academicSubject->academicLevel, 'academic_group' => getRouteParameter('academic_group')]),
                        $trueOrFalseQuestion->academicTopic->academicSubject->name => route('academic-subjects.show', ['academic_subject' => $trueOrFalseQuestion->academicTopic->academicSubject, 'academic_level' => getRouteParameter('academic_level'), 'academic_group' => getRouteParameter('academic_group')]),
                        'Academic Topics' => route('academic-topics.index', ['academic_subject' => $trueOrFalseQuestion->academicTopic->academicSubject, 'academic_level' => getRouteParameter('academic_level'), 'academic_group'=>getRouteParameter('academic_group')]),
                        $trueOrFalseQuestion->academicTopic->name => route('academic-topics.show', ['academic_topic' => $trueOrFalseQuestion->academicTopic, 'academic_subject' => getRouteParameter('academic_subject'), 'academic_level' => getRouteParameter('academic_level'), 'academic_group' => getRouteParameter('academic_group')]),
                        'True Or False Questions' => route('true-or-false-questions.index', ['academic_topic' => $trueOrFalseQuestion->academicTopic,  'academic_subject'=>getRouteParameter('academic_subject'), 'academic_level'=>getRouteParameter('academic_level'), 'academic_group'=>getRouteParameter('academic_group')]),
                        'Details' => null,
                    ]" />
                </x-slot>

                <div class="max-w-3xl mx-auto space-y-6">
                    <!-- Header Section -->
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                        <div class="flex items-center space-x-4">
                            <div class="p-3 bg-blue-100 rounded-full">
                                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div>
                                <h1 class="text-2xl font-bold text-gray-900">True/False Question</h1>
                                <p class="text-gray-600">Topic: {{ $trueOrFalseQuestion->academicTopic->name }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Question Content -->
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h2 class="text-lg font-semibold text-gray-900">Question Details</h2>
                        </div>
                        <div class="p-6">
                            <div class="prose max-w-none">
                                <span x-html="marked.parse(@js($trueOrFalseQuestion->question->summary))"></span>
                            </div>
                        </div>
                    </div>

                    <!-- Topic Information -->
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <h2 class="text-lg font-semibold text-gray-900">Associated Content</h2>
                        </div>
                        <div class="p-6">
                            <dl class="grid grid-cols-1 gap-4">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Academic Topic</dt>
                                    <dd class="mt-1">
                                        <x-anchor to="{{ route('academic-topics.show', ['academic_topic' => $trueOrFalseQuestion->academicTopic, 'academic_subject'=>getRouteParameter('academic_subject'), 'academic_level'=>getRouteParameter('academic_level'), 'academic_group'=>getRouteParameter('academic_group')]) }}" class="text-blue-600 hover:text-blue-800">
                                            {{ $trueOrFalseQuestion->academicTopic->name }}
                                        </x-anchor>
                                    </dd>
                                </div>

                                @if(isset($trueOrFalseQuestion->subtopic))
                                    <div>
                                        <dt class="text-sm font-medium text-gray-500">Sub Topic</dt>
                                        <dd class="mt-1 text-gray-900">{{ $trueOrFalseQuestion->subtopic->name }}</dd>
                                    </div>
                                @endif
                            </dl>
                        </div>
                    </div>

                    @can('moderate')
                        <!-- Action Buttons -->
                        <div class="flex items-center justify-end space-x-4">
                            <x-button.secondary
                                type="button"
                                x-data="{}"
                                x-on:click="$store.deleteForm.show(
                                    'Danger',
                                    'Are you sure you want to delete this question?',
                                    '{{ route('true-or-false-questions.destroy', [
                                        'true_or_false_question' => $trueOrFalseQuestion,
                                        'academic_subject' => getRouteParameter('academic_subject'),
                                        'academic_topic' => getRouteParameter('academic_topic'),
                                        'academic_level' => getRouteParameter('academic_level'),
                                        'academic_group' => getRouteParameter('academic_group')
                                    ]) }}'
                                )"
                            >
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                                Delete Question
                            </x-button.secondary>

                            <x-link.primary :to="route('true-or-false-questions.edit', [
                                'true_or_false_question' => $trueOrFalseQuestion,
                                'academic_subject' => getRouteParameter('academic_subject'),
                                'academic_topic' => getRouteParameter('academic_topic'),
                                'academic_level' => getRouteParameter('academic_level'),
                                'academic_group' => getRouteParameter('academic_group')
                            ])">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                                Edit Question
                            </x-link.primary>
                        </div>
                    @endcan
                </div>
            </x-layouts.app>
