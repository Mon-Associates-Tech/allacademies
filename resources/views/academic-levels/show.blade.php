<x-layouts.app title="Academic Level Details" page-name="Academic Details" :has-action="false">
    <x-slot name="breadcrumb">
        <x-breadcrumb :paths="[
            'Academic Groups' => route('academic-groups.index'),
            $academicLevel->academicGroup->name => route('academic-groups.show', ['academic_group' => $academicLevel->academicGroup]),
            'Academic Levels' => route('academic-levels.index', ['academic_group' => $academicLevel->academicGroup]),
            $academicLevel->name => null,
        ]" />
    </x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">
        <!-- Header Section -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="bg-gradient-to-r from-indigo-500 to-purple-600 px-6 py-4">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="p-2 bg-white/20 backdrop-blur-sm rounded-lg">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-2M7 21H5m2 0h2m6-11V7h-2v3m0 0V7h2v3m-2 0h2v3m-2-3h-2v3"></path>
                            </svg>
                        </div>
                        <div>
                            <h1 class="text-xl sm:text-2xl font-bold text-white">{{ $academicLevel->name }}</h1>
                            <p class="text-indigo-100 text-sm">{{ $academicLevel->label }} • {{ $academicLevel->academicGroup->name }}</p>
                        </div>
                    </div>

                    @can('administrate')
                        <div class="flex flex-col sm:flex-row space-y-2 sm:space-y-0 sm:space-x-2 mt-3 sm:mt-0">
                            <button type="button"
                                    class="inline-flex items-center px-3 py-1.5 border border-white/30 rounded-lg text-sm font-medium text-white bg-white/10 hover:bg-white/20 backdrop-blur-sm transition-all duration-150 hover:scale-105"
                                    x-data="{}"
                                    x-on:click="$store.deleteForm.show('Delete Academic Level', 'Are you sure you want to delete {{ $academicLevel->name }}? This action cannot be undone.', '{{ route('academic-levels.destroy', ['academic_level' => $academicLevel, 'academic_group' => $academicLevel->academicGroup]) }}')">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                </svg>
                                Delete
                            </button>
                            <a href="{{ route('academic-levels.edit', ['academic_level' => $academicLevel, 'academic_group' => $academicLevel->academicGroup]) }}"
                               class="inline-flex items-center px-3 py-1.5 border border-white/30 rounded-lg text-sm font-medium text-white bg-white/10 hover:bg-white/20 backdrop-blur-sm transition-all duration-150 hover:scale-105">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                                Edit Level
                            </a>
                        </div>
                    @endcan
                </div>
            </div>

            <!-- Stats Overview -->
            <div class="px-6 py-3 bg-gray-50 dark:bg-gray-700/50 border-t border-gray-200 dark:border-gray-600">
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                    <div class="text-center">
                        <div class="text-xl font-bold text-gray-900 dark:text-white">{{ $academicLevel->academic_subjects_count }}</div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">Subjects</div>
                    </div>
                    <div class="text-center">
                        <div class="text-xl font-bold text-gray-900 dark:text-white">{{ $academicLevel->students_count ?? 0 }}</div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">Students</div>
                    </div>
                    <div class="text-center">
                        <div class="text-xl font-bold text-gray-900 dark:text-white">{{ $academicLevel->teachers_count ?? 0 }}</div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">Teachers</div>
                    </div>
                    <div class="text-center">
                        <div class="text-xl font-bold text-indigo-600 dark:text-indigo-400">Active</div>
                        <div class="text-xs text-gray-500 dark:text-gray-400">Status</div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Academic Subjects Section -->
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                    <div class="flex items-center space-x-3">
                        <div class="p-2 bg-blue-100 dark:bg-blue-900/30 rounded-lg">
                            <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Academic Subjects</h3>
                            <p class="text-sm text-gray-600 dark:text-gray-400">
                                {{ $academicLevel->academic_subjects_count }} {{ Str::plural('subject', $academicLevel->academic_subjects_count) }} available
                            </p>
                        </div>
                    </div>

                    @can('administrate')
                        <div class="mt-4 sm:mt-0">
                            <a href="{{ route('academic-subjects.create', ['academic_level' => $academicLevel, 'academic_group' => $academicLevel->academicGroup]) }}"
                               class="inline-flex items-center px-3 py-2 text-sm font-medium text-indigo-600 dark:text-indigo-400 bg-indigo-50 dark:bg-indigo-900/30 rounded-lg hover:bg-indigo-100 dark:hover:bg-indigo-900/50 transition-colors duration-150">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                Add Subject
                            </a>
                        </div>
                    @endcan
                </div>
            </div>

            <div class="divide-y divide-gray-200 dark:divide-gray-700">
                @if($academicLevel->academicSubjects && $academicLevel->academicSubjects->count() > 0)
                    @foreach($academicLevel->academicSubjects as $subject)
                        <div class="p-6 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors duration-150">
                            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                                <div class="flex items-start space-x-4">
                                    <div class="flex-shrink-0">
                                        <div class="w-10 h-10 bg-gradient-to-br from-blue-500 to-indigo-600 rounded-lg flex items-center justify-center">
                                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                            </svg>
                                        </div>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="flex flex-col sm:flex-row sm:items-center sm:space-x-3">
                                            <h4 class="text-lg font-semibold text-gray-900 dark:text-white">{{ $subject->name }}</h4>
                                            @if($subject->code)
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 dark:bg-gray-700 text-gray-800 dark:text-gray-200 mt-1 sm:mt-0">
                                                    {{ $subject->code }}
                                                </span>
                                            @endif
                                        </div>
                                        @if($subject->description)
                                            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">{{ Str::limit($subject->description, 100) }}</p>
                                        @endif
                                        <div class="flex items-center space-x-4 mt-2">
                                            @if(isset($subject->topics_count))
                                                <div class="flex items-center space-x-1 text-sm text-gray-500 dark:text-gray-400">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                                                    </svg>
                                                    <span>{{ $subject->topics_count }} {{ Str::plural('topic', $subject->topics_count) }}</span>
                                                </div>
                                            @endif
                                            <div class="flex items-center space-x-1 text-sm text-green-600 dark:text-green-400">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                </svg>
                                                <span>Active</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="flex items-center space-x-2 mt-4 sm:mt-0">
                                    <a href="{{ route('academic-subjects.show', ['academic_subject' => $subject, 'academic_level' => getRouteParameter('academic_level'), 'academic_group' => getRouteParameter('academic_group')]) }}"
                                       class="inline-flex items-center px-3 py-2 text-sm font-medium text-indigo-600 dark:text-indigo-400 bg-indigo-50 dark:bg-indigo-900/30 rounded-lg hover:bg-indigo-100 dark:hover:bg-indigo-900/50 transition-all duration-150 hover:scale-105">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                        View Details
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="p-12 text-center">
                        <div class="w-16 h-16 mx-auto mb-4 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center">
                            <svg class="w-8 h-8 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">No Subjects Yet</h3>
                        <p class="text-gray-600 dark:text-gray-400 mb-6">This academic level doesn't have any subjects yet. Add the first subject to get started.</p>
                        @can('administrate')
                            <a href="{{ route('academic-subjects.create', ['academic_level' => $academicLevel, 'academic_group' => $academicLevel->academicGroup]) }}"
                               class="inline-flex items-center px-4 py-2 bg-indigo-600 dark:bg-indigo-500 text-white rounded-lg hover:bg-indigo-700 dark:hover:bg-indigo-600 transition-all duration-150 hover:scale-105">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                Add First Subject
                            </a>
                        @endcan
                    </div>
                @endif
            </div>
        </div>

        <!-- Additional Information Card -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Level Information -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <div class="flex items-center space-x-3 mb-4">
                    <div class="p-2 bg-gray-100 dark:bg-gray-700 rounded-lg">
                        <svg class="w-5 h-5 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Level Information</h3>
                </div>
                <div class="space-y-3">
                    <div class="flex justify-between items-center py-2">
                        <span class="text-sm text-gray-600 dark:text-gray-400">Name</span>
                        <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $academicLevel->name }}</span>
                    </div>
                    <div class="flex justify-between items-center py-2">
                        <span class="text-sm text-gray-600 dark:text-gray-400">Label</span>
                        <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $academicLevel->label }}</span>
                    </div>
                    <div class="flex justify-between items-center py-2">
                        <span class="text-sm text-gray-600 dark:text-gray-400">Academic Group</span>
                        <a href="{{ route('academic-groups.show', ['academic_group' => $academicLevel->academicGroup]) }}"
                           class="text-sm font-medium text-indigo-600 dark:text-indigo-400 hover:text-indigo-700 dark:hover:text-indigo-300">
                            {{ $academicLevel->academicGroup->name }}
                        </a>
                    </div>
                    <div class="flex justify-between items-center py-2">
                        <span class="text-sm text-gray-600 dark:text-gray-400">Created</span>
                        <span class="text-sm font-medium text-gray-900 dark:text-white">{{ $academicLevel->created_at->format('M d, Y') }}</span>
                    </div>
                </div>
            </div>

            <!-- Navigation Links -->
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 p-6">
                <div class="flex items-center space-x-3 mb-4">
                    <div class="p-2 bg-gray-100 dark:bg-gray-700 rounded-lg">
                        <svg class="w-5 h-5 text-gray-600 dark:text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path>
                        </svg>
                    </div>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Quick Navigation</h3>
                </div>
                <div class="space-y-2">
                    <a href="{{ route('academic-groups.show', ['academic_group' => $academicLevel->academicGroup]) }}"
                       class="flex items-center p-3 text-sm text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-150">
                        <svg class="w-4 h-4 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                        View Academic Group
                    </a>
                    <a href="{{ route('academic-levels.index', ['academic_group' => $academicLevel->academicGroup]) }}"
                       class="flex items-center p-3 text-sm text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-150">
                        <svg class="w-4 h-4 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path>
                        </svg>
                        All Academic Levels
                    </a>
                    <a href="{{ route('academic-subjects.index', ['academic_level' => $academicLevel, 'academic_group' => $academicLevel->academicGroup]) }}"
                       class="flex items-center p-3 text-sm text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors duration-150">
                        <svg class="w-4 h-4 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                        </svg>
                        View All Subjects
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
