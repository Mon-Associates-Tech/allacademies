<div class="h-full flex flex-col">
    <!-- Comments Sidebar -->
    @if($showComments && $selectedAnnotation)
    <div class="absolute inset-y-0 right-0 w-80 bg-white dark:bg-gray-800 shadow-xl z-50 flex flex-col border-l border-gray-200 dark:border-gray-700">
        <!-- Header -->
        <div class="p-4 border-b border-gray-200 dark:border-gray-700 flex items-center justify-between">
            <h3 class="font-semibold text-gray-900 dark:text-white">Comments</h3>
            <button wire:click="closeComments" class="text-gray-500 hover:text-gray-700 dark:text-gray-400 dark:hover:text-gray-200">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        <!-- Annotation Info -->
        <div class="p-4 bg-gray-50 dark:bg-gray-900 border-b border-gray-200 dark:border-gray-700">
            <p class="text-sm text-gray-600 dark:text-gray-400">Page {{ $selectedAnnotation->page_number }}</p>
            <p class="text-xs text-gray-500 dark:text-gray-500 mt-1">By {{ $selectedAnnotation->user->name }}</p>
            <p class="text-xs text-gray-500 dark:text-gray-500">{{ $selectedAnnotation->created_at->diffForHumans() }}</p>
        </div>

        <!-- Comments List -->
        <div class="flex-1 overflow-y-auto p-4 space-y-4">
            @forelse($selectedAnnotation->comments as $comment)
            <div class="bg-gray-50 dark:bg-gray-900 rounded-lg p-3">
                <div class="flex items-start justify-between mb-2">
                    <div>
                        <p class="text-sm font-medium text-gray-900 dark:text-white">{{ $comment->user->name }}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-500">{{ $comment->created_at->diffForHumans() }}</p>
                    </div>
                    @if($comment->user_id === auth()->id() || auth()->user()->hasAnyRole(['owner', 'admin']))
                    <button wire:click="deleteComment({{ $comment->id }})" class="text-red-500 hover:text-red-700">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                    </button>
                    @endif
                </div>
                <p class="text-sm text-gray-700 dark:text-gray-300">{{ $comment->message }}</p>
            </div>
            @empty
            <p class="text-sm text-gray-500 dark:text-gray-400 text-center py-8">No comments yet</p>
            @endforelse
        </div>

        <!-- Add Comment Form -->
        <div class="p-4 border-t border-gray-200 dark:border-gray-700">
            <form wire:submit.prevent="addComment" class="space-y-2">
                <textarea 
                    wire:model="commentText" 
                    rows="3" 
                    class="w-full px-3 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 dark:bg-gray-700 dark:text-white text-sm"
                    placeholder="Add a comment..."></textarea>
                <div class="flex gap-2">
                    <button type="submit" class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 text-sm">
                        Post
                    </button>
                    @if($selectedAnnotation->user_id === auth()->id() || auth()->user()->hasAnyRole(['owner', 'admin']))
                    <button type="button" wire:click="deleteAnnotation({{ $selectedAnnotation->id }})" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 text-sm">
                        Delete
                    </button>
                    @endif
                </div>
            </form>
        </div>
    </div>
    @endif
</div>
