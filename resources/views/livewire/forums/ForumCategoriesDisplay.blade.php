<div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg overflow-hidden">
    <div class="px-6 py-4 bg-gradient-to-r from-violet-50 to-purple-50 dark:from-gray-700 dark:to-gray-800 border-b border-gray-200 dark:border-gray-700">
        <h2 class="text-lg font-semibold text-gray-900 dark:text-white flex items-center">
            <svg class="w-6 h-6 mr-2 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14-7H5v16h14V4z"/>
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
                            @if($category->icon)
                                <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-{{ $category->color ?? 'violet' }}-400 to-{{ $category->color ?? 'purple' }}-600 flex items-center justify-center text-white">
                                    <i class="{{ $category->icon }}"></i>
                                </div>
                            @else
                                <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-violet-400 to-purple-600 flex items-center justify-center text-white">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                                    </svg>
                                </div>
                            @endif
                            
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white group-hover:text-violet-600 transition-colors">
                                    {{ $category->name }}
                                </h3>
                                @if($category->academicLevel || $category->academicSubject)
                                    <div class="flex items-center space-x-2 mt-1">
                                        @if($category->academicLevel)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                {{ $category->academicLevel->name }}
                                            </span>
                                        @endif
                                        @if($category->academicSubject)
                                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
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
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 8h10M7 12h4m1 8l-4-4H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-3l-4 4z"/>
                                </svg>
                                <span class="font-medium">{{ number_format($category->topics_count) }}</span>
                                <span class="ml-1">{{ Str::plural('topic', $category->topics_count) }}</span>
                            </div>
                            <div class="flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                                </svg>
                                <span class="font-medium">{{ number_format($category->posts_count) }}</span>
                                <span class="ml-1">{{ Str::plural('post', $category->posts_count) }}</span>
                            </div>
                            @if($category->is_private)
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-amber-100 text-amber-800">
                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                    </svg>
                                    Private
                                </span>
                            @endif
                        </div>
                    </div>
                    
                    @if($category->latestPost)
                        <div class="text-right text-sm text-gray-500 dark:text-gray-400 ml-4 flex-shrink-0">
                            <div class="flex items-center justify-end mb-1">
                                <div class="w-6 h-6 bg-gray-300 rounded-full flex items-center justify-center text-xs font-medium mr-2">
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
                <svg class="w-12 h-12 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                </svg>
                <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-2">No categories available</h3>
                <p class="text-gray-600 dark:text-gray-400">Forum categories will appear here once they are created.</p>
            </div>
        @endforelse
    </div>
</div>
