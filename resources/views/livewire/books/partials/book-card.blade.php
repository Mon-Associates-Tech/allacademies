@php
$user = Auth::user();
    $subscribedBookIds = $user->bookSubscriptions()
            ->where('status', 'paid')
            ->pluck('book_id')->toArray() ?: [];

        $borrowedBookIds = $user->borrowedBooks()
            ->where('status', 'borrowed')
            ->pluck('book_id')->toArray() ?: [];
@endphp
<!-- Book Card (Alternative Design) -->
<div class="bg-white dark:bg-gray-900 rounded-xl shadow-sm border border-gray-300 dark:border-gray-800 flex overflow-hidden transition-all duration-300 hover:shadow-md hover:-translate-y-0.5 group">
    <!-- Book Cover -->
    <div class="w-1/3 relative bg-gradient-to-b from-gray-100 to-gray-200 dark:from-gray-800 dark:to-gray-700">
        @if($book->cover_image)
            <img src="{{ $book->cover_image }}" alt="{{ $book->title }} cover"
                 class="w-full h-full object-cover bg-center! transition-opacity duration-300 group-hover:opacity-90"
                 loading="lazy">
        @else
            <div class="flex items-center justify-center h-full bg-gradient-to-b from-gray-200 to-gray-300 dark:from-gray-700 dark:to-gray-600">
                <div class="text-center p-3">
                    <svg class="w-10 h-10 text-gray-400 dark:text-gray-500 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                    <p class="text-xs font-medium text-gray-600 dark:text-gray-400 line-clamp-2">{{ $book->title }}</p>
                </div>
            </div>
        @endif

        <!-- Format Indicators -->
        <div class="absolute bottom-2 hidden left-2 flex gap-1.5">
            @if($book->has_softcopy)
                <span class="bg-white/80 dark:bg-gray-900/80 text-gray-800 dark:text-gray-200 text-xs px-2 py-0.5 rounded-full font-medium shadow-sm">Digital</span>
            @endif
            @if($book->has_hardcopy)
                <span class="bg-white/80 dark:bg-gray-900/80 text-gray-800 dark:text-gray-200 text-xs px-2 py-0.5 rounded-full font-medium shadow-sm">Physical</span>
            @endif
        </div>
    </div>

    <!-- Book Info -->
    <div class="w-2/3 p-4 flex flex-col justify-between">
        <div>
            <h3 class="text-sm font-medium text-gray-900 dark:text-gray-100 line-clamp-2 group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors">
                {{ $book->title }}
            </h3>
            <p class="text-xs text-gray-600 dark:text-gray-300 mb-1.5">
                {{ $book->author->name ?? $book->author->user->name }}
            </p>
            <p class="text-xs text-gray-500 dark:text-gray-400 mb-3">
                {{ $book->bookCategory->name ?? 'Uncategorized' }}
            </p>

            <!-- Status Badges -->
            <div class="flex flex-wrap gap-1.5 mb-3">
                @if(in_array($book->id, $subscribedBookIds))
                    <span class="bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200 text-xs px-2 py-0.5 rounded-full font-medium flex items-center gap-1">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        Subscribed
                    </span>
                @endif
                @if(in_array($book->id, $borrowedBookIds))
                    <span class="bg-orange-100 dark:bg-orange-900 text-orange-800 dark:text-orange-200 text-xs px-2 py-0.5 rounded-full font-medium flex items-center gap-1">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                        </svg>
                        Borrowed
                    </span>
                @endif
                @if(!$book->annual_subscription_fee || $book->annual_subscription_fee == 0)
                    <span class="bg-blue-100 dark:bg-blue-900 text-blue-800 dark:text-blue-200 text-xs px-2 py-0.5 rounded-full font-medium flex items-center gap-1">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Free
                    </span>
                @endif
            </div>

            @if($book->annual_subscription_fee > 0)
                <div class="text-lg font-semibold text-indigo-600 dark:text-indigo-400 mb-3">
                    {{ $book->formatted_subscription_fee }}
                    <span class="text-xs font-normal text-gray-500 dark:text-gray-400">/year</span>
                </div>
            @endif
        </div>

        <!-- Action Buttons -->
        <div class="flex flex-col gap-2">
            <a href="{{ route('books.show', $book) }}"
               class="w-full text-xs bg-indigo-100 dark:bg-indigo-900 hover:bg-indigo-200 dark:hover:bg-indigo-800 text-indigo-800 dark:text-indigo-200 py-2 px-3 rounded-lg font-medium text-center transition-colors duration-200 flex items-center justify-center gap-1.5">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
                View Details
            </a>

            @if(in_array($book->id, $subscribedBookIds) && $book->has_softcopy)
                <a href="{{ route('books.read', $book) }}"
                   class="w-full bg-green-100 dark:bg-green-900 hover:bg-green-200 dark:hover:bg-green-800 text-green-800 dark:text-green-200 py-2 px-3 rounded-lg font-medium text-center transition-colors duration-200 flex items-center justify-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                    Read Now
                </a>
            @endif
        </div>
    </div>
</div>
