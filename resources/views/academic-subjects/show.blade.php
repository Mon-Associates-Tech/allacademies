<x-layouts.app title="Academic Subject Details" :has-action="false">
    <x-slot name="breadcrumb">
        <x-breadcrumb :paths="[
                    'Academic Groups' => route('academic-groups.index'),
                    $academicSubject->academicLevel->academicGroup->name => route('academic-groups.show', ['academic_group' => $academicSubject->academicLevel->academicGroup]),
                    'Academic Levels' => route('academic-levels.index', ['academic_group' => $academicSubject->academicLevel->academicGroup]),
                    $academicSubject->academicLevel->name => route('academic-levels.show', ['academic_level' => $academicSubject->academicLevel, 'academic_group' => $academicSubject->academicLevel->academicGroup]),
                    'Academic Subjects' => route('academic-subjects.index', ['academic_level' => $academicSubject->academicLevel, 'academic_group' => $academicSubject->academicLevel->academicGroup]),
                    $academicSubject->name => null,
                ]"/>
    </x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
        <div
            class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">

            <x-academic-header>
                <x-slot name="headerContent">
                    <div class="flex items-center space-x-4">
                        <div>
                            <h1 class="text-xl sm:text-2xl font-bold text-gray-800 dark:text-white">{{ $academicSubject->name }}</h1>
                            <p class="text-gray-600 dark:text-gray-400 text-sm mt-1">
                                <span class="font-medium">{{ $academicSubject->code }}</span>
                            </p>
                        </div>
                    </div>
                </x-slot>

                @can('administrate')
                    <x-slot name="headerActions">
                        <div class="flex flex-col sm:flex-row gap-2">
                            <x-button.white>
                                <a href="{{ route('academic-topics.create', ['academic_subject' => $academicSubject, 'academic_level' => $academicSubject->academicLevel, 'academic_group' => $academicSubject->academicLevel->academicGroup]) }}"
                                   class="inline-flex text-sm items-center justify-center ">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor"
                                         viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                    </svg>
                                    Add Topic
                                </a>
                            </x-button.white>

                            <a href="{{ route('academic-subjects.edit', ['academic_subject' => $academicSubject, 'academic_level' => $academicSubject->academicLevel, 'academic_group' => $academicSubject->academicLevel->academicGroup]) }}"
                               class="inline-flex items-center px-3 py-2 text-sm font-medium rounded-lg bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors duration-150">
                                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                                Edit
                            </a>
                            <button type="button"
                                    class="inline-flex items-center px-3 py-2 text-sm font-medium rounded-lg text-white bg-red-600 hover:bg-red-700 transition-colors duration-150"
                                    x-data="{}"
                                    x-on:click="$store.deleteForm.show(
                                'Delete Subject',
                                'Are you sure you want to delete {{ $academicSubject->name }}? This action cannot be undone.',
                                '{{ route('academic-subjects.destroy', ['academic_subject' => $academicSubject, 'academic_level' => $academicSubject->academicLevel, 'academic_group' => $academicSubject->academicLevel->academicGroup]) }}'
                            )">
                                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                                Delete
                            </button>
                        </div>
                    </x-slot>
                @endcan
            </x-academic-header>

            <!-- Subject Stats -->
            <div class="px-6 py-6">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div
                        class="bg-gradient-to-br from-blue-50 to-indigo-50 dark:from-gray-700 dark:to-gray-800 rounded-xl p-5 border border-gray-200 dark:border-gray-600 shadow-sm">
                        <div class="flex items-center">
                            <div class="p-2 bg-blue-100 dark:bg-blue-900/30 rounded-lg">
                                <svg class="w-5 h-5 sm:w-6 sm:h-6 text-blue-600 dark:text-blue-400" fill="none"
                                     stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Topics</p>
                                <p class="text-2xl font-bold text-gray-900 dark:text-white">{{ $topics->total() }}</p>
                            </div>
                        </div>
                    </div>

                    <div
                        class="bg-gradient-to-br from-green-50 to-emerald-50 dark:from-gray-700 dark:to-gray-800 rounded-xl p-5 border border-gray-200 dark:border-gray-600 shadow-sm">
                        <div class="flex items-center">
                            <div class="p-2 bg-green-100 dark:bg-green-900/30 rounded-lg">
                                <svg class="w-5 h-5 sm:w-6 sm:h-6 text-green-600 dark:text-green-400" fill="none"
                                     stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Level</p>
                                <p class="text-lg font-bold text-gray-900 dark:text-white truncate">{{ $academicSubject->academicLevel->name }}</p>
                            </div>
                        </div>
                    </div>

                    <div
                        class="bg-gradient-to-br from-purple-50 to-violet-50 dark:from-gray-700 dark:to-gray-800 rounded-xl p-5 border border-gray-200 dark:border-gray-600 shadow-sm">
                        <div class="flex items-center">
                            <div class="p-2 bg-purple-100 dark:bg-purple-900/30 rounded-lg">
                                <svg class="w-5 h-5 sm:w-6 sm:h-6 text-purple-600 dark:text-purple-400" fill="none"
                                     stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                </svg>
                            </div>
                            <div class="ml-4 relative overflow-hidden">
                                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Group</p>
                                <p class="text-lg font-bold text-gray-900 dark:text-white truncate">{{ $academicSubject->academicLevel->academicGroup->name }}</p>
                            </div>
                        </div>
                    </div>

                    <div
                        class="bg-gradient-to-br from-orange-50 to-amber-50 dark:from-gray-700 dark:to-gray-800 rounded-xl p-5 border border-gray-200 dark:border-gray-600 shadow-sm">
                        <div class="flex items-center">
                            <div class="p-2 bg-orange-100 dark:bg-orange-900/30 rounded-lg">
                                <svg class="w-5 h-5 sm:w-6 sm:h-6 text-orange-600 dark:text-orange-400" fill="none"
                                     stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"></path>
                                </svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Status</p>
                                <p class="text-lg font-bold text-indigo-600 dark:text-indigo-400">Active</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Topics List -->
            @if($topics->count() > 0)
                <div class="border-t border-gray-200 dark:border-gray-700">
                    <div class="divide-y divide-gray-200 dark:divide-gray-700">
                        @foreach($topics as $topic)
                            <div
                                class="px-6 py-4 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors duration-150">
                                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                                    <div class="flex items-start space-x-4">
                                        <div class="flex-shrink-0 mt-1">
                                            <div
                                                class="w-10 h-10 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-xl flex items-center justify-center">
                                                <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor"
                                                     viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                          stroke-width="2"
                                                          d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                                </svg>
                                            </div>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <h3 class="text-base font-semibold text-gray-900 dark:text-white truncate">{{ $topic->name }}</h3>
                                            <div class="flex items-center space-x-4 mt-1">
                                                @if($topic->subtopics_count > 0)
                                                    <span
                                                        class="inline-flex items-center text-sm text-gray-500 dark:text-gray-400">
                                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor"
                                                             viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                  stroke-width="2"
                                                                  d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                                                        </svg>
                                                        {{ $topic->subtopics_count }} {{ Str::plural('subtopic', $topic->subtopics_count) }}
                                                    </span>
                                                @else
                                                    <span
                                                        class="inline-flex items-center text-sm text-gray-400 dark:text-gray-500">
                                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor"
                                                             viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                  stroke-width="2"
                                                                  d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path>
                                                        </svg>
                                                        No subtopics
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    <div class="flex items-center space-x-2 mt-3 sm:mt-0">
                                        <a href="{{ route('academic-topics.show', ['academic_topic' => $topic, 'academic_subject' => $academicSubject, 'academic_level' => $academicSubject->academicLevel, 'academic_group' => $academicSubject->academicLevel->academicGroup]) }}"
                                           class="inline-flex items-center px-3 py-1.5 bg-indigo-600 dark:bg-indigo-500 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 dark:hover:bg-indigo-600 transition-colors duration-150">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor"
                                                 viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                            View
                                        </a>

                                        @can('administrate')
                                            <a href="{{ route('academic-topics.edit', ['academic_topic' => $topic, 'academic_subject' => $academicSubject, 'academic_level' => $academicSubject->academicLevel, 'academic_group' => $academicSubject->academicLevel->academicGroup]) }}"
                                               class="inline-flex items-center p-1.5 text-gray-400 hover:text-gray-600 dark:text-gray-500 dark:hover:text-gray-300 transition-colors duration-150"
                                               title="Edit topic">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                     viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                          stroke-width="2"
                                                          d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                </svg>
                                            </a>
                                            <button type="button"
                                                    class="inline-flex items-center p-1.5 text-red-400 hover:text-red-600 dark:text-red-500 dark:hover:text-red-400 transition-colors duration-150"
                                                    title="Delete topic"
                                                    x-data="{}"
                                                    x-on:click="$store.deleteForm.show(
                                                    'Delete Topic',
                                                    'Are you sure you want to delete {{ $topic->name }}? This action cannot be undone.',
                                                    '{{ route('academic-topics.destroy', ['academic_topic' => $topic, 'academic_subject' => $academicSubject, 'academic_level' => $academicSubject->academicLevel, 'academic_group' => $academicSubject->academicLevel->academicGroup]) }}'
                                                )">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                     viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                          stroke-width="2"
                                                          d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                </svg>
                                            </button>
                                        @endcan
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Pagination -->
                    @if($topics->hasPages())
                        <div
                            class="px-6 py-4 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800/50">
                            {{ $topics->appends(request()->query())->links() }}
                        </div>
                    @endif
                </div>
            @else
                <!-- Empty State -->
                <div class="border-t border-gray-200 dark:border-gray-700 p-8 text-center">
                    <div
                        class="mx-auto w-24 h-24 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mb-4">
                        <svg class="w-12 h-12 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor"
                             viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">No topics yet</h3>
                    <p class="text-gray-600 dark:text-gray-400 mb-6 max-w-md mx-auto">
                        Start building your curriculum by adding topics to this subject. Topics help organize your
                        content
                        and make it easier for students to learn.
                    </p>
                    @can('administrate')
                        <a href="{{ route('academic-topics.create', ['academic_subject' => $academicSubject, 'academic_level' => $academicSubject->academicLevel, 'academic_group' => $academicSubject->academicLevel->academicGroup]) }}"
                           class="inline-flex items-center px-6 py-3 bg-indigo-600 dark:bg-indigo-500 text-white font-medium rounded-lg hover:bg-indigo-700 dark:hover:bg-indigo-600 transition-colors duration-150">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            Add Your First Topic
                        </a>
                    @endcan
                </div>
            @endif
        </div>
    </div>
</x-layouts.app>
