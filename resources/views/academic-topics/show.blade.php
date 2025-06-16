<x-layouts.app title="{{ $academicTopic->name }} Topic Details" action-link-text="Add Subtopic"
    :action_link="route('subtopics.create', ['academic_topic' => $academicTopic, 'academic_subject' => getRouteParameter('academic_subject'), 'academic_level' => getRouteParameter('academic_level'), 'academic_group' => getRouteParameter('academic_group')])">
    <x-slot name="breadcrumb">
        <x-breadcrumb :paths="[
            'Academic Groups' => route('academic-groups.index'),
            $academicTopic->academicSubject->academicLevel->academicGroup->name => route('academic-groups.show', ['academic_group' => $academicTopic->academicSubject->academicLevel->academicGroup]),
            'Academic Levels' => route('academic-levels.index', ['academic_group' => $academicTopic->academicSubject->academicLevel->academicGroup]),
            $academicTopic->academicSubject->academicLevel->name => route('academic-levels.show', ['academic_level' => $academicTopic->academicSubject->academicLevel, 'academic_group' => $academicTopic->academicSubject->academicLevel->academicGroup]),
            'Academic Subjects' => route('academic-subjects.index', ['academic_level' => $academicTopic->academicSubject->academicLevel, 'academic_group' => $academicTopic->academicSubject->academicLevel->academicGroup]),
            $academicTopic->academicSubject->name => route('academic-subjects.show', ['academic_subject' => $academicTopic->academicSubject, 'academic_level' => $academicTopic->academicSubject->academicLevel, 'academic_group' => $academicTopic->academicSubject->academicLevel->academicGroup]),
            'Academic Topics' => route('academic-topics.index', ['academic_subject' => $academicTopic->academicSubject, 'academic_level' => $academicTopic->academicSubject->academicLevel, 'academic_group' => $academicTopic->academicSubject->academicLevel->academicGroup]),
            $academicTopic->name => null,
        ]" />
    </x-slot>

    <div class="space-y-6">
        <!-- Header Section -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-start justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 mb-2">{{ $academicTopic->name }}</h1>
                    <div class="flex items-center space-x-2 text-sm text-gray-500">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                        </svg>
                        <span>Part of {{ $academicTopic->academicSubject->name }}</span>
                    </div>
                </div>
                @can('administrate')
                <div class="flex space-x-3">
                    <x-button.secondary type="button" x-data="{}"
                        x-on:click="$store.deleteForm.show('Danger', 'Are you sure you want to delete {{ $academicTopic->name }}?', '{{ route('academic-topics.destroy', ['academic_topic' => $academicTopic, 'academic_subject' => getRouteParameter('academic_subject'), 'academic_level' => getRouteParameter('academic_level'), 'academic_group' => getRouteParameter('academic_group')]) }}')">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                        Delete
                    </x-button.secondary>
                    <x-link.primary :to="route('academic-topics.edit', ['academic_topic' => $academicTopic, 'academic_subject' => getRouteParameter('academic_subject'), 'academic_level' => getRouteParameter('academic_level'), 'academic_group' => getRouteParameter('academic_group')])">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                        Edit Topic
                    </x-link.primary>
                </div>
                @endcan
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Multiple Choice Questions Card -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow duration-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Multiple Choice Questions</p>
                        <p class="text-3xl font-bold text-blue-600">{{ $academicTopic->multiple_choice_questions_count }}</p>
                        <p class="text-sm text-gray-500 mt-1">{{ Str::plural('question', $academicTopic->multiple_choice_questions_count) }}</p>
                    </div>
                    <div class="p-3 bg-blue-100 rounded-full">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                    </div>
                </div>
                <div class="mt-4">
                    <x-anchor :to="route('multiple-choice-questions.index', ['academic_topic' => $academicTopic, 'academic_subject' => getRouteParameter('academic_subject'), 'academic_level' => getRouteParameter('academic_level'), 'academic_group' => getRouteParameter('academic_group')])" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                        View all questions →
                    </x-anchor>
                </div>
            </div>

            <!-- True or False Questions Card -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow duration-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">True/False Questions</p>
                        <p class="text-3xl font-bold text-green-600">{{ $academicTopic->true_or_false_questions_count }}</p>
                        <p class="text-sm text-gray-500 mt-1">{{ Str::plural('question', $academicTopic->true_or_false_questions_count) }}</p>
                    </div>
                    <div class="p-3 bg-green-100 rounded-full">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                    </div>
                </div>
                <div class="mt-4">
                    <x-anchor :to="route('true-or-false-questions.index', ['academic_topic' => $academicTopic, 'academic_subject' => getRouteParameter('academic_subject'), 'academic_level' => getRouteParameter('academic_level'), 'academic_group' => getRouteParameter('academic_group')])" class="text-green-600 hover:text-green-800 text-sm font-medium">
                        View all questions →
                    </x-anchor>
                </div>
            </div>

            <!-- Essay Questions Card -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow duration-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Essay Questions</p>
                        <p class="text-3xl font-bold text-purple-600">{{ $academicTopic->essay_questions_count }}</p>
                        <p class="text-sm text-gray-500 mt-1">{{ Str::plural('question', $academicTopic->essay_questions_count) }}</p>
                    </div>
                    <div class="p-3 bg-purple-100 rounded-full">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                    </div>
                </div>
                <div class="mt-4">
                    <x-anchor :to="route('essay-questions.index', ['academic_topic' => $academicTopic, 'academic_subject' => getRouteParameter('academic_subject'), 'academic_level' => getRouteParameter('academic_level'), 'academic_group' => getRouteParameter('academic_group')])" class="text-purple-600 hover:text-purple-800 text-sm font-medium">
                        View all questions →
                    </x-anchor>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        @can('moderate')
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">Quick Actions</h2>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <x-link.secondary :to="route('subtopics.create', ['academic_topic' => $academicTopic, 'academic_subject' => getRouteParameter('academic_subject'), 'academic_level' => getRouteParameter('academic_level'), 'academic_group' => getRouteParameter('academic_group')])" class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors duration-200">
                        <svg class="w-5 h-5 text-gray-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        <div>
                            <div class="font-medium text-gray-900">Add Subtopic</div>
                            <div class="text-sm text-gray-500">Create new subtopic</div>
                        </div>
                    </x-link.secondary>
                </div>
            </div>
        </div>
        @endcan
    </div>
</x-layouts.app>

