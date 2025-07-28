<x-layouts.app title="Edit Subtopic">
    <x-slot name="breadcrumb">
        <x-breadcrumb :paths="[
                        'Academic Groups' => route('academic-groups.index'),
                        $academicSubject->academicLevel->academicGroup->name => route('academic-groups.show', ['academic_group' => $academicSubject->academicLevel->academicGroup]),
                        'Academic Levels' => route('academic-levels.index', ['academic_group' => $academicSubject->academicLevel->academicGroup]),
                        $academicSubject->academicLevel->name => route('academic-levels.show', ['academic_level' => $academicSubject->academicLevel, 'academic_group' => $academicSubject->academicLevel->academicGroup]),
                        'Academic Subjects' => route('academic-subjects.index', ['academic_level' => $academicSubject->academicLevel, 'academic_group' => $academicSubject->academicLevel->academicGroup]),
                        $academicSubject->name => route('academic-subjects.show', ['academic_subject' => $academicSubject, 'academic_level' => $academicSubject->academicLevel, 'academic_group' => $academicSubject->academicLevel->academicGroup]),
                        'Academic Topics' => route('academic-topics.index', ['academic_subject' => $academicSubject, 'academic_level' => $academicSubject->academicLevel, 'academic_group' => $academicSubject->academicLevel->academicGroup]),
                        $academic_topic->name => route('academic-topics.show', ['academic_topic' => $academic_topic, 'academic_subject' => $academicSubject, 'academic_level' => $academicSubject->academicLevel, 'academic_group' => $academicSubject->academicLevel->academicGroup]),
                        'Edit Subtopic' => null,
                    ]"/>
    </x-slot>

    <div class="max-w-2xl mx-auto">
        <!-- Header Section -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6 mb-6">
            <div class="flex items-center space-x-4">
                <div class="p-3 bg-blue-100 rounded-full">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">Edit Subtopic</h1>
                    <p class="text-gray-600">Update the details of the subtopic.</p>
                </div>
            </div>
        </div>

        <!-- Success/Error Messages -->
        @if(session('success'))
            <div class="bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg mb-6">
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg mb-6">
                {{ session('error') }}
            </div>
        @endif

        <!-- Form Section -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">Subtopic Details</h2>
            </div>

            <form method="POST"
                  action="{{ route('subtopics.update', [
                            'subtopic' => $subtopic,
                            'academic_topic' => $academic_topic,
                            'academic_subject' => $academicSubject,
                            'academic_level' => $academicSubject->academicLevel,
                            'academic_group' => $academicSubject->academicLevel->academicGroup
                        ]) }}"
                  class="p-6 space-y-6">
                @csrf
                @method('PUT')

                <!-- Parent Topic Information -->
                <div class="bg-gray-50 rounded-lg p-4 mb-4">
                    <div class="text-sm text-gray-600">
                        <span class="font-medium">Parent Topic:</span>
                        <span class="ml-1 text-gray-900">{{ $academic_topic->name }}</span>
                    </div>
                    <div class="text-sm text-gray-500 mt-1">
                        <span class="font-medium">Subject:</span>
                        <span class="ml-1">{{ $academicSubject->name }}</span>
                        <span class="mx-2">•</span>
                        <span class="font-medium">Level:</span>
                        <span class="ml-1">{{ $academicSubject->academicLevel->name }}</span>
                    </div>
                </div>

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
                        :value="old('name', $subtopic->name)"
                        placeholder="Enter subtopic name"
                        required
                        :has-label="false"
                        autofocus
                        class="w-full"
                    />
                    @error('name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="text-xs text-gray-500">Enter a unique name for this subtopic within the parent topic.</p>
                </div>

                <!-- Slug Input -->
                <div class="space-y-2">
                    <label for="slug" class="block text-sm font-medium text-gray-700">
                        Slug
                        <span class="text-gray-400 text-xs">(Optional)</span>
                    </label>
                    <x-form.input
                        id="slug"
                        name="slug"
                        type="text"
                        :value="old('slug', $subtopic->slug)"
                        placeholder="Auto-generated from name if empty"
                        :has-label="false"
                        class="w-full"
                    />
                    @error('slug')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="text-xs text-gray-500">URL-friendly version of the name. Leave empty to auto-generate.</p>
                </div>

                <!-- Description Input -->
                <div class="space-y-2">
                    <label for="description" class="block text-sm font-medium text-gray-700">
                        Description
                        <span class="text-gray-400 text-xs">(Optional)</span>
                    </label>
                    <x-form.textarea
                        id="description"
                        name="description"
                        :value="old('description', $subtopic->description)"
                        placeholder="Brief description of the subtopic (optional)"
                        rows="4"
                        label=""
                        class="w-full"
                    />
                    @error('description')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                    <p class="text-xs text-gray-500">Provide additional context about this subtopic.</p>
                </div>

                <!-- Action Buttons -->
                <div class="flex items-center justify-between pt-6 border-t border-gray-200">
                    <div class="text-sm text-gray-500">
                        <span class="font-medium">Last updated:</span>
                        {{ $subtopic->updated_at->format('M j, Y \a\t g:i A') }}
                    </div>

                    <div class="flex items-center space-x-4">
                        <x-link.secondary
                            :to="route('academic-topics.show', [
                                            'academic_topic' => $academic_topic,
                                            'academic_subject' => $academicSubject,
                                            'academic_level' => $academicSubject->academicLevel,
                                            'academic_group' => $academicSubject->academicLevel->academicGroup
                                        ])"
                        >
                            Cancel
                        </x-link.secondary>

                        <x-button.primary type="submit">
                            <span class="flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M5 13l4 4L19 7"/>
                                </svg>
                                Update Subtopic
                            </span>
                        </x-button.primary>
                    </div>
                </div>
            </form>
        </div>

        <!-- Additional Information -->
        <div class="mt-6 bg-blue-50 border border-blue-200 rounded-lg p-4">
            <div class="flex">
                <div class="flex-shrink-0">
                    <svg class="h-5 w-5 text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                              d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z"
                              clip-rule="evenodd"></path>
                    </svg>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-blue-800">
                        Tips for editing subtopics
                    </h3>
                    <div class="mt-2 text-sm text-blue-700">
                        <ul class="list-disc pl-5 space-y-1">
                            <li>Keep names concise and descriptive</li>
                            <li>Slugs are used in URLs, so they should be lowercase and contain only letters, numbers,
                                and dashes
                            </li>
                            <li>Descriptions help students understand what the subtopic covers</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
        <script>
            // Auto-generate slug from name if slug field is empty
            document.getElementById('name').addEventListener('input', function () {
                const slugField = document.getElementById('slug');
                if (!slugField.value.trim()) {
                    const slug = this.value
                        .toLowerCase()
                        .replace(/[^a-z0-9\s-]/g, '')
                        .replace(/\s+/g, '-')
                        .replace(/-+/g, '-')
                        .trim('-');
                    slugField.value = slug;
                }
            });
        </script>
    @endpush
</x-layouts.app>

