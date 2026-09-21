@php use App\Models\BookSubscription; @endphp
@if($book)
    <div class="group relative flex bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden hover:shadow-lg hover:border-indigo-200 dark:hover:border-indigo-800 transition-all duration-300">

        <!-- Book Cover -->
        <div class="relative w-36 sm:w-44 flex-shrink-0 bg-gradient-to-br from-gray-100 to-gray-200 dark:from-gray-700 dark:to-gray-800 overflow-hidden">
            <img src="{{ $book->cover_image }}"
                 alt="{{ $book->title }}"
                 class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500 ease-out"
                 loading="lazy">

            <!-- Status Badge -->
            @php $bookStatus = $this->getBookStatus($book->id); @endphp
            @if($bookStatus)
                <div class="absolute top-2 left-2 z-10">
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold shadow-sm backdrop-blur-sm {{ $bookStatus['class'] }}">
                        @if($bookStatus['type'] === 'free')
                            <svg class="w-2.5 h-2.5 mr-0.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                        @elseif($bookStatus['type'] === 'subscribed')
                            <svg class="w-2.5 h-2.5 mr-0.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                        @elseif($bookStatus['type'] === 'group_subscribed')
                            <svg class="w-2.5 h-2.5 mr-0.5" fill="currentColor" viewBox="0 0 20 20"><path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3z"></path></svg>
                        @elseif($bookStatus['type'] === 'pending')
                            <svg class="w-2.5 h-2.5 mr-0.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path></svg>
                        @endif
                        {{ $bookStatus['label'] }}
                    </span>
                </div>
            @endif

            <!-- Price Tag -->
            @if($book->annual_subscription_fee && $book->annual_subscription_fee > 0)
                <div class="absolute bottom-2 left-2 px-2 py-0.5 bg-black/70 backdrop-blur-sm rounded-full">
                    <span class="text-white text-[10px] font-bold">GHS {{ number_format($book->annual_subscription_fee) }}</span>
                </div>
            @else
                <div class="absolute bottom-2 left-2 px-2 py-0.5 bg-emerald-600/90 backdrop-blur-sm rounded-full">
                    <span class="text-white text-[10px] font-bold">Free</span>
                </div>
            @endif
        </div>

        <!-- Content -->
        <div class="flex flex-col flex-1 p-4 sm:p-5 min-w-0">
            <!-- Header -->
            <div class="flex items-start justify-between gap-2 mb-1">
                <h3 class="text-sm font-semibold text-gray-900 dark:text-white line-clamp-2 group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors duration-200 leading-snug">
                    {{ $book->title }}
                </h3>
                <button wire:click="addToWishlist({{ $book->id }})"
                        class="flex-shrink-0 p-1.5 rounded-lg text-gray-400 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 transition-all duration-200">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                    </svg>
                </button>
            </div>

            <!-- Author -->
            <p class="text-xs text-gray-500 dark:text-gray-400 mb-3 flex items-center">
                <svg class="w-3 h-3 mr-1 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path>
                </svg>
                {{ $book->author_name }}
            </p>

            <!-- Badges Row -->
            <div class="flex flex-wrap items-center gap-1.5 mb-3">
                @if($book->bookCategory)
                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-medium bg-indigo-50 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-300">
                        {{ $book->bookCategory->name }}
                    </span>
                @endif
                @if($book->has_hardcopy)
                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-medium bg-sky-50 text-sky-700 dark:bg-sky-900/30 dark:text-sky-300">
                        <svg class="w-2.5 h-2.5 mr-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C20.832 18.477 19.246 18 17.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                        Hardcopy
                    </span>
                @endif
                @if($book->has_softcopy)
                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-medium bg-emerald-50 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300">
                        <svg class="w-2.5 h-2.5 mr-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                        Digital
                    </span>
                @endif
            </div>

            <!-- Stats -->
            <div class="flex items-center gap-4 text-[11px] text-gray-400 dark:text-gray-500 mb-3">
                <span class="flex items-center">
                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    {{ $book->subscriptions_count ?? 0 }} subscribers
                </span>
                <span class="flex items-center">
                    <svg class="w-3 h-3 mr-1 text-amber-400" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                    {{ $book->rating ?? 0 }}/5
                </span>
            </div>

            <!-- Actions -->
            <div class="mt-auto flex items-center gap-2">
                @php $hasAccess = $this->hasBookAccess($book->id); @endphp

                @if($hasAccess && $book->has_softcopy)
                    <a href="{{ route('books.read', $book) }}"
                       class="inline-flex items-center px-3 py-1.5 text-xs font-medium rounded-lg text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-1 focus:ring-indigo-500 transition-all duration-200 shadow-sm">
                        <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C20.832 18.477 19.246 18 17.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                        Read Now
                    </a>
                @endif

                <a href="{{ route('books.show', $book) }}"
                   class="inline-flex items-center px-3 py-1.5 text-xs font-medium rounded-lg text-gray-600 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 transition-all duration-200">
                    Details
                </a>

                <button wire:click="showBookPreview({{ $book->id }})"
                        class="inline-flex items-center px-3 py-1.5 text-xs font-medium rounded-lg text-gray-500 dark:text-gray-400 border border-gray-200 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-700 transition-all duration-200">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                </button>
            </div>
        </div>
    </div>
