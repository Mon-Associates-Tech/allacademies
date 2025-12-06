<div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm">
    <!-- Tabs -->
    <div class="px-6 pt-1">
        @php
            $tabs = [
                ['key' => 'book-notes', 'label' => Str::limit($book->title, 40)],
                ['key' => 'all-notes', 'label' => 'All  Notes']
            ];
        @endphp
        <livewire:scrollable-tabs
            :tabs="$tabs"
            :activeTab="$activeTab"
        />
    </div>

    <!-- Search Bar -->
    <div class="px-6 pt-4">
        <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
            </div>
            <input
                type="text"
                wire:model.live.debounce.300ms="searchTerm"
                placeholder="Search notes..."
                class="block w-full pl-10 pr-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg leading-5 bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
            >
            @if($searchTerm)
                <button
                    wire:click="$set('searchTerm', '')"
                    class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            @endif
        </div>
    </div>

    <div class="p-6">
        <!-- Book Notes Tab -->
        <div class="{{ $activeTab === 'book-notes' ? '' : 'hidden' }}">
            <!-- Book Title Display -->
            <div class="mb-6 pb-4 border-b border-gray-200 dark:border-gray-700">
                <div class="flex items-start space-x-3">
                    <svg class="w-6 h-6 text-indigo-600 dark:text-indigo-400 flex-shrink-0 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                    </svg>
                    <div class="flex-1 min-w-0">
                        <h2 class="text-xl font-bold text-gray-900 dark:text-white break-words">{{ $book->title }}</h2>
                        @if($book->author)
                            <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">by {{ $book->author_name }}</p>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Add New Note Section -->
            <div class="mb-8 bg-gradient-to-br from-indigo-50 to-blue-50 dark:from-gray-800 dark:to-gray-750 rounded-lg p-6 border border-indigo-100 dark:border-gray-700">
                <div class="flex items-center mb-4">
                    <svg class="w-6 h-6 text-indigo-600 dark:text-indigo-400 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Add New Note</h3>
                </div>
                <div class="space-y-4">
                    <x-form.markdown-editor
                        name="newNoteContent"
                        wire:model="newNoteContent"
                        :height="200"
                        label=""
                    />
                    <div class="flex justify-end">
                        <button
                            wire:click="saveNote"
                            wire:loading.attr="disabled"
                            class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white font-medium rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50 disabled:cursor-not-allowed transition-colors">
                            <svg wire:loading.remove class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <svg wire:loading class="animate-spin w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span wire:loading.remove>Save Note</span>
                            <span wire:loading>Saving...</span>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Notes List -->
            <div class="space-y-4">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                        Your Notes ({{ $this->bookNotes->total() }})
                    </h3>
                </div>

                @if($this->bookNotes->isEmpty())
                    <div class="text-center py-12 bg-gray-50 dark:bg-gray-800 rounded-lg border-2 border-dashed border-gray-300 dark:border-gray-600">
                        <svg class="mx-auto h-16 w-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <h3 class="mt-4 text-base font-medium text-gray-900 dark:text-white">
                            @if($searchTerm)
                                No notes found
                            @else
                                No notes yet
                            @endif
                        </h3>
                        <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                            @if($searchTerm)
                                Try adjusting your search terms.
                            @else
                                Get started by writing your first note about this book.
                            @endif
                        </p>
                    </div>
                @else
                    @foreach($this->bookNotes as $note)
                        @php
                            $isExpanded = $this->isNoteExpanded($note->id);
                            $contentLength = strlen(strip_tags($note->content));
                            $hasLongContent = $contentLength > 300;
                        @endphp
                        <div class="bg-white dark:bg-gray-750 border border-gray-200 dark:border-gray-700 rounded-lg p-5 hover:shadow-md transition-shadow">
                            @if($editingNoteId === $note->id)
                                <div class="space-y-4">
                                    <x-form.markdown-editor
                                        name="editingContent"
                                        wire:model="editingContent"
                                        :height="200"
                                        label=""
                                    />
                                    <div class="flex justify-end space-x-2">
                                        <button
                                            wire:click="cancelEdit"
                                            class="inline-flex items-center px-4 py-2 text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-colors">
                                            Cancel
                                        </button>
                                        <button
                                            wire:click="updateNote"
                                            wire:loading.attr="disabled"
                                            class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 disabled:opacity-50 transition-colors">
                                            <svg wire:loading class="animate-spin -ml-1 mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                            </svg>
                                            <span wire:loading.remove>Update Note</span>
                                            <span wire:loading>Updating...</span>
                                        </button>
                                    </div>
                                </div>
                            @else
                                <div class="{{ $hasLongContent && !$isExpanded ? 'max-h-40 overflow-hidden relative' : '' }}">
                                    <x-form.markdown-with-math
                                        :content="$note->content"
                                        class="prose prose-sm prose-gray dark:prose-invert max-w-none"
                                    />
                                    @if($hasLongContent && !$isExpanded)
                                        <div class="absolute bottom-0 left-0 right-0 h-20 bg-gradient-to-t from-white dark:from-gray-750 to-transparent pointer-events-none"></div>
                                    @endif
                                </div>

                                @if($hasLongContent)
                                    <button
                                        wire:click="toggleNote({{ $note->id }})"
                                        class="inline-flex items-center text-sm font-medium text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 dark:hover:text-indigo-300 mt-3 transition-colors">
                                        <span>{{ $isExpanded ? 'Read less' : 'Read more' }}</span>
                                        <svg class="w-4 h-4 ml-1 transition-transform {{ $isExpanded ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                        </svg>
                                    </button>
                                @endif

                                <div class="flex items-center justify-between pt-3 mt-3 border-t border-gray-200 dark:border-gray-700">
                                    <div class="flex items-center text-xs text-gray-500 dark:text-gray-400">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        <span>{{ $note->updated_at->diffForHumans() }}</span>
                                    </div>
                                    <div class="flex space-x-3">
                                        <button
                                            wire:click="editNote({{ $note->id }})"
                                            class="inline-flex items-center text-sm font-medium text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 dark:hover:text-indigo-300 transition-colors">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                            </svg>
                                            Edit
                                        </button>
                                        <button
                                            wire:click="deleteNote({{ $note->id }})"
                                            wire:confirm="Are you sure you want to delete this note? This action cannot be undone."
                                            class="inline-flex items-center text-sm font-medium text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300 transition-colors">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                            Delete
                                        </button>
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endforeach

                    <!-- Pagination -->
                    <div class="mt-6">
                        {{ $this->bookNotes->links() }}
                    </div>
                @endif
            </div>
        </div>

        <!-- All Notes Tab -->
        <div class="{{ $activeTab === 'all-notes' ? '' : 'hidden' }}">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                    All Your Notes ({{ $this->userNotes->total() }})
                </h3>
            </div>

            @if($this->userNotes->isEmpty())
                <div class="text-center py-12 bg-gray-50 dark:bg-gray-800 rounded-lg border-2 border-dashed border-gray-300 dark:border-gray-600">
                    <svg class="mx-auto h-16 w-16 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <h3 class="mt-4 text-base font-medium text-gray-900 dark:text-white">
                        @if($searchTerm)
                            No notes found
                        @else
                            No notes yet
                        @endif
                    </h3>
                    <p class="mt-2 text-sm text-gray-500 dark:text-gray-400">
                        @if($searchTerm)
                            Try adjusting your search terms.
                        @else
                            Start taking notes on books you read.
                        @endif
                    </p>
                </div>
            @else
                <div class="space-y-4">
                    @foreach($this->userNotes as $note)
                        @php
                            $isExpanded = $this->isNoteExpanded($note->id);
                            $contentLength = strlen(strip_tags($note->content));
                            $hasLongContent = $contentLength > 300;
                        @endphp
                        <div class="bg-white dark:bg-gray-750 border border-gray-200 dark:border-gray-700 rounded-lg p-5 hover:shadow-md transition-shadow">
                            @if($editingNoteId === $note->id)
                                <div class="space-y-4">
                                    <div class="flex items-center justify-between mb-3">
                                        <h4 class="font-semibold text-gray-900 dark:text-white flex items-center min-w-0">
                                            <svg class="w-5 h-5 mr-2 text-indigo-600 dark:text-indigo-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                            </svg>
                                            <span class="truncate" title="{{ $note->book->title ?? 'Untitled Book' }}">
                                                {{ $note->book->title ?? 'Untitled Book' }}
                                            </span>
                                        </h4>
                                    </div>
                                    <x-form.rich-editor
                                        name="editingContent"
                                        wire:model="editingContent"
                                        :height="200"
                                        label=""
                                    />
                                    <div class="flex justify-end space-x-2">
                                        <button
                                            wire:click="cancelEdit"
                                            class="inline-flex items-center px-4 py-2 text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors">
                                            Cancel
                                        </button>
                                        <button
                                            wire:click="updateNote"
                                            wire:loading.attr="disabled"
                                            class="inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700 disabled:opacity-50 transition-colors">
                                            <svg wire:loading class="animate-spin -ml-1 mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                            </svg>
                                            <span wire:loading.remove>Update Note</span>
                                            <span wire:loading>Updating...</span>
                                        </button>
                                    </div>
                                </div>
                            @else
                                <div class="flex items-start justify-between mb-3 gap-3">
                                    <h4 class="font-semibold text-gray-900 dark:text-white flex items-center min-w-0 flex-1">
                                        <svg class="w-5 h-5 mr-2 text-indigo-600 dark:text-indigo-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                        </svg>
                                        <span class="truncate" title="{{ $note->book->title ?? 'Untitled Book' }}">
                                            {{ $note->book->title ?? 'Untitled Book' }}
                                        </span>
                                    </h4>
                                    @if($note->book)
                                        <a href="{{ route('books.show', $note->book) }}"
                                           class="inline-flex items-center text-sm font-medium text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 dark:hover:text-indigo-300 transition-colors whitespace-nowrap flex-shrink-0">
                                            <span>View Book</span>
                                            <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                            </svg>
                                        </a>
                                    @endif
                                </div>

                                <div class="{{ $hasLongContent && !$isExpanded ? 'max-h-40 overflow-hidden relative' : '' }}">
                                    <x-form.markdown-with-math
                                        :content="$note->content"
                                        class="prose prose-sm prose-gray dark:prose-invert max-w-none"
                                    />
                                    @if($hasLongContent && !$isExpanded)
                                        <div class="absolute bottom-0 left-0 right-0 h-20 bg-gradient-to-t from-white dark:from-gray-750 to-transparent pointer-events-none"></div>
                                    @endif
                                </div>

                                @if($hasLongContent)
                                    <button
                                        wire:click="toggleNote({{ $note->id }})"
                                        class="inline-flex items-center text-sm font-medium text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 dark:hover:text-indigo-300 mt-3 transition-colors">
                                        <span>{{ $isExpanded ? 'Read less' : 'Read more' }}</span>
                                        <svg class="w-4 h-4 ml-1 transition-transform {{ $isExpanded ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                        </svg>
                                    </button>
                                @endif

                                <div class="flex items-center justify-between pt-3 mt-3 border-t border-gray-200 dark:border-gray-700">
                                    <div class="flex items-center text-xs text-gray-500 dark:text-gray-400">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        <span>{{ $note->updated_at->diffForHumans() }}</span>
                                    </div>
                                    <div class="flex space-x-3">
                                        <button
                                            wire:click="editNote({{ $note->id }})"
                                            class="inline-flex items-center text-sm font-medium text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 dark:hover:text-indigo-300 transition-colors">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                            </svg>
                                            Edit
                                        </button>
                                        <button
                                            wire:click="deleteNote({{ $note->id }})"
                                            wire:confirm="Are you sure you want to delete this note? This action cannot be undone."
                                            class="inline-flex items-center text-sm font-medium text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300 transition-colors">
                                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                            Delete
                                        </button>
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endforeach

                    <!-- Pagination -->
                    <div class="mt-6">
                        {{ $this->userNotes->links() }}
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
