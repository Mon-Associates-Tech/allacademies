<x-layouts.app title="Multiple Choice Question Details" :show-title-area="false" :has-action="false">
    <x-slot name="breadcrumb">
        <x-breadcrumb :paths="[
            'Academic Groups' => route('academic-groups.index'),
            $multipleChoiceQuestion->academicTopic->academicSubject->academicLevel->academicGroup->name => route('academic-groups.show', ['academic_group' => $multipleChoiceQuestion->academicTopic->academicSubject->academicLevel->academicGroup]),
            'Academic Levels' => route('academic-levels.index', ['academic_group' => $multipleChoiceQuestion->academicTopic->academicSubject->academicLevel->academicGroup]),
            $multipleChoiceQuestion->academicTopic->academicSubject->academicLevel->name => route('academic-levels.show', ['academic_level' => $multipleChoiceQuestion->academicTopic->academicSubject->academicLevel, 'academic_group' => getRouteParameter('academic_group')]),
            'Academic Subjects' => route('academic-subjects.index', ['academic_level' => $multipleChoiceQuestion->academicTopic->academicSubject->academicLevel, 'academic_group' => getRouteParameter('academic_group')]),
            $multipleChoiceQuestion->academicTopic->academicSubject->name => route('academic-subjects.show', ['academic_subject' => $multipleChoiceQuestion->academicTopic->academicSubject, 'academic_level' => getRouteParameter('academic_level'), 'academic_group' => getRouteParameter('academic_group')]),
            'Academic Topics' => route('academic-topics.index', ['academic_subject' => $multipleChoiceQuestion->academicTopic->academicSubject,  'academic_level' => getRouteParameter('academic_level'), 'academic_group' => getRouteParameter('academic_group')]),
            $multipleChoiceQuestion->academicTopic->name => route('academic-topics.show', ['academic_topic' => $multipleChoiceQuestion->academicTopic, 'academic_subject' => getRouteParameter('academic_subject'), 'academic_level' => getRouteParameter('academic_level'), 'academic_group' => getRouteParameter('academic_group')]),
            'Multiple Choice Questions' => route('multiple-choice-questions.index', ['academic_topic' => $multipleChoiceQuestion->academicTopic, 'academic_subject' => getRouteParameter('academic_subject'), 'academic_level' => getRouteParameter('academic_level'), 'academic_group' => getRouteParameter('academic_group')]),
        ]" class="overflow-x-auto"/>
    </x-slot>

    <div class="max-w-4xl mx-auto">
        <!-- Unified container for header and content -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
            <!-- Header Section using academic-header component -->
            <x-academic-header>
                <x-slot:headerIcon>
                    <div class="flex-shrink-0 w-12 h-12 sm:w-14 sm:h-14 bg-gradient-to-br from-purple-500 to-indigo-600 rounded-xl flex items-center justify-center shadow-lg">
                        <svg class="w-6 h-6 sm:w-7 sm:h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </x-slot:headerIcon>
                <x-slot name="headerContent">
                    <div class="flex items-center space-x-4">

                        <div>
                            <h1 class="text-xl sm:text-2xl font-bold text-gray-800 dark:text-white">Multiple Choice Question</h1>
                            <p class="text-gray-600 dark:text-gray-400 text-sm mt-1">
                                ID: {{ $multipleChoiceQuestion->id }} | Topic: {{ $multipleChoiceQuestion->academicTopic->name }}
                            </p>
                        </div>
                    </div>
                </x-slot>

                @can('moderate')
                    <x-slot name="headerActions">
                        <div class="flex flex-col sm:flex-row gap-2">
                            <x-link.secondary :to="route('multiple-choice-questions.edit', ['multiple_choice_question' => $multipleChoiceQuestion, 'academic_subject' => getRouteParameter('academic_subject'), 'academic_topic' => getRouteParameter('academic_topic'), 'academic_level' => getRouteParameter('academic_level'), 'academic_group' => getRouteParameter('academic_group')])"
                                              class="inline-flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-all duration-200 hover:scale-105">
                                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                                Edit
                            </x-link.secondary>
                            <button type="button"
                                    class="inline-flex items-center px-3 py-2 text-sm font-medium rounded-lg text-white bg-red-600 hover:bg-red-700 transition-all duration-200 hover:scale-105"
                                    x-data="{}"
                                    x-on:click="$store.deleteForm.show('Delete Question', 'Are you sure you want to delete this question?', '{{ route('multiple-choice-questions.destroy', ['multiple_choice_question' => $multipleChoiceQuestion, 'academic_subject' => getRouteParameter('academic_subject'), 'academic_topic' => getRouteParameter('academic_topic'), 'academic_level' => getRouteParameter('academic_level'), 'academic_group' => getRouteParameter('academic_group')]) }}')">
                                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
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
                    <!-- Question Text -->
                    <div class="prose dark:prose-invert max-w-none p-4 bg-gray-50 dark:bg-gray-700/30 rounded-lg border border-gray-200 dark:border-gray-600">
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Question</h3>
                        <div>
                            <span x-html="marked.parse(@js($multipleChoiceQuestion->question->down))"></span>
                        </div>
                    </div>

                    <!-- Options -->
                    <div class="space-y-4">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Answer Options</h3>
                        <div class="space-y-3">
                            @foreach(['a', 'b', 'c', 'd', 'e'] as $option)
                                @if($multipleChoiceQuestion->{"option_$option"}->html)
                                    <div class="flex items-start p-4 rounded-lg border {{ strtoupper($multipleChoiceQuestion->answer) === strtoupper($option) ? 'bg-green-50 dark:bg-green-900/20 border-green-200 dark:border-green-800/50' : 'bg-white dark:bg-gray-700/30 border-gray-200 dark:border-gray-600' }}">
                                        <span class="inline-flex items-center justify-center w-8 h-8 rounded-full flex-shrink-0
                                            {{ strtoupper($multipleChoiceQuestion->answer) === strtoupper($option)
                                                ? 'bg-green-100 dark:bg-green-900 text-green-700 dark:text-green-300 font-bold'
                                                : 'bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300'
                                            }}">
                                            {{ strtoupper($option) }}
                                        </span>
                                        @if(strtoupper($multipleChoiceQuestion->answer) === strtoupper($option))
                                            <div class="flex items-center ml-2">
                                                <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                </svg>
                                            </div>
                                        @endif
                                        <div class="ml-3 flex-1 min-w-0">
                                            @php
                                                $optText = $multipleChoiceQuestion->{"option_$option"}->down;
                                            @endphp
                                            <div class="prose dark:prose-invert max-w-none">
                                                <span x-html="marked.parse(@js($optText))"></span>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            @endforeach
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
                        <!-- Score -->
                        <div class="bg-gradient-to-br from-blue-50 to-indigo-50 dark:from-gray-700 dark:to-gray-800 rounded-xl p-5 border border-gray-200 dark:border-gray-600 shadow-sm">
                            <div class="flex items-start">
                                <div class="p-2 bg-blue-100 dark:bg-blue-900/30 rounded-lg">
                                    <svg class="w-5 h-5 sm:w-6 sm:h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Score</p>
                                    <p class="text-xl font-bold text-gray-900 dark:text-white mt-1">
                                        {{ $multipleChoiceQuestion->score ?? 'Not set' }}
                                        @if($multipleChoiceQuestion->score)
                                            <span class="text-base text-gray-500">points</span>
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Difficulty Level -->
                        <div class="bg-gradient-to-br from-orange-50 to-amber-50 dark:from-gray-700 dark:to-gray-800 rounded-xl p-5 border border-gray-200 dark:border-gray-600 shadow-sm">
                            <div class="flex items-start">
                                <div class="p-2 bg-orange-100 dark:bg-orange-900/30 rounded-lg">
                                    @php
                                        $difficultyColor = match($multipleChoiceQuestion->difficulty_level) {
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
                                        {{ $multipleChoiceQuestion->difficulty_level ?? 'Not set' }}
                                    </p>
                                </div>
                            </div>
                        </div>

                        <!-- Correct Answer -->
                        <div class="bg-gradient-to-br from-green-50 to-emerald-50 dark:from-gray-700 dark:to-gray-800 rounded-xl p-5 border border-gray-200 dark:border-gray-600 shadow-sm">
                            <div class="flex items-start">
                                <div class="p-2 bg-green-100 dark:bg-green-900/30 rounded-lg">
                                    <div class="w-6 h-6 rounded-full bg-green-500 flex items-center justify-center">
                                        <span class="text-white font-bold text-xs">
                                            {{ strtoupper($multipleChoiceQuestion->answer) }}
                                        </span>
                                    </div>
                                </div>
                                <div class="ml-4">
                                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Correct Answer</p>
                                    <p class="text-xl font-bold text-gray-900 dark:text-white mt-1">
                                        Option {{ strtoupper($multipleChoiceQuestion->answer) }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            <!-- Academic Information -->
            <div class="border-t border-gray-200 dark:border-gray-700">
                <div class="px-6 py-4 bg-gray-50 dark:bg-gray-800/50 border-b border-gray-200 dark:border-gray-700">
                    <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Academic Information</h2>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="bg-gradient-to-br from-gray-50 to-blue-50 dark:from-gray-700 dark:to-gray-800 rounded-xl p-5 border border-gray-200 dark:border-gray-600 shadow-sm">
                            <h3 class="text-base font-medium text-gray-700 dark:text-gray-300 mb-4">Academic Hierarchy</h3>
                            <div class="space-y-3">
                                <div class="flex items-center text-sm">
                                    <span class="text-gray-600 dark:text-gray-400 w-20">Group:</span>
                                    <x-anchor to="{{ route('academic-groups.show', ['academic_group' => $multipleChoiceQuestion->academicTopic->academicSubject->academicLevel->academicGroup]) }}"
                                              class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300 font-medium inline-flex items-center">
                                        {{ $multipleChoiceQuestion->academicTopic->academicSubject->academicLevel->academicGroup->name }}
                                        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                        </svg>
                                    </x-anchor>
                                </div>
                                <div class="flex items-center text-sm">
                                    <span class="text-gray-600 dark:text-gray-400 w-20">Level:</span>
                                    <x-anchor to="{{ route('academic-levels.show', ['academic_level' => $multipleChoiceQuestion->academicTopic->academicSubject->academicLevel, 'academic_group' => getRouteParameter('academic_group')]) }}"
                                              class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300 font-medium inline-flex items-center">
                                        {{ $multipleChoiceQuestion->academicTopic->academicSubject->academicLevel->name }}
                                        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                        </svg>
                                    </x-anchor>
                                </div>
                                <div class="flex items-center text-sm">
                                    <span class="text-gray-600 dark:text-gray-400 w-20">Subject:</span>
                                    <x-anchor to="{{ route('academic-subjects.show', ['academic_subject' => $multipleChoiceQuestion->academicTopic->academicSubject, 'academic_level' => getRouteParameter('academic_level'), 'academic_group' => getRouteParameter('academic_group')]) }}"
                                              class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300 font-medium inline-flex items-center">
                                        {{ $multipleChoiceQuestion->academicTopic->academicSubject->name }}
                                        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                        </svg>
                                    </x-anchor>
                                </div>
                                <div class="flex items-center text-sm">
                                    <span class="text-gray-600 dark:text-gray-400 w-20">Topic:</span>
                                    <x-anchor to="{{ route('academic-topics.show', ['academic_topic' => $multipleChoiceQuestion->academicTopic, 'academic_subject' => getRouteParameter('academic_subject'), 'academic_level' => getRouteParameter('academic_level'), 'academic_group' => getRouteParameter('academic_group')]) }}"
                                              class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300 font-medium inline-flex items-center">
                                        {{ $multipleChoiceQuestion->academicTopic->name }}
                                        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                        </svg>
                                    </x-anchor>
                                </div>
                                @if(isset($multipleChoiceQuestion->subtopic))
                                    <div class="flex items-center text-sm">
                                        <span class="text-gray-600 dark:text-gray-400 w-20">Subtopic:</span>
                                        <span class="text-gray-700 dark:text-gray-300 font-medium">
                                            {{ $multipleChoiceQuestion->subtopic->name }}
                                        </span>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="bg-gradient-to-br from-gray-50 to-indigo-50 dark:from-gray-700 dark:to-gray-800 rounded-xl p-5 border border-gray-200 dark:border-gray-600 shadow-sm">
                            <h3 class="text-base font-medium text-gray-700 dark:text-gray-300 mb-4">Question Statistics</h3>
                            <div class="space-y-3">
                                <div class="flex items-center text-sm">
                                    <span class="text-gray-600 dark:text-gray-400 w-24">Options:</span>
                                    <span class="text-gray-700 dark:text-gray-300 font-medium">
                                        {{ collect(['a', 'b', 'c', 'd', 'e'])->filter(fn($opt) => $multipleChoiceQuestion->{"option_$opt"}->html)->count() }} options
                                    </span>
                                </div>
                                <div class="flex items-center text-sm">
                                    <span class="text-gray-600 dark:text-gray-400 w-24">Created:</span>
                                    <span class="text-gray-700 dark:text-gray-300">
                                        {{ $multipleChoiceQuestion->created_at->format('M j, Y g:i A') }}
                                    </span>
                                </div>
                                @if($multipleChoiceQuestion->updated_at->ne($multipleChoiceQuestion->created_at))
                                    <div class="flex items-center text-sm">
                                        <span class="text-gray-600 dark:text-gray-400 w-24">Updated:</span>
                                        <span class="text-gray-700 dark:text-gray-300">
                                            {{ $multipleChoiceQuestion->updated_at->format('M j, Y g:i A') }}
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
