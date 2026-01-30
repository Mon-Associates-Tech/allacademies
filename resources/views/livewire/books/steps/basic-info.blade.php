<div>
    <div class="flex items-center mb-8">
        <div class="flex items-center justify-center w-10 h-10 bg-blue-600 text-white rounded-full text-sm font-bold mr-4">1</div>
        <div>
            <h3 class="text-xl font-bold text-gray-900">Basic Information</h3>
            <p class="text-gray-600">Enter the fundamental details about the book</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Title -->
        <div class="lg:col-span-2">
            <label class="block text-sm font-semibold text-gray-700 mb-3">
                Book Title <span class="text-red-500">*</span>
            </label>
            <input type="text" wire:model.live="title" placeholder="Enter the complete book title"
                   class="block w-full px-4 py-3 border border-gray-300 rounded-xl shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            @error('title') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        <!-- Slug -->
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-3">URL Slug</label>
            <input type="text" wire:model="slug" {{ $mode === 'edit' ? '' : 'readonly' }}
                   class="block w-full px-4 py-3 border border-gray-300 rounded-xl {{ $mode === 'edit' ? 'bg-white' : 'bg-gray-50 cursor-not-allowed' }}">
            @if($mode === 'create')
                <p class="mt-1 text-xs text-gray-500">Auto-generated from title</p>
            @endif
        </div>

        <!-- Author Selection -->
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-3">
                Author <span class="text-red-500">*</span>
            </label>
            @if(!$showNewAuthorForm)
                <div class="space-y-3">
                    <div wire:ignore>
                        @livewire('common.searchable-multi-select', [
                            'selected' => $authorId ? [$authorId] : [],
                            'multiple' => false,
                            'items' => $authors->map(fn($author) => ['id' => $author->id, 'name' => $author->name ?? $author->user?->name ?? 'Unknown'])->toArray(),
                            'labelKey' => 'name',
                            'placeholder' => 'Choose or search an author',
                            'valueKey' => 'id',
                            'name' => 'authorId',
                        ])
                    </div>
                    <button type="button" wire:click="toggleNewAuthorForm"
                            class="w-full flex items-center justify-center px-4 py-2 border border-dashed border-blue-300 rounded-xl text-sm font-medium text-blue-600 hover:border-blue-400">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        Add New Author
                    </button>
                </div>
            @else
                <div class="space-y-4 p-4 bg-blue-50 rounded-xl border border-blue-200">
                    <div class="flex items-center justify-between">
                        <h4 class="font-medium text-blue-900">Add New Author</h4>
                        <button type="button" wire:click="toggleNewAuthorForm" class="text-blue-600 hover:text-blue-800">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                    <div>
                        <input type="text" wire:model="newAuthorName" placeholder="Author name"
                               class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                        @error('newAuthorName') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <button type="button" wire:click="createNewAuthor"
                            class="w-full bg-blue-600 text-white py-2 px-4 rounded-lg hover:bg-blue-700">
                        Create Author
                    </button>
                </div>
            @endif
            @error('authorId') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        <!-- Category Selection -->
        <div class="lg:col-span-2">
            <label class="block text-sm font-semibold text-gray-700 mb-3">
                Categories <span class="text-red-500">*</span>
            </label>
            @if(!$showNewCategoryForm)
                <div class="space-y-3">
                    <div wire:ignore>
                        @livewire('common.searchable-multi-select', [
                            'selected' => $bookCategoryIds ?: [],
                            'multiple' => true,
                            'items' => $bookCategories->map(fn($category) => ['id' => $category->id, 'name' => $category->name])->toArray(),
                            'labelKey' => 'name',
                            'placeholder' => 'Choose or search categories',
                            'valueKey' => 'id',
                            'name' => 'bookCategoryIds',
                        ])
                    </div>
                    <button type="button" wire:click="toggleNewCategoryForm"
                            class="w-full flex items-center justify-center px-4 py-2 border border-dashed border-purple-300 rounded-xl text-sm font-medium text-purple-600 hover:border-purple-400">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        Add New Category
                    </button>
                </div>
            @else
                <div class="space-y-4 p-4 bg-purple-50 rounded-xl border border-purple-200">
                    <div class="flex items-center justify-between">
                        <h4 class="font-medium text-purple-900">Add New Category</h4>
                        <button type="button" wire:click="toggleNewCategoryForm" class="text-purple-600 hover:text-purple-800">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                    <div>
                        <input type="text" wire:model="newCategoryName" placeholder="Category name"
                               class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500">
                        @error('newCategoryName') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <textarea wire:model="newCategoryDescription" rows="3" placeholder="Brief description..."
                                  class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 resize-none"></textarea>
                        @error('newCategoryDescription') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <button type="button" wire:click="createNewCategory"
                            class="w-full bg-purple-600 text-white py-2 px-4 rounded-lg hover:bg-purple-700">
                        Create Category
                    </button>
                </div>
            @endif
            @error('bookCategoryIds') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
        </div>

        <!-- Age Groups -->
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-3">Age Groups</label>
            <div wire:ignore>
                @livewire('common.searchable-multi-select', [
                    'selected' => $ageGroups ?: [],
                    'multiple' => true,
                    'items' => collect($this->ageGroupOptions)->map(fn($label, $value) => ['id' => $value, 'name' => $label])->values()->toArray(),
                    'labelKey' => 'name',
                    'placeholder' => 'Select age groups',
                    'valueKey' => 'id',
                    'name' => 'ageGroups',
                ])
            </div>
        </div>

        <!-- Academic Groups -->
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-3">Academic Groups</label>
            <div wire:ignore>
                @livewire('common.searchable-multi-select', [
                    'selected' => $academicGroupIds ?: [],
                    'multiple' => true,
                    'items' => $academicGroups->map(fn($group) => ['id' => $group->id, 'name' => $group->name])->toArray(),
                    'labelKey' => 'name',
                    'placeholder' => 'Select academic groups',
                    'valueKey' => 'id',
                    'name' => 'academicGroupIds',
                ])
            </div>
        </div>

        <!-- Academic Levels -->
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-3">Academic Levels</label>
            <div wire:ignore>
                @livewire('common.searchable-multi-select', [
                    'selected' => $academicLevelIds ?: [],
                    'multiple' => true,
                    'items' => $academicLevels->map(fn($level) => ['id' => $level->id, 'name' => $level->name])->toArray(),
                    'labelKey' => 'name',
                    'placeholder' => 'Select academic levels',
                    'valueKey' => 'id',
                    'name' => 'academicLevelIds',
                ])
            </div>
        </div>

        <!-- Academic Subjects -->
        <div>
            <label class="block text-sm font-semibold text-gray-700 mb-3">Academic Subjects</label>
            <div wire:ignore>
                @livewire('common.searchable-multi-select', [
                    'selected' => $academicSubjectIds ?: [],
                    'multiple' => true,
                    'items' => $academicSubjects->map(fn($subject) => ['id' => $subject->id, 'name' => $subject->name])->toArray(),
                    'labelKey' => 'name',
                    'placeholder' => 'Select academic subjects',
                    'valueKey' => 'id',
                    'name' => 'academicSubjectIds',
                ])
            </div>
        </div>
    </div>
</div>