@endif


@php use App\Models\BookSubscription; @endphp
@if($book)
    <div class="group relative bg-white dark:bg-gray-900 rounded-2xl border border-gray-100 dark:border-gray-800 overflow-hidden hover:border-gray-300 dark:hover:border-gray-600 hover:shadow-xl hover:shadow-gray-200/50 dark:hover:shadow-black/30 transition-all duration-500">

        @php $bookStatus = $this->getBookStatus($book->id); @endphp

            <!-- Cover Area -->
        <div class="relative aspect-[3/4] w-full bg-gray-50 dark:bg-gray-800 overflow-hidden">
            <img src="{{ $book->cover_image }}"
                 alt="{{ $book->title }}"
                 class="w-full h-full object-cover group-hover:scale-[1.03] transition-transform duration-700 ease-out"
                 loading="lazy">

            <!-- Soft top gradient for badge readability -->
            <div class="absolute inset-x-0 top-0 h-16 bg-gradient-to-b from-black/20 to-transparent"></div>

            <!-- Status Badge -->
            @if($bookStatus)
                <div class="absolute top-3 left-3 z-10">
                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[11px] font-medium shadow-sm backdrop-blur-md {{ $bookStatus['class'] }}">
                        @if($bookStatus['type'] === 'free')
                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                        @elseif($bookStatus['type'] === 'subscribed')
                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                        @elseif($bookStatus['type'] === 'group_subscribed')
                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20"><path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3z"></path></svg>
                        @elseif($bookStatus['type'] === 'pending')
                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path></svg>
                        @endif
                        {{ $bookStatus['label'] }}
                    </span>
                </div>
            @endif

            <!-- Wishlist floating button -->
            <button wire:click="addToWishlist({{ $book->id }})"
                    class="absolute top-3 right-3 z-10 p-2 rounded-full bg-white/80 dark:bg-gray-900/80 backdrop-blur-sm text-gray-400 hover:text-red-500 shadow-sm hover:shadow-md transition-all duration-200 opacity-0 group-hover:opacity-100 translate-y-1 group-hover:translate-y-0">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                </svg>
            </button>

            <!-- Bottom info strip on cover -->
            <div class="absolute inset-x-0 bottom-0 p-3 bg-gradient-to-t from-black/60 via-black/20 to-transparent translate-y-full group-hover:translate-y-0 transition-transform duration-300 ease-out">
                <div class="flex items-center justify-between text-white text-[11px]">
                    <span class="flex items-center">
                        <svg class="w-3 h-3 mr-1 text-amber-400" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                        {{ $book->rating ?? 0 }}
                    </span>
                    <span>{{ $book->subscriptions_count ?? 0 }} subscribers</span>
                </div>
            </div>
        </div>

        <!-- Content -->
        <div class="p-5">
            <!-- Category -->
            @if($book->bookCategory)
                <p class="text-[10px] font-semibold uppercase tracking-wider text-indigo-500 dark:text-indigo-400 mb-1.5">
                    {{ $book->bookCategory->name }}
                </p>
            @endif

            <!-- Title -->
            <h3 class="text-base font-semibold text-gray-900 dark:text-white mb-1.5 line-clamp-2 leading-snug group-hover:text-gray-600 dark:group-hover:text-gray-300 transition-colors duration-200">
                {{ $book->title }}
            </h3>

            <!-- Author -->
            <p class="text-xs text-gray-400 dark:text-gray-500 mb-4">
                by {{ $book->author_name }}
            </p>

            <!-- Divider -->
            <div class="h-px bg-gray-100 dark:bg-gray-800 mb-4"></div>

            <!-- Format & Price Row -->
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-2">
                    @if($book->has_softcopy)
                        <span class="inline-flex items-center gap-1 text-[11px] text-gray-500 dark:text-gray-400">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                            Digital
                        </span>
                    @endif
                    @if($book->has_hardcopy)
                        <span class="inline-flex items-center gap-1 text-[11px] text-gray-500 dark:text-gray-400">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C20.832 18.477 19.246 18 17.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                            Print
                        </span>
                    @endif
                </div>
                @if($book->annual_subscription_fee && $book->annual_subscription_fee > 0)
                    <span class="text-sm font-semibold text-gray-900 dark:text-white">GHS {{ number_format($book->annual_subscription_fee) }}<span class="text-[10px] font-normal text-gray-400">/yr</span></span>
                @else
                    <span class="text-sm font-semibold text-emerald-600 dark:text-emerald-400">Free</span>
                @endif
            </div>

            <!-- Actions -->
            @php $hasAccess = $this->hasBookAccess($book->id); @endphp
            <div class="flex items-center gap-2">
                @if($hasAccess && $book->has_softcopy)
                    <a href="{{ route('books.read', $book) }}"
                       class="flex-1 inline-flex justify-center items-center px-4 py-2 text-xs font-medium rounded-xl text-white bg-gray-900 dark:bg-white dark:text-gray-900 hover:bg-gray-800 dark:hover:bg-gray-100 transition-all duration-200 shadow-sm">
                        Read Now
                    </a>
                @endif
                <a href="{{ route('books.show', $book) }}"
                   class="flex-1 inline-flex justify-center items-center px-4 py-2 text-xs font-medium rounded-xl text-gray-600 dark:text-gray-300 border border-gray-200 dark:border-gray-700 hover:bg-gray-50 dark:hover:bg-gray-800 transition-all duration-200">
                    Details
                </a>
                <button wire:click="showBookPreview({{ $book->id }})"
                        class="inline-flex justify-center items-center px-3 py-2 text-xs font-medium rounded-xl text-gray-400 dark:text-gray-500 border border-gray-200 dark:border-gray-700 hover:text-gray-600 dark:hover:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 transition-all duration-200">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                </button>
            </div>
        </div>
    </div>
