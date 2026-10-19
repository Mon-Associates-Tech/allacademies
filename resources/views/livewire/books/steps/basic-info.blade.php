<div>
    <x-exam.callout tone="primary" class="mb-8">
        <div class="flex items-center gap-3">
            <span class="flex items-center justify-center w-8 h-8 bg-primary text-on-primary rounded-full text-sm font-bold shrink-0">1</span>
            <div>
                <h3 class="font-bold text-ink">Basic Information</h3>
                <p class="text-sm text-ink-soft">Enter the fundamental details about the book</p>
            </div>
        </div>
    </x-exam.callout>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-0">
        <!-- Title -->
        <x-exam.field label="Book Title" :required="true" class="lg:col-span-2">
            <x-exam.input type="text" wire:model.live="title" placeholder="Enter the complete book title" error-key="title" />
        </x-exam.field>

        <!-- Slug -->
        <x-exam.field label="URL Slug">
            <x-exam.input type="text" wire:model="slug" {{ $mode === 'edit' ? '' : 'readonly' }}
                           :class="($mode === 'create' ? 'bg-surface-2 cursor-not-allowed' : '')" />
            @if($mode === 'create')
                <p class="mt-1 font-mono text-[11px] text-muted">Auto-generated from title</p>
            @endif
        </x-exam.field>

        <!-- Author Selection -->
        <x-exam.field label="Author" :required="true">
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
                    <x-exam.button variant="ghost" wire:click="toggleNewAuthorForm" class="w-full justify-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        Add New Author
                    </x-exam.button>
                </div>
            @else
                <x-exam.callout tone="primary">
                    <div class="flex items-center justify-between mb-3">
                        <h4 class="font-medium text-ink">Add New Author</h4>
                        <x-exam.button variant="ghost" wire:click="toggleNewAuthorForm">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </x-exam.button>
                    </div>
                    <x-exam.input type="text" wire:model="newAuthorName" placeholder="Author name" error-key="newAuthorName" />
                    <x-exam.button wire:click="createNewAuthor" class="w-full justify-center mt-3">Create Author</x-exam.button>
                </x-exam.callout>
            @endif
            @error('authorId') <p class="mt-2 font-mono text-[11.5px] text-accent">▲ {{ $message }}</p> @enderror
        </x-exam.field>

        <!-- Category Selection -->
        <x-exam.field label="Categories" :required="true" class="lg:col-span-2">
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
                    <x-exam.button variant="ghost" wire:click="toggleNewCategoryForm" class="w-full justify-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        Add New Category
                    </x-exam.button>
                </div>
            @else
                <x-exam.callout tone="primary">
                    <div class="flex items-center justify-between mb-3">
                        <h4 class="font-medium text-ink">Add New Category</h4>
                        <x-exam.button variant="ghost" wire:click="toggleNewCategoryForm">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </x-exam.button>
                    </div>
                    <x-exam.input type="text" wire:model="newCategoryName" placeholder="Category name" error-key="newCategoryName" />
                    <x-exam.input type="text" wire:model="newCategoryDescription" placeholder="Brief description..." error-key="newCategoryDescription" />
                    <x-exam.button wire:click="createNewCategory" class="w-full justify-center mt-3">Create Category</x-exam.button>
                </x-exam.callout>
            @endif
            @error('bookCategoryIds') <p class="mt-2 font-mono text-[11.5px] text-accent">▲ {{ $message }}</p> @enderror
        </x-exam.field>

        <!-- Age Groups -->
        <x-exam.field label="Age Groups">
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
        </x-exam.field>

        <!-- Academic Groups -->
        <x-exam.field label="Academic Groups">
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
        </x-exam.field>

        <!-- Academic Levels -->
        <x-exam.field label="Academic Levels">
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
        </x-exam.field>

        <!-- Academic Subjects -->
        <x-exam.field label="Academic Subjects">
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
        </x-exam.field>
    </div>
</div>
