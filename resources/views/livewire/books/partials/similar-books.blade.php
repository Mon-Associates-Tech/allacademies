<!-- Similar Books Section -->
<div class="mb-16">
    @if($similarBooks->count() > 0)
        <div class="flex items-center justify-between mb-8">
            <div class="flex items-center gap-4">
                <div class="w-1 h-12 bg-gradient-to-b from-indigo-500 to-purple-600 rounded-full"></div>
                <div>
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-white">
                        {{$heading ?? 'Similar Books'}}
                    </h2>
                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">
                        {{ $similarBooks->count() }} {{ Str::plural('book', $similarBooks->count()) }} you might enjoy
                    </p>
                </div>
            </div>
            <a href="{{ route('books.index', ['search' => $book->author_name]) }}"
               class="group flex items-center gap-2 px-4 py-2 text-sm text-indigo-600 dark:text-indigo-400 hover:text-indigo-700 dark:hover:text-indigo-300 font-semibold bg-indigo-50 dark:bg-indigo-900/20 hover:bg-indigo-100 dark:hover:bg-indigo-900/30 rounded-xl transition-all duration-200">
                <span>See All</span>
                <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </a>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @foreach($similarBooks as $book)
                @if($book->id !== $currentBook->id)
                    @include('livewire.books.partials.book-card', ['book' => $book])
                @endif
            @endforeach
        </div>
    @else
        <!-- Empty State -->
        <div class="text-center py-16 bg-gray-50 dark:bg-gray-800/50 rounded-2xl border-2 border-dashed border-gray-300 dark:border-gray-600 dark:border-gray-700">
            <div class="w-20 h-20 bg-gradient-to-br from-gray-100 to-gray-200 dark:from-gray-700 dark:to-gray-600 rounded-full mx-auto flex items-center justify-center mb-4">
                <svg class="w-10 h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                          d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                </svg>
            </div>
            <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">No similar books found</h3>
            <p class="text-gray-600 dark:text-gray-400 text-sm">Explore other categories to find more books.</p>
        </div>
    @endif
</div>