@endif


@php use App\Models\BookSubscription; @endphp
@if($book)
    @php
        $bookStatus = $this->getBookStatus($book->id);
        $hasAccess = $this->hasBookAccess($book->id);
    @endphp

    <div class="group relative bg-white dark:bg-gray-800 rounded-2xl shadow-md border border-gray-200/60 dark:border-gray-700/60 overflow-hidden hover:shadow-2xl hover:shadow-indigo-500/10 dark:hover:shadow-indigo-500/5 transition-all duration-500 transform hover:-translate-y-1.5">

        <!-- Full Cover Background -->
        <div class="relative aspect-[3/4] w-full overflow-hidden">
            <img src="{{ $book->cover_image }}"
                 alt="{{ $book->title }}"
                 class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700 ease-out"
                 loading="lazy">

            <!-- Heavy gradient overlay -->
            <div class="absolute inset-0 bg-gradient-to-t from-gray-900 via-gray-900/40 to-transparent"></div>
            <div class="absolute inset-0 bg-gradient-to-r from-gray-900/30 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>

            <!-- Status Badge (top-left) -->
            @if($bookStatus)
                <div class="absolute top-3 left-3 z-10">
                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wide shadow-lg backdrop-blur-md {{ $bookStatus['class'] }}">
                        @if($bookStatus['type'] === 'free')
                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                        @elseif($bookStatus['type'] === 'subscribed')
                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                        @elseif($bookStatus['type'] === 'group_subscribed')
                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20"><path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3z"></path></svg>
                        @elseif($bookStatus['type'] === 'pending')
                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path></svg>
                        @endif
                        {{ $bookStatus['label'] }}
                    </span>
                </div>
            @endif

            <!-- Wishlist (top-right) -->
            <button wire:click="addToWishlist({{ $book->id }})"
                    class="absolute top-3 right-3 z-10 p-2 rounded-full bg-white/10 backdrop-blur-md text-white/70 hover:text-red-400 hover:bg-white/20 transition-all duration-200 opacity-0 group-hover:opacity-100 scale-75 group-hover:scale-100">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                </svg>
            </button>

            <!-- Overlaid Content Panel -->
            <div class="absolute inset-x-0 bottom-0 p-5 z-10">
                <!-- Category pill -->
                @if($book->bookCategory)
                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-semibold uppercase tracking-wider bg-white/15 backdrop-blur-sm text-white mb-2">
                        {{ $book->bookCategory->name }}
                    </span>
                @endif

                <!-- Title -->
                <h3 class="text-lg font-bold text-white mb-1 line-clamp-2 leading-tight drop-shadow-lg">
                    {{ $book->title }}
                </h3>

                <!-- Author -->
                <p class="text-xs text-white/60 mb-3 flex items-center">
                    <svg class="w-3 h-3 mr-1.5 text-white/40" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path>
                    </svg>
                    {{ $book->author_name }}
                </p>

                <!-- Format badges & Price -->
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-1.5">
                        @if($book->has_softcopy)
                            <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[9px] font-medium bg-emerald-500/20 text-emerald-300 backdrop-blur-sm">
                                <svg class="w-2.5 h-2.5 mr-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                                Digital
                            </span>
                        @endif
                        @if($book->has_hardcopy)
                            <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[9px] font-medium bg-sky-500/20 text-sky-300 backdrop-blur-sm">
                                <svg class="w-2.5 h-2.5 mr-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C20.832 18.477 19.246 18 17.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                                Print
                            </span>
                        @endif
                    </div>
                    @if($book->annual_subscription_fee && $book->annual_subscription_fee > 0)
                        <span class="text-sm font-bold text-white drop-shadow">GHS {{ number_format($book->annual_subscription_fee) }}</span>
                    @else
                        <span class="text-sm font-bold text-emerald-400 drop-shadow">Free</span>
                    @endif
                </div>

                <!-- Action Buttons -->
                <div class="flex items-center gap-2">
                    @if($hasAccess && $book->has_softcopy)
                        <a href="{{ route('books.read', $book) }}"
                           class="flex-1 inline-flex justify-center items-center px-4 py-2 text-xs font-semibold rounded-xl text-gray-900 bg-white hover:bg-gray-100 transition-all duration-200 shadow-lg hover:shadow-xl transform hover:scale-[1.02] active:scale-[0.98]">
                            <svg class="w-3.5 h-3.5 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C20.832 18.477 19.246 18 17.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                            Read Now
                        </a>
                    @endif
                    <a href="{{ route('books.show', $book) }}"
                       class="flex-1 inline-flex justify-center items-center px-4 py-2 text-xs font-semibold rounded-xl text-white bg-white/15 backdrop-blur-sm border border-white/20 hover:bg-white/25 transition-all duration-200">
                        Details
                    </a>
                    <button wire:click="showBookPreview({{ $book->id }})"
                            class="inline-flex justify-center items-center p-2 rounded-xl text-white/60 bg-white/10 backdrop-blur-sm border border-white/10 hover:text-white hover:bg-white/20 transition-all duration-200">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                    </button>
                </div>

                <!-- Stats bar -->
                <div class="flex items-center justify-between mt-3 pt-3 border-t border-white/10 text-[10px] text-white/40">
                    <span class="flex items-center">
                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        {{ $book->subscriptions_count ?? 0 }} subscribers
                    </span>
                    <span class="flex items-center">
                        <svg class="w-3 h-3 mr-1 text-amber-400/70" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                        {{ $book->rating ?? 0 }}/5
                    </span>
                </div>
            </div>
        </div>
    </div>
