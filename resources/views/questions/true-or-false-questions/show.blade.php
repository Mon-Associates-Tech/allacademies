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

    <div class="max-w-5xl mx-auto">
        <!-- Unified container for header and content -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
            <!-- Header Section using academic-header component -->
            <x-academic-header>
                <x-slot:headerIcon>
                    <div class="flex-shrink-0 w-12 h-12 sm:w-14 sm:h-14 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-xl flex items-center justify-center shadow-lg">
                        <svg class="w-6 h-6 sm:w-7 sm:h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </x-slot:headerIcon>
                <x-slot name="headerContent">
                    <div class="flex items-center space-x-4">
                        <div>
                            <h1 class="text-xl sm:text-2xl font-bold text-gray-800 dark:text-white">True/False Question</h1>
                            <p class="text-gray-600 dark:text-gray-400 text-sm mt-1">
                                Topic: {{ $trueOrFalseQuestion->academicTopic->name }}
                            </p>
                        </div>
                    </div>
                </x-slot>

                @can('moderate')
                    <x-slot name="headerActions">
                        <div class="flex flex-col sm:flex-row gap-2">
                            <x-link.secondary :to="route('true-or-false-questions.edit', [
                                    'true_or_false_question' => $trueOrFalseQuestion,
                                    'academic_subject' => getRouteParameter('academic_subject'),
                                    'academic_topic' => getRouteParameter('academic_topic'),
                                    'academic_level' => getRouteParameter('academic_level'),
                                    'academic_group' => getRouteParameter('academic_group')
                                ])"
                                class="inline-flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-all duration-200 hover:scale-105">
                                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                                Edit
                            </x-link.secondary>
                            <button type="button"
                                    class="inline-flex items-center px-3 py-2 text-sm font-medium rounded-lg text-white bg-red-600 hover:bg-red-700 transition-all duration-200 hover:scale-105"
                                    x-data="{}"
                                    x-on:click="$store.deleteForm.show(
                                        'Delete Question',
                                        'Are you sure you want to delete this question? This action cannot be undone.',
                                        '{{ route('true-or-false-questions.destroy', [
                                            'true_or_false_question' => $trueOrFalseQuestion,
                                            'academic_subject' => getRouteParameter('academic_subject'),
                                            'academic_topic' => getRouteParameter('academic_topic'),
                                            'academic_level' => getRouteParameter('academic_level'),
                                            'academic_group' => getRouteParameter('academic_group')
                                        ]) }}'
                                    )">
                                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                                Delete
                            </button>
                        </div>
                    </x-slot>
                @endcan
            </x-academic-header>


            <!-- Question Content -->
            <div class="border-t border-gray-200 dark:border-gray-700">
                <div class="px-6 py-4 bg-gray-50 dark:bg-gray-800/50 border-b border-gray-200 dark:border-gray-700">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Question Content</h2>
                </div>
                <div class="p-6 space-y-6">
                    <!-- Question Statement -->
                    <div class="prose dark:prose-invert max-w-none p-4 bg-gray-50 dark:bg-gray-700/30 rounded-lg border border-gray-200 dark:border-gray-600">
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2 flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            Question Statement
                        </h3>
                        <div>
                            <x-form.markdown-with-math :content="$trueOrFalseQuestion->question->down" />
                        </div>
                    </div>

                    <!-- Correct Answer -->
                    <div class="prose dark:prose-invert max-w-none p-4 bg-gray-50 dark:bg-gray-700/30 rounded-lg border border-gray-200 dark:border-gray-600">
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2 flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Correct Answer
                        </h3>
                        <div class="flex items-center space-x-4">
                            @if($trueOrFalseQuestion->answer)
                                <div class="flex items-center space-x-2 text-green-700 dark:text-green-400">
                                    <div class="w-8 h-8 bg-green-100 dark:bg-green-900 rounded-full flex items-center justify-center">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                        </svg>
                                    </div>
                                    <span class="font-semibold text-xl">True</span>
                                </div>
                            @else
                                <div class="flex items-center space-x-2 text-red-700 dark:text-red-400">
                                    <div class="w-8 h-8 bg-red-100 dark:bg-red-900 rounded-full flex items-center justify-center">
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                        </svg>
                                    </div>
                                    <span class="font-semibold text-xl">False</span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Question Properties -->
            <div class="border-t border-gray-200 dark:border-gray-700">
                <div class="px-6 py-4 bg-gray-50 dark:bg-gray-800/50 border-b border-gray-200 dark:border-gray-700">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Question Properties</h2>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <!-- Difficulty Level -->
                        <div class="bg-gradient-to-br from-orange-50 to-amber-50 dark:from-gray-700 dark:to-gray-800 rounded-xl p-5 border border-gray-200 dark:border-gray-600 shadow-sm">
                            <div class="flex items-start">
                                <div class="p-2 bg-orange-100 dark:bg-orange-900/30 rounded-lg">
                                    @php
                                        $difficultyColor = match($trueOrFalseQuestion->difficulty_level) {
                                            'easy' => 'text-green-600 dark:text-green-400',
                                            'medium' => 'text-yellow-600 dark:text-yellow-400',
                                            'hard' => 'text-red-600 dark:text-red-400',
                                            default => 'text-gray-600 dark:text-gray-400'
                                        };
                                    @endphp
                                    <svg class="w-5 h-5 sm:w-6 sm:h-6 {{ $difficultyColor }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                    </svg>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Difficulty</p>
                                    <p class="text-xl font-bold text-gray-900 dark:text-white mt-1 capitalize">
                                        {{ $trueOrFalseQuestion->difficulty_level ?? 'Not set' }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Score Points -->
                        <div class="bg-gradient-to-br from-blue-50 to-indigo-50 dark:from-gray-700 dark:to-gray-800 rounded-xl p-5 border border-gray-200 dark:border-gray-600 shadow-sm">
                            <div class="flex items-start">
                                <div class="p-2 bg-blue-100 dark:bg-blue-900/30 rounded-lg">
                                    <svg class="w-5 h-5 sm:w-6 sm:h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                    </svg>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Score</p>
                                    <p class="text-xl font-bold text-gray-900 dark:text-white mt-1">
                                        {{ $trueOrFalseQuestion->score ?? 'Not set' }}
                                        @if($trueOrFalseQuestion->score)
                                            <span class="text-base text-gray-500">points</span>
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Question Type -->
                        <div class="bg-gradient-to-br from-purple-50 to-violet-50 dark:from-gray-700 dark:to-gray-800 rounded-xl p-5 border border-gray-200 dark:border-gray-600 shadow-sm">
                            <div class="flex items-start">
                                <div class="p-2 bg-purple-100 dark:bg-purple-900/30 rounded-lg">
                                    <svg class="w-5 h-5 sm:w-6 sm:h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Question Type</p>
                                    <p class="text-xl font-bold text-gray-900 dark:text-white mt-1">True/False</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Academic Context -->
            <div class="border-t border-gray-200 dark:border-gray-700">
                <div class="px-6 py-4 bg-gray-50 dark:bg-gray-800/50 border-b border-gray-200 dark:border-gray-700">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Academic Context</h2>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="bg-gradient-to-br from-gray-50 to-blue-50 dark:from-gray-700 dark:to-gray-800 rounded-xl p-5 border border-gray-200 dark:border-gray-600 shadow-sm">
                            <h3 class="text-base font-medium text-gray-700 dark:text-gray-300 mb-4">Academic Hierarchy</h3>
                            <div class="space-y-3">
                                <div class="flex items-center text-sm">
                                    <span class="text-gray-600 dark:text-gray-400 w-20">Group:</span>
                                    <x-anchor :to="route('academic-groups.show', ['academic_group' => $trueOrFalseQuestion->academicTopic->academicSubject->academicLevel->academicGroup])"
                                              class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300 font-medium inline-flex items-center">
                                        {{ $trueOrFalseQuestion->academicTopic->academicSubject->academicLevel->academicGroup->name }}
                                        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                        </svg>
                                    </x-anchor>
                                </div>
                                <div class="flex items-center text-sm">
                                    <span class="text-gray-600 dark:text-gray-400 w-20">Level:</span>
                                    <x-anchor :to="route('academic-levels.show', [
                                            'academic_level' => $trueOrFalseQuestion->academicTopic->academicSubject->academicLevel,
                                            'academic_group' => getRouteParameter('academic_group')
                                        ])"
                                        class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300 font-medium inline-flex items-center">
                                        {{ $trueOrFalseQuestion->academicTopic->academicSubject->academicLevel->name }}
                                        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                        </svg>
                                    </x-anchor>
                                </div>
                                <div class="flex items-center text-sm">
                                    <span class="text-gray-600 dark:text-gray-400 w-20">Subject:</span>
                                    <x-anchor :to="route('academic-subjects.show', [
                                            'academic_subject' => $trueOrFalseQuestion->academicTopic->academicSubject,
                                            'academic_level' => getRouteParameter('academic_level'),
                                            'academic_group' => getRouteParameter('academic_group')
                                        ])"
                                        class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300 font-medium inline-flex items-center">
                                        {{ $trueOrFalseQuestion->academicTopic->academicSubject->name }}
                                        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                        </svg>
                                    </x-anchor>
                                </div>
                                <div class="flex items-center text-sm">
                                    <span class="text-gray-600 dark:text-gray-400 w-20">Topic:</span>
                                    <x-anchor :to="route('academic-topics.show', [
                                            'academic_topic' => $trueOrFalseQuestion->academicTopic,
                                            'academic_subject' => getRouteParameter('academic_subject'),
                                            'academic_level' => getRouteParameter('academic_level'),
                                            'academic_group' => getRouteParameter('academic_group')
                                        ])"
                                        class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300 font-medium inline-flex items-center">
                                        {{ $trueOrFalseQuestion->academicTopic->name }}
                                        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                        </svg>
                                    </x-anchor>
                                </div>
                                @if($trueOrFalseQuestion->subtopic)
                                    <div class="flex items-center text-sm">
                                        <span class="text-gray-600 dark:text-gray-400 w-20">Subtopic:</span>
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 dark:bg-purple-900/30 text-purple-800 dark:text-purple-300">
                                            {{ $trueOrFalseQuestion->subtopic->name }}
                                        </span>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="bg-gradient-to-br from-gray-50 to-indigo-50 dark:from-gray-700 dark:to-gray-800 rounded-xl p-5 border border-gray-200 dark:border-gray-600 shadow-sm">
                            <h3 class="text-base font-medium text-gray-700 dark:text-gray-300 mb-4">Question Details</h3>
                            <div class="space-y-3">
                                <div class="flex items-center text-sm">
                                    <span class="text-gray-600 dark:text-gray-400 w-24">Question ID:</span>
                                    <span class="text-gray-700 dark:text-gray-300 font-medium">
                                        #{{ $trueOrFalseQuestion->id }}
                                    </span>
                                </div>
                                <div class="flex items-center text-sm">
                                    <span class="text-gray-600 dark:text-gray-400 w-24">Created:</span>
                                    <span class="text-gray-700 dark:text-gray-300">
                                        {{ $trueOrFalseQuestion->created_at->format('M j, Y g:i A') }}
                                    </span>
                                </div>
                                @if($trueOrFalseQuestion->updated_at->ne($trueOrFalseQuestion->created_at))
                                    <div class="flex items-center text-sm">
                                        <span class="text-gray-600 dark:text-gray-400 w-24">Updated:</span>
                                        <span class="text-gray-700 dark:text-gray-300">
                                            {{ $trueOrFalseQuestion->updated_at->format('M j, Y g:i A') }}
                                        </span>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
