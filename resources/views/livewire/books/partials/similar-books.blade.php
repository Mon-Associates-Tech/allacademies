<!-- Similar Books Section -->
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-2">
    @if($similarBooks->count() > 0)
        <div class="flex justify-between">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">{{$heading ?? 'Similar Books'}}</h2>
            <a href="{{ route('books.index', ['search' =>$book->author->name ?? $book->author->user?->name]) }}"
               class="ml-1 text-sm font-medium text-gray-700 hover:text-blue-600 dark:text-gray-400 dark:hover:text-white md:ml-2">
                See All
            </a>
        </div>

        <div
            class="flex overflow-x-auto space-x-4 pb-4 snap-x snap-mandatory scrollbar-thin scrollbar-thumb-gray-300 dark:scrollbar-thumb-gray-600 scrollbar-track-gray-100 dark:scrollbar-track-gray-800">
            @foreach($similarBooks as $book)
                @if($book->id !== $currentBook->id)
                    @include('livewire.books.partials.book-card', ['book' => $book])
                @endif
            @endforeach
        </div>
    @else
        <!-- Empty State -->
        <div class="text-center py-6">
            <svg class="w-16 h-16 text-gray-300 dark:text-gray-600 mx-auto mb-3" fill="none" stroke="currentColor"
                 viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
            </svg>
            <h3 class="text-md font-medium text-gray-900 dark:text-white mb-2">No similar books found</h3>
            <p class="text-gray-600 dark:text-gray-400 text-sm">Explore other categories to find more books.</p>
        </div>
    @endif
</div>

<style>
    /* Custom Scrollbar Styles */
    .scrollbar-thin {
        scrollbar-width: thin;
    }

    .scrollbar-thin::-webkit-scrollbar {
        height: 8px;
    }

    .scrollbar-thin::-webkit-scrollbar-thumb {
        background-color: #d1d5db;
        border-radius: 9999px;
    }

    .scrollbar-thin::-webkit-scrollbar-track {
        background-color: #f3f4f6;
        border-radius: 9999px;
    }

    .dark .scrollbar-thin::-webkit-scrollbar-thumb {
        background-color: #4b5563;
    }

    .dark .scrollbar-thin::-webkit-scrollbar-track {
        background-color: #1f2937;
    }
</style>
