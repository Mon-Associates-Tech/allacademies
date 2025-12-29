@props(['notes' => [], 'canCreate' => false])

<div class="notes-list">
    {{-- Header with Actions --}}
    <div class="mb-4 flex justify-between items-center">
        <div class="flex items-center space-x-2">
            @if(count($notes) > 0)
                {{-- Export Dropdown --}}
                <div x-data="{ open: false }" class="relative">
                    <button @click="open = !open"
                            type="button"
                            class="inline-flex items-center px-3 py-2 border border-gray-300 dark:border-gray-600 text-sm font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors duration-200">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                        </svg>
                        Export All
                        <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                        </svg>
                    </button>
                    <div x-show="open"
                         @click.away="open = false"
                         x-transition:enter="transition ease-out duration-100"
                         x-transition:enter-start="transform opacity-0 scale-95"
                         x-transition:enter-end="transform opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-75"
                         x-transition:leave-start="transform opacity-100 scale-100"
                         x-transition:leave-end="transform opacity-0 scale-95"
                         class="absolute left-0 mt-2 w-48 rounded-md shadow-lg bg-white dark:bg-gray-800 ring-1 ring-black ring-opacity-5 z-10">
                        <div class="py-1">
                            <a href="#" wire:click.prevent="exportNotes('pdf')" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                                <svg class="w-4 h-4 inline-block mr-2 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd"></path>
                                </svg>
                                Export as PDF
                            </a>
                            <a href="#" wire:click.prevent="exportNotes('markdown')" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                                <svg class="w-4 h-4 inline-block mr-2 text-blue-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd"></path>
                                </svg>
                                Export as Markdown
                            </a>
                            <a href="#" wire:click.prevent="exportNotes('text')" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                                <svg class="w-4 h-4 inline-block mr-2 text-gray-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h4.586A2 2 0 0112 2.586L15.414 6A2 2 0 0116 7.414V16a2 2 0 01-2 2H6a2 2 0 01-2-2V4z" clip-rule="evenodd"></path>
                                </svg>
                                Export as Plain Text
                            </a>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        @if($canCreate)
            <button type="button"
                    @click="$dispatch('open-modal', { name: 'create-note' })"
                    class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:bg-green-700 active:bg-green-900 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                Create Note
            </button>
        @endif
    </div>

    {{-- Notes List --}}
    @if(count($notes) > 0)
        <div class="space-y-4">
            @foreach($notes as $note)
                <div class="bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg shadow-sm hover:shadow-md transition-shadow duration-200">
                    <div class="p-4">
                        {{-- Note Header --}}
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <h4 class="text-lg font-medium text-gray-900 dark:text-white">{{ $note->title }}</h4>
                                <div class="mt-1 flex items-center space-x-2 text-sm text-gray-500 dark:text-gray-400">
                                    <span>By {{ $note->user->name ?? 'Unknown' }}</span>
                                    <span>&bull;</span>
                                    <span>{{ $note->created_at->diffForHumans() }}</span>
                                    @if($note->is_public)
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                            Public
                                        </span>
                                    @endif
                                </div>
                            </div>

                            {{-- Actions Dropdown --}}
                            <div x-data="{ open: false }" class="relative">
                                <button @click="open = !open" class="p-1 rounded-full text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 focus:outline-none">
                                    <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z"></path>
                                    </svg>
                                </button>
                                <div x-show="open"
                                     @click.away="open = false"
                                     class="absolute right-0 mt-2 w-48 rounded-md shadow-lg bg-white dark:bg-gray-800 ring-1 ring-black ring-opacity-5 z-10">
                                    <div class="py-1">
                                        <a href="#" @click.prevent="$dispatch('open-modal', { name: 'view-note', noteId: {{ $note->id }} })" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                                            View
                                        </a>
                                        <a href="#" wire:click.prevent="exportNote({{ $note->id }}, 'pdf')" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                                            Export as PDF
                                        </a>
                                        <a href="#" wire:click.prevent="exportNote({{ $note->id }}, 'markdown')" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                                            Export as Markdown
                                        </a>
                                        <a href="#" wire:click.prevent="exportNote({{ $note->id }}, 'text')" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                                            Export as Text
                                        </a>
                                        @can('update', $note)
                                            <a href="#" @click.prevent="$dispatch('open-modal', { name: 'edit-note', noteId: {{ $note->id }} })" class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700">
                                                Edit
                                            </a>
                                        @endcan
                                        @can('delete', $note)
                                            <a href="#" wire:click.prevent="deleteNote({{ $note->id }})" wire:confirm="Are you sure you want to delete this note?" class="block px-4 py-2 text-sm text-red-600 dark:text-red-400 hover:bg-gray-100 dark:hover:bg-gray-700">
                                                Delete
                                            </a>
                                        @endcan
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Note Content Preview --}}
                        <div class="mt-3 text-sm text-gray-600 dark:text-gray-300 line-clamp-3 prose dark:prose-invert max-w-none">
                            {!! Str::limit(strip_tags($note->content), 200) !!}
                        </div>

                        {{-- Attachments Count --}}
                        @if($note->attachments && $note->attachments->count() > 0)
                            <div class="mt-3 flex items-center text-sm text-gray-500 dark:text-gray-400">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path>
                                </svg>
                                {{ $note->attachments->count() }} attachment(s)
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @else
        {{-- Empty State --}}
        <div class="text-center py-12">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No notes</h3>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">No notes have been created yet.</p>
            @if($canCreate)
                <div class="mt-6">
                    <button type="button"
                            @click="$dispatch('open-modal', { name: 'create-note' })"
                            class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-green-600 hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                        <svg class="-ml-1 mr-2 h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                        </svg>
                        Create Note
                    </button>
                </div>
            @endif
        </div>
    @endif
</div>

{{-- View Note Modal --}}
<x-modal-component name="view-note" size="3xl" title="View Note">
    <div x-data="{ noteId: null }"
         @modal-opened.window="if ($event.detail.name === 'view-note') { noteId = $event.detail.noteId; }">
        <div class="prose dark:prose-invert max-w-none">
            {{-- Note content will be loaded dynamically via Livewire --}}
            <template x-if="noteId">
                <div wire:key="note-content">
                    @livewire('notes.note-viewer', ['noteId' => null], key('note-viewer'))
                </div>
            </template>
        </div>
    </div>
</x-modal-component>
