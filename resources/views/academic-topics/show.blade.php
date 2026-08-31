<x-layouts.app title="{{ $academicTopic->name }} Topic Details" :show-title-area="false" action-link-text="Add Subtopic"
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
        <!-- Unified container for header and content -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
            <!-- Header Section using academic-header component -->
            <x-academic-header>
                <x-slot name="headerContent">
                    <div class="flex items-center space-x-4">
                        <div>
                            <h1 class="text-xl sm:text-2xl font-bold text-gray-800 dark:text-white">{{ $academicTopic->name }}</h1>
                            <p class="text-gray-600 dark:text-gray-400 text-sm mt-1">
                                Part of {{ $academicTopic->academicSubject->name }}
                            </p>
                        </div>
                    </div>
                </x-slot>

                @can('administrate')
                    <x-slot name="headerActions">
                        <div class="flex flex-col sm:flex-row gap-2">
                            @can('administrate')
                                <x-link.primary :to="route('subtopics.create', ['academic_topic' => $academicTopic, 'academic_subject' => getRouteParameter('academic_subject'), 'academic_level' => getRouteParameter('academic_level'), 'academic_group' => getRouteParameter('academic_group')])"
                                                class="inline-flex items-center px-4 py-2 text-sm font-medium rounded-lg transition-all duration-200 hover:scale-105">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                    </svg>
                                    Add Subtopic
                                </x-link.primary>
                                
                                <!-- Import Questions Button -->
                                <a href="{{ route('questions.import.form', [
                                    'academic_topic' => $academicTopic,
                                    'academic_subject' => $academicTopic->academicSubject,
                                    'academic_level' => $academicTopic->academicSubject->academicLevel,
                                    'academic_group' => $academicTopic->academicSubject->academicLevel->academicGroup
                                ]) }}" 
                                   class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:border-indigo-900 focus:ring ring-indigo-300 disabled:opacity-25 transition ease-in-out duration-150 ml-2">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                                    </svg>
                                    Import Questions
                                </a>
                                
                                <!-- Import Questions for Subject Button -->
                                <a href="{{ route('questions.subject.import.form', [
                                    'academic_subject' => $academicTopic->academicSubject,
                                    'academic_level' => $academicTopic->academicSubject->academicLevel,
                                    'academic_group' => $academicTopic->academicSubject->academicLevel->academicGroup
                                ]) }}" 
                                   class="inline-flex hidden items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 active:bg-green-900 focus:outline-none focus:border-green-900 focus:ring ring-green-300 disabled:opacity-25 transition ease-in-out duration-150 ml-2">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 0115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                                    </svg>
                                    Import Subject Questions
                                </a>
                            @endcan
                            <x-link.secondary :to="route('academic-topics.edit', ['academic_topic' => $academicTopic, 'academic_subject' => getRouteParameter('academic_subject'), 'academic_level' => getRouteParameter('academic_level'), 'academic_group' => getRouteParameter('academic_group')])"
                                            class="inline-flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-all duration-200 hover:scale-105">
                                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                                Edit
                            </x-link.secondary>
                            <button type="button"
                                    class="inline-flex items-center px-3 py-2 text-sm font-medium rounded-lg text-white bg-red-600 hover:bg-red-700 transition-all duration-200 hover:scale-105"
                                    x-data="{}"
                                    x-on:click="$store.deleteForm.show('Delete Topic', 'Are you sure you want to delete {{ $academicTopic->name }}?', '{{ route('academic-topics.destroy', ['academic_topic' => $academicTopic, 'academic_subject' => getRouteParameter('academic_subject'), 'academic_level' => getRouteParameter('academic_level'), 'academic_group' => getRouteParameter('academic_group')]) }}')">
                                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                                Delete
                            </button>
                        </div>
                    </x-slot>
                @endcan
            </x-academic-header>

            <!-- Topic Info Bar -->
            <div class="px-6 py-4 bg-gray-50 dark:bg-gray-700/50 border-t border-gray-200 dark:border-gray-600">
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div class="bg-gradient-to-br from-blue-50 to-indigo-50 dark:from-gray-700 dark:to-gray-800 rounded-xl p-4 border border-gray-200 dark:border-gray-600 shadow-sm">
                        <div class="flex items-center">
                            <div class="p-2 bg-blue-100 dark:bg-blue-900/30 rounded-lg">
                                <svg class="w-5 h-5 sm:w-6 sm:h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-xs sm:text-sm font-medium text-gray-600 dark:text-gray-400">Academic Group</p>
                                <p class="text-base sm:text-lg font-bold text-gray-900 dark:text-white truncate">{{ $academicTopic->academicSubject->academicLevel->academicGroup->name }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gradient-to-br from-green-50 to-emerald-50 dark:from-gray-700 dark:to-gray-800 rounded-xl p-4 border border-gray-200 dark:border-gray-600 shadow-sm">
                        <div class="flex items-center">
                            <div class="p-2 bg-green-100 dark:bg-green-900/30 rounded-lg">
                                <svg class="w-5 h-5 sm:w-6 sm:h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-xs sm:text-sm font-medium text-gray-600 dark:text-gray-400">Academic Level</p>
                                <p class="text-base sm:text-lg font-bold text-gray-900 dark:text-white truncate">{{ $academicTopic->academicSubject->academicLevel->name }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="bg-gradient-to-br from-orange-50 to-amber-50 dark:from-gray-700 dark:to-gray-800 rounded-xl p-4 border border-gray-200 dark:border-gray-600 shadow-sm">
                        <div class="flex items-center">
                            <div class="p-2 bg-orange-100 dark:bg-orange-900/30 rounded-lg">
                                <svg class="w-5 h-5 sm:w-6 sm:h-6 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-xs sm:text-sm font-medium text-gray-600 dark:text-gray-400">Status</p>
                                <p class="text-base sm:text-lg font-bold text-indigo-600 dark:text-indigo-400">Active</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Actions -->
            @can('moderate')
                <div class="bg-white dark:bg-gray-800 rounded-b-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                    <!-- Action Grid -->
                    <div class="p-6">
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                            <!-- Multiple Choice Questions -->
                            <div class="flex items-center p-4 bg-blue-50 dark:bg-blue-900/20 rounded-xl hover:bg-blue-100 dark:hover:bg-blue-900/30 transition-colors duration-200 group cursor-pointer border border-blue-100 dark:border-blue-800/50"
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
                            <div class="flex items-center p-4 bg-green-50 dark:bg-green-900/20 rounded-xl hover:bg-green-100 dark:hover:bg-green-900/30 transition-colors duration-200 group cursor-pointer border border-green-100 dark:border-green-800/50"
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
                            <div class="flex items-center p-4 bg-purple-50 dark:bg-purple-900/20 rounded-xl hover:bg-purple-100 dark:hover:bg-purple-900/30 transition-colors duration-200 group cursor-pointer border border-purple-100 dark:border-purple-800/50"
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
                            <div class="flex items-center p-4 bg-orange-50 dark:bg-orange-900/20 rounded-xl hover:bg-orange-100 dark:hover:bg-orange-900/30 transition-colors duration-200 group cursor-pointer border border-orange-100 dark:border-orange-800/50"
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
                            <div class="flex items-center p-4 bg-indigo-50 dark:bg-indigo-900/20 rounded-xl hover:bg-indigo-100 dark:hover:bg-indigo-900/30 transition-colors duration-200 group cursor-pointer border border-indigo-100 dark:border-indigo-800/50"
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

                            <!-- Import Questions -->
                            <div class="flex items-center p-4 bg-yellow-50 dark:bg-yellow-900/20 rounded-xl hover:bg-yellow-100 dark:hover:bg-yellow-900/30 transition-colors duration-200 group cursor-pointer border border-yellow-100 dark:border-yellow-800/50"
                                 onclick="window.location.href='{{ route('questions.import.form', [
                                    'academic_topic' => $academicTopic,
                                    'academic_subject' => $academicTopic->academicSubject,
                                    'academic_level' => $academicTopic->academicSubject->academicLevel,
                                    'academic_group' => $academicTopic->academicSubject->academicLevel->academicGroup
                                ]) }}'">
                                <div class="p-2 bg-yellow-500 rounded-lg mr-3">
                                    <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                                    </svg>
                                </div>
                                <div class="flex-1">
                                    <h3 class="text-sm font-medium text-yellow-900 dark:text-yellow-100 group-hover:text-yellow-700 dark:group-hover:text-yellow-200">Import Questions</h3>
                                    <p class="text-xs text-yellow-600 dark:text-yellow-400">Excel, Word, PDF</p>
                                </div>
                                <svg class="w-4 h-4 text-yellow-600 dark:text-yellow-400 group-hover:translate-x-1 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                </div>
            @endcan
        </div>


    </div>
</x-layouts.app>