<x-layouts.app title="Academic Group Details" :has-action="false">
    <x-slot name="breadcrumb">
        <x-breadcrumb :paths="[
            'Academic Groups' => route('academic-groups.index'),
            $academicGroup->name => '#'
        ]" />
    </x-slot>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Combined Main Container -->
        <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-lg overflow-hidden">
            <!-- Header Section -->
            <div class="px-4 sm:px-6 py-5 bg-gray-50 dark:bg-gray-700 border-b border-gray-200 dark:border-gray-600">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                            </svg>
                        </div>
                        <div>
                            <h1 class="text-xl font-bold text-gray-900 dark:text-white">{{ $academicGroup->name }}</h1>
                            <div class="flex flex-wrap items-center gap-2 text-sm text-gray-600 dark:text-gray-400 mt-1">
                                <span>{{ $academicGroup->academic_levels_count }} levels</span>
                                <span>{{ $academicGroup->teachers_count ?? 0 }} teachers</span>
                                <span class="text-emerald-600 dark:text-emerald-400">● Active</span>
                            </div>
                        </div>
                    </div>

                    @can('administrate')
                        <div class="relative" x-data="{ open: false }">
                            <button @click="open = !open" class="p-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 rounded-lg transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"/>
                                </svg>
                            </button>

                            <div x-show="open" @click.away="open = false" x-transition
                                 class="absolute right-0 mt-2 w-40 bg-white dark:bg-gray-700 rounded-lg shadow-lg border border-gray-200 dark:border-gray-600 z-10">
                                <a href="{{ route('academic-groups.edit', ['academic_group' => $academicGroup]) }}"
                                   class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-600 rounded-t-lg">
                                    Edit Group
                                </a>
                                <button type="button"
                                        x-data="{}"
                                        x-on:click="$store.deleteForm.show('Danger', 'Are you sure you want to delete {{ $academicGroup->name }}?', '{{ route('academic-groups.destroy', ['academic_group' => $academicGroup]) }}')"
                                        class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-b-lg">
                                    Delete Group
                                </button>
                            </div>
                        </div>
                    @endcan
                </div>
            </div>

            <!-- Main Content Area -->
            <div class="p-4 sm:p-6">
                <div class="grid lg:grid-cols-3 gap-6">
                    <!-- Academic Levels Section -->
                    <div class="lg:col-span-2">
                        <div class="mb-6">
                            <div class="flex items-center justify-between mb-4">
                                <div class="flex items-center">
                                    <svg class="w-5 h-5 text-blue-600 dark:text-blue-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                                    </svg>
                                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Academic Levels</h3>
                                </div>
                                @can('administrate')
                                    <a href="{{ route('academic-levels.create', ['academic_group' => $academicGroup]) }}"
                                       class="inline-flex items-center px-3 py-1.5 border border-transparent text-sm font-medium rounded-lg text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-900/20 hover:bg-blue-100 dark:hover:bg-blue-900/40 transition-colors duration-200">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                        </svg>
                                        Add Level
                                    </a>
                                @endcan
                            </div>

                          @if($academicGroup->academicLevels && $academicGroup->academicLevels->count() > 0)
    <div class="space-y-3">
        @foreach($academicGroup->academicLevels as $level)
            <div class="group bg-gray-50 dark:bg-gray-700/50 rounded-xl shadow-sm hover:shadow border border-gray-200 dark:border-gray-600 hover:border-blue-300 dark:hover:border-blue-600 transition-all duration-300 overflow-hidden">
                <div class="p-4">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                        <div class="flex items-start space-x-3 flex-1 min-w-0">
                            <div class="flex-shrink-0 w-12 h-12 bg-gradient-to-br from-blue-500 via-blue-600 to-purple-600 rounded-xl flex items-center justify-center shadow">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                </svg>
                            </div>

                            <div class="flex-1 min-w-0">
                                <h4 class="font-bold text-gray-900 dark:text-white text-lg mb-1 truncate">
                                    {{ $level->name }}
                                </h4>
                                @if($level->label && $level->label !== $level->name)
                                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-2 truncate">
                                        {{ $level->label }}
                                    </p>
                                @endif

                                <div class="flex flex-wrap gap-3 mt-2">
                                    <div class="flex items-center bg-white dark:bg-gray-600/50 px-2 py-1 rounded-lg">
                                        <svg class="w-4 h-4 text-blue-600 dark:text-blue-400 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                        </svg>
                                        <span class="text-sm font-medium text-gray-700 dark:text-gray-200">{{ $level->subjects->count() }} {{ Str::plural('Subject', $level->subjects->count()) }}</span>
                                    </div>
                                    <div class="flex items-center bg-white dark:bg-gray-600/50 px-2 py-1 rounded-lg">
                                        <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/>
                                        </svg>
                                        <span class="text-sm font-medium text-gray-700 dark:text-gray-200">{{ $level->students->count() }} {{ Str::plural('Student', $level->students->count()) }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center space-x-2">
                            <a href="{{ route('academic-levels.show', ['academic_level' => $level, 'academic_group' => getRouteParameter('academic_group')]) }}"
                               class="inline-flex items-center px-3 py-2 text-sm font-medium text-blue-700 dark:text-blue-300 bg-blue-100 dark:bg-blue-900/50 hover:bg-blue-200 dark:hover:bg-blue-800 rounded-lg transition-colors duration-200"
                               title="View Level Details">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                                View
                            </a>

                            @can('administrate')
                                <a href="{{ route('academic-levels.edit', ['academic_level' => $level, 'academic_group' => getRouteParameter('academic_group')]) }}"
                                   class="inline-flex items-center px-3 py-2 text-sm font-medium text-amber-700 dark:text-amber-300 bg-amber-100 dark:bg-amber-900/50 hover:bg-amber-200 dark:hover:bg-amber-800 rounded-lg transition-colors duration-200"
                                   title="Edit Level">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                    </svg>
                                    Edit
                                </a>
                            @endcan
                        </div>
                    </div>
                </div>
            </div>
        @endforeach

        @if($academicGroup->academicLevels->count() >= 5)
            <div class="pt-2 border-t border-gray-200 dark:border-gray-600">
                <a href="{{ route('academic-levels.index', ['academic_group' => $academicGroup]) }}"
                   class="inline-flex items-center text-sm text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300 font-medium">
                    View all {{ $academicGroup->academicLevels->count() }} academic levels
                    <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                    </svg>
                </a>
            </div>
        @endif
    </div>
@else
    <div class="text-center py-8 bg-gray-50 dark:bg-gray-700/50 rounded-xl">
        <div class="mx-auto w-12 h-12 bg-gray-100 dark:bg-gray-600 rounded-full flex items-center justify-center mb-4">
            <svg class="w-6 h-6 text-gray-400 dark:text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
            </svg>
        </div>
        <h4 class="text-base font-medium text-gray-900 dark:text-white mb-1">No academic levels</h4>
        <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">Get started by creating an academic level for this group.</p>
        @can('administrate')
            <a href="{{ route('academic-levels.create', ['academic_group' => $academicGroup]) }}"
               class="inline-flex items-center px-3 py-1.5 border border-transparent text-sm font-medium rounded-lg text-white bg-blue-600 hover:bg-blue-700 transition-colors duration-200">
                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                </svg>
                Create Academic Level
            </a>
        @endcan
    </div>
@endif

                        </div>
                    </div>

                    <!-- Sidebar -->
                    <div>
                        <div class="space-y-5">
                            <!-- Details Card -->
                            <div class="bg-gray-50 dark:bg-gray-700/50 rounded-xl p-4">
                                <h4 class="text-md font-semibold text-gray-900 dark:text-white flex items-center mb-3">
                                    <svg class="w-4 h-4 text-blue-600 dark:text-blue-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    Group Details
                                </h4>
                                <div class="space-y-3">
                                    <div>
                                        <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Name</dt>
                                        <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $academicGroup->name }}</dd>
                                    </div>
                                    <div>
                                        <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Created</dt>
                                        <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $academicGroup->created_at->format('M j, Y') }}</dd>
                                    </div>
                                    <div>
                                        <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Last Updated</dt>
                                        <dd class="mt-1 text-sm text-gray-900 dark:text-white">{{ $academicGroup->updated_at->format('M j, Y') }}</dd>
                                    </div>
                                    @if($academicGroup->tag)
                                        <div>
                                            <dt class="text-xs font-medium text-gray-500 dark:text-gray-400 uppercase tracking-wide">Tag</dt>
                                            <dd class="mt-1">
                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 dark:bg-blue-900/50 text-blue-800 dark:text-blue-200">
                                                    {{ $academicGroup->tag }}
                                                </span>
                                            </dd>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <!-- Quick Actions Card -->
                            <div class="bg-gray-50 dark:bg-gray-700/50 rounded-xl p-4">
                                <h4 class="text-md font-semibold text-gray-900 dark:text-white flex items-center mb-3">
                                    <svg class="w-4 h-4 text-green-600 dark:text-green-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                    </svg>
                                    Quick Actions
                                </h4>
                                <div class="space-y-2">
                                    <a href="{{ route('academic-levels.index', ['academic_group' => $academicGroup]) }}"
                                       class="flex items-center p-2 text-sm font-medium text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors duration-200">
                                        <svg class="w-4 h-4 text-blue-500 dark:text-blue-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                                        </svg>
                                        Manage Levels
                                    </a>

                                    @can('administrate')
                                        <a href="{{ route('academic-levels.create', ['academic_group' => $academicGroup]) }}"
                                           class="flex items-center p-2 text-sm font-medium text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors duration-200">
                                            <svg class="w-4 h-4 text-green-500 dark:text-green-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                            </svg>
                                            Add New Level
                                        </a>

                                        <a href="{{ route('academic-groups.edit', ['academic_group' => $academicGroup]) }}"
                                           class="flex items-center p-2 text-sm font-medium text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors duration-200">
                                            <svg class="w-4 h-4 text-amber-500 dark:text-amber-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                            </svg>
                                            Edit Group
                                        </a>
                                    @endcan

                                    <a href="{{ route('academic-groups.index') }}"
                                       class="flex items-center p-2 text-sm font-medium text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-600 transition-colors duration-200">
                                        <svg class="w-4 h-4 text-gray-500 dark:text-gray-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                                        </svg>
                                        All Groups
                                    </a>
                                </div>
                            </div>

                            <!-- Tips Card -->
                            <div class="bg-gradient-to-br from-blue-50 to-indigo-50 dark:from-blue-900/30 dark:to-indigo-900/30 rounded-xl p-4 border border-blue-200 dark:border-blue-800">
                                <div class="flex items-center mb-3">
                                    <svg class="w-4 h-4 text-blue-600 dark:text-blue-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                                    </svg>
                                    <h4 class="text-md font-semibold text-blue-900 dark:text-blue-100">Tips</h4>
                                </div>
                                <div class="space-y-2 text-sm text-blue-800 dark:text-blue-200">
                                    <div class="flex items-start">
                                        <svg class="w-3 h-3 text-blue-600 dark:text-blue-400 mr-2 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        <span>Academic levels organize curriculum into sections</span>
                                    </div>
                                    <div class="flex items-start">
                                        <svg class="w-3 h-3 text-blue-600 dark:text-blue-400 mr-2 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        <span>Use consistent naming conventions</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
