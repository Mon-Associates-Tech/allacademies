<x-layouts.app title="Edit Essay Question" :main-only="false">
    <x-slot name="breadcrumb">
        <x-breadcrumb :paths="[
            'Academic Groups' => route('academic-groups.index'),
            $essayQuestion->academicTopic->academicSubject->academicLevel->academicGroup->name => route('academic-groups.show', ['academic_group' => $essayQuestion->academicTopic->academicSubject->academicLevel->academicGroup]),
            'Academic Levels' => route('academic-levels.index', ['academic_group' => $essayQuestion->academicTopic->academicSubject->academicLevel->academicGroup]),
            $essayQuestion->academicTopic->academicSubject->academicLevel->name => route('academic-levels.show', ['academic_level' => $essayQuestion->academicTopic->academicSubject->academicLevel, 'academic_group' => getRouteParameter('academic_group')]),
            'Academic Subjects' => route('academic-subjects.index', ['academic_level' => $essayQuestion->academicTopic->academicSubject->academicLevel, 'academic_group' => getRouteParameter('academic_group')]),
            $essayQuestion->academicTopic->academicSubject->name => route('academic-subjects.show', ['academic_subject' => $essayQuestion->academicTopic->academicSubject, 'academic_level' => getRouteParameter('academic_level'), 'academic_group' => getRouteParameter('academic_group')]),
            'Academic Topics' => route('academic-topics.index', ['academic_subject' => $essayQuestion->academicTopic->academicSubject, 'academic_level' => getRouteParameter('academic_level'), 'academic_group' => getRouteParameter('academic_group')]),
            $essayQuestion->academicTopic->name => route('academic-topics.show', ['academic_topic' => $essayQuestion->academicTopic, 'academic_subject' => getRouteParameter('academic_subject'), 'academic_level' => getRouteParameter('academic_level'), 'academic_group' => getRouteParameter('academic_group')]),
            'Essay Questions' => route('essay-questions.index', ['academic_topic' => $essayQuestion->academicTopic, 'academic_subject' => getRouteParameter('academic_subject'), 'academic_level' => getRouteParameter('academic_level'), 'academic_group' => getRouteParameter('academic_group')]),
            'Question #' . $essayQuestion->id => route('essay-questions.show', ['essay_question' => $essayQuestion, 'academic_subject' => getRouteParameter('academic_subject'), 'academic_topic' => getRouteParameter('academic_topic'), 'academic_level' => getRouteParameter('academic_level'), 'academic_group' => getRouteParameter('academic_group')]),
            'Edit' => null,
        ]"/>
    </x-slot>

    <div class="max-w-7xl mx-auto">
        <!-- Page Header -->
        <div
            class="mb-6 bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
            <div class="flex items-center space-x-4">
                <div class="p-3 bg-amber-100 dark:bg-amber-900/50 rounded-full">
                    <svg class="w-6 h-6 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor"
                         viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                </div>
                <div class="">
                    <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Edit Essay Question</h1>
                    <p class="text-gray-600 dark:text-gray-400">
                        Modify the essay question in <span
                            class="font-medium">{{ $essayQuestion->academicTopic->name }}</span>
                        <span class="text-gray-400">• ID: {{ $essayQuestion->id }}</span>
                    </p>
                </div>
            </div>
        </div>

        <!-- Main Content Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-5 gap-6">
            <!-- Form Section -->
            <div class="lg:col-span-3">
                @include('questions.essay-questions.form', [
'academicTopic' => $essayQuestion->academicTopic,
'action' => route('essay-questions.update', [
    'essay_question' => $essayQuestion,
     'academic_topic' => $essayQuestion->academicTopic,
     'academic_subject' => getRouteParameter('academic_subject'),
     'academic_level' => getRouteParameter('academic_level'),
     'academic_group' => getRouteParameter('academic_group')
 ]),
 'description' => 'Update the details for this essay question.',
 'title' => 'Edit Question Details',
 'submitText' => 'Update Question',
 'essayQuestion' => $essayQuestion,
 'method' => 'PATCH'
])
            </div>

            <!-- Sidebar -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Question Info -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Question Information</h2>
                    </div>
                    <div class="p-6 space-y-4">
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Question ID</dt>
                            <dd class="text-sm text-gray-900 dark:text-white font-mono">{{ $essayQuestion->id }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Created</dt>
                            <dd class="text-sm text-gray-900 dark:text-white">{{ $essayQuestion->created_at->format('M j, Y g:i A') }}</dd>
                        </div>
                        @if($essayQuestion->updated_at->ne($essayQuestion->created_at))
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Last Modified</dt>
                                <dd class="text-sm text-gray-900 dark:text-white">{{ $essayQuestion->updated_at->format('M j, Y g:i A') }}</dd>
                            </div>
                        @endif
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Current Topic</dt>
                            <dd class="text-sm text-blue-600 dark:text-blue-400">{{ $essayQuestion->academicTopic->name }}</dd>
                        </div>
                        @if($essayQuestion->subtopic)
                            <div>
                                <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Current Subtopic</dt>
                                <dd class="text-sm text-gray-900 dark:text-white">{{ $essayQuestion->subtopic->name }}</dd>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Quick Actions</h2>
                    </div>
                    <div class="p-6 space-y-3">
                        <x-link.secondary
                            :to="route('essay-questions.show', [
                                'essay_question' => $essayQuestion,
                                'academic_subject' => getRouteParameter('academic_subject'),
                                'academic_topic' => getRouteParameter('academic_topic'),
                                'academic_level' => getRouteParameter('academic_level'),
                                'academic_group' => getRouteParameter('academic_group')
                            ])"
                            class="w-full justify-center"
                        >
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                            </svg>
                            View Question
                        </x-link.secondary>

                        <x-link.secondary
                            :to="route('essay-questions.index', [
                                'academic_topic' => $essayQuestion->academicTopic,
                                'academic_subject' => getRouteParameter('academic_subject'),
                                'academic_level' => getRouteParameter('academic_level'),
                                'academic_group' => getRouteParameter('academic_group')
                            ])"
                            class="w-full justify-center"
                        >
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                            </svg>
                            All Questions
                        </x-link.secondary>
                    </div>
                </div>

                <!-- Helpful Tools -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
                    <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Helpful Tools</h2>
                    </div>
                    <div class="p-6">
                        <x-plugins link="{{ url()->current() . '/edit' }}"/>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
