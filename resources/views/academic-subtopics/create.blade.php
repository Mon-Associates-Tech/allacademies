<x-layouts.app title="Create New Subtopic">
    <x-slot name="breadcrumb">
        <x-breadcrumb :paths="[
                        'Academic Groups' => route('academic-groups.index'),
                        $academicSubject->academicLevel->academicGroup->name => route('academic-groups.show', ['academic_group' => $academicSubject->academicLevel->academicGroup]),
                        'Academic Levels' => route('academic-levels.index', ['academic_group' => $academicSubject->academicLevel->academicGroup, 'academic_subject' => $academicSubject]),
                        $academicSubject->academicLevel->name => route('academic-levels.show', ['academic_level' => $academicSubject->academicLevel, 'academic_group' => $academicSubject->academicLevel->academicGroup]),
                        'Academic Subjects' => route('academic-subjects.index', ['academic_level' => $academicSubject->academicLevel, 'academic_group' => $academicSubject->academicLevel->academicGroup]),
                        $academicSubject->name => route('academic-subjects.show', ['academic_subject' => $academicSubject, 'academic_level' => $academicSubject->academicLevel, 'academic_group' => $academicSubject->academicLevel->academicGroup]),
                        'Academic Topics' => route('academic-topics.index', ['academic_subject' => $academicSubject, 'academic_level' => $academicSubject->academicLevel, 'academic_group' => $academicSubject->academicLevel->academicGroup]),
                        'New Subtopic' => null,
                    ]"/>
    </x-slot>

    <div class="max-w-2xl mx-auto">
        <!-- Header Section -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
            <div class="flex items-center space-x-4">
                <div class="p-3 bg-blue-100 rounded-full">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Create New Subtopic</h1>
                    <p class="text-gray-600">Add a new subtopic to your academic content</p>
                </div>
            </div>
        </div>

        <!-- Form Section -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">Subtopic Details</h2>
            </div>

            <form method="POST" action="{{ route('subtopics.store', [
                            'academic_topic' => $academic_topic,
                            'academic_subject' => getRouteParameter('academic_subject'),
                            'academic_level' => getRouteParameter('academic_level'),
                            'academic_group' => getRouteParameter('academic_group')
                        ]) }}" class="p-6 space-y-6">
                @csrf

                <!-- Parent Topic Information -->
                <div class="bg-gray-50 rounded-lg p-4 mb-4">
                    <div class="text-sm text-gray-600">
                        <span class="font-medium">Parent Topic:</span>
                        <span class="ml-1">{{ $academic_topic->name }}</span>
                    </div>
                </div>

                <!-- Name Input -->
                <!-- Name Input -->
                <div class="space-y-2">
                    <label for="name" class="block text-sm font-medium text-gray-700">
                        Subtopic Name
                        <span class="text-red-500">*</span>
                    </label>
                    <x-form.input
                        id="name"
                        name="name"
                        type="text"
                        :value="old('name')"
                        placeholder="Enter subtopic name"
                        required
                        autofocus
                    />
                    @error('name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Description Input (Optional) -->
                <div class="space-y-2">
                    <label for="description" class="block text-sm font-medium text-gray-700">
                        Subtopic Name

                    </label>
                    <x-form.textarea
                        id="description"
                        name="description"
                        :value="old('description')"
                        placeholder="Brief description of the subtopic (optional)"
                        rows="3"
                    />
                    @error('description')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Action Buttons -->
                <div class="flex items-center justify-end space-x-4 pt-4">
                    <x-link.secondary
                        :to="route('academic-topics.show', [
                                        'academic_topic' => $academic_topic,
                                        'academic_subject' => getRouteParameter('academic_subject'),
                                        'academic_level' => getRouteParameter('academic_level'),
                                        'academic_group' => getRouteParameter('academic_group')
                                    ])"
                    >
                        Cancel
                    </x-link.secondary>

                    <x-button.primary type="submit">
                                    <span class="flex items-center">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                        </svg>
                                        Create Subtopic
                                    </span>
                    </x-button.primary>
                </div>
            </form>
        </div>
    </div>
</x-layouts.app>
