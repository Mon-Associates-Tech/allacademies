<x-layouts.app title="Essay Question Details" :has-action="false">
    <x-slot name="breadcrumb">
        <x-breadcrumb :paths="[
            'Academic Groups' => route('academic-groups.index'),
            $essayQuestion->academicTopic->academicSubject->academicLevel->academicGroup->name => route('academic-groups.show', ['academic_group' => $essayQuestion->academicTopic->academicSubject->academicLevel->academicGroup]),
            'Academic Levels' => route('academic-levels.index', ['academic_group' => $essayQuestion->academicTopic->academicSubject->academicLevel->academicGroup]),
            $essayQuestion->academicTopic->academicSubject->academicLevel->name => route('academic-levels.show', ['academic_level' => $essayQuestion->academicTopic->academicSubject->academicLevel, 'academic_group' => getRouteParameter('academic_group')]),
            'Academic Subjects' => route('academic-subjects.index', ['academic_level' => $essayQuestion->academicTopic->academicSubject->academicLevel, 'academic_group' => getRouteParameter('academic_group')]),
            $essayQuestion->academicTopic->academicSubject->name => route('academic-subjects.show', ['academic_subject' => $essayQuestion->academicTopic->academicSubject, 'academic_level' => getRouteParameter('academic_level'), 'academic_group' => getRouteParameter('academic_group')]),
            'Academic Topics' => route('academic-topics.index', ['academic_subject' => $essayQuestion->academicTopic->academicSubject,  'academic_level' => getRouteParameter('academic_level'), 'academic_group' => getRouteParameter('academic_group')]),
            $essayQuestion->academicTopic->name => route('academic-topics.show', ['academic_topic' => $essayQuestion->academicTopic, 'academic_subject' => getRouteParameter('academic_subject'), 'academic_level' => getRouteParameter('academic_level'), 'academic_group' => getRouteParameter('academic_group')]),
            'Essay Questions' => route('essay-questions.index', ['academic_topic' => $essayQuestion->academicTopic, 'academic_subject' => getRouteParameter('academic_subject'), 'academic_level' => getRouteParameter('academic_level'), 'academic_group' => getRouteParameter('academic_group')]),
        ]" class="overflow-x-auto"/>
    </x-slot>

    <div class="max-w-4xl mx-auto">
        <!-- Question Header -->
        <div
            class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6 mb-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-xl md:text-2xl font-bold text-gray-900 dark:text-white">
                        Essay Question
                    </h1>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                        ID: {{ $essayQuestion->id }}
                    </p>
                </div>
                @can('moderate')
                    <div class="flex space-x-2">
                        <x-button.secondary
                            type="button"
                            x-data="{}"
                            x-on:click="$store.deleteForm.show(
                                'Delete Question',
                                'Are you sure you want to delete this essay question?',
                                '{{ route('essay-questions.destroy', ['essay_question' => $essayQuestion, 'academic_subject' => getRouteParameter('academic_subject'), 'academic_topic' => getRouteParameter('academic_topic'), 'academic_level' => getRouteParameter('academic_level'), 'academic_group' => getRouteParameter('academic_group')]) }}'
                            )"
                            class="hidden sm:inline-flex"
                        >
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                            </svg>
                            Delete
                        </x-button.secondary>
                        <x-link.primary
                            :to="route('essay-questions.edit', ['essay_question' => $essayQuestion, 'academic_subject' => getRouteParameter('academic_subject'), 'academic_topic' => getRouteParameter('academic_topic'), 'academic_level' => getRouteParameter('academic_level'), 'academic_group' => getRouteParameter('academic_group')])"
                            class="hidden sm:inline-flex"
                        >
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

        <!-- Question Properties -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <!-- Score -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-4">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="w-8 h-8 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Score</p>
                        <p class="text-lg font-semibold text-gray-900 dark:text-white">
                            {{ $essayQuestion->score ?? 'Not set' }}
                            @if($essayQuestion->score)
                                <span class="text-sm text-gray-500">points</span>
                            @endif
                        </p>
                    </div>
                </div>
            </div>

            <!-- Difficulty Level -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-4">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        @php
                            $difficultyColor = match($essayQuestion->difficulty_level) {
                                'easy' => 'text-green-500',
                                'medium' => 'text-yellow-500',
                                'hard' => 'text-red-500',
                                default => 'text-gray-500'
                            };
                        @endphp
                        <svg class="w-8 h-8 {{ $difficultyColor }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Difficulty</p>
                        <p class="text-lg font-semibold text-gray-900 dark:text-white capitalize">
                            {{ $essayQuestion->difficulty_level ?? 'Not set' }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Question Content -->
        <div
            class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden mb-6">
            <!-- Question Text -->
            <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                <h2 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Question</h2>
                <div class="prose dark:prose-invert max-w-none">
                    <span x-html="marked.parse(@js($essayQuestion->question->down))"></span>
                </div>
            </div>

            <!-- Sample Answer -->
            @if($essayQuestion->answer && $essayQuestion->answer->html)
                <div class="p-6 bg-blue-50 dark:bg-blue-900/20">
                    <h2 class="text-sm font-medium text-blue-700 dark:text-blue-300 mb-2 flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                        </svg>
                        Sample Answer / Guidelines
                    </h2>
                    <div class="prose dark:prose-invert max-w-none">
                        <span x-html="marked.parse(@js($essayQuestion->answer->down))"></span>
                    </div>
                </div>
            @endif
        </div>

        <!-- Assessment Guidelines -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden mb-6">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
                    <svg class="w-5 h-5 mr-2 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v11a2 2 0 002 2h5.586a1 1 0 00.707-.293l5.414-5.414a1 1 0 00.293-.707V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                    Essay Assessment Guidelines
                </h2>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h3 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">Grading Criteria</h3>
                        <ul class="space-y-2 text-sm text-gray-600 dark:text-gray-400">
                            <li class="flex items-center">
                                <svg class="w-4 h-4 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                Content relevance and accuracy
                            </li>
                            <li class="flex items-center">
                                <svg class="w-4 h-4 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                Organization and structure
                            </li>
                            <li class="flex items-center">
                                <svg class="w-4 h-4 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                Grammar and language use
                            </li>
                            <li class="flex items-center">
                                <svg class="w-4 h-4 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                </svg>
                                Critical thinking and analysis
                            </li>
                        </ul>
                    </div>
                    <div>
                        <h3 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-3">Question Type</h3>
                        <div class="bg-gray-50 dark:bg-gray-700 rounded-lg p-3">
                            <div class="flex items-center">
                                <svg class="w-6 h-6 text-orange-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/>
                                </svg>
                                <div>
                                    <p class="font-medium text-gray-900 dark:text-white">Essay Question</p>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Open-ended response required</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Academic Information -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="bg-gray-50 dark:bg-gray-700/50 px-6 py-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Academic Hierarchy</h3>
                        <div class="space-y-2">
                            <div class="flex items-center text-sm">
                                <span class="text-gray-600 dark:text-gray-400 w-16">Group:</span>
                                <x-anchor
                                    to="{{ route('academic-groups.show', ['academic_group' => $essayQuestion->academicTopic->academicSubject->academicLevel->academicGroup]) }}"
                                    class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 font-medium"
                                >
                                    {{ $essayQuestion->academicTopic->academicSubject->academicLevel->academicGroup->name }}
                                </x-anchor>
                            </div>
                            <div class="flex items-center text-sm">
                                <span class="text-gray-600 dark:text-gray-400 w-16">Level:</span>
                                <x-anchor
                                    to="{{ route('academic-levels.show', ['academic_level' => $essayQuestion->academicTopic->academicSubject->academicLevel, 'academic_group' => getRouteParameter('academic_group')]) }}"
                                    class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 font-medium"
                                >
                                    {{ $essayQuestion->academicTopic->academicSubject->academicLevel->name }}
                                </x-anchor>
                            </div>
                            <div class="flex items-center text-sm">
                                <span class="text-gray-600 dark:text-gray-400 w-16">Subject:</span>
                                <x-anchor
                                    to="{{ route('academic-subjects.show', ['academic_subject' => $essayQuestion->academicTopic->academicSubject, 'academic_level' => getRouteParameter('academic_level'), 'academic_group' => getRouteParameter('academic_group')]) }}"
                                    class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 font-medium"
                                >
                                    {{ $essayQuestion->academicTopic->academicSubject->name }}
                                </x-anchor>
                            </div>
                            <div class="flex items-center text-sm">
                                <span class="text-gray-600 dark:text-gray-400 w-16">Topic:</span>
                                <x-anchor
                                    to="{{ route('academic-topics.show', ['academic_topic' => $essayQuestion->academicTopic, 'academic_subject' => getRouteParameter('academic_subject'), 'academic_level' => getRouteParameter('academic_level'), 'academic_group' => getRouteParameter('academic_group')]) }}"
                                    class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 font-medium"
                                >
                                    {{ $essayQuestion->academicTopic->name }}
                                </x-anchor>
                            </div>
                            @if(isset($essayQuestion->subtopic))
                                <div class="flex items-center text-sm">
                                    <span class="text-gray-600 dark:text-gray-400 w-16">Subtopic:</span>
                                    <span class="text-gray-700 dark:text-gray-300 font-medium">
                                        {{ $essayQuestion->subtopic->name }}
                                    </span>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div>
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Question Details</h3>
                        <div class="space-y-2">
                            <div class="flex items-center text-sm">
                                <span class="text-gray-600 dark:text-gray-400 w-20">Type:</span>
                                <span class="text-gray-700 dark:text-gray-300">Essay Question</span>
                            </div>
                            <div class="flex items-center text-sm">
                                <span class="text-gray-600 dark:text-gray-400 w-20">Has Answer:</span>
                                <span class="text-gray-700 dark:text-gray-300">
                                    @if($essayQuestion->answer && $essayQuestion->answer->html)
                                        <span class="inline-flex items-center text-green-600 dark:text-green-400">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                            </svg>
                                            Yes
                                        </span>
                                    @else
                                        <span class="inline-flex items-center text-red-600 dark:text-red-400">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                            </svg>
                                            No
                                        </span>
                                    @endif
                                </span>
                            </div>
                            <div class="flex items-center text-sm">
                                <span class="text-gray-600 dark:text-gray-400 w-20">Created:</span>
                                <span class="text-gray-700 dark:text-gray-300">
                                    {{ $essayQuestion->created_at->format('M j, Y g:i A') }}
                                </span>
                            </div>
                            @if($essayQuestion->updated_at->ne($essayQuestion->created_at))
                                <div class="flex items-center text-sm">
                                    <span class="text-gray-600 dark:text-gray-400 w-20">Updated:</span>
                                    <span class="text-gray-700 dark:text-gray-300">
                                        {{ $essayQuestion->updated_at->format('M j, Y g:i A') }}
                                    </span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Mobile Action Buttons -->
        @can('moderate')
            <div
                class="fixed bottom-0 left-0 right-0 bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700 p-4 sm:hidden">
                <div class="flex justify-between gap-4">
                    <x-button.secondary
                        type="button"
                        x-data="{}"
                        x-on:click="$store.deleteForm.show('Delete Question', 'Are you sure?', '{{academicRoute('essay-questions.destroy', ['essay_question' => $essayQuestion])}}')"
                        class="flex-1"
                    >
                        Delete
                    </x-button.secondary>
                    <x-link.primary
                        :to="academicRoute('essay-questions.destroy', ['essay_question' => $essayQuestion])"
                        class="flex-1"
                    >
                        Edit
                    </x-link.primary>
                </div>
            </div>
        @endcan
    </div>
</x-layouts.app>
