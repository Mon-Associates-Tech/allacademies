@php
    $user = Auth::user();
    $subscribedBookIds = $user->bookSubscriptions()
            ->where('status', 'paid')
            ->pluck('book_id')->toArray() ?: [];

    $borrowedBookIds = $user->borrowedBooks()
            ->where('status', 'borrowed')
            ->pluck('book_id')->toArray() ?: [];
@endphp

<div
    class="group relative bg-white dark:bg-gray-800 rounded-xl overflow-hidden transition-all duration-500 hover:shadow-2xl hover:-translate-y-2 border border-gray-200 dark:border-gray-700 hover:border-indigo-300 dark:hover:border-indigo-600 ring-1 ring-gray-100 dark:ring-gray-700/50 hover:ring-indigo-200 dark:hover:ring-indigo-500/50 h-full">

    <div class="absolute inset-0 opacity-0 group-hover:opacity-100 transition-opacity duration-700 pointer-events-none">
        <div
            class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-bl from-indigo-500/10 to-transparent rounded-full blur-2xl"></div>
        <div
            class="absolute bottom-0 left-0 w-24 h-24 bg-gradient-to-tr from-purple-500/10 to-transparent rounded-full blur-xl"></div>
    </div>

    <div class="relative h-full flex flex-col">
        <!-- Book Cover Section -->
        <div
            class="relative h-64 bg-gradient-to-br from-gray-50 to-gray-100 dark:from-gray-900 dark:to-gray-800  overflow-visible">
            <div
                class="absolute inset-6 bg-gray-900/5 dark:bg-gray-100/5 rounded-xl blur-sm translate-x-1 translate-y-1 group-hover:translate-x-2 group-hover:translate-y-2 transition-transform duration-300"></div>

            <div
                class="relative h-full bg-gradient-to-br from-gray-100 to-gray-200 dark:from-gray-800 dark:to-gray-700 overflow-hidden shadow-sm transform group-hover:scale-105 group-hover:rotate-1 transition-all duration-300">
                @if($book->cover_image)
                    <img src="{{ $book->cover_image }}" alt="{{ $book->title }} cover"
                         class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700"
                         loading="lazy">
                @else
                    <div class="flex items-center justify-center h-full p-4">
                        <div class="text-center">
                            <div
                                class="w-16 h-16 mx-auto mb-3 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-xl flex items-center justify-center shadow-lg">
                                <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                </svg>
                            </div>
                            <p class="text-sm font-semibold text-gray-700 dark:text-gray-300 leading-tight line-clamp-3">
                                {{ $book->title }}
                            </p>
                        </div>
                    </div>
                @endif

                <div class="absolute top-2 right-2 flex flex-col gap-1.5">
                    @if($book->has_softcopy)
                        <div class="w-2.5 h-2.5 bg-blue-500 rounded-full shadow-lg ring-2 ring-white dark:ring-gray-800"
                             title="Digital Copy"></div>
                    @endif
                    @if($book->has_hardcopy)
                        <div
                            class="w-2.5 h-2.5 bg-amber-500 rounded-full shadow-lg ring-2 ring-white dark:ring-gray-800"
                            title="Physical Copy"></div>
                    @endif
                </div>
            </div>

            @if($book->annual_subscription_fee > 0)
                <div
                    class="absolute top-4 -right-2 bg-gradient-to-r from-indigo-600 to-purple-600 text-white text-xs font-bold px-3 py-1.5 rounded-lg shadow-xl transform rotate-3 group-hover:rotate-0 transition-transform duration-300">
                    {{ $book->formatted_subscription_fee }}
                </div>
            @else
                <div
                    class="absolute top-4 -right-2 bg-gradient-to-r from-green-500 to-emerald-600 text-white text-xs font-bold px-3 py-1.5 rounded-lg shadow-xl transform rotate-3 group-hover:rotate-0 transition-transform duration-300">
                    FREE
                </div>
            @endif
        </div>

        <!-- Book Details Section -->
        <div class="flex-1 p-6 flex flex-col">
            <!-- Title & Author -->
            <div class="mb-4">
                <h4 class="font-bold text-gray-900 dark:text-white text-lg/3 line-clamp-2 mb-2 group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors duration-300 leading-tight">
                    {{ $book->title }}
                </h4>

                <div class="flex items-center gap-2">
                    <div
                        class="w-1 h-4 bg-gradient-to-b from-indigo-500 to-purple-600 rounded-full flex-shrink-0"></div>
                    <p class="text-sm text-gray-600 dark:text-gray-300 font-medium truncate">
                        {{ $book->author_name }}
                    </p>
                </div>
            </div>

            <!-- Category & Status -->
            <div class="flex items-center gap-2 mb-4 flex-wrap">
                <span
                    class="inline-flex items-center gap-1.5 text-xs text-gray-500 dark:text-gray-400 bg-gray-100 dark:bg-gray-700/50 px-3 py-1.5 rounded-full">
                    <svg class="w-3.5 h-3.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd"
                              d="M17.707 9.293a1 1 0 010 1.414l-7 7a1 1 0 01-1.414 0l-7-7A.997.997 0 012 10V5a3 3 0 013-3h5c.256 0 .512.098.707.293l7 7zM5 6a1 1 0 100-2 1 1 0 000 2z"
                              clip-rule="evenodd"/>
                    </svg>
                    {{ $book->primaryCategory->name ?? 'Uncategorized' }}
                </span>

                @if(in_array($book->id, $subscribedBookIds))
                    <span
                        class="inline-flex items-center gap-1.5 bg-green-50 dark:bg-green-900/20 text-green-700 dark:text-green-400 text-xs px-3 py-1.5 rounded-full font-semibold border border-green-200 dark:border-green-800">
                        <div class="w-1.5 h-1.5 bg-green-500 rounded-full animate-pulse"></div>
                        Subscribed
                    </span>
                @elseif(in_array($book->id, $borrowedBookIds))
                    <span
                        class="inline-flex items-center gap-1.5 bg-orange-50 dark:bg-orange-900/20 text-orange-700 dark:text-orange-400 text-xs px-3 py-1.5 rounded-full font-semibold border border-orange-200 dark:border-orange-800">
                        <div class="w-1.5 h-1.5 bg-orange-500 rounded-full animate-pulse"></div>
                        Borrowed
                    </span>
                @endif
            </div>

            <!-- Description -->
            <div class="flex-1 mb-4">
                @if($book->description)
                    <div class="relative">
                        <p class="text-sm text-gray-600 dark:text-gray-300 line-clamp-3 leading-relaxed">
                            {{ $book->description }}
                        </p>
                    </div>
                @else
                    <p class="text-sm text-gray-400 dark:text-gray-500 italic">No description available</p>
                @endif
            </div>

            <!-- Action Button -->
            <div class="mt-auto">
                @if(in_array($book->id, $subscribedBookIds) && $book->has_softcopy)
                    <a href="{{ route('books.show', $book) }}"
                       class="block w-full bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 text-white py-3 px-4 rounded-xl font-semibold text-sm text-center transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-105 flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                        </svg>
                        <span>Start Reading</span>
                    </a>
                @else
                    <a href="{{ route('books.show', $book) }}"
                       class="block w-full bg-gray-700 hover:bg-gray-600 dark:bg-gray-700 dark:hover:bg-gray-600 text-white py-3 px-4 rounded-xl font-semibold text-sm text-center transition-all duration-300 shadow-md hover:shadow-lg transform hover:scale-105 flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                        <span>View Details</span>
                    </a>
                @endif
            </div>
        </div>
    </div>
</div>
