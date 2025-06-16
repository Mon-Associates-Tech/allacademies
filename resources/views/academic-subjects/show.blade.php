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

            <div class="max-w-7xl mx-auto space-y-6">
                <!-- Header Section -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                    <div class="flex items-start justify-between">
                        <div class="flex items-center space-x-4">
                            <div class="p-3 bg-indigo-100 rounded-full">
                                <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                </svg>
                            </div>
                            <div>
                                <h1 class="text-2xl font-bold text-gray-900">{{ $academicSubject->name }}</h1>
                                <p class="text-gray-600 mt-1">Subject Code: {{ $academicSubject->code }}</p>
                            </div>
                        </div>

                        @can('administrate')
                            <div class="flex space-x-3">
                                <x-button.secondary type="button" x-data="{}"
                                    x-on:click="$store.deleteForm.show(
                                        'Delete Subject',
                                        'Are you sure you want to delete {{ $academicSubject->name }}? This action cannot be undone.',
                                        '{{ route('academic-subjects.destroy', ['academic_subject' => $academicSubject, 'academic_level' => $academicSubject->academicLevel, 'academic_group' => $academicSubject->academicLevel->academicGroup]) }}'
                                    )">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                    Delete Subject
                                </x-button.secondary>
                                <x-link.primary :to="route('academic-subjects.edit', ['academic_subject' => $academicSubject, 'academic_level' => $academicSubject->academicLevel, 'academic_group' => $academicSubject->academicLevel->academicGroup])">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                    </svg>
                                    Edit Subject
                                </x-link.primary>
                            </div>
                        @endcan
                    </div>
                </div>

                <!-- Subject Info & Stats -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <!-- Topics Card -->
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-sm font-medium text-gray-600">Topics</p>
                                <p class="text-3xl font-bold text-indigo-600">{{ $academicSubject->academic_topics_count }}</p>
                                <p class="text-sm text-gray-500 mt-1">
                                    {{ Str::plural('topic', $academicSubject->academic_topics_count) }} available
                                </p>
                            </div>
                            <div class="p-3 bg-indigo-100 rounded-full">
                                <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                </svg>
                            </div>
                        </div>
                        <div class="mt-4">
                            <x-link.primary :to="route('academic-topics.index', ['academic_subject' => $academicSubject, 'academic_level' => $academicSubject->academicLevel, 'academic_group' => $academicSubject->academicLevel->academicGroup])" class="w-full justify-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                </svg>
                                View All Topics
                            </x-link.primary>
                        </div>
                    </div>

                    <!-- Level Info -->
                    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                        <h3 class="font-medium text-gray-900 mb-4">Academic Level</h3>
                        <div class="flex items-start space-x-3">
                            <div class="flex-shrink-0 mt-1">
                                <div class="w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                                    <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-2M7 21H5m2 0h2m6-11V7h-2v3m0 0V7h2v3m-2 0h2v3m-2-3h-2v3"></path>
                                    </svg>
                                </div>
                            </div>
                            <div>
                                <x-anchor :to="route('academic-levels.show', ['academic_level' => $academicSubject->academicLevel, 'academic_group' => $academicSubject->academicLevel->academicGroup])" class="font-medium text-gray-900 hover:text-indigo-600">
                                    {{ $academicSubject->academicLevel->name }}
                                </x-anchor>
                                <p class="text-sm text-gray-500 mt-1">{{ $academicSubject->academicLevel->label }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Actions -->
                <div class="bg-white rounded-lg shadow-sm border border-gray-200">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-lg font-semibold text-gray-900">Quick Actions</h2>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <x-link.primary :to="route('academic-topics.create', ['academic_subject' => $academicSubject, 'academic_level' => $academicSubject->academicLevel, 'academic_group' => $academicSubject->academicLevel->academicGroup])" class="justify-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                Add New Topic
                            </x-link.primary>

                            <x-link.secondary :to="route('academic-subjects.index', ['academic_level' => $academicSubject->academicLevel, 'academic_group' => $academicSubject->academicLevel->academicGroup])" class="justify-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path>
                                </svg>
                                View All Subjects
                            </x-link.secondary>
                        </div>
                    </div>
                </div>
            </div>
        </x-layouts.app>
