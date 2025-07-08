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
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden transition-colors duration-200">
            <!-- Hero Section -->
            <div class="bg-gradient-to-r from-indigo-500 to-purple-600 px-4 sm:px-6 py-6 sm:py-8">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div class="flex items-center space-x-4">
                        <div class="p-3 bg-white/20 backdrop-blur-sm rounded-xl shadow-lg">
                            <svg class="w-7 h-7 sm:w-8 sm:h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                            </svg>
                        </div>
                        <div>
                            <h1 class="text-2xl sm:text-3xl font-bold text-white">{{ $academicTopic->name }}</h1>
                            <div class="flex items-center mt-2 text-indigo-100">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                </svg>
                                <span class="text-sm sm:text-base">Part of {{ $academicTopic->academicSubject->name }}</span>
                            </div>
                        </div>
                    </div>

                    @can('administrate')
                        <div class="flex flex-col sm:flex-row space-y-2 sm:space-y-0 sm:space-x-3">
                            <button type="button"
                                    class="inline-flex items-center justify-center px-4 py-2 border border-white/30 rounded-lg text-sm font-medium text-white bg-white/10 hover:bg-white/20 backdrop-blur-sm transition-all duration-200 hover:scale-105"
                                    x-data="{}"
                                    x-on:click="$store.deleteForm.show('Danger', 'Are you sure you want to delete {{ $academicTopic->name }}?', '{{ route('academic-topics.destroy', ['academic_topic' => $academicTopic, 'academic_subject' => getRouteParameter('academic_subject'), 'academic_level' => getRouteParameter('academic_level'), 'academic_group' => getRouteParameter('academic_group')]) }}')">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                                <span class="hidden sm:inline">Delete</span>
                            </button>
                            <x-link.primary :to="route('academic-topics.edit', ['academic_topic' => $academicTopic, 'academic_subject' => getRouteParameter('academic_subject'), 'academic_level' => getRouteParameter('academic_level'), 'academic_group' => getRouteParameter('academic_group')])"
                                            class="inline-flex items-center justify-center px-4 py-2 border border-white/30 rounded-lg text-sm font-medium text-white bg-white/10 hover:bg-white/20 backdrop-blur-sm transition-all duration-200 hover:scale-105">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                                <span class="hidden sm:inline">Edit Topic</span>
                                <span class="sm:hidden">Edit</span>
                            </x-link.primary>
                        </div>
                    @endcan
                </div>
            </div>

            <!-- Topic Info Bar -->
            <div class="px-4 sm:px-6 py-4 bg-gray-50 dark:bg-gray-700/50 border-t border-gray-200 dark:border-gray-600">
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-center">
                    <div>
                        <div class="text-lg sm:text-xl font-bold text-gray-900 dark:text-white">{{ $academicTopic->academicSubject->academicLevel->academicGroup->name }}</div>
                        <div class="text-xs sm:text-sm text-gray-500 dark:text-gray-400">Academic Group</div>
                    </div>
                    <div>
                        <div class="text-lg sm:text-xl font-bold text-gray-900 dark:text-white">{{ $academicTopic->academicSubject->academicLevel->name }}</div>
                        <div class="text-xs sm:text-sm text-gray-500 dark:text-gray-400">Academic Level</div>
                    </div>
                    <div>
                        <div class="text-lg sm:text-xl font-bold text-indigo-600 dark:text-indigo-400">Active</div>
                        <div class="text-xs sm:text-sm text-gray-500 dark:text-gray-400">Status</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        @can('moderate')
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700">
                <div class="px-4 sm:px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                        <div>
                            <h2 class="text-lg sm:text-xl font-semibold text-gray-900 dark:text-white">Quick Actions</h2>
                            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">Manage this topic efficiently</p>
                        </div>
                        @can('administrate')
                            <div class="flex flex-wrap gap-2 sm:gap-3">
                                <x-link.primary :to="route('subtopics.create', ['academic_topic' => $academicTopic, 'academic_subject' => getRouteParameter('academic_subject'), 'academic_level' => getRouteParameter('academic_level'), 'academic_group' => getRouteParameter('academic_group')])"
                                                class="inline-flex items-center px-3 sm:px-4 py-2 text-sm font-medium rounded-lg transition-all duration-200 hover:scale-105">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                    </svg>
                                    <span class="hidden sm:inline">Add Subtopic</span>
                                    <span class="sm:hidden">Subtopic</span>
                                </x-link.primary>
                            </div>
                        @endcan
                    </div>
                </div>

                <!-- Action Grid -->
                <div class="p-4 sm:p-6">
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                        <!-- Multiple Choice Questions -->
                        <div class="flex items-center p-4 bg-blue-50 dark:bg-blue-900/20 rounded-xl hover:bg-blue-100 dark:hover:bg-blue-900/30 transition-colors duration-200 group cursor-pointer"
                             onclick="window.location.href='{{ route('multiple-choice-questions.index', ['academic_topic' => $academicTopic, 'academic_subject' => getRouteParameter('academic_subject'), 'academic_level' => getRouteParameter('academic_level'), 'academic_group' => getRouteParameter('academic_group')]) }}'">
                            <div class="p-2 bg-blue-500 rounded-lg mr-3">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                </svg>
                            </div>
                            <div class="flex-1">
                                <h3 class="text-sm font-medium text-blue-900 dark:text-blue-100 group-hover:text-blue-700 dark:group-hover:text-blue-200">MCQ Questions</h3>
                                <p class="text-xs text-blue-600 dark:text-blue-400">{{ $academicTopic->multiple_choice_questions_count }} questions</p>
                            </div>
                            <svg class="w-4 h-4 text-blue-600 dark:text-blue-400 group-hover:translate-x-1 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </div>

                        <!-- True/False Questions -->
                        <div class="flex items-center p-4 bg-green-50 dark:bg-green-900/20 rounded-xl hover:bg-green-100 dark:hover:bg-green-900/30 transition-colors duration-200 group cursor-pointer"
                             onclick="window.location.href='{{ route('true-or-false-questions.index', ['academic_topic' => $academicTopic, 'academic_subject' => getRouteParameter('academic_subject'), 'academic_level' => getRouteParameter('academic_level'), 'academic_group' => getRouteParameter('academic_group')]) }}'">
                            <div class="p-2 bg-green-500 rounded-lg mr-3">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                            </div>
                            <div class="flex-1">
                                <h3 class="text-sm font-medium text-green-900 dark:text-green-100 group-hover:text-green-700 dark:group-hover:text-green-200">True/False</h3>
                                <p class="text-xs text-green-600 dark:text-green-400">{{ $academicTopic->true_or_false_questions_count }} questions</p>
                            </div>
                            <svg class="w-4 h-4 text-green-600 dark:text-green-400 group-hover:translate-x-1 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </div>

                        <!-- Essay Questions -->
                        <div class="flex items-center p-4 bg-purple-50 dark:bg-purple-900/20 rounded-xl hover:bg-purple-100 dark:hover:bg-purple-900/30 transition-colors duration-200 group cursor-pointer"
                             onclick="window.location.href='{{ route('essay-questions.index', ['academic_topic' => $academicTopic, 'academic_subject' => getRouteParameter('academic_subject'), 'academic_level' => getRouteParameter('academic_level'), 'academic_group' => getRouteParameter('academic_group')]) }}'">
                            <div class="p-2 bg-purple-500 rounded-lg mr-3">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                            </div>
                            <div class="flex-1">
                                <h3 class="text-sm font-medium text-purple-900 dark:text-purple-100 group-hover:text-purple-700 dark:group-hover:text-purple-200">Essay Questions</h3>
                                <p class="text-xs text-purple-600 dark:text-purple-400">{{ $academicTopic->essay_questions_count }} questions</p>
                            </div>
                            <svg class="w-4 h-4 text-purple-600 dark:text-purple-400 group-hover:translate-x-1 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </div>

                        <!-- Subtopics -->
                        <div class="flex items-center p-4 bg-orange-50 dark:bg-orange-900/20 rounded-xl hover:bg-orange-100 dark:hover:bg-orange-900/30 transition-colors duration-200 group cursor-pointer"
                             onclick="window.location.href='{{ route('subtopics.index', ['academic_topic' => $academicTopic, 'academic_subject' => getRouteParameter('academic_subject'), 'academic_level' => getRouteParameter('academic_level'), 'academic_group' => getRouteParameter('academic_group')]) }}'">
                            <div class="p-2 bg-orange-500 rounded-lg mr-3">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path>
                                </svg>
                            </div>
                            <div class="flex-1">
                                <h3 class="text-sm font-medium text-orange-900 dark:text-orange-100 group-hover:text-orange-700 dark:group-hover:text-orange-200">Subtopics</h3>
                                <p class="text-xs text-orange-600 dark:text-orange-400">Manage subtopics</p>
                            </div>
                            <svg class="w-4 h-4 text-orange-600 dark:text-orange-400 group-hover:translate-x-1 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </div>

                        <!-- Lesson Notes -->
                        <div class="flex items-center p-4 bg-indigo-50 dark:bg-indigo-900/20 rounded-xl hover:bg-indigo-100 dark:hover:bg-indigo-900/30 transition-colors duration-200 group cursor-pointer"
                             onclick="window.location.href='{{ route('lesson-notes.index', ['academic_topic' => $academicTopic, 'academic_subject' => getRouteParameter('academic_subject'), 'academic_level' => getRouteParameter('academic_level'), 'academic_group' => getRouteParameter('academic_group')]) }}'">
                            <div class="p-2 bg-indigo-500 rounded-lg mr-3">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                </svg>
                            </div>
                            <div class="flex-1">
                                <h3 class="text-sm font-medium text-indigo-900 dark:text-indigo-100 group-hover:text-indigo-700 dark:group-hover:text-indigo-200">Lesson Notes</h3>
                                <p class="text-xs text-indigo-600 dark:text-indigo-400">Study materials</p>
                            </div>
                            <svg class="w-4 h-4 text-indigo-600 dark:text-indigo-400 group-hover:translate-x-1 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </div>

                        <!-- General Questions -->
                        <div class="flex items-center p-4 bg-gray-50 dark:bg-gray-700/50 rounded-xl hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors duration-200 group cursor-pointer"
                             onclick="window.location.href='{{ route('academic-subjects.index', ['academic_topic' => $academicTopic, 'academic_subject' => getRouteParameter('academic_subject'), 'academic_level' => getRouteParameter('academic_level'), 'academic_group' => getRouteParameter('academic_group')]) }}'">
                            <div class="p-2 bg-gray-500 rounded-lg mr-3">
                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div class="flex-1">
                                <h3 class="text-sm font-medium text-gray-900 dark:text-gray-100 group-hover:text-gray-700 dark:group-hover:text-gray-200">All Questions</h3>
                                <p class="text-xs text-gray-600 dark:text-gray-400">Manage all questions</p>
                            </div>
                            <svg class="w-4 h-4 text-gray-600 dark:text-gray-400 group-hover:translate-x-1 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
        @endcan
    </div>
</x-layouts.app>