@endif

@php use App\Models\BookSubscription; @endphp
@if($book)
    <div class="group relative flex bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden hover:shadow-lg hover:border-indigo-200 dark:hover:border-indigo-800 transition-all duration-300">

        {{-- Cover Image (Left) --}}
        <div class="relative w-36 sm:w-44 flex-shrink-0 bg-gradient-to-br from-gray-100 to-gray-200 dark:from-gray-700 dark:to-gray-800 overflow-hidden">
            <img src="{{ $book->cover_image }}"
                 alt="{{ $book->title }}"
                 class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500 ease-out"
                 loading="lazy">

            {{-- Status Badge --}}
            @php $bookStatus = $this->getBookStatus($book->id); @endphp
            @if($bookStatus)
                <div class="absolute top-2 left-2 z-10">
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-semibold shadow-sm {{ $bookStatus['class'] }}">
                        {{ $bookStatus['label'] }}
                    </span>
                </div>
            @endif

            {{-- Price Ribbon --}}
            <div class="absolute bottom-0 inset-x-0 bg-gradient-to-t from-black/70 to-transparent p-2 pt-6">
                @if($book->annual_subscription_fee && $book->annual_subscription_fee > 0)
                    <span class="text-white text-xs font-bold">GHS {{ number_format($book->annual_subscription_fee) }}/yr</span>
                @else
                    <span class="text-emerald-400 text-xs font-bold">Free Access</span>
                @endif
            </div>
        </div>

        {{-- Content (Right) --}}
        <div class="flex-1 flex flex-col justify-between p-4 sm:p-5 min-w-0">
            <div>
                {{-- Header Row --}}
                <div class="flex items-start justify-between gap-2 mb-1">
                    <h3 class="text-sm sm:text-base font-bold text-gray-900 dark:text-white line-clamp-2 group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors">
                        {{ $book->title }}
                    </h3>
                    <button wire:click="addToWishlist({{ $book->id }})"
                            class="flex-shrink-0 p-1.5 rounded-full text-gray-400 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-900/30 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                        </svg>
                    </button>
                </div>

                {{-- Author --}}
                <p class="text-xs text-gray-500 dark:text-gray-400 mb-2">by {{ $book->author_name }}</p>

                {{-- Tags --}}
                <div class="flex flex-wrap gap-1.5 mb-3">
                    @if($book->bookCategory)
                        <span class="px-2 py-0.5 rounded-md text-[10px] font-medium bg-indigo-50 text-indigo-700 dark:bg-indigo-900/40 dark:text-indigo-300">
                            {{ $book->bookCategory->name }}
                        </span>
                    @endif
                    @if($book->has_hardcopy)
                        <span class="px-2 py-0.5 rounded-md text-[10px] font-medium bg-amber-50 text-amber-700 dark:bg-amber-900/40 dark:text-amber-300">Hardcopy</span>
                    @endif
                    @if($book->has_softcopy)
                        <span class="px-2 py-0.5 rounded-md text-[10px] font-medium bg-emerald-50 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300">Digital</span>
                    @endif
                </div>

                {{-- Stats --}}
                <div class="flex items-center gap-4 text-[11px] text-gray-400 dark:text-gray-500">
                    <span class="flex items-center gap-1">
                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        {{ $book->subscriptions_count ?? 0 }} subscribers
                    </span>
                    <span class="flex items-center gap-1">
                        <svg class="w-3 h-3 text-yellow-400" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                        {{ $book->rating ?? 0 }}/5
                    </span>
                </div>
            </div>

            {{-- Action Buttons --}}
            <div class="flex items-center gap-2 mt-3 pt-3 border-t border-gray-100 dark:border-gray-700">
                @php $hasAccess = $this->hasBookAccess($book->id); @endphp

                @if($hasAccess && $book->has_softcopy)
                    <a href="{{ route('books.read', $book) }}"
                       class="inline-flex items-center px-3 py-1.5 text-xs font-semibold rounded-lg text-white bg-indigo-600 hover:bg-indigo-700 transition-colors shadow-sm">
                        <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C20.832 18.477 19.246 18 17.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                        </svg>
                        Read Now
                    </a>
                @endif

                <a href="{{ route('books.show', $book) }}"
                   class="inline-flex items-center px-3 py-1.5 text-xs font-medium rounded-lg text-gray-600 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors">
                    Details
                </a>

                <button wire:click="showBookPreview({{ $book->id }})"
                        class="inline-flex items-center px-3 py-1.5 text-xs font-medium rounded-lg text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                    </svg>
                </button>
            </div>
        </div>
    </div>
