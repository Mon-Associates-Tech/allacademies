@php use App\Models\BookSubscription; @endphp
@if($book)
    <div class="group relative bg-white dark:bg-gray-800 rounded-xl shadow-sm border border-gray-200 dark:border-gray-700 overflow-hidden hover:shadow-lg hover:border-gray-300 dark:hover:border-gray-600 transition-all duration-300 transform hover:-translate-y-1">
        <!-- Enhanced Book Status Badge -->
        @php
            $bookStatus = $this->getBookStatus($book->id);
        @endphp

        @if($bookStatus)
            <div class="absolute top-3 right-3 z-10">
                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium shadow-sm backdrop-blur-sm {{ $bookStatus['class'] }}">
                            @if($bookStatus['type'] === 'free')
                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                </svg>
                            @elseif($bookStatus['type'] === 'subscribed')
                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                </svg>
                            @elseif($bookStatus['type'] === 'group_subscribed')
                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3z"></path>
                                </svg>
                            @elseif($bookStatus['type'] === 'pending')
                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                                </svg>
                            @endif
                            {{ $bookStatus['label'] }}
                        </span>
            </div>
        @endif

        <!-- Enhanced Book Cover with overlay effect -->
        <div class="relative aspect-[4/3] bg-gray-100 dark:bg-gray-700">
            <img src="{{ $book->cover_image }}"
                 alt="{{ $book->title }}"
                 class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                 loading="lazy">
            <div class="absolute bottom-0 left-0 inset- mx-2 mb-1 bg-gradient-to-t from-black/20 to-transparent opacity-  group-hover:opacity-100 transition-opacity duration-300">
                @php
                    $subscription = BookSubscription::where('student_id', auth()->user()->student->id ?? null)
                        ->where('book_id', $book->id)
                        ->where('status', 'pending_payment')
                        ->first();
                @endphp

                @if($subscription)
                    <!-- Enhanced pending payment button -->
                @else
                    <button wire:click="subscribeToBook({{ $book->id }})"
                            class="w-ful inline-flex justify-center items-center px-4 py-2.5 border border-transparent text-sm  rounded-lg text-white bg-gradient-to-r from-indigo-600 to-indigo-700 hover:from-indigo-700 hover:to-indigo-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-200 shadow-sm hover:shadow-md transform hover:scale-[1.02] active:scale-[0.98]">
                        <span class="truncate">GHS {{ number_format($book->annual_subscription_fee ?? 50.00, 2) }}</span>
                    </button>
                @endif
            </div>
        </div>

        <!-- Enhanced Book Content -->
        <div class="p-5">
            <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-2 line-clamp-2 group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors duration-200">
                {{ $book->title }}
            </h3>
            <p class="text-xs text-gray-600 dark:text-gray-400 mb-3">
                by {{ $book->author->user->name ?? 'Unknown Author' }}
            </p>

            <!-- Enhanced Category and Format badges -->
            <div class="flex flex-wrap gap-2 mb-4">
                @if($book->bookCategory)
                    <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-200 hover:bg-gray-200 dark:hover:bg-gray-600 transition-colors duration-200">
                                {{ $book->bookCategory->name }}
                            </span>
                @endif

                @if($book->has_hardcopy)
                    <span class="inline-flex hidden items-center px-2.5 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200 hover:bg-blue-200 dark:hover:bg-blue-800 transition-colors duration-200">
                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                Hardcopy
                            </span>
                @endif

                @if($book->has_softcopy)
                    <span class="inline-flex hidden items-center px-2.5 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200 hover:bg-green-200 dark:hover:bg-green-800 transition-colors duration-200">
                                <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C20.832 18.477 19.246 18 17.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                </svg>
                                Softcopy
                            </span>
                @endif
            </div>

            <!-- Enhanced Action Buttons -->
            <div class="space-y-3">
                @if($book->has_softcopy)
                    @php
                        $hasAccess = $this->hasBookAccess($book->id);
                    @endphp

                    @if($hasAccess)
                        <!-- Enhanced Read button -->
                        <button wire:click="openPdfReader({{ $book->id }})"
                                class="w-full inline-flex justify-center items-center px-4 py-2.5 border border-transparent text-sm font-medium rounded-lg text-white bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-all duration-200 shadow-sm hover:shadow-md transform hover:scale-[1.02] active:scale-[0.98]">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C20.832 18.477 19.246 18 17.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                            </svg>
                            Read Now
                        </button>
                    @else
                        @php
                            $subscription = BookSubscription::where('student_id', auth()->user()->student->id ?? null)
                                ->where('book_id', $book->id)
                                ->where('status', 'pending_payment')
                                ->first();
                        @endphp

                        @if($subscription)
                            <!-- Enhanced pending payment button -->
                            <button wire:click="showSubscriptionDetails({{ $subscription->id }})"
                                    class="w-full inline-flex justify-center items-center px-4 py-2.5 border border-transparent text-sm font-medium rounded-lg text-white bg-gradient-to-r from-orange-600 to-orange-700 hover:from-orange-700 hover:to-orange-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-orange-500 transition-all duration-200 shadow-sm hover:shadow-md transform hover:scale-[1.02] active:scale-[0.98]">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                                </svg>
                                Complete Payment
                            </button>
                        @else
                            <!-- Enhanced subscribe buttons -->
                            @if($this->isBookFree($book->id))
                                <button wire:click="subscribeToBook({{ $book->id }})"
                                        class="w-full inline-flex justify-center items-center px-4 py-2.5 border border-transparent text-sm font-medium rounded-lg text-white bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 transition-all duration-200 shadow-sm hover:shadow-md transform hover:scale-[1.02] active:scale-[0.98]">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                    </svg>
                                    Add to Library - Free
                                </button>
                            @else
                                <button wire:click="subscribeToBook({{ $book->id }})"
                                        class="w-full inline-flex justify-center items-center px-4 py-2.5 border border-transparent text-sm font-medium rounded-lg text-white bg-gradient-to-r from-indigo-600 to-indigo-700 hover:from-indigo-700 hover:to-indigo-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-200 shadow-sm hover:shadow-md transform hover:scale-[1.02] active:scale-[0.98]">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                    <span class="truncate">Subscribe - GHS {{ number_format($book->annual_subscription_fee ?? 50.00, 2) }}/year</span>
                                </button>
                            @endif
                        @endif
                    @endif
                @endif

                <!-- Enhanced Hardcopy borrowing section -->
                @if($book->has_hardcopy)
                    @if($this->isBookBorrowed($book->id))
                        <button wire:click="returnBook({{ $book->id }})"
                                class="w-full inline-flex justify-center items-center px-4 py-2.5 border border-transparent text-sm font-medium rounded-lg text-white bg-gradient-to-r from-red-600 to-red-700 hover:from-red-700 hover:to-red-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-all duration-200 shadow-sm hover:shadow-md transform hover:scale-[1.02] active:scale-[0.98]">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"></path>
                            </svg>
                            Return Book
                        </button>
                    @else
                        <button wire:click="borrowBook({{ $book->id }})"
                                class="w-full inline-flex justify-center items-center px-4 py-2.5 border border-gray-300 dark:border-gray-600 text-sm font-medium rounded-lg text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 hover:bg-gray-50 dark:hover:bg-gray-600 hover:border-gray-400 dark:hover:border-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all duration-200 shadow-sm hover:shadow-md transform hover:scale-[1.02] active:scale-[0.98]">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2v0a2 2 0 01-2-2v-1"></path>
                            </svg>
                            Borrow Book
                        </button>
                    @endif
                @endif
            </div>
        </div>
    </div>

@else
    <div class="">
        <div class="flex flex-col items-center justify-center">
            <div class="w-full max-w-sm">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md p-6 text-center">
                    <svg class="w-12 h-12 mx-auto mb-4 text-gray-400 dark:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 0v4m0-4h4m-4 0H8m6 10H6a2 2 0 01-2-2V6a2 2 0 012-2h12a2 2 0 012 2v10a2 2 0 01-2 2z"></path>
                    </svg>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white">No Book Found</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mt-2">Please check back later or contact support.</p>
                </div>
            </div>
        </div>
    </div>
@endif

