<div class="h-full bg-white dark:bg-gray-900 border-l border-gray-200 dark:border-gray-700 flex flex-col">
    <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700">
        <h3 class="text-sm font-semibold text-gray-900 dark:text-gray-100">Annotation Discussion</h3>
        @if($selectedAnnotation)
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">
                Page {{ $selectedAnnotation->page_number }}
                @if($selectedAnnotation->resolved_at)
                    · Resolved
                @endif
            </p>
        @else
            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Select an annotation on the page.</p>
        @endif
    </div>

    @if($selectedAnnotation)
        <div class="px-4 py-3 border-b border-gray-200 dark:border-gray-700 flex items-center gap-2">
            @if($this->canManageAnnotation($selectedAnnotation))
                @if($selectedAnnotation->resolved_at)
                    <button wire:click="reopenAnnotation" type="button" class="px-2 py-1 text-xs font-medium rounded bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-300">
                        Reopen
                    </button>
                @else
                    <button wire:click="resolveAnnotation" type="button" class="px-2 py-1 text-xs font-medium rounded bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-300">
                        Resolve
                    </button>
                @endif
                <button wire:click="deleteAnnotation({{ $selectedAnnotation->id }})" type="button" class="px-2 py-1 text-xs font-medium rounded bg-rose-100 text-rose-700 dark:bg-rose-900/40 dark:text-rose-300">
                    Delete
                </button>
            @endif
        </div>

        <div class="flex-1 overflow-y-auto p-4 space-y-3">
            @forelse($selectedAnnotation->comments as $comment)
                <div class="rounded-lg border border-gray-200 dark:border-gray-700 p-3 bg-gray-50 dark:bg-gray-800/60">
                    <div class="flex items-center justify-between gap-2">
                        <div class="text-xs text-gray-500 dark:text-gray-400">
                            <span class="font-medium text-gray-700 dark:text-gray-200">{{ $comment->user->name ?? 'User' }}</span>
                            · {{ $comment->created_at?->diffForHumans() }}
                            @if($comment->edited_at)
                                · edited
                            @endif
                        </div>
                        <div class="flex items-center gap-2">
                            <button wire:click="startReply({{ $comment->id }})" type="button" class="text-xs text-blue-600 dark:text-blue-400">Reply</button>
                            @if($this->canEditComment($comment))
                                <button wire:click="startEditing({{ $comment->id }})" type="button" class="text-xs text-gray-600 dark:text-gray-300">Edit</button>
                                <button wire:click="deleteComment({{ $comment->id }})" type="button" class="text-xs text-rose-600 dark:text-rose-400">Delete</button>
                            @endif
                        </div>
                    </div>

                    @if($editingCommentId === $comment->id)
                        <div class="mt-2 space-y-2">
                            <textarea wire:model="editingMessage" rows="2" class="w-full rounded border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 text-sm"></textarea>
                            <div class="flex items-center gap-2">
                                <button wire:click="saveCommentEdit" type="button" class="px-2 py-1 text-xs rounded bg-blue-600 text-white">Save</button>
                                <button wire:click="cancelEditing" type="button" class="px-2 py-1 text-xs rounded bg-gray-200 dark:bg-gray-700 dark:text-gray-100">Cancel</button>
                            </div>
                        </div>
                    @else
                        <p class="mt-2 text-sm text-gray-700 dark:text-gray-200 whitespace-pre-wrap">{{ $comment->message }}</p>
                    @endif

                    @if($replyingToCommentId === $comment->id)
                        <div class="mt-3 space-y-2">
                            <textarea wire:model="replyMessage" rows="2" class="w-full rounded border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 text-sm" placeholder="Write a reply..."></textarea>
                            <div class="flex items-center gap-2">
                                <button wire:click="addReply" type="button" class="px-2 py-1 text-xs rounded bg-blue-600 text-white">Reply</button>
                                <button wire:click="cancelReply" type="button" class="px-2 py-1 text-xs rounded bg-gray-200 dark:bg-gray-700 dark:text-gray-100">Cancel</button>
                            </div>
                        </div>
                    @endif

                    @if($comment->replies->isNotEmpty())
                        <div class="mt-3 space-y-2">
                            @foreach($comment->replies as $reply)
                                <div class="rounded border border-gray-200 dark:border-gray-700 bg-white dark:bg-gray-900 px-3 py-2">
                                    <div class="text-xs text-gray-500 dark:text-gray-400">
                                        <span class="font-medium text-gray-700 dark:text-gray-200">{{ $reply->user->name ?? 'User' }}</span>
                                        · {{ $reply->created_at?->diffForHumans() }}
                                        @if($reply->edited_at)
                                            · edited
                                        @endif
                                    </div>
                                    <p class="mt-1 text-sm text-gray-700 dark:text-gray-200 whitespace-pre-wrap">{{ $reply->message }}</p>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>
            @empty
                <p class="text-sm text-gray-500 dark:text-gray-400">No comments yet.</p>
            @endforelse
        </div>

        <div class="p-4 border-t border-gray-200 dark:border-gray-700">
            <label for="new-comment" class="sr-only">New comment</label>
            <textarea id="new-comment" wire:model="newComment" rows="3" class="w-full rounded border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 text-sm" placeholder="Write a comment..."></textarea>
            @error('newComment')
                <p class="mt-1 text-xs text-rose-600">{{ $message }}</p>
            @enderror
            <button wire:click="addComment" type="button" class="mt-2 inline-flex px-3 py-1.5 text-xs font-medium rounded bg-blue-600 text-white">Post comment</button>
        </div>
    @else
        <div class="flex-1 p-4 text-sm text-gray-500 dark:text-gray-400">
            Click an annotation highlight on the PDF to view or add comments.
        </div>
    @endif
</div>
