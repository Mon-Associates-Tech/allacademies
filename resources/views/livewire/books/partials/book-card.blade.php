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
    class="group relative bg-gradient-to-br h-full from-white via-gray-50/50 to-indigo-50/30 dark:from-gray-900 dark:via-gray-800/50 dark:to-indigo-950/30 rounded-2xl border border-gray-200/60 dark:border-gray-700/60 overflow-hidden transition-all duration-500 hover:shadow-2xl hover:shadow-indigo-500/10 hover:-translate-y-2 hover:border-indigo-300/50 dark:hover:border-indigo-600/50 cursor-pointer">

    <div class="absolute inset-0 opacity-0 group-hover:opacity-100 transition-opacity duration-700">
        <div
            class="absolute top-0 right-0 w-32 h-32 bg-gradient-to-bl from-indigo-200/20 to-transparent dark:from-indigo-400/10 rounded-full blur-2xl"></div>
        <div
            class="absolute bottom-0 left-0 w-24 h-24 bg-gradient-to-tr from-purple-200/20 to-transparent dark:from-purple-400/10 rounded-full blur-xl"></div>
    </div>

    <div class="relative h-full flex flex-col md:flex-row">
        <div class="w-auto h-48 md:w-28 md:h-auto flex-shrink-0 relative m-4">
            <div
                class="absolute inset-0 bg-gray-900/20 dark:bg-gray-100/10 rounded-lg blur-sm translate-x-1 translate-y-1 group-hover:translate-x-2 group-hover:translate-y-2 transition-transform duration-300"></div>

            <div
                class="relative bg-gradient-to-br h-full from-gray-100 to-gray-200 dark:from-gray-800 dark:to-gray-700 rounded-lg overflow-hidden transform group-hover:rotate-2 transition-transform duration-300">
                @if($book->cover_image)
                    <img src="{{ $book->cover_image }}" alt="{{ $book->title }} cover"
                         class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700"
                         loading="lazy">
                @else
                    <div class="flex items-center justify-center h-full">
                        <div class="text-center p-2">
                            <div
                                class="w-8 h-8 mx-auto mb-2 bg-gradient-to-br from-indigo-500 to-purple-600 rounded-lg flex items-center justify-center">
                                <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                          d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                                </svg>
                            </div>
                            <p class="text-xs font-medium text-gray-600 dark:text-gray-300 leading-tight">
                                {{ Str::limit($book->title, 25) }}
                            </p>
                        </div>
                    </div>
                @endif

                <div class="absolute top-1 right-1 flex flex-col gap-1">
                    @if($book->has_softcopy)
                        <div class="w-2 h-2 bg-blue-500 rounded-full shadow-lg"></div>
                    @endif
                    @if($book->has_hardcopy)
                        <div class="w-2 h-2 bg-amber-500 rounded-full shadow-lg"></div>
                    @endif
                </div>
            </div>

            @if($book->annual_subscription_fee > 0)
                <div
                    class="absolute -top-1 -right-1 bg-gradient-to-r from-indigo-500 to-purple-600 text-white text-xs font-bold px-2 py-1 rounded-lg shadow-lg transform rotate-12 group-hover:rotate-0 transition-transform duration-300">
                    {{ $book->formatted_subscription_fee }}
                </div>
            @else
                <div
                    class="absolute -top-1 -right-1 bg-gradient-to-r from-green-500 to-emerald-600 text-white text-xs font-bold px-2 py-1 rounded-lg shadow-lg transform rotate-12 group-hover:rotate-0 transition-transform duration-300">
                    FREE
                </div>
            @endif
        </div>

        <div class="flex-1 p-4 pt-0 md:pl-0 md:pt-4 flex flex-col justify-between">
            <div>
                <h3 class="font-bold text-gray-900 dark:text-white text-base line-clamp-2 mb-1 group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors duration-300">
                    {{ $book->title }}
                </h3>

                <div class="flex items-center gap-2 mb-2">
                    <div class="w-1 h-4 bg-gradient-to-b from-indigo-500 to-purple-600 rounded-full"></div>
                    <p class="text-sm text-gray-600 dark:text-gray-300 font-medium">
                        {{ $book->author->name ?? $book->author->user->name }}
                    </p>
                </div>

                <div class="flex">
                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-2 flex items-center gap-1">
                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd"
                                  d="M17.707 9.293a1 1 0 010 1.414l-7 7a1 1 0 01-1.414 0l-7-7A.997.997 0 012 10V5a3 3 0 013-3h5c.256 0 .512.098.707.293l7 7zM5 6a1 1 0 100-2 1 1 0 000 2z"
                                  clip-rule="evenodd"/>
                        </svg>
                        {{ $book->bookCategory->name ?? 'Uncategorized' }}
                    </p>

                    <div class="flex gap-1.5 mb-3 ml-3">
                        @if(in_array($book->id, $subscribedBookIds))
                            <span
                                class="inline-flex items-center gap-1 bg-green-500/10 text-green-600 dark:text-green-400 text-xs px-2 py-1 rounded-full font-semibold border border-green-200 dark:border-green-800">
                            <div class="w-1.5 h-1.5 bg-green-500 rounded-full animate-pulse"></div>
                            Active
                        </span>
                        @elseif(in_array($book->id, $borrowedBookIds))
                            <span
                                class="inline-flex items-center gap-1 bg-orange-500/10 text-orange-600 dark:text-orange-400 text-xs px-2 py-1 rounded-full font-semibold border border-orange-200 dark:border-orange-800">
                            <div class="w-1.5 h-1.5 bg-orange-500 rounded-full animate-pulse"></div>
                            Borrowed
                        </span>
                        @endif
                    </div>
                </div>


                @if($book->description)
                    <div class="relative mb-3">
                        <p class="text-xs text-gray-600 dark:text-gray-300 line-clamp-2 italic leading-relaxed">
                            "{{ $book->description }}"
                        </p>
                        <div
                            class="absolute -left-1 top-0 w-0.5 h-full bg-gradient-to-b from-indigo-400 to-purple-500 rounded-full opacity-30"></div>
                    </div>
                @endif

            </div>

            <div class="space-y-2">
                @if(in_array($book->id, $subscribedBookIds) && $book->has_softcopy)
                    <a href="{{ route('books.show', $book) }}"
                       class="block w-full bg-gray-900 dark:bg-white hover:bg-gray-800 dark:hover:bg-gray-100 text-white dark:text-gray-900 py-2 px-4 rounded-lg font-medium text-sm text-center transition-colors duration-200 flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                        </svg>
                        <span class="hidden md:block">Start Reading</span>
                        <span class="block md:hidden">Read</span>
                    </a>
                @else
                    <a href="{{ route('books.show', $book) }}"
                       class="block w-full bg-gray-900 dark:bg-white hover:bg-gray-800 dark:hover:bg-gray-100 text-white dark:text-gray-900 py-2 px-4 rounded-lg font-medium text-sm text-center transition-colors duration-200 flex items-center justify-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                        </svg>
                        View <span class="hidden md:block">Details</span>
                    </a>
                @endif
            </div>
        </div>
    </div>

    <div
        class="absolute inset-0 rounded-2xl opacity-0 group-hover:opacity-100 transition-opacity duration-500 pointer-events-none">
        <div
            class="absolute inset-0 rounded-2xl bg-gradient-to-r from-indigo-500/5 via-purple-500/5 to-pink-500/5 blur-xl"></div>
    </div>
</div>

<style>
    @keyframes float {
        0%, 100% {
            transform: translateY(0px);
        }
        50% {
            transform: translateY(-4px);
        }
    }

    .group:hover .book-float {
        animation: float 2s ease-in-out infinite;
    }
</style>
