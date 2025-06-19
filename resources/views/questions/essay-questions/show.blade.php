<x-layouts.app title="Essay Question Details" :has-action="false">
    <x-slot name="breadcrumb">
        <x-breadcrumb :paths="[
                        'Academic Groups' => route('academic-groups.index'),
                        $essayQuestion->academicTopic->academicSubject->academicLevel->academicGroup->name => route('academic-groups.show', [
                            'academic_group' => $essayQuestion->academicTopic->academicSubject->academicLevel->academicGroup
                        ]),
                        'Academic Levels' => route('academic-levels.index', [
                            'academic_group' => $essayQuestion->academicTopic->academicSubject->academicLevel->academicGroup
                        ]),
                        $essayQuestion->academicTopic->academicSubject->academicLevel->name => route('academic-levels.show', [
                            'academic_level' => $essayQuestion->academicTopic->academicSubject->academicLevel,
                            'academic_group' => getRouteParameter('academic_group')
                        ]),
                        'Academic Subjects' => route('academic-subjects.index', [
                            'academic_level' => $essayQuestion->academicTopic->academicSubject->academicLevel,
                            'academic_group' => getRouteParameter('academic_group')
                        ]),
                        $essayQuestion->academicTopic->academicSubject->name => route('academic-subjects.show', [
                            'academic_subject' => $essayQuestion->academicTopic->academicSubject,
                            'academic_level' => getRouteParameter('academic_level'),
                            'academic_group' => getRouteParameter('academic_group')
                        ]),
                        'Academic Topics' => route('academic-topics.index', [
                            'academic_subject' => $essayQuestion->academicTopic->academicSubject,
                            'academic_level' => getRouteParameter('academic_level'),
                            'academic_group' => getRouteParameter('academic_group')
                        ]),
                        $essayQuestion->academicTopic->name => route('academic-topics.show', [
                            'academic_topic' => $essayQuestion->academicTopic,
                            'academic_subject' => getRouteParameter('academic_subject'),
                            'academic_level' => getRouteParameter('academic_level'),
                            'academic_group' => getRouteParameter('academic_group')
                        ]),
                        'Essay Questions' => route('essay-questions.index', [
                            'academic_topic' => $essayQuestion->academicTopic,
                            'academic_subject' => getRouteParameter('academic_subject'),
                            'academic_level' => getRouteParameter('academic_level'),
                            'academic_group' => getRouteParameter('academic_group')
                        ]),
                        'Question Details' => null,
                    ]"/>
    </x-slot>

    <div class="space-y-6">
        <!-- Header Section -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-start justify-between">
                <div class="space-y-1">
                    <h1 class="text-2xl font-bold text-gray-900">Essay Question Details</h1>
                    <p class="text-sm text-gray-600">
                        From topic: {{ $essayQuestion->academicTopic->name }}
                    </p>
                </div>
                @can('moderate')
                    <div class="flex space-x-3">
                        <x-button.secondary
                            type="button"
                            x-data="{}"
                            x-on:click="$store.deleteForm.show(
                                            'Danger',
                                            'Are you sure you want to delete this essay question?',
                                            '{{ route('essay-questions.destroy', [
                                                'essay_question' => $essayQuestion,
                                                'academic_subject' => getRouteParameter('academic_subject'),
                                                'academic_topic' => getRouteParameter('academic_topic'),
                                                'academic_level' => getRouteParameter('academic_level'),
                                                'academic_group' => getRouteParameter('academic_group')
                                            ]) }}'
                                        )"
                        >
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                            Delete
                        </x-button.secondary>
                        <x-link.primary :to="route('essay-questions.edit', [
                                        'essay_question' => $essayQuestion,
                                        'academic_subject' => getRouteParameter('academic_subject'),
                                        'academic_topic' => getRouteParameter('academic_topic'),
                                        'academic_level' => getRouteParameter('academic_level'),
                                        'academic_group' => getRouteParameter('academic_group')
                                    ])">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                            </svg>
                            Edit
                        </x-link.primary>
                    </div>
                @endcan
            </div>
        </div>

        <!-- Question Content -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">Question Content</h2>
            </div>
            <div class="p-6 prose max-w-none">
                <span x-html="marked.parse(@js($essayQuestion->question->summary))"></span>
            </div>
        </div>

        <!-- Topic Information -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">Topic Information</h2>
            </div>
            <div class="divide-y divide-gray-200">
                <div class="px-6 py-4">
                    <dt class="text-sm font-medium text-gray-500">Academic Topic</dt>
                    <dd class="mt-1">
                        <x-anchor :to="route('academic-topics.show', [
                                        'academic_topic' => $essayQuestion->academicTopic,
                                        'academic_subject' => getRouteParameter('academic_subject'),
                                        'academic_level' => getRouteParameter('academic_level'),
                                        'academic_group' => getRouteParameter('academic_group')
                                    ])">
                            {{ $essayQuestion->academicTopic->name }}
                        </x-anchor>
                    </dd>
                </div>

                @if (isset($essayQuestion->academicTopic->subtopic))
                    <div class="px-6 py-4">
                        <dt class="text-sm font-medium text-gray-500">Sub Topic</dt>
                        <dd class="mt-1">
                            <x-anchor
                                :to="route('subtopics.index', ['academic_topic' => $essayQuestion->academicTopic])">
                                {{ $essayQuestion->subtopic->name }}
                            </x-anchor>
                        </dd>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-layouts.app>
