<div class="space-y-6">
    <!-- Books Grid -->
    @if($books && $books->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @foreach($books as $book)
                <div class="bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden hover:shadow-lg transition-all duration-300 group">
                    <!-- Book Cover -->
                    <div class="relative h-48 bg-gradient-to-br from-emerald-50 to-teal-50 dark:from-gray-700 dark:to-gray-600 flex items-center justify-center">
                        @if($book->cover_image)
                            <img src="{{  $book->cover_image }}"
                                 alt="{{ $book->title }}"
                                 class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                        @else
                            <div class="text-center p-4">
                                <svg class="w-16 h-16 text-emerald-300 dark:text-gray-400 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                </svg>
                                <p class="text-sm text-gray-500 dark:text-gray-400 font-medium">{{ $book->title }}</p>
                            </div>
                        @endif

                        <!-- Format Badge -->
                        <div class="absolute top-3 right-3">
                            <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                {{ $book->format === 'softcopy' ? 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200' : 'bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-200' }}">
                                {{ $book->format === 'softcopy' ? '📱 Digital' : '📚 Physical' }}
                            </span>
                        </div>

                        <!-- Price Badge -->
                        @if($book->price > 0)
                            <div class="absolute top-3 left-3">
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200">
                                    ${{ number_format($book->price, 2) }}
                                </span>
                            </div>
                        @else
                            <div class="absolute top-3 left-3">
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800 dark:bg-emerald-900 dark:text-emerald-200">
                                    🆓 Free
                                </span>
                            </div>
                        @endif
                    </div>

                    <!-- Book Details -->
                    <div class="p-4 space-y-3">
                        <div>
                            <h3 class="font-semibold text-gray-900 dark:text-gray-100 text-sm line-clamp-2 group-hover:text-emerald-600 dark:group-hover:text-emerald-400 transition-colors">
                                {{ $book->title }}
                            </h3>
                            @if($book->author)
                                <p class="text-xs text-gray-600 dark:text-gray-400 mt-1">by {{ $book->author }}</p>
                            @endif
                        </div>

                        @if($book->category)
                            <div class="flex items-center space-x-1">
                                <svg class="w-3 h-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                                </svg>
                                <span class="text-xs text-gray-500 dark:text-gray-400">{{ $book->category->name ?? 'Uncategorized' }}</span>
                            </div>
                        @endif

                        @if($book->description)
                            <p class="text-xs text-gray-600 dark:text-gray-400 line-clamp-2">{{ $book->description }}</p>
                        @endif

                        <!-- Actions -->
                        <!-- In the book card actions section, replace existing buttons with: -->
                        <div class="flex items-center justify-between pt-2 space-x-2">
                            @php
                                $bookStatus = $this->getBookStatus($book->id);
                                $isFree = $this->isBookFree($book->id);
                            @endphp

                            @if($bookStatus)
                                @if($bookStatus['type'] === 'free' || $bookStatus['type'] === 'subscribed' || $bookStatus['type'] === 'group_subscribed')
                                    <!-- Book is accessible - show read button -->
                                    @if($book->format === 'softcopy' && $book->content_url)
                                        <button wire:click="openPdfReader({{ $book->id }})"
                                                class="flex-1 inline-flex items-center justify-center px-3 py-1.5 bg-green-600 hover:bg-green-700 text-white text-xs font-medium rounded-lg transition-colors">
                                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                            </svg>
                                            Read Now
                                        </button>
                                    @endif
                                @elseif($bookStatus['type'] === 'pending')
                                    <!-- Pending payment -->
                                    <div class="flex-1 text-center">
                <span class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-medium {{ $bookStatus['class'] }}">
                    {{ $bookStatus['label'] }}
                </span>
                                    </div>
                                @endif

                                <!-- Show status badge -->
                                <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium {{ $bookStatus['class'] }}">
            {{ $bookStatus['label'] }}
        </span>
                            @else
                                <!-- Not subscribed - show subscribe button -->
                                @if($isFree)
                                    <button wire:click="subscribeToBook({{ $book->id }})"
                                            class="flex-1 inline-flex items-center justify-center px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-medium rounded-lg transition-colors">
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                        </svg>
                                        Add Free Book
                                    </button>
                                @else
                                    <button wire:click="confirmSubscription({{ $book->id }})"
                                            class="flex-1 inline-flex items-center justify-center px-3 py-1.5 bg-purple-600 hover:bg-purple-700 text-white text-xs font-medium rounded-lg transition-colors">
                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                                        </svg>
                                        Subscribe GHS {{ number_format($book->annual_subscription_fee, 2) }}
                                    </button>
                                @endif
                            @endif
                        </div>                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        @if($books->hasPages())
            <div class="mt-6">
                {{ $books->links() }}
            </div>
        @endif
    @else
        <!-- Empty State -->
        <div class="text-center py-12">
            <div class="mx-auto w-24 h-24 bg-gray-100 dark:bg-gray-700 rounded-full flex items-center justify-center mb-4">
                <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                </svg>
            </div>
            <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-2">No books found</h3>
            <p class="text-gray-600 dark:text-gray-400 mb-4">
                @if($search || $selectedCategory || $selectedFormat || $selectedPrice)
                    Try adjusting your filters to find more books.
                @else
                    There are no books available at the moment.
                @endif
            </p>
            @if($search || $selectedCategory || $selectedFormat || $selectedPrice)
                <button wire:click="clearFilters"
                        class="inline-flex items-center px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium rounded-lg transition-colors">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                    Clear Filters
                </button>
            @endif
        </div>
    @endif
</div>
