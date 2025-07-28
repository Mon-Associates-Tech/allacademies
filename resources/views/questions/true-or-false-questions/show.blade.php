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

    <div class="max-w-5xl mx-auto grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Content -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Header Section -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <div class="flex items-center space-x-4">
                    <div class="p-3 bg-blue-100 dark:bg-blue-900 rounded-full">
                        <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="flex-1">
                        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">True/False Question</h1>
                        <p class="text-gray-600 dark:text-gray-400">Topic: {{ $trueOrFalseQuestion->academicTopic->name }}</p>
                    </div>
                    @can('moderate')
                        <div class="flex items-center space-x-2">
                            <x-link.secondary
                                :to="route('true-or-false-questions.edit', [
                                    'true_or_false_question' => $trueOrFalseQuestion,
                                    'academic_subject' => getRouteParameter('academic_subject'),
                                    'academic_topic' => getRouteParameter('academic_topic'),
                                    'academic_level' => getRouteParameter('academic_level'),
                                    'academic_group' => getRouteParameter('academic_group')
                                ])"
                                class="inline-flex items-center px-3 py-2 text-sm">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                                Edit
                            </x-link.secondary>
                        </div>
                    @endcan
                </div>
            </div>

            <!-- Question Content -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <div class="flex items-center space-x-2">
                        <svg class="w-5 h-5 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Question Statement</h2>
                    </div>
                </div>
                <div class="p-6">
                    <div class="prose dark:prose-invert max-w-none">
                        <x-form.markdown-with-math :content="$trueOrFalseQuestion->question->down" />
                    </div>
                </div>
            </div>

            <!-- Answer Section -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <div class="flex items-center space-x-2">
                        <svg class="w-5 h-5 text-gray-500 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Correct Answer</h2>
                    </div>
                </div>
                <div class="p-6">
                    <div class="flex items-center space-x-4">
                        <div class="flex items-center space-x-2">
                            @if($trueOrFalseQuestion->answer)
                                <div class="flex items-center space-x-2 text-green-700 dark:text-green-400">
                                    <div class="w-6 h-6 bg-green-100 dark:bg-green-900 rounded-full flex items-center justify-center">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                        </svg>
                                    </div>
                                    <span class="font-semibold text-lg">True</span>
                                </div>
                            @else
                                <div class="flex items-center space-x-2 text-red-700 dark:text-red-400">
                                    <div class="w-6 h-6 bg-red-100 dark:bg-red-900 rounded-full flex items-center justify-center">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                        </svg>
                                    </div>
                                    <span class="font-semibold text-lg">False</span>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            @can('moderate')
                <!-- Danger Zone -->
                <div class="bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-lg p-6">
                    <div class="flex items-start space-x-3">
                        <div class="flex-shrink-0">
                            <svg class="w-5 h-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.5 0L4.268 18.5c-.77.833.192 2.5 1.732 2.5z"></path>
                            </svg>
                        </div>
                        <div class="flex-1">
                            <h3 class="text-sm font-medium text-red-800 dark:text-red-200">Danger Zone</h3>
                            <p class="mt-1 text-sm text-red-700 dark:text-red-300">
                                Permanently delete this question. This action cannot be undone.
                            </p>
                            <div class="mt-4">
                                <x-button.secondary
                                    type="button"
                                    class="bg-red-100 text-red-800 hover:bg-red-200 dark:bg-red-900 dark:text-red-200 dark:hover:bg-red-800"
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
                                    )"
                                >
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                    Delete Question
                                </x-button.secondary>
                            </div>
                        </div>
                    </div>
                </div>
            @endcan
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Question Properties -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Question Properties</h3>
                </div>
                <div class="p-6 space-y-4">
                    <!-- Difficulty Level -->
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Difficulty Level</span>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                            @if($trueOrFalseQuestion->difficulty_level === 'easy') bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200
                            @elseif($trueOrFalseQuestion->difficulty_level === 'medium') bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-200
                            @elseif($trueOrFalseQuestion->difficulty_level === 'difficult') bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-200
                            @else bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300 @endif">
                            {{ ucfirst($trueOrFalseQuestion->difficulty_level) }}
                        </span>
                    </div>

                    <!-- Score Points -->
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Score Points</span>
                        <span class="inline-flex items-center space-x-1">
                            <svg class="w-4 h-4 text-yellow-500" fill="currentColor" viewBox="0 0 20 20">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                            </svg>
                            <span class="text-sm font-semibold text-gray-900 dark:text-white">{{ $trueOrFalseQuestion->score }}</span>
                        </span>
                    </div>

                    <!-- Question Type -->
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-medium text-gray-500 dark:text-gray-400">Question Type</span>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                            True/False
                        </span>
                    </div>
                </div>
            </div>

            <!-- Academic Context -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Academic Context</h3>
                </div>
                <div class="p-6 space-y-4">
                    <!-- Academic Group -->
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Academic Group</dt>
                        <dd class="mt-1">
                            <x-anchor
                                :to="route('academic-groups.show', ['academic_group' => $trueOrFalseQuestion->academicTopic->academicSubject->academicLevel->academicGroup])"
                                class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 font-medium">
                                {{ $trueOrFalseQuestion->academicTopic->academicSubject->academicLevel->academicGroup->name }}
                            </x-anchor>
                        </dd>
                    </div>

                    <!-- Academic Level -->
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Academic Level</dt>
                        <dd class="mt-1">
                            <x-anchor
                                :to="route('academic-levels.show', [
                                    'academic_level' => $trueOrFalseQuestion->academicTopic->academicSubject->academicLevel,
                                    'academic_group' => getRouteParameter('academic_group')
                                ])"
                                class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 font-medium">
                                {{ $trueOrFalseQuestion->academicTopic->academicSubject->academicLevel->name }}
                            </x-anchor>
                        </dd>
                    </div>

                    <!-- Academic Subject -->
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Academic Subject</dt>
                        <dd class="mt-1">
                            <x-anchor
                                :to="route('academic-subjects.show', [
                                    'academic_subject' => $trueOrFalseQuestion->academicTopic->academicSubject,
                                    'academic_level' => getRouteParameter('academic_level'),
                                    'academic_group' => getRouteParameter('academic_group')
                                ])"
                                class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 font-medium">
                                {{ $trueOrFalseQuestion->academicTopic->academicSubject->name }}
                            </x-anchor>
                        </dd>
                    </div>

                    <!-- Academic Topic -->
                    <div>
                        <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Academic Topic</dt>
                        <dd class="mt-1">
                            <x-anchor
                                :to="route('academic-topics.show', [
                                    'academic_topic' => $trueOrFalseQuestion->academicTopic,
                                    'academic_subject' => getRouteParameter('academic_subject'),
                                    'academic_level' => getRouteParameter('academic_level'),
                                    'academic_group' => getRouteParameter('academic_group')
                                ])"
                                class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 font-medium">
                                {{ $trueOrFalseQuestion->academicTopic->name }}
                            </x-anchor>
                        </dd>
                    </div>

                    <!-- Subtopic -->
                    @if($trueOrFalseQuestion->subtopic)
                        <div>
                            <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Subtopic</dt>
                            <dd class="mt-1">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800 dark:bg-purple-900 dark:text-purple-200">
                                    {{ $trueOrFalseQuestion->subtopic->name }}
                                </span>
                            </dd>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700">
                <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Quick Actions</h3>
                </div>
                <div class="p-6 space-y-3">
                    <x-link.secondary
                        :to="route('true-or-false-questions.index', [
                            'academic_topic' => $trueOrFalseQuestion->academicTopic,
                            'academic_subject' => getRouteParameter('academic_subject'),
                            'academic_level' => getRouteParameter('academic_level'),
                            'academic_group' => getRouteParameter('academic_group')
                        ])"
                        class="w-full justify-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path>
                        </svg>
                        View All Questions
                    </x-link.secondary>

                    <x-link.secondary
                        :to="route('true-or-false-questions.create', [
                            'academic_topic' => $trueOrFalseQuestion->academicTopic,
                            'academic_subject' => getRouteParameter('academic_subject'),
                            'academic_level' => getRouteParameter('academic_level'),
                            'academic_group' => getRouteParameter('academic_group')
                        ])"
                        class="w-full justify-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        Create New Question
                    </x-link.secondary>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
