<x-layouts.app title="Academic Level Details" page-name="Academic Details" :has-action="false">
    <x-slot name="breadcrumb">
        <x-breadcrumb :paths="[
            'Academic Groups' => route('academic-groups.index'),
            $academicLevel->academicGroup->name => route('academic-groups.show', ['academic_group' => $academicLevel->academicGroup]),
            'Academic Levels' => route('academic-levels.index', ['academic_group' => $academicLevel->academicGroup]),
            $academicLevel->name => null,
        ]" />
    </x-slot>

    <div class="space-y-6">
        <!-- Header Section -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-start justify-between">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900 mb-2">{{ $academicLevel->name }}</h1>
                    <p class="text-lg text-gray-600 mb-3">{{ $academicLevel->label }}</p>
                    <div class="flex items-center space-x-2 text-sm text-gray-500">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-2M7 21H5m2 0h2m6-11V7h-2v3m0 0V7h2v3m-2 0h2v3m-2-3h-2v3"></path>
                        </svg>
                        <span>Part of {{ $academicLevel->academicGroup->name }}</span>
                    </div>
                </div>
                @can('administrate')
                <div class="flex space-x-3">
                    <x-button.secondary type="button" x-data="{}" x-on:click="$store.deleteForm.show('Danger', 'Are you sure you want to delete {{ $academicLevel->name }}?', '{{ route('academic-levels.destroy', ['academic_level' => $academicLevel, 'academic_group' => $academicLevel->academicGroup]) }}')">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                        Delete
                    </x-button.secondary>
                    <x-link.primary :to="route('academic-levels.edit', ['academic_level' => $academicLevel, 'academic_group' => $academicLevel->academicGroup])">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                        Edit Level
                    </x-link.primary>
                </div>
                @endcan
            </div>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Academic Subjects Card -->
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow duration-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Academic Subjects</p>
                        <p class="text-3xl font-bold text-blue-600">{{ $academicLevel->academic_subjects_count }}</p>
                        <p class="text-sm text-gray-500 mt-1">{{ Str::plural('subject', $academicLevel->academic_subjects_count) }} available</p>
                    </div>
                    <div class="p-3 bg-blue-100 rounded-full">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                        </svg>
                    </div>
                </div>
                <div class="mt-4">
                    <x-anchor to="{{ route('academic-subjects.index', ['academic_level' => $academicLevel, 'academic_group' => $academicLevel->academicGroup]) }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                        View all subjects →
                    </x-anchor>
                </div>
            </div>

            <!-- Students Card (if relationship exists) -->
            @if(method_exists($academicLevel, 'students'))
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow duration-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Students</p>
                        <p class="text-3xl font-bold text-green-600">{{ $academicLevel->students_count ?? 0 }}</p>
                        <p class="text-sm text-gray-500 mt-1">enrolled students</p>
                    </div>
                    <div class="p-3 bg-green-100 rounded-full">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                        </svg>
                    </div>
                </div>
            </div>
            @endif

            <!-- Teachers Card (if relationship exists) -->
            @if(method_exists($academicLevel, 'teachers'))
            <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 hover:shadow-md transition-shadow duration-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-gray-600">Teachers</p>
                        <p class="text-3xl font-bold text-purple-600">{{ $academicLevel->teachers_count ?? 0 }}</p>
                        <p class="text-sm text-gray-500 mt-1">assigned teachers</p>
                    </div>
                    <div class="p-3 bg-purple-100 rounded-full">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                    </div>
                </div>
            </div>
            @endif
        </div>

        <!-- Details Section -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">Level Information</h2>
            </div>
            <div class="p-6">
                <dl class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <dt class="text-sm font-medium text-gray-500 mb-1">Level Name</dt>
                        <dd class="text-lg text-gray-900">{{ $academicLevel->name }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500 mb-1">Display Label</dt>
                        <dd class="text-lg text-gray-900">{{ $academicLevel->label }}</dd>
                    </div>
                    <div class="md:col-span-2">
                        <dt class="text-sm font-medium text-gray-500 mb-1">Academic Group</dt>
                        <dd class="text-lg">
                            <x-anchor to="{{ route('academic-groups.show', ['academic_group' => $academicLevel->academicGroup]) }}" class="text-blue-600 hover:text-blue-800 font-medium">
                                {{ $academicLevel->academicGroup->name }}
                            </x-anchor>
                        </dd>
                    </div>
                </dl>
            </div>
        </div>

        <!-- Quick Actions Section -->
        @can('moderate')
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">Quick Actions</h2>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    <x-link.secondary :to="route('academic-subjects.index', ['academic_level' => $academicLevel, 'academic_group' => $academicLevel->academicGroup])" class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors duration-200">
                        <svg class="w-5 h-5 text-gray-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                        </svg>
                        <div>
                            <div class="font-medium text-gray-900">Manage Subjects</div>
                            <div class="text-sm text-gray-500">View and manage academic subjects</div>
                        </div>
                    </x-link.secondary>

                    @can('administrate')
                    <x-link.secondary :to="route('academic-subjects.create', ['academic_level' => $academicLevel, 'academic_group' => $academicLevel->academicGroup])" class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors duration-200">
                        <svg class="w-5 h-5 text-gray-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        <div>
                            <div class="font-medium text-gray-900">Add Subject</div>
                            <div class="text-sm text-gray-500">Create a new academic subject</div>
                        </div>
                    </x-link.secondary>
                    @endcan

                    <x-link.secondary :to="route('academic-groups.show', ['academic_group' => $academicLevel->academicGroup])" class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors duration-200">
                        <svg class="w-5 h-5 text-gray-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-2M7 21H5m2 0h2m6-11V7h-2v3m0 0V7h2v3m-2 0h2v3m-2-3h-2v3"></path>
                        </svg>
                        <div>
                            <div class="font-medium text-gray-900">View Group</div>
                            <div class="text-sm text-gray-500">Go to {{ $academicLevel->academicGroup->name }}</div>
                        </div>
                    </x-link.secondary>
                </div>
            </div>
        </div>
        @endcan
    </div>
</x-layouts.app>
