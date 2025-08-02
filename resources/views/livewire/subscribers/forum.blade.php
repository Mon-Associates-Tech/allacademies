<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    @if($currentView === 'categories')
        <div class="flex justify-between items-center mb-6">
            <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Discussion Forums</h1>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200 dark:border-gray-700">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white">Forum Categories</h2>
            </div>
            <div class="divide-y divide-gray-200 dark:divide-gray-700">
                @foreach($categories as $category)
                    <div wire:click="selectCategory({{ $category->id }})"
                         class="p-6 hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer">
                        <div class="flex justify-between items-start">
                            <div class="flex-1">
                                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">
                                    {{ $category->name }}
                                </h3>
                                <p class="text-gray-600 dark:text-gray-400 text-sm mb-3">
                                    {{ $category->description }}
                                </p>
                                <div class="flex space-x-4 text-sm text-gray-500 dark:text-gray-400">
                                    <span>{{ $category->topics_count }} topics</span>
                                    <span>{{ $category->posts_count }} posts</span>
                                </div>
                            </div>
                            @if($category->latestPost)
                                <div class="text-right text-sm text-gray-500 dark:text-gray-400">
                                    <p>Latest: {{ $category->latestPost->created_at->diffForHumans() }}</p>
                                    <p>by {{ $category->latestPost->user->name }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

    @elseif($currentView === 'topics')
        <div class="flex justify-between items-center mb-6">
            <div>
                <button wire:click="backToCategories" class="text-violet-600 hover:text-violet-800 mb-2">
                    ← Back to Categories
                </button>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $category->name }}</h1>
            </div>
            <button wire:click="showCreateTopic"
                    class="bg-violet-600 text-white px-4 py-2 rounded-lg hover:bg-violet-700">
                New Topic
            </button>
        </div>

        <!-- Search and Sort -->
        <div class="mb-6 bg-white dark:bg-gray-800 rounded-lg shadow p-4">
            <div class="flex space-x-4">
                <input wire:model.live="search" type="text" placeholder="Search topics..."
                       class="flex-1 px-3 py-2 border rounded-md">
                <select wire:model.live="sortBy" class="px-3 py-2 border rounded-md">
                    <option value="recent">Most Recent</option>
                    <option value="popular">Most Popular</option>
                    <option value="oldest">Oldest First</option>
                </select>
            </div>
        </div>

        <!-- Topics List -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow divide-y divide-gray-200 dark:divide-gray-700">
            @foreach($topics as $topic)
                <div wire:click="selectTopic({{ $topic->id }})"
                     class="p-6 hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer">
                    <div class="flex justify-between items-start">
                        <div class="flex-1">
                            <div class="flex items-center space-x-2 mb-2">
                                @if($topic->is_pinned)
                                    <span class="bg-yellow-100 text-yellow-800 text-xs px-2 py-1 rounded">Pinned</span>
                                @endif
                                @if($topic->is_locked)
                                    <span class="bg-red-100 text-red-800 text-xs px-2 py-1 rounded">Locked</span>
                                @endif
                            </div>
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">
                                {{ $topic->title }}
                            </h3>
                            <div class="flex space-x-4 text-sm text-gray-500 dark:text-gray-400">
                                <span>by {{ $topic->user->name }}</span>
                                <span>{{ $topic->created_at->diffForHumans() }}</span>
                                <span>{{ $topic->posts_count }} replies</span>
                            </div>
                        </div>
                        @if($topic->latestPost)
                            <div class="text-right text-sm text-gray-500 dark:text-gray-400">
                                <p>Latest: {{ $topic->latestPost->created_at->diffForHumans() }}</p>
                                <p>by {{ $topic->latestPost->user->name }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        {{ $topics->links() }}

    @elseif($currentView === 'posts')
        <div class="flex justify-between items-center mb-6">
            <div>
                <button wire:click="backToTopics" class="text-violet-600 hover:text-violet-800 mb-2">
                    ← Back to Topics
                </button>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">{{ $topic->title }}</h1>
            </div>
            <button wire:click="showCreatePost"
                    class="bg-violet-600 text-white px-4 py-2 rounded-lg hover:bg-violet-700">
                Reply
            </button>
        </div>

        <!-- Posts -->
        <div class="space-y-6">
            @foreach($posts as $post)
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                    <div class="flex items-start space-x-4">
                        <div class="flex-shrink-0">
                            <div class="w-10 h-10 bg-gray-300 rounded-full flex items-center justify-center">
                                {{ substr($post->user->name, 0, 1) }}
                            </div>
                        </div>
                        <div class="flex-1">
                            <div class="flex items-center space-x-2 mb-2">
                                <h4 class="font-medium text-gray-900 dark:text-white">{{ $post->user->name }}</h4>
                                <span class="text-sm text-gray-500 dark:text-gray-400">
                                    {{ $post->created_at->diffForHumans() }}
                                </span>
                            </div>
                            <div class="prose prose-sm dark:prose-invert max-w-none">
                                {!! nl2br(e($post->content)) !!}
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{ $posts->links() }}

    @elseif($currentView === 'create-post')
        <div class="flex justify-between items-center mb-6">
            <div>
                <button wire:click="selectTopic({{ $selectedTopic }})" class="text-violet-600 hover:text-violet-800 mb-2">
                    ← Back to Topic
                </button>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Reply to: {{ $topic->title }}</h1>
            </div>
        </div>

        <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
            <form wire:submit="createPost">
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Your Reply</label>
                    <textarea wire:model="newPostContent" rows="8"
                              class="w-full px-3 py-2 border rounded-md resize-none"
                              placeholder="Write your reply..."></textarea>
                    @error('newPostContent') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <div class="flex space-x-4">
                    <button type="submit" class="bg-violet-600 text-white px-6 py-2 rounded-lg hover:bg-violet-700">
                        Post Reply
                    </button>
                    <button type="button" wire:click="selectTopic({{ $selectedTopic }})"
                            class="bg-gray-300 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-400">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    @endif
</div>
