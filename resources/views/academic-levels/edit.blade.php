<x-layouts.app title="Edit Academic Level" :has-action="false">
    <x-slot name="breadcrumb">
        <x-breadcrumb :paths="[
            'Academic Groups' => route('academic-groups.index'),
            $academicLevel->academicGroup->name => route('academic-groups.show', ['academic_group' => $academicLevel->academicGroup]),
            'Academic Levels' => route('academic-levels.index', ['academic_group' => $academicLevel->academicGroup]),
            $academicLevel->name => route('academic-levels.show', ['academic_level' => $academicLevel, 'academic_group' => $academicLevel->academicGroup]),
            'Edit' => null,
        ]" />
    </x-slot>

    <div class="max-w-2xl mx-auto space-y-6">
        <!-- Header Section -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center space-x-4">
                <div class="p-3 bg-blue-100 rounded-full">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                    </svg>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Edit Academic Level</h1>
                    <p class="text-gray-600">Update the details for {{ $academicLevel->name }}</p>
                </div>
            </div>
        </div>

        <!-- Context Information -->
        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-blue-700">
                        This academic level belongs to <strong>{{ $academicLevel->academicGroup->name }}</strong>
                        @if($academicLevel->academic_subjects_count > 0)
                            and currently has <strong>{{ $academicLevel->academic_subjects_count }}</strong>
                            {{ Str::plural('subject', $academicLevel->academic_subjects_count) }} associated with it.
                        @endif
                    </p>
                </div>
            </div>
        </div>

        <!-- Edit Form -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">Level Information</h2>
                <p class="text-sm text-gray-600 mt-1">Update the basic information for this academic level.</p>
            </div>

            <form method="POST" action="{{ route('academic-levels.update', ['academic_level' => $academicLevel, 'academic_group' => $academicLevel->academicGroup]) }}" class="p-6 space-y-6">
                @csrf
                @method('PATCH')

                <!-- Name Field -->
                <div class="space-y-2">
                    <x-form.input
                        name="name"
                        type="text"
                        :value="$academicLevel->name"
                        placeholder="e.g., Form 1, Grade 10, Year 7"
                        class="w-full"
                        required
                    />
                    <p class="text-xs text-gray-500">The internal name used to identify this academic level.</p>
                    @error('name')
                    <p class="text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Label Field -->
                <div class="space-y-2">
                    <x-form.input
                        name="label"
                        type="text"
                        :value="$academicLevel->label"
                        placeholder="e.g., Form One, Tenth Grade, Year Seven"
                        class="w-full"
                        required
                    />
                    <p class="text-xs text-gray-500">The user-friendly name displayed to students and teachers.</p>
                    @error('label')
                    <p class="text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Academic Group (Read-only display) -->
                <div class="space-y-2">
                    <label class="block text-sm font-medium text-gray-700">Academic Group</label>
                    <div class="flex items-center space-x-3 p-3 bg-gray-50 rounded-lg border border-gray-200">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-2M7 21H5m2 0h2m6-11V7h-2v3m0 0V7h2v3m-2 0h2v3m-2-3h-2v3"></path>
                        </svg>
                        <span class="text-gray-900 font-medium">{{ $academicLevel->academicGroup->name }}</span>
                        <span class="text-xs text-gray-500 bg-gray-200 px-2 py-1 rounded-full">Cannot be changed</span>
                    </div>
                    <p class="text-xs text-gray-500">To move this level to a different group, contact your administrator.</p>
                </div>

                <!-- Action Buttons -->
                <div class="flex items-center justify-between pt-6 border-t border-gray-200">
                    <div class="flex items-center space-x-4">
                        <x-link.secondary :to="route('academic-levels.show', ['academic_level' => $academicLevel, 'academic_group' => $academicLevel->academicGroup])">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                            </svg>
                            Cancel
                        </x-link.secondary>

                        <x-link.secondary :to="route('academic-levels.index', ['academic_group' => $academicLevel->academicGroup])">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path>
                            </svg>
                            View All Levels
                        </x-link.secondary>
                    </div>

                    <x-button.primary type="submit" class="inline-flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Update Academic Level
                    </x-button.primary>
                </div>
            </form>
        </div>

        <!-- Warning Section (if there are subjects) -->
        @if($academicLevel->academic_subjects_count > 0)
            <div class="bg-amber-50 border border-amber-200 rounded-lg p-4">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L4.082 15.5c-.77.833.192 2.5 1.732 2.5z"></path>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-amber-800">Important Note</h3>
                        <p class="text-sm text-amber-700 mt-1">
                            Changing the name or label will affect how this level appears across the system, including in:
                        </p>
                        <ul class="text-sm text-amber-700 mt-2 list-disc list-inside space-y-1">
                            <li>Subject listings and navigation</li>
                            <li>Student and teacher dashboards</li>
                            <li>Reports and analytics</li>
                            <li>Academic transcripts</li>
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        <!-- Quick Actions -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">Quick Actions</h2>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <x-link.secondary :to="route('academic-subjects.index', ['academic_level' => $academicLevel, 'academic_group' => $academicLevel->academicGroup])" class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors duration-200">
                        <svg class="w-5 h-5 text-gray-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                        </svg>
                        <div>
                            <div class="font-medium text-gray-900">Manage Subjects</div>
                            <div class="text-sm text-gray-500">{{ $academicLevel->academic_subjects_count }} subjects</div>
                        </div>
                    </x-link.secondary>

                    @can('administrate')
                        <x-link.secondary :to="route('academic-subjects.create', ['academic_level' => $academicLevel, 'academic_group' => $academicLevel->academicGroup])" class="flex items-center p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition-colors duration-200">
                            <svg class="w-5 h-5 text-gray-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                            </svg>
                            <div>
                                <div class="font-medium text-gray-900">Add Subject</div>
                                <div class="text-sm text-gray-500">Create new subject</div>
                            </div>
                        </x-link.secondary>
                    @endcan
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
