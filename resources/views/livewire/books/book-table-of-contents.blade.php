<div x-data="{
    highlightedChapter: @entangle('highlightedChapter'),
    removeHighlightTimeout: null
}"
     @scroll-to-chapter.window="
        $nextTick(() => {
            const element = document.getElementById('chapter-' + $event.detail.chapterNumber);
            if (element) {
                element.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }
        })
     "
     @remove-highlight-after-delay.window="
        clearTimeout(removeHighlightTimeout);
        removeHighlightTimeout = setTimeout(() => {
            $wire.set('highlightedChapter', null);
        }, 3000);
     "
     @download-toc.window="
        const blob = new Blob([$event.detail.content], { type: 'text/plain' });
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.href = url;
        a.download = $event.detail.filename;
        document.body.appendChild(a);
        a.click();
        document.body.removeChild(a);
        window.URL.revokeObjectURL(url);
     "
     class="space-y-6">

    <!-- Header with Controls -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-6">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between space-y-4 lg:space-y-0">
            <div>
                <h2 class="text-2xl font-bold text-gray-900 dark:text-white mb-2">Table of Contents</h2>
                <div class="flex flex-wrap items-center gap-4 text-sm text-gray-600 dark:text-gray-400">
                    <span>{{ $totalChapters }} chapters</span>
                    <span>•</span>
                    <span>{{ $totalPages }} pages</span>
                    <span>•</span>
                    <span>Est. {{ $estimatedReadingTime }}</span>
                </div>
            </div>

            <!-- Action Buttons -->
            <div class="flex flex-wrap items-center gap-3">
                <!-- View Mode Toggle -->
                <div class="flex bg-gray-100 dark:bg-gray-700 rounded-lg p-1">
                    <button wire:click="setViewMode('detailed')"
                            class="px-3 py-1 text-sm font-medium rounded-md transition-colors {{ $viewMode === 'detailed' ? 'bg-white dark:bg-gray-600 text-gray-900 dark:text-white shadow-sm' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white' }}">
                        Detailed
                    </button>
                    <button wire:click="setViewMode('compact')"
                            class="px-3 py-1 text-sm font-medium rounded-md transition-colors {{ $viewMode === 'compact' ? 'bg-white dark:bg-gray-600 text-gray-900 dark:text-white shadow-sm' : 'text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white' }}">
                        Compact
                    </button>
                </div>

                <!-- Page Numbers Toggle -->
                <button wire:click="togglePageNumbers"
                        class="inline-flex items-center px-3 py-2 border border-gray-300 dark:border-gray-600 shadow-sm text-sm font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                    </svg>
                    {{ $showPageNumbers ? 'Hide' : 'Show' }} Pages
                </button>

                <!-- Export Button -->
                <button wire:click="exportToc"
                        class="inline-flex items-center px-3 py-2 border border-gray-300 dark:border-gray-600 shadow-sm text-sm font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    Export
                </button>
            </div>
        </div>

        <!-- Search and Expand/Collapse Controls -->
        <div class="mt-6 flex flex-col sm:flex-row sm:items-center space-y-3 sm:space-y-0 sm:space-x-4">
            <!-- Search Input -->
            <div class="flex-1 relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                    </svg>
                </div>
                <input type="text"
                       wire:model.live.debounce.300ms="searchTerm"
                       placeholder="Search chapters, sections..."
                       class="block w-full pl-10 pr-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md leading-5 bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-blue-500 focus:border-blue-500">
                @if($searchTerm)
                    <button wire:click="clearSearch"
                            class="absolute inset-y-0 right-0 pr-3 flex items-center">
                        <svg class="h-5 w-5 text-gray-400 hover:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                @endif
            </div>

            <!-- Expand/Collapse Buttons -->
            <div class="flex space-x-2">
                <button wire:click="expandAll"
                        class="inline-flex items-center px-3 py-2 border border-gray-300 dark:border-gray-600 shadow-sm text-sm font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    Expand All
                </button>
                <button wire:click="collapseAll"
                        class="inline-flex items-center px-3 py-2 border border-gray-300 dark:border-gray-600 shadow-sm text-sm font-medium rounded-md text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 transition-colors">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>
                    </svg>
                    Collapse All
                </button>
            </div>
        </div>

        <!-- Search Results Info -->
        @if($searchTerm)
            <div class="mt-4 p-3 bg-blue-50 dark:bg-blue-900 rounded-lg">
                <div class="flex items-center">
                    <svg class="w-5 h-5 text-blue-400 mr-2" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd"/>
                    </svg>
                    <span class="text-sm text-blue-800 dark:text-blue-200">
                        Found {{ count($filteredChapters) }} chapter{{ count($filteredChapters) !== 1 ? 's' : '' }} matching "{{ $searchTerm }}"
                    </span>
                </div>
            </div>
        @endif
    </div>

    <!-- Table of Contents List -->
    @if(count($filteredChapters) > 0)
        <div class="space-y-4">
            @foreach($filteredChapters as $chapter)
                <div id="chapter-{{ $chapter['chapter_number'] }}"
                     class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 transition-all duration-300 {{ $highlightedChapter === $chapter['chapter_number'] ? 'ring-2 ring-blue-500 shadow-lg' : '' }}">

                    <!-- Chapter Header -->
                    <div class="p-6 {{ $viewMode === 'compact' ? 'py-4' : '' }}">
                        <div class="flex items-start justify-between">
                            <div class="flex-1 min-w-0">
                                <!-- Chapter Title and Number -->
                                <div class="flex items-start space-x-3">
                                    <div class="flex-shrink-0">
                                        <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center text-white text-sm font-semibold">
                                            {{ $chapter['chapter_number'] }}
                                        </div>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white {{ $viewMode === 'compact' ? 'text-base' : '' }}">
                                            {{ $chapter['title'] }}
                                        </h3>

                                        @if($viewMode === 'detailed' && $chapter['description'])
                                            <p class="mt-2 text-gray-600 dark:text-gray-400 text-sm leading-relaxed">
                                                {{ $chapter['description'] }}
                                            </p>
                                        @endif

                                        <!-- Chapter Meta Information -->
                                        <div class="flex flex-wrap items-center gap-4 mt-3 text-xs text-gray-500 dark:text-gray-400">
                                            @if($showPageNumbers && $chapter['page_range'])
                                                <span class="inline-flex items-center px-2 py-1 rounded-full bg-gray-100 dark:bg-gray-700">
                                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                                                    </svg>
                                                    {{ $chapter['page_range'] }}
                                                </span>
                                            @endif

                                            @if($chapter['page_count'] > 0)
                                                <span class="inline-flex items-center px-2 py-1 rounded-full bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200">
                                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                                    </svg>
                                                    {{ $this->getEstimatedChapterReadingTime($chapter) }}
                                                </span>
                                            @endif

                                            @if(!empty($chapter['sections']))
                                                <span class="inline-flex items-center px-2 py-1 rounded-full bg-purple-100 dark:bg-purple-900 text-purple-800 dark:text-purple-200">
                                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                                                    </svg>
                                                    {{ count($chapter['sections']) }} section{{ count($chapter['sections']) !== 1 ? 's' : '' }}
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Chapter Actions -->
                            <div class="flex items-center space-x-2 ml-4">
                                <!-- Jump to Chapter Button -->
                                <button wire:click="jumpToChapter({{ $chapter['chapter_number'] }})"
                                        class="p-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 rounded-md hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors"
                                        title="Highlight this chapter">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                    </svg>
                                </button>

                                <!-- Expand/Collapse Button -->
                                @if(!empty($chapter['sections']))
                                    <button wire:click="toggleChapter({{ $chapter['chapter_number'] }})"
                                            class="p-2 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 rounded-md hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                                        <svg class="w-4 h-4 transition-transform {{ in_array($chapter['chapter_number'], $expandedChapters) ? 'rotate-180' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                        </svg>
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Chapter Sections (Expandable) -->
                    @if(!empty($chapter['sections']) && in_array($chapter['chapter_number'], $expandedChapters))
                        <div class="border-t border-gray-200 dark:border-gray-600 bg-gray-50 dark:bg-gray-700/50">
                            <div class="p-6 pt-4">
                                <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-3 flex items-center">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                                    </svg>
                                    Sections
                                </h4>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-2">
                                    @foreach($chapter['sections'] as $index => $section)
                                        <div class="flex items-center p-3 bg-white dark:bg-gray-800 rounded-lg border border-gray-200 dark:border-gray-600">
                                            <div class="flex-shrink-0 w-6 h-6 bg-gray-200 dark:bg-gray-600 rounded-full flex items-center justify-center text-xs font-medium text-gray-600 dark:text-gray-300 mr-3">
                                                {{ $index + 1 }}
                                            </div>
                                            <span class="text-sm text-gray-700 dark:text-gray-300 truncate">{{ $section['title'] }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
    @else
        <!-- No Results State -->
        <div class="bg-white dark:bg-gray-800 rounded-lg shadow-sm border border-gray-200 dark:border-gray-700 p-12 text-center">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900 dark:text-white">No chapters found</h3>
            <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">
                @if($searchTerm)
                    No chapters match your search term "{{ $searchTerm }}".
                @else
                    This book doesn't have a table of contents available yet.
                @endif
            </p>
            @if($searchTerm)
                <button wire:click="clearSearch"
                        class="mt-3 inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                    Clear Search
                </button>
            @endif
        </div>
    @endif

    <!-- Quick Navigation (Floating) -->
    @if(count($filteredChapters) > 5)
        <div class="fixed bottom-6 right-6 z-40" x-data="{ showQuickNav: false }">
            <button @click="showQuickNav = !showQuickNav"
                    class="bg-blue-600 hover:bg-blue-700 text-white p-3 rounded-full shadow-lg transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"/>
                </svg>
            </button>

            <div x-show="showQuickNav"
                 x-transition
                 @click.away="showQuickNav = false"
                 class="absolute bottom-16 right-0 w-64 bg-white dark:bg-gray-800 rounded-lg shadow-xl border border-gray-200 dark:border-gray-700 max-h-80 overflow-y-auto">
                <div class="p-3 border-b border-gray-200 dark:border-gray-600">
                    <h4 class="font-medium text-gray-900 dark:text-white text-sm">Quick Navigation</h4>
                </div>
                <div class="p-2">
                    @foreach($filteredChapters as $chapter)
                        <button wire:click="jumpToChapter({{ $chapter['chapter_number'] }})"
                                @click="showQuickNav = false"
                                class="w-full text-left p-2 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-100 dark:hover:bg-gray-700 rounded-md transition-colors">
                            <div class="flex items-center">
                                <span class="w-6 h-6 bg-blue-100 dark:bg-blue-900 text-blue-600 dark:text-blue-400 rounded-full flex items-center justify-center text-xs font-medium mr-3">
                                    {{ $chapter['chapter_number'] }}
                                </span>
                                <span class="truncate">{{ $chapter['title'] }}</span>
                            </div>
                        </button>
                    @endforeach
                </div>
            </div>
        </div>
    @endif
</div>
