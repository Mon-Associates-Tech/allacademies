<div>
    <!-- Header -->
    <div class="mb-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Visual Activities</h1>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Browse and download audio & video resources</p>
            </div>
            @if($this->canUpload())
                <a href="{{ route('educational-resources.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Upload Media
                </a>
            @endif
        </div>
    </div>

    <!-- Filters Section -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-4 mb-6">
        <!-- Primary Filters Row - Always Visible -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Search -->
            <div class="lg:col-span-2">
                <label for="search" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Search</label>
                <div class="relative">
                    <input
                        type="text"
                        id="search"
                        wire:model.live.debounce.300ms="search"
                        placeholder="Search by title or description..."
                        class="w-full pl-10 pr-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    >
                    <svg class="absolute left-3 top-2.5 h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
            </div>

            <!-- Tag Search -->
            <div>
                <label for="tagSearch" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Search by Tags</label>
                <input
                    type="text"
                    id="tagSearch"
                    wire:model.live.debounce.300ms="tagSearch"
                    placeholder="Tags (comma separated)..."
                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                >
            </div>

            <!-- Format Filter -->
            <div>
                <label for="format" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Format</label>
                <select
                    id="format"
                    wire:model.live="format"
                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                >
                    <option value="">All Formats</option>
                    @foreach($formats as $fmt)
                        <option value="{{ $fmt }}">{{ ucfirst($fmt) }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <!-- Collapsible Academic Hierarchy Filters -->
        <div x-data="{ expanded: false }" class="mt-4">
            <button
                type="button"
                x-on:click="expanded = !expanded"
                class="flex items-center gap-2 text-sm font-medium text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-200 transition-colors"
            >
                <svg
                    class="w-4 h-4 transition-transform duration-200"
                    :class="{ 'rotate-180': expanded }"
                    fill="none"
                    stroke="currentColor"
                    viewBox="0 0 24 24"
                >
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                </svg>
                <span>Academic Filters</span>
                @if($academicGroupId || $academicLevelId || $academicSubjectId || $topicId || $subtopicId)
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                        Active
                    </span>
                @endif
            </button>

            <div
                x-show="expanded"
                x-collapse
                class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700"
            >
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <!-- Academic Group -->
                    <div>
                        <label for="academicGroupId" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Academic Group</label>
                        <select
                            id="academicGroupId"
                            wire:model.live="academicGroupId"
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                        >
                            <option value="">All Groups</option>
                            @foreach($academicGroups as $group)
                                <option value="{{ $group->id }}">{{ $group->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Academic Level -->
                    <div>
                        <label for="academicLevelId" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Academic Level</label>
                        <select
                            id="academicLevelId"
                            wire:model.live="academicLevelId"
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            @if(!$academicGroupId) disabled @endif
                        >
                            <option value="">All Levels</option>
                            @foreach($academicLevels as $level)
                                <option value="{{ $level->id }}">{{ $level->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Subject -->
                    <div>
                        <label for="academicSubjectId" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Subject</label>
                        <select
                            id="academicSubjectId"
                            wire:model.live="academicSubjectId"
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            @if(!$academicLevelId) disabled @endif
                        >
                            <option value="">All Subjects</option>
                            @foreach($academicSubjects as $subject)
                                <option value="{{ $subject->id }}">{{ $subject->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Topic and Subtopic Filters -->
                @if($academicSubjectId)
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                        <!-- Topic -->
                        <div>
                            <label for="topicId" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Topic</label>
                            <select
                                id="topicId"
                                wire:model.live="topicId"
                                class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                            >
                                <option value="">All Topics</option>
                                @foreach($topics as $topic)
                                    <option value="{{ $topic->id }}">{{ $topic->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Subtopic -->
                        @if($topicId && $subtopics->count() > 0)
                            <div>
                                <label for="subtopicId" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Subtopic</label>
                                <select
                                    id="subtopicId"
                                    wire:model.live="subtopicId"
                                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg bg-white dark:bg-gray-700 text-gray-900 dark:text-white focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                >
                                    <option value="">All Subtopics</option>
                                    @foreach($subtopics as $subtopic)
                                        <option value="{{ $subtopic->id }}">{{ $subtopic->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        @endif
                    </div>
                @endif
            </div>
        </div>

        <!-- Filter Actions -->
        <div class="flex items-center justify-between mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
            <div class="flex items-center gap-4">
                <!-- View Mode Toggle -->
                <div class="flex items-center bg-gray-100 dark:bg-gray-700 rounded-lg p-1">
                    <button
                        wire:click="setViewMode('grid')"
                        class="p-2 rounded {{ $viewMode === 'grid' ? 'bg-white dark:bg-gray-600 shadow-sm' : '' }}"
                        title="Grid View"
                    >
                        <svg class="w-5 h-5 text-gray-600 dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path>
                        </svg>
                    </button>
                    <button
                        wire:click="setViewMode('list')"
                        class="p-2 rounded {{ $viewMode === 'list' ? 'bg-white dark:bg-gray-600 shadow-sm' : '' }}"
                        title="List View"
                    >
                        <svg class="w-5 h-5 text-gray-600 dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path>
                        </svg>
                    </button>
                </div>

                <!-- Sort -->
                <div class="flex items-center gap-2">
                    <span class="text-sm text-gray-500 dark:text-gray-400">Sort:</span>
                    <button wire:click="setSort('created_at')" class="text-sm {{ $sortBy === 'created_at' ? 'text-blue-600 dark:text-blue-400 font-medium' : 'text-gray-600 dark:text-gray-400' }}">
                        Date {{ $sortBy === 'created_at' ? ($sortDirection === 'desc' ? '↓' : '↑') : '' }}
                    </button>
                    <button wire:click="setSort('title')" class="text-sm {{ $sortBy === 'title' ? 'text-blue-600 dark:text-blue-400 font-medium' : 'text-gray-600 dark:text-gray-400' }}">
                        Title {{ $sortBy === 'title' ? ($sortDirection === 'desc' ? '↓' : '↑') : '' }}
                    </button>
                    <button wire:click="setSort('view_count')" class="text-sm {{ $sortBy === 'view_count' ? 'text-blue-600 dark:text-blue-400 font-medium' : 'text-gray-600 dark:text-gray-400' }}">
                        Views {{ $sortBy === 'view_count' ? ($sortDirection === 'desc' ? '↓' : '↑') : '' }}
                    </button>
                </div>
            </div>

            <button wire:click="clearFilters" class="text-sm text-gray-500 dark:text-gray-400 hover:text-gray-700 dark:hover:text-gray-200">
                Clear Filters
            </button>
        </div>
    </div>

    <!-- Results Count -->
    <div class="mb-4 text-sm text-gray-500 dark:text-gray-400">
        Showing {{ $resources->firstItem() ?? 0 }} - {{ $resources->lastItem() ?? 0 }} of {{ $resources->total() }} resources
    </div>

    <!-- Resources Grid/List -->
    @if($resources->count() > 0)
        @if($viewMode === 'grid')
            <!-- Grid View -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                @foreach($resources as $resource)
                    <a href="{{ route('educational-resources.show', $resource) }}" class="group bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden hover:shadow-md transition-shadow">
                        <!-- Format Badge & Preview -->
                        <div class="relative h-40 bg-gray-100 dark:bg-gray-700 flex items-center justify-center">
                            @switch($resource->format)
                                @case('video')
                                    <div class="text-red-500">
                                        <svg class="w-16 h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                    </div>
                                    @break
                                @case('pdf')
                                    <div class="text-orange-500">
                                        <svg class="w-16 h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                        </svg>
                                    </div>
                                    @break
                                @case('image')
                                    <div class="text-green-500">
                                        <svg class="w-16 h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                    </div>
                                    @break
                                @default
                                    <div class="text-blue-500">
                                        <svg class="w-16 h-16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                    </div>
                            @endswitch

                            <!-- Format Badge -->
                            <span class="absolute top-2 right-2 px-2 py-1 text-xs font-medium rounded-full
                                @switch($resource->format)
                                    @case('video') bg-red-100 text-red-800 dark:bg-red-900/50 dark:text-red-300 @break
                                    @case('pdf') bg-orange-100 text-orange-800 dark:bg-orange-900/50 dark:text-orange-300 @break
                                    @case('image') bg-green-100 text-green-800 dark:bg-green-900/50 dark:text-green-300 @break
                                    @default bg-blue-100 text-blue-800 dark:bg-blue-900/50 dark:text-blue-300
                                @endswitch
                            ">
                                {{ ucfirst($resource->format) }}
                            </span>
                        </div>

                        <!-- Content -->
                        <div class="p-4">
                            <h3 class="font-semibold text-gray-900 dark:text-white group-hover:text-blue-600 dark:group-hover:text-blue-400 line-clamp-2 mb-2">
                                {{ $resource->title }}
                            </h3>

                            <p class="text-sm text-gray-500 dark:text-gray-400 mb-3">
                                {{ $resource->academicSubject->name ?? 'N/A' }}
                            </p>

                            <!-- Tags -->
                            @if($resource->tags && count($resource->tags) > 0)
                                <div class="flex flex-wrap gap-1 mb-3">
                                    @foreach(array_slice($resource->tags, 0, 3) as $tag)
                                        <span class="px-2 py-0.5 text-xs bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400 rounded">
                                            {{ $tag }}
                                        </span>
                                    @endforeach
                                    @if(count($resource->tags) > 3)
                                        <span class="px-2 py-0.5 text-xs bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400 rounded">
                                            +{{ count($resource->tags) - 3 }}
                                        </span>
                                    @endif
                                </div>
                            @endif

                            <!-- Meta -->
                            <div class="flex items-center justify-between text-xs text-gray-400 dark:text-gray-500">
                                <span>{{ $resource->formatted_file_size }}</span>
                                <span class="flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                    {{ number_format($resource->view_count) }}
                                </span>
                            </div>
                        </div>
                    </a>
                @endforeach
            </div>
        @else
            <!-- List View -->
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden">
                <div class="divide-y divide-gray-200 dark:divide-gray-700">
                    @foreach($resources as $resource)
                        <a href="{{ route('educational-resources.show', $resource) }}" class="flex items-center p-4 hover:bg-gray-50 dark:hover:bg-gray-700/50 transition-colors">
                            <!-- Icon -->
                            <div class="flex-shrink-0 w-12 h-12 rounded-lg flex items-center justify-center mr-4
                                @switch($resource->format)
                                    @case('video') bg-red-100 dark:bg-red-900/30 text-red-500 @break
                                    @case('pdf') bg-orange-100 dark:bg-orange-900/30 text-orange-500 @break
                                    @case('image') bg-green-100 dark:bg-green-900/30 text-green-500 @break
                                    @default bg-blue-100 dark:bg-blue-900/30 text-blue-500
                                @endswitch
                            ">
                                @switch($resource->format)
                                    @case('video')
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        @break
                                    @case('pdf')
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                        </svg>
                                        @break
                                    @case('image')
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                        @break
                                    @default
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                @endswitch
                            </div>

                            <!-- Content -->
                            <div class="flex-1 min-w-0">
                                <h3 class="font-semibold text-gray-900 dark:text-white truncate">{{ $resource->title }}</h3>
                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                    {{ $resource->academicSubject->name ?? 'N/A' }} • {{ $resource->formatted_file_size }}
                                </p>
                            </div>

                            <!-- Tags -->
                            <div class="hidden md:flex items-center gap-2 mx-4">
                                @if($resource->tags && count($resource->tags) > 0)
                                    @foreach(array_slice($resource->tags, 0, 2) as $tag)
                                        <span class="px-2 py-0.5 text-xs bg-gray-100 dark:bg-gray-700 text-gray-600 dark:text-gray-400 rounded">
                                            {{ $tag }}
                                        </span>
                                    @endforeach
                                @endif
                            </div>

                            <!-- Stats -->
                            <div class="flex items-center gap-4 text-sm text-gray-400 dark:text-gray-500">
                                <span class="flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                    {{ number_format($resource->view_count) }}
                                </span>
                                <span class="flex items-center gap-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path>
                                    </svg>
                                    {{ number_format($resource->download_count) }}
                                </span>
                            </div>

                            <!-- Arrow -->
                            <svg class="w-5 h-5 text-gray-400 ml-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Pagination -->
        <div class="mt-6">
            {{ $resources->links() }}
        </div>
    @else
        <!-- Empty State -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-12 text-center">
            <div class="flex justify-center mb-4">
                <div class="w-16 h-16 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center">
                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                </div>
            </div>
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">No resources found</h3>
            <p class="text-gray-500 dark:text-gray-400 mb-4">Try adjusting your filters or search terms</p>
            <button wire:click="clearFilters" class="inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition-colors">
                Clear All Filters
            </button>
        </div>
    @endif
</div>