@endif

@php use App\Models\BookSubscription; @endphp
@if($book)
    @php
        $bookStatus = $this->getBookStatus($book->id);
        $hasAccess  = $this->hasBookAccess($book->id);
    @endphp

    <div class="group relative bg-white dark:bg-gray-900 rounded-2xl border border-gray-100 dark:border-gray-800 overflow-hidden hover:border-gray-300 dark:hover:border-gray-600 transition-all duration-500">

        {{-- Cover with tall aspect ratio --}}
        <div class="relative aspect-[3/4] w-full overflow-hidden bg-gray-50 dark:bg-gray-800">
            <img src="{{ $book->cover_image }}"
                 alt="{{ $book->title }}"
                 class="w-full h-full object-cover group-hover:scale-[1.03] transition-transform duration-700 ease-out"
                 loading="lazy">

            {{-- Subtle gradient overlay --}}
            <div class="absolute inset-0 bg-gradient-to-t from-black/50 via-black/5 to-transparent"></div>

            {{-- Status Pill (top-left) --}}
            @if($bookStatus)
                <div class="absolute top-3 left-3 z-10">
                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-semibold tracking-wide uppercase backdrop-blur-md bg-white/20 text-white border border-white/20 {{ $bookStatus['class'] }}">
                        {{ $bookStatus['label'] }}
                    </span>
                </div>
            @endif

            {{-- Wishlist (top-right) --}}
            <button wire:click="addToWishlist({{ $book->id }})"
                    class="absolute top-3 right-3 z-10 p-2 rounded-full backdrop-blur-md bg-white/20 text-white hover:bg-white/40 border border-white/20 transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                </svg>
            </button>

            {{-- Bottom overlay info --}}
            <div class="absolute bottom-0 inset-x-0 p-4">
                <p class="text-white/70 text-[10px] uppercase tracking-widest font-medium mb-1">{{ $book->author_name }}</p>
                <h3 class="text-white text-base font-bold leading-snug line-clamp-2">{{ $book->title }}</h3>
            </div>
        </div>

        {{-- Content Area --}}
        <div class="p-4">
            {{-- Meta Row --}}
            <div class="flex items-center justify-between mb-3">
                <div class="flex items-center gap-2">
                    @if($book->bookCategory)
                        <span class="text-[10px] font-semibold uppercase tracking-wider text-gray-400 dark:text-gray-500">
                            {{ $book->bookCategory->name }}
                        </span>
                    @endif
                    <span class="w-1 h-1 rounded-full bg-gray-300 dark:bg-gray-600"></span>
                    <span class="text-[10px] text-gray-400 dark:text-gray-500">
                        {{ $book->has_softcopy ? 'Digital' : '' }}{{ $book->has_softcopy && $book->has_hardcopy ? ' & ' : '' }}{{ $book->has_hardcopy ? 'Print' : '' }}
                    </span>
                </div>

                {{-- Price --}}
                @if($book->annual_subscription_fee && $book->annual_subscription_fee > 0)
                    <span class="text-xs font-bold text-gray-900 dark:text-white">GHS {{ number_format($book->annual_subscription_fee) }}</span>
                @else
                    <span class="text-xs font-bold text-emerald-600 dark:text-emerald-400">Free</span>
                @endif
            </div>

            {{-- Stats Divider --}}
            <div class="flex items-center gap-3 text-[11px] text-gray-400 dark:text-gray-500 mb-4 pb-3 border-b border-gray-100 dark:border-gray-800">
                <span>{{ $book->subscriptions_count ?? 0 }} readers</span>
                <span class="flex items-center gap-0.5">
                    <svg class="w-3 h-3 text-amber-400" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                    {{ $book->rating ?? 0 }}
                </span>
            </div>

            {{-- Actions --}}
            <div class="grid grid-cols-2 gap-2">
                @if($hasAccess && $book->has_softcopy)
                    <a href="{{ route('books.read', $book) }}"
                       class="col-span-2 inline-flex justify-center items-center px-4 py-2.5 text-xs font-semibold rounded-xl text-white bg-gray-900 dark:bg-white dark:text-gray-900 hover:bg-gray-800 dark:hover:bg-gray-100 transition-colors">
                        Read Now
                    </a>
                @endif

                <a href="{{ route('books.show', $book) }}"
                   class="inline-flex justify-center items-center px-3 py-2 text-xs font-medium rounded-xl border border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
                    Details
                </a>

                <button wire:click="showBookPreview({{ $book->id }})"
                        class="inline-flex justify-center items-center px-3 py-2 text-xs font-medium rounded-xl border border-gray-200 dark:border-gray-700 text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors">
                    Preview
                </button>
            </div>
        </div>
    </div>
