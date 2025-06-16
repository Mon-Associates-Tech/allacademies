<x-layouts.app title="Academic Group Details" :has-action="false">
    <x-slot name="breadcrumb">
        <x-breadcrumb :paths="[
            'Academic Groups' => route('academic-groups.index'),
            $academicGroup->name => '#'
        ]" />
    </x-slot>

    <div class="max-w-7xl mx-auto space-y-6">
        <!-- Header Section -->
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
            <div class="bg-gradient-to-r from-blue-600 to-indigo-600 px-8 py-6">
                <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 w-16 h-16 bg-white/20 rounded-2xl flex items-center justify-center">
                            <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
                            </svg>
                        </div>
                        <div class="ml-6">
                            <h1 class="text-3xl font-bold text-white mb-2">{{ $academicGroup->name }}</h1>
                            <p class="text-blue-100 text-lg">Academic Group Overview</p>
                        </div>
                    </div>

                    @can('administrate')
                        <div class="mt-4 lg:mt-0 flex flex-col sm:flex-row gap-3">
                            <button type="button"
                                    x-data="{}"
                                    x-on:click="$store.deleteForm.show('Danger', 'Are you sure you want to delete {{ $academicGroup->name }}?', '{{ route('academic-groups.destroy', ['academic_group' => $academicGroup]) }}')"
                                    class="inline-flex items-center px-4 py-2 border border-white/30 text-sm font-medium rounded-xl text-white bg-white/10 hover:bg-white/20 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-white transition-all duration-200">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                </svg>
                                Delete Group
                            </button>

                            <a href="{{ route('academic-groups.edit', ['academic_group' => $academicGroup]) }}"
                               class="inline-flex items-center px-6 py-2 border border-transparent text-sm font-medium rounded-xl text-blue-600 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200 shadow-sm">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                                Edit Group
                            </a>
                        </div>
                    @endcan
                </div>
            </div>

            <!-- Stats Section -->
            <div class="px-8 py-6 bg-gray-50 border-b border-gray-200">
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Academic Levels Count -->
                    <div class="flex items-center">
                        <div class="flex-shrink-0 w-12 h-12 bg-blue-100 rounded-xl flex items-center justify-center">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <div class="text-2xl font-bold text-gray-900">{{ $academicGroup->academic_levels_count }}</div>
                            <div class="text-sm text-gray-600">Academic {{ Str::plural('Level', $academicGroup->academic_levels_count) }}</div>
                        </div>
                    </div>

                    <!-- Created Date -->
                    <div class="flex items-center">
                        <div class="flex-shrink-0 w-12 h-12 bg-green-100 rounded-xl flex items-center justify-center">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <div class="text-2xl font-bold text-gray-900">{{ $academicGroup->created_at->format('M Y') }}</div>
                            <div class="text-sm text-gray-600">Created</div>
                        </div>
                    </div>

                    <!-- Status -->
                    <div class="flex items-center">
                        <div class="flex-shrink-0 w-12 h-12 bg-emerald-100 rounded-xl flex items-center justify-center">
                            <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <div class="text-2xl font-bold text-gray-900">Active</div>
                            <div class="text-sm text-gray-600">Status</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content Grid -->
        <div class="grid lg:grid-cols-3 gap-6">
            <!-- Academic Levels Section -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                    <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 text-blue-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                                </svg>
                                <h3 class="text-lg font-semibold text-gray-900">Academic Levels</h3>
                            </div>
                            @can('administrate')
                                <a href="{{ route('academic-levels.create', ['academic_group' => $academicGroup]) }}"
                                   class="inline-flex items-center px-3 py-1.5 border border-transparent text-xs font-medium rounded-lg text-blue-600 bg-blue-50 hover:bg-blue-100 transition-colors duration-200">
                                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                    </svg>
                                    Add Level
                                </a>
                            @endcan
                        </div>
                    </div>

                    <div class="p-6">
                        @if($academicGroup->academic_levels_count > 0)
                            <div class="space-y-4">
                                <!-- This would be populated with actual academic levels data -->
                                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-xl hover:bg-gray-100 transition-colors duration-200">
                                    <div class="flex items-center">
                                        <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                            </svg>
                                        </div>
                                        <div class="ml-4">
                                            <div class="text-base font-medium text-gray-900">View All Academic Levels</div>
                                            <div class="text-sm text-gray-600">{{ $academicGroup->academic_levels_count }} {{ Str::plural('level', $academicGroup->academic_levels_count) }} available</div>
                                        </div>
                                    </div>
                                    <a href="{{ route('academic-levels.index', ['academic_group' => $academicGroup]) }}"
                                       class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg text-blue-600 bg-blue-50 hover:bg-blue-100 transition-colors duration-200">
                                        View Levels
                                        <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                        </svg>
                                    </a>
                                </div>
                            </div>
                        @else
                            <div class="text-center py-12">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                                </svg>
                                <h3 class="mt-2 text-sm font-medium text-gray-900">No academic levels</h3>
                                <p class="mt-1 text-sm text-gray-500">Get started by creating an academic level for this group.</p>
                                @can('administrate')
                                    <div class="mt-6">
                                        <a href="{{ route('academic-levels.create', ['academic_group' => $academicGroup]) }}"
                                           class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-lg text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                            </svg>
                                            Create Academic Level
                                        </a>
                                    </div>
                                @endcan
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Quick Info Card -->
                <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                    <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                            <svg class="w-5 h-5 text-blue-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Details
                        </h3>
                    </div>
                    <div class="p-6 space-y-4">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Group Name</dt>
                            <dd class="mt-1 text-base text-gray-900">{{ $academicGroup->name }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Created</dt>
                            <dd class="mt-1 text-base text-gray-900">{{ $academicGroup->created_at->format('F j, Y') }}</dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">Last Updated</dt>
                            <dd class="mt-1 text-base text-gray-900">{{ $academicGroup->updated_at->format('F j, Y') }}</dd>
                        </div>
                        @if($academicGroup->tag)
                            <div>
                                <dt class="text-sm font-medium text-gray-500">Tag</dt>
                                <dd class="mt-1">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    {{ $academicGroup->tag }}
                                </span>
                                </dd>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Quick Actions Card -->
                <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                    <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                        <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                            <svg class="w-5 h-5 text-green-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                            </svg>
                            Quick Actions
                        </h3>
                    </div>
                    <div class="p-6 space-y-3">
                        <a href="{{ route('academic-levels.index', ['academic_group' => $academicGroup]) }}"
                           class="flex items-center p-3 text-sm font-medium text-gray-700 rounded-lg hover:bg-gray-50 transition-colors duration-200">
                            <svg class="w-5 h-5 text-blue-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                            </svg>
                            Manage Academic Levels
                        </a>

                        @can('administrate')
                            <a href="{{ route('academic-levels.create', ['academic_group' => $academicGroup]) }}"
                               class="flex items-center p-3 text-sm font-medium text-gray-700 rounded-lg hover:bg-gray-50 transition-colors duration-200">
                                <svg class="w-5 h-5 text-green-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                </svg>
                                Add New Level
                            </a>

                            <a href="{{ route('academic-groups.edit', ['academic_group' => $academicGroup]) }}"
                               class="flex items-center p-3 text-sm font-medium text-gray-700 rounded-lg hover:bg-gray-50 transition-colors duration-200">
                                <svg class="w-5 h-5 text-amber-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                                </svg>
                                Edit Group Settings
                            </a>
                        @endcan

                        <a href="{{ route('academic-groups.index') }}"
                           class="flex items-center p-3 text-sm font-medium text-gray-700 rounded-lg hover:bg-gray-50 transition-colors duration-200">
                            <svg class="w-5 h-5 text-gray-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                            </svg>
                            Back to All Groups
                        </a>
                    </div>
                </div>

                <!-- Usage Tips Card -->
                <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-2xl p-6 border border-blue-200">
                    <div class="flex items-center mb-4">
                        <svg class="w-6 h-6 text-blue-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/>
                        </svg>
                        <h4 class="text-lg font-semibold text-blue-900">Tips</h4>
                    </div>
                    <div class="space-y-3 text-sm text-blue-800">
                        <div class="flex items-start">
                            <svg class="w-4 h-4 text-blue-600 mr-2 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span>Academic groups help organize content by educational level</span>
                        </div>
                        <div class="flex items-start">
                            <svg class="w-4 h-4 text-blue-600 mr-2 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span>Add academic levels to create a complete educational structure</span>
                        </div>
                        <div class="flex items-start">
                            <svg class="w-4 h-4 text-blue-600 mr-2 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span>Each level can contain multiple subjects and courses</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
