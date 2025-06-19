<x-layouts.app
    title="{{ $academic_subtopic->name }}"
    :has-action="true"
    :action-link-text="'Edit Subtopic'"
    :action_link="route('subtopics.edit', ['academic_topic' => $academicTopic, 'subtopic' => $academic_subtopic, 'academic_subject' => getRouteParameter('academic_subject'), 'academic_level' => getRouteParameter('academic_level'), 'academic_group' => getRouteParameter('academic_group')])"
>
    <x-slot name="breadcrumb">
        <x-breadcrumb :paths="[
            'Academic Groups' => route('academic-groups.index'),
            $academicTopic->academicSubject->academicLevel->academicGroup->name => route('academic-groups.show', ['academic_group' => $academicTopic->academicSubject->academicLevel->academicGroup]),
            'Academic Levels' => route('academic-levels.index', ['academic_group' => $academicTopic->academicSubject->academicLevel->academicGroup]),
            $academicTopic->academicSubject->academicLevel->name => route('academic-levels.show', ['academic_level' => $academicTopic->academicSubject->academicLevel, 'academic_group' => getRouteParameter('academic_group'), ]),
            'Academic Subjects' => route('academic-subjects.index', ['academic_level' => $academicTopic->academicSubject->academicLevel, 'academic_group' => getRouteParameter('academic_group')]),
            $academicTopic->academicSubject->name => route('academic-subjects.show', ['academic_subject' => getRouteParameter('academic_subject'), 'academic_level' => getRouteParameter('academic_level'), 'academic_group' => getRouteParameter('academic_group')]),
            'Academic Topics' => route('academic-topics.index', ['academic_subject' => getRouteParameter('academic_subject'), 'academic_level' => getRouteParameter('academic_level'), 'academic_group' => getRouteParameter('academic_group')]),
            $academicTopic->name => route('academic-topics.show', ['academic_topic' => $academicTopic, 'academic_subject' => getRouteParameter('academic_subject'), 'academic_level' => getRouteParameter('academic_level'), 'academic_group' => getRouteParameter('academic_group')]),
            'Subtopics' => route('subtopics.index', ['academic_topic' => $academicTopic, 'academic_subject' => getRouteParameter('academic_subject'), 'academic_level' => getRouteParameter('academic_level'), 'academic_group' => getRouteParameter('academic_group')]),
            $academic_subtopic->name => null,
        ]"/>
    </x-slot>

    <div class="space-y-6">
        <!-- Basic Information Card -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">Basic Information</h2>
            </div>
            <div class="p-6 space-y-4">
                <x-detail.data label="Name" class="text-lg font-medium">
                    {{ $academic_subtopic->name }}
                </x-detail.data>

                <x-detail.data label="Parent Topic">
                    <x-anchor to="{{ route('academic-topics.show', ['academic_topic' => getRouteParameter('academic_topic'), 'academic_subject' => getRouteParameter('academic_subject'),  'academic_level' => $academicTopic->academicSubject->academicLevel, 'academic_group' => getRouteParameter('academic_group'), ]) }}" class="text-blue-600 hover:text-blue-800">
                        {{ $academicTopic->name }}
                    </x-anchor>
                </x-detail.data>

                <x-detail.data label="Subject">
                    <x-anchor to="{{ route('academic-subjects.show', ['academic_topic' => getRouteParameter('academic_topic'), 'academic_subject' => getRouteParameter('academic_subject'),  'academic_level' => $academicTopic->academicSubject->academicLevel, 'academic_group' => getRouteParameter('academic_group'), ]) }}" class="text-blue-600 hover:text-blue-800">
                        {{ $academicTopic->academicSubject->name }}
                    </x-anchor>
                </x-detail.data>
            </div>
        </div>

        <!-- Questions Overview Card -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">Questions Overview</h2>
            </div>
            <div class="p-6 grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Multiple Choice Questions -->
                <div class="bg-blue-50 rounded-lg p-4">
                    <h3 class="font-medium text-blue-900">Multiple Choice</h3>
                    <div class="mt-2 flex items-center justify-between">
                        <span class="text-2xl font-bold text-blue-700">
                            {{ $academicTopic->multiple_choice_questions_count }}
                        </span>
                        <x-anchor to="{{ route('multiple-choice-questions.index', ['academic_topic' => getRouteParameter('academic_topic'), 'academic_subject' => getRouteParameter('academic_subject'),  'academic_level' => $academicTopic->academicSubject->academicLevel, 'academic_group' => getRouteParameter('academic_group'), ]) }}" class="text-blue-600 hover:text-blue-800 text-sm">
                            View All →
                        </x-anchor>
                    </div>
                </div>

                <!-- True/False Questions -->
                <div class="bg-green-50 rounded-lg p-4">
                    <h3 class="font-medium text-green-900">True or False</h3>
                    <div class="mt-2 flex items-center justify-between">
                        <span class="text-2xl font-bold text-green-700">
                            {{ $academicTopic->true_or_false_questions_count }}
                        </span>
                        <x-anchor to="{{ route('true-or-false-questions.index', ['academic_topic' => getRouteParameter('academic_topic'), 'academic_subject' => getRouteParameter('academic_subject'),  'academic_level' => $academicTopic->academicSubject->academicLevel, 'academic_group' => getRouteParameter('academic_group'), ]) }}" class="text-green-600 hover:text-green-800 text-sm">
                            View All →
                        </x-anchor>
                    </div>
                </div>

                <!-- Essay Questions -->
                <div class="bg-purple-50 rounded-lg p-4">
                    <h3 class="font-medium text-purple-900">Essay</h3>
                    <div class="mt-2 flex items-center justify-between">
                        <span class="text-2xl font-bold text-purple-700">
                            {{ $academicTopic->essay_questions_count }}
                        </span>
                        <x-anchor to="{{ route('essay-questions.index', ['academic_topic' => getRouteParameter('academic_topic'), 'academic_subject' => getRouteParameter('academic_subject'),  'academic_level' => $academicTopic->academicSubject->academicLevel, 'academic_group' => getRouteParameter('academic_group'), ]) }}" class="text-purple-600 hover:text-purple-800 text-sm">
                            View All →
                        </x-anchor>
                    </div>
                </div>
            </div>
        </div>

        <!-- Actions -->
        @can('administrate')
            <div class="flex justify-end space-x-4">
                <x-button.secondary
                    x-data="{}"
                    x-on:click="$store.deleteForm.show(
                        'Delete Subtopic',
                        'Are you sure you want to delete {{ $academic_subtopic->name }}?',
                        '{{ route('subtopics.destroy', ['subtopic' => $academic_subtopic, 'academic_topic' => getRouteParameter('academic_topic'), 'academic_subject' => getRouteParameter('academic_subject'),  'academic_level' => $academicTopic->academicSubject->academicLevel, 'academic_group' => getRouteParameter('academic_group'), ]) }}'
                    )"
                >
                    <span class="flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                        </svg>
                        Delete Subtopic
                    </span>
                </x-button.secondary>
            </div>
        @endcan
    </div>
</x-layouts.app>
