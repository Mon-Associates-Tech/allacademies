<x-layouts.app title="Multiple Choice Question Details" :has-action="false">
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
        <!-- Question Header -->
        <div
            class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6 mb-6">
            <div class="flex items-center justify-between">
                <div>
                    <h1 class="text-xl md:text-2xl font-bold text-gray-900 dark:text-white">
                        Multiple Choice Question
                    </h1>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                        ID: {{ $multipleChoiceQuestion->id }}
                    </p>
                </div>
                @can('moderate')
                    <div class="flex space-x-2">
                        <x-button.secondary
                            type="button"
                            x-data="{}"
                            x-on:click="$store.deleteForm.show(
                                'Delete Question',
                                'Are you sure you want to delete this question?',
                                '{{ route('multiple-choice-questions.destroy', ['multiple_choice_question' => $multipleChoiceQuestion, 'academic_subject' => getRouteParameter('academic_subject'), 'academic_topic' => getRouteParameter('academic_topic'), 'academic_level' => getRouteParameter('academic_level'), 'academic_group' => getRouteParameter('academic_group')]) }}'
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
                            :to="route('multiple-choice-questions.edit', ['multiple_choice_question' => $multipleChoiceQuestion, 'academic_subject' => getRouteParameter('academic_subject'), 'academic_topic' => getRouteParameter('academic_topic'), 'academic_level' => getRouteParameter('academic_level'), 'academic_group' => getRouteParameter('academic_group')])"
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
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
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
                            {{ $multipleChoiceQuestion->score ?? 'Not set' }}
                            @if($multipleChoiceQuestion->score)
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
                            $difficultyColor = match($multipleChoiceQuestion->difficulty_level) {
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
                            {{ $multipleChoiceQuestion->difficulty_level ?? 'Not set' }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- Correct Answer -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-4">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 rounded-full bg-green-100 dark:bg-green-900 flex items-center justify-center">
                            <span class="text-green-700 dark:text-green-300 font-bold text-sm">
                                {{ strtoupper($multipleChoiceQuestion->answer) }}
                            </span>
                        </div>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-gray-500 dark:text-gray-400">Correct Answer</p>
                        <p class="text-lg font-semibold text-gray-900 dark:text-white">
                            Option {{ strtoupper($multipleChoiceQuestion->answer) }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Question Content -->
        <div
            class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
            <!-- Question Text -->
            <div class="p-6 border-b border-gray-200 dark:border-gray-700">
                <h2 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Question</h2>
                <div class="prose dark:prose-invert max-w-none">
                    <span x-html="marked.parse(@js($multipleChoiceQuestion->question->down))"></span>
                </div>
            </div>

            <!-- Options -->
            <div class="divide-y divide-gray-200 dark:divide-gray-700">
                @foreach(['a', 'b', 'c', 'd', 'e'] as $option)
                    @if($multipleChoiceQuestion->{"option_$option"}->html)
                        <div class="px-6 py- my-auto hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors duration-150
                          {{ strtoupper($multipleChoiceQuestion->answer) === strtoupper($option) ? 'bg-green-50 dark:bg-green-900/20' : '' }}">
                            <div class="flex items-start space-x-4 my-auto">
                                <div class="flex-shrink-0 flex my-auto">
                                  <span class="inline-flex items-center justify-center w-8 h-8 rounded-full
                                      {{ strtoupper($multipleChoiceQuestion->answer) === strtoupper($option)
                                          ? 'bg-green-100 dark:bg-green-900 text-green-700 dark:text-green-300'
                                          : 'bg-gray-100 dark:bg-gray-800 text-gray-700 dark:text-gray-300'
                                      }}">
                                      {{ strtoupper($option) }}
                                  </span>
                                    @if(strtoupper($multipleChoiceQuestion->answer) === strtoupper($option))
                                        <div class="flex items-center my-auto text-sm text-green-600 dark:text-green-400 mb-2">
                                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor"
                                                 viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                            </svg>
                                        </div>
                                    @endif
                                </div>
                                <div class="flex-1 min-w-0">
                                    @php
                                        $optText = $multipleChoiceQuestion->{"option_$option"}->down;
                                    @endphp
                                    <div class="prose dark:prose-invert max-w-none">
                                        <span x-html="marked.parse(@js($optText))"></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>

            <!-- Academic Information -->
            <div class="bg-gray-50 dark:bg-gray-700/50 px-6 py-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Academic Hierarchy</h3>
                        <div class="space-y-2">
                            <div class="flex items-center text-sm">
                                <span class="text-gray-600 dark:text-gray-400 w-16">Group:</span>
                                <x-anchor
                                    to="{{ route('academic-groups.show', ['academic_group' => $multipleChoiceQuestion->academicTopic->academicSubject->academicLevel->academicGroup]) }}"
                                    class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 font-medium"
                                >
                                    {{ $multipleChoiceQuestion->academicTopic->academicSubject->academicLevel->academicGroup->name }}
                                </x-anchor>
                            </div>
                            <div class="flex items-center text-sm">
                                <span class="text-gray-600 dark:text-gray-400 w-16">Level:</span>
                                <x-anchor
                                    to="{{ route('academic-levels.show', ['academic_level' => $multipleChoiceQuestion->academicTopic->academicSubject->academicLevel, 'academic_group' => getRouteParameter('academic_group')]) }}"
                                    class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 font-medium"
                                >
                                    {{ $multipleChoiceQuestion->academicTopic->academicSubject->academicLevel->name }}
                                </x-anchor>
                            </div>
                            <div class="flex items-center text-sm">
                                <span class="text-gray-600 dark:text-gray-400 w-16">Subject:</span>
                                <x-anchor
                                    to="{{ route('academic-subjects.show', ['academic_subject' => $multipleChoiceQuestion->academicTopic->academicSubject, 'academic_level' => getRouteParameter('academic_level'), 'academic_group' => getRouteParameter('academic_group')]) }}"
                                    class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 font-medium"
                                >
                                    {{ $multipleChoiceQuestion->academicTopic->academicSubject->name }}
                                </x-anchor>
                            </div>
                            <div class="flex items-center text-sm">
                                <span class="text-gray-600 dark:text-gray-400 w-16">Topic:</span>
                                <x-anchor
                                    to="{{ route('academic-topics.show', ['academic_topic' => $multipleChoiceQuestion->academicTopic, 'academic_subject' => getRouteParameter('academic_subject'), 'academic_level' => getRouteParameter('academic_level'), 'academic_group' => getRouteParameter('academic_group')]) }}"
                                    class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 font-medium"
                                >
                                    {{ $multipleChoiceQuestion->academicTopic->name }}
                                </x-anchor>
                            </div>
                            @if(isset($multipleChoiceQuestion->subtopic))
                                <div class="flex items-center text-sm">
                                    <span class="text-gray-600 dark:text-gray-400 w-16">Subtopic:</span>
                                    <span class="text-gray-700 dark:text-gray-300 font-medium">
                                        {{ $multipleChoiceQuestion->subtopic->name }}
                                    </span>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div>
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Question Statistics</h3>
                        <div class="space-y-2">
                            <div class="flex items-center text-sm">
                                <span class="text-gray-600 dark:text-gray-400 w-20">Options:</span>
                                <span class="text-gray-700 dark:text-gray-300">
                                    {{ collect(['a', 'b', 'c', 'd', 'e'])->filter(fn($opt) => $multipleChoiceQuestion->{"option_$opt"}->html)->count() }} options
                                </span>
                            </div>
                            <div class="flex items-center text-sm">
                                <span class="text-gray-600 dark:text-gray-400 w-20">Created:</span>
                                <span class="text-gray-700 dark:text-gray-300">
                                    {{ $multipleChoiceQuestion->created_at->format('M j, Y g:i A') }}
                                </span>
                            </div>
                            @if($multipleChoiceQuestion->updated_at->ne($multipleChoiceQuestion->created_at))
                                <div class="flex items-center text-sm">
                                    <span class="text-gray-600 dark:text-gray-400 w-20">Updated:</span>
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

        <!-- Mobile Action Buttons -->
        @can('moderate')
            <div
                class="fixed bottom-0 left-0 right-0 bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700 p-4 sm:hidden">
                <div class="flex justify-between gap-4">
                    <x-button.secondary
                        type="button"
                        x-data="{}"
                        x-on:click="$store.deleteForm.show('Delete Question', 'Are you sure?', '{{ route('multiple-choice-questions.destroy', ['multiple_choice_question' => $multipleChoiceQuestion, 'academic_subject' => getRouteParameter('academic_subject'), 'academic_topic' => getRouteParameter('academic_topic'), 'academic_level' => getRouteParameter('academic_level'), 'academic_group' => getRouteParameter('academic_group')]) }}')"
                        class="flex-1"
                    >
                        Delete
                    </x-button.secondary>
                    <x-link.primary
                        :to="route('multiple-choice-questions.edit', ['multiple_choice_question' => $multipleChoiceQuestion, 'academic_subject' => getRouteParameter('academic_subject'), 'academic_topic' => getRouteParameter('academic_topic'), 'academic_level' => getRouteParameter('academic_level'), 'academic_group' => getRouteParameter('academic_group')])"
                        class="flex-1"
                    >
                        Edit
                    </x-link.primary>
                </div>
            </div>
        @endcan
    </div>
</x-layouts.app>