@endif


@php use App\Models\BookSubscription; @endphp
@if($book)
    @php
        $bookStatus = $this->getBookStatus($book->id);
        $hasAccess  = $this->hasBookAccess($book->id);
    @endphp

    <div class="group relative rounded-2xl overflow-hidden bg-gradient-to-br from-indigo-50 via-white to-purple-50 dark:from-gray-800 dark:via-gray-900 dark:to-gray-800 border border-white/60 dark:border-gray-700/50 shadow-md hover:shadow-xl transition-all duration-500 hover:-translate-y-1">

        {{-- Decorative blurred background blob --}}
        <div class="absolute -top-10 -right-10 w-40 h-40 bg-indigo-200/30 dark:bg-indigo-800/20 rounded-full blur-3xl group-hover:scale-125 transition-transform duration-700"></div>
        <div class="absolute -bottom-8 -left-8 w-32 h-32 bg-purple-200/30 dark:bg-purple-800/20 rounded-full blur-3xl"></div>

        {{-- Cover Section --}}
        <div class="relative px-5 pt-5">
            <div class="relative mx-auto w-28 sm:w-32 aspect-[2/3] rounded-xl overflow-hidden shadow-lg group-hover:shadow-2xl transition-shadow duration-500 ring-1 ring-black/5">
                <img src="{{ $book->cover_image }}"
                     alt="{{ $book->title }}"
                     class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700 ease-out"
                     loading="lazy">

                {{-- Status Badge --}}
                @if($bookStatus)
                    <div class="absolute -top-1 -right-1 z-10">
                        <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[9px] font-bold shadow-md {{ $bookStatus['class'] }}">
                            {{ $bookStatus['label'] }}
                        </span>
                    </div>
                @endif
            </div>
        </div>

        {{-- Content --}}
        <div class="relative p-5 text-center">
            {{-- Title --}}
            <h3 class="text-sm font-bold text-gray-900 dark:text-white mb-1 line-clamp-2 group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors">
                {{ $book->title }}
            </h3>

            {{-- Author --}}
            <p class="text-xs text-gray-500 dark:text-gray-400 mb-2">{{ $book->author_name }}</p>

            {{-- Tags --}}
            <div class="flex justify-center flex-wrap gap-1.5 mb-3">
                @if($book->bookCategory)
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-medium bg-white/80 dark:bg-gray-700/80 text-gray-600 dark:text-gray-300 backdrop-blur-sm border border-gray-200/50 dark:border-gray-600/50">
                        {{ $book->bookCategory->name }}
                    </span>
                @endif
                @if($book->has_softcopy)
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-medium bg-emerald-50/80 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-300 backdrop-blur-sm">Digital</span>
                @endif
                @if($book->has_hardcopy)
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-medium bg-blue-50/80 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300 backdrop-blur-sm">Print</span>
                @endif
            </div>

            {{-- Price + Stats --}}
            <div class="flex items-center justify-center gap-3 mb-4 text-[11px] text-gray-400 dark:text-gray-500">
                @if($book->annual_subscription_fee && $book->annual_subscription_fee > 0)
                    <span class="font-bold text-gray-800 dark:text-gray-200 text-sm">GHS {{ number_format($book->annual_subscription_fee) }}</span>
                @else
                    <span class="font-bold text-emerald-600 dark:text-emerald-400 text-sm">Free</span>
                @endif
                <span class="w-px h-3 bg-gray-300 dark:bg-gray-600"></span>
                <span class="flex items-center gap-0.5">
                    <svg class="w-3 h-3 text-amber-400" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                    {{ $book->rating ?? 0 }}
                </span>
                <span class="w-px h-3 bg-gray-300 dark:bg-gray-600"></span>
                <span>{{ $book->subscriptions_count ?? 0 }} readers</span>
            </div>

            {{-- Actions --}}
            <div class="space-y-2">
                @if($hasAccess && $book->has_softcopy)
                    <a href="{{ route('books.read', $book) }}"
                       class="w-full inline-flex justify-center items-center px-4 py-2.5 text-xs font-bold rounded-xl text-white bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 shadow-md hover:shadow-lg transition-all duration-300 active:scale-[0.97]">
                        <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C20.832 18.477 19.246 18 17.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                        </svg>
                        Start Reading
                    </a>
                @endif

                <div class="flex gap-2">
                    <a href="{{ route('books.show', $book) }}"
                       class="flex-1 inline-flex justify-center items-center px-3 py-2 text-xs font-medium rounded-xl text-gray-600 dark:text-gray-300 bg-white/70 dark:bg-gray-700/70 backdrop-blur-sm border border-gray-200/60 dark:border-gray-600/60 hover:bg-white dark:hover:bg-gray-700 transition-colors">
                        Details
                    </a>
                    <button wire:click="showBookPreview({{ $book->id }})"
                            class="flex-1 inline-flex justify-center items-center px-3 py-2 text-xs font-medium rounded-xl text-gray-600 dark:text-gray-300 bg-white/70 dark:bg-gray-700/70 backdrop-blur-sm border border-gray-200/60 dark:border-gray-600/60 hover:bg-white dark:hover:bg-gray-700 transition-colors">
                        Preview
                    </button>
                    <button wire:click="addToWishlist({{ $book->id }})"
                            class="inline-flex justify-center items-center px-2.5 py-2 rounded-xl text-gray-400 hover:text-red-500 bg-white/70 dark:bg-gray-700/70 backdrop-blur-sm border border-gray-200/60 dark:border-gray-600/60 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </div>
@endif
