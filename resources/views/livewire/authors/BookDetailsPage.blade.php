<div x-data="{
    showPdfReader: false,
    currentPage: 1,
    isLoading: false,
    showImageModal: false,
    imageModalSrc: '',
    showSubscriptionModal: false,
    subscriptionData: null
}" class="min-h-screen rounded-lg bg-gradient-to-br from-gray-50 via-blue-50 to-indigo-50 dark:from-gray-900 dark:via-gray-800 dark:to-gray-900">

    <!-- Listen for subscription modal events -->
    <div x-on:show-subscription-modal.window="showSubscriptionModal = true; subscriptionData = $event.detail[0]"></div>

    <!-- Animated Background Elements -->
    <div class="fixed inset-0 overflow-hidden pointer-events-none">
        <div class="absolute -top-40 -right-40 w-80 h-80 bg-gradient-to-br from-blue-400/20 to-purple-400/20 rounded-full blur-3xl animate-pulse"></div>
        <div class="absolute -bottom-40 -left-40 w-80 h-80 bg-gradient-to-br from-indigo-400/20 to-pink-400/20 rounded-full blur-3xl animate-pulse delay-1000"></div>
    </div>

    <!-- Header Section -->
    <div class="relative bg-white/80 dark:bg-gray-800/80 backdrop-blur-xl shadow-sm rounded-lg border-b border-gray-200/50 dark:border-gray-700/50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <button onclick="history.back()"
                            class="group inline-flex items-center px-4 py-2 text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white bg-white/60 dark:bg-gray-700/60 rounded-xl backdrop-blur-sm transition-all duration-200 hover:shadow-lg">
                        <svg class="w-5 h-5 mr-2 transform group-hover:-translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                        </svg>
                        Back to Books
                    </button>
                    <div class="h-8 w-px bg-gradient-to-b from-gray-300 to-transparent dark:from-gray-600"></div>
                    <div>
                        <h1 class="text-3xl font-bold bg-gradient-to-r from-gray-900 to-gray-700 dark:from-white dark:to-gray-300 bg-clip-text text-transparent">
                            Book Details
                        </h1>
                        <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Comprehensive overview and access</p>
                    </div>
                </div>

                @auth
                    @if(auth()->user()->hasRole('author'))
                        <div class="flex items-center space-x-3">
                            <a href="{{ route('author.books.edit', $book) }}"
                               class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white rounded-xl font-medium shadow-lg hover:shadow-xl transform hover:scale-105 transition-all duration-200">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                                Edit Book
                            </a>
                            <div class="relative z-50" x-data="{ open: false }">
                                <button @click="open = !open"
                                        class="inline-flex items-center px-4 py-3 bg-white/60 dark:bg-gray-700/60 backdrop-blur-sm text-gray-700 dark:text-gray-300 hover:bg-white/80 dark:hover:bg-gray-700/80 rounded-xl shadow-lg transition-all duration-200">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"></path>
                                    </svg>
                                    Actions
                                </button>
                                <div x-show="open" @click.away="open = false"
                                     x-transition:enter="transition ease-out duration-200"
                                     x-transition:enter-start="opacity-0 transform scale-95"
                                     x-transition:enter-end="opacity-100 transform scale-100"
                                     style="z-index:100000!important"
                                     class="absolute right-0 mt-2 w-56 bg-white/90 dark:bg-gray-800/90 backdrop-blur-xl rounded-xl shadow-2xl z-50 border border-gray-200/50 dark:border-gray-700/50">
                                    <div class="p-2 z-10">
                                        <a href="{{ route('author.analytics.book', $book) }}"
                                           class="flex items-center px-4 py-3 text-sm text-gray-700 dark:text-gray-300 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded-lg transition-colors">
                                            <span class="text-lg mr-3">📊</span>
                                            <span>View Analytics</span>
                                        </a>
                                        <a href="{{ route('author.revenue.index', $book) }}"
                                           class="flex items-center px-4 py-3 text-sm text-gray-700 dark:text-gray-300 hover:bg-green-50 dark:hover:bg-green-900/20 rounded-lg transition-colors">
                                            <span class="text-lg mr-3">💰</span>
                                            <span>Revenue Details</span>
                                        </a>
                                        <a href="{{ route('author.reviews.index', $book) }}"
                                           class="flex items-center px-4 py-3 text-sm text-gray-700 dark:text-gray-300 hover:bg-yellow-50 dark:hover:bg-yellow-900/20 rounded-lg transition-colors">
                                            <span class="text-lg mr-3">⭐</span>
                                            <span>Reviews</span>
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endif
                @endauth
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Book Cover & Basic Info -->
            <div class="lg:col-span-2">
                <div class="bg-white/80 dark:bg-gray-800/80 backdrop-blur-xl rounded-2xl shadow-2xl p-8 border border-gray-200/50 dark:border-gray-700/50">
                    <!-- Book Cover -->
                    <div class="relative group mb-8">
                        <div class="aspect-[3/2] max-h-96 rounded-xl overflow-hidden bg-gradient-to-br from-gray-100 to-gray-200 dark:from-gray-700 dark:to-gray-800 shadow-2xl">
                            <img src="{{ $book->cover_image }}"
                                 alt="{{ $book->title }}"
                                 class="w-full h-full object-cover transform group-hover:scale-105 transition-transform duration-300 cursor-pointer"
                                 @click="imageModalSrc = '{{ $book->cover_image }}'; showImageModal = true">
                        </div>
                        <div class="absolute inset-0 bg-gradient-to-t from-black/20 to-transparent rounded-xl pointer-events-none"></div>

                        <!-- PDF Reader Button -->
                        @if($book->content_url)
                            <button wire:click="openPdfReader"
                                    class="absolute bottom-4 right-4 {{ $canRead ? 'bg-green-600 hover:bg-green-700' : 'bg-gray-500 cursor-not-allowed' }} text-white p-3 rounded-full shadow-lg {{ $canRead ? 'transform hover:scale-110 transition-all duration-200' : '' }}"
                                {{ $canRead ? '' : 'disabled' }}>
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                </svg>
                            </button>
                        @endif
                    </div>

                    <!-- Book Title & Author -->
                    <div class="text-center mb-8">
                        <h2 class="text-3xl font-bold bg-gradient-to-r from-gray-900 to-gray-700 dark:from-white dark:to-gray-300 bg-clip-text text-transparent mb-3">
                            {{ $book->title }}
                        </h2>
                        <p class="text-gray-600 dark:text-gray-400 text-lg">by {{ $book->author->user->name }}</p>
                        <div class="flex justify-center mt-4">
                            <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium bg-gradient-to-r from-blue-100 to-blue-200 text-blue-800 dark:from-blue-900 dark:to-blue-800 dark:text-blue-300">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a.997.997 0 01-1.414 0l-7-7A1.997 1.997 0 013 12V7a4 4 0 014-4z"></path>
                                </svg>
                                {{ $book->bookCategory->name }}
                            </span>
                        </div>
                    </div>

                    <!-- Subscription Status & Actions -->
                    @auth
                        @if(auth()->user()->student)
                            <div class="border-t border-gray-200/50 dark:border-gray-700/50 pt-8">
                                @if($userSubscription)
                                    @if($userSubscription->status === 'active')
                                        <div class="bg-gradient-to-r from-green-50 to-emerald-50 dark:from-green-900/20 dark:to-emerald-900/20 rounded-xl p-6 mb-6">
                                            <div class="flex items-center justify-between">
                                                <div class="flex items-center">
                                                    <svg class="w-8 h-8 text-green-600 dark:text-green-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                    </svg>
                                                    <div>
                                                        <p class="text-lg font-semibold text-green-800 dark:text-green-200">Active Subscription</p>
                                                        <p class="text-sm text-green-600 dark:text-green-400">Valid until {{ $userSubscription->end_date->format('M d, Y') }}</p>
                                                    </div>
                                                </div>
                                                <button wire:click="openPdfReader"
                                                        class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded-lg font-medium transition-colors">
                                                    Start Reading
                                                </button>
                                            </div>
                                        </div>
                                    @elseif($userSubscription->status === 'pending_payment')
                                        <div class="bg-gradient-to-r from-yellow-50 to-orange-50 dark:from-yellow-900/20 dark:to-orange-900/20 rounded-xl p-6 mb-6">
                                            <div class="flex items-center justify-between">
                                                <div class="flex items-center">
                                                    <svg class="w-8 h-8 text-yellow-600 dark:text-yellow-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.082 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                                    </svg>
                                                    <div>
                                                        <p class="text-lg font-semibold text-yellow-800 dark:text-yellow-200">Pending Payment</p>
                                                        <p class="text-sm text-yellow-600 dark:text-yellow-400">Complete payment to start reading</p>
                                                    </div>
                                                </div>
                                                <button wire:click="showSubscriptionModal = true; subscriptionData = { book_title: '{{ $book->title }}', amount: {{ $userSubscription->annual_fee }}, reference: '{{ $userSubscription->reference }}', subscription_id: {{ $userSubscription->id }} }"
                                                        class="bg-yellow-600 hover:bg-yellow-700 text-white px-6 py-3 rounded-lg font-medium transition-colors">
                                                    Pay Now
                                                </button>
                                            </div>
                                        </div>
                                    @endif
                                @else
                                    <div class="bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 rounded-xl p-6 mb-6">
                                        <div class="text-center">
                                            <svg class="w-16 h-16 text-blue-600 dark:text-blue-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                            </svg>
                                            <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">Subscribe to Read</h3>
                                            <p class="text-gray-600 dark:text-gray-400 mb-4">Get unlimited access to this book with an annual subscription</p>

                                            <div class="text-center mb-6">
                                                @if($book->is_free)
                                                    <span class="text-3xl font-bold text-green-600 dark:text-green-400">FREE</span>
                                                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">No payment required</p>
                                                @else
                                                    <span class="text-3xl font-bold text-blue-600 dark:text-blue-400">{{ $book->formatted_subscription_fee }}</span>
                                                    <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">Annual subscription</p>
                                                @endif
                                            </div>

                                            <button wire:click="subscribeToBook"
                                                    wire:loading.attr="disabled"
                                                    class="w-full bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white px-8 py-4 rounded-lg font-medium shadow-lg hover:shadow-xl transform hover:scale-105 transition-all duration-200 disabled:opacity-50 disabled:cursor-not-allowed">
                                                <span wire:loading.remove wire:target="subscribeToBook">
                                                    @if($book->is_free)
                                                        Subscribe Free
                                                    @else
                                                        Subscribe & Pay
                                                    @endif
                                                </span>
                                                <span wire:loading wire:target="subscribeToBook" class="flex items-center justify-center">
                                                    <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                                    </svg>
                                                    Processing...
                                                </span>
                                            </button>

                                            <div class="mt-4 text-xs text-gray-500 dark:text-gray-400">
                                                <p>• Access for full year from subscription date</p>
                                                <p>• {{ $book->is_free ? 'Instant access' : 'Access after payment confirmation' }}</p>
                                                <p>• Read online anytime, anywhere</p>
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @endif
                    @else
                        <div class="border-t border-gray-200/50 dark:border-gray-700/50 pt-8">
                            <div class="bg-gradient-to-r from-gray-50 to-gray-100 dark:from-gray-800 dark:to-gray-700 rounded-xl p-6 text-center">
                                <svg class="w-16 h-16 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                </svg>
                                <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">Login Required</h3>
                                <p class="text-gray-600 dark:text-gray-400 mb-4">Please log in to subscribe and read this book</p>
                                <a href="{{ route('sign-in') }}"
                                   class="inline-flex items-center px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition-colors">
                                    Log In
                                </a>
                            </div>
                        </div>
                    @endauth

                    <!-- Pricing Info -->
                    <div class="border-t border-gray-200/50 dark:border-gray-700/50 pt-8">
                        <div class="bg-gradient-to-r from-green-50 to-emerald-50 dark:from-green-900/20 dark:to-emerald-900/20 rounded-xl p-6 mb-6">
                            <div class="flex items-center justify-between">
                                <span class="text-sm font-medium text-gray-600 dark:text-gray-400">Annual Subscription</span>
                                <span class="text-2xl font-bold">
                                    @if($book->is_free)
                                        <span class="text-green-600 dark:text-green-400">FREE</span>
                                    @else
                                        <span class="text-green-600 dark:text-green-400">{{ $book->formatted_subscription_fee }}</span>
                                    @endif
                                </span>
                            </div>
                        </div>

                        <!-- Rest of the existing content... -->
                        <!-- Availability -->
                        <div class="space-y-4">
                            <div class="flex items-center justify-between p-4 bg-gray-50/50 dark:bg-gray-700/50 rounded-xl">
                                <div class="flex items-center">
                                    <svg class="w-5 h-5 mr-3 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.746 0 3.332.477 4.5 1.253v13C19.832 18.477 18.246 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                                    </svg>
                                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Hard Copy</span>
                                </div>
                                <span class="text-sm">
                                    @if($book->has_hardcopy)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300">
                                            Available
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300">
                                            Not Available
                                        </span>
                                    @endif
                                </span>
                            </div>

                            <div class="flex items-center justify-between p-4 bg-gray-50/50 dark:bg-gray-700/50 rounded-xl">
                                <div class="flex items-center">
                                    <svg class="w-5 h-5 mr-3 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                    </svg>
                                    <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Digital Copy</span>
                                </div>
                                <span class="text-sm">
                                    @if($book->has_softcopy)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300">
                                            Available
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300">
                                            Not Available
                                        </span>
                                    @endif
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>


            <div class="d grid-codls-1 gap-6 space-y-6 mb-8" >
                @if(in_array(auth()->user()->role, ['owner', 'author'] ))
                <div class="bg-gradient-to-br max-h-48 from-blue-500 to-blue-600 rounded-2xl p-6 text-white shadow-2xl transform hover:scale-105 transition-all duration-300">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-blue-100 text-sm font-medium">Active Subscriptions</p>
                            <p class="text-3xl font-bold">{{ $book->subscriptions_count ?? 0 }}</p>
                            <p class="text-blue-200 text-xs mt-1">+15% this month</p>
                        </div>
                        <div class="p-4 bg-white/20 rounded-full">
                            <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M16 7c0-2.21-1.79-4-4-4s-4 1.79-4 4 1.79 4 4 4 4-1.79 4-4zm4 7v4h-2v-4c0-1.1-.9-2-2-2H8c-1.1 0-2 .9-2 2v4H4v-4c0-2.21 1.79-4 4-4h8c2.21 0 4 1.79 4 4z"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-gradient-to-br from-green-500 to-green-600 max-h-48 rounded-2xl p-6 text-white shadow-2xl transform hover:scale-105 transition-all duration-300">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-green-100 text-sm font-medium">Total Borrowings</p>
                            <p class="text-3xl font-bold">{{ $book->borrowings_count ?? 0 }}</p>
                            <p class="text-green-200 text-xs mt-1">Popular read</p>
                        </div>
                        <div class="p-4 bg-white/20 rounded-full">
                            <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M17 3H7c-1.1 0-2 .9-2 2v16l7-3 7 3V5c0-1.1-.9-2-2-2z"/>
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-gradient-to-br from-purple-500 to-purple-600 max-h-48 rounded-2xl p-6 text-white shadow-2xl transform hover:scale-105 transition-all duration-300">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-purple-100 text-sm font-medium">Revenue Generated</p>
                            <p class="text-3xl font-bold">
                                GHS {{ number_format(($book->subscriptions_count ?? 0) * $book->annual_subscription_fee, 2) }}
                            </p>
                            <p class="text-purple-200 text-xs mt-1">+8% this month</p>
                        </div>
                        <div class="p-4 bg-white/20 rounded-full">
                            <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8z"/>
                            </svg>
                        </div>
                    </div>
                </div>
                @endif
                <!-- Book Information -->
                <div class="">
                    <div class="flex items-center mb-6">
                        <div class="p-3 bg-gradient-to-r from-blue-500 to-purple-600 rounded-xl mr-4">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-900 dark:text-white">Book Information</h3>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-1 gap-2">
                        <div class="space-y-1">
                            <div class="flex items-center p-4 bg-gray-50/50 dark:bg-gray-700/50 rounded-xl">
                                <div class="p-2 bg-blue-100 dark:bg-blue-900 rounded-lg mr-4">
                                    <svg class="w-5 h-5 text-blue-600 dark:text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                    </svg>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Publisher</dt>
                                    <dd class="text-lg font-semibold text-gray-900 dark:text-white">{{ $book->publisher ?? 'Not specified' }}</dd>
                                </div>
                            </div>

                            <div class="flex items-center p-4 bg-gray-50/50 dark:bg-gray-700/50 rounded-xl">
                                <div class="p-2 bg-green-100 dark:bg-green-900 rounded-lg mr-4">
                                    <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a.997.997 0 01-1.414 0l-7-7A1.997 1.997 0 013 12V7a4 4 0 014-4z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Edition</dt>
                                    <dd class="text-lg font-semibold text-gray-900 dark:text-white">{{ $book->edition ?? 'First Edition' }}</dd>
                                </div>
                            </div>

                            <div class="flex items-center p-4 bg-gray-50/50 dark:bg-gray-700/50 rounded-xl">
                                <div class="p-2 bg-yellow-100 dark:bg-yellow-900 rounded-lg mr-4">
                                    <svg class="w-5 h-5 text-yellow-600 dark:text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Total Pages</dt>
                                    <dd class="text-lg font-semibold text-gray-900 dark:text-white">{{ $book->pages ?? 'Not specified' }}</dd>
                                </div>
                            </div>
                        </div>

                        <div class="space-y-6">
                            <div class="flex items-center p-4 bg-gray-50/50 dark:bg-gray-700/50 rounded-xl">
                                <div class="p-2 bg-purple-100 dark:bg-purple-900 rounded-lg mr-4">
                                    <svg class="w-5 h-5 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Publication Date</dt>
                                    <dd class="text-lg font-semibold text-gray-900 dark:text-white">{{ $book->created_at->format('M d, Y') }}</dd>
                                </div>
                            </div>

                            <div class="flex items-center p-4 bg-gray-50/50 dark:bg-gray-700/50 rounded-xl">
                                <div class="p-2 bg-indigo-100 dark:bg-indigo-900 rounded-lg mr-4">
                                    <svg class="w-5 h-5 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                                    </svg>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Last Updated</dt>
                                    <dd class="text-lg font-semibold text-gray-900 dark:text-white">{{ $book->updated_at->format('M d, Y') }}</dd>
                                </div>
                            </div>

                            <div class="flex items-center p-4 bg-gray-50/50 dark:bg-gray-700/50 rounded-xl">
                                <div class="p-2 bg-green-100 dark:bg-green-900 rounded-lg mr-4">
                                    <svg class="w-5 h-5 text-green-600 dark:text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500 dark:text-gray-400">Status</dt>
                                    <dd>
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300">
                                            Published
                                        </span>
                                    </dd>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Book Details & Statistics -->
            <div class="lg:col-span-2">

                <!-- Subscription Conditions -->
                <div class="bg-white/80 hidden dark:bg-gray-800/80 backdrop-blur-xl rounded-2xl shadow-2xl p-8 border border-gray-200/50 dark:border-gray-700/50">
                    <div class="flex items-center mb-6">
                        <div class="p-3 bg-gradient-to-r from-orange-500 to-red-600 rounded-xl mr-4">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                            </svg>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-900 dark:text-white">Subscription Conditions</h3>
                    </div>
                    <div class="space-y-4">
                        @foreach(explode("\n", $book->subscription_conditions) as $condition)
                            @if(trim($condition))
                                <div class="flex items-start p-4 bg-gradient-to-r from-blue-50 to-indigo-50 dark:from-blue-900/20 dark:to-indigo-900/20 rounded-xl">
                                    <div class="flex-shrink-0 w-6 h-6 bg-blue-500 rounded-full flex items-center justify-center mr-4 mt-0.5">
                                        <svg class="w-3 h-3 text-white" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                        </svg>
                                    </div>
                                    <span class="text-gray-700 dark:text-gray-300 leading-relaxed">{{ trim($condition) }}</span>
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Activity -->
        @if(in_array(auth()->user()->role, ['admin', 'owner']))
        <div class="mt-8 bg-white/80 dark:bg-gray-800/80 backdrop-blur-xl rounded-2xl shadow-2xl p-8 border border-gray-200/50 dark:border-gray-700/50">
            <div class="flex items-center mb-6">
                <div class="p-3 bg-gradient-to-r from-purple-500 to-pink-600 rounded-xl mr-4">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                    </svg>
                </div>
                <h3 class="text-2xl font-bold text-gray-900 dark:text-white">Recent Activity</h3>
            </div>
            <div class="space-y-4">
                @forelse($book->subscriptions()->latest()->take(5)->get() as $subscription)
                    <div class="flex items-center justify-between p-6 bg-gradient-to-r from-gray-50 to-blue-50 dark:from-gray-700/50 dark:to-blue-900/20 rounded-xl border border-gray-200/50 dark:border-gray-700/50">
                        <div class="flex items-center">
                            <div class="w-12 h-12 bg-gradient-to-r from-blue-500 to-purple-600 rounded-full flex items-center justify-center mr-4">
                                <svg class="w-6 h-6 text-white" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                </svg>
                            </div>
                            <div>
                                <p class="text-lg font-semibold text-gray-900 dark:text-white">New subscription</p>
                                <p class="text-sm text-gray-500 dark:text-gray-400">{{ $subscription->created_at->diffForHumans() }}</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="text-xl font-bold text-green-600 dark:text-green-400">
                                +{{ $book->formatted_subscription_fee }}
                            </span>
                            <p class="text-sm text-gray-500 dark:text-gray-400">Revenue</p>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-12">
                        <div class="mx-auto w-24 h-24 bg-gradient-to-r from-gray-100 to-gray-200 dark:from-gray-700 dark:to-gray-800 rounded-full flex items-center justify-center mb-6">
                            <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 dark:text-white mb-2">No recent activity</h3>
                        <p class="text-gray-500 dark:text-gray-400">Activity will appear here as readers interact with your book</p>
                    </div>
                @endforelse
            </div>
        </div>
            @endif
    </div>

    <!-- Image Modal -->
    <div x-show="showImageModal"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click="showImageModal = false"
         class="fixed inset-0 bg-black/90 backdrop-blur-sm z-50 flex items-center justify-center p-4"
         style="display: none;">
        <div class="relative max-w-4xl max-h-full">
            <img :src="imageModalSrc" class="max-w-full max-h-full object-contain rounded-lg shadow-2xl">
            <button @click="showImageModal = false" class="absolute top-4 right-4 text-white hover:text-gray-300 p-2">
                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
    </div>

