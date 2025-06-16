<x-layouts.app title="Edit Academic Subject" :has-action="false">
    <x-slot name="breadcrumb">
        <x-breadcrumb :paths="[
            'Academic Groups' => route('academic-groups.index'),
            $academicSubject->academicLevel->academicGroup->name => route('academic-groups.show', ['academic_group' => $academicSubject->academicLevel->academicGroup]),
            'Academic Levels' => route('academic-levels.index', ['academic_group' => $academicSubject->academicLevel->academicGroup]),
            $academicSubject->academicLevel->name => route('academic-levels.show', ['academic_level' => $academicSubject->academicLevel, 'academic_group' => $academicSubject->academicLevel->academicGroup]),
            'Academic Subjects' => route('academic-subjects.index', ['academic_level' => $academicSubject->academicLevel, 'academic_group' => $academicSubject->academicLevel->academicGroup]),
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
                    <h1 class="text-2xl font-bold text-gray-900">Edit Academic Subject</h1>
                    <p class="text-gray-600">Update the details for {{ $academicSubject->name }}</p>
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
                        This subject belongs to <strong>{{ $academicSubject->academicLevel->name }}</strong> in
                        <strong>{{ $academicSubject->academicLevel->academicGroup->name }}</strong>
                    </p>
                </div>
            </div>
        </div>

        <!-- Edit Form -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">Subject Information</h2>
                <p class="text-sm text-gray-600 mt-1">Update the basic information for this academic subject.</p>
            </div>

            <form method="POST" action="{{ route('academic-subjects.update', ['academic_subject' => $academicSubject, 'academic_level' => $academicSubject->academicLevel, 'academic_group' => $academicSubject->academicLevel->academicGroup]) }}" class="p-6 space-y-6">
                @csrf
                @method('PATCH')

                <!-- Name Field -->
                <div class="space-y-2">
                    <label for="name" class="block text-sm font-medium text-gray-700">
                        Subject Name <span class="text-red-500">*</span>
                    </label>
                    <x-form.input
                        name="name"
                        type="text"
                        :value="$academicSubject->name"
                        placeholder="e.g., Mathematics, English, Science"
                        class="w-full"
                        required
                    />
                    <p class="text-xs text-gray-500">The full name of the subject as it will appear across the system.</p>
                    @error('name')
                        <p class="text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Code Field -->
                <div class="space-y-2">
                    <label for="code" class="block text-sm font-medium text-gray-700">
                        Subject Code <span class="text-red-500">*</span>
                    </label>
                    <x-form.input
                        name="code"
                        type="text"
                        :value="$academicSubject->code"
                        placeholder="e.g., MATH101, ENG201"
                        class="w-full"
                        required
                    />
                    <p class="text-xs text-gray-500">A unique identifier code for this subject.</p>
                    @error('code')
                        <p class="text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Action Buttons -->
                <div class="flex items-center justify-between pt-6 border-t border-gray-200">
                    <div class="flex items-center space-x-4">
                        <x-link.secondary :to="route('academic-subjects.show', ['academic_subject' => $academicSubject, 'academic_level' => $academicSubject->academicLevel, 'academic_group' => $academicSubject->academicLevel->academicGroup])">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                            </svg>
                            Cancel
                        </x-link.secondary>

                        <x-link.secondary :to="route('academic-subjects.index', ['academic_level' => $academicSubject->academicLevel, 'academic_group' => $academicSubject->academicLevel->academicGroup])">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path>
                            </svg>
                            View All Subjects
                        </x-link.secondary>
                    </div>

                    <x-button.primary type="submit" class="inline-flex items-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Update Subject
                    </x-button.primary>
                </div>
            </form>
        </div>
    </div>
</x-layouts.app>
