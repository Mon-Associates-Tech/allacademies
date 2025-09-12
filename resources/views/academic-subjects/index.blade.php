<x-layouts.app title="Academic Subjects" page-name="Academic Subjects">
    <x-slot name="breadcrumb">
        <x-breadcrumb :paths="[
            'Academic Groups' => route('academic-groups.index'),
            $academicLevel->academicGroup->name => route('academic-groups.show', ['academic_group' => $academicLevel->academicGroup]),
            'Academic Levels' => route('academic-levels.index', ['academic_group' => $academicLevel->academicGroup]),
            $academicLevel->name => route('academic-levels.show', ['academic_level' => $academicLevel, 'academic_group' => Route::getCurrentRoute()->parameter('academic_group')]),
        ]" />
    </x-slot>

    <!-- Unified container for header and content -->
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
        <!-- Header Section using academic-header component -->
        <x-academic-header class="border-b border-gray-200 dark:border-gray-700">
            <x-slot name="headerContent">
                <div class="flex items-center space-x-4">
                    <div class="p-3 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-xl shadow-lg">
                        <svg class="w-6 h-6 sm:w-7 sm:h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-2xl lg:text-3xl font-bold text-gray-900 dark:text-white">Academic Subjects</h1>
                        <p class="text-gray-600 dark:text-gray-300 mt-1 text-sm sm:text-base">
                            <span class="font-medium">{{ $academicLevel->name }}</span>
                            <span class="text-gray-400 dark:text-gray-500">•</span>
                            <span class="text-indigo-600 dark:text-indigo-400">{{ $academicLevel->academicGroup->name }}</span>
                        </p>
                    </div>
                </div>
            </x-slot>

            @can('administrate')
                <x-slot name="headerActions">
                    <div class="flex flex-col sm:flex-row gap-3">
                        <x-link.primary :to="route('academic-subjects.create', ['academic_level' => $academicLevel, 'academic_group' => $academicLevel->academicGroup])"
                                        class="inline-flex items-center justify-center px-4 py-2.5 text-sm font-medium rounded-lg transition-all duration-200 hover:scale-105 focus:ring-4 focus:ring-indigo-200 dark:focus:ring-indigo-800">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                            </svg>
                            <span class="hidden sm:inline">New Academic Subject</span>
                            <span class="sm:hidden">New Subject</span>
                        </x-link.primary>
                    </div>
                </x-slot>
            @endcan
        </x-academic-header>

        @if ($academicSubjects->count())
            <!-- Stats Summary -->
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 p-4 sm:p-6">
                <div class="bg-gradient-to-br from-blue-50 to-indigo-50 dark:from-gray-700 dark:to-gray-800 rounded-xl p-4 sm:p-5 border border-gray-200 dark:border-gray-600 shadow-sm">
                    <div class="flex items-center">
                        <div class="p-2 bg-blue-100 dark:bg-blue-900/30 rounded-lg">
                            <svg class="w-5 h-5 sm:w-6 sm:h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                            </svg>
                        </div>
                        <div class="ml-3 sm:ml-4">
                            <p class="text-xs sm:text-sm font-medium text-gray-600 dark:text-gray-400">Total Subjects</p>
                            <p class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-white">{{ $academicSubjects->total() }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-gradient-to-br from-green-50 to-emerald-50 dark:from-gray-700 dark:to-gray-800 rounded-xl p-4 sm:p-5 border border-gray-200 dark:border-gray-600 shadow-sm">
                    <div class="flex items-center">
                        <div class="p-2 bg-green-100 dark:bg-green-900/30 rounded-lg">
                            <svg class="w-5 h-5 sm:w-6 sm:h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="ml-3 sm:ml-4">
                            <p class="text-xs sm:text-sm font-medium text-gray-600 dark:text-gray-400">Level</p>
                            <p class="text-lg sm:text-xl font-bold text-gray-900 dark:text-white truncate">{{ $academicLevel->name }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-gradient-to-br from-purple-50 to-violet-50 dark:from-gray-700 dark:to-gray-800 rounded-xl p-4 sm:p-5 border border-gray-200 dark:border-gray-600 shadow-sm">
                    <div class="flex items-center">
                        <div class="p-2 bg-purple-100 dark:bg-purple-900/30 rounded-lg">
                            <svg class="w-5 h-5 sm:w-6 sm:h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                            </svg>
                        </div>
                        <div class="ml-3 sm:ml-4 relative overflow-hidden">
                            <p class="text-xs sm:text-sm font-medium text-gray-600 dark:text-gray-400">Group</p>
                            <p class="text-lg sm:text-xl font-bold text-gray-900 dark:text-white truncate  overflow-hidden">{{ $academicLevel->academicGroup->name }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-gradient-to-br from-orange-50 to-amber-50 dark:from-gray-700 dark:to-gray-800 rounded-xl p-4 sm:p-5 border border-gray-200 dark:border-gray-600 shadow-sm">
                    <div class="flex items-center">
                        <div class="p-2 bg-orange-100 dark:bg-orange-900/30 rounded-lg">
                            <svg class="w-5 h-5 sm:w-6 sm:h-6 text-orange-600 dark:text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 00-2-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                        </div>
                        <div class="ml-3 sm:ml-4">
                            <p class="text-xs sm:text-sm font-medium text-gray-600 dark:text-gray-400">Page</p>
                            <p class="text-xl sm:text-2xl font-bold text-gray-900 dark:text-white">{{ $academicSubjects->currentPage() }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Subjects List -->
            <div class="border-t border-gray-200 dark:border-gray-700">
                <div class="divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach ($academicSubjects as $academicSubject)
                        <div class="hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors duration-200 group">
                            <div class="px-4 sm:px-6 py-4 sm:py-5">
                                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                                    <!-- Subject Info -->
                                    <div class="flex items-center space-x-4 min-w-0 flex-1">
                                        <div class="flex-shrink-0 w-12 h-12 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-xl flex items-center justify-center shadow-lg group-hover:shadow-xl transition-shadow duration-300">
                                            <span class="text-white font-bold text-sm">{{ strtoupper(substr($academicSubject->code, 0, 2)) }}</span>
                                        </div>
                                        <div class="min-w-0 flex-1">
                                            <h3 class="text-lg sm:text-xl font-semibold text-gray-900 dark:text-white group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors duration-200 truncate">
                                                {{ $academicSubject->name }}
                                            </h3>
                                            <div class="flex flex-col sm:flex-row sm:items-center gap-2 sm:gap-4 mt-1">
                                                <p class="text-sm text-gray-600 dark:text-gray-400">
                                                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200">
                                                        {{ $academicSubject->code }}
                                                    </span>
                                                </p>
                                                <div class="flex items-center space-x-4 text-xs sm:text-sm text-gray-500 dark:text-gray-400">
                                                    <span class="flex items-center">
                                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                                                        </svg>
                                                        {{ $academicSubject->academicTopics()->count() }} Topics
                                                    </span>
                                                    <span class="flex items-center">
                                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                                        </svg>
                                                        {{ $academicSubject->quizzes()->count() }} Quizzes
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Actions -->
                                    <div class="flex items-center space-x-2 flex-shrink-0">
                                        <x-link.secondary :to="route('academic-subjects.show', ['academic_subject' => $academicSubject, 'academic_level' => getRouteParameter('academic_level'), 'academic_group' => getRouteParameter('academic_group')])"
                                                          class="inline-flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-all duration-200 hover:scale-105">
                                            <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                            </svg>
                                            <span class="hidden sm:inline">View</span>
                                        </x-link.secondary>

                                        @can('administrate')
                                            <x-link.secondary :to="route('academic-subjects.edit', ['academic_subject' => $academicSubject, 'academic_level' => $academicLevel, 'academic_group' => getRouteParameter('academic_group')])"
                                                              class="inline-flex items-center px-3 py-2 text-sm font-medium rounded-lg transition-all duration-200 hover:scale-105">
                                                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                                </svg>
                                                <span class="hidden sm:inline">Edit</span>
                                            </x-link.secondary>

                                            <button type="button"
                                                    x-data="{}"
                                                    x-on:click="$store.deleteForm.show('Delete Subject', 'Are you sure you want to delete {{ $academicSubject->name }}?', '{{ route('academic-subjects.destroy', ['academic_subject' => $academicSubject, 'academic_level' => $academicLevel, 'academic_group' => getRouteParameter('academic_group')]) }}')"
                                                    class="inline-flex items-center px-3 py-2 text-sm font-medium rounded-lg text-red-700 dark:text-red-400 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 hover:bg-red-100 dark:hover:bg-red-900/30 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition-all duration-200 hover:scale-105">
                                                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
                                                <span class="hidden sm:inline">Delete</span>
                                            </button>
                                        @endcan
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Pagination -->
            <div class="border-t border-gray-200 dark:border-gray-700 p-4 sm:p-6">
                <div class="flex justify-center">
                    {{ $academicSubjects->links() }}
                </div>
            </div>
        @else
            <!-- Empty State -->
            <div class="text-center py-16 px-6">
                <div class="mx-auto w-20 h-20 sm:w-24 sm:h-24 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-full flex items-center justify-center mb-8 shadow-lg">
                    <svg class="w-10 h-10 sm:w-12 sm:h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                </div>

                <h3 class="text-2xl font-bold text-gray-900 dark:text-white mb-3">No Subjects Found</h3>
                <p class="text-gray-600 dark:text-gray-400 mb-8 max-w-md mx-auto text-sm sm:text-base">
                    Get started by creating your first academic subject for <span class="font-semibold text-indigo-600 dark:text-indigo-400">{{ $academicLevel->name }}</span>.
                    You can organize your curriculum and manage educational content efficiently.
                </p>

                @can('administrate')
                    <div class="space-y-4">
                        <x-link.primary :to="route('academic-subjects.create', ['academic_level' => $academicLevel, 'academic_group' => $academicLevel->academicGroup])"
                                        class="inline-flex items-center px-6 py-3 text-base font-medium rounded-lg transition-all duration-200 hover:scale-105 focus:ring-4 focus:ring-indigo-200 dark:focus:ring-indigo-800">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                            </svg>
                            Create First Subject
                        </x-link.primary>

                        <div class="text-sm text-gray-500 dark:text-gray-400">
                            <p>✨ Start building your academic curriculum today</p>
                        </div>
                    </div>
                @endcan
            </div>
        @endif
    </div>
</x-layouts.app>