{{--    <livewire:common.pdf-reader--}}
{{--        :key="$book->id"--}}
{{--    />--}}

    <!-- PDF Reader Modal -->
    @if($showPdfReader)
        <div class="fixed inset-0 bg-black/90 backdrop-blur-sm flex items-center justify-center z-50 p-4"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0">

            <div class="bg-white dark:bg-gray-800 rounded-2xl w-full max-w-6xl h-[90vh] flex flex-col shadow-2xl"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 transform scale-90"
                 x-transition:enter-end="opacity-100 transform scale-100">

                <!-- PDF Reader Header -->
                <div class="flex items-center justify-between p-4 border-b border-gray-200 dark:border-gray-700 bg-gradient-to-r from-red-600 to-red-700 text-white rounded-t-2xl">
                    <h3 class="text-xl font-bold">{{ $book->title }}</h3>
                    <div class="flex items-center space-x-4">
                        <div class="text-sm">
                            <span id="total-pages">--</span> pages
                        </div>
                        <button wire:click="closePdfReader"
                                class="text-white hover:text-gray-200 p-2 rounded-lg hover:bg-white/10 transition-colors">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                </div>

                <!-- PDF Reader Content -->
{{--                <div class="flex-1 p-4 overflow-hidden">--}}
{{--                    <div id="pdfContainer" class="w-full h-full bg-gray-100 dark:bg-gray-900 rounded-lg overflow-y-auto">--}}
{{--                        <div id="pdf-pages" class="flex flex-col items-center gap-4 p-4"></div>--}}
{{--                    </div>--}}
{{--                </div>--}}

