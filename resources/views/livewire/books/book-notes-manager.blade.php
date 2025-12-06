<div class="p-6">
    <div class="border-b border-gray-200 dark:border-gray-700">
        <nav class="-mb-px flex space-x-8" aria-label="Tabs">
            <button
                wire:click="$set('activeTab', 'book-notes')"
                class="whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm transition-colors duration-200"
                :class="activeTab === 'book-notes'
                    ? 'border-blue-500 text-blue-600 dark:text-blue-400'
                    : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300'">
                Notes for "{{ $book->title }}"
            </button>
            <button
                wire:click="$set('activeTab', 'all-notes')"
                class="whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm transition-colors duration-200"
                :class="activeTab === 'all-notes'
                    ? 'border-blue-500 text-blue-600 dark:text-blue-400'
                    : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300 dark:text-gray-400 dark:hover:text-gray-300'">
                All My Notes
            </button>
        </nav>
    </div>

    <div class="py-4">
        <!-- Book Notes Tab -->
        <div x-show="activeTab === 'book-notes'" x-transition>
            <div class="mb-6">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-3">Add New Note</h3>
                <div class="space-y-4">
                    <x-form.rich-editor class="rich-editor"
                                        name="newNoteContent"
                                        wire:model="newNoteContent"
                                        rows="4"
                                        class="w-full p-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                                        placeholder="Write your notes about this book here...">
                        >

                    </x-form.rich-editor>
                    <div class="flex justify-end">
                        <button
                            wire:click="saveNote"
                            wire:loading.attr="disabled"
                            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 disabled:opacity-50">
                            <span wire:loading.remove>Save Note</span>
                            <span wire:loading>Saving...</span>
                        </button>
                    </div>
                </div>
            </div>

            <div class="border-t border-gray-200 dark:border-gray-700 pt-6">
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-3">
                    Your Notes for "{{ $book->title }}" ({{ $bookNotes->count() }})
                </h3>

                @if($bookNotes->isEmpty())
                    <div class="text-center py-8">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No notes yet</h3>
                        <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Get started by writing your first note.</p>
                    </div>
                @else
                    <div class="space-y-4">
                        @foreach($bookNotes as $note)
                            <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                                @if($editingNoteId === $note->id)
                                    <div class="space-y-3">
                                        <x-form.rich-editor class="rich-editor"
                                                            name="editingContent"
                                                            wire:model="editingContent"
                                                            rows="4"
                                                            class="w-full p-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                                                            placeholder="Write your notes about this book here...">
                                            >

                                        </x-form.rich-editor>
                                        <div class="flex justify-end space-x-2">
                                            <button
                                                wire:click="cancelEdit"
                                                class="px-3 py-1 text-gray-700 dark:text-gray-300 border border-gray-300 dark:border-gray-600 rounded-md hover:bg-gray-50 dark:hover:bg-gray-700">
                                                Cancel
                                            </button>
                                            <button
                                                wire:click="updateNote"
                                                wire:loading.attr="disabled"
                                                class="px-3 py-1 bg-blue-600 text-white rounded-md hover:bg-blue-700 disabled:opacity-50">
                                                <span wire:loading.remove>Update</span>
                                                <span wire:loading>Updating...</span>
                                            </button>
                                        </div>
                                    </div>
                                @else
                                    <div class="prose prose-gray dark:prose-invert max-w-none">
                                        <p class="text-gray-700 dark:text-gray-300 whitespace-pre-line">{{ $note->content }}</p>
                                    </div>
                                    <div class="mt-3 flex items-center justify-between">
                                        <span class="text-xs text-gray-500 dark:text-gray-400">
                                            Last updated: {{ $note->updated_at->diffForHumans() }}
                                        </span>
                                        <div class="flex space-x-2">
                                            <button
                                                wire:click="editNote({{ $note->id }})"
                                                class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300">
                                                Edit
                                            </button>
                                            <button
                                                wire:click="deleteNote({{ $note->id }})"
                                                wire:confirm="Are you sure you want to delete this note?"
                                                class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300">
                                                Delete
                                            </button>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        <!-- All Notes Tab -->
        <div x-show="activeTab === 'all-notes'" x-transition>
            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-3">
                All Your Notes ({{ $userNotes->count() }})
            </h3>

            @if($userNotes->isEmpty())
                <div class="text-center py-8">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No notes yet</h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Get started by writing your first note.</p>
                </div>
            @else
                <div class="space-y-4">
                    @foreach($userNotes as $note)
                        <div class="border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                            <div class="flex items-start justify-between">
                                <div>
                                    <h4 class="font-medium text-gray-900 dark:text-white">
                                        {{ $note->book->title ?? 'Untitled Book' }}
                                    </h4>
                                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                                        {{ Str::limit($note->content, 100) }}
                                    </p>
                                </div>
                                <a href="{{ route('books.show', $note->book) }}"
                                   class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 text-sm">
                                    View Book
                                </a>
                            </div>
                            <div class="mt-3 flex items-center justify-between">
                                <span class="text-xs text-gray-500 dark:text-gray-400">
                                    Last updated: {{ $note->updated_at->diffForHumans() }}
                                </span>
                                <div class="flex space-x-2">
                                    <button
                                        wire:click="editNote({{ $note->id }})"
                                        class="text-blue-600 hover:text-blue-800 dark:text-blue-400 dark:hover:text-blue-300 text-sm">
                                        Edit
                                    </button>
                                    <button
                                        wire:click="deleteNote({{ $note->id }})"
                                        wire:confirm="Are you sure you want to delete this note?"
                                        class="text-red-600 hover:text-red-800 dark:text-red-400 dark:hover:text-red-300 text-sm">
                                        Delete
                                    </button>
                                </div>
                            </div>

                            @if($editingNoteId === $note->id)
                                <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                                    <x-form.rich-editor class="rich-editor"
                                                        name="newNoteContent"
                                                        wire:model="newNoteContent"
                                                        rows="4"
                                                        class="w-full p-3 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-700 dark:text-white"
                                                        placeholder="Write your notes about this book here...">
                                        >

                                    </x-form.rich-editor>
                                    <div class="mt-3 flex justify-end space-x-2">
                                        <button
                                            wire:click="cancelEdit"
                                            class="px-3 py-1 text-gray-700 dark:text-gray-300 border border-gray-300 dark:border-gray-600 rounded-md hover:bg-gray-50 dark:hover:bg-gray-700">
                                            Cancel
                                        </button>
                                        <button
                                            wire:click="updateNote"
                                            wire:loading.attr="disabled"
                                            class="px-3 py-1 bg-blue-600 text-white rounded-md hover:bg-blue-700 disabled:opacity-50">
                                            <span wire:loading.remove>Update</span>
                                            <span wire:loading>Updating...</span>
                                        </button>
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>
</div>
