<div>
    @if($showSuccess)
        <!-- Success Message -->
        <div class="bg-green-50 dark:bg-green-900/30 border border-green-200 dark:border-green-800 rounded-lg p-6 text-center">
            <div class="flex justify-center mb-4">
                <div class="w-16 h-16 bg-green-100 dark:bg-green-800 rounded-full flex items-center justify-center">
                    <svg class="w-8 h-8 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
            </div>
            <h3 class="text-lg font-semibold text-green-800 dark:text-green-200 mb-2">Resource Updated Successfully!</h3>
            <p class="text-green-600 dark:text-green-400 mb-4">Your resource "{{ $resource->title }}" has been updated.</p>
            <div class="flex justify-center gap-4">
                <a href="{{ route('educational-resources.show', $resource) }}" class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg transition-colors">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                    </svg>
                    View Media
                </a>
                <a href="{{ route('educational-resources.index') }}" class="inline-flex items-center px-4 py-2 bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-800 dark:text-gray-200 rounded-lg transition-colors">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path>
                    </svg>
                    Back to Media Lab
                </a>
            </div>
        </div>
    @else
        <!-- Edit Form -->
        <form wire:submit.prevent="updateResource" class="space-y-6">
            <!-- Title -->
            <div>
                <label for="title" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    Title <span class="text-red-500">*</span>
                </label>
                <input
                    type="text"
                    id="title"
                    wire:model="title"
                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    placeholder="Enter a descriptive title for your resource"
                >
                @error('title')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Description -->
            <div>
                <x-form.markdown-editor
                    name="description"
                    label="Description"
                    wire:model="description"
                    :height="250"
                    info="Provide a detailed description of the resource content"
                />
                @error('description')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Tags -->
            <div>
                <label for="tagsInput" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    Tags
                </label>
                <input
                    type="text"
                    id="tagsInput"
                    wire:model="tagsInput"
                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    placeholder="Enter tags separated by commas (e.g., algebra, equations, math)"
                >
                <p class="mt-1 text-xs text-gray-500 dark:text-gray-400">Separate multiple tags with commas</p>
                @error('tagsInput')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Current File Info -->
            <div class="bg-gray-50 dark:bg-gray-800/50 rounded-lg p-4">
                <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-3">Current File</h3>
                <div class="flex items-center gap-4">
                    <div class="flex-shrink-0">
                        @switch($resource->format)
                            @case('video')
                                <div class="w-12 h-12 bg-purple-100 dark:bg-purple-900/30 rounded-lg flex items-center justify-center">
                                    <svg class="w-6 h-6 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path>
                                    </svg>
                                </div>
                                @break
                            @case('pdf')
                                <div class="w-12 h-12 bg-red-100 dark:bg-red-900/30 rounded-lg flex items-center justify-center">
                                    <svg class="w-6 h-6 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                    </svg>
                                </div>
                                @break
                            @case('image')
                                <div class="w-12 h-12 bg-green-100 dark:bg-green-900/30 rounded-lg flex items-center justify-center">
                                    <svg class="w-6 h-6 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                </div>
                                @break
                            @default
                                <div class="w-12 h-12 bg-blue-100 dark:bg-blue-900/30 rounded-lg flex items-center justify-center">
                                    <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                </div>
                        @endswitch
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-900 dark:text-white truncate">{{ $resource->file_name }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400">
                            {{ $resource->formatted_file_size }} • {{ ucfirst($resource->format) }}
                        </p>
                    </div>
                </div>
            </div>

            <!-- File Upload (Optional) -->
            <div>
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                    Replace File <span class="text-gray-400">(Optional)</span>
                </label>
                <div
                    x-data="{ isDragging: false }"
                    x-on:dragover.prevent="isDragging = true"
                    x-on:dragleave.prevent="isDragging = false"
                    x-on:drop.prevent="isDragging = false"
                    class="relative"
                >
                    <label
                        :class="isDragging ? 'border-blue-500 bg-blue-50 dark:bg-blue-900/20' : 'border-gray-300 dark:border-gray-600'"
                        class="flex flex-col items-center justify-center w-full h-32 border-2 border-dashed rounded-lg cursor-pointer bg-gray-50 dark:bg-gray-800 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors"
                    >
                        <div class="flex flex-col items-center justify-center pt-5 pb-6">
                            @if($file)
                                <svg class="w-8 h-8 mb-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                <p class="mb-1 text-sm text-gray-700 dark:text-gray-300">
                                    <span class="font-semibold">{{ $file->getClientOriginalName() }}</span>
                                </p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                    {{ number_format($file->getSize() / 1024 / 1024, 2) }} MB - Will replace current file
                                </p>
                            @else
                                <svg class="w-8 h-8 mb-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                                </svg>
                                <p class="mb-1 text-sm text-gray-500 dark:text-gray-400">
                                    <span class="font-semibold">Click to upload a new file</span> or drag and drop
                                </p>
                                <p class="text-xs text-gray-500 dark:text-gray-400">
                                    Leave empty to keep the current file
                                </p>
                            @endif
                        </div>
                        <input type="file" wire:model="file" class="hidden" accept="video/*,application/pdf,image/*,text/*,.doc,.docx,.txt,.rtf">
                    </label>
                </div>
                <div wire:loading wire:target="file" class="mt-2">
                    <div class="flex items-center text-sm text-blue-600 dark:text-blue-400">
                        <svg class="animate-spin -ml-1 mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Uploading file...
                    </div>
                </div>
                @error('file')
                    <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            <!-- Academic Hierarchy -->
            <div class="bg-gray-50 dark:bg-gray-800/50 rounded-lg p-4 space-y-4">
                <h3 class="text-sm font-semibold text-gray-700 dark:text-gray-300 mb-2">Academic Classification</h3>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <!-- Academic Group -->
                    <div>
                        <label for="academicGroupId" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Academic Group <span class="text-red-500">*</span>
                        </label>
                        <select
                            id="academicGroupId"
                            wire:model.live="academicGroupId"
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        >
                            <option value="">Select Group</option>
                            @foreach($academicGroups as $group)
                                <option value="{{ $group->id }}">{{ $group->name }}</option>
                            @endforeach
                        </select>
                        @error('academicGroupId')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Academic Level -->
                    <div>
                        <label for="academicLevelId" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Academic Level <span class="text-red-500">*</span>
                        </label>
                        <select
                            id="academicLevelId"
                            wire:model.live="academicLevelId"
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            @if(!$academicGroupId) disabled @endif
                        >
                            <option value="">Select Level</option>
                            @foreach($academicLevels as $level)
                                <option value="{{ $level->id }}">{{ $level->name }}</option>
                            @endforeach
                        </select>
                        @error('academicLevelId')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Subject -->
                    <div>
                        <label for="academicSubjectId" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                            Subject <span class="text-red-500">*</span>
                        </label>
                        <select
                            id="academicSubjectId"
                            wire:model.live="academicSubjectId"
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            @if(!$academicLevelId) disabled @endif
                        >
                            <option value="">Select Subject</option>
                            @foreach($academicSubjects as $subject)
                                <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                            @endforeach
                        </select>
                        @error('academicSubjectId')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <!-- Topics and Subtopics (Optional) -->
                @if($academicSubjectId && $topics->count() > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                        <!-- Topics -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                Topics (Optional)
                            </label>
                            <div class="max-h-40 overflow-y-auto border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 p-2 space-y-1">
                                @foreach($topics as $topic)
                                    <label class="flex items-center p-2 hover:bg-gray-50 dark:hover:bg-gray-700 rounded cursor-pointer">
                                        <input
                                            type="checkbox"
                                            wire:model.live="selectedTopics"
                                            value="{{ $topic->id }}"
                                            class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                                        >
                                        <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">{{ $topic->name }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <!-- Subtopics -->
                        @if(count($selectedTopics) > 0 && $subtopics->count() > 0)
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                    Subtopics (Optional)
                                </label>
                                <div class="max-h-40 overflow-y-auto border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-800 p-2 space-y-1">
                                    @foreach($subtopics as $subtopic)
                                        <label class="flex items-center p-2 hover:bg-gray-50 dark:hover:bg-gray-700 rounded cursor-pointer">
                                            <input
                                                type="checkbox"
                                                wire:model="selectedSubtopics"
                                                value="{{ $subtopic->id }}"
                                                class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                                            >
                                            <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">{{ $subtopic->name }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                @endif
            </div>

            <!-- School Scoping -->
            @if(auth()->user()->school_id)
                <div class="flex items-center p-4 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-lg">
                    <label class="flex items-center cursor-pointer">
                        <input
                            type="checkbox"
                            wire:model="isSchoolScoped"
                            class="w-4 h-4 text-yellow-600 border-gray-300 rounded focus:ring-yellow-500"
                        >
                        <span class="ml-2 text-sm text-yellow-800 dark:text-yellow-200">
                            <strong>Restrict to my school only</strong> - This resource will only be visible to users in your school
                        </span>
                    </label>
                </div>
            @endif

            <!-- Submit Button -->
            <div class="flex justify-end gap-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                <a href="{{ route('educational-resources.show', $resource) }}" class="px-6 py-2 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors">
                    Cancel
                </a>
                <button
                    type="button"
                    wire:click="updateResource"
                    class="px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors flex items-center"
                    wire:loading.attr="disabled"
                    wire:loading.class="opacity-50 cursor-not-allowed"
                    wire:target="updateResource"
                >
                    <span wire:loading.remove wire:target="updateResource">
                        <svg class="w-5 h-5 mr-2 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Save Changes
                    </span>
                    <span wire:loading wire:target="updateResource" class="flex items-center">
                        <svg class="animate-spin -ml-1 mr-2 h-5 w-5" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Saving...
                    </span>
                </button>
            </div>
        </form>
    @endif
</div>
