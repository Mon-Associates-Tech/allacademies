@props(['category'])
<a href="{{ route('books.index') }}?category={{ $category->id }}"
   class="block bg-white dark:bg-gray-800 rounded-lg shadow-md p-4 hover:shadow-lg transition-shadow">
    <div class="text-center">
        <div class="w-12 h-12 bg-violet-100 dark:bg-violet-900 rounded-full flex items-center justify-center mx-auto mb-2">
            <svg class="w-6 h-6 text-violet-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
            </svg>
        </div>
        <h3 class="font-medium text-gray-900 dark:text-white text-sm mb-1">{{ $category->name }}</h3>
        <p class="text-xs text-gray-500 dark:text-gray-400">{{ $category->books_count }} books</p>
    </div>
</a>