{{--                <livewire:common.pdf-reader />--}}

                <!-- PDF Controls -->
                <div class="p-4 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-700 rounded-b-2xl">
                    <div class="flex items-center justify-center space-x-4">
                        <div class="flex items-center space-x-2">
                            <button id="zoom-out" class="px-3 py-2 bg-gray-200 dark:bg-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-500 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                                </svg>
                            </button>
                            <span id="zoom-level" class="text-sm text-gray-600 dark:text-gray-400 min-w-[50px] text-center">100%</span>
                            <button id="zoom-in" class="px-3 py-2 bg-gray-200 dark:bg-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-500 transition-colors">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                                </svg>
                            </button>
                        </div>
                        <button id="fit-width" class="px-4 py-2 bg-gray-200 dark:bg-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-300 dark:hover:bg-gray-500 transition-colors text-sm">
                            Fit Width
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            console.log('DOM loaded, setting up PDF reader event listeners');

            // Function to setup event listeners
            function setupPdfEventListeners() {
                console.log('Setting up PDF event listeners');

                // Listen for the openPdfReader event
                window.addEventListener('openPdfReader', function(event) {
                    console.log('openPdfReader event received:', event.detail);

                    const pdfUrl = event.detail[0].pdfUrl;
                    const currentPage = event.detail[0].currentPage || 1;

                    console.log('PDF URL:', pdfUrl);
                    console.log('Current Page:', currentPage);

                    // Small delay to ensure DOM is ready
                    setTimeout(() => {
                        initializePDFReader(pdfUrl, currentPage);
                    }, 100);
                });
            }

            // Try multiple approaches to ensure event listeners are set up
            if (window.Livewire) {
                console.log('Livewire already available, setting up listeners');
                setupPdfEventListeners();
            } else {
                console.log('Waiting for Livewire to initialize');

                // Try livewire:initialized event
                document.addEventListener('livewire:initialized', function() {
                    console.log('Livewire initialized via event');
                    setupPdfEventListeners();
                });

                // Also try a timeout as fallback
                setTimeout(function() {
                    if (window.Livewire && !window.pdfListenersSetup) {
                        console.log('Livewire found via timeout, setting up listeners');
                        setupPdfEventListeners();
                        window.pdfListenersSetup = true;
                    }
                }, 1000);
            }
        });

        function initializePDFReader(pdfUrl, startPage = 1) {
            console.log('Initializing PDF reader with URL:', pdfUrl);

            const pagesContainer = document.getElementById('pdf-pages');

            if (!pagesContainer) {
                console.error('Pages container not found');
                return;
            }

            let pdfDoc = null;
            let scale = 1.0;
            let baseScale = 1.0;
            let canvases = [];

            console.log('Loading PDF document...');

            // Load PDF
            pdfjsLib.getDocument(pdfUrl).promise.then(function(pdf) {
                console.log('PDF loaded successfully, pages:', pdf.numPages);

                pdfDoc = pdf;

                // Update total pages
                const totalPagesElement = document.getElementById('total-pages');
                if (totalPagesElement) {
                    totalPagesElement.textContent = pdf.numPages;
                }

                // Calculate initial scale
                calculateInitialScale();

                // Render all pages
                renderAllPages();

                // Setup controls
                setupControls();
            }).catch(function(error) {
                console.error('Error loading PDF:', error);
                alert('Error loading PDF: ' + error.message);
            });

            function calculateInitialScale() {
                if (!pdfDoc) return;

                pdfDoc.getPage(1).then(function(page) {
                    const container = document.getElementById('pdfContainer');
                    if (!container) return;

                    const containerWidth = container.clientWidth - 80; // Account for padding and scrollbar

                    const viewport = page.getViewport({ scale: 1 });
                    baseScale = containerWidth / viewport.width;
                    scale = baseScale;

                    console.log('Base scale calculated:', baseScale);
                });
            }

            function renderAllPages() {
                console.log('Rendering all pages...');

                // Clear existing canvases
                pagesContainer.innerHTML = '';
                canvases = [];

                // Render each page
                for (let pageNum = 1; pageNum <= pdfDoc.numPages; pageNum++) {
                    renderPage(pageNum);
                }
            }

            function renderPage(pageNum) {
                pdfDoc.getPage(pageNum).then(function(page) {
                    console.log('Rendering page:', pageNum);

                    // Create canvas for this page
                    const canvas = document.createElement('canvas');
                    const ctx = canvas.getContext('2d');

                    // Add some styling
                    canvas.className = 'shadow-lg mb-4 bg-white';
                    canvas.style.maxWidth = '100%';

                    // Get device pixel ratio for high-DPI displays
                    const devicePixelRatio = window.devicePixelRatio || 1;

                    // Calculate viewport with current scale
                    const viewport = page.getViewport({ scale: scale });

                    // Set canvas size (accounting for device pixel ratio)
                    canvas.width = viewport.width * devicePixelRatio;
                    canvas.height = viewport.height * devicePixelRatio;

                    // Set canvas display size
                    canvas.style.width = viewport.width + 'px';
                    canvas.style.height = viewport.height + 'px';

                    // Scale context for high-DPI
                    ctx.scale(devicePixelRatio, devicePixelRatio);

                    const renderContext = {
                        canvasContext: ctx,
                        viewport: viewport
                    };

                    // Store canvas reference
                    canvases[pageNum - 1] = canvas;

                    // Add canvas to container
                    pagesContainer.appendChild(canvas);

                    // Render the page
                    page.render(renderContext).promise.then(function() {
                        console.log('Page', pageNum, 'rendered successfully');
                    }).catch(function(error) {
                        console.error('Error rendering page', pageNum, ':', error);
                    });
                }).catch(function(error) {
                    console.error('Error getting page', pageNum, ':', error);
                });
            }

            function updateZoom() {
                console.log('Updating zoom to:', scale);

                // Re-render all pages with new scale
                renderAllPages();

                // Update zoom level display
                const zoomElement = document.getElementById('zoom-level');
                if (zoomElement) {
                    const zoomPercentage = Math.round((scale / baseScale) * 100);
                    zoomElement.textContent = zoomPercentage + '%';
                }
            }

            function setupControls() {
                console.log('Setting up PDF controls');

                const zoomInBtn = document.getElementById('zoom-in');
                if (zoomInBtn) {
                    // Remove any existing listeners
                    zoomInBtn.replaceWith(zoomInBtn.cloneNode(true));
                    const newZoomInBtn = document.getElementById('zoom-in');
                    newZoomInBtn.addEventListener('click', function() {
                        console.log('Zoom in clicked');
                        scale *= 1.25;
                        updateZoom();
                    });
                }

                const zoomOutBtn = document.getElementById('zoom-out');
                if (zoomOutBtn) {
                    // Remove any existing listeners
                    zoomOutBtn.replaceWith(zoomOutBtn.cloneNode(true));
                    const newZoomOutBtn = document.getElementById('zoom-out');
                    newZoomOutBtn.addEventListener('click', function() {
                        console.log('Zoom out clicked');
                        scale /= 1.25;
                        // Prevent zooming out too much
                        if (scale < baseScale * 0.25) {
                            scale = baseScale * 0.25;
                        }
                        updateZoom();
                    });
                }

                const fitWidthBtn = document.getElementById('fit-width');
                if (fitWidthBtn) {
                    // Remove any existing listeners
                    fitWidthBtn.replaceWith(fitWidthBtn.cloneNode(true));
                    const newFitWidthBtn = document.getElementById('fit-width');
                    newFitWidthBtn.addEventListener('click', function() {
                        console.log('Fit width clicked');
                        scale = baseScale;
                        updateZoom();
                    });
                }
            }
        }
    </script>
</div>


