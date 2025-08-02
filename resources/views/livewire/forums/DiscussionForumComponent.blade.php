<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8" x-data="{ showFilters: false, showAttachments: false }">
    <!-- Header -->
    <div class="flex flex-col lg:flex-row lg:justify-between lg:items-center mb-6 space-y-4 lg:space-y-0">
        <div class="flex items-center space-x-2">
            @if($currentView !== 'categories')
                <button wire:click="backToCategories"
                        class="flex items-center text-violet-600 hover:text-violet-800 transition-colors">
                    <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
                    </svg>
                    Forums
                </button>
                <span class="text-gray-400">/</span>
            @endif

            @if($currentView === 'topics' && isset($category))
                <button wire:click="backToTopics" class="text-violet-600 hover:text-violet-800">
                    {{ $category->name }}
                </button>
            @elseif($currentView === 'posts' && isset($topic))
                <button wire:click="backToTopics" class="text-violet-600 hover:text-violet-800">
                    {{ $topic->category->name }}
                </button>
                <span class="text-gray-400">/</span>
                <h1 class="text-xl font-bold text-gray-900 dark:text-white truncate">{{ $topic->title }}</h1>
            @else
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Discussion Forums</h1>
            @endif
        </div>

        <div class="flex items-center space-x-2">
            @if($currentView === 'topics')
                <button @click="showFilters = !showFilters"
                        class="px-4 py-2 text-gray-600 hover:text-gray-800 border border-gray-300 rounded-lg transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"/>
                    </svg>
                </button>
            @endif

            @if(in_array($currentView, ['topics', 'posts']))
                <button wire:click="{{ $currentView === 'topics' ? 'showCreateTopic' : 'showCreatePost' }}"
                        class="bg-violet-600 text-white px-4 py-2 rounded-lg hover:bg-violet-700 transition-colors flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                    </svg>
                    {{ $currentView === 'topics' ? 'New Topic' : 'Reply' }}
                </button>
            @endif
        </div>
    </div>

    <!-- Success/Error Messages -->
    @if (session()->has('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    @if (session()->has('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            {{ session('error') }}
        </div>
    @endif

    <!-- Filters Panel -->
    @if($currentView === 'topics')
        <div x-show="showFilters" x-transition class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-4 mb-6">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <input wire:model.live="search" type="text" placeholder="Search topics..."
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-violet-500 focus:border-violet-500">
                </div>
                <div>
                    <select wire:model.live="sortBy"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-violet-500 focus:border-violet-500">
                        <option value="recent">Most Recent</option>
                        <option value="popular">Most Popular</option>
                        <option value="oldest">Oldest First</option>
                    </select>
                </div>
                <div>
                    <select wire:model.live="academicLevelFilter"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-violet-500 focus:border-violet-500">
                        <option value="">All Levels</option>
                        @foreach($academicLevels as $level)
                            <option value="{{ $level->id }}">{{ $level->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <select wire:model.live="academicSubjectFilter"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-violet-500 focus:border-violet-500">
                        <option value="">All Subjects</option>
                        @foreach($academicSubjects as $subject)
                            <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    @endif

    <!-- Categories View -->
    @if($currentView === 'categories')
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg overflow-hidden">
            <div
                class="px-6 py-4 bg-gradient-to-r from-violet-50 to-purple-50 dark:from-gray-700 dark:to-gray-800 border-b border-gray-200 dark:border-gray-700">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
                    <svg class="w-6 h-6 mr-2 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M19 11H5m14-7H5v16h14V4z"/>
                    </svg>
                    Forum Categories
                </h2>
                <p class="text-sm text-gray-600 dark:text-gray-400 mt-1">
                    Choose a category to browse topics and join discussions
                </p>
            </div>

            <div class="divide-y divide-gray-200 dark:divide-gray-700">
                @forelse($categories as $category)
                    <div wire:click="selectCategory({{ $category->id }})"
                         class="p-6 hover:bg-gradient-to-r hover:from-violet-50 hover:to-purple-50 dark:hover:from-gray-700 dark:hover:to-gray-800 cursor-pointer transition-all duration-200 group">
                        <div class="flex items-start justify-between">
                            <div class="flex-1">
                                <div class="flex items-center space-x-3 mb-2">
                                    <div
                                        class="w-10 h-10 rounded-lg bg-gradient-to-br from-{{ $category->color ?? 'violet' }}-400 to-{{ $category->color ?? 'purple' }}-600 flex items-center justify-center text-white">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                                        </svg>
                                    </div>

                                    <div>
                                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white group-hover:text-violet-600 transition-colors">
                                            {{ $category->name }}
                                        </h3>
                                        @if($category->academicLevel || $category->academicSubject)
                                            <div class="flex items-center space-x-2 mt-1">
                                                @if($category->academicLevel)
                                                    <span
                                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                        {{ $category->academicLevel->name }}
                                                    </span>
                                                @endif
                                                @if($category->academicSubject)
                                                    <span
                                                        class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                        {{ $category->academicSubject->name }}
                                                    </span>
                                                @endif
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                <p class="text-gray-600 dark:text-gray-400 text-sm mb-3 leading-relaxed">
                                    {{ $category->description }}
                                </p>

                                <div class="flex items-center space-x-4 text-sm text-gray-500 dark:text-gray-400">
                                    <div class="flex items-center">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/>
                                        </svg>
                                        <span class="font-medium">{{ number_format($category->topics_count) }}</span>
                                        <span class="ml-1">{{ Str::plural('topic', $category->topics_count) }}</span>
                                    </div>
                                    <div class="flex items-center">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                  d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                                        </svg>
                                        <span class="font-medium">{{ number_format($category->posts_count) }}</span>
                                        <span class="ml-1">{{ Str::plural('post', $category->posts_count) }}</span>
                                    </div>
                                    @if($category->is_private)
                                        <span
                                            class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-amber-100 text-amber-800">
                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor"
                                                 viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                            </svg>
                                            Private
                                        </span>
                                    @endif
                                </div>
                            </div>

                            @if($category->latestPost)
                                <div class="text-right text-sm text-gray-500 dark:text-gray-400 ml-4 flex-shrink-0">
                                    <div class="flex items-center justify-end mb-1">
                                        <div
                                            class="w-6 h-6 bg-gray-300 rounded-full flex items-center justify-center text-xs font-medium mr-2">
                                            {{ substr($category->latestPost->user->name, 0, 1) }}
                                        </div>
                                        <span class="font-medium">{{ $category->latestPost->user->name }}</span>
                                    </div>
                                    <div class="text-xs">
                                        <time datetime="{{ $category->latestPost->created_at->toISOString() }}">
                                            {{ $category->latestPost->created_at->diffForHumans() }}
                                        </time>
                                    </div>
                                </div>
                            @else
                                <div class="text-right text-sm text-gray-400 ml-4 flex-shrink-0">
                                    No posts yet
                                </div>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="p-12 text-center">
                        <svg class="w-12 h-12 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor"
                             viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                        </svg>
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">No categories available</h3>
                        <p class="text-gray-600 dark:text-gray-400">Forum categories will appear here once they are
                            created.</p>
                    </div>
                @endforelse
            </div>
        </div>
    @endif

    <!-- Topics View -->
    @if($currentView === 'topics')
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg overflow-hidden">
            <div
                class="px-6 py-4 bg-gradient-to-r from-violet-50 to-purple-50 dark:from-gray-700 dark:to-gray-800 border-b border-gray-200 dark:border-gray-700">
                <h2 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
                    <svg class="w-6 h-6 mr-2 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/>
                    </svg>
                    Topics in {{ $category->name }}
                </h2>
            </div>

            <div class="divide-y divide-gray-200 dark:divide-gray-700">
                @forelse($topics as $topic)
                    <div wire:click="selectTopic({{ $topic->id }})"
                         class="p-6 hover:bg-gradient-to-r hover:from-violet-50 hover:to-purple-50 dark:hover:from-gray-700 dark:hover:to-gray-800 cursor-pointer transition-all duration-200 group">
                        <div class="flex justify-between items-start">
                            <div class="flex-1">
                                <div class="flex items-center space-x-2 mb-2">
                                    @if($topic->is_pinned)
                                        <span
                                            class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor"
                                                 viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M5 5a2 2 0 012-2h10a2 2 0 012 2v16l-7-3.5L5 21V5z"/>
                                            </svg>
                                            Pinned
                                        </span>
                                    @endif
                                    @if($topic->is_locked)
                                        <span
                                            class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor"
                                                 viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                            </svg>
                                            Locked
                                        </span>
                                    @endif
                                    @if($topic->is_announcement)
                                        <span
                                            class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor"
                                                 viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z"/>
                                            </svg>
                                            Announcement
                                        </span>
                                    @endif
                                </div>

                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white group-hover:text-violet-600 transition-colors mb-2">
                                    {{ $topic->title }}
                                </h3>

                                <div class="flex flex-wrap items-center gap-2 mb-3">
                                    @if($topic->academicLevel)
                                        <span
                                            class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            {{ $topic->academicLevel->name }}
                                        </span>
                                    @endif
                                    @if($topic->academicSubject)
                                        <span
                                            class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            {{ $topic->academicSubject->name }}
                                        </span>
                                    @endif
                                    @if($topic->referencedBook)
                                        <span
                                            class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor"
                                                 viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                            </svg>
                                            {{ Str::limit($topic->referencedBook->title, 20) }}
                                        </span>
                                    @endif
                                    @if($topic->tags && count($topic->tags) > 0)
                                        @foreach(array_slice($topic->tags, 0, 3) as $tag)
                                            <span
                                                class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                #{{ $tag }}
                                            </span>
                                        @endforeach
                                    @endif
                                </div>

                                <div class="flex items-center space-x-4 text-sm text-gray-500 dark:text-gray-400">
                                    <div class="flex items-center">
                                        <div
                                            class="w-5 h-5 bg-gray-300 rounded-full flex items-center justify-center text-xs mr-2">
                                            {{ substr($topic->user->name, 0, 1) }}
                                        </div>
                                        <span>by {{ $topic->user->name }}</span>
                                    </div>
                                    <span>{{ $topic->created_at->diffForHumans() }}</span>
                                    <span>{{ $topic->posts_count }} {{ Str::plural('reply', $topic->posts_count) }}</span>
                                    <span>{{ $topic->views_count ?? 0 }} {{ Str::plural('view', $topic->views_count ?? 0) }}</span>
                                </div>
                            </div>

                            @if($topic->latestPost)
                                <div class="text-right text-sm text-gray-500 dark:text-gray-400 ml-4 flex-shrink-0">
                                    <div class="flex items-center justify-end mb-1">
                                        <div
                                            class="w-5 h-5 bg-gray-300 rounded-full flex items-center justify-center text-xs mr-2">
                                            {{ substr($topic->latestPost->user->name, 0, 1) }}
                                        </div>
                                        <span class="font-medium">{{ $topic->latestPost->user->name }}</span>
                                    </div>
                                    <div class="text-xs">
                                        Latest: {{ $topic->latestPost->created_at->diffForHumans() }}
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="p-12 text-center">
                        <svg class="w-12 h-12 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor"
                             viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/>
                        </svg>
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">No topics yet</h3>
                        <p class="text-gray-600 dark:text-gray-400">Be the first to start a discussion in this
                            category!</p>
                        <button wire:click="showCreateTopic"
                                class="mt-4 bg-violet-600 text-white px-4 py-2 rounded-lg hover:bg-violet-700 transition-colors">
                            Create First Topic
                        </button>
                    </div>
                @endforelse
            </div>

            @if(isset($topics) && $topics->hasPages())
                <div class="px-6 py-4 bg-gray-50 dark:bg-gray-700">
                    {{ $topics->links() }}
                </div>
            @endif
        </div>
    @endif

    <!-- Posts View -->
    @if($currentView === 'posts')
        <div class="space-y-6">
            <!-- Topic Header -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6">
                <div class="flex items-start justify-between mb-4">
                    <div class="flex-1">
                        <h1 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">{{ $topic->title }}</h1>

                        <div class="flex flex-wrap items-center gap-2 mb-4">
                            @if($topic->academicLevel)
                                <span
                                    class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                    {{ $topic->academicLevel->name }}
                                </span>
                            @endif
                            @if($topic->academicSubject)
                                <span
                                    class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    {{ $topic->academicSubject->name }}
                                </span>
                            @endif
                            @if($topic->referencedBook)
                                <span
                                    class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                    </svg>
                                    {{ $topic->referencedBook->title }}
                                </span>
                            @endif
                        </div>

                        <div class="flex items-center space-x-4 text-sm text-gray-500 dark:text-gray-400">
                            <div class="flex items-center">
                                <div
                                    class="w-6 h-6 bg-gray-300 rounded-full flex items-center justify-center text-xs mr-2">
                                    {{ substr($topic->user->name, 0, 1) }}
                                </div>
                                <span>Started by {{ $topic->user->name }}</span>
                            </div>
                            <span>{{ $topic->created_at->diffForHumans() }}</span>
                            <span>{{ $topic->views_count ?? 0 }} {{ Str::plural('view', $topic->views_count ?? 0) }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Posts -->
            @foreach($posts as $index => $post)
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg overflow-hidden">
                    <div class="p-6">
                        <div class="flex items-start space-x-4">
                            <!-- User Avatar -->
                            <div class="flex-shrink-0">
                                <div
                                    class="w-12 h-12 bg-gradient-to-br from-violet-400 to-purple-600 rounded-full flex items-center justify-center text-white font-semibold">
                                    {{ substr($post->user->name, 0, 1) }}
                                </div>
                            </div>

                            <!-- Post Content -->
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center justify-between mb-2">
                                    <div class="flex items-center space-x-2">
                                        <h4 class="font-semibold text-gray-900 dark:text-white">{{ $post->user->name }}</h4>
                                        @if($index === 0)
                                            <span
                                                class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-violet-100 text-violet-800">
                                                Original Post
                                            </span>
                                        @endif
                                        @if($post->is_answer)
                                            <span
                                                class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor"
                                                     viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round"
                                                          stroke-width="2" d="M5 13l4 4L19 7"/>
                                                </svg>
                                                Answer
                                            </span>
                                        @endif
                                    </div>
                                    <span class="text-sm text-gray-500 dark:text-gray-400">
                                        {{ $post->created_at->diffForHumans() }}
                                    </span>
                                </div>

                                <div class="prose prose-sm dark:prose-invert max-w-none mb-4">
                                    {!! nl2br(e($post->content)) !!}
                                </div>

                                <!-- Attachments -->
                                @if($post->attachments && $post->attachments->count() > 0)
                                    <div class="mb-4">
                                        <h5 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                            Attachments:</h5>
                                        <div class="space-y-2">
                                            @foreach($post->attachments as $attachment)
                                                <div
                                                    class="flex items-center p-2 bg-gray-50 dark:bg-gray-700 rounded-lg">
                                                    <svg class="w-5 h-5 text-gray-400 mr-2" fill="none"
                                                         stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                              stroke-width="2"
                                                              d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"/>
                                                    </svg>
                                                    <span
                                                        class="text-sm text-gray-700 dark:text-gray-300">{{ $attachment->file_name }}</span>
                                                    <span class="text-xs text-gray-500 ml-2">({{ $attachment->file_size_human ?? 'Unknown size' }})</span>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif

                                <!-- Post Actions -->
                                <div
                                    class="flex items-center justify-between pt-4 border-t border-gray-200 dark:border-gray-700">
                                    <div class="flex items-center space-x-4">
                                        <button wire:click="toggleLike({{ $post->id }})"
                                                wire:loading.attr="disabled"
                                                wire:target="toggleLike({{ $post->id }})"
                                                class="flex items-center space-x-1 text-sm {{ $post->hasUserLiked(Auth::user()) ? 'text-green-600' : 'text-gray-500 hover:text-green-600' }} transition-colors disabled:opacity-50">
                                            <svg class="w-4 h-4"
                                                 fill="{{ $post->hasUserLiked(Auth::user()) ? 'currentColor' : 'none' }}"
                                                 stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M14 10h4.764a2 2 0 011.789 2.894l-3.5 7A2 2 0 0115.263 21h-4.017c-.163 0-.326-.02-.485-.06L7 20m7-10V5a2 2 0 00-2-2h-.095c-.5 0-.905.405-.905.905 0 .714-.211 1.412-.608 2.006L7 11v9m7-10h-2M7 20H5a2 2 0 01-2-2v-6a2 2 0 012-2h2.5"/>
                                            </svg>
                                            <span wire:loading.remove
                                                  wire:target="toggleLike({{ $post->id }})">{{ $post->likes_count ?? 0 }}</span>
                                            <span wire:loading wire:target="toggleLike({{ $post->id }})">...</span>
                                        </button>

                                        <button wire:click="startReply({{ $post->id }})"
                                                class="flex items-center space-x-1 text-sm text-gray-500 hover:text-violet-600 transition-colors">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/>
                                            </svg>
                                            <span>Reply</span>
                                        </button>


                                        <!-- Share -->
                                        <button wire:click="sharePost({{ $post->id }})"
                                                class="flex items-center space-x-1 text-sm text-gray-500 hover:text-blue-600 transition-colors">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                      d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 005.367 2.684 3 3 0 00-5.367-2.684z"/>
                                            </svg>
                                            <span>Share</span>
                                        </button>
                                    </div>

                                    @if($post->edited_at)
                                        <span class="text-xs text-gray-400">
                                            Last edited {{ $post->edited_at->diffForHumans() }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach

            @if(isset($posts) && $posts->hasPages())
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-4">
                    {{ $posts->links() }}
                </div>
            @endif
        </div>
    @endif

    <!-- Create Topic View -->
    @if($currentView === 'create-topic')
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6">
            <div class="mb-6">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">Create New Topic</h2>
                <p class="text-gray-600 dark:text-gray-400">Start a new discussion in {{ $category->name }}</p>
            </div>

            <form wire:submit="createTopic" class="space-y-6">
                <!-- Topic Title -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Topic Title</label>
                    <input wire:model="newTopicTitle" type="text"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-violet-500 focus:border-violet-500"
                           placeholder="Enter a descriptive title for your topic">
                    @error('newTopicTitle') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                </div>

                <!-- Academic Context -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Academic
                            Level</label>
                        <select wire:model="newTopicAcademicLevel"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-violet-500 focus:border-violet-500">
                            <option value="">Select Level</option>
                            @foreach($academicLevels as $level)
                                <option value="{{ $level->id }}">{{ $level->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Subject</label>
                        <select wire:model="newTopicAcademicSubject"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-violet-500 focus:border-violet-500">
                            <option value="">Select Subject</option>
                            @foreach($academicSubjects as $subject)
                                <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Study
                            Group</label>
                        <select wire:model="newTopicStudyGroup"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-violet-500 focus:border-violet-500">
                            <option value="">Select Group</option>
                            @foreach($studyGroups as $group)
                                <option value="{{ $group->id }}">{{ $group->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Referenced Book -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Referenced Book
                        (Optional)</label>
                    <select wire:model="newTopicReferencedBook"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-violet-500 focus:border-violet-500">
                        <option value="">Select Book</option>
                        @foreach($books as $book)
                            <option value="{{ $book->id }}">{{ $book->title }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Tags -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Tags</label>
                    <input wire:model="newTopicTags" type="text"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-violet-500 focus:border-violet-500"
                           placeholder="Enter tags separated by commas (e.g., math, algebra, homework)">
                    <p class="text-sm text-gray-500 mt-1">Use tags to help others find your topic</p>
                </div>

                <!-- Content -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Content</label>
                    <textarea wire:model="newTopicContent" rows="8"
                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-violet-500 focus:border-violet-500 resize-none"
                              placeholder="Describe your topic in detail. What would you like to discuss?"></textarea>
                    @error('newTopicContent') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                </div>

                <!-- Attachments -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Attachments
                        (Optional)</label>
                    <input wire:model="newTopicAttachments" type="file" multiple
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-violet-500 focus:border-violet-500"
                           accept=".pdf,.doc,.docx,.txt,.jpg,.jpeg,.png,.gif">
                    <p class="text-sm text-gray-500 mt-1">Maximum 10MB per file. Supported formats: PDF, DOC, TXT,
                        Images</p>
                    @error('newTopicAttachments.*') <span
                        class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                </div>

                <!-- Actions -->
                <div class="flex justify-end space-x-4 pt-6 border-t border-gray-200 dark:border-gray-700">
                    <button type="button" wire:click="backToTopics"
                            class="px-6 py-2 text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300 transition-colors">
                        Cancel
                    </button>
                    <button type="submit"
                            wire:loading.attr="disabled"
                            wire:target="createTopic"
                            class="px-6 py-2 bg-violet-600 text-white rounded-lg hover:bg-violet-700 transition-colors flex items-center disabled:opacity-50">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                        </svg>
                        <span wire:loading.remove wire:target="createTopic">Create Topic</span>
                        <span wire:loading wire:target="createTopic">Creating...</span>
                    </button>
                </div>
            </form>
        </div>
    @endif

    <!-- Create Post View -->
    @if($currentView === 'create-post')
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6">
            <div class="mb-6">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">
                    {{ $replyToPostId ? 'Reply to Post' : 'Reply to Topic' }}
                </h2>
                <p class="text-gray-600 dark:text-gray-400">{{ $topic->title }}</p>
                @if($replyToPostId)
                    <p class="text-sm text-gray-500 mt-1">You are replying to post #{{ $replyToPostId }}</p>
                @endif
            </div>

            <form wire:submit="createPost" class="space-y-6">
                <!-- Content -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Your Reply</label>
                    <textarea wire:model="newPostContent" rows="8"
                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-violet-500 focus:border-violet-500 resize-none"
                              placeholder="Write your reply here. You can mention other users with @username"></textarea>
                    @error('newPostContent') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                </div>

                <!-- Attachments -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Attachments
                        (Optional)</label>
                    <input wire:model="newPostAttachments" type="file" multiple
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:ring-violet-500 focus:border-violet-500"
                           accept=".pdf,.doc,.docx,.txt,.jpg,.jpeg,.png,.gif">
                    <p class="text-sm text-gray-500 mt-1">Maximum 10MB per file</p>
                    @error('newPostAttachments.*') <span
                        class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                </div>

                <!-- Actions -->
                <div class="flex justify-end space-x-4 pt-6 border-t border-gray-200 dark:border-gray-700">
                    <button type="button" wire:click="selectTopic({{ $selectedTopic }})"
                            class="px-6 py-2 text-gray-700 bg-gray-200 rounded-lg hover:bg-gray-300 transition-colors">
                        Cancel
                    </button>
                    <button type="submit"
                            wire:loading.attr="disabled"
                            wire:target="createPost"
                            class="px-6 py-2 bg-violet-600 text-white rounded-lg hover:bg-violet-700 transition-colors flex items-center disabled:opacity-50">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                        </svg>
                        <span wire:loading.remove wire:target="createPost">Post Reply</span>
                        <span wire:loading wire:target="createPost">Posting...</span>
                    </button>
                </div>
            </form>
        </div>
    @endif
</div>

@push('scripts')
    <script>
        document.addEventListener('livewire:initialized', () => {
            // Auto-resize textareas
            document.querySelectorAll('textarea').forEach(textarea => {
                textarea.addEventListener('input', function () {
                    this.style.height = 'auto';
                    this.style.height = this.scrollHeight + 'px';
                });
            });

            // Handle @mentions with simple highlighting
            document.addEventListener('input', function (e) {
                if (e.target.matches('textarea')) {
                    const text = e.target.value;
                    const mentionRegex = /@(\w+)/g;
                    let match;
                    const mentions = [];

                    while ((match = mentionRegex.exec(text)) !== null) {
                        mentions.push(match[1]);
                    }

                    // You could implement a mention dropdown here
                    console.log('Mentions found:', mentions);
                }
            });

            // Handle share post functionality
            Livewire.on('sharePost', (event) => {
                const data = event[0];

                // Create shareable URL
                const shareUrl = data.url;
                const shareText = `Check out this discussion: ${data.title}`;

                // Try to use native sharing API if available
                if (navigator.share && navigator.canShare) {
                    navigator.share({
                        title: data.title,
                        text: shareText,
                        url: shareUrl
                    }).catch(err => {
                        console.log('Error sharing:', err);
                        // Fallback to copying to clipboard
                        fallbackShare(shareUrl, shareText);
                    });
                } else {
                    // Fallback to copying to clipboard
                    fallbackShare(shareUrl, shareText);
                }
            });

            function fallbackShare(url, text) {
                // Try to copy to clipboard
                if (navigator.clipboard && navigator.clipboard.writeText) {
                    navigator.clipboard.writeText(url).then(() => {
                        // Show success message
                        showToast('Link copied to clipboard!', 'success');
                    }).catch(() => {
                        // Fallback for older browsers
                        fallbackCopyTextToClipboard(url);
                    });
                } else {
                    fallbackCopyTextToClipboard(url);
                }
            }

            function fallbackCopyTextToClipboard(text) {
                const textArea = document.createElement('textarea');
                textArea.value = text;

                // Avoid scrolling to bottom
                textArea.style.top = '0';
                textArea.style.left = '0';
                textArea.style.position = 'fixed';

                document.body.appendChild(textArea);
                textArea.focus();
                textArea.select();

                try {
                    const successful = document.execCommand('copy');
                    if (successful) {
                        showToast('Link copied to clipboard!', 'success');
                    } else {
                        showToast('Failed to copy link', 'error');
                    }
                } catch (err) {
                    showToast('Failed to copy link', 'error');
                }

                document.body.removeChild(textArea);
            }

            function showToast(message, type = 'info') {
                // Create toast element
                const toast = document.createElement('div');
                toast.className = `fixed top-4 right-4 px-4 py-2 rounded-lg text-white z-50 transition-opacity duration-300 ${
                    type === 'success' ? 'bg-green-500' :
                        type === 'error' ? 'bg-red-500' : 'bg-blue-500'
                }`;
                toast.textContent = message;

                document.body.appendChild(toast);

                // Remove toast after 3 seconds
                setTimeout(() => {
                    toast.style.opacity = '0';
                    setTimeout(() => {
                        document.body.removeChild(toast);
                    }, 300);
                }, 3000);
            }
        });
    </script>
@endpush
