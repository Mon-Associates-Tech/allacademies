<div>
    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm">
        {{-- Header --}}
        <div class="px-4 sm:px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="flex-shrink-0">
                        <div class="h-10 w-10 rounded-lg bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center">
                            <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                            </svg>
                        </div>
                    </div>
                    <div>
                        <h3 class="text-base sm:text-lg font-semibold text-gray-900 dark:text-white">Attachments</h3>
                        <p class="text-xs sm:text-sm text-gray-500 dark:text-gray-400 mt-0.5">
                            {{ $existingAttachments->count() }} {{ Str::plural('file', $existingAttachments->count()) }}
                        </p>
                    </div>
                </div>

                @if($note->canUserEdit(Auth::id()))
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-300">
                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Can Edit
                    </span>
                @endif
            </div>
        </div>

        {{-- Upload Section (if can edit) --}}
        @if($note->canUserEdit(Auth::id()))
            <div class="px-4 sm:px-6 py-4 bg-gray-50 dark:bg-gray-900/50 border-b border-gray-200 dark:border-gray-700">
                <div x-data="{ dragOver: false }"
                     @dragover.prevent="dragOver = true"
                     @dragleave.prevent="dragOver = false"
                     @drop.prevent="dragOver = false; $refs.fileInput.files = $event.dataTransfer.files; $refs.fileInput.dispatchEvent(new Event('change'))">

                    <label :class="{'border-blue-500 bg-blue-50 dark:bg-blue-900/20': dragOver}"
                           class="flex flex-col items-center justify-center w-full border-2 border-gray-300 border-dashed rounded-lg cursor-pointer hover:bg-gray-50 dark:hover:bg-gray-800 dark:border-gray-600 transition-colors py-6">
                        <div class="flex flex-col items-center justify-center">
                            <svg class="w-10 h-10 mb-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                            </svg>
                            <p class="mb-2 text-sm text-gray-600 dark:text-gray-400 text-center">
                                <span class="font-semibold">Click to upload</span> or drag and drop
                            </p>
                            <p class="text-xs text-gray-500 dark:text-gray-500 text-center px-4">
                                PDF, Word, Excel, TXT, ZIP and images (MAX. 10MB)
                            </p>
                        </div>
                        <input x-ref="fileInput" type="file" wire:model="attachments" multiple class="hidden" accept=".pdf,.txt,.doc,.docx,.xls,.xlsx,.zip,.rar,.7z,.jpg,.jpeg,.png,.gif" />
                    </label>
                </div>

                {{-- Pending Uploads --}}
                @if(count($tempAttachments) > 0)
                    <div class="mt-4 space-y-2">
                        <div class="flex items-center justify-between">
                            <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300">
                                Pending Uploads ({{ count($tempAttachments) }})
                            </h4>
                            <button wire:click="saveAttachments"
                                    wire:loading.attr="disabled"
                                    class="inline-flex items-center px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors disabled:opacity-50">
                                <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10" />
                                </svg>
                                Upload All
                            </button>
                        </div>

                        @foreach($tempAttachments as $attachment)
                            <div class="flex items-center justify-between p-3 bg-white dark:bg-gray-800 border border-gray-200 dark:border-gray-700 rounded-lg">
                                <div class="flex items-center gap-3 flex-1 min-w-0">
                                    <span class="text-2xl">📎</span>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm font-medium text-gray-900 dark:text-white truncate">
                                            {{ $attachment['original_filename'] }}
                                        </p>
                                        <p class="text-xs text-gray-500 dark:text-gray-400">
                                            {{ $attachment['human_size'] }}
                                        </p>
                                    </div>
                                </div>
                                <button wire:click="removeAttachment('{{ $attachment['id'] }}')"
                                        type="button"
                                        class="flex-shrink-0 p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-lg transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>
                        @endforeach
                    </div>
                @endif

                {{-- Loading State --}}
                <div wire:loading wire:target="attachments" class="mt-4">
                    <div class="flex items-center justify-center p-4 bg-blue-50 dark:bg-blue-900/20 rounded-lg">
                        <svg class="animate-spin h-5 w-5 text-blue-600 dark:text-blue-400 mr-3" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span class="text-sm text-blue-600 dark:text-blue-400">Processing files...</span>
                    </div>
                </div>
            </div>
        @endif

        {{-- Existing Attachments List --}}
        <div class="px-4 sm:px-6 py-4">
            @if($existingAttachments->count() > 0)
                <div class="space-y-2">
                    @foreach($existingAttachments as $attachment)
                        <div class="flex items-center justify-between p-3 sm:p-4 bg-gray-50 dark:bg-gray-900/50 hover:bg-gray-100 dark:hover:bg-gray-800 rounded-lg transition-colors">
                            <div class="flex items-center gap-3 flex-1 min-w-0">
                                <span class="text-2xl flex-shrink-0">{{ $attachment->file_icon }}</span>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-gray-900 dark:text-white truncate">
                                        {{ $attachment->original_filename }}
                                    </p>
                                    <div class="flex flex-wrap items-center gap-2 mt-1 text-xs text-gray-500 dark:text-gray-400">
                                        <span>{{ $attachment->file_size_human }}</span>
                                        <span>•</span>
                                        <span>{{ $attachment->created_at->diffForHumans() }}</span>
                                        <span>•</span>
                                        <span>by {{ $attachment->user->name }}</span>
                                    </div>
                                </div>
                            </div>

                            <div class="flex items-center gap-2 flex-shrink-0 ml-2">
                                {{-- View Button (for supported formats) --}}
                                @if(in_array($attachment->file_extension, ['pdf', 'jpg', 'jpeg', 'png', 'gif', 'txt']))
                                    <a href="{{ route('notes.attachments.view', ['note' => $note, 'attachment' => $attachment]) }}"
                                       target="_blank"
                                       class="p-2 text-blue-600 hover:bg-blue-50 dark:text-blue-400 dark:hover:bg-blue-900/20 rounded-lg transition-colors"
                                       title="View">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>
                                    </a>
                                @endif

                                {{-- Download Button --}}
                                <a href="{{ route('notes.attachments.download', ['note' => $note, 'attachment' => $attachment]) }}"
                                   class="p-2 text-gray-600 hover:bg-gray-200 dark:text-gray-400 dark:hover:bg-gray-700 rounded-lg transition-colors"
                                   title="Download">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                                    </svg>
                                </a>

                                {{-- Delete Button (if can edit) --}}
                                @if($note->canUserEdit(Auth::id()))
                                    <button wire:click="deleteAttachment({{ $attachment->id }})"
                                            wire:confirm="Are you sure you want to delete this attachment?"
                                            class="p-2 text-red-600 hover:bg-red-50 dark:text-red-400 dark:hover:bg-red-900/20 rounded-lg transition-colors"
                                            title="Delete">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13" />
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No attachments</h3>
                    <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                        @if($note->canUserEdit(Auth::id()))
                            Upload files to attach them to this note
                        @else
                            This note has no attachments
                        @endif
                    </p>
                </div>
            @endif
        </div>
    </div>
</div>
